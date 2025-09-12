<?php

namespace App\Console\Commands;

use App\Models\Comment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class AnalyzeCommentSentiments extends Command
{
    protected $signature = 'sentiments:sync-and-analyze';
    protected $description = 'Fetches comments from WordPress, syncs them, and analyzes sentiment of pending ones';

    public function handle()
    {
        // --- PARTE 1: BUSCAR E SINCRONIZAR COM O WORDPRESS ---
        $this->info('Iniciando sincronização com o WordPress...');
        
        $wordpressApiUrl = 'https://blog-cryptowallet.dingols.com.br/wp-json/wp/v2/comments';

        $response = Http::get($wordpressApiUrl);

        if (!$response->successful()) {
            $this->error('Falha ao conectar com a API do WordPress.');
            Log::error('WordPress API connection failed', ['status' => $response->status()]);
            return;
        }

        $wordpressComments = $response->json();
        $this->info(count($wordpressComments) . ' comentários encontrados. Sincronizando com o banco de dados local...');

        foreach ($wordpressComments as $wpComment) {
            Comment::updateOrCreate(
                ['wordpress_id' => $wpComment['id']],
                [
                    'content' => strip_tags($wpComment['content']['rendered']),
                    'author' => $wpComment['author_name'],
                ]
            );
        }
        $this->info('Sincronização concluída.');

        // --- PARTE 2: ANALISAR COMENTÁRIOS PENDENTES ---
        $this->info('Buscando comentários pendentes para análise...');
        
        $pendingComments = Comment::where('status', 'pending')->get(['id', 'content']);

        if ($pendingComments->isEmpty()) {
            $this->info('Nenhum comentário novo para analisar.');
            return;
        }

        $this->info($pendingComments->count() . ' comentários encontrados. Enviando para a IA...');

        $scriptPath = storage_path('app/scripts/sentiment_analyzer.py');
        $result = Process::run([
            'python3',
            $scriptPath,
            $pendingComments->toJson()
        ]);

        if (!$result->successful()) {
            $this->error('O script de análise da IA falhou: ' . $result->errorOutput());
            Log::error('Falha ao executar script de sentimentos', ['error' => $result->errorOutput()]);
            return;
        }

        $analysisResults = json_decode($result->output(), true);

        if (!is_array($analysisResults) || isset($analysisResults['error'])) {
            $this->error('A IA retornou um erro ou formato inesperado.');
            Log::error('IA sentiment script returned an error', ['response' => $analysisResults]);
            return;
        }

        // --- PARTE 3: ATUALIZAR O BANCO DE DADOS (A PEÇA QUE FALTAVA) ---
        $this->info('IA retornou os scores. Atualizando o banco de dados...');
        $processedCount = 0;
        foreach ($analysisResults as $res) {
            if (isset($res['id']) && isset($res['score'])) {
                Comment::where('id', $res['id'])->update([
                    'polarity_score' => $res['score'],
                    'status' => 'processed'
                ]);
                $processedCount++;
            }
        }

        $this->info("Processo concluído! {$processedCount} comentários foram analisados e atualizados.");
    }
}