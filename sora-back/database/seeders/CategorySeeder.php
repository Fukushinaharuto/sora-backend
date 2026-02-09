<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => '傘・雨', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '服装', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '移動', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '外での活動', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
