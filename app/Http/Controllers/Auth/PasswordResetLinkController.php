<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = null;

        if (app()->environment('local')) {
            $user = User::query()->where('email', '=', (string) $request->input('email'))->first();
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            $resetLink = null;

            if (app()->environment('local') && $user) {
                // Generate this after RESET_LINK_SENT so the token shown in UI is the latest valid token.
                $token = Password::createToken($user);
                $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);
            }

            $response = back()->with('status', __($status));

            if ($resetLink) {
                $response->with('dev_reset_link', $resetLink);
            }

            return $response;
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
