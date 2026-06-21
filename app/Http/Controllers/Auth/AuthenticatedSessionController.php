<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // 1. Fetch the authenticated user instance
        $user = $request->user();

        // 2. Check if their approval status is not 'Approved'
        if ($user->status !== 'Approved') {

            // 3. Force log them out immediately to clear the session
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 4. Throw a clean error message back to the login screen field
            $message = $user->status === 'Pending'
                ? 'Your account is currently awaiting approval from a Program Manager.'
                : 'Your account registration has been rejected.';

            throw ValidationException::withMessages([
                'email' => [$message],
            ]);
        }

        $request->session()->regenerate();

        // 5. Handle role-based dashboard routing
        if ($user->role === 'Admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'Program Manager') {
            return redirect()->route('manager.dashboard');
        } elseif ($user->role === 'Coordinator') {
            return redirect()->route('coordinator.dashboard');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
