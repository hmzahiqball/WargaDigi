# Dokumen User Acceptance Testing (UAT)
# WargaDigi — Platform Digital Manajemen Lingkungan RW/RT

| Item | Keterangan |
|------|------------|
| Nama Aplikasi | WargaDigi |
| Versi Dokumen | 1.0 |
| Tanggal | 17 September 2026 |
| Acuan | Dokumen Teknis Pengembangan Aplikasi v1.0 |
| Lingkungan Uji | http://127.0.0.1:8000 (lokal) |
| Basis Data | SQLite (data seeder) |

---

## Daftar Isi
1. [Tujuan dan Ruang Lingkup](#1-tujuan-dan-ruang-lingkup)
2. [Prasyarat Pengujian](#2-prasyarat-pengujian)
3. [Data Uji (Test Data)](#3-data-uji-test-data)
4. [Ketentuan Status dan Pengukuran](#4-ketentuan-status-dan-pengukuran)
5. [Skenario Uji (Test Cases)](#5-skenario-uji-test-cases)
6. [Ringkasan Hasil UAT](#6-ringkasan-hasil-uat)
7. [Log Defect](#7-log-defect)
8. [Lembar Persetujuan (Sign-off)](#8-lembar-persetujuan-sign-off)

---

## 1. Tujuan dan Ruang Lingkup

### 1.1 Tujuan
Memastikan bahwa aplikasi WargaDigi memenuhi kebutuhan fungsional dan acceptance criteria sebagaimana didefinisikan pada Dokumen Teknis Pengembangan, dari sudut pandang pengguna akhir.

### 1.2 Ruang Lingkup
Pengujian mencakup 11 kelompok fungsi: Autentikasi, Pengaturan Sistem, Manajemen Hak Akses, Log Aktivitas, Master Data RT, Persuratan, Keuangan & Transparansi, Tagihan & Pembayaran, Informasi & Komunikasi, UMKM, serta Otorisasi lintas modul.

### 1.3 Di Luar Ruang Lingkup
Uji performa/beban, uji keamanan penetrasi, dan uji kompatibilitas lintas peramban mendalam tidak termasuk dalam UAT ini.

---

## 2. Prasyarat Pengujian
- Aplikasi telah ter-deploy dan dapat diakses di lingkungan uji.
- Migrasi dan seeder telah dijalankan (`php artisan migrate --force && php artisan db:seed --force`).
- Symlink storage aktif (`php artisan storage:link`) agar unggahan/logo tampil.
- Penguji memiliki akun sesuai peran yang diuji.

## 3. Data Uji (Test Data)

Seluruh akun contoh menggunakan password: `password`. Login memerlukan NIK + Nomor WhatsApp yang cocok dengan data keluarga.

| Peran | NIK | Nomor WhatsApp | Password |
|-------|-----|----------------|----------|
| Admin Aplikasi | 3217010101010001 | 082123456789 | password |
| Admin RW | 3217010101010002 | 081234567890 | password |
| Pimpinan RW | 3217010101010004 | 085712345678 | password |
| Ketua RT | 3217010101010008 | 081398765432 | password |
| Op Konten RW | 3217010101010005 | 087812345678 | password |
| Op Keuangan RW | 3217010101010006 | 089612345678 | password |
| Warga | 3217010101010003 | 081987654321 | password |

Data uji tambahan:
- **NIK tidak terdaftar**: 9999999999999999
- **Nomor WhatsApp salah**: 081111111111
- **Kode RT baru (valid)**: 07 · **Kode RT duplikat**: 01

## 4. Ketentuan Status dan Pengukuran

- **Status** setiap test case: **Pass** (hasil aktual = hasil diharapkan), **Fail** (tidak sesuai), atau **Blocked** (tidak dapat diuji karena prasyarat gagal).
- **Kriteria Lulus Modul**: seluruh test case prioritas Tinggi berstatus Pass.
- **Kriteria Lulus UAT Keseluruhan**: minimal **100% test case prioritas Tinggi** dan **>= 95% seluruh test case** berstatus Pass, tanpa defect kategori Kritis/Mayor yang terbuka.
- **Terukur**: setiap hasil yang diharapkan dinyatakan dalam kondisi objektif (kode status HTTP, pesan spesifik, perubahan data yang dapat diverifikasi, atau nilai terhitung), sehingga penilaian Pass/Fail tidak ambigu.

---

## 5. Skenario Uji (Test Cases)

Format kolom: **ID · Prioritas · Prasyarat · Langkah · Data Uji · Hasil Diharapkan (Terukur) · Hasil Aktual · Status**. Kolom Hasil Aktual & Status diisi saat pelaksanaan.

### 5.1 Modul Autentikasi (mengacu AC-01)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-AUTH-01 | Tinggi | Buka `/login`, isi NIK < 16 digit, isi WA & password, klik Masuk | NIK=`123`, WA=`082123456789`, pw=`password` | Tetap di `/login`; muncul pesan tepat "NIK harus terdiri dari 16 digit angka"; tidak terautentikasi | | |
| UAT-AUTH-02 | Tinggi | Login dengan NIK tidak terdaftar | NIK=`9999999999999999`, WA=`082123456789`, pw=`password` | Muncul pesan "NIK tidak terdaftar dalam sistem"; gagal login | | |
| UAT-AUTH-03 | Tinggi | Login NIK terdaftar tetapi WA tidak cocok | NIK=`3217010101010001`, WA=`081111111111`, pw=`password` | Muncul pesan "Nomor WhatsApp tidak cocok dengan data NIK tersebut"; gagal login | | |
| UAT-AUTH-04 | Tinggi | Login NIK+WA cocok, password salah | NIK=`3217010101010001`, WA=`082123456789`, pw=`salah` | Muncul pesan "Password yang Anda masukkan salah"; gagal login | | |
| UAT-AUTH-05 | Tinggi | Login kredensial valid (Admin Aplikasi) | NIK=`3217010101010001`, WA=`082123456789`, pw=`password` | HTTP 302 diarahkan ke `/admin/dashboard`; sesi aktif | | |
| UAT-AUTH-06 | Sedang | Login dengan WA berawalan 62 | NIK=`3217010101010001`, WA=`6282123456789`, pw=`password` | Login berhasil (normalisasi 62 -> 0 dianggap cocok) | | |
| UAT-AUTH-07 | Tinggi | Login valid sebagai Warga | NIK=`3217010101010003`, WA=`081987654321`, pw=`password` | Diarahkan ke dashboard Warga (`/warga/...`) | | |
| UAT-AUTH-08 | Sedang | Setelah login berhasil, buka Log Aktivitas sebagai Admin | akun Admin Aplikasi | Terdapat entri log kategori login dengan pelaku = pengguna terkait dan waktu login | | |
| UAT-AUTH-09 | Sedang | Klik Logout | sesi login aktif | Sesi berakhir; diarahkan ke `/login`; entri logout tercatat | | |
### 5.2 Modul Pengaturan Sistem (mengacu AC-02)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-SET-01 | Tinggi | Login Admin Aplikasi, buka `/admin/pengaturan-sistem` | akun Admin Aplikasi | Halaman tampil HTTP 200; field terisi nilai tersimpan/ default | | |
| UAT-SET-02 | Tinggi | Ubah Nama Instance & Deskripsi, klik Simpan; muat ulang halaman | Nama=`RW 21 Tanimulya Uji`, Deskripsi bebas | Muncul flash "Pengaturan sistem berhasil disimpan"; setelah reload, nilai baru tetap tampil | | |
| UAT-SET-03 | Tinggi | Ubah Session Timeout ke 60 menit, simpan, reload | timeout=`60` | Setelah reload, opsi `1 jam` (60) terpilih | | |
| UAT-SET-04 | Sedang | Aktifkan toggle Notifikasi WhatsApp lalu simpan, reload | toggle ON | Setelah reload, toggle tetap dalam kondisi aktif | | |
| UAT-SET-05 | Sedang | Unggah logo valid, simpan | file PNG < 2MB | Logo tersimpan; pratinjau logo tampil di halaman | | |
| UAT-SET-06 | Sedang | Unggah file bukan gambar / > 2MB | file .txt atau > 2MB | Sistem menolak; muncul pesan validasi; logo lama tidak berubah | | |
| UAT-SET-07 | Sedang | Setelah menyimpan pengaturan, cek Log Aktivitas | - | Terdapat entri kategori pengaturan dengan pelaku Admin | | |

### 5.3 Modul Manajemen Hak Akses / RBAC (mengacu AC-03)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-RBAC-01 | Tinggi | Buka `/admin/manajemen-hak-akses` | akun Admin Aplikasi | HTTP 200; tabel menampilkan pengguna asli dari DB (>= 11 baris data seeder), bukan 3 data statis | | |
| UAT-RBAC-02 | Sedang | Cari pengguna via kotak pencarian | kata kunci `adminrw21` | Daftar terfilter hanya menampilkan pengguna yang cocok | | |
| UAT-RBAC-03 | Tinggi | Ubah role seorang Warga menjadi `Op Konten RW`, klik simpan | user Warga | Flash sukses; nilai role pada baris berubah menjadi `Op Konten RW` | | |
| UAT-RBAC-04 | Tinggi | Verifikasi sinkronisasi RBAC setelah UAT-RBAC-03 | user yang diubah | Peran Spatie pengguna = `Op Konten RW` (login pengguna itu diarahkan sesuai peran baru) | | |
| UAT-RBAC-05 | Sedang | Kembalikan role pengguna ke `Warga` | user yang sama | Role dan peran RBAC kembali `Warga` | | |

### 5.4 Modul Log Aktivitas (mengacu AC-04)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-LOG-01 | Tinggi | Buka `/admin/log-aktivitas` | akun Admin Aplikasi | HTTP 200; tabel berisi aktivitas nyata (waktu, pengguna & peran, jenis, deskripsi, status) | | |
| UAT-LOG-02 | Sedang | Gunakan filter Kategori = Login/Logout, klik Filter | kategori `login` | Hanya baris kategori login/logout yang tampil | | |
| UAT-LOG-03 | Sedang | Gunakan pencarian deskripsi/nama | kata kunci bebas | Daftar terfilter di sisi server sesuai kata kunci | | |
| UAT-LOG-04 | Tinggi | Amati komponen paginasi | data > 15 entri | Paginasi tampil bergaya Bootstrap 5 (tanpa ikon panah raksasa/cacat tampilan) | | |
| UAT-LOG-05 | Rendah | Filter kategori yang belum ada datanya | kategori tanpa data | Muncul status kosong "Belum ada aktivitas yang tercatat" | | |
### 5.5 Modul Master Data RT (mengacu AC-05)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-RT-01 | Tinggi | Login Admin RW, buka `/rw/rt` | akun Admin RW | HTTP 200; daftar RT tampil (data seeder RT 01-06) dengan kolom jumlah keluarga | | |
| UAT-RT-02 | Tinggi | Klik Tambah RT, isi kode & nama unik, simpan | kode=`07`, nama=`07` | Flash "Data RT 07 berhasil ditambahkan"; RT 07 muncul di daftar | | |
| UAT-RT-03 | Tinggi | Tambah RT dengan kode duplikat | kode=`01`, nama=`01` | Sistem menolak; pesan "Kode RT tersebut sudah terdaftar"; data tidak bertambah | | |
| UAT-RT-04 | Sedang | Edit nama RT 07 lalu simpan | nama=`07 Baru` | Flash sukses; nama RT 07 berubah menjadi `07 Baru` | | |
| UAT-RT-05 | Tinggi | Hapus RT yang masih punya data keluarga | RT 01 | Sistem menolak; muncul pesan RT tidak dapat dihapus karena masih memiliki data keluarga; tombol Hapus nonaktif | | |
| UAT-RT-06 | Tinggi | Hapus RT tanpa data keluarga | RT 07 | Flash "Data RT 07 berhasil dihapus"; RT 07 hilang dari daftar | | |
| UAT-RT-07 | Sedang | Gunakan pencarian RT | kata kunci `02` | Daftar terfilter menampilkan RT yang cocok | | |
| UAT-RT-08 | Rendah | Verifikasi menu sidebar RW "Manajemen RT" | akun Admin RW | Menu mengarah ke `/rw/rt` dan menyorot saat aktif | | |

### 5.6 Modul Layanan Persuratan (mengacu AC-06)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-SRT-01 | Tinggi | Login Warga, ajukan surat + lampiran KTP/KK, submit | akun Warga | Pengajuan tersimpan dengan status "Diajukan" | | |
| UAT-SRT-02 | Tinggi | Login Ketua RT, setujui pengajuan dengan catatan | akun Ketua RT | Status berubah menjadi "Disetujui RT"; catatan tersimpan | | |
| UAT-SRT-03 | Sedang | Login Ketua RT, tolak pengajuan lain dengan alasan | akun Ketua RT | Status "Ditolak RT"; alasan tersimpan dan terlihat pemohon | | |
| UAT-SRT-04 | Tinggi | Login Admin/Pimpinan RW, sahkan pengajuan "Disetujui RT" | akun RW | Status menjadi "Selesai"; PDF surat resmi dihasilkan | | |
| UAT-SRT-05 | Sedang | Warga mengunduh PDF surat selesai | akun Warga | Berkas PDF terunduh dan dapat dibuka | | |

### 5.7 Modul Keuangan & Transparansi (mengacu AC-07)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-KEU-01 | Tinggi | Login Op Keuangan, catat transaksi pemasukan | nominal & kategori bebas | Transaksi tersimpan; muncul di daftar transaksi unit terkait | | |
| UAT-KEU-02 | Sedang | Buat laporan keuangan periodik lalu ajukan | periode berjalan | Laporan tersimpan; status "Submitted" | | |
| UAT-KEU-03 | Tinggi | Login RW/RT, setujui laporan | akun penyetuju | Status laporan "Approved"; pencatat penyetuju tersimpan | | |
| UAT-KEU-04 | Tinggi | Publikasikan laporan, cek sebagai pengguna lain | akun Warga | Laporan yang dipublikasikan tampil di `/laporan-keuangan` dan dapat diunduh PDF | | |

### 5.8 Modul Tagihan & Pembayaran (mengacu AC-08)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-TAG-01 | Tinggi | Login Op Keuangan, buat tagihan iuran rutin | nominal, periode, tenggat | Tagihan berstatus "Active" tersimpan; muncul untuk warga terkait | | |
| UAT-TAG-02 | Tinggi | Login Warga, bayar tagihan + unggah bukti | metode Transfer/QRIS | Status pembayaran menjadi "Pending" | | |
| UAT-TAG-03 | Tinggi | Op Keuangan verifikasi pembayaran | pembayaran Pending | Status menjadi "Paid"; penyetuju & waktu tercatat | | |
| UAT-TAG-04 | Sedang | Op Keuangan tolak pembayaran tidak valid | pembayaran Pending | Status kembali ke "Unpaid"/ditolak dengan catatan | | |
| UAT-TAG-05 | Sedang | Kirim pengingat tagihan | tagihan aktif | Pengingat terkirim via kanal pesan (WA/Telegram) tanpa error | | |
### 5.9 Modul Informasi & Komunikasi (mengacu AC-09)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-INF-01 | Tinggi | Login Op Konten, buat berita status Draft lalu ajukan Review | akun Op Konten | Berita tersimpan; status berpindah Draft -> Review | | |
| UAT-INF-02 | Tinggi | Login RW, setujui berita menjadi Publish | akun RW | Status berita menjadi "Publish"; tanggal publish terisi | | |
| UAT-INF-03 | Sedang | Login RW, minta Revisi pada konten | akun RW | Status "Revisi"; catatan revisi tersimpan | | |
| UAT-INF-04 | Sedang | Login Warga, lihat konten Publish | akun Warga | Konten berstatus Publish tampil; konten Draft/Review tidak tampil | | |
| UAT-INF-05 | Sedang | Warga RSVP agenda (Hadir) | agenda RSVP aktif | Kehadiran tercatat; RSVP kedua menimpa (satu tanggapan per agenda per user) | | |

### 5.10 Modul UMKM (mengacu AC-10)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-UMKM-01 | Tinggi | Login Warga, daftarkan usaha + produk | data usaha bebas | Usaha tersimpan status "Pending"; produk terkait tersimpan | | |
| UAT-UMKM-02 | Tinggi | Login RW, verifikasi usaha (Approved) | usaha Pending | Status usaha menjadi "Approved" | | |
| UAT-UMKM-03 | Sedang | Akses Galeri UMKM tanpa login | `/galeri` | Halaman tampil publik; usaha Approved terlihat | | |
| UAT-UMKM-04 | Rendah | Klik tombol WhatsApp pada produk | produk aktif | Penghitung klik WA / akses bertambah | | |

### 5.11 Otorisasi Lintas Modul (mengacu AC-11)

| ID | Prio | Langkah Uji | Data Uji | Hasil Diharapkan (Terukur) | Hasil Aktual | Status |
|----|------|-------------|----------|----------------------------|--------------|--------|
| UAT-AUTZ-01 | Tinggi | Login Warga, akses rute admin `/admin/pengaturan-sistem` | akun Warga | Ditolak; diarahkan ke halaman berwenang dengan pesan tidak memiliki hak akses | | |
| UAT-AUTZ-02 | Tinggi | Tanpa login, akses `/admin/log-aktivitas` | tanpa sesi | Diarahkan ke `/login` | | |
| UAT-AUTZ-03 | Sedang | Login Op Konten, akses rute Op Keuangan | akun Op Konten | Ditolak / diarahkan; tidak dapat mengakses fitur keuangan | | |
| UAT-AUTZ-04 | Sedang | Login Ketua RT, akses `/rw/rt` (Master RT) | akun Ketua RT | Ditolak; tidak berwenang mengelola master RT | | |

---

## 6. Ringkasan Hasil UAT

Diisi setelah pelaksanaan pengujian.

| Modul | Jml Test Case | Pass | Fail | Blocked | % Pass |
|-------|:-------------:|:----:|:----:|:-------:|:------:|
| 5.1 Autentikasi | 9 | | | | |
| 5.2 Pengaturan Sistem | 7 | | | | |
| 5.3 Manajemen Hak Akses | 5 | | | | |
| 5.4 Log Aktivitas | 5 | | | | |
| 5.5 Master Data RT | 8 | | | | |
| 5.6 Layanan Persuratan | 5 | | | | |
| 5.7 Keuangan & Transparansi | 4 | | | | |
| 5.8 Tagihan & Pembayaran | 5 | | | | |
| 5.9 Informasi & Komunikasi | 5 | | | | |
| 5.10 UMKM | 4 | | | | |
| 5.11 Otorisasi Lintas Modul | 4 | | | | |
| **TOTAL** | **61** | | | | |

**Keputusan UAT:** ☐ Diterima ☐ Diterima dengan Catatan ☐ Ditolak

## 7. Log Defect

| ID Defect | Ref Test Case | Deskripsi Masalah | Severity (Kritis/Mayor/Minor) | Status (Open/Fixed/Closed) | Catatan |
|-----------|---------------|-------------------|-------------------------------|----------------------------|---------|
| | | | | | |

## 8. Lembar Persetujuan (Sign-off)

| Peran | Nama | Tanda Tangan | Tanggal |
|-------|------|--------------|---------|
| Penguji (Tester) | | | |
| Perwakilan Pengguna (RW) | | | |
| Pengembang | | | |
| Project Owner | | | |

---

*Dokumen UAT ini diturunkan dari Dokumen Teknis Pengembangan Aplikasi WargaDigi v1.0 dan acceptance criteria AC-01 s.d. AC-11.*
