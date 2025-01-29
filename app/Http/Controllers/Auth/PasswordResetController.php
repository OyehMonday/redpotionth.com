<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending the password reset link.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __('A password reset link has been sent to your email.'))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show the reset password form.
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle the password reset process.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'password_updated_at' => now(), // ✅ Prevents redirect loop
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Auth::logout();  // ✅ Logout to reset session
            session()->flush();
            session()->invalidate();
            session()->regenerate();

            return redirect()->route('login')->with('success', __('Your password has been reset! You can now log in.'));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
