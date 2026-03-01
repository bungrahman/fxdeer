<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;

class SampleClientSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Master Plan Template for FxDeer Clients
        $proPlan = Plan::create([
            'name' => 'PRO_FOREX_ASIA',
            'price' => 20.00,
            'daily_outlook' => true,
            'upcoming_event_alerts' => true,
            'post_event_reaction' => true,
            'max_alerts_per_day' => 20,
            'max_languages' => 1,
            'channels_allowed' => ['telegram'],
            'tags' => 'forex,asia',
            'hashtags' => '#fxdeer #fxnews #fxupdates #dailyupdates',
            'telegram_bot_token' => '76543210:SAMPLE_BOT_TOKEN', // Placeholder
            'enable_telegram' => true,
            'enable_email' => true,
        ]);

        $clients = [
            ['email' => 'fxdeer_en@example.com', 'lang' => 'en', 'chat_id' => '-1003672402238'],
            ['email' => 'fxdeer_in@example.com', 'lang' => 'hi', 'chat_id' => '-1003610573421'],
            ['email' => 'fxdeer_ur@example.com', 'lang' => 'ur', 'chat_id' => '-1003630636225'],
            ['email' => 'fxdeer_my@example.com', 'lang' => 'my', 'chat_id' => '-1003681941957'],
            ['email' => 'fxdeer_idn@example.com', 'lang' => 'id', 'chat_id' => '-1003505364437'],
        ];

        foreach ($clients as $clientData) {
            // Create specific plan for each language/client to match their specific telegram chat ID
            $clientPlan = $proPlan->replicate();
            $clientPlan->name = "PLAN_" . strtoupper($clientData['lang']) . "_" . rand(100, 999);
            $clientPlan->telegram_chat_id = $clientData['chat_id'];
            
            // Append language specific hashtags
            if ($clientData['lang'] === 'hi') $clientPlan->hashtags .= " #forexindia";
            if ($clientData['lang'] === 'ur') $clientPlan->hashtags .= " #forexpakistan";
            if ($clientData['lang'] === 'id' || $clientData['lang'] === 'my') $clientPlan->hashtags .= " #forexmalaysia";
            
            $clientPlan->save();

            $user = User::create([
                'email' => $clientData['email'],
                'password' => Hash::make('password123'),
                'status' => 'ACTIVE',
                'timezone' => 'UTC',
                'default_language' => $clientData['lang'],
            ]);

            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $clientPlan->id,
                'status' => 'ACTIVE',
                'renewal_date' => now()->addMonth(),
            ]);
        }
    }
}
