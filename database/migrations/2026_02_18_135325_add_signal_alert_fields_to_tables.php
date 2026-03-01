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
            $table->boolean('enable_signal_alert')->default(false)->after('post_event_reaction');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('personal_bot_token')->nullable()->after('role');
            $table->string('personal_telegram_chat_id')->nullable()->after('personal_bot_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('enable_signal_alert');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['personal_bot_token', 'personal_telegram_chat_id']);
        });
    }
};
