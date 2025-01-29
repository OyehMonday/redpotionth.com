<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Google_Client;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Storage;

class PasswordResetLinkController extends Controller
{

    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the reset password link request.
     */
    public function store(Request $request)
    {
        // Validate the email address
        $request->validate(['email' => 'required|email']);

        // Generate the password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Send the reset email via Gmail API
            $email = $request->input('email');
            $this->sendResetEmail($email);

            return back()->with('status', __('ลิงก์สำหรับรีเซ็ตรหัสผ่านถูกส่งไปยังอีเมลของคุณแล้ว'));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Send the reset email via Gmail API.
     */
    private function sendResetEmail($toEmail)
    {
        try {
            // Load the Gmail token
            $tokenPath = storage_path('app/google/gmail-token.json');
            if (!file_exists($tokenPath)) {
                throw new \Exception('Gmail token file not found.');
            }
    
            $tokenData = json_decode(file_get_contents($tokenPath), true);
    
            // Initialize the Google Client
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));
            $client->setAccessToken($tokenData);
    
            // Refresh the token if it has expired
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                Storage::put('google/gmail-token.json', json_encode($client->getAccessToken()));
            }
    
            // Retrieve the password reset token
            $token = Password::createToken(
                \App\Models\User::where('email', $toEmail)->first()
            );
    
            if (!$token) {
                throw new \Exception('Failed to generate reset token.');
            }
    
            // Generate the reset password link
            $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($toEmail));

    
            // Create the Gmail service
            $gmail = new Google_Service_Gmail($client);
    
            // Build the email message
            $subject = 'Reset Your Password';
            $messageText = "Click the link below to reset your password:\n\n$resetLink";
    
            $rawMessage = "From: your-email@gmail.com\r\n";
            $rawMessage .= "To: $toEmail\r\n";
            $rawMessage .= "Subject: $subject\r\n\r\n";
            $rawMessage .= $messageText;
    
            $encodedMessage = base64_encode($rawMessage);
            $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
    
            $message = new \Google_Service_Gmail_Message();
            $message->setRaw($encodedMessage);
    
            // Send the email
            $gmail->users_messages->send('me', $message);
    
        } catch (\Exception $e) {
            \Log::error('Failed to send reset email: ' . $e->getMessage());
        }
    }    
}
