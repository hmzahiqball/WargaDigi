<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Mengelola langganan Web Push (browser) milik user yang sedang login.
 */
class PushSubscriptionController extends Controller
{
    /**
     * Simpan/perbarui subscription dari browser.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'endpoint'          => ['required', 'string'],
            'keys'              => ['required', 'array'],
            'keys.p256dh'       => ['required', 'string'],
            'keys.auth'         => ['required', 'string'],
            'content_encoding'  => ['nullable', 'string'],
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => hash('sha256', $validated['endpoint'])],
            [
                'user_id'          => Auth::id(),
                'endpoint'         => $validated['endpoint'],
                'public_key'       => $validated['keys']['p256dh'],
                'auth_token'       => $validated['keys']['auth'],
                'content_encoding' => $validated['content_encoding'] ?? 'aesgcm',
            ]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Hapus subscription (unsubscribe) berdasarkan endpoint.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        PushSubscription::where('endpoint_hash', hash('sha256', $validated['endpoint']))
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['ok' => true]);
    }
}
