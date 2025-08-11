<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;
use Illuminate\Support\Facades\DB;
use Throwable;

class WalletController extends Controller
{
    public function buy(Request $request)
    {
        $request->validate([
            'coin' => 'required|exists:coins,id',
            'amount' => 'required|numeric|gt:0',
        ]);

        $coinId = $request->coin;
        $amount = $request->amount;
        $user = auth()->user();

        try {
            DB::beginTransaction();

            $wallet = $user->wallets()->lockForUpdate()->firstOrCreate(
                ['coin_id' => $coinId],
                ['balance' => 0]
            );

            $wallet->increment('balance', $amount);

            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Ocorreu um erro ao processar sua compra. Tente novamente.');
        }

        return redirect()->route('dashboard')->with('success', 'Compra realizada com sucesso!');
    }
}
