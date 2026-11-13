# Catatan Deploy — Notifikasi, Web Push & PWA

Dokumen ini WAJIB dibaca saat deploy ke server. Berisi langkah yang hanya bisa
diselesaikan di server (HTTPS) untuk mengaktifkan Web Push dan PWA.

## 1. Web Push (notifikasi ke perangkat/browser)

Web Push membutuhkan:
- HTTPS (otomatis via Caddy di server). Di lokal (http://wargadigi.test) Web Push NON-AKTIF.
- Pasangan kunci VAPID yang diisi ke `.env`.

### Langkah di server
1. Generate VAPID keys:
   ```
   php artisan webpush:vapid
   ```
   > Catatan: di lingkungan Laragon lokal perintah ini GAGAL ("Unable to create the key")
   > karena dukungan OpenSSL EC curve belum lengkap. Di server Linux (OpenSSL penuh) berhasil.
   > Alternatif bila tetap gagal: `npx web-push generate-vapid-keys`.

2. Salin hasilnya ke `.env`:
   ```
   WEBPUSH_SUBJECT="${APP_URL}"
   WEBPUSH_PUBLIC_KEY=<publicKey>
   WEBPUSH_PRIVATE_KEY=<privateKey>
   ```

3. Bersihkan cache config lalu build ulang frontend (agar meta public key termuat):
   ```
   php artisan config:clear
   npm run build
   ```

### Perilaku fail-safe
- Bila `WEBPUSH_PUBLIC_KEY` kosong: notifikasi in-app (database) tetap berfungsi penuh,
  hanya pengiriman ke perangkat (push browser) yang dilewati. Tidak ada error.
- Subscription yang kedaluwarsa (HTTP 404/410) otomatis dihapus dari tabel `push_subscriptions`.

## 2. PWA (installable di smartphone)

Sudah disiapkan:
- Route `/manifest.webmanifest` (`PwaController`, dibuat dinamis dari Pengaturan Sistem: nama instansi dan ikon
  `/ikon-aplikasi/{varian}.png` dari logo yang diunggah; theme #2E7D32, display standalone).
- `public/sw.js` (service worker: install/activate, offline fallback, event push & notificationclick).
- `public/offline.html` (halaman offline).
- Ikon: `public/icons/icon-192.png`, `icon-512.png`, `icon-maskable-512.png`.
- Link manifest + meta theme-color + apple-touch-icon di layout `layouts/global.blade.php`.
- Registrasi SW di `resources/js/pwa.js` (diimpor `app.js`).

### Syarat agar bisa di-install
- Harus diakses via HTTPS (atau `localhost`). Di server dengan Caddy sudah HTTPS, jadi
  menu "Tambahkan ke Layar Utama"/Install akan muncul di Chrome/Edge Android.
- iOS Safari: install manual via Share → "Add to Home Screen"; Web Push iOS hanya jalan
  jika di-install sebagai PWA dan iOS >= 16.4.

### Uji di server
1. Buka situs via HTTPS di Chrome Android → menu titik tiga → "Install app".
2. Pastikan service worker aktif: DevTools → Application → Service Workers.
3. Izinkan notifikasi saat diminta; coba trigger event (mis. ajukan surat) untuk melihat push.

## 3. Ringkasan cakupan notifikasi in-app (sudah aktif tanpa HTTPS)

Dikirim otomatis saat transisi berikut (tabel `notifications`, tampil di lonceng topbar semua peran):
- Surat: diajukan → Ketua RT; disetujui RT → pemohon + RW; terbit → pemohon; ditolak RT/RW → pemohon.
- Perubahan data (mutasi): menunggu RT → Ketua RT; menunggu RW → Admin RW + Ketua RW; hasil → pengaju.
- Keuangan: laporan disubmit → approver (Ketua RT untuk unit RT; Ketua RW + Admin RW untuk RW/DKM);
  disetujui/ditolak → pembuat laporan.
- UMKM: baru diajukan → pemilik (Pending) + Admin RW & Ketua RW; disetujui/ditolak → pemilik.
- Konten: Pengumuman & Agenda dipublikasikan → semua warga; Berita TIDAK mengirim notif (hanya tampil
  di dashboard); konten dikembalikan (revisi) → pembuat (Op Konten).

Toggle kanal mengikuti Pengaturan Sistem (`notif_push`, `notif_whatsapp`).

## 4. OCR Baca KK/KTP (OCR.space)

Fitur Tambah Keluarga (Ketua RT) membaca KK & KTP otomatis via OCR.space.

### Konfigurasi (.env)
```
OCR_PROVIDER=ocrspace
OCR_SPACE_API_KEY=<API_KEY_ANDA>   # default 'helloworld' (demo, rate-limit ketat)
OCR_SPACE_LANGUAGE=eng
OCR_SPACE_ENGINE=2
```
- Daftar gratis di https://ocr.space/ocrapi untuk memperoleh API key sendiri; ganti dari 'helloworld' agar tidak kena limit demo.
- Untuk menonaktifkan OCR (selalu input manual): set `OCR_PROVIDER=null`.

### Catatan privasi (PENTING)
- Dengan OCR.space, file KK/KTP (data pribadi: NIK, nama, alamat) DIKIRIM ke server pihak ketiga (OCR.space) untuk diproses. Pertimbangkan kebijakan privasi/izin warga. Bila tidak diinginkan, pakai `OCR_PROVIDER=null` (manual) atau ganti ke OCR on-premise (mis. Tesseract) di masa depan.

### Perilaku & keterbatasan
- OCR bersifat best-effort: hasil tidak selalu akurat (bergantung kualitas foto/scan). Form SELALU menyediakan input manual sebagai fallback.
- Bila API key kosong/gagal/limit: sistem fail-safe → "Dokumen tidak dapat dibaca otomatis, silakan isi manual" (tidak error).
- Arsitektur provider (`App\Services\Ocr`): mudah diganti ke engine lain (Tesseract/Vision) tanpa mengubah form.
