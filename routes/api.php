<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Endpoints untuk n8n
Route::prefix('events')->group(function () {
    Route::get('/users/active', [EventController::class, 'fetchActiveUsers']);
    Route::get('/plans/{plan}', [EventController::class, 'fetchPlanRules']);
    Route::post('/eligible', [EventController::class, 'checkEligibility']);
    Route::post('/mark-sent', [EventController::class, 'markAsSent']);
});

// Admin Control Plane (Protected)
Route::prefix('admin')->group(function () {
    Route::post('/settings', [\App\Http\Controllers\Api\AdminController::class, 'updateSettings']);
    Route::post('/users/{user}/status', [\App\Http\Controllers\Api\AdminController::class, 'updateUserStatus']);
    Route::post('/reset-quotas', [\App\Http\Controllers\Api\AdminController::class, 'resetDailyQuotas']);
});

// Signal Config CRUD
Route::get('/signal_config', [\App\Http\Controllers\Api\SignalConfigController::class, 'index']);
Route::post('/signal_config', [\App\Http\Controllers\Api\SignalConfigController::class, 'store']);
Route::put('/signal_config/{id}', [\App\Http\Controllers\Api\SignalConfigController::class, 'update']);
Route::delete('/signal_config/{id}', [\App\Http\Controllers\Api\SignalConfigController::class, 'destroy']);

// Signals CRUD
Route::get('/signals', [\App\Http\Controllers\Api\SignalController::class, 'index']);
Route::post('/signals', [\App\Http\Controllers\Api\SignalController::class, 'store']);
Route::put('/signals/{id}', [\App\Http\Controllers\Api\SignalController::class, 'update']);
Route::delete('/signals/{id}', [\App\Http\Controllers\Api\SignalController::class, 'destroy']);
Route::post('/signals/bulk-delete', [\App\Http\Controllers\Api\SignalController::class, 'bulkDelete']);
Route::get('/signals/stats', [\App\Http\Controllers\Api\SignalController::class, 'stats']);

// Payment Gateways Webhooks & Callbacks
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
Route::post('/duitku/callback', [\App\Http\Controllers\PaymentController::class, 'duitkuCallback']);
