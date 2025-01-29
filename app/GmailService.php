<?php

namespace App;

class GmailService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
}

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;

class GmailService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Your App Name');
        $this->client->setScopes(Gmail::GMAIL_SEND);
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->setAccessType('offline');

        $this->service = new Gmail($this->client);
    }

    public function sendEmail($to, $subject, $messageText)
    {
        $message = new \Google\Service\Gmail\Message();
        $rawMessage = "To: {$to}\r\n";
        $rawMessage .= "Subject: {$subject}\r\n";
        $rawMessage .= "\r\n{$messageText}";
        $message->setRaw(base64_encode($rawMessage));

        $this->service->users_messages->send('me', $message);
    }
}
