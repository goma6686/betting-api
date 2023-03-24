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
        DB::table('games')->delete();

        DB::table('games')->insert([
                ['id' => '1', 'game_name' => 'Lucky 7', 'banner_image' => 'L7_500x500.png'],
                ['id' => '3', 'game_name' => 'Lucky 5', 'banner_image' => 'L5_500x500.png'],
                ['id' => '5', 'game_name' => 'Bet On Poker', 'banner_image' => 'Poker_500x500.png'],
                ['id' => '6', 'game_name' => 'Bet On Baccarat', 'banner_image' => 'Baccarat_500x500.png'],
                ['id' => '7', 'game_name' => 'Wheel Of Fortune', 'banner_image' => 'WheelofFortune_500x500.png'],
                ['id' => '8', 'game_name' => 'War Of Bets', 'banner_image' => 'WarofBet_500x500.png'],
                ['id' => '9', 'game_name' => 'Lucky 6', 'banner_image' => 'L6_500x500.png'],
                ['id' => '10', 'game_name' => 'Dice Duel', 'banner_image' => 'DiceDuel_500x500.png'],
                ['id' => '11', 'game_name' => 'Speedy 7', 'banner_image' => 'Speedy7_500x500.png'],
                ['id' => '12', 'game_name' => '6+ Poker', 'banner_image' => '6Poker_500x500.png'],
                ['id' => '13', 'game_name' => 'Andar Bahar', 'banner_image' => 'AndarBahar_500x500.png'],
                ['id' => '16', 'game_name' => 'Classic Wheel', 'banner_image' => 'ClassicWheel_500x500.png'],
                ['id' => '17', 'game_name' => 'Football Grid ', 'banner_image' => 'FootballGrid_500x500.jpg'],
                ['id' => '18', 'game_name' => 'Satta Matka', 'banner_image' => 'SattaMatka_500x500.png'],
        ]);
    }
}
