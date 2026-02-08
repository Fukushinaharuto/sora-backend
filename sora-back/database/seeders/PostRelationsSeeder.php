<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;

class PostRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();

        foreach ($posts as $post) {
            $post->postWeatherSnapshot()->create([
                'weather_type' => 'clear',
                'temperature' => 25,
                'wind_speed' => 5,
                'wind_direction' => 14,
                'precipitation' => 0,
            ]);

            $post->postImages()->create([
                'image_url' => 'https://placehold.jp/150x150.png',
            ]);
        }
    }
}
