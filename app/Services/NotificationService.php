<?php
// app/Services/NotificationService.php
namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function __construct(private FcmService $fcm) {}

    public function notify(User $user, string $title, string $body, string $type, array $data = []): void
    {
        // Simpan ke database
        Notification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'body'    => $body,
            'type'    => $type,
            'data'    => $data,
        ]);

        // Kirim FCM kalau ada token
        if ($user->fcm_token) {
            $this->fcm->send($user->fcm_token, $title, $body, $data);
        }
    }
}