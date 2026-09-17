# Dokumen Teknis Pengembangan Aplikasi
# WargaDigi — Platform Digital Manajemen Lingkungan RW/RT

| Item | Keterangan |
|------|------------|
| Nama Aplikasi | WargaDigi |
| Versi Dokumen | 1.0 |
| Tanggal | 17 September 2026 |
| Studi Kasus | RW 21 Desa Tanimulya |
| Jenis Aplikasi | Aplikasi Web (Monolitik, Server-Side Rendered) |
| Framework | Laravel 13 (PHP 8.3) |

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Kebutuhan (Requirements)](#2-kebutuhan-requirements)
3. [Pengguna dan Hak Akses](#3-pengguna-dan-hak-akses)
4. [Fitur Aplikasi](#4-fitur-aplikasi)
5. [Arsitektur Sistem](#5-arsitektur-sistem)
6. [Basis Data](#6-basis-data)
7. [Acceptance Criteria](#7-acceptance-criteria)
8. [Panduan Instalasi dan Menjalankan](#8-panduan-instalasi-dan-menjalankan)

---

## 1. Pendahuluan

### 1.1 Latar Belakang
WargaDigi adalah platform digital untuk mendukung tata kelola lingkungan tingkat Rukun Warga (RW) dan Rukun Tetangga (RT). Aplikasi ini mengonsolidasikan proses administrasi kependudukan, persuratan, keuangan, informasi/komunikasi, UMKM warga, serta pengelolaan sistem ke dalam satu portal berbasis peran (role-based).

### 1.2 Tujuan
- Mendigitalkan layanan administratif warga (permohonan surat, iuran, informasi).
- Menyediakan alur persetujuan berjenjang RT ke RW yang transparan.
- Memberikan transparansi keuangan lingkungan kepada warga.
- Memfasilitasi promosi UMKM warga.
- Menyediakan kontrol sistem (pengaturan, hak akses, dan audit log) bagi Admin Aplikasi.

### 1.3 Ruang Lingkup
Dokumen ini mencakup kebutuhan fungsional dan non-fungsional, daftar pengguna dan hak akses, fitur, arsitektur perangkat lunak, rancangan basis data, serta kriteria penerimaan (acceptance criteria) untuk tiap modul utama.

---

## 2. Kebutuhan (Requirements)

### 2.1 Kebutuhan Fungsional

| Kode | Kebutuhan Fungsional |
|------|----------------------|
| RF-01 | Sistem menyediakan autentikasi pengguna berbasis NIK (16 digit) + Nomor WhatsApp + password. |
| RF-02 | Sistem mengarahkan pengguna ke dashboard sesuai peran setelah login berhasil. |
| RF-03 | Warga dapat mengajukan permohonan surat (mis. Surat Keterangan Domisili) secara daring. |
| RF-04 | Ketua RT dapat menyetujui/menolak permohonan surat pada tahap RT. |
| RF-05 | Admin/Pimpinan RW dapat mengesahkan permohonan surat pada tahap akhir (RW) dan menghasilkan PDF. |
| RF-06 | Operator Keuangan dapat mencatat transaksi keuangan (pemasukan/pengeluaran) dan membuat laporan keuangan. |
| RF-07 | Ketua RT dan Pimpinan RW dapat menyetujui laporan/transaksi keuangan secara berjenjang. |
| RF-08 | Sistem menampilkan laporan keuangan yang dipublikasikan kepada seluruh pengguna terautentikasi. |
| RF-09 | Operator Keuangan dapat membuat tagihan iuran (rutin/insidental) dan warga dapat membayar serta mengunggah bukti. |
| RF-10 | Sistem mengirim pengingat tagihan melalui kanal pesan (WhatsApp/Telegram). |
| RF-11 | Operator Konten dapat membuat berita, pengumuman, agenda, dan galeri dokumentasi dengan alur persetujuan RW. |
| RF-12 | Warga dapat melihat informasi (berita/agenda/pengumuman) dan melakukan RSVP kehadiran agenda. |
| RF-13 | Warga dapat mendaftarkan dan mengelola UMKM beserta produknya; RW memverifikasi UMKM. |
| RF-14 | Pengunjung publik dapat menelusuri galeri UMKM tanpa login. |
| RF-15 | Admin Aplikasi dapat mengelola Pengaturan Sistem (nama instansi, domain, logo, notifikasi, keamanan, mode pemeliharaan) dan menyimpannya secara permanen. |
| RF-16 | Admin Aplikasi dapat mengelola hak akses (role) pengguna asli dari basis data, terintegrasi RBAC. |
| RF-17 | Sistem mencatat aktivitas penting pengguna (login, perubahan data) secara otomatis ke log aktivitas. |
| RF-18 | Admin/Pimpinan RW dapat mengelola data master RT (tambah, ubah, hapus). |

### 2.2 Kebutuhan Non-Fungsional

| Kode | Kebutuhan Non-Fungsional |
|------|--------------------------|
| RNF-01 | Keamanan: kontrol akses berbasis peran (RBAC) pada setiap rute terproteksi. |
| RNF-02 | Keamanan: password disimpan ter-hash; proteksi CSRF pada setiap form. |
| RNF-03 | Auditabilitas: aktivitas krusial tercatat lengkap dengan pelaku, waktu, dan perubahan data. |
| RNF-04 | Kompatibilitas: antarmuka berbasis Bootstrap 5, responsif untuk desktop dan perangkat mobile. |
| RNF-05 | Keterpeliharaan: kode mengikuti pola MVC Laravel dan konvensi PSR-4. |
| RNF-06 | Integritas data: relasi antar-tabel memakai foreign key dengan aturan hapus yang sesuai (restrict/cascade/null). |
| RNF-07 | Portabilitas: menggunakan UUID sebagai primary key pada mayoritas entitas. |

---

## 3. Pengguna dan Hak Akses

### 3.1 Daftar Peran (Role)
Sistem mendefinisikan 10 peran yang tersimpan pada kolom `role` tabel `users` sekaligus disinkronkan ke lapisan RBAC (Spatie Permission).

| No | Peran | Deskripsi Singkat |
|----|-------|-------------------|
| 1 | Admin Aplikasi | Pengelola sistem: pengaturan, hak akses, log aktivitas, arsip & manajemen data. |
| 2 | Admin RW | Administrasi tingkat RW: master RT, persetujuan dokumen/keuangan, UMKM, pusat informasi. |
| 3 | Pimpinan RW | Setara Admin RW untuk pengesahan/persetujuan tingkat RW. |
| 4 | Op Konten RW | Operator konten tingkat RW: berita, agenda, pengumuman, galeri. |
| 5 | Op Keuangan RW | Operator keuangan tingkat RW: transaksi, laporan, tagihan/iuran, rekening. |
| 6 | Ketua RT | Persetujuan dokumen & keuangan tahap RT, informasi keluarga. |
| 7 | Op Konten RT | Operator konten tingkat RT. |
| 8 | Op Keuangan RT | Operator keuangan tingkat RT. |
| 9 | DKM | Pengurus keuangan unit DKM (Dewan Kemakmuran Masjid). |
| 10 | Warga | Pengguna akhir: layanan surat, iuran, informasi, agenda, keluarga, UMKM. |

Selain itu terdapat **Pengunjung Publik** (tanpa autentikasi) yang dapat mengakses landing page, informasi publik, transparansi, dan galeri UMKM.

### 3.2 Matriks Hak Akses (Ringkas)

| Modul / Fitur | Warga | Ketua RT | Admin/Pimpinan RW | Op Konten | Op Keuangan/DKM | Admin Aplikasi |
|---------------|:-----:|:--------:|:-----------------:|:---------:|:---------------:|:--------------:|
| Permohonan Surat (ajukan) | ✔ | | | | | |
| Persetujuan Surat (RT) | | ✔ | | | | |
| Pengesahan Surat (RW) | | | ✔ | | | |
| Master Data RT | | | ✔ | | | |
| Transaksi & Laporan Keuangan | | | | | ✔ | |
| Persetujuan Keuangan | | ✔ | ✔ | | | |
| Tagihan/Iuran (kelola) | | | | | ✔ | |
| Bayar Iuran | ✔ | | | | | |
| Berita/Agenda/Pengumuman (kelola) | | | | ✔ | | |
| Persetujuan Konten | | | ✔ | | | |
| UMKM (daftar & kelola) | ✔ | | | | | |
| Verifikasi UMKM | | | ✔ | | | |
| Pengaturan Sistem | | | | | | ✔ |
| Manajemen Hak Akses | | | | | | ✔ |
| Log Aktivitas | | | | | | ✔ |

*Catatan: kontrol akses ditegakkan melalui middleware `role` pada grup rute dan disinkronkan dengan Spatie Permission.*

---

## 4. Fitur Aplikasi

### 4.1 Autentikasi & Otorisasi
- **Login berbasis NIK + WhatsApp**: pengguna memasukkan NIK (16 digit), Nomor WhatsApp, dan password. Sistem memverifikasi NIK terdaftar, mencocokkan Nomor WhatsApp dengan data keluarga (dengan normalisasi awalan 62 ke 0), lalu memvalidasi password.
- **Aktivasi akun**: pencocokan NIK dan Nomor WhatsApp terhadap data master keluarga.
- **Otorisasi**: middleware `role` membatasi akses tiap grup rute; lapisan RBAC menggunakan Spatie Permission.

### 4.2 Administrasi Kependudukan
- Data master RT, Keluarga (KK), dan Penduduk.
- Relasi: satu Keluarga memiliki banyak Penduduk; setiap Keluarga terhubung ke satu RT.

### 4.3 Layanan Persuratan
- Warga mengajukan permohonan surat dengan lampiran (KTP/KK).
- Alur status: Diajukan → Disetujui RT / Ditolak RT → Selesai (disahkan RW) / Ditolak RW.
- Tanda tangan digital dan stempel didukung; surat resmi dihasilkan sebagai PDF (dompdf).

### 4.4 Keuangan
- Pencatatan transaksi (pemasukan/pengeluaran) per unit (RT/RW/DKM).
- Penyusunan laporan keuangan periodik dengan status (Draft/Submitted/Approved/Rejected).
- Alur persetujuan keuangan berjenjang.
- Laporan keuangan publik untuk seluruh pengguna terautentikasi (unduh PDF).

### 4.5 Tagihan & Portal Pembayaran
- Pembuatan tagihan iuran rutin/insidental beserta tenggat.
- Pembayaran oleh warga (Transfer/QRIS/Cash) dengan unggah bukti; status Unpaid/Pending/Paid.
- Verifikasi pembayaran oleh operator; pengingat via kanal pesan (WhatsApp/Telegram).
- Pengaturan rekening bendahara per unit.

### 4.6 Informasi & Komunikasi
- Berita, Pengumuman, Agenda, dan Galeri Dokumentasi.
- Alur konten: Draft → Review → Revisi → Publish (persetujuan oleh RW).
- RSVP kehadiran agenda oleh warga.

### 4.7 UMKM Warga
- Pendaftaran usaha dan produk oleh warga.
- Verifikasi usaha oleh RW (Pending/Approved/Rejected).
- Galeri UMKM publik dan penghitung akses/klik WhatsApp.

### 4.8 Modul Pengelolaan Sistem (Admin Aplikasi)
- **Pengaturan Sistem**: nama instansi, domain, deskripsi, logo, notifikasi (WhatsApp/Email/Push), keamanan (2FA, session timeout), dan mode pemeliharaan; tersimpan permanen (key-value + cache).
- **Manajemen Hak Akses**: menampilkan pengguna asli dari basis data, mengubah role per pengguna, tersinkron dengan RBAC.
- **Log Aktivitas**: pencatatan otomatis login/logout, percobaan login gagal, dan perubahan data model penting; dilengkapi pencarian, filter kategori, dan paginasi.

### 4.9 Master Data RT (Admin/Pimpinan RW)
- CRUD data RT (kode RT, nama RT) dengan pencarian dan paginasi.
- Perlindungan integritas: RT yang masih memiliki data keluarga tidak dapat dihapus.

---

## 5. Arsitektur Sistem

### 5.1 Gaya Arsitektur
Aplikasi menggunakan **arsitektur monolitik berpola MVC (Model-View-Controller)** khas Laravel dengan rendering sisi server (Blade). Otorisasi berbasis peran diterapkan melalui middleware dan RBAC.

### 5.2 Teknologi (Tech Stack)

| Lapisan | Teknologi |
|---------|-----------|
| Bahasa | PHP 8.3 |
| Framework | Laravel 13 |
| Templating | Blade |
| Frontend | Bootstrap 5, Bootstrap Icons, Chart.js |
| Build Tool | Vite (+ SASS) |
| Basis Data | SQLite (default; kompatibel MySQL/MariaDB) |
| PDF | barryvdh/laravel-dompdf |
| RBAC | spatie/laravel-permission ^8.3 |
| Audit Log | spatie/laravel-activitylog ^4.12 |
| Pesan | Layanan Messaging (driver WhatsApp dan Telegram) |

### 5.3 Struktur Direktori Utama

```
app/
  Console/Commands/        Perintah terjadwal (mis. GenerateTagihanRutin)
  Http/Controllers/        Controller per domain dan per peran (Rw/, Warga/, OpKonten/)
  Http/Middleware/         RoleMiddleware (kontrol akses berbasis peran)
  Models/                  Model Eloquent (UUID, relasi, trait log/role)
  Notifications/           Notifikasi (TagihanReminder)
  Providers/               AppServiceProvider (event log, pagination Bootstrap)
  Services/Messaging/      Abstraksi pengiriman pesan (driver WA/Telegram)
config/                    Konfigurasi (permission, activitylog, sidebarMenu, dll.)
database/migrations/       Skema basis data
database/seeders/          Data awal (roles, users, master RT, dsb.)
resources/views/           Blade views (admin/, rw/, rt/, warga/, auth/, dll.)
routes/web.php             Definisi rute dan grup middleware
```

### 5.4 Komponen Lintas Modul
- **RoleMiddleware** (alias `role`): memeriksa peran pengguna terhadap peran yang diizinkan rute; mengarahkan ulang bila tidak berwenang.
- **config/sidebarMenu.php**: sumber tunggal menu navigasi per peran; juga dipakai untuk menentukan tujuan pengalihan setelah login.
- **AppServiceProvider**: mendaftarkan listener event autentikasi untuk log aktivitas dan mengatur paginasi memakai tema Bootstrap 5.
- **Sinkronisasi Role**: model `User` menyinkronkan kolom `role` (sumber kebenaran untuk middleware/menu) dengan peran Spatie secara otomatis pada setiap penyimpanan.

### 5.5 Alur Autentikasi (Ringkas)
1. Pengguna mengirim NIK + Nomor WhatsApp + password.
2. Sistem memvalidasi format (NIK 16 digit) dan keberadaan pengguna.
3. Sistem mencocokkan Nomor WhatsApp dengan data keluarga terkait NIK.
4. Kredensial diverifikasi; sesi diregenerasi; `last_login` diperbarui.
5. Pengguna diarahkan ke dashboard sesuai peran (berdasarkan `sidebarMenu`).
6. Event login dicatat ke log aktivitas.

---

## 6. Basis Data

Mayoritas entitas menggunakan **UUID** sebagai primary key. Berikut daftar tabel utama beserta kolom pentingnya.

### 6.1 Autentikasi dan RBAC

**users**
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | uuid (PK) | Identitas pengguna |
| nik | string, unique | Nomor Induk Kependudukan (16 digit) |
| username | string | Nama pengguna/tampilan |
| nik_verified_at | timestamp, nullable | Waktu verifikasi NIK |
| role | enum | Salah satu dari 10 peran |
| password | string | Password ter-hash |
| status_akun | enum(Unverified, Active) | Status akun |
| last_login | timestamp, nullable | Waktu login terakhir |
| rt_id | uuid, FK->master_rt, nullable | RT terkait (nullOnDelete) |

**roles, permissions, model_has_roles, model_has_permissions, role_has_permissions** (Spatie Permission)
- Tabel RBAC standar Spatie. Kolom `model_id` (morph key) disesuaikan menjadi **UUID** agar cocok dengan `users.id`.

### 6.2 Kependudukan

**master_rt**: `id (uuid, PK)`, `kode_rt (unique)`, `nama_rt`, timestamps.

**keluarga**: `id (uuid, PK)`, `no_kk (unique)`, `nik_kepala_keluarga`, `alamat`, `rt_id (FK->master_rt, restrictOnDelete)`, `no_wa`, `status_aktivasi (Unverified/Active)`, timestamps.

**penduduk**: `id (uuid, PK)`, `keluarga_id (FK->keluarga, cascade)`, `nik (unique)`, `nama_lengkap`, `jenis_kelamin (L/P)`, `tempat_lahir`, `tanggal_lahir`, `agama`, `pekerjaan`, `status_hubungan_keluarga`, `status_perkawinan`, `file_kk`, `file_ktp`, timestamps.

### 6.3 Persuratan

**pengajuan_surat**: `id (PK)`, `penduduk_id (FK->penduduk, cascade)`, `tipe_surat`, `keterangan_tambahan`, `file_ktp`, `file_kk`, `status (Diajukan/Ditolak RT/Disetujui RT/Ditolak RW/Selesai)`, `catatan_rt`, `catatan_rw`, `file_surat_resmi`, `tanggal_disetujui_rt`, `tanggal_selesai`, timestamps. (Ditambah kolom tanda tangan/stempel pada migrasi lanjutan.)

### 6.4 Informasi dan Komunikasi

**berita**: `id (uuid, PK)`, `judul_berita`, `slug (unique)`, `kategori`, `isi_berita`, `featured_image`, `status (Draft/Review/Revisi/Publish/Archive)`, `catatan_revisi`, `operator_id (FK->users)`, `approval_id (FK->users)`, `tanggal_publish`, timestamps.

**pengumuman**: `id (uuid, PK)`, `judul_pengumuman`, `isi_pengumuman`, `is_priority`, `status (Draft/Review/Revisi/Publish)`, `catatan_revisi`, `operator_id`, `approval_id`, `tanggal_publish`, timestamps.

**agenda**: `id (uuid, PK)`, `judul_agenda`, `kategori`, `tanggal_mulai`, `tanggal_selesai`, `lokasi`, `link_gmaps`, `detail_pengumuman`, `banner_flyer`, `is_rsvp_enabled`, `foto_dokumentasi (json)`, `status`, `operator_id`, `approval_id`, timestamps. (Ditambah lat/long dan jumlah peserta pada migrasi lanjutan.)

**agenda_kehadiran**: `id (uuid, PK)`, `agenda_id (FK->agenda, cascade)`, `user_id (FK->users, cascade)`, `status_kehadiran (Hadir/Tidak Hadir/Ragu-ragu)`, unique(agenda_id, user_id), timestamps.

**galeri_dokumentasi**: dokumentasi kegiatan (dibuat pada migrasi terpisah).
### 6.5 UMKM

**kategori_umkm**: `id (uuid, PK)`, `nama_kategori`, timestamps.

**umkm_usaha**: `id (uuid, PK)`, `nik (FK->users.nik, cascade)`, `kategori_umkm_id (FK)`, `nama_usaha`, `deskripsi`, `alamat_usaha`, `no_wa`, `foto_usaha`, `status_verifikasi (Pending/Approved/Rejected)`, `catatan_verifikasi`, `klikWA`, `is_active`, timestamps.

**kategori_produk**: `id (uuid, PK)`, `umkm_usaha_id (FK)`, `nama_kategori`, timestamps.

**umkm_produk**: `id (uuid, PK)`, `umkm_usaha_id (FK)`, `kategori_produk_id (FK)`, `nama_produk`, `deskripsi`, `harga`, `status_stok (tersedia/menipis/habis)`, `foto_produk`, `status_produk (Aktif/Tidak Aktif)`, `jumlah_akses`, timestamps.

### 6.6 Keuangan

**transaksi_keuangan**: `id (uuid, PK)`, `kode_transaksi (unique)`, `tipe (pemasukan/pengeluaran)`, `kategori`, `judul`, `deskripsi`, `jumlah`, `tanggal`, `bukti_file`, `status (Draft/Pending/Verified/Approved/Rejected)`, `catatan_verifikasi`, `unit_sumber (RT/RW/DKM)`, `rt_id (FK)`, `dicatat_oleh (FK->users)`, `diverifikasi_oleh (FK->users)`, `tanggal_verifikasi`, timestamps.

**laporan_keuangan**: `id (uuid, PK)`, `judul`, `periode_bulan`, `periode_tahun`, `unit (RT/RW/DKM)`, `rt_id (FK)`, `total_pemasukan`, `total_pengeluaran`, `saldo_awal`, `saldo_akhir`, `status (Draft/Submitted/Approved/Rejected)`, `catatan`, `file_pdf`, `dibuat_oleh (FK)`, `disetujui_oleh (FK)`, `tanggal_disetujui`, timestamps. (Ditambah `is_published` pada migrasi lanjutan.)

### 6.7 Tagihan dan Pembayaran

**rekening_bendahara**: `id (uuid, PK)`, `unit (RT/RW/DKM)`, `rt_id (FK)`, `bank`, `no_rek`, `nama_rek`, `qris_file`, `created_by (FK->users)`, timestamps.

**tagihan**: `id (uuid, PK)`, `judul`, `deskripsi`, `jenis (rutin/insidental)`, `nominal`, `periode_bulan`, `periode_tahun`, `unit (RT/RW/DKM)`, `rt_id (FK)`, `pembuat_id (FK->users)`, `status (Active/Closed)`, `tenggat`, timestamps.

**pembayaran_tagihan**: `id (uuid, PK)`, `tagihan_id (FK, cascade)`, `keluarga_id (FK, cascade)`, `warga_id (FK->users)`, `status (Unpaid/Pending/Paid)`, `metode (Transfer/QRIS/Cash)`, `bukti_file`, `catatan`, `approved_by (FK->users)`, `approved_at`, timestamps.

### 6.8 Pengelolaan Sistem

**system_settings**: `id (uuid, PK)`, `key (unique)`, `value (text)`, `type (string/boolean/integer/json)`, `group (general/notification/security/maintenance)`, timestamps. Diakses lewat model dengan mekanisme default + cache.

**activity_log** (Spatie): `id`, `log_name`, `description`, `subject_type`, `subject_id (uuid)`, `causer_type`, `causer_id (uuid)`, `properties (json)`, `event`, `batch_uuid`, timestamps. Kolom morph disesuaikan ke UUID.

**notifications**: tabel notifikasi standar Laravel untuk pengingat (mis. tagihan).

### 6.9 Ringkasan Relasi Utama
- `master_rt` 1—N `keluarga` 1—N `penduduk`.
- `users.nik` <-> `penduduk.nik` (satu-satu logis); `users.rt_id` -> `master_rt`.
- `keluarga` 1—N `pembayaran_tagihan` N—1 `tagihan`.
- `users.nik` 1—N `umkm_usaha` 1—N `umkm_produk`.
- `penduduk` 1—N `pengajuan_surat`.
- `agenda` 1—N `agenda_kehadiran` N—1 `users`.

---

## 7. Acceptance Criteria

Kriteria penerimaan berikut menjadi acuan pengujian penerimaan (UAT) tiap fitur. Format: **Diberikan (Given) - Ketika (When) - Maka (Then)**.

### AC-01 Login (NIK 16 digit + WhatsApp + Password)
- **AC-01.1** Diberikan pengguna di halaman login; Ketika mengisi NIK bukan 16 digit; Maka sistem menolak dan menampilkan pesan "NIK harus terdiri dari 16 digit angka".
- **AC-01.2** Diberikan NIK yang tidak terdaftar; Ketika submit; Maka sistem menampilkan "NIK tidak terdaftar dalam sistem".
- **AC-01.3** Diberikan NIK terdaftar namun Nomor WhatsApp tidak cocok dengan data keluarga; Ketika submit; Maka sistem menampilkan "Nomor WhatsApp tidak cocok dengan data NIK tersebut".
- **AC-01.4** Diberikan NIK, WhatsApp cocok, namun password salah; Ketika submit; Maka sistem menampilkan "Password yang Anda masukkan salah".
- **AC-01.5** Diberikan kredensial valid (NIK + WhatsApp cocok + password benar); Ketika submit; Maka pengguna login, sesi diregenerasi, dan diarahkan ke dashboard sesuai peran.
- **AC-01.6** Nomor WhatsApp berawalan 62 dianggap setara dengan awalan 0 (normalisasi).
- **AC-01.7** Setiap login berhasil/gagal tercatat pada log aktivitas.

### AC-02 Pengaturan Sistem
- **AC-02.1** Diberikan Admin Aplikasi membuka halaman pengaturan; Maka form menampilkan nilai tersimpan (atau nilai default bila belum pernah disimpan).
- **AC-02.2** Ketika Admin menyimpan perubahan (nama instansi, domain, deskripsi, timeout, toggle notifikasi/keamanan/pemeliharaan); Maka nilai tersimpan permanen dan tetap ada setelah halaman dimuat ulang.
- **AC-02.3** Ketika Admin mengunggah logo valid (PNG/JPG/SVG/WEBP, maksimal 2 MB); Maka logo tersimpan dan pratinjau ditampilkan; logo lama dihapus.
- **AC-02.4** Ketika file logo melebihi batas atau format salah; Maka sistem menolak dengan pesan validasi.
- **AC-02.5** Perubahan pengaturan tercatat pada log aktivitas.

### AC-03 Manajemen Hak Akses (RBAC)
- **AC-03.1** Diberikan Admin Aplikasi membuka halaman; Maka daftar pengguna yang tampil berasal dari data asli tabel `users`, bukan data statis.
- **AC-03.2** Ketika Admin mencari nama/NIK; Maka daftar terfilter sesuai kata kunci.
- **AC-03.3** Ketika Admin mengubah role seorang pengguna dan menyimpan; Maka kolom `role` diperbarui dan peran Spatie (RBAC) tersinkron otomatis.
- **AC-03.4** Role yang dikirim harus salah satu dari 10 peran valid; nilai lain ditolak.

### AC-04 Log Aktivitas
- **AC-04.1** Diberikan aksi login, perubahan pengaturan, atau perubahan data RT; Maka aktivitas tercatat otomatis beserta pelaku dan waktu.
- **AC-04.2** Halaman log menampilkan data nyata dengan kolom waktu, pengguna & peran, jenis aktivitas, deskripsi, dan status.
- **AC-04.3** Ketika Admin memakai pencarian atau filter kategori; Maka daftar log terfilter di sisi server.
- **AC-04.4** Log ditampilkan dengan paginasi bergaya Bootstrap 5 (tanpa cacat tampilan).
- **AC-04.5** Bila belum ada aktivitas; Maka tampil status kosong yang informatif.

### AC-05 Master Data RT
- **AC-05.1** Diberikan Admin/Pimpinan RW; Ketika menambah RT dengan kode unik; Maka RT tersimpan dan muncul di daftar.
- **AC-05.2** Ketika menambah RT dengan kode yang sudah ada; Maka sistem menolak dengan pesan "Kode RT tersebut sudah terdaftar".
- **AC-05.3** Ketika mengubah data RT; Maka perubahan tersimpan dan tervalidasi (kode tetap unik).
- **AC-05.4** Ketika menghapus RT yang masih memiliki data keluarga; Maka sistem menolak dan menampilkan alasan.
- **AC-05.5** Ketika menghapus RT tanpa data keluarga; Maka RT terhapus.
- **AC-05.6** Daftar RT mendukung pencarian, menampilkan jumlah keluarga per RT, dan paginasi.

### AC-06 Layanan Persuratan
- **AC-06.1** Warga dapat mengajukan surat dengan lampiran; status awal "Diajukan".
- **AC-06.2** Ketua RT dapat menyetujui (status "Disetujui RT") atau menolak (status "Ditolak RT") dengan catatan.
- **AC-06.3** RW dapat mengesahkan hingga "Selesai" dan menghasilkan PDF surat resmi, atau menolak ("Ditolak RW").
- **AC-06.4** Warga dapat mengunduh PDF surat yang telah disahkan.

### AC-07 Keuangan dan Transparansi
- **AC-07.1** Operator Keuangan dapat mencatat transaksi pemasukan/pengeluaran per unit.
- **AC-07.2** Laporan keuangan dapat disusun, diajukan, dan disetujui berjenjang.
- **AC-07.3** Laporan yang dipublikasikan dapat dilihat dan diunduh (PDF) oleh seluruh pengguna terautentikasi.

### AC-08 Tagihan dan Pembayaran
- **AC-08.1** Operator dapat membuat tagihan (rutin/insidental) dengan nominal, periode, dan tenggat.
- **AC-08.2** Warga dapat membayar dan mengunggah bukti; status menjadi "Pending".
- **AC-08.3** Operator dapat memverifikasi pembayaran menjadi "Paid" atau menolaknya.
- **AC-08.4** Sistem dapat mengirim pengingat tagihan melalui kanal pesan.

### AC-09 Informasi dan Komunikasi
- **AC-09.1** Operator Konten dapat membuat berita/pengumuman/agenda berstatus "Draft" lalu diajukan "Review".
- **AC-09.2** RW dapat menyetujui menjadi "Publish" atau meminta "Revisi".
- **AC-09.3** Warga dapat melihat konten "Publish" dan melakukan RSVP agenda (Hadir/Tidak Hadir/Ragu-ragu), satu tanggapan per agenda.

### AC-10 UMKM
- **AC-10.1** Warga dapat mendaftarkan usaha dan produk.
- **AC-10.2** RW dapat memverifikasi usaha (Approved/Rejected).
- **AC-10.3** Galeri UMKM publik dapat diakses tanpa login; penghitung akses/klik WhatsApp bertambah saat diakses.

### AC-11 Otorisasi (Lintas Modul)
- **AC-11.1** Diberikan pengguna tanpa peran yang sesuai; Ketika mengakses rute terproteksi; Maka sistem menolak dan mengarahkan ke halaman yang diizinkan dengan pesan tidak berwenang.
- **AC-11.2** Pengguna belum login yang mengakses rute terproteksi diarahkan ke halaman login.

---

## 8. Panduan Instalasi dan Menjalankan

### 8.1 Prasyarat
- PHP 8.3
- Composer 2.x
- Node.js 20+ dan npm
- Basis data: SQLite (default) atau MySQL/MariaDB

### 8.2 Langkah Instalasi
```bash
# 1. Pasang dependensi PHP
composer install

# 2. Siapkan environment
cp .env.example .env
php artisan key:generate

# 3. Siapkan basis data (SQLite default)
#    Pastikan file database/database.sqlite tersedia
php artisan migrate --force

# 4. Isi data awal (roles, users, master RT, dsb.)
php artisan db:seed --force

# 5. Buat symlink storage (untuk file unggahan/logo)
php artisan storage:link

# 6. Pasang dependensi frontend dan build aset
npm install
npm run build

# 7. Jalankan server pengembangan
php artisan serve
```

### 8.3 Catatan Konfigurasi
- **RBAC**: `spatie/laravel-permission` (config `config/permission.php`). Peran dibuat oleh `RoleSeeder` dan disinkronkan otomatis dari kolom `role` pengguna.
- **Audit Log**: `spatie/laravel-activitylog` (config `config/activitylog.php`). Tabel morph memakai UUID.
- **Menu Navigasi**: `config/sidebarMenu.php` mengatur item menu per peran.
- **Kanal Pesan**: konfigurasi driver WhatsApp/Telegram melalui variabel environment terkait layanan Messaging.

### 8.4 Akun Uji (Seeder)
Seluruh akun contoh menggunakan password `password`. Login memerlukan NIK + Nomor WhatsApp yang cocok pada data keluarga. Contoh: Admin Aplikasi (superadmin) NIK `3217010101010001` dengan Nomor WhatsApp keluarga `082123456789`.

---

## Lampiran: Daftar Modul Controller

| Area | Controller |
|------|------------|
| Publik / Landing | HomeController, InformasiController, TransparansiController, BeritaController, LayananController, UmkmController |
| Autentikasi | AuthController, RegisterController |
| Admin Aplikasi | AdminController |
| RW | RwController, Rw/MasterRtController, Rw/PusatInformasiController |
| RT | RtController |
| Operator Konten | OpKontenController, OpKonten/(Berita, Agenda, Pengumuman, Galeri)Controller |
| Operator Keuangan | OpKeuanganController, TagihanController, PersetujuanKeuanganController |
| Warga | Warga/(Dashboard, Berita, Agenda, Keluarga, Surat, Tagihan, GaleriUmkm, KelolaUmkm)Controller |
| Keuangan Publik | LaporanKeuanganPublicController |

---

*Dokumen ini disusun berdasarkan kondisi kode aktual pada repositori WargaDigi (branch integration/wargadigi).*
