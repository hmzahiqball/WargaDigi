// Registrasi Service Worker (PWA) + langganan Web Push.
// Fail-safe: bila browser tidak mendukung atau izin ditolak, tidak mengganggu aplikasi.

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    const output = new Uint8Array(raw.length);
    for (let i = 0; i < raw.length; i++) output[i] = raw.charCodeAt(i);
    return output;
}

function getMeta(name) {
    const el = document.querySelector(`meta[name="${name}"]`);
    return el ? el.getAttribute('content') : '';
}

function csrf() {
    return getMeta('csrf-token');
}

async function kirimSubscription(sub) {
    const json = sub.toJSON();
    await fetch('/push/subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
        body: JSON.stringify({
            endpoint: sub.endpoint,
            keys: { p256dh: json.keys?.p256dh, auth: json.keys?.auth },
            content_encoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
        }),
    }).catch(() => {});
}

async function aktifkanPush(registration) {
    const publicKey = getMeta('webpush-public-key');
    if (!publicKey) return; // VAPID belum dikonfigurasi (mis. di lokal) → lewati.
    if (!('PushManager' in window) || !('Notification' in window)) return;

    // Jangan paksa prompt; hanya lanjut bila sudah/akan diizinkan.
    let permission = Notification.permission;
    if (permission === 'default') {
        permission = await Notification.requestPermission();
    }
    if (permission !== 'granted') return;

    try {
        let sub = await registration.pushManager.getSubscription();
        if (!sub) {
            sub = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey),
            });
        }
        await kirimSubscription(sub);
    } catch (e) {
        // Abaikan kegagalan subscribe (mis. HTTP non-secure di lokal).
    }
}

export function initPwa() {
    if (!('serviceWorker' in navigator)) return;

    window.addEventListener('load', async () => {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

            // Aktifkan push hanya untuk user yang login (ada meta csrf + body admin).
            const loggedIn = document.body.classList.contains('admin-body');
            if (loggedIn) {
                // Tunda sedikit agar tidak mengganggu load awal.
                setTimeout(() => aktifkanPush(registration), 1500);
            }
        } catch (e) {
            // Registrasi SW gagal (mis. bukan HTTPS/localhost) → aplikasi tetap jalan.
        }
    });
}
