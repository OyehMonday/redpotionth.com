<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash; 
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class CustomAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            // Get user details from Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists in the database
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create a new user if not already in the database
                $user = User::create([
                    'username' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(uniqid()), // Generate a random password
                    'google_id' => $googleUser->getId(), // Store Google ID (optional)
                ]);
            }

            // Log the user in by saving them to the session
            Session::put('user', $user);

            return redirect()->route('dashboard')->with('success', 'Logged in successfully!');
        } catch (\Exception $e) {
            return redirect()->route('custom.login.form')->with('error', 'Failed to authenticate with Google.');
        }
    }

    /**
     * Show the sign-up form.
     */
    public function showSignUpForm()
    {
        if (Session::has('user')) {
            return redirect()->route('landing.page'); // Adjust route name as needed
        }        
        return view('signup');
    }

    /**
     * Handle sign-up logic.
     */
    public function signUp(Request $request)
    {
        // Validate inputs
        $request->validate([
            'username' => 'required|unique:users,username|max:50',
            'email' => 'required|email|unique:users,email|max:100',
            'password' => 'required|min:6|max:50',
        ]);

        // Create a new user with hashed password
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password
        ]);

        return redirect()->route('custom.login.form')->with('success', 'Account created successfully! Please login.');
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Session::has('user')) {
            return redirect()->route('landing.page'); // Adjust route name as needed
        }
        return view('login');
    }

    /**
     * Handle login logic.
     */
    public function login(Request $request)
    {
        // Validate inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) { // Verify hashed password
            // Store user in session
            Session::put('user', $user);

            return redirect()->route('dashboard');
        }

        return redirect()->back()->with('error', 'Invalid email or password.');
    }

    /**
     * Show dashboard.
     */
    public function dashboard()
    {
        if (!Session::has('user')) {
            return redirect()->route('custom.login.form');
        }

        return view('dashboard', ['user' => Session::get('user')]);
    }

    /**
     * Logout the user.
     */
    public function logout()
    {
        Session::forget('user');
        return redirect()->route('custom.login.form');
    }
}
