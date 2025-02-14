<?php

// Get raw request body
$rawData = file_get_contents('php://input');
$jsonData = json_decode($rawData, true);

// Log the webhook request
file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - Webhook Data:\n" . print_r($jsonData, true) . "\n", FILE_APPEND);

// Extract necessary data
$replyToken = $jsonData['events'][0]['replyToken'] ?? null;
$sourceType = $jsonData['events'][0]['source']['type'] ?? null;
$groupId = $jsonData['events'][0]['source']['groupId'] ?? null;
$messageType = $jsonData['events'][0]['message']['type'] ?? null;

// Define the allowed group ID
$allowedGroupId = "C295065f7b9f89b468bd1dd40cf6e7946"; // Replace with your actual group ID

// LINE API Credentials
$channelAccessToken = "j6tQ/l00yeVPCtJ73y5ydJj+OM2y51mbO8zGjlA+QMXsQNEX6bRyo+veIaqsQe8TyJB9W2tbCdmofRIQEr2tu/QR+MS+bNT8C9VPEVUwNRwBSVTQpH1FJVcmql9KqnYLiSqR/VmURiqzzzDDNpDFMo9PbdgDzCFqoOLOYbqAITQ="; // Replace with your actual token

// Check if the event is from the specific group and is a text message
if ($replyToken && $sourceType === "group" && $groupId === $allowedGroupId && $messageType === "text") {
    // Prepare reply message
    $replyMessage = [
        "replyToken" => $replyToken,
        "messages" => [
            [
                "type" => "text",
                "text" => "Yes"
            ]
        ]
    ];

    // Send reply
    $ch = curl_init("https://api.line.me/v2/bot/message/reply");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $channelAccessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($replyMessage));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    // Log the response
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - Reply Sent: " . $result . "\n", FILE_APPEND);
}

// Respond to LINE with success status
http_response_code(200);
echo json_encode(['status' => 'success']);

?>
