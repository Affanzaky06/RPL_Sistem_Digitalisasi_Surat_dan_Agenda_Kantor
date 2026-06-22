<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getUnread()
    {
        $user = Auth::user();
        $unread = $user->unreadNotifications;

        return response()->json([
            'count' => $unread->count(),
            'notifications' => $unread->take(10)->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->data['title'] ?? 'Notifikasi',
                    'body' => $notif->data['body'] ?? '',
                    'url' => $notif->data['url'] ?? '#',
                    'created_at' => $notif->created_at->diffForHumans()
                ];
            })
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return back();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah dibaca.');
    }
}
