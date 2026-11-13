# Panduan Deployment — WargaDigi

Panduan ini menjelaskan cara men-deploy aplikasi WargaDigi (Laravel 13 / PHP 8.3) ke server produksi. Skenario utama: VPS Linux (Ubuntu 22.04/24.04) dengan Nginx + PHP-FPM + MySQL. Di bagian akhir disertakan opsi alternatif (shared hosting cPanel dan Laragon/Windows Server).

---

## 1. Ringkasan Kebutuhan Server

| Komponen | Minimum | Catatan |
|----------|---------|---------|
| PHP | 8.3 | Ekstensi: mbstring, pdo, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, curl, gd/zip |
| Composer | 2.x | Pengelola dependensi PHP |
| Node.js | 20+ & npm | Untuk build aset (Vite). Bisa dibuild lokal lalu diunggah |
| Database | MySQL 8 / MariaDB 10.6+ | Disarankan untuk produksi (default dev = SQLite) |
| Web server | Nginx (atau Apache) | + PHP-FPM |
| Lainnya | Cron, Supervisor | Untuk scheduler & queue worker |

> Aplikasi memakai UUID sebagai primary key dan kompatibel dengan MySQL/MariaDB maupun SQLite.

---

## 2. Persiapan Server (Ubuntu)

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# PHP 8.3 + ekstensi umum Laravel
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-mysql php8.3-sqlite3 \
  nginx mysql-server unzip git

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 20 (untuk build aset; opsional bila build dilakukan lokal)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 3. Ambil Kode dan Pasang Dependensi

```bash
# Clone dari branch main (sudah berisi seluruh fitur terintegrasi)
cd /var/www
sudo git clone -b main https://github.com/hmzahiqball/WargaDigi.git wargadigi
cd wargadigi

# Dependensi PHP untuk PRODUKSI (tanpa dev, teroptimasi)
composer install --no-dev --optimize-autoloader

# Build aset frontend
npm install
npm run build
```

> Jika server tidak memiliki Node.js, jalankan `npm install && npm run build` di komputer lokal, lalu unggah folder `public/build` ke server.

---

## 4. Konfigurasi Database (MySQL)

```bash
sudo mysql
```
```sql
CREATE DATABASE wargadigi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'wargadigi'@'localhost' IDENTIFIED BY 'PASSWORD_KUAT_ANDA';
GRANT ALL PRIVILEGES ON wargadigi.* TO 'wargadigi'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 5. Konfigurasi Environment (.env)

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Sesuaikan nilai berikut untuk produksi:

```env
APP_NAME=WargaDigi
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.id

# Gunakan MySQL untuk produksi
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wargadigi
DB_USERNAME=wargadigi
DB_PASSWORD=PASSWORD_KUAT_ANDA

# Session/Cache/Queue memakai database (default aplikasi ini)
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Email (isi sesuai penyedia SMTP Anda)
MAIL_MAILER=smtp
MAIL_HOST=smtp.penyedia.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS="no-reply@domain-anda.id"

# Kanal Pesan (WhatsApp/Telegram) - isi sesuai konfigurasi layanan Messaging
# TELEGRAM_BOT_TOKEN=...
# WHATSAPP_API_URL=...
# WHATSAPP_API_TOKEN=...
```

> Penting: `APP_DEBUG=false` dan `APP_ENV=production` wajib di server produksi demi keamanan.

---

## 6. Migrasi, Seeder, dan Storage

```bash
# Buat struktur tabel
php artisan migrate --force

# (Opsional) Isi data awal peran/master. Untuk produksi, sebaiknya
# jalankan hanya seeder yang perlu, mis. peran & RT. HINDARI seeder akun demo
# di produksi karena berisi password contoh.
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=MasterRtSeeder --force

# Symlink storage (agar logo, bukti bayar, foto UMKM tampil)
php artisan storage:link
```

> Peringatan keamanan: `UserSeeder` berisi akun contoh dengan password `password`. JANGAN jalankan di produksi. Buat akun pengurus asli secara manual atau lewat proses aktivasi.

---

## 7. Hak Akses Folder

```bash
sudo chown -R www-data:www-data /var/www/wargadigi
sudo chmod -R 775 /var/www/wargadigi/storage /var/www/wargadigi/bootstrap/cache
```

---

## 8. Optimasi untuk Produksi

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> Setiap kali `.env` diubah, jalankan `php artisan config:clear` lalu `config:cache` ulang.

---

## 9. Konfigurasi Nginx

Buat file `/etc/nginx/sites-available/wargadigi`:

```nginx
server {
    listen 80;
    server_name domain-anda.id www.domain-anda.id;
    root /var/www/wargadigi/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
}
```

Aktifkan dan reload:
```bash
sudo ln -s /etc/nginx/sites-available/wargadigi /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

> Catatan: `root` HARUS menunjuk ke folder `public`, bukan root proyek. `client_max_body_size` disesuaikan agar unggahan file (bukti bayar, foto) tidak tertolak.

---

## 10. HTTPS (SSL) dengan Let\'s Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.id -d www.domain-anda.id
```
Certbot akan memperbarui sertifikat otomatis.

---

## 11. Scheduler (Cron) — Wajib untuk Tagihan Rutin

Aplikasi memiliki perintah terjadwal `tagihan:generate-rutin` (membuat iuran bulanan RT & Dana Kematian DKM). Daftarkan Laravel scheduler ke cron:

```bash
sudo crontab -e -u www-data
```
Tambahkan baris:
```
* * * * * cd /var/www/wargadigi && php artisan schedule:run >> /dev/null 2>&1
```

> Pastikan perintah dijadwalkan di dalam `routes/console.php` atau scheduler aplikasi. Bila belum, jalankan manual/terjadwal langsung: `php artisan tagihan:generate-rutin` pada awal bulan.

---

## 12. Queue Worker — untuk Notifikasi/Pesan

Karena `QUEUE_CONNECTION=database`, notifikasi (mis. pengingat tagihan) dan pesan diproses oleh worker. Gunakan Supervisor agar worker berjalan permanen.

Buat `/etc/supervisor/conf.d/wargadigi-worker.conf`:
```ini
[program:wargadigi-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/wargadigi/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/wargadigi/storage/logs/worker.log
stopwaitsecs=3600
```
Aktifkan:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start wargadigi-worker:*
```
