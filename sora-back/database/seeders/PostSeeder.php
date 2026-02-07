<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1Id = '83490f86-0bcf-4297-ad7b-7db8bc63548e';
        $user2Id = '660e81cd-039d-42f8-ae49-c0748c777cb4';

        DB::table('posts')->insert([
            [
                'user_id' => $user1Id,
                'category_id' => 1,
                'city_id' => 1,
                'message' => '天気が良いです！',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user1Id,
                'category_id' => 1,
                'city_id' => 1,
                'message' => '天気が良いです！',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user1Id,
                'category_id' => 2,
                'city_id' => 3,
                'message' => '天気が良いです！',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user2Id,
                'category_id' => 2,
                'city_id' => 3,
                'message' => '天気が良いです！',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
