<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Client;
use Google\Service\Gmail;
use Swift_Mailer;
use Swift_SmtpTransport;

class GmailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('GmailClient', function () {
            $client = new Client();
            $client->setApplicationName(config('app.name'));
            $client->setAuthConfig(storage_path('app/google/gmail-token.json'));
            $client->setScopes(Gmail::MAIL_GOOGLE_COM);
            $client->setAccessType('offline');

            return $client;
        });

        $this->app->extend('mailer', function ($service, $app) {
            $client = $app->make('GmailClient');

            $transport = new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
            $transport->setUsername(config('mail.username'));
            $transport->setPassword(config('mail.password'));

            return new Swift_Mailer($transport);
        });
    }
}
