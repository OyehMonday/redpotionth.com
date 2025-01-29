<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope([
            'https://www.googleapis.com/auth/gmail.send',
        ]);
        $client->setAccessType('offline'); // Request offline access for a refresh token
        $client->setPrompt('consent'); // Force the consent screen to appear for refresh token
    
        $authUrl = $client->createAuthUrl();
    
        return redirect($authUrl);
    }

    /**
     * Handle the callback from Google OAuth.
     */
    public function handleGoogleCallback()
    {
        try {
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));
    
            $authCode = request('code');
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    
            if (isset($accessToken['error'])) {
                return redirect('/')->with('error', 'Failed to generate token: ' . $accessToken['error']);
            }
    
            // Save the token with refresh_token
            Storage::put('google/gmail-token.json', json_encode($accessToken));
    
            return redirect('/')->with('success', 'Token generated successfully!');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
}