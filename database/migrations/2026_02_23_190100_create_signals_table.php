<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->integer('row_number')->nullable();
            $table->string('signal'); // BUY / SELL
            $table->string('pair'); // e.g. OP/USD
            $table->string('price');
            $table->string('sl')->nullable(); // stop loss
            $table->string('tp')->nullable(); // take profit
            $table->string('reason')->nullable();
            $table->string('signal_timestamp')->nullable(); // original timestamp from source
            $table->string('score')->nullable();
            $table->string('stars')->nullable();
            $table->string('conf_level')->nullable(); // HIGH / MEDIUM / LOW
            $table->string('last_sl')->nullable();
            $table->string('last_tp')->nullable();
            $table->string('result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
