# WargaDigi 21

**Digitalisasi Gotong Royong RW 21 Tanimulya**

Platform digital untuk membangun lingkungan yang lebih baik melalui teknologi. WargaDigi 21 adalah sistem informasi berbasis web untuk RW 21 Desa Tanimulya, Kec. Ngamprah, Kabupaten Bandung Barat.

## 🚀 Tech Stack

- **Backend**: Laravel 13 (PHP 8.3+)
- **Frontend**: Blade + Bootstrap 5 + Vite
- **Database**: PostgreSQL
- **Charts**: Chart.js
- **Icons**: Bootstrap Icons

## 📋 Features

- **Landing Page** — Informasi utama, berita terkini, kalender interaktif, dan UMKM showcase
- **Pusat Informasi Data** — Dashboard statistik dan demografi warga RW 21
- **Transparansi Keuangan** — Laporan keuangan kas RW secara transparan
- **Arsip Berita** — Kumpulan informasi, pengumuman, dan dokumentasi kegiatan
- **Pojok UMKM** — Marketplace produk dan jasa warga lokal
- **Layanan Mandiri** — Portal pengajuan surat pengantar secara online
- **Sistem Autentikasi** — Mockup alur pendaftaran dan verifikasi OTP (Frontend)

## 🔧 Installation

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+
- PostgreSQL

### Setup

```bash
# Clone repository
git clone https://github.com/your-username/wargadigi.git
cd wargadigi

# Install PHP dependencies
composer install

# Install frontend dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=wargadigi
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Run migrations (when available)
php artisan migrate

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

### Development

```bash
# Run Vite dev server (for hot reload)
npm run dev

# Run Laravel dev server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## ⚠️ Current Development Status

Untuk kemudahan pengembangan (UI prototyping), project saat ini berada di state berikut:
1. **Database & Migrations**: Tabel database belum sepenuhnya diimplementasikan.
2. **Session & Cache**: Di `.env`, konfigurasi `SESSION_DRIVER`, `CACHE_STORE`, dan `QUEUE_CONNECTION` diatur ke `file`/`sync` (bukan `database`). Kembalikan nilai-nilai ini jika migrasi tabel sudah selesai dibuat.
3. **Data**: Data statistik, berita, UMKM, dan layanan masih menggunakan dummy data statis (*hardcoded*) di dalam layer Controllers.

## 📁 Project Structure

```
├── app/Http/Controllers/    # Page controllers
├── resources/
│   ├── views/
│   │   ├── auth/            # Halaman autentikasi (Register, OTP)
│   │   ├── layouts/         # Main layout template
│   │   ├── components/      # Reusable Blade components
│   │   └── pages/           # Page views (Home, UMKM, dll)
│   ├── sass/                # SCSS stylesheets
│   └── js/                  # JavaScript files
├── public/images/           # Static images
└── routes/web.php           # Route definitions
```

## 📄 License

© 2026 WargaDigi 21 Desa Tanimulya. All rights reserved.
