<?php

namespace App\Console\Commands;

use Google\Client;
use Illuminate\Console\Command;

class GenerateGmailToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gmail:generate-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Gmail API Access Token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client();
        $client->setApplicationName('Your App Name');
        $client->setScopes(\Google\Service\Gmail::GMAIL_SEND);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');

        $authUrl = $client->createAuthUrl();
        $this->info("Open the following link in your browser:\n$authUrl");

        $authCode = $this->ask('Enter the verification code:');
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        file_put_contents(storage_path('app/google/token.json'), json_encode($accessToken));
        $this->info('Access token saved to storage/app/google/token.json');
    }
}
