<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1Id = '83490f86-0bcf-4297-ad7b-7db8bc63548e';
        $user2Id = '660e81cd-039d-42f8-ae49-c0748c777cb4';

        User::insert([
            [
                'id' => $user1Id,
                'name' => 'a君',
                'email' => 'a@example.com',
                'password' => Hash::make('password'),
                'city_id' => 1,
            ],
            [
                'id' => $user2Id,
                'name' => 'b君',
                'email' => 'b@example.com',
                'password' => Hash::make('password'),
                'city_id' => 2,
            ],
        ]);
    }
}
