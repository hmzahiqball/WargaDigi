# Deploy WargaDigi di Server (Docker + Caddy)

Domain: **wargadigi21.com**
Arsitektur: Internet -> Caddy (:80/:443, TLS otomatis) -> 127.0.0.1:APP_HOST_PORT -> container app (Nginx+PHP-FPM+Queue+Scheduler) -> MySQL.

Container app hanya di-bind ke localhost, jadi tidak terekspos langsung ke internet. Caddy (yang sudah mengelola domain lain) yang menghadap publik.

## Prasyarat di server
- Docker + Docker Compose v2 terpasang
- Caddy sudah berjalan (punya /etc/caddy/Caddyfile)
- DNS `wargadigi21.com` mengarah ke IP server
- Port 80 & 443 dikuasai Caddy

## Cara Deploy (sekali jalan)
```bash
# 1. Ambil kode
cd /opt   # atau lokasi pilihan Anda
git clone -b main https://github.com/hmzahiqball/WargaDigi.git wargadigi
cd wargadigi

# 2. Jalankan skrip setup (butuh sudo untuk menulis Caddyfile & reload Caddy)
sudo bash scripts/server-setup.sh
```

Skrip akan otomatis:
1. Membuat `.env.docker` dari template + mengisi APP_KEY & password DB acak.
2. Build image & menjalankan container (app + MySQL).
3. Menunggu app siap di 127.0.0.1:APP_HOST_PORT (default 8080).
4. Menjalankan SELURUH seeder demo (termasuk UserSeeder) — akun demo berpassword `password`.
5. Menambahkan blok `wargadigi21.com` ke Caddyfile dan reload Caddy.

## Blok Caddy yang ditambahkan
```
wargadigi21.com {
    encode gzip zstd
    reverse_proxy 127.0.0.1:8080
}
```
Caddy otomatis menerbitkan sertifikat HTTPS (Let''s Encrypt) untuk domain ini.

## Setelah deploy
```bash
# Lihat log
docker compose --env-file .env.docker -f docker-compose.prod.yml logs -f app

# Buat akun admin awal (contoh via tinker)
docker compose --env-file .env.docker -f docker-compose.prod.yml exec app php artisan tinker

# Update versi baru
git pull
docker compose --env-file .env.docker -f docker-compose.prod.yml build
docker compose --env-file .env.docker -f docker-compose.prod.yml up -d
```

## Akun demo (setelah seeding)
Login memakai NIK + Nomor WhatsApp. Semua password: `password`.

| Peran | NIK | Nomor WhatsApp |
|-------|-----|----------------|
| Admin Aplikasi | 3217010101010001 | 082123456789 |
| Admin RW | 3217010101010002 | 081234567890 |
| Ketua RT | 3217010101010008 | 081398765432 |
| Warga | 3217010101010003 | 081987654321 |

> PERINGATAN: ini data DEMO dengan password lemah. Untuk produksi nyata, ganti password akun-akun ini atau jalankan ulang tanpa `UserSeeder`.

## Catatan penting
- `.env.docker` berisi rahasia (password DB, APP_KEY) dan sudah masuk `.gitignore`. Backup file ini secara aman.
- Ganti `APP_HOST_PORT` di `.env.docker` bila port 8080 sudah dipakai layanan lain; skrip & Caddy mengikuti nilai tersebut (jalankan ulang bagian Caddy bila mengubahnya).
- `SESSION_SECURE_COOKIE=true` aktif karena diakses via HTTPS; aplikasi juga mempercayai proxy (trustProxies) agar skema HTTPS terdeteksi benar.
- Data persisten: volume `db_data` (database) & `storage_data` (unggahan).
