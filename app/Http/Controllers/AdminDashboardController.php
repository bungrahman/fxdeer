<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\UsageLog;
use App\Models\Setting;
use App\Models\EventRegistry;
use App\Models\FailedDelivery;
use App\Models\SignalConfig;
use App\Models\Signal;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_subs' => Subscription::where('status', 'ACTIVE')->count(),
            'events_today' => UsageLog::where('date', now()->toDateString())->count(),
        ];

        $settings = [
            'emergency_pause' => Setting::get('emergency_pause', false),
            'kill_switch_a' => Setting::get('kill_switch_pipeline_a', false),
            'kill_switch_b' => Setting::get('kill_switch_pipeline_b', false),
            'kill_switch_c' => Setting::get('kill_switch_pipeline_c', false),
        ];

        $recent_users = User::with('activeSubscription.plan')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'settings', 'recent_users'));
    }

    public function users()
    {
        $users = User::with('activeSubscription.plan')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $plans = Plan::all();
        $languages = \App\Models\Setting::getSupportedLanguages();
        return view('admin.users.create', compact('plans', 'languages'));
    }

    public function storeUser(Request $request)
    {
          $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'status' => 'required',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'status' => $request->status,
            'role' => 'CLIENT',
            'timezone' => $request->timezone ?? 'UTC',
            'default_language' => $request->default_language ?? 'en',
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'status' => 'ACTIVE',
            'renewal_date' => now()->addMonth(),
            'stripe_subscription_id' => 'manual_' . uniqid(),
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    public function editUser(User $user)
    {
        $plans = Plan::all();
        $languages = \App\Models\Setting::getSupportedLanguages();
        $user->load('activeSubscription');
        return view('admin.users.edit', compact('user', 'plans', 'languages'));
    }

    public function updateUser(Request $request, User $user)
    {
          $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8',
            'status' => 'required',
            'default_language' => 'required|string|max:5',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $userUpdate = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => $data['status'],
            'default_language' => $data['default_language'],
        ];

        if ($request->filled('password')) {
            $userUpdate['password'] = $data['password']; // Cast 'hashed' will handle it
        }

        $user->update($userUpdate);

        // Update plan if changed
        $currentSub = $user->activeSubscription;
        if (!$currentSub || $currentSub->plan_id != $data['plan_id']) {
            if ($currentSub) {
                // Technically we should cancel old one, but for manual override we just update or create new one
                Subscription::where('user_id', $user->id)->update(['status' => 'CANCELLED']);
            }
            
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $data['plan_id'],
                'status' => 'ACTIVE',
                'renewal_date' => now()->addMonth(),
                'stripe_subscription_id' => 'manual_update_' . uniqid(),
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    public function settings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            // If the value is for supported_languages, we ensure it's saved as an array
            // The JSON cast in Setting model will handle the rest
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully');
    }

    public function testDuitku(Request $request)
    {
        $merchantCode = $request->input('duitku_merchant_code', Setting::get('duitku_merchant_code'));
        $apiKey = $request->input('duitku_api_key', Setting::get('duitku_api_key'));
        $mode = $request->input('duitku_mode', Setting::get('duitku_mode', 'sandbox'));
        
        if (empty($merchantCode) || empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Merchant Code and API Key are required.']);
        }

        $datetime = date('Y-m-d H:i:s');  
        $amount = 10000;
        $signature = hash('sha256', $merchantCode . $amount . $datetime . $apiKey);

        $params = [
            'merchantcode' => $merchantCode,
            'amount' => $amount,
            'datetime' => $datetime,
            'signature' => $signature
        ];

        $baseUrl = $mode === 'production' 
            ? 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod'
            : 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';

        try {
            $response = \Illuminate\Support\Facades\Http::post($baseUrl, $params);
            $res = $response->json();

            if (isset($res['paymentFee']) && count($res['paymentFee']) > 0) {
                return response()->json(['success' => true, 'methods' => $res['paymentFee']]);
            } else {
                return response()->json(['success' => false, 'message' => $res['responseMessage'] ?? 'Failed to connect. Invalid credentials or unsupported currency.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Connection Error: ' . $e->getMessage()]);
        }
    }

    public function plans()
    {
        $plans = Plan::all();
        return view('admin.plans.index', compact('plans'));
    }

    public function createPlan()
    {
        return view('admin.plans.create');
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_alerts_per_day' => 'required|integer|min:1',
            'channels_allowed' => 'required|array',
            'daily_outlook' => 'boolean',
            'upcoming_event_alerts' => 'boolean',
            'post_event_reaction' => 'boolean',
            'enable_signal_alert' => 'boolean',
            'tags' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'blotato_key' => 'nullable|string',
            'enable_telegram' => 'boolean',
            'enable_blotato' => 'boolean',
            'enable_email' => 'boolean',
        ]);

        Plan::create($data);

        return redirect()->route('admin.plans')->with('success', 'Plan created successfully');
    }

    public function editPlan(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_alerts_per_day' => 'required|integer|min:1',
            'channels_allowed' => 'required|array',
            'daily_outlook' => 'boolean',
            'upcoming_event_alerts' => 'boolean',
            'post_event_reaction' => 'boolean',
            'enable_signal_alert' => 'boolean',
            'tags' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'blotato_key' => 'nullable|string',
            'enable_telegram' => 'boolean',
            'enable_blotato' => 'boolean',
            'enable_email' => 'boolean',
        ]);

        $plan->update($data);

        return redirect()->route('admin.plans')->with('success', 'Plan updated successfully');
    }

    public function deletePlan(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans')->with('success', 'Plan deleted successfully');
    }

    public function logs()
    {
        $logs = UsageLog::with('user')->latest()->paginate(50);
        $failed = FailedDelivery::latest()->limit(10)->get();
        return view('admin.logs', compact('logs', 'failed'));
    }

    public function toggleSwitch(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|boolean',
        ]);

        Setting::set($request->key, $request->value);

        return back()->with('success', 'Setting updated successfully');
    }

    // ========================================
    // Signal Config Management
    // ========================================

    public function signalConfigs()
    {
        $configs = SignalConfig::latest()->get();
        return view('admin.signal-configs.index', compact('configs'));
    }

    public function createSignalConfig()
    {
        return view('admin.signal-configs.create');
    }

    public function storeSignalConfig(Request $request)
    {
        $data = $request->validate([
            'status' => 'required|in:active,inactive',
            'pairs' => 'required|string',
            'api_keys' => 'required|string',
        ]);

        SignalConfig::create($data);

        return redirect()->route('admin.signal-configs')->with('success', 'Signal config created successfully');
    }

    public function editSignalConfig(SignalConfig $signalConfig)
    {
        return view('admin.signal-configs.edit', compact('signalConfig'));
    }

    public function updateSignalConfig(Request $request, SignalConfig $signalConfig)
    {
        $data = $request->validate([
            'status' => 'required|in:active,inactive',
            'pairs' => 'required|string',
            'api_keys' => 'required|string',
        ]);

        $signalConfig->update($data);

        return redirect()->route('admin.signal-configs')->with('success', 'Signal config updated successfully');
    }

    public function deleteSignalConfig(SignalConfig $signalConfig)
    {
        $signalConfig->delete();
        return redirect()->route('admin.signal-configs')->with('success', 'Signal config deleted successfully');
    }

    // ========================================
    // Signal Data Management
    // ========================================

    public function signals(Request $request)
    {
        $signals = Signal::latest()->paginate(50);

        // Stats computation
        $statsDate = $request->query('stats_date', now()->toDateString());
        $daySignals = Signal::whereDate('created_at', $statsDate)->get();

        $totalSignals = $daySignals->count();
        $buyCount = $daySignals->where('signal', 'BUY')->count();
        $sellCount = $daySignals->where('signal', 'SELL')->count();
        $tpHits = $daySignals->filter(fn($s) => stripos($s->result ?? '', 'TP') !== false || stripos($s->result ?? '', 'WIN') !== false)->count();
        $slHits = $daySignals->filter(fn($s) => stripos($s->result ?? '', 'SL') !== false || stripos($s->result ?? '', 'LOSS') !== false)->count();
        $resolved = $tpHits + $slHits;
        $winRate = $resolved > 0 ? round(($tpHits / $resolved) * 100, 2) . '%' : '0%';
        $topPair = $daySignals->groupBy('pair')->sortByDesc(fn($g) => $g->count())->keys()->first() ?? '-';

        $stats = [
            'date' => $statsDate,
            'total_signals' => $totalSignals,
            'buy_count' => $buyCount,
            'sell_count' => $sellCount,
            'tp_hits' => $tpHits,
            'sl_hits' => $slHits,
            'win_rate' => $winRate,
            'top_pair' => $topPair,
        ];

        return view('admin.signals.index', compact('signals', 'stats'));
    }

    public function createSignal()
    {
        return view('admin.signals.create');
    }

    public function storeSignal(Request $request)
    {
        $data = $request->validate([
            'signal' => 'required|in:BUY,SELL',
            'pair' => 'required|string',
            'price' => 'required|string',
            'sl' => 'nullable|string',
            'tp' => 'nullable|string',
            'reason' => 'nullable|string',
            'signal_timestamp' => 'nullable|string',
            'score' => 'nullable|string',
            'stars' => 'nullable|string',
            'conf_level' => 'nullable|in:HIGH,MEDIUM,LOW',
            'last_sl' => 'nullable|string',
            'last_tp' => 'nullable|string',
            'result' => 'nullable|string',
        ]);

        Signal::create($data);

        return redirect()->route('admin.signals')->with('success', 'Signal created successfully');
    }

    public function editSignal(Signal $signal)
    {
        return view('admin.signals.edit', compact('signal'));
    }

    public function updateSignal(Request $request, Signal $signal)
    {
        $data = $request->validate([
            'signal' => 'required|in:BUY,SELL',
            'pair' => 'required|string',
            'price' => 'required|string',
            'sl' => 'nullable|string',
            'tp' => 'nullable|string',
            'reason' => 'nullable|string',
            'signal_timestamp' => 'nullable|string',
            'score' => 'nullable|string',
            'stars' => 'nullable|string',
            'conf_level' => 'nullable|in:HIGH,MEDIUM,LOW',
            'last_sl' => 'nullable|string',
            'last_tp' => 'nullable|string',
            'result' => 'nullable|string',
        ]);

        $signal->update($data);

        return redirect()->route('admin.signals')->with('success', 'Signal updated successfully');
    }

    public function deleteSignal(Signal $signal)
    {
        $signal->delete();
        return redirect()->route('admin.signals')->with('success', 'Signal deleted successfully');
    }

    public function bulkDeleteSignals(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:signals,id',
        ]);

        Signal::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.signals')->with('success', count($request->ids) . ' signal(s) deleted successfully');
    }
    public function transactions()
    {
        $transactions = Transaction::with(['user', 'plan'])->latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function editTransaction(Transaction $transaction)
    {
        $users = User::all();
        $plans = Plan::all();
        return view('admin.transactions.edit', compact('transaction', 'users', 'plans'));
    }

    public function updateTransaction(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'status' => 'required|in:PENDING,SUCCESS,FAILED',
            'amount' => 'required|numeric',
            'reference_id' => 'nullable|string',
        ]);

        $transaction->update($data);

        return redirect()->route('admin.transactions')->with('success', 'Transaction updated successfully');
    }

    public function deleteTransaction(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('admin.transactions')->with('success', 'Transaction deleted successfully');
    }
}
