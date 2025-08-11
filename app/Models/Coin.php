<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    protected $table = 'coins';

    protected $fillable = [
        'name',
        'symbol',
        'image',
        'risk',
        'price',
    ];

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
}
