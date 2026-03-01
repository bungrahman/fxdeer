<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * User Management: List/Update Status
     */
    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:ACTIVE,SUSPENDED',
        ]);

        $user->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => "User status updated to {$request->status}",
        ]);
    }

    /**
     * Settings Management: Update Kill-switches
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'emergency_pause' => 'boolean',
            'kill_switch_pipeline_a' => 'boolean',
            'kill_switch_pipeline_b' => 'boolean',
            'kill_switch_pipeline_c' => 'boolean',
            'disabled_languages' => 'array',
        ]);

        foreach ($request->all() as $key => $value) {
            Setting::set($key, $value);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Admin settings updated successfully',
        ]);
    }

    /**
     * Emergency Reset: Daily Quotas
     */
    public function resetDailyQuotas(Request $request)
    {
        // Logika reset usage_log bisa di sini if needed manually
        \App\Models\UsageLog::where('date', now()->toDateString())->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Daily usage logs cleared for all users',
        ]);
    }
}
