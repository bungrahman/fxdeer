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
            if (Schema::hasColumn('plans', 'telegram_bot_token')) {
                $table->dropColumn('telegram_bot_token');
            }
            if (Schema::hasColumn('plans', 'telegram_chat_id')) {
                $table->dropColumn('telegram_chat_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('telegram_bot_token')->nullable();
            $table->string('telegram_chat_id')->nullable();
        });
    }
};
