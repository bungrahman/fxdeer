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
        Schema::create('failed_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pipeline');
            $table->string('channel');
            $table->string('error_message');
            $table->json('payload')->nullable(); // Original data from n8n
            $table->integer('retry_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_deliveries');
    }
};
