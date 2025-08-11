<?php

namespace Database\Seeders;

use App\Models\Coin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coins = [
            [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'price' => 100000,
                'risk' => 'low',
                'image' => 'btc.svg',
            ],
            [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'price' => 10000,
                'image' => 'eth.svg',
            ],
            [
                'name' => 'Cardano',
                'symbol' => 'ADA',
                'price' => 1000,
                'image' => 'ada.svg',
            ],
            [
                'name' => 'Solana',
                'symbol' => 'SOL',
                'price' => 100,
                'risk' => 'high',
                'image' => 'sol.svg',
            ],
            [
                'name' => 'Tether',
                'symbol' => 'USDT',
                'price' => 1,
                'risk' => 'low',
                'image' => 'usdt.svg',
            ],
        ];

        foreach ($coins as $coin) {
            Coin::create($coin);
        }
    }
}
