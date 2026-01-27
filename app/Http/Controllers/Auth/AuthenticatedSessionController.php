<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request using a simple
     * Auth::attempt()+role-based redirect flow.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Optional debug logging to laravel.log for troubleshooting
        Log::info('Login attempt', [
            'email' => $credentials['email'],
            'remember' => $request->boolean('remember'),
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            Log::warning('Login failed: invalid credentials', [
                'email' => $credentials['email'],
            ]);

            return back()
                ->withErrors(['email' => __('These credentials do not match our records.')])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Simple role-based redirects
        switch ((int) $user->role_id) {
            case 1: // Regular Client
            case 2: // VIP Client
            case 3: // Enterprise Client
                return redirect()->intended(route('client.dashboard'));
            case 4: // Employee
            case 5: // Manager
            case 6: // Supervisor
            case 7: // CEO
                return redirect()->intended(route('employee.dashboard'));
            case 8: // Admin
                return redirect()->intended(route('admin.dashboard'));
            default:
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('error', 'Your account role is not configured correctly.');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

