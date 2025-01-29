<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

class GmailService
{
    protected $client;
    protected $tokenPath;

    public function __construct()
    {
        $this->tokenPath = storage_path('app/google/gmail-token.json');

        // Ensure the token file exists
        if (!file_exists($this->tokenPath)) {
            throw new \Exception("Gmail API credentials file not found at: " . $this->tokenPath);
        }

        $this->initializeClient();
    }

    protected function initializeClient()
    {
        // Load credentials from the token file
        $credentials = json_decode(file_get_contents($this->tokenPath), true);

        if (!isset($credentials['client_id'], $credentials['client_secret'], $credentials['refresh_token'])) {
            throw new \Exception("Invalid Gmail API credentials file. Ensure 'client_id', 'client_secret', and 'refresh_token' are present.");
        }

        $this->client = new Client();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setClientId($credentials['client_id']);
        $this->client->setClientSecret($credentials['client_secret']);
        $this->client->setAccessType('offline');
        $this->client->setRedirectUri('https://developers.google.com/oauthplayground');
        $this->client->setScopes([Gmail::MAIL_GOOGLE_COM]);

        // Set access token if available
        if (!empty($credentials['access_token'])) {
            $this->client->setAccessToken($credentials);
        }

        // Refresh the token if expired
        if ($this->client->isAccessTokenExpired()) {
            if (isset($credentials['refresh_token'])) {
                $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($credentials['refresh_token']);
                $credentials['access_token'] = $newAccessToken['access_token'];

                // Save updated token back to the JSON file
                file_put_contents($this->tokenPath, json_encode($credentials, JSON_PRETTY_PRINT));
            } else {
                throw new \Exception("Gmail API token has expired and no refresh token is available.");
            }
        }
    }

    public function sendMail($to, $subject, $body)
    {
        $service = new Gmail($this->client);
        $message = new Message();

        // Prepare raw email content
        $rawMessage = "To: $to\r\n";
        $rawMessage .= "Subject: $subject\r\n";
        $rawMessage .= "MIME-Version: 1.0\r\n";
        $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
        $rawMessage .= $body;

        // Encode message and send
        $rawMessage = str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($rawMessage));
        $message->setRaw($rawMessage);

        return $service->users_messages->send('me', $message);
    }
}
