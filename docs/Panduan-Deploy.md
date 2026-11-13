# Panduan Deploy WargaDigi 21

Panduan ini untuk server Linux (contoh Ubuntu) dengan web server Caddy atau Nginx, PHP-FPM,
dan MySQL/MariaDB. Sesuaikan path `/var/www/wargadigi`, nama user, dan domain dengan server Anda.
Untuk Web Push, PWA, dan OCR, lihat juga `docs/Catatan-Deploy-Notifikasi-PWA.md`.

Ada dua skenario:
- **A. Update server yang sudah berjalan** (paling sering, data produksi dipertahankan).
- **B. Instalasi baru** (server kosong, produksi atau demo).

> PENTING: di server produksi yang sudah berisi data, JANGAN menjalankan `migrate:fresh`,
> `db:wipe`, `db:seed`, `MasterSeeder`, atau `DemoSeeder`. Seeder menimpa pengaturan
> (termasuk Halaman Utama) dan password akun `superadmin`, sedangkan `migrate:fresh` menghapus semua data.

---

## 1. Kebutuhan server

| Komponen | Versi / catatan |
|---|---|
| PHP | 8.3 atau lebih baru (Laravel 13) |
| Ekstensi PHP | `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `bcmath`, `curl`, `zip`, `gd` (dengan dukungan WebP dan JPEG), `exif`, `intl` |
| Composer | 2.x |
| Node.js | 18 atau lebih baru (hanya untuk build aset) |
| Database | MySQL 8 atau MariaDB 10.6+ (`DB_CONNECTION=mysql`) |
| Lainnya | Cron (scheduler), Supervisor atau systemd (queue worker), HTTPS (wajib untuk PWA/Web Push) |

Cek ekstensi GD mendukung WebP (dipakai untuk mengonversi foto unggahan):

```bash
php -r "var_dump(function_exists('imagewebp'));"   # harus bool(true)
```

Bila `false`, unggahan tetap berjalan tetapi gambar disimpan dalam format aslinya (tanpa konversi WebP).

Batas unggah PHP (sesuaikan di `php.ini` / pool FPM):

```ini
upload_max_filesize = 10M
post_max_size = 12M
memory_limit = 256M
```

---

## 2. Data yang harus dipertahankan antar-deploy

Folder berikut berisi berkas unggahan pengguna. Jangan dihapus atau ditimpa saat deploy, dan
ikutkan dalam backup:

| Folder | Isi |
|---|---|
| `storage/app/public/` | `berita/`, `beranda/` (banner dan logo partner Halaman Utama), `settings/` (logo, gambar login), `umkm-photos/`, `rekening/` (QRIS), `bukti_pembayaran/`, `bukti_transaksi/`, `dokumen/`, `mutasi_kk/` |
| `storage/app/private/` | `ttd/` (tanda tangan pejabat) dan berkas privat lain |
| `public/assets/images/agenda/` | flyer agenda |
| `public/assets/images/galeri/` | foto galeri dokumentasi |
| `.env` | konfigurasi dan rahasia server (tidak ada di Git) |

---

## A. Update server yang sudah berjalan

Jalankan sebagai user pemilik aplikasi (mis. `deploy` atau `www-data`) dari folder aplikasi.

### A.1 Backup dulu

```bash
cd /var/www/wargadigi
mkdir -p ~/backup
mysqldump -u <db_user> -p --single-transaction --routines <db_name> > ~/backup/wargadigi-$(date +%F-%H%M).sql
tar -czf ~/backup/uploads-$(date +%F-%H%M).tar.gz storage/app public/assets/images
git rev-parse HEAD > ~/backup/commit-sebelum-deploy.txt
```

### A.2 Mode maintenance dan ambil kode terbaru

```bash
php artisan down --retry=60
git fetch origin
git checkout integration/wargadigi      # atau branch rilis yang dipakai server
git pull --ff-only origin integration/wargadigi
```

Bila `git pull` gagal karena ada perubahan lokal di server, cek dengan `git status`. Jangan
memakai `git reset --hard` tanpa memastikan berkas yang berubah bukan data unggahan.

### A.3 Dependensi dan aset

```bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
```

### A.4 Migrasi database

```bash
php artisan migrate --force
```

Migrasi pada rilis ini aman untuk data lama:
- `add_slug_to_agenda_pengumuman`: menambah slug dan mengisinya untuk agenda/pengumuman lama.
- `create_kategori_konten_table`: membuat master kategori konten, kategori lama dipindahkan menjadi
  master dan konten lama disambungkan lewat `kategori_id`.
- `add_archive_status_to_pengumuman`: menambah status `Archive` pada pengumuman.

Tidak perlu menjalankan seeder apa pun untuk update.

### A.5 Storage link, cache, dan worker

```bash
php artisan storage:link          # aman diulang; abaikan bila "already exists"
php artisan optimize:clear
php artisan optimize              # config, route, view, event cache
php artisan queue:restart         # worker memuat kode baru
php artisan up
```

### A.6 Cek setelah deploy

- Buka halaman utama, `/berita`, `/agenda`, `/pojok-umkm`, `/layanan-mandiri`.
- Login sebagai Admin Aplikasi lalu buka **Pengaturan Halaman Utama** dan **Pengaturan Sistem**.
- Login sebagai Op Konten, cek menu **Kategori Konten** sudah berisi kategori lama.
- Cek log: `tail -n 50 storage/logs/laravel.log`.

### A.7 Rollback bila bermasalah

```bash
php artisan down
git checkout $(cat ~/backup/commit-sebelum-deploy.txt)
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build
# Database: pulihkan dari backup bila migrasi sudah berjalan
mysql -u <db_user> -p <db_name> < ~/backup/wargadigi-<tanggal>.sql
php artisan optimize:clear && php artisan optimize
php artisan queue:restart
php artisan up
```

---

## B. Instalasi baru

### B.1 Ambil kode dan siapkan `.env`

```bash
cd /var/www
git clone -b integration/wargadigi https://github.com/hmzahiqball/WargaDigi.git wargadigi
cd wargadigi
cp .env.example .env
php artisan key:generate
```

Isi `.env` minimal untuk produksi:

```dotenv
APP_NAME="WargaDigi 21"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://wargadigi21.com
APP_DEMO=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wargadigi
DB_USERNAME=<db_user>
DB_PASSWORD=<db_password>

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local
LOG_LEVEL=warning
```

Variabel WhatsApp gateway, Web Push (VAPID), mail, dan OCR diisi sesuai layanan yang dipakai
(lihat `docs/Catatan-Deploy-Notifikasi-PWA.md`). Jangan commit `.env`.

### B.2 Dependensi, aset, database

```bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build
php artisan migrate --force
```

Pilih salah satu pengisian data:

- **Produksi (data kosong, hanya master):**
  ```bash
  php artisan db:seed --class=MasterSeeder --force
  ```
  Membuat role, wilayah, master RT, kategori (UMKM, produk, transaksi, konten), pengaturan sistem,
  teks Halaman Utama, fasilitas umum, dan satu akun `superadmin` dengan password `password`.
  **Segera login dan ganti password `superadmin`.**

- **Server demo (data contoh lengkap):** set `APP_DEMO=true` lalu
  ```bash
  php artisan db:seed --force          # memanggil DemoSeeder bila APP_DEMO=true
  ```
  Membuat 14 akun demo (password semua `password`), penduduk, konten, UMKM, keuangan, dan contoh
  surat. Jangan dipakai untuk server produksi dengan data warga asli.

### B.3 Izin folder dan storage link

```bash
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache public/assets/images
sudo find storage bootstrap/cache public/assets/images -type d -exec chmod 775 {} \;
php artisan optimize
```

### B.4 Web server

Document root mengarah ke folder `public/`.

Contoh **Caddy** (`/etc/caddy/Caddyfile`, HTTPS otomatis):

```caddy
wargadigi21.com {
    root * /var/www/wargadigi/public
    encode gzip
    php_fastcgi unix//run/php/php8.3-fpm.sock
    file_server
    request_body {
        max_size 12MB
    }
}
```

Contoh **Nginx**:

```nginx
server {
    server_name wargadigi21.com;
    root /var/www/wargadigi/public;
    index index.php;
    client_max_body_size 12M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known) {
        deny all;
    }
}
```

Untuk Nginx, pasang HTTPS dengan Certbot (`sudo certbot --nginx -d wargadigi21.com`).

### B.5 Scheduler (cron)

Scheduler menjalankan pengingat iuran (`iuran:reminder`, `iuran:reminder-lainnya`) dan pembersihan
dokumen sementara (`dokumen:bersihkan-sementara`). Tambahkan ke crontab user aplikasi:

```cron
* * * * * cd /var/www/wargadigi && php artisan schedule:run >> /dev/null 2>&1
```

### B.6 Queue worker (Supervisor)

`/etc/supervisor/conf.d/wargadigi-worker.conf`:

```ini
[program:wargadigi-worker]
command=php /var/www/wargadigi/artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www/wargadigi
user=www-data
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/wargadigi/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start wargadigi-worker
```

---

## Ringkasan perintah update (A)

```bash
cd /var/www/wargadigi
mysqldump -u <db_user> -p --single-transaction <db_name> > ~/backup/wargadigi-$(date +%F-%H%M).sql
php artisan down --retry=60
git pull --ff-only origin integration/wargadigi
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear && php artisan optimize
php artisan queue:restart
php artisan up
```
