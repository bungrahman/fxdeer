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
        Schema::create('usage_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('pipeline', ['A', 'B', 'C']);
            $table->string('event_id');
            $table->date('date');
            $table->timestamps();
            
            // Composite index untuk query harian per user
            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'pipeline', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_log');
    }
};
