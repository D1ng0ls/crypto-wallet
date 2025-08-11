<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'coin_id',
        'amount',
        'type',
        'status',
        'transaction',
        'before_transaction'
    ];
}
