<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('activeSubscription.plan');
        $usageToday = \App\Models\UsageLog::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->count();
            
        return view('client.dashboard', compact('user', 'usageToday'));
    }

    public function pipelines()
    {
        $user = Auth::user()->load('activeSubscription.plan');
        return view('client.pipelines', compact('user'));
    }

    public function billing()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->paginate(10);
            
        return view('client.billing', compact('transactions'));
    }

    public function updateBotSettings(Request $request)
    {
        $user = Auth::user()->load('activeSubscription.plan');
        
        if (!$user->activeSubscription || !$user->activeSubscription->plan->enable_signal_alert) {
            return back()->with('error', 'Your plan does not support Signal Alert feature.');
        }

        $data = $request->validate([
            'signal_bot_token' => 'required|string|max:255',
            'signal_telegram_chat' => 'required|string|max:255',
        ]);

        $user->update($data);

        return back()->with('success', 'Signal Alert settings updated successfully!');
    }
    public function updateLanguage(Request $request)
    {
        $supportedCodes = collect(\App\Models\Setting::getSupportedLanguages())->pluck('code')->toArray();
        $codesStr = implode(',', $supportedCodes);

        $data = $request->validate([
            'default_language' => 'required|string|in:' . $codesStr,
        ]);

        Auth::user()->update($data);

        return back()->with('success', 'Language preference updated successfully!');
    }
}
