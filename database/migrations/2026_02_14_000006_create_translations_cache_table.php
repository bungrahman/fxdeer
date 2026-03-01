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
        Schema::create('translations_cache', function (Blueprint $table) {
            $table->id();
            $table->string('content_hash', 64)->index(); // SHA256 hash dari konten asli
            $table->string('language', 5);
            $table->text('translated_text');
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['content_hash', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations_cache');
    }
};
