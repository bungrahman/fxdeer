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
        Schema::create('event_registry', function (Blueprint $table) {
            $table->string('event_id')->primary(); // CRITICAL: Primary key untuk idempotency
            $table->enum('pipeline', ['A', 'B', 'C']);
            $table->timestamp('event_time_utc');
            $table->timestamp('sent_at')->nullable();
            $table->string('language', 5);
            $table->string('channel'); // telegram, social, etc.
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['pipeline', 'event_time_utc']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registry');
    }
};
