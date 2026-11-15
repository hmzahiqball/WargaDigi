# Manual Book WargaDigi 21: Admin Aplikasi

Buku panduan ini untuk **Admin Aplikasi**, yaitu pengelola sistem WargaDigi 21 (https://wargadigi21.com). Isinya cara mengatur kepengurusan, kategori transaksi, identitas dan tampilan aplikasi, halaman utama, akun dan peran pengguna, serta membaca log aktivitas. Semua contoh memakai akun demo `superadmin`.

<!-- AI-INSTRUKSI: Ubah file ini menjadi dokumen Word bergambar mengikuti konvensi di README.md (docs/manual). Blok "SIMULASI" jadi kotak biru muda, "SCREENSHOT" diganti gambar bernama sama dengan keterangan "Gambar <KODE>: <judul>", "Hasilnya" kotak hijau muda, "Perhatian" kotak kuning, "Tips" kotak abu-abu. Semua gambar desktop 1366x768 (peran ini tidak memakai mobile). Jangan cetak komentar HTML. Pengambilan gambar mengikuti urutan bab karena simulasi mengubah data demo. -->

## Daftar Isi

1. [Kenalan Dulu](#1-kenalan-dulu)
2. [Mulai Menggunakan](#2-mulai-menggunakan)
3. [Dashboard](#3-dashboard)
4. [Kepengurusan](#4-kepengurusan)
5. [Kategori Transaksi](#5-kategori-transaksi)
6. [Pojok UMKM](#6-pojok-umkm)
7. [Pengaturan Sistem](#7-pengaturan-sistem)
8. [Pengaturan Halaman Utama](#8-pengaturan-halaman-utama)
9. [Manajemen Hak Akses](#9-manajemen-hak-akses)
10. [Log Aktivitas](#10-log-aktivitas)
11. [Cerita Lengkap (Lintas Peran)](#11-cerita-lengkap-lintas-peran)
12. [Masalah yang Sering Terjadi (FAQ)](#12-masalah-yang-sering-terjadi-faq)
13. [Lampiran: Daftar Gambar](#13-lampiran-daftar-gambar)

---

## 1. Kenalan Dulu

### 1.1 Apa tugas Admin Aplikasi?

Admin Aplikasi adalah pengelola sistem. Peran ini **tidak mengurus urusan warga sehari-hari** seperti surat, iuran, atau berita. Tugasnya menjaga agar aplikasi siap dipakai oleh semua peran lain.

Yang bisa Anda lakukan:

- Melihat gambaran jumlah warga dan akun pada **Dashboard**.
- Menetapkan siapa **Ketua, Sekretaris, dan Bendahara** pada tingkat RW, RT, dan DKM lewat **Kepengurusan**.
- Mengelola **Kategori Transaksi** umum yang dipakai semua unit keuangan.
- Melihat **Pojok UMKM** seperti yang dilihat warga.
- Mengatur identitas aplikasi, logo, gambar, dan aturan persetujuan konten lewat **Pengaturan Sistem**.
- Mengatur banner, footer, dan daftar partner di halaman depan lewat **Pengaturan Halaman Utama**.
- Mengatur akun dan peran pengguna lewat **Manajemen Hak Akses**.
- Memeriksa jejak aktivitas lewat **Log Aktivitas**.

### 1.2 Tokoh dalam buku ini

> 👤 **Mas Fajar** *(tokoh fiktif)* memegang akun **`superadmin`** sebagai Admin Aplikasi WargaDigi 21. Namanya tidak tampil di aplikasi karena akun ini hanya memiliki username.

Tokoh lain yang muncul dalam cerita:

| Tokoh | Akun | Peran dalam cerita |
|---|---|---|
| Pak Dedi Mulyadi | `pimpinan` | Ketua RW (pejabat aktif) |
| Pak Hendra Gunawan | `opkeuanganrw21` | Bendahara RW (pejabat aktif) |
| Pak Ahmad Zaki | `ketuart01rw21` | Ketua RT 01 (pejabat aktif) |
| Pak Budi Sampurno | `budisampurno` | Ketua RT 03 (pejabat aktif), juga Warga |
| Bu Siti Aminah | `operator_rt3` | Bendahara RT 03 (pejabat aktif), Op Keuangan RT 03 |

### 1.3 Peta menu

Setelah masuk, menu ada di samping kiri. Di bagian atas sidebar tertulis **WargaDigi** dan **Administrator**. Urutan menu Admin Aplikasi:

| Menu | Alamat | Fungsi singkat | Bab |
|---|---|---|---|
| **Dashboard** | /admin/dashboard | Gambaran jumlah warga, akun, dan aktivitas terbaru | 3 |
| **Kepengurusan** | /admin/pejabat | Menetapkan pejabat per jabatan dan unit | 4 |
| **Kategori Transaksi** | /admin/kategori-transaksi | Kategori umum untuk transaksi keuangan | 5 |
| **Pojok UMKM** | /galeri | Melihat etalase usaha warga | 6 |
| **Pengaturan Sistem** | /admin/pengaturan-sistem | Identitas, logo, gambar, persetujuan konten | 7 |
| **Pengaturan Halaman Utama** | /admin/halaman-utama | Banner, footer, partner halaman depan | 8 |
| **Manajemen Hak Akses** | /admin/manajemen-hak-akses | Akun dan peran pengguna | 9 |
| **Log Aktivitas** | /admin/log-aktivitas | Jejak aktivitas pengguna | 10 |

Di bagian bawah sidebar ada tombol **Keluar** (warna merah) untuk mengakhiri sesi.

> 📸 **SCREENSHOT ADM-01: Peta menu Admin Aplikasi**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/dashboard
> - **Kondisi:** baru masuk, sidebar terbuka penuh
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada sidebar kiri yang memuat delapan menu, dan tombol **Keluar** di bagian bawah. Beri nomor 1 sampai 8 sesuai tabel di atas
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-01-peta-menu.png`

> ⚠️ **Perhatian**
> - Menu lain yang mungkin Anda dengar, seperti Arsip Data Warga dan Manajemen Data, belum dibuka untuk dipakai sehingga tidak ada di buku ini.

---

## 2. Mulai Menggunakan

### 2.1 Masuk

Cara masuk sama seperti peran lain. Langkah lengkapnya ada di **00-Umum-Publik-dan-Login.md**. Ringkasnya:

1. Buka https://wargadigi21.com lalu buka halaman masuk.
2. Isi username `superadmin` dan kata sandi `password` (akun demo).
3. Tekan tombol masuk. Anda langsung diarahkan ke **Dashboard** (/admin/dashboard).

Akun `superadmin` hanya punya satu peran, jadi Anda tidak diminta memilih peran.

### 2.2 Keluar

Tekan **Keluar** di bagian bawah sidebar. Sesi ditutup dan Anda kembali ke halaman depan.

### 2.3 Soal kata sandi

> ⚠️ **Perhatian**
> - Kata sandi `password` hanya untuk **data demo**. Di server sungguhan, kata sandi akun `superadmin` **harus segera diganti**.
> - Setelah memeriksa aplikasi, **tidak ada menu untuk mengganti kata sandi** milik Anda sendiri, dan tidak ada tombol reset kata sandi untuk akun lain di Manajemen Hak Akses. Karena itu, minta pengelola server mengganti kata sandi `superadmin` sebelum aplikasi dipakai warga.
> - Jangan membagikan akun `superadmin` ke banyak orang. Semua perubahan di buku ini berlaku untuk seluruh RW.

---

## 3. Dashboard

Dashboard adalah halaman pertama setelah Anda masuk. Isinya ringkasan singkat kondisi aplikasi.

> 👤 **SIMULASI A-00: Pemeriksaan pagi**
>
> Setiap pagi Mas Fajar membuka aplikasi untuk memastikan semuanya normal: berapa warga terdata, berapa orang sudah masuk hari ini, dan apa saja yang baru terjadi.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Dashboard

### Langkah-langkah

**1. Buka Dashboard.**
Klik **Dashboard** di sidebar. Judul halaman: **Gambaran RW 21 -Tanimulya**, dengan keterangan *Rangkuman data warga dari seluruh RW aktif dan aplikasi.*

**2. Baca tiga kartu angka.**

| Kartu | Artinya |
|---|---|
| **AKTIF HARI INI** | Jumlah akun yang masuk hari ini, ditulis dengan pembanding total akun (contoh `3 / 14`) |
| **TOTAL KEPALA KELUARGA** | Jumlah keluarga yang terdata |
| **TOTAL PENDUDUK** | Jumlah penduduk yang terdata |

**3. Baca kotak Peringatan Terbaru.**
Menampilkan sampai **5 aktivitas terakhir** dari log aktivitas, lengkap dengan keterangan waktu seperti "5 menit yang lalu". Tanda hijau berarti aktivitas biasa, tanda kuning berarti aktivitas gagal atau penghapusan. Bila belum ada aktivitas, muncul tulisan **Belum ada aktivitas tercatat.**

**4. Lihat tabel instance.**
Di bawahnya ada tabel dengan kolom **NAMA INSTANCE**, **DOMAIN**, **STATUS**, **USER AKTIF**, dan **AKSI**. Pada data demo isinya satu baris: *RW 21 Tanimulya*, domain *wargadigi21.com*, status *Active*.

**5. Buka log lengkap.**
Tekan tombol **Lihat Semua log** atau tautan **Lihat Semua** untuk pindah ke **Log Aktivitas** (lihat Bab 10).

> 📸 **SCREENSHOT ADM-02: Dashboard Admin Aplikasi**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/dashboard
> - **Kondisi:** halaman selesai dimuat, angka sudah berhenti berhitung
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tiga kartu angka, kotak **Peringatan Terbaru**, dan tabel instance
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-02-dashboard.png`

### ✅ Hasilnya

- Anda tahu jumlah warga dan akun yang terdata.
- Anda melihat sampai lima aktivitas terbaru tanpa membuka Log Aktivitas.

### ⚠️ Perhatian

- Kolom **STATUS** menampilkan *Active*. Bila status berubah menjadi *Maintenance*, berarti mode pemeliharaan sedang menyala di pengaturan server.
- Kolom **USER AKTIF** pada tabel instance menunjukkan **jumlah seluruh akun terdaftar**, bukan hanya akun yang sedang masuk. Untuk yang masuk hari ini, lihat kartu **AKTIF HARI INI**.
- Dua tombol ikon kecil di kolom **AKSI** (roda gigi dan grafik) hanya memunculkan pemberitahuan singkat dan **belum membuka halaman lain**. Anda tidak perlu menekannya.

> 💡 **Tips:** Bila angka **AKTIF HARI INI** terlihat rendah padahal warga ramai memakai aplikasi, periksa **Log Aktivitas** dengan kategori **Login / Logout**.

> ➡️ **Cerita berlanjut:** Bila di Peringatan Terbaru ada aktivitas yang ingin Anda telusuri, lanjut ke Bab 10 (Log Aktivitas).

---

## 4. Kepengurusan

Menu **Kepengurusan** dipakai untuk menetapkan siapa yang menjabat sebagai **Ketua, Sekretaris, dan Bendahara** di RW, setiap RT, dan DKM. Datanya diambil dari data penduduk, jadi pejabat harus sudah terdata sebagai penduduk.

Nama pejabat dipakai di tempat lain:

- **Surat keterangan**: nama **Ketua RT** dan **Ketua RW** tercetak pada surat. Bila belum ditetapkan, bagian nama dicetak titik-titik.
- **Laporan keuangan**: nama **Ketua** (menyetujui) dan **Bendahara** (membuat) mengikuti pejabat yang menjabat pada **akhir bulan laporan**. Laporan RT memakai Ketua RT, laporan RW dan DKM memakai Ketua RW.
- **Resi pembayaran**: memakai nama Bendahara sesuai unit.
- **Peran akun**: peran **Ketua RT** dan **Ketua RW** pada Manajemen Hak Akses hanya bisa diberikan kepada orang yang sudah menjadi pejabat aktif (lihat Bab 9).
- Jabatan **Sekretaris** tersimpan sebagai data kepengurusan.

### 4.1 Melihat pejabat aktif

> 👤 **SIMULASI C-12: Pengaturan kepengurusan dan Halaman Utama (bagian 1 dari 2)**
>
> Awal tahun, Mas Fajar merapikan data RW 21. Pertama, ia memeriksa siapa saja pejabat yang tercatat, lalu menambahkan **Sekretaris RW** yang belum ada. Setelah itu ia memperbarui halaman depan (lanjutannya di Bab 8).
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Kepengurusan
> - **Data demo awal:** Ketua RW Dedi Mulyadi, Bendahara RW Hendra Gunawan, Ketua RT 01 Ahmad Zaki, Ketua RT 03 Budi Sampurno, Bendahara RT 03 Siti Aminah. Jabatan lain **Belum ditetapkan**.

#### Langkah-langkah

**1. Buka Kepengurusan.**
Klik **Kepengurusan** di sidebar. Judul halaman: **Kepengurusan (Pejabat)**.

**2. Baca tabel.**
Kolomnya: **Unit**, **Ketua**, **Sekretaris**, **Bendahara**, dan **Aksi**. Barisnya berurutan: **RW**, lalu setiap RT (**RT 01** dan seterusnya), terakhir **DKM**. Baris DKM hanya punya kolom Bendahara, kolom lainnya bertanda `-`.

- Sel yang sudah terisi menampilkan nama pejabat dan keterangan *sejak (tanggal)*.
- Sel yang masih kosong menampilkan label **Belum ditetapkan**.

> 📸 **SCREENSHOT ADM-03: Tabel Kepengurusan**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/pejabat
> - **Kondisi:** data demo awal, belum ada perubahan
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada baris RW dan RT 03 (nama pejabat), pada label **Belum ditetapkan** di kolom Sekretaris, dan pada tombol di kolom **Aksi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-03-tabel-kepengurusan.png`

### 4.2 Menetapkan pejabat baru

#### Langkah-langkah

**1. Pilih tombol Set pada baris yang tepat.**
Di baris **RW**, tekan tombol hijau **Set Sekretaris**. Setiap baris punya tombol Set untuk jabatan yang tersedia di unit itu (**Set Ketua**, **Set Sekretaris**, **Set Bendahara**).

**2. Isi formulir.**
Muncul jendela berjudul **Set Sekretaris RW**.

- **Pilih Penduduk** (wajib): ketik nama atau NIK, lalu pilih **Bambang Sutrisno**. Untuk jabatan tingkat RT, daftar otomatis dibatasi pada warga RT itu. Untuk RW dan DKM, semua penduduk bisa dipilih.
- **Mulai Menjabat** (wajib): terisi tanggal hari ini, ubah bila perlu.
- **Tanggal Selesai Menjabat**: boleh dikosongkan.

> 📸 **SCREENSHOT ADM-04: Jendela Set Sekretaris RW**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/pejabat (jendela terbuka)
> - **Kondisi:** penduduk "Bambang Sutrisno" sudah dipilih, tanggal mulai terisi
> - **Area:** jendela **Set Sekretaris RW** saja
> - **Sorot:** kotak merah pada kolom **Pilih Penduduk**, **Mulai Menjabat**, dan keterangan abu-abu di bagian bawah jendela
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-04-set-sekretaris-rw.png`

**3. Baca keterangan di bawah formulir.**
Tertulis bahwa menyimpan akan membuat data pejabat baru dan menjadikannya aktif, sedangkan pejabat sebelumnya dipindah ke riwayat **tanpa mengubah tanggalnya**.

**4. Tekan Simpan.**

#### ✅ Hasilnya

- Muncul pesan **"Pejabat Sekretaris RW berhasil ditetapkan. Pejabat sebelumnya (jika ada) dipindah ke riwayat."**
- Pada baris **RW**, kolom Sekretaris menampilkan **Bambang Sutrisno** dengan keterangan *sejak (tanggal)*.

> 📸 **SCREENSHOT ADM-05: Sekretaris RW sudah terisi**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/pejabat (sesaat setelah Simpan)
> - **Kondisi:** pesan sukses tampil, baris RW kolom Sekretaris terisi
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses dan pada sel Sekretaris baris RW
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-05-sekretaris-terisi.png`

#### ⚠️ Perhatian

- **Muncul pesan "Pilih penduduk terlebih dahulu."** Kolom Pilih Penduduk belum diisi. Pilih satu nama dari daftar.
- **Muncul pesan "Penduduk tidak ditemukan."** NIK yang dikirim tidak ada di data penduduk. Pilih ulang dari daftar.
- **Muncul pesan "Tanggal mulai menjabat wajib diisi."** Isi kolom Mulai Menjabat.
- **Muncul pesan "Tanggal selesai tidak boleh sebelum tanggal mulai."** Perbaiki tanggal selesai, atau kosongkan.
- **Muncul pesan "RT wajib dipilih untuk jabatan tingkat RT."** Terjadi bila jendela dibuka dari baris yang salah. Tutup jendela lalu tekan tombol Set pada baris RT yang dimaksud.
- **Nama warga yang dicari tidak ada.** Orang itu belum terdata sebagai penduduk. Minta Ketua RT menambahkannya dulu.
- Menetapkan pejabat **tidak mengubah akun atau peran** siapa pun. Bila pejabat baru ini perlu akun Ketua RT atau Ketua RW, lanjutkan ke Manajemen Hak Akses (Bab 9).

> 💡 **Tips:** Mengganti Ketua RT 03? Cukup tekan **Set Ketua** pada baris RT 03 dan pilih orang barunya. Pejabat lama otomatis masuk riwayat, dan surat atau laporan berikutnya memakai nama baru.

### 4.3 Melihat riwayat dan mengaktifkan kembali

Setiap pergantian tersimpan sebagai riwayat. Dari sini Anda bisa melihatnya, atau mengaktifkan kembali pejabat lama bila ada salah tetapkan.

#### Langkah-langkah

**1. Buka menu Riwayat.**
Pada baris yang dituju, tekan tombol **Riwayat**, lalu pilih jabatan (**Ketua**, **Sekretaris**, atau **Bendahara**).

**2. Baca jendela riwayat.**
Judulnya misalnya **Riwayat Sekretaris RW**. Kolomnya: **Nama** (dengan NIK), **Mulai**, **Selesai**, **Status**, dan **Aksi**. Status berisi **Aktif Sekarang** atau **Riwayat**. Bila belum ada data, tertulis **Belum ada riwayat.**

**3. Aktifkan kembali bila perlu.**
Pada baris berstatus **Riwayat**, tekan **Jadikan Aktif**. Hanya statusnya yang berpindah. Tanggal mulai dan selesai tidak berubah.

> 📸 **SCREENSHOT ADM-06: Jendela Riwayat pejabat**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/pejabat (jendela riwayat terbuka)
> - **Kondisi:** buka **Riwayat** lalu **Ketua** pada baris RW
> - **Area:** jendela riwayat saja
> - **Sorot:** kotak merah pada label **Aktif Sekarang** dan pada kolom tanggal Mulai dan Selesai
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-06-riwayat-pejabat.png`

#### ✅ Hasilnya

- Setelah **Jadikan Aktif**, muncul pesan **"Pejabat (jabatan) aktif diperbarui tanpa mengubah tanggal menjabat."**
- Pejabat yang dipilih tampil di tabel utama, pejabat sebelumnya menjadi **Riwayat**.

#### ⚠️ Perhatian

- **Jendela riwayat menampilkan "Riwayat gagal dimuat. Coba lagi."** Tutup jendela lalu buka lagi. Bila tetap gagal, muat ulang halaman.
- Laporan keuangan yang lama memakai pejabat yang menjabat **pada akhir bulan laporan**, jadi tanggal **Mulai** dan **Selesai** di riwayat berpengaruh pada laporan itu. Isi tanggal dengan benar.

> ➡️ **Cerita berlanjut:** Setelah kepengurusan rapi, Mas Fajar memperbarui halaman depan. Lihat **Bab 8 (C-12 bagian 2 dari 2)** di file ini.

---

## 5. Kategori Transaksi

Kategori dipakai agar pemasukan dan pengeluaran tercatat rapi dan mudah direkap. Sebagai Admin Aplikasi, Anda mengelola **kategori umum**, yaitu kategori yang otomatis tersedia untuk **semua unit** (RT, RW, DKM). Setiap Operator Keuangan menambah kategori khusus unitnya sendiri, dan itu tidak muncul di halaman ini.

### 5.1 Melihat dan mencari kategori

> 👤 **SIMULASI A-03: Menambah kategori umum baru**
>
> Pada bulan ini warga RW 21 kerja bakti membersihkan saluran air dan menyumbang dana. Mas Fajar menambahkan kategori **Dana Kerja Bakti** supaya semua bendahara bisa memakainya.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Kategori Transaksi
> - **Data demo awal:** 18 kategori umum (8 pemasukan, 8 pengeluaran, 2 keduanya)

#### Langkah-langkah

**1. Buka Kategori Transaksi.**
Klik **Kategori Transaksi** di sidebar. Di bawah judul tertulis *Kategori umum yang berlaku untuk semua unit (RT, RW, DKM).*

**2. Gunakan filter bila perlu.**
Isi **Cari Nama Kategori**, pilih **Tipe** (Semua, Pemasukan, Pengeluaran, Keduanya) atau **Status** (Semua, Aktif, Nonaktif), lalu tekan **Filter**. Tombol silang kecil di sebelahnya (**Reset**) mengembalikan semua filter.

**3. Baca tabel.**
Kolomnya **NAMA KATEGORI**, **TIPE**, **STATUS**, dan **AKSI**. Daftar menampilkan 15 kategori per halaman, dengan keterangan *Menampilkan 1-15 dari 18 kategori* dan tombol halaman di bawahnya.

> 📸 **SCREENSHOT ADM-07: Daftar Kategori Transaksi**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/kategori-transaksi
> - **Kondisi:** data demo awal, halaman 1
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Tambah Kategori**, kotak filter, label tipe (hijau, merah, abu-abu), dan ikon pensil serta tempat sampah di kolom **AKSI**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-07-daftar-kategori.png`

### 5.2 Menambah kategori

#### Langkah-langkah

**1. Tekan Tambah Kategori.**
Tombol hijau di kanan atas halaman. Muncul jendela **Tambah Kategori**.

**2. Isi formulir.**
- **Nama Kategori** (wajib, maksimal 100 huruf): ketik *Dana Kerja Bakti*.
- **Tipe**: pilih **Pemasukan**. Pilihan lainnya **Pengeluaran** dan **Keduanya**. Tipe menentukan kategori ini boleh dipakai untuk transaksi masuk, keluar, atau keduanya.
- **Aktif**: biarkan tercentang agar langsung bisa dipakai.

**3. Tekan Simpan.**

> 📸 **SCREENSHOT ADM-08: Jendela Tambah Kategori**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/kategori-transaksi (jendela terbuka)
> - **Kondisi:** nama "Dana Kerja Bakti" dan tipe Pemasukan sudah diisi
> - **Area:** jendela **Tambah Kategori** saja
> - **Sorot:** kotak merah pada **Nama Kategori**, **Tipe**, dan kotak centang **Aktif**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-08-tambah-kategori.png`

#### ✅ Hasilnya

- Muncul pesan **"Kategori transaksi berhasil ditambahkan."**
- Kategori **Dana Kerja Bakti** tampil dengan label hijau **Pemasukan** dan status **Aktif**.
- Operator Keuangan RT, RW, dan DKM bisa memakainya di halaman masing-masing.

> 📸 **SCREENSHOT ADM-09: Kategori baru tampil di daftar**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/kategori-transaksi (sesaat setelah Simpan)
> - **Kondisi:** pesan sukses tampil, kategori baru ada di daftar
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses dan baris **Dana Kerja Bakti**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-09-kategori-baru.png`

### 5.3 Mengubah, menonaktifkan, atau menghapus

Pada kolom **AKSI** ada dua tombol: pensil (ubah) dan tempat sampah (hapus).

**Mengubah:** tekan ikon pensil. Jendela **Edit Kategori** berisi **Nama Kategori**, **Tipe**, dan **Aktif**. Hilangkan centang **Aktif** bila kategori tidak ingin dipakai lagi tanpa dihapus, lalu tekan **Simpan**. Muncul pesan **"Kategori transaksi berhasil diperbarui."**

**Menghapus:** tekan ikon tempat sampah. Muncul konfirmasi **Hapus Kategori** dengan tulisan *Hapus kategori ini? Jika sudah dipakai transaksi, kategori hanya akan dinonaktifkan.* Tekan **Hapus** untuk melanjutkan.

- Kategori yang **belum pernah dipakai** terhapus: **"Kategori transaksi berhasil dihapus."**
- Kategori yang **sudah dipakai** transaksi atau tagihan hanya dinonaktifkan: **"Kategori sudah dipakai transaksi, sehingga dinonaktifkan (bukan dihapus) agar rekap tetap utuh."**

> 📸 **SCREENSHOT ADM-10: Jendela Edit Kategori**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/kategori-transaksi (jendela terbuka)
> - **Kondisi:** tekan ikon pensil pada kategori "Dana Kerja Bakti"
> - **Area:** jendela **Edit Kategori** saja
> - **Sorot:** kotak merah pada **Nama Kategori**, **Tipe**, dan kotak centang **Aktif**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-10-edit-kategori.png`

### ⚠️ Perhatian

- **Tombol Simpan tidak jalan karena Nama Kategori kosong.** Isi nama, maksimal 100 huruf.
- **Muncul pesan "Tipe kategori tidak valid."** Pilih salah satu dari Pemasukan, Pengeluaran, atau Keduanya.
- **Kategori yang dibuat Operator Keuangan tidak tampil di halaman Anda.** Itu memang bukan kategori umum. Admin Aplikasi hanya mengelola kategori umum, dan tidak bisa mengubah kategori buatan unit lain.
- **Urutan tampil.** Kategori baru dan kategori yang baru disimpan ulang bisa muncul di bagian atas daftar, karena daftar diurutkan berdasarkan nomor urut lalu nama.
- Mengubah **Tipe** kategori yang sudah banyak dipakai bisa membuat transaksi lama terlihat janggal di rekap. Lebih aman nonaktifkan lalu buat kategori baru.

> 💡 **Tips:** Takut salah hapus? Pilih **nonaktifkan** (hilangkan centang Aktif). Data lama tetap utuh dan kategori bisa dinyalakan lagi kapan saja.

> ➡️ **Cerita berlanjut:** Kategori ini dipakai Operator Keuangan saat mencatat pemasukan. Lihat **04-Op-Keuangan-RT.md**, **08-Op-Keuangan-RW.md**, dan **09-DKM.md** (pencatatan transaksi).

---

## 6. Pojok UMKM

**Pojok UMKM** adalah etalase usaha warga RW 21. Admin Aplikasi bisa membukanya untuk melihat tampilan yang dilihat warga dan pengunjung. Di sini Anda **hanya melihat**. Tombol *Daftarkan Usaha Anda* dan *Kelola UMKM Anda* khusus peran Warga, dan persetujuan usaha dilakukan oleh Admin RW.

> 👤 **SIMULASI A-05: Memeriksa etalase UMKM**
>
> Seorang warga bertanya kenapa usahanya belum tampil di etalase. Mas Fajar membuka Pojok UMKM untuk memastikan etalase berjalan, lalu mengarahkan warga ke pengurus RW.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Pojok UMKM
> - **Data demo:** usaha *Warung Nasi Ibu Ani* dan *Fashion Muslimah Aini* sudah disetujui

### Langkah-langkah

**1. Buka Pojok UMKM.**
Klik **Pojok UMKM** di sidebar. Alamat halaman: /galeri.

**2. Baca halaman.**
- **Produk Unggulan RW 21**: tiga produk paling banyak dilihat. Tautan **Lihat Semua** membuka daftar lengkapnya.
- **Produk Terbaru WargaDigi**: produk yang baru didaftarkan, dalam bentuk carousel.
- Kotak pencarian dengan isian **Cari Toko / Produk Berdasarkan Nama** dan tombol **Cari**.
- Daftar produk, delapan per halaman.

**3. Cari produk.**
Ketik nama toko atau produk lalu tekan **Cari**. Bila tidak ada hasil, tertulis **Tidak ada produk ditemukan**, dengan tombol **Reset Pencarian**.

**4. Buka detail.**
Klik kartu produk untuk melihat halaman detail produknya.

> 📸 **SCREENSHOT ADM-11: Pojok UMKM dilihat Admin Aplikasi**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /galeri
> - **Kondisi:** data demo, halaman 1
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada judul **Pojok UMKM**, bagian **Produk Unggulan RW 21**, dan kotak pencarian. Beri catatan "tidak ada tombol daftar usaha untuk Admin"
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-11-pojok-umkm.png`

### ✅ Hasilnya

- Anda melihat tampilan etalase yang sama dengan yang dilihat warga.
- Hanya usaha yang **sudah disetujui** dan **aktif** yang tampil di sini.

### ⚠️ Perhatian

- **Usaha warga tidak tampil.** Kemungkinan masih menunggu persetujuan, ditolak, atau dinonaktifkan oleh pengurus RW. Admin Aplikasi tidak bisa menyetujui dari halaman ini. Arahkan warga ke Admin RW.
- Bila produk unggulan kurang dari tiga, kartu besar di bagian atas bisa menampilkan **produk contoh** (misalnya nama dan harga contoh) sebagai tampilan cadangan. Itu bukan data warga. Tambahkan lebih banyak produk aktif agar tampilan terisi data sungguhan.
- Tautan di kaki halaman (Kebijakan Privasi, Syarat dan Ketentuan UMKM, Kontak RW) belum mengarah ke halaman tujuan.

> 💡 **Tips:** Untuk melihat apa yang dilihat pengunjung tanpa login, buka https://wargadigi21.com/galeri di jendela penyamaran (incognito).

> ➡️ **Cerita berlanjut:** Proses mendaftarkan usaha ada di **01-Warga.md** (cerita C-05 dan C-06), dan persetujuannya di **05-Admin-RW.md** (cerita C-05 dan C-06).

---

## 7. Pengaturan Sistem

Menu **Pengaturan Sistem** mengatur identitas aplikasi, logo dan gambar, serta aturan persetujuan konten. Halaman ini punya satu formulir dengan satu tombol simpan di bawah, yaitu **Simpan Semua Perubahan**. Semua kartu di halaman disimpan bersamaan.

Pada data demo: nama instansi **RW 21 Tanimulya**, domain **wargadigi21.com**, persetujuan **Berita aktif**, sedangkan **Agenda** dan **Pengumuman** tidak.

### 7.1 Identitas instansi

> 👤 **SIMULASI A-06: Memperbarui deskripsi instansi**
>
> Mas Fajar ingin deskripsi aplikasi lebih jelas untuk pengunjung baru.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Pengaturan Sistem
> - **Deskripsi baru:** *Platform digital untuk layanan dan informasi warga RW 21 Desa Tanimulya*

#### Langkah-langkah

**1. Buka Pengaturan Sistem.**
Klik **Pengaturan Sistem** di sidebar. Judul halaman **Pengaturan Sistem**, keterangan *Konfigurasi informasi instance aplikasi.*

**2. Isi kartu Informasi Instance.**
- **Nama Instance** (wajib, maksimal 100 huruf): `RW 21 Tanimulya`. Nama ini tampil pada Dashboard.
- **Domain** (maksimal 150 huruf): `wargadigi21.com`.
- **Deskripsi** (maksimal 500 huruf): ketik deskripsi baru.

**3. Tekan Simpan Semua Perubahan.**
Tombol hijau di bagian bawah halaman.

> 📸 **SCREENSHOT ADM-12: Kartu Informasi Instance**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/pengaturan-sistem
> - **Kondisi:** deskripsi baru sudah diketik, belum disimpan
> - **Area:** bagian atas halaman sampai kartu **Informasi Instance**
> - **Sorot:** kotak merah pada **Nama Instance**, **Domain**, dan **Deskripsi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-12-informasi-instance.png`

#### ✅ Hasilnya

- Muncul pesan **"Pengaturan sistem berhasil disimpan."**
- Nama dan domain baru langsung terlihat di tabel instance pada Dashboard.
- Perubahan tercatat di **Log Aktivitas** dengan keterangan *Mengubah pengaturan sistem.*

#### ⚠️ Perhatian

- **Halaman menampilkan kotak merah "Terjadi kesalahan:".** Baca daftar di bawahnya. Penyebab paling umum: Nama Instance kosong.
- Domain dan nama hanya **informasi**. Mengubahnya tidak memindahkan alamat aplikasi. Alamat sebenarnya diatur di server.

### 7.2 Logo dan gambar

Kartu **Logo dan Gambar** mengatur empat gambar. Bila Anda tidak mengunggah, aplikasi memakai **gambar bawaan** (lencana abu-abu **Bawaan**). Gambar yang sudah Anda unggah diberi lencana hijau **Kustom**.

| Gambar | Dipakai di | Format dan ukuran |
|---|---|---|
| **Logo Instance** | Sidebar, navbar, ikon tab browser, halaman pemeliharaan | PNG, JPG, SVG, atau WEBP, maksimal 2 MB |
| **Logo Kop Surat dan Resi** | Kop surat keterangan dan resi pembayaran (PDF) | PNG atau JPG, maksimal 2 MB (WEBP dan SVG tidak didukung pembuat PDF) |
| **Gambar Halaman Login** | Foto di sisi kiri halaman masuk (layar lebar) | PNG, JPG, atau WEBP, minimal 800 x 600 piksel, maksimal 4 MB |
| **Ikon Aplikasi** | Ikon saat aplikasi dipasang di ponsel, layar offline, notifikasi (dipotong persegi dari tengah) | PNG, JPG, atau WEBP, minimal 512 x 512 piksel, maksimal 4 MB |

> 👤 **SIMULASI A-07: Mengganti logo kop surat**
>
> RW 21 memiliki logo baru. Mas Fajar menggantinya agar surat dan resi memakai logo yang benar.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Pengaturan Sistem, kartu Logo dan Gambar
> - **Berkas:** `logo-rw21-baru.png` (PNG, kurang dari 2 MB)

#### Langkah-langkah

**1. Cari kotak Logo Kop Surat dan Resi.**
Gulir ke kartu **Logo dan Gambar**. Setiap gambar punya pratinjau, tombol pilih berkas, dan keterangan format.

**2. Pilih berkas.**
Tekan kolom pilih berkas pada **Logo Kop Surat dan Resi**, lalu pilih `logo-rw21-baru.png`. Pratinjau langsung berubah.

**3. Tekan Simpan Semua Perubahan.**
Perubahan baru berlaku setelah disimpan.

> 📸 **SCREENSHOT ADM-13: Kartu Logo dan Gambar**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/pengaturan-sistem (gulir ke kartu Logo dan Gambar)
> - **Kondisi:** berkas baru dipilih pada Logo Kop Surat dan Resi, pratinjau sudah berubah
> - **Area:** kartu **Logo dan Gambar** saja
> - **Sorot:** kotak merah pada pratinjau Logo Kop Surat dan Resi, lencana **Bawaan** atau **Kustom**, dan keterangan format
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-13-logo-dan-gambar.png`

#### ✅ Hasilnya

- Muncul pesan **"Pengaturan sistem berhasil disimpan."**
- Lencana pada Logo Kop Surat dan Resi berubah menjadi hijau **Kustom**, dan muncul kotak centang **Kembalikan ke gambar bawaan**.
- Surat dan resi PDF yang dibuat sesudahnya memakai logo baru.

#### ⚠️ Perhatian

- **Muncul pesan "Gambar halaman login minimal 800 x 600 piksel."** Foto terlalu kecil. Pakai foto yang lebih besar.
- **Muncul pesan "Ikon aplikasi minimal 512 x 512 piksel."** Pakai gambar yang lebih besar, sebaiknya sudah persegi.
- **Muncul pesan "Format berkas tidak didukung untuk (nama gambar)."** Ubah ke format yang tertulis di keterangan bawah kotak.
- **Muncul pesan "Ukuran berkas (nama gambar) terlalu besar."** Kecilkan ukuran berkas sesuai batas pada tabel.
- **Ingin kembali ke gambar awal.** Centang **Kembalikan ke gambar bawaan** di bawah gambar itu, lalu **Simpan Semua Perubahan**. Berkas unggahan dihapus.
- Setelah mengganti **Ikon Aplikasi**, ponsel yang sudah memasang aplikasi mungkin baru memperbarui ikon setelah aplikasi dibuka ulang.

> 💡 **Tips:** Bila logo tidak berubah di browser, muat ulang halaman dengan menekan Ctrl+F5.

### 7.3 Persetujuan konten

Kartu **Persetujuan Konten** menentukan apakah konten yang diajukan operator **harus disetujui dulu** sebelum tayang. Ada tiga jenis konten, masing-masing dengan saklar sendiri: **Perlu persetujuan: Berita**, **Perlu persetujuan: Agenda**, dan **Perlu persetujuan: Pengumuman**. Penyetujunya adalah **Admin RW**, untuk konten RT maupun RW.

- **Saklar menyala**: konten dari operator menunggu persetujuan Admin RW.
- **Saklar mati**: konten **langsung terbit** saat diajukan.

> 👤 **SIMULASI A-08: Mengaktifkan persetujuan agenda**
>
> Beberapa agenda RT sempat terbit dengan jadwal yang keliru. Pengurus sepakat agenda kini harus diperiksa Admin RW dulu. Mas Fajar menyalakan saklar agenda.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Pengaturan Sistem, kartu Persetujuan Konten
> - **Data demo awal:** Berita menyala, Agenda mati, Pengumuman mati

#### Langkah-langkah

**1. Gulir ke kartu Persetujuan Konten.**

**2. Nyalakan saklar Agenda.**
Geser saklar pada **Perlu persetujuan: Agenda**. Muncul pemberitahuan kecil *Persetujuan agenda diaktifkan*.

**3. Tekan Simpan Semua Perubahan.**

> 📸 **SCREENSHOT ADM-14: Kartu Persetujuan Konten**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/pengaturan-sistem (gulir ke kartu Persetujuan Konten)
> - **Kondisi:** saklar Agenda sudah dinyalakan, belum disimpan
> - **Area:** kartu **Persetujuan Konten** saja
> - **Sorot:** kotak merah pada tiga saklar dan keterangan *Penyetuju: Admin RW*
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-14-persetujuan-konten.png`

#### ✅ Hasilnya

- Muncul pesan **"Pengaturan sistem berhasil disimpan."**
- Agenda yang diajukan operator sesudahnya menunggu persetujuan Admin RW.

#### ⚠️ Perhatian

- Perubahan berlaku untuk **pengajuan berikutnya**. Konten yang sudah menunggu persetujuan tetap diproses oleh penyetujunya.
- Mematikan saklar membuat konten **langsung tayang tanpa diperiksa**. Pertimbangkan dulu sebelum mematikan untuk berita.

> 💡 **Tips:** Setelah mencoba, kembalikan saklar Agenda ke posisi mati bila Anda ingin data demo kembali seperti awal.

> ➡️ **Cerita berlanjut:** Dampaknya terlihat pada Operator Konten dan Admin RW. Lihat **03-Op-Konten-RT.md** dan **07-Op-Konten-RW.md** (cerita C-07 dan C-08), serta **05-Admin-RW.md** (cerita C-07).

---

## 8. Pengaturan Halaman Utama

Menu **Pengaturan Halaman Utama** mengatur tampilan halaman depan (https://wargadigi21.com) yang dilihat semua pengunjung: teks banner, tiga gambar banner, footer, dan daftar partner. Di kanan atas ada tombol **Lihat Halaman Utama** untuk membuka halaman depan di tab baru.

Semua bagian ada dalam satu formulir. Bar di bagian bawah halaman berisi tombol **Batal** dan **Simpan Perubahan**.

> 👤 **SIMULASI C-12: Pengaturan kepengurusan dan Halaman Utama (bagian 2 dari 2)**
>
> Kepengurusan sudah rapi (lihat Bab 4). Sekarang Mas Fajar memastikan halaman depan memuat kontak sekretariat yang bisa dihubungi warga, dan menambahkan satu partner.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Pengaturan Halaman Utama
> - **Data demo awal:** badge *Halo Warga RW 21*, judul *Digitalisasi Gotong Royong RW 21 Tanimulya*, tombol *Layanan Warga* ke `/layanan-mandiri`, footer *WargaDigi 21*, copyright *© {tahun} WargaDigi 21 Desa Tanimulya. Supported by PkM POLBAN*, partner *Politeknik Negeri Bandung* (tanpa logo), gambar banner bawaan
> - **Data baru (contoh):** email `sekretariat@contoh.id`, telepon `0812-0000-0000`, partner *Karang Taruna RW 21*

### 8.1 Teks banner

Kartu **Banner: Teks** mengatur tulisan besar di bagian atas halaman depan.

| Kolom | Isi | Batas |
|---|---|---|
| **Label Kecil (badge)** | Tulisan kecil di atas judul, contoh *Halo Warga RW 21* | 60 huruf |
| **Judul** (wajib) | Judul besar banner | 120 huruf |
| **Deskripsi** | Kalimat penjelas di bawah judul | 400 huruf |
| **Teks Tombol** | Tulisan pada tombol. Kosongkan untuk **menyembunyikan tombol** | 40 huruf |
| **Tautan Tombol** | Tujuan tombol | 255 huruf |

Isi **Tautan Tombol** dengan awalan `/` untuk halaman di aplikasi (contoh `/layanan-mandiri`), atau alamat lengkap `https://...`. Awalan `#` juga diterima.

#### Langkah-langkah

**1. Buka Pengaturan Halaman Utama.**
Klik **Pengaturan Halaman Utama** di sidebar.

**2. Periksa kartu Banner: Teks.**
Pada cerita ini teksnya tidak diubah. Cukup periksa bahwa **Judul** terisi dan **Tautan Tombol** berisi `/layanan-mandiri`.

> 📸 **SCREENSHOT ADM-15: Kartu Banner: Teks**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/halaman-utama
> - **Kondisi:** data demo awal
> - **Area:** bagian atas halaman sampai kartu **Banner: Teks**
> - **Sorot:** kotak merah pada **Label Kecil (badge)**, **Judul**, **Teks Tombol**, dan **Tautan Tombol** beserta petunjuk di bawahnya
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-15-banner-teks.png`

### 8.2 Gambar banner

Kartu **Banner: Gambar** punya tiga kotak: **Gambar Utama** (berlabel *Besar*), **Gambar Samping Atas** dan **Gambar Samping Bawah** (berlabel *Kecil*). Setiap gambar punya tiga pilihan sumber:

- **Bawaan**: gambar bawaan aplikasi.
- **Galeri**: foto dari **Galeri Dokumentasi**. Hanya foto dari agenda tingkat RW yang sudah terbit yang bisa dipilih. Bila galeri kosong, tombol pilih tidak bisa ditekan dan tertulis *Galeri Dokumentasi masih kosong*.
- **Unggah**: berkas baru (JPG, PNG, atau WEBP, maksimal 5 MB). Otomatis dikompres menjadi WEBP.

Pada dua gambar samping, ada kolom **Keterangan** (maksimal 40 huruf) dan **Sub-keterangan** (maksimal 60 huruf) yang tampil di atas gambar.

#### Langkah-langkah

**1. Pilih sumber gambar.**
Pada kotak gambar, tekan salah satu tombol **Bawaan**, **Galeri**, atau **Unggah**. Pratinjau di atasnya ikut berubah.

**2. Bila memilih Galeri.**
Tekan **Pilih dari Galeri Dokumentasi**, klik foto yang diinginkan pada jendela **Pilih Foto dari Galeri Dokumentasi**. Jendela menutup sendiri dan foto menjadi pratinjau.

**3. Bila memilih Unggah.**
Tekan kolom **Unggah gambar** lalu pilih berkas. Bila gambar sebelumnya sudah berupa unggahan, mengosongkan kolom ini berarti tetap memakai gambar yang sudah ada.

**4. Pada cerita ini** biarkan ketiganya tetap **Bawaan**.

> 📸 **SCREENSHOT ADM-16: Kartu Banner: Gambar**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/halaman-utama (gulir ke kartu Banner: Gambar)
> - **Kondisi:** data demo awal, ketiganya Bawaan
> - **Area:** kartu **Banner: Gambar** saja
> - **Sorot:** kotak merah pada tiga tombol pilihan **Bawaan / Galeri / Unggah**, pratinjau, dan kolom **Keterangan** serta **Sub-keterangan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-16-banner-gambar.png`

### 8.3 Footer

Kartu **Footer** mengatur bagian bawah halaman depan.

| Kolom | Isi | Batas |
|---|---|---|
| **Nama di Footer** (wajib) | Contoh *WargaDigi 21* | 60 huruf |
| **Deskripsi Singkat** | Kalimat singkat di footer | 300 huruf |
| **Alamat Sekretariat** | Satu baris per baris alamat | 300 huruf |
| **Email** | Alamat email sekretariat | 120 huruf |
| **Telepon / WA** | Hanya angka, spasi, `+`, `-`, dan tanda kurung | 30 huruf |
| **Tautan Peta Lokasi** | Alamat peta lengkap (`https://...`) | 500 huruf |
| **Teks Copyright** (wajib) | Gunakan `{tahun}` agar tahun terisi otomatis | 160 huruf |

#### Langkah-langkah

**1. Gulir ke kartu Footer.**

**2. Isi kontak sekretariat.**
- **Email**: `sekretariat@contoh.id`
- **Telepon / WA**: `0812-0000-0000`

**3. Periksa Teks Copyright.**
Pastikan masih berbunyi `© {tahun} WargaDigi 21 Desa Tanimulya. Supported by PkM POLBAN`. Tulisan `{tahun}` akan diganti tahun berjalan di halaman depan.

> 📸 **SCREENSHOT ADM-17: Kartu Footer**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/halaman-utama (gulir ke kartu Footer)
> - **Kondisi:** Email dan Telepon / WA sudah diisi, belum disimpan
> - **Area:** kartu **Footer** saja
> - **Sorot:** kotak merah pada **Email**, **Telepon / WA**, dan **Teks Copyright** beserta petunjuk `{tahun}`
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-17-footer.png`

### 8.4 Partner

Kartu **Partner / Didukung Oleh** berisi daftar pihak yang mendukung. Judul kolom di halaman depan diatur lewat **Judul Kolom** (contoh *Didukung oleh*). Maksimal **12 partner**, dan kolom ini tidak tampil di halaman depan bila daftarnya kosong.

Setiap partner punya: nama (wajib, maksimal 80 huruf), tautan (opsional, harus alamat lengkap `https://...`), dan logo (opsional, JPG, PNG, atau WEBP, maksimal 5 MB). Tanpa logo, partner tampil sebagai nama.

#### Langkah-langkah

**1. Tekan Tambah Partner.**
Tombol hijau di kanan atas kartu. Satu baris kosong muncul dan kolom namanya langsung aktif.

**2. Isi baris baru.**
- Nama partner: `Karang Taruna RW 21`
- Tautan: kosongkan (opsional)
- Logo: kosongkan (opsional)

**3. Hapus partner bila perlu.**
Tekan ikon tempat sampah di ujung baris.

> 📸 **SCREENSHOT ADM-18: Kartu Partner**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/halaman-utama (gulir ke kartu Partner)
> - **Kondisi:** partner *Politeknik Negeri Bandung* dan satu baris baru *Karang Taruna RW 21*
> - **Area:** kartu **Partner / Didukung Oleh** saja
> - **Sorot:** kotak merah pada tombol **Tambah Partner**, kolom nama, tautan, pilih logo, dan ikon tempat sampah
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-18-partner.png`

### 8.5 Menyimpan dan memeriksa hasil

**1. Tekan Simpan Perubahan.**
Tombol hijau di bar bawah halaman.

**2. Lihat hasilnya.**
Tekan **Lihat Halaman Utama** di kanan atas. Halaman depan terbuka di tab baru.

#### ✅ Hasilnya

- Muncul pesan **"Pengaturan halaman utama berhasil disimpan."**
- Footer halaman depan memuat email dan telepon sekretariat, dan kolom **Didukung oleh** memuat dua partner.

> 📸 **SCREENSHOT ADM-19: Halaman depan setelah disimpan**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** / (halaman depan, tab baru)
> - **Kondisi:** setelah menekan **Simpan Perubahan**, gulir ke footer
> - **Area:** bagian footer halaman depan
> - **Sorot:** kotak merah pada email, telepon, teks copyright, dan kolom **Didukung oleh**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-19-halaman-depan-footer.png`

### ⚠️ Perhatian

- **Muncul kotak merah "Periksa kembali isian berikut:".** Baca daftar di bawahnya. Pesan yang mungkin muncul:
  - **"Judul banner wajib diisi."** Isi kolom Judul.
  - **"Tautan tombol harus diawali "/" (halaman aplikasi), "http(s)://", atau "#"."** Perbaiki isi Tautan Tombol.
  - **"Nomor telepon hanya boleh angka, spasi, +, -, dan tanda kurung."** Hapus huruf pada kolom Telepon / WA.
  - **"Teks copyright wajib diisi."** Isi kolom Teks Copyright.
  - **"Nama partner wajib diisi."** Isi nama pada setiap baris partner, atau hapus barisnya.
  - **"Tautan partner harus berupa URL lengkap (https://...)."** Tulis alamat lengkap, atau kosongkan.
  - **"Maksimal 12 partner."** Hapus satu partner sebelum menambah yang baru. Tombol **Tambah Partner** otomatis tidak bisa ditekan saat sudah 12.
  - **"Format gambar harus JPG, PNG, atau WEBP."** atau **"Ukuran gambar maksimal 5 MB."** Ganti berkas gambar.
  - **"Pilih foto dari Galeri Dokumentasi untuk gambar utama."** Anda memilih sumber **Galeri** tetapi belum memilih foto. Pilih foto atau ganti sumbernya.
  - **"Unggah gambar untuk gambar utama."** Anda memilih sumber **Unggah** tetapi belum memilih berkas.
- Bagian yang berisi kesalahan membuat **seluruh** simpanan gagal. Perbaiki dulu, lalu simpan lagi.
- Foto Galeri hanya dari **agenda tingkat RW** yang sudah terbit, karena halaman depan terbuka untuk umum. Foto agenda RT tidak bisa dipilih.
- Tombol **Batal** menutup perubahan yang belum disimpan dan memuat ulang halaman.

> 💡 **Tips:** Gunakan `{tahun}` pada copyright. Anda tidak perlu mengganti tahun setiap tahun baru.

> ➡️ **Cerita berlanjut:** Cerita C-12 selesai di sini. Foto yang bisa dipilih dari Galeri berasal dari dokumentasi agenda RW. Lihat **07-Op-Konten-RW.md** (cerita C-09).

---

## 9. Manajemen Hak Akses

Menu **Manajemen Hak Akses** menampilkan semua akun pengguna dan perannya. Di sini Anda **mengubah peran** dan **membuat akun pengurus**. Satu akun boleh memiliki **lebih dari satu peran** (akun multi-peran). Pemilik akun multi-peran diminta memilih peran saat masuk, dan bisa berganti lewat tombol **Ganti Peran** di sidebar.

Di atas tabel ada pemberitahuan biru: peran **Ketua RT** dan **Ketua RW** hanya bisa diberikan kepada akun ber-NIK yang sudah menjadi **pejabat aktif** di menu Kepengurusan.

Daftar peran yang tersedia: **Admin Aplikasi**, **Admin RW**, **Ketua RW**, **Op Konten RW**, **Op Keuangan RW**, **Ketua RT**, **Op Konten RT**, **Op Keuangan RT**, **DKM**, dan **Warga**.

### 9.1 Mencari dan membaca daftar akun

#### Langkah-langkah

**1. Buka Manajemen Hak Akses.**
Klik **Manajemen Hak Akses** di sidebar.

**2. Cari akun.**
Ketik di kolom *Cari nama pengguna atau NIK...* lalu tekan **Cari**. Pencarian cocok dengan **username** atau **NIK**.

**3. Baca tabel.**
Kolomnya **Nama Pengguna** (dengan username di bawahnya), **NIK** (kosong bila akun tanpa NIK), **Status Akun**, **Role / Hak Akses**, dan **Aksi**. Daftar menampilkan 10 akun per halaman, dengan keterangan *Menampilkan 1-10 dari 14 pengguna* (pada data demo).

- **Status Akun** hijau **Aktif** berarti akun bisa dipakai masuk. Status lain tampil abu-abu, misalnya **Nonaktif** atau **Unverified**.
- Setiap peran tampil sebagai lencana biru. Akun tanpa peran menampilkan **Belum ada peran**.

> 📸 **SCREENSHOT ADM-20: Daftar Manajemen Hak Akses**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/manajemen-hak-akses
> - **Kondisi:** data demo awal, halaman 1
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Buat Akun Pengurus**, kotak pencarian, kolom **Status Akun**, lencana peran (akun `budisampurno` dengan dua peran), dan tombol pensil di kolom **Aksi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-20-daftar-hak-akses.png`

### 9.2 Menambah peran pada akun

> 👤 **SIMULASI A-01: Bu Siti Aminah merangkap Operator Konten RT**
>
> Bu Siti Aminah, Bendahara RT 03, bersedia membantu menulis berita RT 03. Mas Fajar menambahkan peran **Op Konten RT** pada akunnya, di samping peran **Op Keuangan RT** yang sudah ada.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Akun yang diubah:** `operator_rt3` (Op Keuangan RT, RT 03)
> - **Peran ditambahkan:** Op Konten RT

#### Langkah-langkah

**1. Cari akunnya.**
Ketik `operator_rt3` pada kolom pencarian lalu tekan **Cari**.

**2. Buka Edit Peran.**
Tekan tombol pensil hijau di kolom **Aksi**. Muncul jendela **Edit Peran** yang memuat nama akun, username, dan NIK (atau tulisan *tanpa NIK*).

**3. Centang peran tambahan.**
Pada **Pilih peran (centang satu atau lebih)**, biarkan **Op Keuangan RT** tercentang dan centang juga **Op Konten RT**.

**4. Periksa RT.**
Kolom **RT yang Dikelola** muncul untuk peran tingkat RT. Pastikan terpilih **RT 03**.

**5. Tekan Simpan Peran.**

> 📸 **SCREENSHOT ADM-21: Jendela Edit Peran**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/manajemen-hak-akses?search=operator_rt3 (jendela terbuka)
> - **Kondisi:** peran **Op Keuangan RT** dan **Op Konten RT** tercentang, RT 03 terpilih
> - **Area:** jendela **Edit Peran** saja
> - **Sorot:** kotak merah pada dua kotak centang peran, kolom **RT yang Dikelola**, dan peringatan kuning di bagian bawah
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-21-edit-peran.png`

#### ✅ Hasilnya

- Muncul pesan **"Peran operator_rt3 berhasil diperbarui: Op Konten RT, Op Keuangan RT."**
- Pada tabel, akun `operator_rt3` menampilkan dua lencana peran.
- Saat `operator_rt3` masuk berikutnya, ia diminta **memilih peran** (Op Konten RT atau Op Keuangan RT).

> 📸 **SCREENSHOT ADM-22: Peran berhasil ditambahkan**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/manajemen-hak-akses (sesaat setelah Simpan Peran)
> - **Kondisi:** pesan sukses tampil, cari `operator_rt3`
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses dan dua lencana peran di baris `operator_rt3`
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-22-peran-bertambah.png`

#### ⚠️ Perhatian

- **Muncul pesan "Pilih minimal satu peran."** Setiap akun harus punya minimal satu peran. Centang setidaknya satu.
- **Kotak Warga atau Ketua RT atau Ketua RW tidak bisa dicentang.** Akun itu tanpa NIK (tertulis *butuh NIK*). Peran ini hanya untuk akun yang tertaut NIK penduduk.
- **Muncul pesan "Peran Warga hanya untuk akun yang tertaut NIK kependudukan."** Sama dengan di atas.
- **Muncul pesan "Akun ini belum terdaftar sebagai Ketua RT aktif di menu Kepengurusan. Tetapkan dulu yang bersangkutan sebagai Ketua RT aktif sebelum memberi peran ini."** (atau versi Ketua RW). Tetapkan orangnya lebih dulu di **Kepengurusan** (Bab 4.2), lalu ulangi.
- **Muncul pesan "Pilih RT untuk peran operator/ketua RT."** Pilih RT pada kolom **RT yang Dikelola**.
- Mencabut centang suatu peran berarti akun **kehilangan akses** peran itu pada saat yang sama. Pastikan pemilik akun tahu.
- Peran yang paling atas dalam daftar centang menjadi peran utama akun.

> 💡 **Tips:** Untuk mengembalikan data demo seperti awal, buka lagi **Edit Peran** `operator_rt3` dan hilangkan centang **Op Konten RT**.

> ➡️ **Cerita berlanjut:** Akun yang punya dua peran memilih peran saat masuk. Lihat **00-Umum-Publik-dan-Login.md** (memilih peran).

### 9.3 Memeriksa akun nonaktif

Beberapa akun berstatus **Nonaktif**. Status ini muncul **otomatis** ketika pemegang akun keluarga dipindahkan ke anggota lain dan disetujui oleh Ketua RT. Akun lama tidak dihapus, tetapi tidak bisa dipakai masuk. Menu Manajemen Hak Akses **tidak punya tombol untuk menonaktifkan atau mengaktifkan** akun. Di sini Anda hanya membaca statusnya.

> 👤 **SIMULASI A-02: Menelusuri akun nonaktif `donisetiawan`**
>
> Pak Doni Setiawan bertanya kenapa ia tidak bisa masuk. Mas Fajar mencari akunnya untuk memastikan penyebabnya.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Akun yang diperiksa:** `donisetiawan` (Warga RT 03, status **Nonaktif**, tanpa peran)

#### Langkah-langkah

**1. Cari akunnya.**
Ketik `donisetiawan` pada kolom pencarian lalu tekan **Cari**.

**2. Baca barisnya.**
**Status Akun** tampil abu-abu **Nonaktif**, dan kolom peran menampilkan **Belum ada peran**.

**3. Jelaskan ke warga.**
Bila Pak Doni mencoba masuk, aplikasi menampilkan: **"Akun ini telah dinonaktifkan dan tidak dapat digunakan untuk masuk."** Penyebab umumnya pemegang akun keluarga sudah dipindahkan. Arahkan Pak Doni ke Ketua RT 03 untuk proses akun keluarganya.

> 📸 **SCREENSHOT ADM-23: Akun nonaktif donisetiawan**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/manajemen-hak-akses?search=donisetiawan
> - **Kondisi:** hasil pencarian menampilkan satu akun
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada label **Nonaktif** dan tulisan **Belum ada peran**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-23-akun-nonaktif.png`

#### ✅ Hasilnya

- Anda tahu pasti bahwa akun `donisetiawan` nonaktif, sehingga masalahnya bukan salah kata sandi.

#### ⚠️ Perhatian

- Menambahkan peran lewat **Edit Peran** pada akun nonaktif **tidak mengaktifkan** akunnya. Status tetap **Nonaktif** dan akun tetap tidak bisa dipakai masuk. Jangan menjanjikan akun aktif hanya karena peran ditambah.
- **Pencarian tidak menemukan akun.** Pencarian hanya cocok dengan username dan NIK. Mencari dengan nama lengkap tidak menghasilkan apa-apa.

> ➡️ **Cerita berlanjut:** Proses pemindahan pemegang akun keluarga ada di **01-Warga.md** (profil keluarga) dan **02-Ketua-RT.md** (persetujuan perubahan data keluarga, cerita C-10).

### 9.4 Membuat akun pengurus

Tombol **Buat Akun Pengurus** (kanan atas) dipakai untuk membuat akun baru bagi pengurus atau petugas. Ada dua cara, dipilih pada **Sumber akun**:

| Sumber akun | Untuk siapa | Catatan |
|---|---|---|
| **Ambil dari warga** | Penduduk yang **belum punya akun** | Akun tertaut NIK. Peran Ketua RT dan Ketua RW boleh, asalkan orangnya pejabat aktif |
| **Username baru** | Petugas tanpa NIK, login pakai username | Peran Ketua RT dan Ketua RW **tidak tersedia** |

Kolom pada kedua cara: **Username**, **Password** (minimal 8 karakter), **Konfirmasi Password**, **Peran (boleh lebih dari satu)**, dan **RT yang Dikelola** (muncul bila memilih peran tingkat RT). Untuk **Ambil dari warga**, ada juga **Pilih warga** (cari nama atau NIK) dan username terisi saran dari nama yang bisa diubah.

> 👤 **SIMULASI A-04: Akun baru untuk Operator Konten RT 05**
>
> RT 05 menunjuk petugas penulis konten tetapi belum punya akun. Mas Fajar membuatkan akun baru.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Manajemen Hak Akses
> - **Akun baru:** username `opkontenrt05rw21`, peran **Op Konten RT**, RT 05

#### Langkah-langkah

**1. Tekan Buat Akun Pengurus.**
Muncul jendela **Buat Akun Pengurus**.

**2. Pilih sumber akun.**
Tekan **Username baru**.

**3. Isi formulir.**
- **Username**: `opkontenrt05rw21` (huruf, angka, titik, garis bawah, atau strip)
- **Password** dan **Konfirmasi Password**: minimal 8 karakter, harus sama
- **Peran**: centang **Op Konten RT**
- **RT yang Dikelola**: pilih **RT 05**

**4. Tekan Buat Akun.**

> 📸 **SCREENSHOT ADM-24: Jendela Buat Akun Pengurus**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/manajemen-hak-akses (jendela terbuka)
> - **Kondisi:** mode **Username baru**, semua kolom sudah diisi
> - **Area:** jendela **Buat Akun Pengurus** saja
> - **Sorot:** kotak merah pada tombol **Username baru**, kolom Username dan Password, peran **Op Konten RT**, dan kolom **RT yang Dikelola**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-24-buat-akun-pengurus.png`

#### ✅ Hasilnya

- Muncul pesan **"Akun pengurus "opkontenrt05rw21" berhasil dibuat dengan peran: Op Konten RT."**
- Akun langsung berstatus **Aktif** dan bisa dipakai masuk.

#### ⚠️ Perhatian

- **Muncul pesan "Username sudah digunakan. Pilih username lain."** Ganti username.
- **Muncul pesan "Username hanya boleh huruf, angka, titik, garis bawah, atau strip."** Hapus spasi atau tanda lain.
- **Muncul pesan "Password minimal 8 karakter."** atau **"Konfirmasi password tidak sama."** Perbaiki sandi.
- **Muncul pesan "Pilih minimal satu peran pengurus."** (mode Username baru) atau **"Pilih minimal satu peran."** Centang minimal satu peran.
- **Muncul pesan "Pilih RT untuk peran operator RT."** Pilih RT yang dikelola.
- **Mode Username baru: pesan "Peran Ketua RT, Ketua RW tidak dapat diberikan ke akun tanpa NIK..."** Gunakan **Ambil dari warga**, dan pastikan orangnya sudah pejabat aktif di Kepengurusan.
- **Mode Ambil dari warga: "Pilih warga terlebih dahulu."** Pilih satu warga dari daftar. Bila tertulis *Semua penduduk sudah memiliki akun*, gunakan **Edit Peran** pada akun yang ada.
- **Mode Ambil dari warga: "Warga ini sudah memiliki akun. Gunakan Edit Peran pada akun tersebut."** Cari akunnya di tabel lalu tambahkan perannya.
- Akun yang dibuat memakai kata sandi dari Anda. Sampaikan sandi lewat cara aman, dan minta pemiliknya menjaga kerahasiaannya.

> 💡 **Tips:** Pada **Ambil dari warga**, username terisi otomatis dari nama. Ubah bila terlalu panjang atau mirip akun lain.

> ➡️ **Cerita berlanjut:** Pemilik akun baru bisa mulai bekerja sesuai perannya. Lihat **03-Op-Konten-RT.md** (cerita C-08).

---

## 10. Log Aktivitas

**Log Aktivitas** adalah catatan jejak apa yang terjadi di aplikasi: siapa masuk, siapa mengubah pengaturan, siapa mengelola data. Catatan ini berguna saat ada pertanyaan seperti "siapa yang mengubah ini?".

### 10.1 Membaca dan menyaring log

> 👤 **SIMULASI A-09: Menelusuri perubahan pengaturan**
>
> Seorang pengurus bertanya kenapa agenda RT kini harus disetujui. Mas Fajar membuka log untuk melihat kapan pengaturan berubah.
>
> - **Masuk sebagai:** `superadmin`, kata sandi `password`
> - **Menu:** Log Aktivitas
> - **Kategori:** Perubahan Pengaturan

#### Langkah-langkah

**1. Buka Log Aktivitas.**
Klik **Log Aktivitas** di sidebar. Secara bawaan halaman menampilkan **7 hari terakhir**.

**2. Pilih kategori.**
Pada **Kategori Aktivitas**, pilih salah satu: **Semua Kategori**, **Login / Logout**, **Perubahan Pengaturan**, **Manajemen RT**, atau **Manajemen Pengguna**.

**3. Atur tanggal.**
Isi **Dari Tanggal** dan **Sampai Tanggal** untuk melihat rentang yang lebih panjang.

**4. Cari kata kunci.**
Ketik kata kunci pada *Cari Pengguna / Aktivitas* (placeholder: *Ketik kata kunci lalu tekan Enter...*). Pencarian mencocokkan **isi deskripsi** dan jenis log.

**5. Tekan tombol saring.**
Tombol hijau berikon corong. Bila filter sedang dipakai, muncul tautan **Reset Filter** untuk kembali ke tampilan awal.

**6. Baca tabelnya.**

| Kolom | Isi |
|---|---|
| **Waktu** | Tanggal dan jam (WIB) |
| **Pengguna & Peran** | Siapa pelakunya, dengan lencana peran. Tertulis **SISTEM** atau **Tamu** bila tidak ada akun pelaku |
| **Jenis Aktivitas** | Misalnya *Login*, *Logout*, *Login Gagal*, *Mengubah Pengaturan*, *Manajemen Pengguna*, *Membuat Data Baru*, *Memperbarui Data*, *Menghapus Data* |
| **Deskripsi** | Keterangan singkat, misalnya *Mengubah pengaturan sistem.* |
| **Status** | **Sukses** (hijau) atau **Gagal** |

Daftar menampilkan 15 catatan per halaman, dengan keterangan *Menampilkan 1-15 dari (jumlah) aktivitas*. Bila tidak ada catatan, tertulis **Belum ada aktivitas yang tercatat.**

> 📸 **SCREENSHOT ADM-25: Log Aktivitas dengan filter**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/log-aktivitas?category=pengaturan
> - **Kondisi:** kategori **Perubahan Pengaturan** dipilih, ada catatan hasil simulasi sebelumnya
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kolom filter, tombol **Hapus Log** dan **Ekspor Log**, lencana peran di kolom **Pengguna & Peran**, dan kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-25-log-aktivitas.png`

#### ✅ Hasilnya

- Anda melihat baris *Mengubah pengaturan sistem.* lengkap dengan waktu dan pelakunya (`superadmin`).

#### ⚠️ Perhatian

- **Log yang dicari tidak muncul.** Cek rentang tanggal. Bawaannya hanya 7 hari terakhir.
- **Filter kategori tidak menampilkan semua aktivitas.** Pilihan kategori hanya mencakup empat jenis. Untuk aktivitas lain, misalnya perubahan kepengurusan atau kategori transaksi, pilih **Semua Kategori**.
- **Kolom pencarian tidak mengenali nama orang.** Pencarian mencocokkan deskripsi dan jenis log, bukan nama pelaku. Untuk melihat satu orang, cari kata yang ada di deskripsi.
- Tombol **Ekspor Log** hanya memunculkan pemberitahuan *Fitur ekspor log akan segera tersedia.* Fitur ekspor belum bisa dipakai.

> 💡 **Tips:** Untuk mencari upaya masuk yang gagal, pilih kategori **Login / Logout** lalu perhatikan baris berjenis **Login Gagal**.

### 10.2 Menghapus log lama

Tombol **Hapus Log** menghapus catatan pada rentang tanggal tertentu. Penghapusan **permanen**.

#### Langkah-langkah

**1. Tekan Hapus Log.**
Tombol merah di kanan atas. Muncul jendela **Hapus Log Aktivitas**.

**2. Isi rentang tanggal.**
Isi **Dari Tanggal** dan **Sampai Tanggal** (keduanya wajib). Tanggal akhir ikut terhapus sampai akhir hari.

**3. Tekan Hapus.**
Muncul jendela konfirmasi dengan tulisan *Data log pada rentang tanggal ini akan dihapus permanen. Lanjutkan?* Tekan **Ya, Hapus** bila yakin.

> 📸 **SCREENSHOT ADM-26: Jendela Hapus Log Aktivitas**
> - **Akun:** superadmin (Admin Aplikasi)
> - **URL:** /admin/log-aktivitas (jendela terbuka)
> - **Kondisi:** rentang tanggal sudah diisi (contoh: satu bulan yang sudah lama berlalu)
> - **Area:** jendela **Hapus Log Aktivitas** saja
> - **Sorot:** kotak merah pada **Dari Tanggal**, **Sampai Tanggal**, dan tombol **Hapus**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ADM-26-hapus-log.png`

#### ✅ Hasilnya

- Muncul pesan **"Berhasil menghapus (jumlah) log aktivitas pada rentang (tanggal awal - tanggal akhir)."**
- Aksi penghapusan sendiri **dicatat sebagai log baru** (*Menghapus (jumlah) log aktivitas periode ...*) agar tetap bisa ditelusuri.

#### ⚠️ Perhatian

- **Muncul pesan "Lengkapi rentang tanggal terlebih dahulu."** Isi kedua tanggal.
- **Muncul pesan "Tanggal akhir tidak boleh sebelum tanggal mulai."** Perbaiki urutan tanggal.
- **Muncul pesan "Tidak ada log aktivitas pada rentang tanggal tersebut."** Tidak ada yang terhapus. Ubah rentang tanggal.
- Log yang sudah dihapus **tidak bisa dikembalikan**. Hapus hanya catatan yang sudah tidak diperlukan, dan jangan menghapus catatan yang masih menjadi bukti suatu kejadian.

> ➡️ **Cerita berlanjut:** Hasil kerja Anda di bab lain tercatat di sini. Lihat Bab 11 untuk cerita lengkapnya.

---

## 11. Cerita Lengkap (Lintas Peran)

### Cerita bersama peran lain (dari README)

| Kode | Cerita | Bagian Admin Aplikasi | Di mana |
|---|---|---|---|
| **C-12** | Pengaturan kepengurusan (pejabat penanda tangan surat) dan Halaman Utama | Seluruh cerita: bagian 1 di Bab 4, bagian 2 di Bab 8 | File ini |

Cerita C-12 tidak melibatkan peran lain. Dampaknya terasa pada surat yang dicetak (nama Ketua RT dan Ketua RW), laporan keuangan (nama Ketua dan Bendahara), dan halaman depan.

### Cerita khusus Admin Aplikasi

| Kode | Cerita | Letak |
|---|---|---|
| **A-00** | Pemeriksaan pagi di Dashboard | Bab 3 |
| **A-01** | Bu Siti Aminah merangkap Operator Konten RT (menambah peran) | Bab 9.2 |
| **A-02** | Menelusuri akun nonaktif `donisetiawan` | Bab 9.3 |
| **A-03** | Menambah kategori umum *Dana Kerja Bakti* | Bab 5 |
| **A-04** | Membuat akun Operator Konten RT 05 | Bab 9.4 |
| **A-05** | Memeriksa etalase Pojok UMKM | Bab 6 |
| **A-06** | Memperbarui deskripsi instansi | Bab 7.1 |
| **A-07** | Mengganti logo kop surat dan resi | Bab 7.2 |
| **A-08** | Mengaktifkan persetujuan agenda | Bab 7.3 |
| **A-09** | Menelusuri perubahan pengaturan lewat log | Bab 10.1 |

### Urutan kerja yang disarankan saat menyiapkan aplikasi baru

1. **Pengaturan Sistem**: isi nama, domain, deskripsi, dan unggah logo (Bab 7).
2. **Kepengurusan**: tetapkan Ketua, Sekretaris, dan Bendahara untuk RW, setiap RT, dan DKM (Bab 4).
3. **Manajemen Hak Akses**: buat akun pengurus dan berikan peran. Peran Ketua RT dan Ketua RW **setelah** pejabatnya ditetapkan (Bab 9).
4. **Kategori Transaksi**: periksa kategori umum (Bab 5).
5. **Persetujuan Konten**: tentukan aturan berita, agenda, dan pengumuman (Bab 7.3).
6. **Pengaturan Halaman Utama**: rapikan halaman depan (Bab 8).
7. **Log Aktivitas**: pantau aktivitas awal (Bab 10).

---

## 12. Masalah yang Sering Terjadi (FAQ)

**Saya tidak melihat menu Admin Aplikasi setelah masuk.**
Akun Anda tidak memiliki peran **Admin Aplikasi**, atau Anda sedang memakai peran lain. Bila akun punya banyak peran, tekan **Ganti Peran** di sidebar lalu pilih **Admin Aplikasi**.

**Saya lupa kata sandi `superadmin`.**
Aplikasi tidak punya menu reset kata sandi. Hubungi pengelola server.

**Apakah saya bisa mengganti kata sandi akun orang lain di Manajemen Hak Akses?**
Tidak. Menu itu mengubah peran dan membuat akun baru, tetapi tidak mereset kata sandi.

**Bisakah saya menonaktifkan atau mengaktifkan akun dari Manajemen Hak Akses?**
Tidak. Status akun hanya bisa dibaca di sana. Status **Nonaktif** muncul otomatis saat pemegang akun keluarga dipindahkan lewat proses persetujuan Ketua RT.

**Saya menetapkan Ketua RT baru, tapi akunnya tidak bisa membuka menu Ketua RT.**
Menetapkan pejabat di **Kepengurusan** tidak otomatis mengubah akun. Tambahkan peran **Ketua RT** pada akun orang itu lewat **Manajemen Hak Akses** (Bab 9.2).

**Surat atau laporan masih memakai nama Ketua yang lama.**
Pastikan pejabat baru sudah **Aktif Sekarang** pada **Riwayat** dan tanggal **Mulai**-nya benar. Laporan keuangan memakai pejabat yang menjabat pada **akhir bulan laporan**, jadi laporan bulan lama tetap memakai pejabat saat itu.

**Nama pejabat tidak muncul di surat atau laporan, hanya titik-titik atau kosong.**
Jabatan itu **Belum ditetapkan** di Kepengurusan. Tetapkan pejabatnya.

**Logo baru tidak tampil di browser.**
Muat ulang dengan Ctrl+F5. Bila tetap lama, pastikan Anda sudah menekan **Simpan Semua Perubahan**.

**Pengaturan Sistem gagal disimpan dengan kotak merah "Terjadi kesalahan:".**
Baca pesan di bawahnya. Biasanya karena Nama Instance kosong, atau gambar terlalu besar, terlalu kecil, atau berformat salah (lihat tabel di Bab 7.2).

**Saya tidak menemukan pengaturan notifikasi, keamanan, atau mode pemeliharaan di Pengaturan Sistem.**
Bagian itu belum ditampilkan di halaman. Yang tersedia: Informasi Instance, Logo dan Gambar, dan Persetujuan Konten.

**Tombol pilih foto Galeri di Halaman Utama tidak bisa ditekan.**
Belum ada foto dokumentasi dari agenda **RW** yang berstatus terbit. Operator Konten RW perlu mengunggah dokumentasi agenda terlebih dulu, atau gunakan **Bawaan** atau **Unggah**.

**Halaman depan belum berubah padahal sudah disimpan.**
Muat ulang halaman depan dengan Ctrl+F5. Pastikan pesan **"Pengaturan halaman utama berhasil disimpan."** sempat tampil.

**Saya tidak bisa menambah partner ke-13.**
Batasnya 12 partner. Hapus salah satu partner dulu.

**Kategori transaksi buatan Operator Keuangan tidak ada di halaman saya.**
Memang begitu. Admin Aplikasi hanya mengelola kategori umum.

**Warga bertanya kenapa usahanya tidak tampil di Pojok UMKM.**
Usaha tampil setelah disetujui Admin RW dan berstatus aktif. Arahkan warga ke pengurus RW.

**Log aktivitas hari ini kosong.**
Periksa rentang tanggal. Filter bawaan 7 hari terakhir, dan kategori mungkin masih terpilih dari pencarian sebelumnya. Tekan **Reset Filter**.

---

## 13. Lampiran: Daftar Gambar

Ambil gambar sesuai urutan bab. Semua gambar desktop 1366×768 dengan akun `superadmin`.

| Kode | Judul | URL | Nama file | Bab |
|---|---|---|---|---|
| ADM-01 | Peta menu Admin Aplikasi | /admin/dashboard | `ADM-01-peta-menu.png` | 1.3 |
| ADM-02 | Dashboard Admin Aplikasi | /admin/dashboard | `ADM-02-dashboard.png` | 3 |
| ADM-03 | Tabel Kepengurusan | /admin/pejabat | `ADM-03-tabel-kepengurusan.png` | 4.1 |
| ADM-04 | Jendela Set Sekretaris RW | /admin/pejabat | `ADM-04-set-sekretaris-rw.png` | 4.2 |
| ADM-05 | Sekretaris RW sudah terisi | /admin/pejabat | `ADM-05-sekretaris-terisi.png` | 4.2 |
| ADM-06 | Jendela Riwayat pejabat | /admin/pejabat | `ADM-06-riwayat-pejabat.png` | 4.3 |
| ADM-07 | Daftar Kategori Transaksi | /admin/kategori-transaksi | `ADM-07-daftar-kategori.png` | 5.1 |
| ADM-08 | Jendela Tambah Kategori | /admin/kategori-transaksi | `ADM-08-tambah-kategori.png` | 5.2 |
| ADM-09 | Kategori baru tampil di daftar | /admin/kategori-transaksi | `ADM-09-kategori-baru.png` | 5.2 |
| ADM-10 | Jendela Edit Kategori | /admin/kategori-transaksi | `ADM-10-edit-kategori.png` | 5.3 |
| ADM-11 | Pojok UMKM dilihat Admin Aplikasi | /galeri | `ADM-11-pojok-umkm.png` | 6 |
| ADM-12 | Kartu Informasi Instance | /admin/pengaturan-sistem | `ADM-12-informasi-instance.png` | 7.1 |
| ADM-13 | Kartu Logo dan Gambar | /admin/pengaturan-sistem | `ADM-13-logo-dan-gambar.png` | 7.2 |
| ADM-14 | Kartu Persetujuan Konten | /admin/pengaturan-sistem | `ADM-14-persetujuan-konten.png` | 7.3 |
| ADM-15 | Kartu Banner: Teks | /admin/halaman-utama | `ADM-15-banner-teks.png` | 8.1 |
| ADM-16 | Kartu Banner: Gambar | /admin/halaman-utama | `ADM-16-banner-gambar.png` | 8.2 |
| ADM-17 | Kartu Footer | /admin/halaman-utama | `ADM-17-footer.png` | 8.3 |
| ADM-18 | Kartu Partner | /admin/halaman-utama | `ADM-18-partner.png` | 8.4 |
| ADM-19 | Halaman depan setelah disimpan | / | `ADM-19-halaman-depan-footer.png` | 8.5 |
| ADM-20 | Daftar Manajemen Hak Akses | /admin/manajemen-hak-akses | `ADM-20-daftar-hak-akses.png` | 9.1 |
| ADM-21 | Jendela Edit Peran | /admin/manajemen-hak-akses | `ADM-21-edit-peran.png` | 9.2 |
| ADM-22 | Peran berhasil ditambahkan | /admin/manajemen-hak-akses | `ADM-22-peran-bertambah.png` | 9.2 |
| ADM-23 | Akun nonaktif donisetiawan | /admin/manajemen-hak-akses | `ADM-23-akun-nonaktif.png` | 9.3 |
| ADM-24 | Jendela Buat Akun Pengurus | /admin/manajemen-hak-akses | `ADM-24-buat-akun-pengurus.png` | 9.4 |
| ADM-25 | Log Aktivitas dengan filter | /admin/log-aktivitas | `ADM-25-log-aktivitas.png` | 10.1 |
| ADM-26 | Jendela Hapus Log Aktivitas | /admin/log-aktivitas | `ADM-26-hapus-log.png` | 10.2 |

Total: **26 gambar**.
