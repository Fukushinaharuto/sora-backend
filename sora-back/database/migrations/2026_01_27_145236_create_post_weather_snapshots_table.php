<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->enum('weather_type', ["rain", "snow", "wind",  "temp"]);
            $table->decimal('temperature', 4, 1);
            $table->decimal('feels_like', 4, 1);
            $table->decimal('wind_speed', 4, 1);
            $table->string('wind_direction', 3);
            $table->unsignedTinyInteger('precipitation_prob');
            $table->unsignedSmallInteger('visibility');
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
