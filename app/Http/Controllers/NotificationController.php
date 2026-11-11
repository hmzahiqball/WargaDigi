<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Notifikasi in-app lintas peran (semua user yang login).
 */
class NotificationController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai sudah dibaca lalu arahkan ke url tujuan.
     */
    public function read(string $id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            $url = $notification->data['url'] ?? null;
            if ($url) {
                return redirect($url);
            }
        }

        return redirect()->back();
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca.
     */
    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
