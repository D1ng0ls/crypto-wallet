<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $user->load('wallets.coin', 'senderTransactions', 'receiverTransactions', 'notifications');

        $coins = Coin::all();

        return view('dashboard', compact('user', 'coins'));
    }
}
