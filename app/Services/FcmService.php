<?php

// app/Services/FcmService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private string $projectId = 'buku-tamu-93cda';
    private string $credentialsFile = 'buku-tamu-93cda-firebase-adminsdk-fbsvc-1702ab1046.json';

    private function getAccessToken(): ?string
    {
        try {
            $credentialsPath = storage_path('app/' . $this->credentialsFile);
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                json_decode(file_get_contents($credentialsPath), true)
            );
            $token = $credentials->fetchAuthToken();
            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('FCM getAccessToken error: ' . $e->getMessage());
            return null;
        }
    }

    public function send(string $fcmToken, string $title, string $body, array $data = []): void
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) return;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'android' => [
                        'notification' => [
                            'sound'        => 'default',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                    'data' => array_map('strval', $data),
                ],
            ]);

            if (!$response->successful()) {
                Log::error('FCM send failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('FCM send error: ' . $e->getMessage());
        }
    }

    public function sendToMultiple(array $fcmTokens, string $title, string $body, array $data = []): void
    {
        foreach ($fcmTokens as $token) {
            $this->send($token, $title, $body, $data);
        }
    }
}