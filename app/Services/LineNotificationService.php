<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LineNotificationService
{
    protected $accessToken;
    protected $groupId;

    public function __construct()
    {
        $this->accessToken = env('LINE_ACCESS_TOKEN');
        $this->groupId = env('LINE_GROUP_ID');
        $this->adminUserId = env('LINE_ADMIN_USER_ID');
    }

    public function sendMessage($message)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type'  => 'application/json',
        ])->post('https://api.line.me/v2/bot/message/push', [
            'to' => $this->groupId,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ],
            ],
        ]);
    }

    public function sendMessageWithImage($message, $imageUrl)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type'  => 'application/json',
        ])->post('https://api.line.me/v2/bot/message/push', [
            'to' => $this->groupId,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ],
                [
                    'type' => 'image',
                    'originalContentUrl' => $imageUrl,
                    'previewImageUrl' => $imageUrl,
                ],
            ],
        ]);
    }
    
}
