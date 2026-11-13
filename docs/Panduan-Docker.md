# Menjalankan WargaDigi dengan Docker

Setup ini menjalankan aplikasi dalam satu container app (Nginx + PHP-FPM 8.3 + Queue Worker + Scheduler) dan satu container database (MySQL 8).

## Prasyarat
- Docker Desktop / Docker Engine + Docker Compose v2

## Cara Cepat
```bash
# Build image aplikasi (build aset Vite + composer dilakukan di dalam image)
docker compose build

# Jalankan semua service (app + db)
docker compose up -d

# Lihat log
docker compose logs -f app
```

Aplikasi dapat diakses di: http://localhost:8080
(MySQL diekspos di host port 3307 untuk keperluan debugging.)

## Yang Terjadi Otomatis saat Start
Entrypoint container melakukan:
1. Menyalin .env dari .env.example bila belum ada.
2. Generate APP_KEY bila belum ada.
3. Menunggu MySQL siap.
4. Menjalankan `php artisan migrate --force`.
5. Membuat symlink storage.
6. Meng-cache config/route/view saat APP_ENV=production.

Queue worker dan scheduler otomatis berjalan via Supervisor di dalam container.

## Seeder (Opsional)
Data awal TIDAK di-seed otomatis. Jalankan manual bila perlu:
```bash
# Peran & master RT (aman untuk produksi)
docker compose exec app php artisan db:seed --class=RoleSeeder --force
docker compose exec app php artisan db:seed --class=MasterRtSeeder --force

# Data demo lengkap (HANYA untuk uji coba; berisi akun password contoh)
docker compose exec app php artisan db:seed --force
```

## Perintah Berguna
```bash
# Masuk shell container
docker compose exec app sh

# Generate tagihan rutin manual
docker compose exec app php artisan tagihan:generate-rutin

# Hentikan
docker compose down

# Hentikan + hapus volume (data DB & storage ikut terhapus)
docker compose down -v
```

## Konfigurasi
- Ubah kredensial DB & APP_URL pada `docker-compose.yml` (bagian environment) sebelum dipakai serius.
- Untuk produksi: ganti semua password default (secret_change_me, root_change_me), set APP_URL ke domain asli, dan taruh di belakang reverse proxy/HTTPS.
- Isi variabel email (MAIL_*) dan kanal pesan (WhatsApp/Telegram) sesuai layanan Anda.

## Persistensi Data
- Database: volume `db_data`.
- File unggahan (logo, bukti bayar, foto UMKM): volume `storage_data` pada `storage/app`.
