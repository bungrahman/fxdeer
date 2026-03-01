<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return $this->redirectUser();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        $languages = \App\Models\Setting::getSupportedLanguages();
        return view('auth.register', compact('languages'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'plan_id' => 'nullable|exists:plans,id',
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => $data['password'], // Cast 'hashed' will handle it
            'timezone' => 'UTC',
            'role' => 'CLIENT',
            'status' => 'ACTIVE',
            'default_language' => $request->default_language ?? 'en',
        ]);

        Auth::login($user);

        if (isset($data['plan_id'])) {
            return redirect()->route('payment.checkout', ['plan' => $data['plan_id']]);
        }

        return redirect()->route('client.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    protected function redirectUser()
    {
        if (Auth::user()->role === 'ADMIN') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('client.dashboard');
    }
}
