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

```

## Update versi baru (otomatis)

Cukup jalankan satu skrip dari root project. Skrip melakukan backup DB + foto agenda/galeri,
`git pull`, build, menjalankan container baru (migrasi otomatis), memulihkan foto, mengecek halaman,
dan rollback otomatis (kode + database + foto) bila gagal. Backup tersimpan di `~/wargadigi-backup`
(10 set terakhir).

```bash
cd /opt/wargadigi
sudo bash scripts/deploy-update.sh                # update biasa, data dipertahankan
sudo bash scripts/deploy-update.sh --reset-demo   # update + HAPUS SEMUA DATA, isi data demo terbaru
```

Notifikasi browser dan OCR ikut disiapkan otomatis:
- **Notifikasi browser (Web Push):** bila `WEBPUSH_PUBLIC_KEY`/`WEBPUSH_PRIVATE_KEY` di `.env.docker`
  kosong, skrip membuat kunci VAPID sekali, lalu menyalakan pengaturan `notif_push`. Kunci yang
  sudah ada tidak diganti (mengganti kunci memaksa semua browser mendaftar ulang).
- **OCR KK/KTP:** bila masih memakai kunci demo `helloworld`, skrip menanyakan API key OCR.space.
  Opsi: `--ocr-key <KEY>` (kunci sendiri), `--no-ocr` (matikan OCR, isi manual).
- `--no-push` melewati notifikasi browser. Status keduanya ditampilkan di akhir skrip.
- `.env.docker` lama dicadangkan ke `~/wargadigi-backup/env-docker-<waktu>` sebelum diubah.

Pertama kali, bila server masih memakai kode lama yang belum punya skrip ini:

```bash
cd /opt/wargadigi
git fetch origin main && git show origin/main:scripts/deploy-update.sh > /tmp/deploy-update.sh
sudo bash /tmp/deploy-update.sh
```

### Pengaturan Docker khusus server

Jangan mengubah `docker-compose.prod.yml` langsung di server (membuat `git pull` gagal). Taruh
tambahan khusus server di `docker-compose.server.yml` (tidak ikut Git); skrip otomatis memakainya.
Contoh: menyambungkan app ke network eksternal `resource`:

```yaml
services:
  app:
    networks:
      - wargadigi
      - resource

networks:
  resource:
    external: true
```

Bila menjalankan `docker compose` manual, tambahkan `-f docker-compose.server.yml` setelah
`-f docker-compose.prod.yml`.

## Update versi baru (manual)

Migrasi berjalan otomatis saat container start (`docker/scripts/entrypoint.sh`); seeder TIDAK
dijalankan otomatis. Jangan menjalankan ulang `scripts/server-setup.sh` atau `db:seed` di server yang
sudah berisi data: seeder menimpa Pengaturan Sistem/Halaman Utama dan mengembalikan password akun.

```bash
cd /opt/wargadigi
DC="docker compose --env-file .env.docker -f docker-compose.prod.yml"
mkdir -p ~/backup

# 1. Backup database dan unggahan
$DC exec -T db sh -c 'mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" --single-transaction "$MYSQL_DATABASE"' > ~/backup/wargadigi-$(date +%F-%H%M).sql
$DC exec -T app tar -czf - storage/app public/assets/images > ~/backup/uploads-$(date +%F-%H%M).tar.gz
git rev-parse HEAD > ~/backup/commit-sebelum-deploy.txt

# 2. Ambil kode, build, jalankan ulang (migrasi otomatis)
git pull --ff-only origin main
$DC build app
$DC up -d

# 3. Cek
$DC logs --tail=50 app
```

Rollback: `git checkout $(cat ~/backup/commit-sebelum-deploy.txt)`, lalu `$DC build app && $DC up -d`.
Bila migrasi sudah berjalan, pulihkan database:
`$DC exec -T db sh -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < ~/backup/wargadigi-<tanggal>.sql`.

> PERHATIAN: volume `storage_data` hanya menyimpan `storage/app`. Foto agenda dan galeri
> (`public/assets/images/agenda`, `public/assets/images/galeri`) berada di dalam container, sehingga
> hilang setiap kali container dibuat ulang (`build` + `up -d`). Backup langkah 1 menyelamatkannya;
> pulihkan dengan `$DC exec -T app tar -xzf - < ~/backup/uploads-<tanggal>.tar.gz` setelah update.

## Akun demo (setelah seeding DemoSeeder)
Login memakai username. Semua password: `password`.

| Username | Peran |
|---|---|
| `superadmin` | Admin Aplikasi |
| `adminrw21` | Admin RW |
| `pimpinan` | Ketua RW |
| `opkontenrw21` / `opkeuanganrw21` | Op Konten RW / Op Keuangan RW |
| `dkm` | DKM |
| `ketuart01rw21` | Ketua RT 01 |
| `opkontenrt01rw21` / `opkeuanganrt01rw21` | Op Konten RT 01 / Op Keuangan RT 01 |
| `opkonten_3` / `operator_rt3` | Op Konten RT 03 / Op Keuangan RT 03 |
| `budisampurno` | Ketua RT 03 + Warga (pilih peran saat login) |
| `budisantoso` | Warga RT 03 |
| `donisetiawan` | Warga nonaktif (contoh akun dinonaktifkan) |

> PERINGATAN: ini data DEMO dengan password lemah. Untuk produksi nyata, jangan jalankan DemoSeeder; pakai `MasterSeeder` lalu ganti password `superadmin`.

## Catatan penting
- `.env.docker` berisi rahasia (password DB, APP_KEY) dan sudah masuk `.gitignore`. Backup file ini secara aman.
- Ganti `APP_HOST_PORT` di `.env.docker` bila port 8080 sudah dipakai layanan lain; skrip & Caddy mengikuti nilai tersebut (jalankan ulang bagian Caddy bila mengubahnya).
- `SESSION_SECURE_COOKIE=true` aktif karena diakses via HTTPS; aplikasi juga mempercayai proxy (trustProxies) agar skema HTTPS terdeteksi benar.
- Data persisten: volume `db_data` (database) & `storage_data` (unggahan).
