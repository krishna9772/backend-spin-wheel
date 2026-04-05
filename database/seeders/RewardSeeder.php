<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reward;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Reward::insert([
            ['label' => '10 Coins', 'chance' => 40, 'stock' => 500],
            ['label' => '50 Coins', 'chance' => 30, 'stock' => 300],
            ['label' => '100 Coins', 'chance' => 20, 'stock' => 200],
            ['label' => 'Jackpot', 'chance' => 10, 'stock' => 50],
        ]);
    }
}
