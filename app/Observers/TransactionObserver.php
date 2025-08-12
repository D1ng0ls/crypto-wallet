<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\TransactionBuy;
use App\Notifications\TransactionWithdraw;
use App\Notifications\TransactionReceived;
use App\Notifications\TransactionSent;


class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        switch ($transaction->type) {

            case 'buy':
                if ($transaction->receiver) {
                    $transaction->receiver->notify(new TransactionBuy($transaction));
                }
                break;

            case 'withdraw':
                if ($transaction->sender) {
                    $transaction->sender->notify(new TransactionWithdraw($transaction));
                }
                break;

            case 'transfer':
                if ($transaction->sender) {
                    $transaction->sender->notify(new TransactionSent($transaction));
                }
                if ($transaction->receiver) {
                    $transaction->receiver->notify(new TransactionReceived($transaction));
                }
                break;
        }
    }
}
