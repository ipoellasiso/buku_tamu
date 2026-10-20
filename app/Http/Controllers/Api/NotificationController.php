<?php

// app/Http/Controllers/Api/NotificationController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifs = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->get();
        return response()->json($notifs);
    }

    public function markRead(Request $request, Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Dibaca']);
    }

    public function markAllRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['message' => 'Semua dibaca']);
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();
        return response()->json(['count' => $count]);
    }
}