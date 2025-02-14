<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Notifications\AdminSignupVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Google_Client;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminVerificationMail;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
    
        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            $admin = Auth::guard('admin')->user();
    
            if (!$admin->is_verified) {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->withErrors(['email' => 'Please verify your email before logging in.']);
            }
    
            return redirect()->route('admin.orders.index');
        }
    
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }
    

    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function showSignupForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.signup'); 
    }
    
    public function signup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $verificationToken = Str::random(32);
    
        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verification_token' => $verificationToken,
        ]);
    
        $adminEmail = 'redpotionth@gmail.com';

        // Send the verification email using AdminVerificationMail
        Mail::to($adminEmail)->send(new AdminVerificationMail($verificationToken));
    
        return response()->json([
            'message' => 'Admin created, please check your email for the verification link.'
        ]);
    }
    

    // public function sendVerificationEmail($toEmail, $verificationToken)
    // {
    //     $verificationLink = url('/admin/verify/' . $verificationToken);
    
    //     Mail::to($toEmail)->send(new AdminVerificationMail($verificationLink));
    // }
    

    // Send the verification email via Gmail API
    // private function sendVerificationEmail($toEmail, $verificationToken)
    // {
    //     try {
    //         // Gmail API token path
    //         $tokenPath = storage_path('app/google/gmail-token.json');
    //         if (!file_exists($tokenPath)) {
    //             throw new \Exception('Gmail token file not found.');
    //         }

    //         $tokenData = json_decode(file_get_contents($tokenPath), true);

    //         // Initialize Google Client
    //         $client = new Google_Client();
    //         $client->setClientId(config('services.google.client_id'));
    //         $client->setClientSecret(config('services.google.client_secret'));
    //         $client->setRedirectUri(config('services.google.redirect'));
    //         $client->setAccessToken($tokenData);

    //         // Refresh the token if expired
    //         if ($client->isAccessTokenExpired()) {
    //             $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    //             Storage::put('google/gmail-token.json', json_encode($client->getAccessToken()));
    //         }

    //         // Hardcoding the email to send the verification link to redpotionth@gmail.com
    //         $verificationLink = url('/admin/verify/' . $verificationToken . '?email=' . urlencode($toEmail));

    //         // Create the Gmail service
    //         $gmail = new Google_Service_Gmail($client);

    //         // Build email message
    //         $subject = 'Admin Account Verification';
    //         $messageText = "Click the link below to verify the admin account:\n\n$verificationLink";

    //         $rawMessage = "From: redpotionth@gmail.com\r\n";
    //         $rawMessage .= "To: redpotionth@gmail.com\r\n"; // Send to redpotionth@gmail.com
    //         $rawMessage .= "Subject: $subject\r\n\r\n";
    //         $rawMessage .= $messageText;

    //         // Base64 encode the raw message and replace URL-safe characters
    //         $encodedMessage = base64_encode($rawMessage);
    //         $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);

    //         $message = new \Google_Service_Gmail_Message();
    //         $message->setRaw($encodedMessage);

    //         // Log message to confirm email content
    //         \Log::info('Sending verification email to redpotionth@gmail.com.');

    //         // Send the email
    //         $gmail->users_messages->send('me', $message);

    //         // Log success
    //         \Log::info('Verification email sent successfully to redpotionth@gmail.com');

    //     } catch (\Exception $e) {
    //         \Log::error('Failed to send verification email: ' . $e->getMessage());
    //     }
    // }

    public function verify($token)
    {
        $admin = Admin::where('email_verification_token', $token)->first();
    
        if (!$admin) {
            return redirect()->route('admin.login')->withErrors(['error' => 'Invalid verification link.']);
        }
    
        // Mark the admin as verified
        $admin->is_verified = true;
        $admin->email_verification_token = null; 
        $admin->save();
    
        return redirect()->route('admin.login')->with('message', 'Email verified successfully.');
    }    

    // Verify the admin account
    // public function verify($token)
    // {
    //     // Find admin by verification token
    //     $admin = Admin::where('email_verification_token', $token)->first();
    
    //     if (!$admin) {
    //         // If no admin found with the token, return with an error
    //         return redirect()->route('admin.login')->withErrors(['error' => 'Invalid verification link.']);
    //     }
    
    //     // Update verification status
    //     $admin->is_verified = true;
    //     $admin->email_verification_token = null; // Remove token
    //     $admin->save();
    
    //     return redirect()->route('admin.login')->with('message', 'Email verified successfully.');
    // }
    
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
