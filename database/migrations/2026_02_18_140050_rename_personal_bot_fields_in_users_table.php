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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('personal_bot_token', 'signal_bot_token');
            $table->renameColumn('personal_telegram_chat_id', 'signal_telegram_chat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('signal_bot_token', 'personal_bot_token');
            $table->renameColumn('signal_telegram_chat', 'personal_telegram_chat_id');
        });
    }
};
