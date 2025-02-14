<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LineNotificationService
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = env('LINE_ACCESS_TOKEN');
    }

    public function sendMessage($to, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type'  => 'application/json',
        ])->post('https://api.line.me/v2/bot/message/push', [
            'to' => $to,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ],
            ],
        ]);

        return $response->json();
    }
}
