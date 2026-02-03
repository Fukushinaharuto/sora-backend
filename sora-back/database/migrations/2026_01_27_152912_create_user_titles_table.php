<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Title;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_titles', function (Blueprint $table) {
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignIdFor(Title::class)->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'title_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_titles');
    }
};
