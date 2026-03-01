<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     * POST /api/stripe/webhook
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Verify webhook signature (uncomment when Stripe is configured)
            // $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            
            // For now, parse the payload directly
            $event = json_decode($payload, true);
            
            Log::info('Stripe webhook received', ['type' => $event['type']]);

            // Handle different event types
            switch ($event['type']) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event['data']['object']);
                    break;

                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($event['data']['object']);
                    break;

                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event['data']['object']);
                    break;

                case 'customer.subscription.deleted':
                case 'customer.subscription.updated':
                    $this->handleSubscriptionCancelled($event['data']['object']);
                    break;

                default:
                    Log::info('Unhandled webhook event type', ['type' => $event['type']]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Handle subscription.created: Aktifkan akses user
     */
    private function handleSubscriptionCreated($subscriptionData)
    {
        $stripeSubscriptionId = $subscriptionData['id'];
        $customerId = $subscriptionData['customer'];

        // Find user by Stripe customer ID (assumes you have this field)
        // For now, we'll find by subscription ID
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'ACTIVE',
                'renewal_date' => now()->addMonth(),
            ]);

            // Activate user
            $subscription->user->update(['status' => 'ACTIVE']);

            Log::info('Subscription activated', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);
        }
    }

    /**
     * Handle payment_failed: Auto-suspend akun
     */
    private function handlePaymentFailed($invoiceData)
    {
        $subscriptionId = $invoiceData['subscription'] ?? null;

        if ($subscriptionId) {
            $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

            if ($subscription) {
                // Suspend user
                $subscription->user->update(['status' => 'SUSPENDED']);

                Log::warning('User suspended due to payment failure', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                ]);
            }
        }
    }

    /**
     * Handle checkout.session.completed
     */
    private function handleCheckoutSessionCompleted($session)
    {
        $transactionId = $session['metadata']['transaction_id'] ?? null;

        if ($transactionId) {
            $transaction = \App\Models\Transaction::find($transactionId);

            if ($transaction && $transaction->status !== 'SUCCESS') {
                $transaction->update(['status' => 'SUCCESS']);

                // Create or Update subscription
                \App\Models\Subscription::updateOrCreate(
                    ['user_id' => $transaction->user_id],
                    [
                        'plan_id' => $transaction->plan_id,
                        'status' => 'ACTIVE',
                        'renewal_date' => now()->addMonth(),
                    ]
                );

                $transaction->user->update(['status' => 'ACTIVE']);

                Log::info('Stripe Checkout Completed', [
                    'transaction_id' => $transactionId,
                    'user_id' => $transaction->user_id
                ]);
            }
        }
    }

    /**
     * Handle subscription.created: Aktifkan akses user
