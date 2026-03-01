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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price', 10, 2);
            $table->boolean('daily_outlook')->default(false);
            $table->boolean('upcoming_event_alerts')->default(false);
            $table->boolean('post_event_reaction')->default(false);
            $table->integer('max_alerts_per_day')->default(0);
            $table->integer('max_languages')->default(1);
            $table->json('channels_allowed')->nullable(); // ['telegram', 'social', etc.]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
