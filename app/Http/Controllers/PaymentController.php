<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman pilihan pembayaran (jika belum login/select)
     */
    public function checkout(Request $request, Plan $plan)
    {
        $settings = [
            'stripe_secret' => Setting::get('stripe_secret'),
            'duitku_merchant_code' => Setting::get('duitku_merchant_code'),
            'duitku_api_key' => Setting::get('duitku_api_key'),
            'paypal_client_id' => Setting::get('paypal_client_id'),
            'paypal_secret' => Setting::get('paypal_secret'),
            'default_payment_gateway' => Setting::get('default_payment_gateway', 'stripe')
        ];

        $duitkuMethods = [];
        if ($settings['duitku_merchant_code'] && $settings['duitku_api_key']) {
            $duitkuMethods = $this->getDuitkuMethods($settings['duitku_merchant_code'], $settings['duitku_api_key']);
        }
        
        return view('client.checkout', compact('plan', 'settings', 'duitkuMethods'));
    }

    protected function getDuitkuMethods($merchantCode, $apiKey)
    {
        return \Cache::remember('duitku_methods_' . $merchantCode, 3600, function() use ($merchantCode, $apiKey) {
            $mode = Setting::get('duitku_mode', 'sandbox');
            $datetime = date('Y-m-d H:i:s');
            $amount = 10000; // Minimal amount for checking
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
                $response = Http::post($baseUrl, $params);
                $res = $response->json();
                return $res['paymentFee'] ?? [];
            } catch (\Exception $e) {
                \Log::error('Duitku Get Methods Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Proses inisialisasi pembayaran
     */
    public function process(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'gateway' => 'required|in:stripe,duitku,paypal',
            'payment_method' => 'nullable|string',
        ]);

        $user = Auth::user();
        $plan = Plan::find($request->plan_id);
        
        // 1. Create Pending Transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => $request->gateway,
            'amount' => $plan->price,
            'status' => 'PENDING',
        ]);

        // 2. Redirect to specific gateway logic
        switch ($request->gateway) {
            case 'stripe':
                return $this->handleStripe($transaction);
            case 'duitku':
                return $this->handleDuitku($transaction, $request->payment_method);
            case 'paypal':
                return $this->handlePayPal($transaction);
        }

        return back()->with('error', 'Gateway not supported');
    }

    protected function handleStripe(Transaction $transaction)
    {
        $stripeSecret = Setting::get('stripe_secret');
        if (!$stripeSecret) {
            return back()->with('error', 'Stripe is not configured in settings.');
        }

        try {
            $response = Http::withToken($stripeSecret)
                ->asForm()
                ->post('https://api.stripe.com/v1/checkout/sessions', [
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Subscription: ' . $transaction->plan->name,
                            ],
                            'unit_amount' => (int)($transaction->amount * 100),
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('client.dashboard'),
                    'metadata' => [
                        'transaction_id' => $transaction->id
                    ]
                ]);

            if ($response->failed()) {
                \Log::error('Stripe API Error', $response->json());
                return back()->with('error', 'Stripe error: ' . ($response->json()['error']['message'] ?? 'Unknown error'));
            }

            $session = $response->json();
            
            // Simpan session ID ke transaksi (opsional tapi bagus buat tracking)
            $transaction->update(['status' => 'PENDING']); 

            return redirect()->away($session['url']);
        } catch (\Exception $e) {
            return back()->with('error', 'Stripe initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * DUITKU Logic
     */
    protected function handleDuitku(Transaction $transaction, $paymentMethod = null)
    {
        $merchantCode = trim(Setting::get('duitku_merchant_code'));
        $apiKey = trim(Setting::get('duitku_api_key'));
        $exchangeRate = (int) Setting::get('usd_to_idr_rate', '15500');
        $mode = Setting::get('duitku_mode', 'sandbox');
        
        $merchantOrderId = "INV-" . $transaction->id;
        $paymentAmount = (int) round($transaction->amount * $exchangeRate);
        
        // New Signature for Duitku Pop (createInvoice)
        $timestamp = (int) round(microtime(true) * 1000);
        $signature = hash('sha256', $merchantCode . $timestamp . $apiKey);

        $baseUrl = $mode === 'production' 
            ? 'https://api-prod.duitku.com' 
            : 'https://api-sandbox.duitku.com';
            
        $endpoint = $baseUrl . '/api/merchant/createinvoice';

        // Customer Details
        $customerDetail = [
            'firstName' => $transaction->user->name ?? 'Customer',
            'email' => $transaction->user->email,
        ];

        // Item Details
        $itemDetails = [[
            'name' => 'NewsAuto Subscription: ' . $transaction->plan->name,
            'price' => $paymentAmount,
            'quantity' => 1
        ]];

        $params = [
            'paymentAmount' => $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => 'NewsAuto Subscription: ' . $transaction->plan->name,
            'email' => $transaction->user->email ?? 'client@newsauto.com',
            'customerVaName' => $transaction->user->name ?? 'NewsAuto Client',
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetail,
            'callbackUrl' => url('/api/duitku/callback'),
            'returnUrl' => route('client.dashboard'),
            'expiryPeriod' => 60,
            'paymentMethod' => $paymentMethod
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-duitku-signature' => $signature,
                'x-duitku-timestamp' => $timestamp,
                'x-duitku-merchantcode' => $merchantCode,
                'Content-Type' => 'application/json'
            ])->post($endpoint, $params);
            
            $res = $response->json();

            if (isset($res['paymentUrl'])) {
                return redirect()->away($res['paymentUrl']);
            } else {
                $errorBody = $response->body();
                // Mask the API Key for security when displaying
                $maskedApiKey = substr($apiKey, 0, 4) . '***' . substr($apiKey, -4);
                
                $debugInfo = "
                    Payload Data: 
                    MerchantCode = '{$merchantCode}', 
                    API Key = '{$maskedApiKey}', 
                    Amount = {$paymentAmount}, 
                    OrderId = '{$merchantOrderId}', 
                    Signature = '{$signature}'
                ";
                
                return back()->with('error', "Duitku API Error (HTTP {$response->status()}): {$errorBody} | {$debugInfo}");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Duitku Connection Failed: ' . $e->getMessage());
        }
    }

    protected function handlePayPal(Transaction $transaction)
    {
        $clientId = Setting::get('paypal_client_id');
        $secret = Setting::get('paypal_secret');
        
        if (!$clientId || !$secret) {
            return back()->with('error', 'PayPal is not configured in settings.');
        }

        try {
            // 1. Get Access Token
            $mode = Setting::get('paypal_mode', 'sandbox');
            $baseUrl = $mode === 'production' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
            
            $tokenResponse = Http::withBasicAuth($clientId, $secret)
                ->asForm()
                ->post($baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($tokenResponse->failed()) {
                return back()->with('error', 'PayPal Auth failed: ' . ($tokenResponse->json()['error_description'] ?? 'Connection error'));
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // 2. Create Order
            $baseUrl = $mode === 'production' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
            $orderResponse = Http::withToken($accessToken)
                ->post($baseUrl . '/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($transaction->amount, 2, '.', '')
                        ],
                        'description' => 'NewsAuto Subscription: ' . $transaction->plan->name
                    ]],
                    'application_context' => [
                        'return_url' => route('payment.success'),
                        'cancel_url' => route('client.dashboard')
                    ]
                ]);

            if ($orderResponse->failed()) {
                return back()->with('error', 'PayPal Order creation failed.');
            }

            $order = $orderResponse->json();
            $approveLink = collect($order['links'])->where('rel', 'approve')->first()['href'];

            return redirect()->away($approveLink);
        } catch (\Exception $e) {
            return back()->with('error', 'PayPal initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Success Callback (General)
     */
    public function success(Request $request)
    {
        return redirect()->route('client.dashboard')->with('success', 'Payment successful! Your plan is being activated.');
    }

    public function duitkuCallback(Request $request)
    {
        $merchantCode = Setting::get('duitku_merchant_code');
        $apiKey = Setting::get('duitku_api_key');
        
        $amount = $request->amount;
        $merchantOrderId = $request->merchantOrderId;
        $signature = $request->signature;
        $resultCode = $request->resultCode;

        // Verify Signature
        $params = $merchantCode . $amount . $merchantOrderId . $apiKey;
        $calcSignature = md5($params);

        if ($signature !== $calcSignature) {
            \Log::error('Duitku Callback: Invalid Signature', ['received' => $signature, 'expected' => $calcSignature]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        if ($resultCode == "00") {
            // Success! Update Transaction & Activate Subscription
            $transactionId = str_replace('INV-', '', $merchantOrderId);
            $transaction = Transaction::find($transactionId);

            if ($transaction && $transaction->status !== 'SUCCESS') {
                $transaction->update(['status' => 'SUCCESS']);
                
                // Activate/Renew Subscription logic
                Subscription::updateOrCreate(
                    ['user_id' => $transaction->user_id],
                    [
                        'plan_id' => $transaction->plan_id,
                        'status' => 'ACTIVE',
                        'renewal_date' => now()->addMonth(),
                    ]
                );
                
                $transaction->user->update(['status' => 'ACTIVE']);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
