<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Display the reset password form.
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showResetForm($token)
    {
        auth()->logout();
        session()->flush();
        session()->invalidate();
        session()->regenerate();
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle the reset password form submission.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
    
                // ✅ Store a flag that the password has been changed
                $user->update(['password_updated_at' => now()]);
            }
        );
    
        if ($status === Password::PASSWORD_RESET) {
            // ✅ Log session data before logout
            Log::info('Session Data Before Logout:', session()->all());
    
            // ✅ Hard reset the session
            auth()->logout();
            session()->flush();
            session()->invalidate();
            session()->regenerateToken();
    
            // ✅ Log after session flush
            Log::info('Session Data After Logout:', session()->all());
    
            return redirect()->route('password.reset.success');
        }
    
        return back()->withErrors(['email' => [__($status)]]);
    }
    

}
