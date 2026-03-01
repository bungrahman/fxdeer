<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventRegistry;
use App\Models\User;
use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class EventController extends Controller
{
    /**
     * Check if user is eligible to receive an event
     * POST /api/events/eligible
     */
    public function checkEligibility(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pipeline' => 'required|in:A,B,C',
        ]);

        $user = User::with('activeSubscription.plan')->find($request->user_id);

        // 0. Safety & Emergency Check (Kill-switches)
        if (\App\Models\Setting::isEmergencyPauseActive()) {
            return response()->json([
                'status' => 'denied',
                'reason' => 'emergency_pause_active',
            ], 503);
        }

        if (\App\Models\Setting::isPipelineDisabled($request->pipeline)) {
            return response()->json([
                'status' => 'denied',
                'reason' => 'pipeline_globally_disabled',
            ], 503);
        }

        // 1. Validasi status user (Must be ACTIVE)
        if ($user->status !== 'ACTIVE') {
            return response()->json([
                'status' => 'denied',
                'reason' => 'user_not_active',
            ], 403);
        }

        // 2. Cek apakah user punya active subscription
        $subscription = $user->activeSubscription;
        if (!$subscription) {
            return response()->json([
                'status' => 'denied',
                'reason' => 'no_active_subscription',
            ], 403);
        }

        $plan = $subscription->plan;

        // 3. Cek apakah pipeline (A/B/C) diizinkan untuk Plan user
        $pipelineAllowed = $this->isPipelineAllowed($plan, $request->pipeline);
        if (!$pipelineAllowed) {
            return response()->json([
                'status' => 'denied',
                'reason' => 'pipeline_not_allowed',
            ], 403);
        }

        // 4. Cek limit harian (UsageLog vs max_alerts_per_day)
        $today = now()->toDateString();
        $usageCount = UsageLog::where('user_id', $user->id)
            ->where('date', $today)
            ->count();

        if ($usageCount >= $plan->max_alerts_per_day) {
            return response()->json([
                'status' => 'denied',
                'reason' => 'quota_full',
            ], 403);
        }

        return response()->json([
            'status' => 'allowed',
            'remaining_quota' => $plan->max_alerts_per_day - $usageCount,
        ]);
    }

    /**
     * Mark event as sent
     * POST /api/events/mark-sent
     */
    public function markAsSent(Request $request)
    {
        $request->validate([
            'event_id' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'pipeline' => 'required|in:A,B,C',
            'event_time_utc' => 'required|date',
            'language' => 'required|string|max:5',
            'channel' => 'required|string',
        ]);

        // Gunakan Redis lock untuk mencegah race condition
        $lockKey = "event_lock:{$request->event_id}:{$request->user_id}";
        $lock = Redis::set($lockKey, '1', 'EX', 10, 'NX');

        if (!$lock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event is being processed by another request',
            ], 409);
        }

        try {
            DB::beginTransaction();

            // Harus Idempotent (Gunakan UNIQUE constraint pada event_id)
            $event = EventRegistry::firstOrCreate(
                ['event_id' => $request->event_id],
                [
                    'pipeline' => $request->pipeline,
                    'event_time_utc' => $request->event_time_utc,
                    'sent_at' => now(),
                    'language' => $request->language,
                    'channel' => $request->channel,
                ]
            );

            // Jika event sudah ada sebelumnya, return success (idempotent)
            if (!$event->wasRecentlyCreated) {
                DB::commit();
                Redis::del($lockKey);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Event already marked as sent',
                    'event' => $event,
                ]);
            }

            // Catat usage log
            UsageLog::create([
                'user_id' => $request->user_id,
                'pipeline' => $request->pipeline,
                'event_id' => $request->event_id,
                'date' => now()->toDateString(),
            ]);

            DB::commit();
            Redis::del($lockKey);

            return response()->json([
                'status' => 'success',
                'message' => 'Event marked as sent',
                'event' => $event,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Redis::del($lockKey);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark event as sent',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch all active users for n8n iteration
     * GET /api/events/users/active
     */
    public function fetchActiveUsers()
    {
        // Global Kill-Switch Check
        if (\App\Models\Setting::isEmergencyPauseActive()) {
            return response()->json([
                'status' => 'paused',
                'message' => 'Emergency Global Pause is active.',
                'count' => 0,
                'users' => [],
            ]);
        }

        $users = User::where('status', 'ACTIVE')
            ->whereHas('activeSubscription', function($q) {
                $q->where('status', 'ACTIVE');
            })
            ->with(['activeSubscription.plan'])
            ->get()
            ->map(function($user) {
                $plan = $user->activeSubscription->plan;
                $langConfig = \App\Models\Setting::getLanguageConfig($user->default_language);
                
                return [
                    'id' => $user->id,
                    'client' => $user->email, // Sesuaikan dengan label 'client' di PDF
                    'status' => $user->status,
                    'timezone' => $user->timezone,
                    'language' => $user->default_language,
                    'plan' => $plan->name,
                    'tags' => $plan->tags,
                    'hashtags' => $plan->hashtags,
                    'telegram_bot' => ($langConfig['bot_token'] ?? null) ?: $plan->telegram_bot_token,
                    'telegram_chat' => ($langConfig['chat_id'] ?? null) ?: $plan->telegram_chat_id,
                    'enable_telegram' => $plan->enable_telegram,
                    'enable_signal_alert' => $plan->enable_signal_alert,
                    'signal_bot_token' => $user->signal_bot_token,
                    'signal_telegram_chat' => $user->signal_telegram_chat,
                    'blotato_key' => $plan->blotato_key,
                    'enable_blotato' => $plan->enable_blotato,
                    'enable_email' => $plan->enable_email,
                    'quota_daily' => $plan->max_alerts_per_day,
                    'remaining_quota' => $plan->max_alerts_per_day - \App\Models\UsageLog::where('user_id', $user->id)->where('date', now()->toDateString())->count(),
                ];
            });

        return response()->json([
        'status' => 'success',
        'global_switches' => [
            'emergency_pause' => \App\Models\Setting::isEmergencyPauseActive(),
            'pipeline_a_pause' => \App\Models\Setting::isPipelineDisabled('a'),
            'pipeline_b_pause' => \App\Models\Setting::isPipelineDisabled('b'),
            'pipeline_c_pause' => \App\Models\Setting::isPipelineDisabled('c'),
        ],
        'count' => $users->count(),
        'users' => $users,
    ]);
    }

    /**
     * Fetch plan rules for a specific user/plan
     * GET /api/events/plans/{plan}
     */
    public function fetchPlanRules(Plan $plan)
    {
        return response()->json([
            'status' => 'success',
            'plan' => $plan,
        ]);
    }

    /**
     * Helper: Check if pipeline is allowed for the plan
     */
    private function isPipelineAllowed($plan, $pipeline)
    {
        // Pipeline A = daily_outlook
        // Pipeline B = upcoming_event_alerts
        // Pipeline C = post_event_reaction
        
        $pipelineMap = [
            'A' => $plan->daily_outlook,
            'B' => $plan->upcoming_event_alerts,
            'C' => $plan->post_event_reaction,
        ];

        return $pipelineMap[$pipeline] ?? false;
    }
}
