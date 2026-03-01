<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Models\Plan;

Route::middleware(['maintenance'])->group(function () {
    Route::get('/', function () {
        $plans = Plan::all();
        return view('welcome', compact('plans'));
    })->name('home');
});
// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Client Area (Protected) - Also under maintenance
Route::middleware(['auth', 'role:CLIENT', 'maintenance'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\ClientController::class, 'index'])->name('client.dashboard');
    Route::get('/pipelines', [\App\Http\Controllers\ClientController::class, 'pipelines'])->name('client.pipelines');
    Route::get('/billing', [\App\Http\Controllers\ClientController::class, 'billing'])->name('client.billing');
    Route::post('/settings/bot', [\App\Http\Controllers\ClientController::class, 'updateBotSettings'])->name('client.settings.bot');
    Route::post('/settings/language', [\App\Http\Controllers\ClientController::class, 'updateLanguage'])->name('client.settings.language');
    
    // Payment Flow
    Route::get('/checkout/{plan}', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/process', [\App\Http\Controllers\PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
});

// Admin Area (Protected)
Route::prefix('admin')->middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [\App\Http\Controllers\AdminDashboardController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [\App\Http\Controllers\AdminDashboardController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [\App\Http\Controllers\AdminDashboardController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\AdminDashboardController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminDashboardController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/plans', [\App\Http\Controllers\AdminDashboardController::class, 'plans'])->name('admin.plans');
    Route::get('/plans/create', [\App\Http\Controllers\AdminDashboardController::class, 'createPlan'])->name('admin.plans.create');
    Route::post('/plans', [\App\Http\Controllers\AdminDashboardController::class, 'storePlan'])->name('admin.plans.store');
    Route::get('/plans/{plan}/edit', [\App\Http\Controllers\AdminDashboardController::class, 'editPlan'])->name('admin.plans.edit');
    Route::put('/plans/{plan}', [\App\Http\Controllers\AdminDashboardController::class, 'updatePlan'])->name('admin.plans.update');
    Route::delete('/plans/{plan}', [\App\Http\Controllers\AdminDashboardController::class, 'deletePlan'])->name('admin.plans.delete');
    Route::get('/logs', [\App\Http\Controllers\AdminDashboardController::class, 'logs'])->name('admin.logs');
    Route::get('/settings', [\App\Http\Controllers\AdminDashboardController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [\App\Http\Controllers\AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');
    Route::post('/settings/test-duitku', [\App\Http\Controllers\AdminDashboardController::class, 'testDuitku'])->name('admin.settings.test-duitku');
    Route::get('/transactions', [\App\Http\Controllers\AdminDashboardController::class, 'transactions'])->name('admin.transactions');
    Route::get('/transactions/{transaction}/edit', [\App\Http\Controllers\AdminDashboardController::class, 'editTransaction'])->name('admin.transactions.edit');
    Route::put('/transactions/{transaction}', [\App\Http\Controllers\AdminDashboardController::class, 'updateTransaction'])->name('admin.transactions.update');
    Route::delete('/transactions/{transaction}', [\App\Http\Controllers\AdminDashboardController::class, 'deleteTransaction'])->name('admin.transactions.delete');
    Route::post('/toggle-switch', [\App\Http\Controllers\AdminDashboardController::class, 'toggleSwitch'])->name('admin.toggle-switch');

    // Signal Config Management
    Route::get('/signal-configs', [\App\Http\Controllers\AdminDashboardController::class, 'signalConfigs'])->name('admin.signal-configs');
    Route::get('/signal-configs/create', [\App\Http\Controllers\AdminDashboardController::class, 'createSignalConfig'])->name('admin.signal-configs.create');
    Route::post('/signal-configs', [\App\Http\Controllers\AdminDashboardController::class, 'storeSignalConfig'])->name('admin.signal-configs.store');
    Route::get('/signal-configs/{signalConfig}/edit', [\App\Http\Controllers\AdminDashboardController::class, 'editSignalConfig'])->name('admin.signal-configs.edit');
    Route::put('/signal-configs/{signalConfig}', [\App\Http\Controllers\AdminDashboardController::class, 'updateSignalConfig'])->name('admin.signal-configs.update');
    Route::delete('/signal-configs/{signalConfig}', [\App\Http\Controllers\AdminDashboardController::class, 'deleteSignalConfig'])->name('admin.signal-configs.delete');

    // Signal Data Management
    Route::get('/signals', [\App\Http\Controllers\AdminDashboardController::class, 'signals'])->name('admin.signals');
    Route::get('/signals/create', [\App\Http\Controllers\AdminDashboardController::class, 'createSignal'])->name('admin.signals.create');
    Route::post('/signals', [\App\Http\Controllers\AdminDashboardController::class, 'storeSignal'])->name('admin.signals.store');
    Route::get('/signals/{signal}/edit', [\App\Http\Controllers\AdminDashboardController::class, 'editSignal'])->name('admin.signals.edit');
    Route::put('/signals/{signal}', [\App\Http\Controllers\AdminDashboardController::class, 'updateSignal'])->name('admin.signals.update');
    Route::delete('/signals/{signal}', [\App\Http\Controllers\AdminDashboardController::class, 'deleteSignal'])->name('admin.signals.delete');
    Route::post('/signals/bulk-delete', [\App\Http\Controllers\AdminDashboardController::class, 'bulkDeleteSignals'])->name('admin.signals.bulk-delete');
});
