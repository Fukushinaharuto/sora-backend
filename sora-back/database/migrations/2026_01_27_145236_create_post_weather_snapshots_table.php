<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Post;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Post::class)->constrained()->cascadeOnDelete();
            $table->enum('weather_type', ["clear", "cloudy", "rain", "snow"]);
            $table->decimal('temperature', 4, 1);
            $table->decimal('wind_speed', 4, 1);
            $table->tinyInteger('wind_direction');
            $table->decimal('precipitation', 5, 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_weather_snapshots');
    }
};
