<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cities')->insert([
            // 北海道（抜粋）
            [
                'prefecture_id' => 1,
                'name' => '札幌市',
                'latitude' => 43.0618,
                'longitude' => 141.3545,
            ],
            [
                'prefecture_id' => 1,
                'name' => '函館市',
                'latitude' => 41.7687,
                'longitude' => 140.7288,
            ],
            [
                'prefecture_id' => 1,
                'name' => '旭川市',
                'latitude' => 43.7706,
                'longitude' => 142.3650,
            ],

            // 青森県（抜粋）
            [
                'prefecture_id' => 2,
                'name' => '青森市',
                'latitude' => 40.8246,
                'longitude' => 140.7406,
            ],
            [
                'prefecture_id' => 2,
                'name' => '弘前市',
                'latitude' => 40.6031,
                'longitude' => 140.4642,
            ],
            [
                'prefecture_id' => 2,
                'name' => '八戸市',
                'latitude' => 40.5123,
                'longitude' => 141.4884,
            ],
        ]);
    }
}
