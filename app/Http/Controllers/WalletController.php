<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\User;

class WalletController extends Controller
{
    public function buy(Request $request)
    {
        $request->validate([
            'coin' => 'required|exists:coins,id',
            'amount' => 'required|numeric|gt:0',
        ], [
            'coin.required' => 'A moeda é obrigatória.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.gt' => 'O valor deve ser maior que zero.',
        ]);

        $coinId = $request->coin;
        $amount = $request->amount;
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $lastTransaction = Transaction::latest('id')->first();
            $previousHash = $lastTransaction ? $lastTransaction->transaction : '0';

            $transactionData = [
                'sender_id' => null,
                'receiver_id' => $user->id,
                'coin_id' => $coinId,
                'amount' => $amount,
                'type' => 'buy',
                'timestamp' => now()->toString(),
                'before_transaction' => $previousHash,
            ];

            $currentHash = hash('sha256', json_encode($transactionData));

            Transaction::create(array_merge($transactionData, [
                'transaction' => $currentHash
            ]));

            $wallet = $user->wallets()->lockForUpdate()->firstOrCreate(
                ['coin_id' => $coinId],
                ['balance' => 0]
            );
            $wallet->increment('balance', $amount);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Falha na Compra para o usuário ' . $user->id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Ocorreu um erro ao processar sua compra.');
        }

        return redirect()->route('dashboard')->with('success', 'Compra realizada com sucesso!');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'coin' => 'required|exists:coins,id',
            'amount' => 'required|numeric|gt:0',
            'account' => 'required|string|exists:users,account',
        ], [
            'coin.required' => 'A moeda é obrigatória.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.gt' => 'O valor deve ser maior que zero.',
            'account.required' => 'A conta de destino é obrigatória.',
            'account.string' => 'A conta de destino deve ser uma string.',
            'account.exists' => 'A conta de destino não existe.',
        ]);

        $sender = auth()->user();
        $receiver = User::where('account', $request->account)->first();
        $amount = $request->amount;
        $coinId = $request->coin;

        DB::beginTransaction();
        try {
            $senderWallet = $sender->wallets()
                ->where('coin_id', $coinId)
                ->lockForUpdate()
                ->first();

            if (!$senderWallet || $senderWallet->balance < $amount) {
                DB::rollBack();
                return back()->with('error', 'Saldo insuficiente.');
            }

            $lastTransaction = Transaction::latest('id')->first();
            $hashDaTransacaoAnterior = $lastTransaction ? $lastTransaction->transaction : '0';

            $currentTransactionData = [
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'coin_id' => $coinId,
                'amount' => $amount,
                'type' => 'transfer',
                'timestamp' => now()->toString(),
                'before_transaction' => $hashDaTransacaoAnterior,
            ];

            $hashDaTransacaoAtual = hash('sha256', json_encode($currentTransactionData));

            Transaction::create(array_merge($currentTransactionData, [
                'transaction' => $hashDaTransacaoAtual
            ]));


            $receiverWallet = $receiver->wallets()->firstOrCreate(['coin_id' => $coinId]);
            $senderWallet->decrement('balance', $amount);
            $receiverWallet->increment('balance', $amount);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            \Log::error('Falha na Transferência para o usuário ' . $sender->id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Ocorreu um erro ao processar sua transferência. Tente novamente mais tarde. ');
        }

        return redirect()->route('dashboard')->with('success', 'Transferência realizada com sucesso!');
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'coin' => 'required|exists:coins,id',
            'amount' => 'required|numeric|gt:0',
        ], [
            'coin.required' => 'A moeda é obrigatória.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.gt' => 'O valor deve ser maior que zero.',
        ]);

        $amount = $request->amount;
        $coinId = $request->coin;
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $wallet = $user->wallets()
                ->where('coin_id', $coinId)
                ->lockForUpdate()
                ->first();

            if (!$wallet || $wallet->balance < $amount) {
                DB::rollBack();
                return back()->with('error', 'Saldo insuficiente para realizar o saque.');
            }

            $lastTransaction = Transaction::latest('id')->first();
            $previousHash = $lastTransaction ? $lastTransaction->transaction : '0';

            $transactionData = [
                'sender_id' => $user->id,
                'receiver_id' => null,
                'coin_id' => $coinId,
                'amount' => $amount,
                'type' => 'withdraw',
                'timestamp' => now()->toString(),
                'before_transaction' => $previousHash,
            ];

            $currentHash = hash('sha256', json_encode($transactionData));

            Transaction::create(array_merge($transactionData, [
                'transaction' => $currentHash
            ]));

            $wallet->decrement('balance', $amount);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Falha no Saque para o usuário ' . $user->id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Ocorreu um erro ao processar seu saque.');
        }

        return redirect()->route('dashboard')->with('success', 'Saque realizado com sucesso!');
    }
}
