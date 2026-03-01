<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signal_configs', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('active'); // active / inactive
            $table->text('pairs'); // comma-separated: BTC/USD,ETH/USD,...
            $table->text('api_keys'); // comma-separated TwelveData API keys
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signal_configs');
    }
};
