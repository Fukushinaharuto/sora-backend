<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\HelpRequest;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('help_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignIdFor(HelpRequest::class)->constrained()->cascadeOnDelete();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_assignments');
    }
};
