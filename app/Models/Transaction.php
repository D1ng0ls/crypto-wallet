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

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function coin()
    {
        return $this->belongsTo(Coin::class, 'coin_id');
    }
}
