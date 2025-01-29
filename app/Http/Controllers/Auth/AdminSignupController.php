<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Google_Client;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Storage;

class AdminSignupController extends Controller
{
    public function create()
    {
        return view('admin.signup'); // Admin signup form view
    }

    /**
     * Handle the admin signup request.
     */
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Generate a verification token
        $verificationToken = Str::random(32);

        // Create the new admin
        $admin = Admin::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'email_verification_token' => $verificationToken,
        ]);

        // Send verification email via Gmail API
        $this->sendVerificationEmail($admin->email, $verificationToken);

        return back()->with('status', 'Admin created, please check your email for the verification link.');
    }

    /**
     * Send the verification email via Gmail API.
     */
    private function sendVerificationEmail($toEmail, $verificationToken)
    {
        try {
            // Gmail API token path
            $tokenPath = storage_path('app/google/gmail-token.json');
            if (!file_exists($tokenPath)) {
                throw new \Exception('Gmail token file not found.');
            }
    
            $tokenData = json_decode(file_get_contents($tokenPath), true);
    
            // Initialize Google Client
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));
            $client->setAccessToken($tokenData);
    
            // Refresh the token if expired
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                Storage::put('google/gmail-token.json', json_encode($client->getAccessToken()));
            }
    
            // Hardcoding the email to send the verification link to redpotionth@gmail.com
            $verificationLink = url('/admin/verify/' . $verificationToken . '?email=' . urlencode('redpotionth@gmail.com'));
    
            // Create the Gmail service
            $gmail = new Google_Service_Gmail($client);
    
            // Build email message
            $subject = 'Admin Account Verification';
            $messageText = "Click the link below to verify the admin account:\n\n$verificationLink";
    
            $rawMessage = "From: your-email@gmail.com\r\n";
            $rawMessage .= "To: redpotionth@gmail.com\r\n"; // Send to redpotionth@gmail.com
            $rawMessage .= "Subject: $subject\r\n\r\n";
            $rawMessage .= $messageText;
    
            // Base64 encode the raw message and replace URL-safe characters
            $encodedMessage = base64_encode($rawMessage);
            $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
    
            $message = new \Google_Service_Gmail_Message();
            $message->setRaw($encodedMessage);
    
            // Log message to confirm encoding
            \Log::info('Sending email to redpotionth@gmail.com with verification link: ' . $verificationLink);
    
            // Send the email
            $gmail->users_messages->send('me', $message);
    
            // Log success
            \Log::info('Verification email sent to: redpotionth@gmail.com');
    
        } catch (\Exception $e) {
            // Log any errors
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }
    
}
