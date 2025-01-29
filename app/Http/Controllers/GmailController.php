<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Storage;

class GmailController extends Controller
{
    public function sendTestEmail()
    {
        $to = 'redpotionth@gmail.com';
        $subject = 'Test Email from Gmail API';
        $messageText = 'This is a test email sent using the Gmail API.';

        // Load the token
        $tokenPath = storage_path('app/google/gmail-token.json');
        if (!file_exists($tokenPath)) {
            return response()->json(['error' => 'Token file not found.'], 500);
        }

        $tokenData = json_decode(file_get_contents($tokenPath), true);

        // Initialize Google Client
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id')); // Set client ID
        $client->setClientSecret(config('services.google.client_secret')); // Set client secret
        $client->setRedirectUri(config('services.google.redirect')); // Set redirect URI
        $client->setAccessToken($tokenData); // Set access token

        // Refresh the access token if it has expired
        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                // Refresh the access token
                $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

                // Save the refreshed token back to the file
                Storage::put('google/gmail-token.json', json_encode($client->getAccessToken()));
            } else {
                return response()->json(['error' => 'Refresh token is missing. Please re-authenticate.'], 401);
            }
        }

        // Create Gmail service
        $gmail = new Google_Service_Gmail($client);

        // Create the email message
        $rawMessage = "From: redpotionth@gmail.com\r\n";
        $rawMessage .= "To: $to\r\n";
        $rawMessage .= "Subject: $subject\r\n\r\n";
        $rawMessage .= "$messageText";

        $encodedMessage = base64_encode($rawMessage);
        $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);

        $message = new \Google_Service_Gmail_Message();
        $message->setRaw($encodedMessage);

        // Send the email
        try {
            $gmail->users_messages->send('me', $message);
            return response()->json(['success' => 'Email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
