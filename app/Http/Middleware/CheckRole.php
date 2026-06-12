<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Block pending school coordinators immediately before accessing system views
        if ($user->role === 'Coordinator' && $user->status === 'Pending') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your coordinator account registration is pending approval from a Program Manager.'
            ]);
        }

        // 3. Check if user's role matches any allowed roles passed to the route group
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Fallback for unauthorized access attempts
        abort(403, 'Unauthorized action. You do not have the required role privileges.');
    }
}
