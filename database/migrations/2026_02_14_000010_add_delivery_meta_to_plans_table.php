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
        Schema::table('plans', function (Blueprint $table) {
            $table->string('tags')->nullable();
            $table->text('hashtags')->nullable();
            $table->string('telegram_bot_token')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->string('blotato_key')->nullable();
            $table->boolean('enable_telegram')->default(false);
            $table->boolean('enable_blotato')->default(false);
            $table->boolean('enable_email')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'tags',
                'hashtags',
                'telegram_bot_token',
                'telegram_chat_id',
                'blotato_key',
                'enable_telegram',
                'enable_blotato',
                'enable_email'
            ]);
        });
    }
};
