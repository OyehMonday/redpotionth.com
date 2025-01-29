<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;

class AdminSignupVerification extends Notification
{
    public $admin;

    public function __construct($admin)
    {
        $this->admin = $admin;
    }

    public function via($notifiable)
    {
        return ['mail'];  // We'll handle the email manually through Gmail API
    }

    public function toMail($notifiable)
    {
        // Generate verification token and URL
        $verificationUrl = route('admin.verify', ['id' => $this->admin->id, 'token' => $this->admin->email_verification_token]);

        // Send the email via Gmail API
        $this->sendGmail($notifiable, $verificationUrl);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Please Verify Your Admin Account')
            ->line('Thank you for signing up as an admin.')
            ->line('Click the link below to verify your email address and complete your registration:')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not create an account, no further action is required.');
    }

    /**
     * Send Gmail using the Gmail API.
     */
    protected function sendGmail($notifiable, $verificationUrl)
    {
        // Initialize Google Client
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google/gmail-token.json'));  // Path to the token
        $client->addScope(Google_Service_Gmail::GMAIL_SEND);  // Define Gmail send scope
    
        // Create Gmail API service
        $service = new Google_Service_Gmail($client);
    
        // Construct the email content
        $subject = 'Please Verify Your Admin Account';
        $body = "Thank you for signing up as an admin. Please verify your email by clicking the link below: " . $verificationUrl;
        $from = 'redpotionth@gmail.com';
        $to = $notifiable->email;
    
        // Generate the MIME message
        $mimeMessage = $this->createMimeMessage($subject, $from, $to, $body);
    
        try {
            // Create Gmail message
            $message = new Google_Service_Gmail_Message();
            $message->setRaw($mimeMessage);
            
            // Send the email using Gmail API
            $service->users_messages->send('me', $message);
            
            Log::info("Verification email sent to: " . $notifiable->email);
        } catch (\Exception $e) {
            Log::error("Error sending email: " . $e->getMessage());
        }
    }
    

    /**
     * Create MIME message format.
     */
    protected function createMimeMessage($subject, $from, $to, $body)
    {
        // Headers for the email
        $headers = [
            'From' => $from,
            'To' => $to,
            'Subject' => $subject,
        ];
    
        // Prepare the MIME message content
        $mimeMessage = base64_encode(implode("\r\n", array_map(
            function ($key, $value) { return "$key: $value"; },
            array_keys($headers), $headers
        )) . "\r\n\r\n" . $body);
    
        return $mimeMessage;
    }
}
