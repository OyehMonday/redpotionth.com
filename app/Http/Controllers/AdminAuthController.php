<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Notifications\AdminSignupVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // Show the admin signup form (This method was missing)
    public function showSignupForm()
    {
        return view('admin.signup');
    }

    // Handle the admin signup
    public function signup(Request $request)
    {
        // Validate input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Generate verification token
        $verificationToken = Str::random(32);

        // Create the new admin
        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verification_token' => $verificationToken,
        ]);

        // Send verification email
        $admin->notify(new AdminSignupVerification($admin));

        return response()->json([
            'message' => 'Admin created, please check your email for the verification link.'
        ]);
    }

    public function verify($id, $token)
    {
        // Find admin and verify token
        $admin = Admin::findOrFail($id);

        if ($admin->email_verification_token === $token) {
            // Update verification status
            $admin->is_verified = true;
            $admin->email_verification_token = null; // Remove token
            $admin->save();

            return redirect()->route('admin.dashboard')->with('message', 'Email verified successfully.');
        }

        return redirect()->route('admin.login')->withErrors(['error' => 'Invalid verification link.']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
