<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Plans
        $pro = Plan::create([
            'name' => 'Professional',
            'price' => 49.00,
            'daily_outlook' => true,
            'upcoming_event_alerts' => true,
            'post_event_reaction' => true,
            'max_alerts_per_day' => 10,
            'channels_allowed' => ['telegram', 'discord', 'webhook'],
        ]);

        $basic = Plan::create([
            'name' => 'Basic',
            'price' => 19.00,
            'daily_outlook' => true,
            'upcoming_event_alerts' => false,
            'post_event_reaction' => false,
            'max_alerts_per_day' => 3,
            'channels_allowed' => ['telegram'],
        ]);

        // 2. Create Initial User
        $user = User::create([
            'email' => 'admin@newsauto.com',
            'password' => bcrypt('password'),
            'status' => 'ACTIVE',
            'timezone' => 'UTC',
            'default_language' => 'en',
        ]);

        // 3. Create Subscription
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $pro->id,
            'status' => 'ACTIVE',
            'renewal_date' => now()->addMonth(),
            'stripe_subscription_id' => 'sub_test_123',
        ]);
        
    }
}
