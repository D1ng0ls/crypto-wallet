<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $user->load('wallets.coin', 'senderTransactions', 'receiverTransactions', 'notifications');

        $coins = Coin::all();

        $recommendedCoins = [];

        $fearAndGreedIndex = 0;

        $averageScore = Comment::where('status', 'processed')->avg('polarity_score');

        if (is_numeric($averageScore)) {
            $normalizedScore = ($averageScore + 1) / 2;
            $fearAndGreedIndex = round($normalizedScore * 100);
        }

        try {
            $userProfile = $user->investment_profile;

            $userBalances = $user->wallets()->with('coin')->get()->map(function ($balance) {
                return [
                    'coin_id' => $balance->coin->id,
                    'symbol' => $balance->coin->symbol,
                    'risk' => $balance->coin->risk,
                    'balance' => $balance->balance,
                    'price' => $balance->coin->price,
                ];
            });

            $allCoins = Coin::all([
                'id',
                'symbol',
                'name',
                'risk',
                'price',
            ]);

            $scriptPath = storage_path('app/scripts/recommend_coins.py');

            $result = Process::run([
                'python',
                $scriptPath,
                $userProfile,
                $userBalances->toJson(),
                $allCoins->toJson(),
            ]);

            if ($result->successful()) {
                $recommendedCoinIds = json_decode($result->output(), true);

                if (is_array($recommendedCoinIds) && !empty($recommendedCoinIds)) {
                    $ids_ordered = implode(',', $recommendedCoinIds);

                    $recommendedCoins = Coin::whereIn('id', $recommendedCoinIds)
                        ->orderByRaw("FIELD(id, $ids_ordered)")
                        ->get();
                }
            } else {
                Log::error('Falha ao executar script de recomendação', [
                    'user_id' => $user->id,
                    'exit_code' => $result->exitCode(),
                    'error_output' => $result->errorOutput(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Exceção ao chamar script de recomendação', ['exception' => $e->getMessage()]);
        }

        return view('dashboard', compact('user', 'coins', 'recommendedCoins', 'fearAndGreedIndex'));
    }
}
