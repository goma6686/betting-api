<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('games')->delete();

        \DB::table('games')->insert([
                ['id' => '1', 'game_name' => 'Lucky 7'],
                ['id' => '3', 'game_name' => 'Lucky 5'],
                ['id' => '5', 'game_name' => 'Bet On Poker'],
                ['id' => '6', 'game_name' => 'Bet On Baccarat'],
                ['id' => '7', 'game_name' => 'Wheel Of Fortune'],
                ['id' => '8', 'game_name' => 'War Of Bets '],
                ['id' => '9', 'game_name' => 'Lucky 6'],
                ['id' => '10', 'game_name' => 'Dice Duel'],
                ['id' => '11', 'game_name' => 'Speedy 7'],
                ['id' => '12', 'game_name' => '6+ Poker'],
                ['id' => '13', 'game_name' => 'Andar Bahar'],
                ['id' => '16', 'game_name' => 'Classic Wheel'],
                ['id' => '17', 'game_name' => 'Football Grid '],
                ['id' => '18', 'game_name' => 'Satta Matka'],
        ]);
    }
}
