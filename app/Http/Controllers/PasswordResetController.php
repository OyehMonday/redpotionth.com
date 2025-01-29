<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Import Hash facade for password hashing
use Illuminate\Support\Str;
use App\Services\GmailService;
use App\Models\User;

class PasswordResetController extends Controller
{
    /**
     * Show the Forgot Password form.
     */
    public function showForgotPasswordForm()
    {
        return view('forgot-password');
    }

    /**
     * Send a password reset link to the user's email.
     */
    public function sendResetLink(Request $request, GmailService $gmailService)
    {
        // Validate input
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Generate token
        $token = Str::random(60);

        // Store token in the password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Generate the reset link
        $resetLink = url('/reset-password/' . $token);

        // Email body
        $emailBody = view('emails.reset-password', ['resetLink' => $resetLink])->render();

        // Send the email via GmailService
        try {
            $gmailService->sendMail($request->email, 'Reset Your Password', $emailBody);
            return back()->with('status', 'Password reset link sent successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the Reset Password form.
     */
    public function showResetPasswordForm($token)
    {
        return view('reset-password', ['token' => $token]);
    }

    /**
     * Handle the reset password submission.
     */
    public function resetPassword(Request $request)
    {
        // Validate the form input
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed', // Ensure password confirmation
            'token' => 'required'
        ]);

        // Check if the token exists in the password_resets table
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password); // Hash the password
            $user->save();

            // Delete the token from the password_resets table
            DB::table('password_resets')->where('email', $request->email)->delete();

            // Redirect to the login page with a success message
            return redirect()->route('custom.login.form')->with('success', 'Your password has been reset successfully!');
        }

        // In case the user is not found (rare scenario)
        return back()->withErrors(['email' => 'User not found.']);
    }
}
