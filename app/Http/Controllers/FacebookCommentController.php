<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FacebookCommentController extends Controller
{
    public function fetchComments()
    {
        $accessToken = 'EAAJMSbXjy6MBO38CHzxIJ02s7X3WiGbmQiS3Uj9o0vliqxSBnXODRJ5KdZBt2jggmi4gEbxLS9QGTxqijkwARc3rRN2TG1ALEdixCVxINChBUalCagnDPLDR4sCSbmpxJCnOAolbXjizDw96CxElS6vOKZA49lUjFjZATsIZC9UtKQSQkJsZBji4iks2bXhFZCUGxZBq7RyvuuS';
        $postId = '100111532754891_110653548367356';
        $myFacebookId = '100111532754891';

        $response = Http::get("https://graph.facebook.com/v22.0/{$postId}/comments", [
            'access_token' => $accessToken,
            'fields' => 'from{name,picture},message,created_time,id',
            'limit' => 12,
            'order' => 'reverse_chronological'
        ]);

        $responseData = $response->json();

        // Reformat response data
        $formattedComments = [];
        if (!empty($responseData['data'])) {
            foreach ($responseData['data'] as $comment) {
                if (isset($comment['from']['id']) && $comment['from']['id'] === $myFacebookId) {
                    continue; 
                }
                $formattedComments[] = [
                    'id' => $comment['id'],
                    'message' => $comment['message'],
                    'created_at' => $comment['created_time'],
                    'formatted_time' => Carbon::parse($comment['created_time'])->format('F j, Y, h:i A'),
                    'user_name' => 'Facebook User',
                    'profile_image' => asset('images/facebook-logo.png'),
                ];
            }
        }

        // Return JSON response (no pagination)
        return response()->json([
            'comments' => $formattedComments
        ]);
    }
}


// namespace App\Http\Controllers;

// use Illuminate\Support\Facades\Http;
// use Illuminate\Http\Request;

// class FacebookCommentController extends Controller
// {
//     public function fetchComments()
//     {
//         $accessToken = 'EAAJMSbXjy6MBOZCephZB7IMXeM59AZAX3N5tgGCgjZArj8YEfPll5mmAhVgAJRTx5Du4AINIJLnzpGZBLbsApgG5qLqjr7hHkhPPEZBl9dLZAaVUMuHde8XYlRizJ3oefGQYAKXUQJvZCD4NCYmQ0AkWHvrrKvD2Lh2rfl2BZA63ZAjCru2QuAHdXD1qoo5dBCFQrXXnGASlXQ6J1Mlxfxep1rzZAsYwjqOaszSHbx5';
//         $postId = '100111532754891_110653548367356';

//         $response = Http::get("https://graph.facebook.com/v22.0/{$postId}/comments", [
//             'access_token' => $accessToken,
//             'fields' => 'from{name,picture},message,created_time',
//             'limit' => 10,
//             'order' => 'reverse_chronological'
//         ]);

//         // Decode JSON response
//         return response()->json($response->json()['data'] ?? []);
//     }
// }
