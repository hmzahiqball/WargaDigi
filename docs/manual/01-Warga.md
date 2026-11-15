# Manual Book WargaDigi 21: Warga (Kepala Keluarga)

> **Untuk siapa:** Warga RW 21 yang memegang akun keluarga (Kepala Keluarga). Buku ini menjelaskan cara memakai semua menu warga: Dashboard, Profil Keluarga, Permohonan Surat, Iuran, Laporan Keuangan, Pusat Informasi, Agenda, dan Pojok UMKM.
>
> **Alamat web:** https://wargadigi21.com
>
> **Akun demo:** `budisantoso` (Pak Budi Santoso, RT 03) dan `budisampurno` (Pak Budi Sampurno, Ketua RT 03 yang juga Warga). Kata sandi keduanya `password`.
>
> **Baca dulu:** halaman publik, cara masuk, pilih peran, dan notifikasi dijelaskan di `00-Umum-Publik-dan-Login.md`.

<!-- AI-INSTRUKSI: Ubah file ini menjadi dokumen Word bergambar.
1. Judul "# ..." menjadi Judul Dokumen, "## Bab" menjadi Heading 1, "### Sub-bab" menjadi Heading 2.
2. Blok "> 👤 SIMULASI" menjadi kotak biru muda. Blok "✅ Hasilnya" menjadi kotak hijau muda. Blok "⚠️ Perhatian" menjadi kotak kuning. Blok "> 💡 Tips" menjadi kotak abu-abu. Blok "> ➡️ Cerita berlanjut" menjadi teks miring.
3. Setiap blok "> 📸 SCREENSHOT <KODE>" diganti gambar dengan nama file yang tertulis di baris "Nama file". Beri keterangan "Gambar <KODE>: <judul>" di bawah gambar. Jangan cetak isi daftar Akun/URL/Kondisi/Area/Sorot di dokumen akhir; itu hanya petunjuk pengambilan gambar. Bila ada dua nama file (desktop dan mobile), tempatkan keduanya berdampingan (desktop di kiri, HP di kanan).
4. Komentar HTML seperti ini tidak dicetak.
5. Tabel dipertahankan sebagai tabel Word. Daftar Isi dibuat otomatis dari Heading.
6. Nomor "bagian 1 dari 3" pada SIMULASI menunjukkan urutan cerita lintas peran; pertahankan. -->

---

## Daftar Isi

1. Kenalan Dulu
   - 1.1 Siapa itu Warga di aplikasi ini
   - 1.2 Peta menu Warga
   - 1.3 Data keluarga demo
2. Mulai Menggunakan
   - 2.1 Masuk
   - 2.2 Memilih peran (khusus akun dua peran)
   - 2.3 Mengenal Dashboard
3. Profil Keluarga
   - 3.1 Melihat data keluarga
   - 3.2 Mengajukan perubahan data KK
   - 3.3 Menambah anggota keluarga
   - 3.4 Melihat dan mengubah data anggota
   - 3.5 Memantau Riwayat Pengajuan
   - 3.6 Memindahkan pemegang akun
4. Permohonan Surat
   - 4.1 Jenis surat yang tersedia
   - 4.2 Mengajukan Surat
   - 4.3 Mengajukan surat untuk anggota keluarga lain
   - 4.4 Memantau status surat
   - 4.5 Surat selesai: unduh PDF dan riwayat
   - 4.6 Jika surat ditolak
5. Iuran Warga
   - 5.1 Iuran Bulanan
   - 5.2 Iuran Lainnya
6. Laporan Keuangan
7. Pusat Informasi
8. Agenda Warga dan Konfirmasi Kehadiran
9. Pojok UMKM
   - 9.1 Melihat produk dan toko
   - 9.2 Mendaftarkan usaha
   - 9.3 Mengelola usaha dan produk
   - 9.4 Status usaha dan mengajukan ulang
10. Keluar
11. Cerita Lengkap (Lintas Peran)
12. Masalah yang Sering Terjadi (FAQ)
13. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### 1.1 Siapa itu Warga di aplikasi ini

Peran **Warga** dipegang oleh **pemegang akun keluarga**, yang umumnya Kepala Keluarga. Satu Kartu Keluarga (KK) memiliki satu akun. Lewat akun ini Anda dapat mengurus keperluan seluruh keluarga: mengajukan surat atas nama sendiri atau anggota keluarga, membayar iuran, mengajukan perubahan data keluarga, dan mendaftarkan usaha.

Di kartu pilihan peran, Warga tampil dengan nama **Kepala Keluarga**.

Hal yang perlu diketahui sejak awal:

- **Data penduduk tidak bisa diubah langsung.** Semua penambahan atau perubahan data keluarga diajukan, diperiksa Ketua RT, lalu disetujui RW. Data baru berubah setelah RW menyetujui.
- **Surat melewati dua pemeriksa.** Ketua RT memeriksa lebih dulu, lalu RW mengesahkan. Setelah itu surat dapat diunduh sebagai PDF.
- **Pembayaran diperiksa bendahara.** Setelah Anda mengunggah bukti, bendahara memeriksa dan menandai lunas atau menolak dengan alasan.

### 1.2 Peta menu Warga

Menu berada di **menu samping** (sebelah kiri). Di HP, tekan tombol **☰** di pojok kiri atas bilah atas supaya menu muncul.

| No | Menu | Alamat | Untuk apa | Bab |
|---|---|---|---|---|
| 1 | **Dashboard** | `/warga/dashboard` | Ringkasan: status surat, berita, pengumuman, agenda, produk UMKM | 2.3 |
| 2 | **Profil Keluarga** | `/warga/keluarga` | Data KK dan anggota; ajukan perubahan; riwayat pengajuan | 3 |
| 3 | **Permohonan Surat** | `/warga/surat` | Ajukan surat pengantar, pantau status, unduh PDF | 4 |
| 4 | **Iuran Warga** (buka submenu) | - | Dua submenu di bawah | 5 |
| 4a | **Iuran Bulanan** | `/warga/iuran` | Kartu 12 bulan iuran wajib RT dan ajukan bayar | 5.1 |
| 4b | **Iuran Lainnya** | `/warga/tagihan` | Tagihan insidental (di luar iuran bulanan) | 5.2 |
| 5 | **Laporan Keuangan** | `/laporan-keuangan` | Ringkasan uang masuk dan keluar yang sudah terbit | 6 |
| 6 | **Pusat Informasi** | `/warga/berita` | Berita, pengumuman, UMKM News, galeri dokumentasi | 7 |
| 7 | **Agenda Warga** | `/warga/agenda` | Kalender kegiatan dan konfirmasi hadir | 8 |
| 8 | **Pojok UMKM** | `/galeri` | Etalase usaha warga; daftar dan kelola usaha | 9 |
| 9 | **Keluar** | - | Mengakhiri sesi | 10 |

Untuk akun dua peran, ada juga tombol **Ganti Peran** di bagian bawah menu (lihat `00-Umum-Publik-dan-Login.md`, bab 3.3).

> 📸 **SCREENSHOT W-01: Peta menu Warga**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/dashboard
> - **Kondisi:** menu **Iuran Warga** dibuka sehingga submenu **Iuran Bulanan** dan **Iuran Lainnya** terlihat
> - **Area:** menu samping penuh dari **Dashboard** sampai **Keluar**
> - **Sorot:** nomor merah 1 sampai 9 di samping setiap menu sesuai tabel 1.2 (4a dan 4b pada dua submenu)
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844 (menu terbuka setelah tombol ☰ ditekan)
> - **Nama file:** `W-01-peta-menu-warga.png`, `W-01m-peta-menu-warga.png`

### 1.3 Data keluarga demo

Buku ini memakai data demo berikut.

**Keluarga Pak Budi Santoso (akun `budisantoso`)**

| Hal | Isi |
|---|---|
| Nomor KK | 3204111122220001 |
| Alamat | Jl. Merdeka Barat No. 45, RT 03 / RW 21, Tanimulya |
| Kepala Keluarga | Budi Santoso |
| Istri | Siti Rahma |
| Anak | Andi Saputra |
| Usaha | Warung Nasi Ibu Ani (disetujui), Fashion Muslimah Aini (disetujui), Koperasi Warga Tanimulya (ditolak) |
| Surat | 9 contoh surat berstatus Selesai, satu untuk setiap jenis |
| Iuran | Iuran Bulanan Warga RT 03 Rp60.000 per bulan; tagihan "Perbaikan Toa Masjid", "Iuran Perbaikan Got", "Iuran Kematian" |

**Keluarga Pak Budi Sampurno (akun `budisampurno`)**

| Hal | Isi |
|---|---|
| Peran | Ketua RT 03 dan Warga |
| Alamat | Gg. Kenanga No. 45, RT 03 / RW 21, Tanimulya |
| Anggota | Budi Sampurno (Kepala Keluarga), Siti Aminah (Istri), Rizky Pratama Sampurno (Anak) |
| Usaha | Kriya Kayu Jati Tanimulya (disetujui), Jasa Service Elektronik Pak Budi (menunggu persetujuan) |

---

## 2. Mulai Menggunakan

### 2.1 Masuk

Buka https://wargadigi21.com, tekan **Masuk**, lalu isi **Username atau Nomor WhatsApp** dan **Password**. Panduan lengkap beserta arti setiap pesan kesalahan ada di `00-Umum-Publik-dan-Login.md`, bab 3.1.

> 👤 **SIMULASI W-A: Pak Budi Santoso masuk untuk pertama kali**
>
> Pak **Budi Santoso** baru menerima kabar bahwa surat bisa diajukan lewat aplikasi. Ia masuk dari HP.
>
> - **Masuk sebagai:** `budisantoso`, kata sandi `password`
> - **Hasil:** langsung tiba di **Dashboard Warga**

**1. Masuk.**
Isi `budisantoso` dan `password`, lalu tekan **Masuk**.

✅ **Hasilnya:** Anda tiba di **Dashboard Warga** (`/warga/dashboard`) dengan menu samping khusus Warga.

> 📸 **SCREENSHOT W-02: Dashboard Warga setelah masuk**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/dashboard
> - **Kondisi:** baru masuk; belum ada pengajuan surat yang sedang diproses
> - **Area:** satu layar penuh (bilah atas, menu samping, dan bagian atas Dashboard)
> - **Sorot:** kotak merah pada tulisan **Selamat Datang**, empat tombol cepat, dan lonceng notifikasi
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-02-dashboard-setelah-masuk.png`, `W-02m-dashboard-setelah-masuk.png`

### 2.2 Memilih peran (khusus akun dua peran)

Akun `budisampurno` memiliki dua peran: Ketua RT 03 dan Warga. Setelah masuk, pilih kartu **Kepala Keluarga** untuk memakai menu Warga. Untuk berpindah tanpa keluar, gunakan tombol **Ganti Peran** di bagian bawah menu samping. Rincian ada di `00-Umum-Publik-dan-Login.md`, bab 3.2 dan 3.3.

> 👤 **SIMULASI W-B: Pak Budi Sampurno memakai peran Warga**
>
> Pak **Budi Sampurno** adalah Ketua RT 03, tetapi hari ini ia ingin mengurus usaha jasa servisnya sebagai warga.
>
> - **Masuk sebagai:** `budisampurno`, kata sandi `password`
> - **Peran yang dipilih:** **Kepala Keluarga**
> - **Yang dilihat:** pengumuman di Dashboard bahwa pendaftaran usahanya masih ditinjau RW

**1. Masuk, lalu pilih peran.**
Pada halaman **Masuk Sebagai**, klik kartu **Kepala Keluarga**.

✅ **Hasilnya:** Dashboard Warga terbuka. Karena usaha "Jasa Service Elektronik Pak Budi" masih menunggu persetujuan, muncul kotak kuning **Pendaftaran Usaha Anda Sedang Ditinjau RW**.

> 📸 **SCREENSHOT W-03: Dashboard Warga milik akun dua peran**
> - **Akun:** budisampurno (peran aktif Warga / Kepala Keluarga)
> - **URL:** /warga/dashboard
> - **Kondisi:** setelah memilih kartu **Kepala Keluarga**; kotak **Pendaftaran Usaha Anda Sedang Ditinjau RW** tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kotak kuning peringatan dan tautan **Cek di Kelola UMKM**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-03-dashboard-akun-dua-peran.png`, `W-03m-dashboard-akun-dua-peran.png`

> ➡️ **Cerita berlanjut:** Persetujuan usaha dilakukan Admin RW. Lihat `05-Admin-RW.md`, cerita C-05. Pemantauan status usaha ada di bab 9.4 buku ini.

### 2.3 Mengenal Dashboard

Dashboard adalah halaman pertama setelah masuk. Isinya, dari atas ke bawah:

- **Selamat Datang** dengan nama Anda, keterangan "Kepala Keluarga - RW 21 Desa Tanimulya", dan penanda **STATUS: AKTIF**.
- **Kotak pemberitahuan** yang hanya tampil bila perlu:
  - kuning: **Pendaftaran Usaha Anda Sedang Ditinjau RW** (tautan **Cek di Kelola UMKM**),
  - **Surat Keterangan Menunggu Verifikasi RT** (tautan **Cek Status Surat**),
  - **Surat Keterangan Menunggu Pengesahan RW** (tautan **Cek Status Surat**),
  - merah: **Surat Keterangan Ditolak** (tautan **Lihat Detail**).
- **Empat tombol cepat:** **Ajukan Surat Keterangan**, **Cek Status Surat Keterangan**, **Tagihan & Iuran**, **Kelola Produk Saya (UMKM)**.
- **Data Anggota Keluarga:** nama dan peran Anda ("Kepala Keluarga", "Status: Terverifikasi"), **Daftar Tanggungan**, dan tautan **Kelola Data** serta **Kelola Keluarga**.
- **Update Berita Terkini** (lima berita terbaru) dengan tautan **Semua Berita**.
- **Sorotan Pengumuman** dalam bentuk kartu geser. Pengumuman prioritas bertanda **PENTING**. Tautan **Lihat Semua**.
- **Kalender Agenda** (tanggal yang punya agenda diberi tanda) dan **Agenda Terdekat (7 Hari Kedepan)**.
- **Pojok UMKM** berisi produk terbaru dari warga, tautan **Lihat Semua**.

Konten yang tampil adalah konten RW dan konten RT Anda. Sebagai warga RT 03, Anda melihat misalnya berita "Penghijauan Taman RT 03" dan pengumuman "Pendataan Warga Baru RT 03" di samping konten RW seperti "Kerja Bakti Bersih Lingkungan Sukses Digelar" dan "Pemadaman Listrik Terjadwal".

> 📸 **SCREENSHOT W-04: Dashboard bagian bawah (berita, pengumuman, kalender, UMKM)**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/dashboard
> - **Kondisi:** halaman digulir sampai **Update Berita Terkini**, **Sorotan Pengumuman**, dan **Kalender Agenda** terlihat
> - **Area:** bagian tengah Dashboard (satu atau dua layar)
> - **Sorot:** kotak merah pada **Semua Berita**, penanda **PENTING** pada "Pemadaman Listrik Terjadwal", dan **Agenda Terdekat (7 Hari Kedepan)**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-04-dashboard-bagian-bawah.png`, `W-04m-dashboard-bagian-bawah.png`

> 💡 **Tips:** Klik berita di Dashboard untuk langsung membukanya di Pusat Informasi. Notifikasi lainnya ada di lonceng (lihat `00-Umum-Publik-dan-Login.md`, bab 4.2).

---

## 3. Profil Keluarga

Menu **Profil Keluarga** (`/warga/keluarga`) menampilkan data Kartu Keluarga dan anggota. Di bagian atas ada pemberitahuan **Akses Terbatas**: data penduduk adalah rekaman resmi, jadi penambahan dan perubahan harus diajukan dan disetujui RT dan RW lebih dulu.

Ikon roda gigi di bilah atas juga membuka halaman ini.

### 3.1 Melihat data keluarga

Isi halaman:

- **Profil Kartu Keluarga:** **Nomor KK**, **Kepala Keluarga**, **Alamat (sesuai KK)**, **Alamat Domisili**, **Status KK** (Warga Tetap atau Bukan Warga Tetap), dan **Pemilik Rumah** (Ya atau Bukan).
- **Akun Keluarga:** **Username**, **Pemegang Akun**, dan **Status Akun** (Aktif). Tombol **Pindahkan Akun ke Anggota Lain** ada di bawahnya.
- **Anggota Keluarga (n jiwa):** tabel dengan kolom **NAMA**, **NIK**, **L/P**, **HUBUNGAN**, dan **AKSI** (tombol **Detail** dan **Edit**). Kepala Keluarga selalu di baris pertama.
- **Riwayat Pengajuan:** lima pengajuan terbaru (lihat 3.5).
- Tombol hijau **Ajukan Perubahan KK** dan **Tambah Anggota** di kanan atas.

Tombol-tombol ubah hanya tampil untuk **pemegang akun keluarga**.

> 👤 **SIMULASI W-C: Pak Budi Santoso memeriksa data keluarganya**
>
> Pak **Budi Santoso** ingin memastikan data KK-nya benar sebelum mengajukan surat.
>
> - **Masuk sebagai:** `budisantoso`
> - **Yang diperiksa:** nomor KK 3204111122220001 dan tiga anggota: Budi Santoso, Siti Rahma, Andi Saputra

**1. Buka Profil Keluarga.**
Klik **Profil Keluarga** di menu samping.

**2. Periksa kartu dan tabel anggota.**
Lihat **Profil Kartu Keluarga** dan tabel **Anggota Keluarga (3 jiwa)**.

> 📸 **SCREENSHOT W-05: Halaman Profil Keluarga**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/keluarga
> - **Kondisi:** belum ada pengajuan perubahan; **Riwayat Pengajuan** kosong ("Belum ada riwayat pengajuan.")
> - **Area:** satu layar penuh, boleh dua potongan (atas dan tabel anggota)
> - **Sorot:** kotak merah pada **Ajukan Perubahan KK**, **Tambah Anggota**, dan kolom **AKSI** pada tabel anggota
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-05-profil-keluarga.png`, `W-05m-profil-keluarga.png`

✅ **Hasilnya:** Semua data keluarga terlihat. Bila ada yang salah, ajukan perubahan (3.2 untuk data KK, 3.4 untuk data anggota).

⚠️ **Perhatian**

- **Muncul pesan "Data keluarga tidak ditemukan."** Akun Anda belum tertaut ke data penduduk. Hubungi Ketua RT.
- **Tombol Ajukan, Tambah, Detail, dan Edit tidak muncul.** Akun Anda bukan pemegang akun keluarga. Pemegang akun saat ini ditampilkan pada kotak **Akun Keluarga**.

---

### 3.2 Mengajukan perubahan data KK

Gunakan fitur ini bila alamat, nomor WhatsApp keluarga, status, atau data wilayah pada KK berubah. Nomor KK dan nama Kepala Keluarga tidak dapat diubah di sini.

> 👤 **SIMULASI W-D: Pak Budi Santoso memperbarui alamat domisili**
>
> Pak **Budi Santoso** menambahkan nomor rumah baru pada alamat domisilinya.
>
> - **Masuk sebagai:** `budisantoso`
> - **Isian yang diubah:** **Alamat Domisili**, misalnya `Jl. Merdeka Barat No. 45A, RT 03 / RW 21, Tanimulya`
> - **Selanjutnya:** Ketua RT memeriksa, lalu RW menyetujui. Lihat `02-Ketua-RT.md` (persetujuan Pengajuan Data) dan `05-Admin-RW.md`.

**1. Buka formulir.**
Di **Profil Keluarga**, tekan **Ajukan Perubahan KK**.

**2. Ubah isian yang perlu.**
Kolom yang tersedia: **Alamat (sesuai KK)** (wajib), **Alamat Domisili**, **No. WhatsApp Keluarga**, **Status KK** (Bukan Warga Tetap atau Warga Tetap), kotak centang **Pemilik Rumah**, **Kelurahan**, **Kecamatan**, **Kabupaten**, **Provinsi**, **Kode Pos**, dan **RW**.

> 📸 **SCREENSHOT W-06: Formulir Ajukan Perubahan Data KK**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/keluarga/edit
> - **Kondisi:** formulir terbuka; kolom **Alamat Domisili** sudah diubah
> - **Area:** satu layar penuh dari judul **Ajukan Perubahan Data KK** sampai tombol di bawah
> - **Sorot:** kotak merah pada kolom **Alamat Domisili** dan tombol **Ajukan Perubahan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-06-ajukan-perubahan-kk.png`

**3. Kirim.**
Tekan **Ajukan Perubahan**. Tombol **Batal** membatalkan tanpa menyimpan.

✅ **Hasilnya:**

- Muncul pesan "Pengajuan perubahan data KK telah terkirim dan sedang diproses oleh Admin RT."
- Pengajuan masuk ke **Riwayat Pengajuan** dengan status **Menunggu RT**. Data KK berubah setelah RW menyetujui.

⚠️ **Perhatian**

- **"Masih terdapat pengajuan perubahan KK yang sedang diproses."** Tunggu pengajuan sebelumnya selesai.
- **"Nomor WA hanya boleh angka, spasi, tanda + atau -."** Perbaiki penulisan nomor WhatsApp.
- **"Hanya pemegang akun keluarga yang dapat mengajukan perubahan KK."** Gunakan akun pemegang akun keluarga.
- **Kolom Alamat (sesuai KK) kosong.** Kolom ini wajib; isi dulu.

> 💡 **Tips:** Nomor WhatsApp keluarga dipakai sebagai alternatif username saat masuk. Pastikan nomor yang Anda isi benar.

---

### 3.3 Menambah anggota keluarga

Gunakan fitur ini untuk anggota baru, misalnya bayi yang baru lahir. Pengajuan ditinjau Ketua RT lalu disetujui RW; data penduduk baru dibuat setelah RW menyetujui.

> 👤 **SIMULASI W-E: Pak Budi Santoso menambahkan anggota**
>
> Keluarga Pak **Budi Santoso** dikaruniai anak baru. Ia mengisi formulir dengan KK sebagai bukti.
>
> - **Masuk sebagai:** `budisantoso`
> - **Nama:** Muhammad Al Fatih Santoso, hubungan **Anak**, jenis kelamin **Laki-laki**
> - **Catatan penyusun gambar:** NIK dan KK pada gambar hanyalah contoh; formulir cukup diisi sampai tombol akhir tanpa dikirim, agar cerita C-10 tetap bersih

**1. Buka formulir.**
Di **Profil Keluarga**, tekan **Tambah Anggota**. Judul halaman: **Tambah Anggota Keluarga**.

**2. Unggah KTP bila ada (opsional).**
Ada tiga tombol: **Upload KTP**, **Baca KTP** (mengisi isian otomatis dari foto), dan **Ambil KTP dari Kamera**. Periksa kembali hasil bacaan terutama NIK, karena angka mudah salah baca.

**3. Unggah scan KK (wajib).**
Pada **Unggah Scan Kartu Keluarga (KK)** pilih berkas PDF, JPG, atau PNG (maksimal 10 MB). Berkas ini wajib saat menambah anggota.

**4. Lengkapi data.**
Isian bertanda bintang wajib: **NIK** (16 digit), **Nama Lengkap**, **Jenis Kelamin**, **Tempat Lahir**, **Tanggal Lahir**, **Agama**, **Pekerjaan**, **Status Kawin**, dan **Hubungan Keluarga** (pilihan: Istri, Anak, Orang Tua, Mertua, Famili Lain, Lainnya). Isian lain: **Nomor WA**, **Alamat (sesuai KTP)**, **Alamat Domisili**, **Kewarganegaraan** (WNI atau WNA), **Pendidikan**, dan **Status Warga**. Kotak centang **Alamat sama dengan KK** dan **Alamat domisili sama dengan KK** mengisi alamat otomatis.

> 📸 **SCREENSHOT W-07: Formulir Tambah Anggota Keluarga**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/keluarga/anggota/create
> - **Kondisi:** formulir terbuka dan sebagian terisi (nama, hubungan **Anak**, jenis kelamin)
> - **Area:** satu layar penuh, boleh digulir dua potongan
> - **Sorot:** kotak merah pada tombol **Upload KTP**, **Baca KTP**, kolom **Unggah Scan Kartu Keluarga (KK)**, dan tombol **Ajukan Tambah Anggota**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-07-tambah-anggota.png`, `W-07m-tambah-anggota.png`

**5. Kirim.**
Tekan **Ajukan Tambah Anggota**.

✅ **Hasilnya:**

- Muncul pesan "Permintaan perubahan data Anda telah kami terima dan sedang diproses oleh Admin RT."
- Pengajuan tampil di **Riwayat Pengajuan** berjenis **Tambah Anggota** dengan status **Menunggu RT**.

⚠️ **Perhatian**

- **"Masih terdapat pengajuan penambahan anggota yang sedang diproses."** Hanya satu pengajuan tambah anggota boleh berjalan. Tunggu sampai selesai.
- **Pesan kesalahan pada NIK.** NIK harus tepat 16 digit dan belum terdaftar di penduduk lain. Periksa angka.
- **Kolom KK kosong.** Scan KK wajib saat menambah anggota.
- **"Hanya pemegang akun keluarga yang dapat menambah anggota."** Gunakan akun pemegang akun.

---

### 3.4 Melihat dan mengubah data anggota

Di tabel **Anggota Keluarga**, tombol **Detail** membuka halaman **Detail Penduduk** (identitas lengkap, nomor KK, alamat, dan foto KTP bila ada). Tombol **Ajukan Perubahan Data** di halaman itu, atau tombol **Edit** di tabel, membuka formulir **Ajukan Perubahan Data Penduduk**. Perubahan diproses Admin RT lalu RW; data berubah setelah RW menyetujui.

> 👤 **SIMULASI C-10 (bagian 1 dari 3): Mengajukan perubahan data pekerjaan istri**
>
> Siti Rahma, istri Pak **Budi Santoso**, kini bekerja sebagai guru honorer. Pak Budi mengajukan perubahan data pekerjaan.
>
> - **Masuk sebagai:** `budisantoso`, kata sandi `password`
> - **Anggota:** Siti Rahma (Istri)
> - **Isian yang diubah:** **Pekerjaan** dari `Ibu Rumah Tangga` menjadi `Guru Honorer`
> - **Selanjutnya:** Ketua RT 03 memeriksa. Lihat `02-Ketua-RT.md`, cerita C-10 bagian 2. Lalu RW menyetujui. Lihat `05-Admin-RW.md`, cerita C-10 bagian 3.

**1. Buka detail anggota.**
Di tabel **Anggota Keluarga**, pada baris **Siti Rahma**, tekan **Detail**.

> 📸 **SCREENSHOT W-08: Detail Penduduk**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/keluarga/anggota/ lalu nomor anggota (Siti Rahma)
> - **Kondisi:** halaman detail Siti Rahma terbuka
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Ajukan Perubahan Data** dan kolom **Pekerjaan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-08-detail-penduduk.png`

**2. Buka formulir perubahan.**
Tekan **Ajukan Perubahan Data**.

**3. Ubah pekerjaan.**
Pada kolom **Pekerjaan**, ketik `Guru Honorer`. Bila Anda mengubah **NIK** atau **Nama Lengkap**, unggah juga scan KK pada **Unggah Scan Kartu Keluarga (KK)**; untuk perubahan lain, KK tidak wajib.

> 📸 **SCREENSHOT W-09: Formulir Ajukan Perubahan Data Penduduk**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/keluarga/anggota/ lalu nomor anggota, lalu /edit
> - **Kondisi:** kolom **Pekerjaan** sudah diubah menjadi `Guru Honorer`
> - **Area:** satu layar penuh dari judul sampai tombol di bawah
> - **Sorot:** kotak merah pada kolom **Pekerjaan** dan tombol **Ajukan Perubahan**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-09-ajukan-perubahan-anggota.png`, `W-09m-ajukan-perubahan-anggota.png`

**4. Kirim.**
Tekan **Ajukan Perubahan**.

✅ **Hasilnya:**

- Muncul pesan "Permintaan perubahan data Anda telah kami terima dan sedang diproses oleh Admin RT."
- Di **Riwayat Pengajuan** muncul baris **Perubahan Data Anggota** untuk Siti Rahma berstatus **Menunggu RT**. Lanjutan memantau ada di 3.5.

⚠️ **Perhatian**

- **"Masih terdapat pengajuan perubahan anggota yang sedang diproses."** Anggota itu sudah punya pengajuan yang berjalan. Tunggu selesai.
- **"Dokumen KK wajib diunggah jika NIK atau nama diubah."** Unggah scan KK, lalu kirim lagi.
- **"Hanya pemegang akun keluarga yang dapat mengubah data anggota."** Gunakan akun pemegang akun.
- **"Anggota ini bukan bagian dari keluarga Anda."** Anda mencoba membuka anggota keluarga lain. Kembali ke tabel anggota.

> ➡️ **Cerita berlanjut:** Ketua RT 03 meninjau pengajuan ini. Lihat `02-Ketua-RT.md`, cerita C-10 bagian 2.

---

### 3.5 Memantau Riwayat Pengajuan

Tabel **Riwayat Pengajuan** di Profil Keluarga menampilkan lima pengajuan terbaru. Bila lebih banyak, tombol **Lihat Semua (n)** membuka halaman **Riwayat Pengajuan** ("Seluruh pengajuan perubahan data keluarga Anda.") dengan tombol **Kembali**.

Kolom tabel: **TANGGAL**, **JENIS**, **STATUS**, **KETERANGAN**, dan **AKSI** (tombol **Detail**).

| Jenis | Artinya |
|---|---|
| **Tambah Anggota** | Pengajuan anggota baru |
| **Perubahan Data Anggota** | Pengajuan ubah data satu anggota |
| **Perubahan Data KK** | Pengajuan ubah identitas KK |
| **Pindah Pemegang Akun** | Pengajuan pindah hak pemegang akun |

| Status | Artinya | Siapa yang bertindak |
|---|---|---|
| 🟡 **Menunggu RT** | Pengajuan sudah terkirim, menunggu Ketua RT | Ketua RT |
| 🔵 **Menunggu RW** | Sudah disetujui RT, menunggu RW | Pengurus RW |
| 🟢 **Disetujui** | Data sudah berubah | - |
| 🔴 **Ditolak** | Ditolak. Alasan tampil di kolom **KETERANGAN** | Anda (ajukan lagi bila perlu) |

Tombol **Detail** membuka jendela dengan dua bagian:

- **Perubahan Data:** tabel **Field**, **Nilai Lama**, dan **Nilai Baru**.
- **Riwayat Proses:** garis waktu **Diajukan oleh warga**, **Persetujuan Ketua RT**, **Persetujuan RW**, dan hasil akhir (disetujui atau **Ditolak**), lengkap dengan waktu, nama pemroses, dan catatan.

> 📸 **SCREENSHOT W-10: Riwayat Pengajuan dan jendela Detail**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/keluarga
> - **Kondisi:** pengajuan "Perubahan Data Anggota" Siti Rahma berstatus **Menunggu RT**; tombol **Detail** pada baris itu sudah ditekan sehingga jendela terbuka
> - **Area:** jendela **Detail** (tabel **Nilai Lama** dan **Nilai Baru** serta **Riwayat Proses**)
> - **Sorot:** kotak merah pada kolom **Nilai Baru** (`Guru Honorer`) dan langkah **Persetujuan Ketua RT**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-10-riwayat-pengajuan-detail.png`, `W-10m-riwayat-pengajuan-detail.png`

✅ **Hasilnya:** Anda tahu posisi pengajuan. Saat disetujui atau ditolak, Anda menerima notifikasi **Perubahan Data Disetujui** atau **Perubahan Data Ditolak** di lonceng.

---

### 3.6 Memindahkan pemegang akun

Gunakan fitur ini bila hak mengelola akun keluarga akan dialihkan ke anggota keluarga lain (misalnya Kepala Keluarga berhalangan). Tombolnya **Pindahkan Akun ke Anggota Lain** ada pada kotak **Akun Keluarga**.

Halaman **Pindahkan Pemegang Akun** ("Alihkan hak pemegang akun keluarga kepada anggota keluarga lain.") menampilkan peringatan **Perhatian**:

- **Akun satu peran:** setelah Ketua RT menyetujui, akun Anda dinonaktifkan dan anggota yang dipilih menerima link aktivasi lewat Ketua RT untuk membuat username dan password sendiri.
- **Akun dua peran (seperti `budisampurno`):** hanya akses Warga yang dialihkan. Peran lain, misalnya Ketua RT, tetap aktif dan Anda masih bisa masuk dengan username dan password yang sama.

Pilih anggota pada **Pilih Anggota Keluarga Tujuan** (awalnya `-- Pilih Anggota --`), lalu tekan **Ajukan Pemindahan Akun**.

> 👤 **SIMULASI W-F: Melihat formulir pindah akun**
>
> Pak **Budi Santoso** hanya ingin tahu cara memindahkan akun bila kelak diperlukan.
>
> - **Masuk sebagai:** `budisantoso`
> - **Anggota tujuan:** Siti Rahma (Istri)
> - **Catatan penyusun gambar:** cukup buka formulir dan pilih anggota; **jangan menekan Ajukan Pemindahan Akun** agar akun demo tidak berubah.

**1. Buka formulir.**
Di **Profil Keluarga**, tekan **Pindahkan Akun ke Anggota Lain**.

> 📸 **SCREENSHOT W-11: Formulir Pindahkan Pemegang Akun**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/keluarga/pindah-akun
> - **Kondisi:** anggota **Siti Rahma (Istri)** sudah dipilih pada daftar tujuan
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kotak **Perhatian** dan tombol **Ajukan Pemindahan Akun**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-11-pindah-pemegang-akun.png`

✅ **Hasilnya (bila dikirim):** Muncul pesan "Pengajuan pemindahan akun telah dikirim dan sedang diproses oleh Admin RT." dan pengajuan berjenis **Pindah Pemegang Akun** tampil di riwayat.

⚠️ **Perhatian**

- **"Masih terdapat pengajuan pemindahan akun yang sedang diproses."** Tunggu sampai selesai.
- **"Pilih anggota keluarga tujuan pemindahan akun."** Anda belum memilih anggota.
- **"Belum ada anggota keluarga lain yang dapat menerima pemindahan akun."** Tambahkan anggota keluarga dulu (3.3).

---

## 4. Permohonan Surat

Lewat menu **Permohonan Surat**, Anda bisa meminta surat pengantar RT/RW dari rumah. Anda tidak perlu datang ke rumah Ketua RT. Surat diperiksa dulu oleh **Ketua RT**, lalu disahkan oleh **RW**. Setelah itu surat bisa langsung diunduh dalam bentuk PDF.

### 4.1 Jenis surat yang tersedia

Ada 9 jenis surat. Beberapa jenis memiliki **Isian Tambahan Surat** khusus.

| Jenis surat | Gunanya | Isian tambahan |
|---|---|---|
| **Kartu Keluarga** | Pengantar pembuatan baru, perubahan, atau penambahan anggota KK | Alasan Pengajuan (boleh kosong): Pembuatan baru, Perubahan data, Penggantian karena hilang/rusak |
| **Kartu Tanda Penduduk** | Pengantar pembuatan baru atau perubahan data KTP elektronik | Alasan Pengajuan (boleh kosong): Pembuatan baru, Perpanjangan, Perubahan data, Penggantian karena hilang/rusak |
| **Surat Keterangan Ahli Waris** | Susunan ahli waris untuk warisan atau perbankan | Nama Pewaris (yang meninggal), Tanggal Wafat, Daftar Ahli Waris (satu nama per baris) |
| **Surat Kelahiran** | Pengantar pelaporan kelahiran untuk akta kelahiran | Nama Bayi, Jenis Kelamin Bayi, Tanggal Lahir Bayi, Jam Lahir, Tempat Lahir |
| **Surat Kematian** | Pengantar pelaporan kematian untuk akta kematian | Nama yang Meninggal, Tanggal Meninggal, Jam Meninggal, Tempat Meninggal, Dimakamkan di, Penyebab (boleh kosong) |
| **Surat Keterangan Domisili** | Keterangan tempat tinggal untuk bank, administrasi, atau usaha | Lama Tinggal (boleh kosong) |
| **Surat Keterangan Miskin / Tidak Mampu** | Bantuan sosial, sekolah, atau kesehatan | Perkiraan Penghasilan per Bulan (boleh kosong) |
| **Surat Keterangan Serbaguna** | Keterangan umum untuk keperluan lain | (tidak ada) |
| **Surat Keterangan Kelakuan Baik / Catatan Kepolisian** | Pengantar SKCK atau melamar kerja | (tidak ada) |

Pada surat Ahli Waris, Kelahiran, dan Kematian, pemohon berperan sebagai **pelapor** peristiwa, bukan sebagai pihak yang disebut dalam surat.

Bagian isian yang kosong akan dicetak berupa titik-titik pada surat untuk diisi pengurus.

### 4.2 Mengajukan Surat

> 👤 **SIMULASI C-01 (bagian 1 dari 3): Surat Keterangan Domisili**
>
> Pak **Budi Santoso**, warga RT 03, mau membuka rekening tabungan di bank. Petugas bank meminta **Surat Keterangan Domisili** dari RT/RW. Malam itu Pak Budi mengajukannya dari HP sambil santai di rumah.
>
> - **Masuk sebagai:** `budisantoso`, kata sandi `password`
> - **Jenis surat:** Surat Keterangan Domisili
> - **Pemohon:** Budi Santoso (dirinya sendiri)
> - **Keperluan:** Pembukaan rekening tabungan di bank
> - **Lama tinggal:** 8 tahun
> - **Selanjutnya:** Ketua RT 03 (Pak Budi Sampurno) memeriksa pengajuan ini. Lihat `02-Ketua-RT.md`, cerita C-01 bagian 2.

**1. Buka menu Permohonan Surat.**
Di menu samping, klik **Permohonan Surat**. Di HP, tekan dulu tombol **☰** di pojok kiri atas supaya menunya muncul.

> 📸 **SCREENSHOT W-12: Halaman Layanan Permohonan Surat**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat
> - **Kondisi:** halaman baru dibuka dan formulir belum tampil; kotak **Sedang Diproses** berisi "Tidak ada surat yang sedang diproses."
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada menu **Permohonan Surat** di samping, dan pada kotak pilihan **JENIS SURAT**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-12-halaman-permohonan-surat.png`, `W-12m-halaman-permohonan-surat.png`

**2. Pilih jenis surat.**
Pada kotak **JENIS SURAT**, klik lalu pilih **Surat Keterangan Domisili**. Setelah dipilih, **Formulir Permohonan** muncul di bawahnya.

> 💡 **Tips:** Kalau Anda datang dari halaman **Layanan Mandiri** di beranda dan sudah memilih jenis surat di sana, jenis surat itu otomatis sudah terpilih.

**3. Pilih siapa yang mengajukan.**
Pada bagian **Pilih Pemohon**, biarkan **Budi Santoso** (dengan keterangan hubungan **Kepala Keluarga**) tetap terpilih. Kalau surat ini untuk anggota keluarga lain (misalnya istri atau anak), pilih namanya di sini (lihat 4.3).

**4. Periksa data diri.**
Bagian **Data Diri Pemohon** terisi otomatis dari data keluarga: **Nama Lengkap**, **NIK**, **Tempat Lahir**, **Tanggal Lahir**, **Jenis Kelamin**, **Pekerjaan**, **Agama**, **Status Perkawinan**, dan **Alamat Domisili**. Data ini **tidak bisa diubah di sini**. Kalau ada yang salah, perbaiki dulu lewat menu **Profil Keluarga** (lihat Bab 3).

> 📸 **SCREENSHOT W-13: Formulir: pemohon dan data diri**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat, lalu pilih jenis "Surat Keterangan Domisili"
> - **Kondisi:** formulir sudah tampil dan pemohon "Budi Santoso" (Kepala Keluarga) terpilih
> - **Area:** bagian **Pilih Pemohon** dan **Data Diri Pemohon** saja
> - **Sorot:** kotak merah pada pilihan pemohon. Beri catatan "terisi otomatis" di samping kolom data diri
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-13-formulir-data-diri.png`

**5. Tulis keperluan surat.**
Pada **Jelaskan Tujuan Pengajuan Surat (Opsional)**, ketik: *Pembukaan rekening tabungan di bank*. Kalimat ini akan ikut tercetak di surat.

**6. Lengkapi isian tambahan.**
Bagian **Isian Tambahan Surat** berbeda untuk setiap jenis surat (lihat tabel 4.1). Untuk Surat Keterangan Domisili ada kolom **Lama Tinggal**. Isi dengan *8 tahun*.
Kolom ini boleh dikosongkan. Kalau kosong, bagian itu dicetak titik-titik dan nanti diisi pengurus.

**7. Periksa dokumen KTP dan KK.**
Bagian **Dokumen Pendukung (KTP & KK)** otomatis menampilkan foto KTP dan KK yang sudah tersimpan. Kalau belum ada, tekan tombol **Unggah KTP** atau **Unggah KK**, lalu pilih foto dari HP atau komputer. Format yang diterima JPG, PNG, atau PDF, maksimal 5 MB per berkas. Mengunggah dokumen bersifat pilihan; pengajuan tetap bisa dikirim tanpa unggahan.

> 📸 **SCREENSHOT W-14: Keperluan, isian tambahan, dan dokumen**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat (lanjutan formulir)
> - **Kondisi:** keperluan "Pembukaan rekening tabungan di bank" dan Lama Tinggal "8 tahun" sudah diisi; kotak **Foto KTP** dan **Kartu Keluarga** menampilkan "Belum ada foto ... tersimpan" dengan tombol **Unggah KTP** dan **Unggah KK**
> - **Area:** bagian **Keperluan Pengajuan**, **Isian Tambahan Surat**, dan **Dokumen Pendukung**
> - **Sorot:** kotak merah pada kolom keperluan, kolom Lama Tinggal, dan tombol unggah
> - **Perangkat:** 📱 mobile 390×844 (gulir ke bagian ini)
> - **Nama file:** `W-14m-keperluan-dan-dokumen.png`

**8. Centang pernyataan, lalu kirim.**
Centang kotak **"Saya menyatakan bahwa data yang saya isikan di atas adalah benar…"**, lalu tekan tombol hijau **Kirim Pengajuan**. Tombol **Batal** membersihkan formulir.

> 📸 **SCREENSHOT W-15: Pernyataan dan tombol kirim**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat (bagian paling bawah formulir)
> - **Kondisi:** kotak pernyataan sudah dicentang
> - **Area:** kotak pernyataan sampai tombol **Batal** dan **Kirim Pengajuan**
> - **Sorot:** kotak merah pada centang pernyataan dan tombol **Kirim Pengajuan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-15-kirim-pengajuan.png`

### ✅ Hasilnya

- Muncul jendela **Berhasil!** dengan pesan **"Pengajuan surat berhasil dikirim! Silakan tunggu proses verifikasi oleh pengurus RT."**
- Di kolom kanan, pada kotak **Sedang Diproses**, muncul surat baru dengan label kuning **Menunggu RT**.
- Ketua RT 03 menerima notifikasi bahwa ada pengajuan surat baru.

> 📸 **SCREENSHOT W-16: Pengajuan berhasil, status Menunggu RT**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat (sesaat setelah menekan Kirim Pengajuan)
> - **Kondisi:** jendela **Berhasil!** tampil, dan pengajuan "Surat Keterangan Domisili" ada di kotak **Sedang Diproses**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses dan label **Menunggu RT**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-16-pengajuan-berhasil.png`, `W-16m-pengajuan-berhasil.png`

### Arti status surat

| Label di layar | Artinya | Siapa yang bertindak |
|---|---|---|
| 🟡 **Menunggu RT** | Surat sudah terkirim dan sedang diperiksa Ketua RT | Ketua RT |
| 🔵 **Menunggu RW** | Sudah disetujui dan ditandatangani Ketua RT, menunggu pengesahan RW | Pengurus RW |
| 🟢 **Selesai** | Surat sah dan bisa diunduh (PDF) | Anda (unduh surat, lihat 4.5) |
| 🔴 **Ditolak RT / Ditolak RW** | Pengajuan ditolak, alasannya bisa dibaca | Anda (perbaiki lalu ajukan lagi, lihat 4.6) |

### ⚠️ Perhatian

- **Muncul pesan "Anda harus menyetujui pernyataan kebenaran data."** Kotak pernyataan belum dicentang. Centang dulu, lalu kirim lagi.
- **Muncul pesan "Silakan pilih jenis surat terlebih dahulu."** Pilih jenis surat pada kotak **JENIS SURAT** di bagian atas.
- **Muncul pesan "Data penduduk tidak ditemukan. Silakan hubungi pengurus RT/RW."** Akun Anda belum tertaut ke data penduduk. Hubungi Ketua RT.
- **Data diri salah, misalnya pekerjaan atau alamat.** Batalkan dengan tombol **Batal**, perbaiki di menu **Profil Keluarga**, lalu ajukan lagi.
- **Ukuran foto KTP/KK terlalu besar.** Batas ukurannya 5 MB per file. Foto biasa dari kamera HP umumnya masih di bawah batas ini.

> 💡 **Tips:** Riwayat surat yang sudah **Selesai** atau **Ditolak** bisa dilihat lewat tombol hijau **Riwayat Pengajuan Surat** di kanan atas halaman. Angka di tombol itu menunjukkan jumlah riwayatnya.

> ➡️ **Cerita berlanjut:** Pak Budi Sampurno, Ketua RT 03, membuka pengajuan ini lalu menyetujuinya. Lihat `02-Ketua-RT.md`, cerita C-01 bagian 2.

---

### 4.3 Mengajukan surat untuk anggota keluarga lain

Pemegang akun dapat mengajukan surat atas nama anggota keluarga mana pun dalam satu KK. Caranya: pada bagian **Pilih Pemohon**, pilih nama anggota. Setiap pilihan ditulis dengan nama dan hubungan keluarganya, dengan **Kepala Keluarga** di urutan pertama. Begitu pemohon diganti, **Data Diri Pemohon** serta kotak **Foto KTP** dan **Kartu Keluarga** ikut berganti mengikuti pemohon tersebut.

> 👤 **SIMULASI W-G: Surat untuk anak**
>
> Andi Saputra, anak Pak **Budi Santoso**, membutuhkan surat keterangan untuk pendaftaran beasiswa sekolah.
>
> - **Masuk sebagai:** `budisantoso`
> - **Jenis surat:** Surat Keterangan Serbaguna
> - **Pemohon:** Andi Saputra (Anak)
> - **Keperluan:** Pendaftaran beasiswa sekolah
> - **Catatan penyusun gambar:** pilih pemohon dan isi keperluan, **jangan dikirim**, agar daftar surat tidak berubah.

**1. Pilih jenis surat.**
Pilih **Surat Keterangan Serbaguna** pada **JENIS SURAT**. Surat ini tidak punya isian tambahan.

**2. Ganti pemohon.**
Pada **Pilih Pemohon**, pilih **Andi Saputra** (Anak).

> 📸 **SCREENSHOT W-17: Memilih pemohon anggota keluarga**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat, jenis "Surat Keterangan Serbaguna"
> - **Kondisi:** daftar pilihan pemohon terbuka memperlihatkan Budi Santoso, Siti Rahma, dan Andi Saputra; **Andi Saputra** disorot
> - **Area:** bagian **Pilih Pemohon** dan awal **Data Diri Pemohon**
> - **Sorot:** kotak merah pada pilihan **Andi Saputra** dan data dirinya yang berubah
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-17-pilih-pemohon-anggota.png`

**3. Lengkapi dan kirim seperti 4.2.**
Isi keperluan, centang pernyataan, lalu **Kirim Pengajuan**.

Contoh surat dengan isian tambahan: pilih **Surat Kematian**, lalu isi **Nama yang Meninggal**, **Tanggal Meninggal**, **Jam Meninggal**, **Tempat Meninggal**, **Dimakamkan di**, dan **Penyebab** (boleh kosong).

> 📸 **SCREENSHOT W-18: Isian tambahan khusus jenis surat**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat, jenis "Surat Kematian"
> - **Kondisi:** bagian **Isian Tambahan Surat** tampil dengan enam kolom (tanpa dikirim)
> - **Area:** bagian **Isian Tambahan Surat**
> - **Sorot:** kotak merah pada kolom **Nama yang Meninggal** dan **Tanggal Meninggal**
> - **Perangkat:** 📱 mobile 390×844
> - **Nama file:** `W-18m-isian-tambahan-surat.png`

⚠️ **Perhatian**

- **"Pemohon yang dipilih tidak terdaftar sebagai anggota keluarga Anda."** Pilih ulang dari daftar yang tersedia.
- **Pemohon baru tidak punya foto KTP atau KK.** Kotak dokumen menampilkan tombol **Unggah KTP** atau **Unggah KK**; unggah bila ada. Mengunggah bersifat pilihan.

---

### 4.4 Memantau status surat

Ada tiga tempat untuk memantau:

1. **Kotak Sedang Diproses** di halaman Permohonan Surat. Menampilkan surat berstatus **Menunggu RT** atau **Menunggu RW**. Klik satu surat untuk membuka jendela **Status Peninjauan Surat**.
2. **Dashboard.** Kotak pemberitahuan **Surat Keterangan Menunggu Verifikasi RT**, **Surat Keterangan Menunggu Pengesahan RW**, atau **Surat Keterangan Ditolak**.
3. **Lonceng notifikasi.** Notifikasi **Surat Disetujui RT**, **Surat Terbit**, atau **Surat Ditolak RT/RW**.

Jendela **Status Peninjauan Surat** menunjukkan alur verifikasi:

- Saat **Menunggu RT:** "1. Diajukan ✓, 2. Verifikasi RT •, 3. Pengesahan RW, 4. Selesai."
- Saat **Menunggu RW:** judul "Disetujui RT • Menunggu Pengesahan RW", dengan catatan dari RT bila ada.

Di sisi kanan halaman ada juga kotak **Butuh Bantuan?** dengan tombol **Hubungi Pengurus** (membuka WhatsApp) dan kotak **Informasi Proses**.

> 👤 **SIMULASI C-01 (bagian 3 dari 3): Memantau sampai surat terbit**
>
> Setelah mengirim, Pak **Budi Santoso** memeriksa posisi suratnya beberapa kali, lalu menerima kabar bahwa surat sudah terbit.
>
> - **Masuk sebagai:** `budisantoso`
> - **Yang dilihat:** status berubah dari **Menunggu RT** ke **Menunggu RW**, lalu **Selesai**
> - **Sebelumnya:** Ketua RT menyetujui (`02-Ketua-RT.md`, cerita C-01 bagian 2) dan RW mengesahkan (`05-Admin-RW.md` / `06-Ketua-RW.md`, cerita C-01)

**1. Buka halaman Permohonan Surat.**
Lihat kotak **Sedang Diproses**.

**2. Klik surat yang dimaksud.**
Jendela **Status Peninjauan Surat** terbuka.

> 📸 **SCREENSHOT W-19: Status Peninjauan Surat, Menunggu RT**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat
> - **Kondisi:** surat "Surat Keterangan Domisili" masih **Menunggu RT**; jendela **Masih Menunggu Peninjauan RT** terbuka
> - **Area:** jendela status
> - **Sorot:** kotak merah pada label **Menunggu RT** dan daftar langkah "Alur Verifikasi Surat"
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-19-status-menunggu-rt.png`, `W-19m-status-menunggu-rt.png`

> 📸 **SCREENSHOT W-20: Status Peninjauan Surat, Menunggu RW**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat
> - **Kondisi:** Ketua RT sudah menyetujui; label **Menunggu RW**; jendela **Disetujui RT • Menunggu Pengesahan RW** terbuka
> - **Area:** jendela status
> - **Sorot:** kotak merah pada judul jendela dan langkah "2. Disetujui RT ✓"
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-20-status-menunggu-rw.png`

> 📸 **SCREENSHOT W-21: Kotak pemberitahuan surat di Dashboard**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/dashboard
> - **Kondisi:** ada surat berstatus **Menunggu RT** (atau **Menunggu RW**), kotak pemberitahuan tampil
> - **Area:** bagian atas Dashboard
> - **Sorot:** kotak merah pada kotak **Surat Keterangan Menunggu Verifikasi RT** dan tautan **Cek Status Surat**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-21-dashboard-pemberitahuan-surat.png`, `W-21m-dashboard-pemberitahuan-surat.png`

✅ **Hasilnya:** Anda mengetahui tahap surat tanpa bertanya ke pengurus.

⚠️ **Perhatian**

- **Surat sudah terbit tetapi tidak ada kotak "siap diunduh" di Dashboard.** Gunakan lonceng (notifikasi **Surat Terbit**) atau tombol **Riwayat Pengajuan Surat** di halaman Permohonan Surat.

---

### 4.5 Surat selesai: unduh PDF dan riwayat

Surat yang sudah disahkan RW berstatus **Selesai** dan pindah dari kotak **Sedang Diproses** ke **Riwayat Pengajuan Surat**. Buka riwayat lewat tombol hijau **Riwayat Pengajuan Surat** di kanan atas halaman (angka kecilnya adalah jumlah surat Selesai dan Ditolak). Jendela riwayat punya dua tab: **Selesai** dan **Ditolak**, lengkap dengan jumlahnya.

Setiap surat selesai tampil sebagai satu baris berisi jenis surat, tanggal selesai, dan tombol hijau **Unduh PDF**. File PDF diberi nama dengan nama pemohon, contoh `Surat_Pengantar_Budi_Santoso.pdf`. Surat sudah berstempel dan bertanda tangan Ketua RT dan Ketua RW.

> 👤 **SIMULASI W-H: Mengunduh surat yang sudah terbit**
>
> Pak **Budi Santoso** membuka aplikasi dan mengunduh surat domisilinya untuk dibawa ke bank.
>
> - **Masuk sebagai:** `budisantoso`
> - **Surat:** Surat Keterangan Domisili (Selesai)
> - **Catatan data demo:** akun ini sudah memiliki 9 contoh surat Selesai, sehingga riwayat langsung terisi

**1. Cari tombol Riwayat Pengajuan Surat.**
Di halaman Permohonan Surat, lihat tombol hijau **Riwayat Pengajuan Surat** di kanan atas. Angka kecil di tombol menunjukkan jumlah surat Selesai ditambah Ditolak (pada data demo: 9).

> 📸 **SCREENSHOT W-23: Tombol Riwayat Pengajuan Surat**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat
> - **Kondisi:** halaman Permohonan Surat terbuka; tombol **Riwayat Pengajuan Surat** dengan angka kecil terlihat
> - **Area:** bagian kepala halaman (judul **Layanan Permohonan Surat** dan tombol di kanan)
> - **Sorot:** kotak merah pada tombol **Riwayat Pengajuan Surat** dan angkanya
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-23-tombol-riwayat-surat.png`

**2. Buka riwayat.**
Tekan tombol itu. Jendela **Riwayat Pengajuan Surat** terbuka pada tab **Selesai**.

> 📸 **SCREENSHOT W-22: Riwayat Pengajuan Surat, tab Selesai**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat
> - **Kondisi:** jendela riwayat terbuka; tab **Selesai** memuat 9 contoh surat dengan tombol **Unduh PDF**
> - **Area:** jendela riwayat
> - **Sorot:** kotak merah pada tab **Selesai** dan tombol **Unduh PDF** di baris "Surat Keterangan Domisili"
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-22-riwayat-surat-selesai.png`, `W-22m-riwayat-surat-selesai.png`

**3. Tekan Unduh PDF.**
File PDF tersimpan di perangkat Anda. Buka dengan aplikasi PDF.

> 📸 **SCREENSHOT W-24: Contoh hasil PDF surat**
> - **Akun:** budisantoso (Warga)
> - **URL:** (berkas PDF yang diunduh dari /warga/surat/ lalu nomor surat lalu /pdf)
> - **Kondisi:** PDF "Surat Keterangan Domisili" terbuka di penampil PDF
> - **Area:** halaman pertama PDF
> - **Sorot:** kotak merah pada kop surat RW 21, nama pemohon, dan area tanda tangan
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-24-contoh-hasil-pdf.png`

✅ **Hasilnya:** Surat PDF resmi siap dicetak atau dikirim.

⚠️ **Perhatian**

- **Pesan "Surat belum selesai diproses."** Anda mencoba mengunduh surat yang belum berstatus **Selesai**. Tunggu sampai RW mengesahkan.
- **Pesan "Unauthorized access."** Akun Anda belum tertaut ke data penduduk. Hubungi Ketua RT.

> 💡 **Tips:** Simpan PDF di HP supaya bisa dibuka kembali tanpa mengunduh ulang.

---

### 4.6 Jika surat ditolak

Surat dapat ditolak oleh Ketua RT (status **Ditolak RT**) atau oleh RW (status **Ditolak RW**). Alasan penolakan bisa dibaca. Setelah memperbaiki data atau kelengkapan, Anda mengajukan surat baru.

> 👤 **SIMULASI C-02 (bagian 1 dari 3): Surat ditolak karena data kurang**
>
> Pak **Budi Santoso** mengajukan **Surat Keterangan Miskin / Tidak Mampu** untuk keperluan bantuan sekolah anaknya, tetapi lupa melampirkan KK dan mengisi penghasilan. Ketua RT menolaknya.
>
> - **Masuk sebagai:** `budisantoso`
> - **Jenis surat:** Surat Keterangan Miskin / Tidak Mampu
> - **Keperluan (pengajuan pertama):** Bantuan biaya sekolah Andi Saputra
> - **Alasan penolakan (contoh yang diketik Ketua RT):** `Mohon lampirkan foto KK dan isi perkiraan penghasilan per bulan.`
> - **Selanjutnya:** Ketua RT 03 menolak pengajuan. Lihat `02-Ketua-RT.md`, cerita C-02 bagian 2. Lalu Pak Budi memperbaiki dan mengajukan ulang (bagian 3 dari 3, di bawah).

**1. Ajukan seperti biasa.**
Ikuti 4.2 dengan jenis **Surat Keterangan Miskin / Tidak Mampu**, kosongkan **Perkiraan Penghasilan per Bulan**, lalu **Kirim Pengajuan**. Status **Menunggu RT**.

**2. Setelah ditolak, lihat alasannya.**
Anda menerima notifikasi **Surat Ditolak RT** dan kotak merah **Surat Keterangan Ditolak** di Dashboard (tautan **Lihat Detail** membawa Anda ke halaman Permohonan Surat). Di halaman itu, tekan **Riwayat Pengajuan Surat**, lalu pindah ke tab **Ditolak**. Setiap baris menampilkan jenis surat, tanggal, label **Ditolak RT** (atau **Ditolak RW**), dan **Alasan:** dari pengurus.

> 📸 **SCREENSHOT W-25: Riwayat Pengajuan Surat, tab Ditolak**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat
> - **Kondisi:** jendela riwayat terbuka pada tab **Ditolak**; baris "Surat Keterangan Miskin / Tidak Mampu" memperlihatkan alasan penolakan
> - **Area:** jendela riwayat
> - **Sorot:** kotak merah pada label **Ditolak RT** dan kalimat **Alasan:**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-25-riwayat-surat-ditolak.png`, `W-25m-riwayat-surat-ditolak.png`

**3. Perbaiki lalu ajukan ulang.**
Tutup jendela riwayat. Pilih jenis yang sama pada **JENIS SURAT**, isi **Perkiraan Penghasilan per Bulan** (misalnya `Rp 1.200.000`), unggah foto KK lewat **Unggah KK**, centang pernyataan, lalu **Kirim Pengajuan**. Pengajuan baru berstatus **Menunggu RT**; pengajuan lama tetap tercatat di riwayat.

> 📸 **SCREENSHOT W-26: Formulir pengajuan ulang setelah ditolak**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat
> - **Kondisi:** jenis **Surat Keterangan Miskin / Tidak Mampu** dipilih; **Perkiraan Penghasilan per Bulan** diisi `Rp 1.200.000`; foto KK sudah dipilih
> - **Area:** bagian **Keperluan Pengajuan**, **Isian Tambahan Surat**, dan **Dokumen Pendukung**
> - **Sorot:** kotak merah pada kolom **Perkiraan Penghasilan per Bulan** dan kotak **Kartu Keluarga**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-26-formulir-ajukan-ulang.png`

✅ **Hasilnya:** Pengajuan baru masuk antrean Ketua RT dari awal.

⚠️ **Perhatian**

- **Alasan tertulis "Tidak ada catatan. Silakan hubungi pengurus RT/RW."** Pengurus tidak mengetik alasan. Hubungi pengurus RT/RW lewat tombol **Hubungi Pengurus** di halaman Permohonan Surat.
- **Tidak ada tombol "ajukan ulang" pada surat lama.** Surat yang ditolak tidak diubah; Anda membuat pengajuan baru dari formulir.
- **Pesan "Tidak ada surat yang ditolak."** Tab **Ditolak** kosong karena belum ada surat yang ditolak.

> ➡️ **Cerita berlanjut:** Ketua RT 03 memeriksa pengajuan baru. Lihat `02-Ketua-RT.md`, cerita C-02 bagian 3.

---

## 5. Iuran Warga

Menu **Iuran Warga** punya dua submenu: **Iuran Bulanan** (iuran wajib RT) dan **Iuran Lainnya** (tagihan insidental dari RT, RW, atau DKM). Bendahara memeriksa bukti bayar Anda; status berubah setelah diperiksa.

### 5.1 Iuran Bulanan

Halaman **Iuran Bulanan** (`/warga/iuran`) menampilkan 12 kartu bulan untuk tahun yang dipilih. Di atas kartu ada nama iuran (**Iuran Bulanan Warga**) dan nominal (**Rp 60.000 / bulan** untuk RT 03). Kotak **Tahun** di kanan atas mengganti tahun. Jika RT belum menetapkan iuran, tampil "Belum ada iuran yang ditetapkan RT untuk tahun ...".

Warna kartu bulan:

| Kartu | Artinya |
|---|---|
| Putih, "Belum bayar" | Belum dibayar |
| Oranye, "Menunggu verifikasi" | Sudah diajukan, menunggu bendahara |
| Hijau, "Lunas" | Sudah diverifikasi lunas |
| Merah, "Ditolak" | Ditolak, disertai **Alasan:** dan "Silakan ajukan ulang." |

Tombol hijau **Bayar Iuran** hanya tampil bila masih ada bulan yang belum dibayar atau ditolak.

> 👤 **SIMULASI C-04 (bagian 1 dari 3): Membayar Iuran Bulanan**
>
> Pak **Budi Santoso** ingin membayar iuran bulan ini lewat transfer ke rekening bendahara RT 03.
>
> - **Masuk sebagai:** `budisantoso`, kata sandi `password`
> - **Iuran:** Iuran Bulanan Warga, Rp60.000 per bulan (RT 03)
> - **Bulan yang dibayar:** bulan berjalan (satu bulan)
> - **Tanggal bayar:** hari ini
> - **Metode:** Transfer, bukti berupa foto struk (JPG)
> - **Rekening tujuan (tampil di formulir):** BSI 0998988997 a.n. Bendahara RT 03
> - **Selanjutnya:** Bendahara RT 03 memeriksa bukti. Lihat `04-Op-Keuangan-RT.md`, cerita C-04 bagian 2.

**1. Buka Iuran Bulanan.**
Klik **Iuran Warga**, lalu submenu **Iuran Bulanan**.

> 📸 **SCREENSHOT W-27: Iuran Bulanan, kartu 12 bulan**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/iuran
> - **Kondisi:** belum ada pembayaran; semua kartu "Belum bayar"
> - **Area:** satu layar penuh dengan lencana nama iuran, nominal, tombol **Bayar Iuran**, dan 12 kartu
> - **Sorot:** kotak merah pada lencana **Rp 60.000 / bulan**, tombol **Bayar Iuran**, dan kotak **Tahun**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-27-iuran-bulanan.png`, `W-27m-iuran-bulanan.png`

**2. Tekan Bayar Iuran.**
Jendela **Bayar Iuran** (disertai tahun) terbuka.

**3. Centang bulan yang dibayar.**
Pada **Pilih Bulan yang Dibayar**, centang bulan berjalan. Bulan yang sudah lunas atau menunggu verifikasi tidak ditampilkan. Kotak hijau **Total** langsung menghitung jumlahnya (contoh: Rp 60.000, 1 bulan). Anda boleh mencentang beberapa bulan sekaligus.

**4. Isi tanggal dan metode.**
Isi **Tanggal Bayar** (tidak boleh lebih dari hari ini). Pilih **Metode**: **Transfer** atau **Tunai**.

**5. Lihat rekening dan unggah bukti.**
Bila memilih **Transfer**, tampil kotak **Rekening RT** berisi bank, nomor rekening, dan nama pemilik rekening (dan kode QRIS bila bendahara sudah mengunggahnya). Bila bendahara belum mengatur rekening tampil "Rekening RT belum diatur bendahara. Anda tetap dapat membayar tunai." Pada **Bukti Pembayaran**, unggah foto struk (JPG atau PNG, maksimal 5 MB).

> 📸 **SCREENSHOT W-28: Jendela Bayar Iuran**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/iuran
> - **Kondisi:** jendela **Bayar Iuran** terbuka; satu bulan dicentang; **Total** Rp 60.000; metode **Transfer**; kotak **Rekening RT** terlihat; foto struk sudah dipilih
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada bulan yang dicentang, kotak **Total**, kotak **Rekening RT**, dan tombol **Kirim**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-28-bayar-iuran-bulanan.png`, `W-28m-bayar-iuran-bulanan.png`

**6. Tekan Kirim.**
Tombol **Kirim** baru aktif setelah minimal satu bulan dicentang.

✅ **Hasilnya:**

- Muncul pesan "Pengajuan pembayaran 1 bulan dikirim dan menunggu verifikasi bendahara."
- Kartu bulan itu berubah oranye bertuliskan **Menunggu verifikasi**.

> 📸 **SCREENSHOT W-29: Kartu bulan Menunggu verifikasi**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/iuran
> - **Kondisi:** setelah **Kirim**; satu kartu bulan berwarna oranye
> - **Area:** kartu 12 bulan
> - **Sorot:** kotak merah pada kartu **Menunggu verifikasi**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-29-iuran-menunggu-verifikasi.png`, `W-29m-iuran-menunggu-verifikasi.png`

**7. Bila ditolak, ajukan ulang.**
Bila bendahara menolak (misalnya struk tidak terbaca), kartu bulan itu berwarna merah bertuliskan **Ditolak**, **Alasan:** dan "Silakan ajukan ulang." Anda juga menerima notifikasi **Pembayaran Ditolak**. Tekan **Bayar Iuran** lagi, centang bulan yang sama, perbaiki bukti, lalu **Kirim**.

> 📸 **SCREENSHOT W-30: Kartu bulan Ditolak**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/iuran
> - **Kondisi:** satu bulan sudah ditolak bendahara dengan alasan (contoh: `Bukti transfer tidak terbaca, mohon kirim ulang.`)
> - **Area:** kartu 12 bulan
> - **Sorot:** kotak merah pada kartu merah, kalimat **Alasan:**, dan "Silakan ajukan ulang."
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-30-iuran-ditolak.png`

⚠️ **Perhatian**

- **"Tidak ada bulan yang diajukan (mungkin sudah lunas atau sedang menunggu verifikasi)."** Bulan yang dipilih sudah diproses. Muat ulang halaman.
- **Tombol Kirim abu-abu (tidak aktif).** Belum ada bulan yang dicentang.
- **Tanggal Bayar tidak bisa memilih tanggal depan.** Itu disengaja; batasnya hari ini.
- **"Belum ada iuran yang ditetapkan RT untuk tahun ...".** Bendahara RT belum menetapkan iuran tahun itu. Pilih tahun lain atau tanyakan ke bendahara.

> 💡 **Tips:** Membayar beberapa bulan sekaligus bisa dilakukan dengan satu kali kirim dan satu bukti.

> ➡️ **Cerita berlanjut:** Bendahara RT 03 memeriksa bukti. Lihat `04-Op-Keuangan-RT.md`, cerita C-04 bagian 2.

---

### 5.2 Iuran Lainnya

Halaman **Iuran Lainnya** (`/warga/tagihan`) berisi tagihan di luar iuran bulanan. Subjudulnya: "Iuran di luar iuran bulanan wajib. Bayar dan unggah bukti di sini." Daftar menampilkan **Iuran**, **Tenggat**, **Nominal**, **Status**, dan **Aksi**. Tagihan yang belum dibayar ada di atas.

Data demo untuk keluarga RT 03:

| Tagihan | Penerbit | Nominal | Keterangan di daftar |
|---|---|---|---|
| **Perbaikan Toa Masjid** | RT 03 | Rp20.000 | Minimal, boleh lebih |
| **Iuran Perbaikan Got** | RW | Rp30.000 | "Dari RW, dibayar lewat RT" |
| **Iuran Kematian** | DKM | Rp20.000 | Minimal, boleh lebih |

(Tagihan "Iuran Perbaikan Jalan" milik RT 01 tidak tampil untuk keluarga RT 03.)

Status tagihan:

| Label | Artinya |
|---|---|
| 🔴 **Belum Lunas** | Belum dibayar. Tombol **Bayar** tersedia. |
| 🔵 **Menunggu Konfirmasi** | Bukti sudah dikirim, tertulis "Sedang diverifikasi". |
| 🟢 **Lunas** | Sudah diverifikasi bendahara. |

Tagihan yang ditolak bendahara kembali berstatus **Belum Lunas** dengan tulisan "Ditolak: (alasan). Silakan bayar ulang."

Penting: tagihan RW yang tertulis "dibayar lewat RT" dibayar ke **rekening atau QRIS RT**, atau tunai ke bendahara RT, bukan ke RW.

> 👤 **SIMULASI C-03 (bagian 1 dari 3): Membayar Iuran Perbaikan Got**
>
> Warga RW 21 diminta membantu perbaikan got utama. Pak **Budi Santoso** membayar tagihan **Iuran Perbaikan Got** lewat transfer ke rekening RT 03.
>
> - **Masuk sebagai:** `budisantoso`, kata sandi `password`
> - **Tagihan:** Iuran Perbaikan Got, Rp30.000 (iuran RW, dikumpulkan lewat RT)
> - **Nominal dibayar:** 30000 (minimal; boleh lebih)
> - **Metode:** Transfer, bukti berupa foto struk (JPG)
> - **Rekening tujuan (tampil di jendela bayar):** BSI 0998988997 a.n. Bendahara RT 03
> - **Selanjutnya:** Bendahara RT 03 memeriksa bukti. Lihat `04-Op-Keuangan-RT.md`, cerita C-03 bagian 2. Setoran kolektif RT ke RW dijelaskan di `08-Op-Keuangan-RW.md`, cerita C-03 bagian 3.

**1. Buka Iuran Lainnya.**
Klik **Iuran Warga**, lalu submenu **Iuran Lainnya**.

> 📸 **SCREENSHOT W-31: Daftar Iuran Lainnya**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/tagihan
> - **Kondisi:** tiga tagihan berstatus **Belum Lunas**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada baris "Iuran Perbaikan Got", tulisan "Dari RW, dibayar lewat RT", dan tombol **Bayar**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-31-iuran-lainnya.png`, `W-31m-iuran-lainnya.png`

**2. Tekan Bayar.**
Pada baris **Iuran Perbaikan Got**, tekan **Bayar**. Jendela **Pembayaran Iuran RT** terbuka (judul mengikuti unit penerima uang; untuk iuran RW lewat RT tertulis RT). Bagian atas jendela menampilkan **Total Tagihan**, status **Belum Lunas**, dan nama tagihan.

**3. Pilih metode pembayaran.**
Ada tiga tab:

- **Transfer Bank:** menampilkan **Nomor Rekening RT** (bank dan nomor), tombol salin (**Salin Nomor Rekening**), dan **Atas Nama**. Pastikan nama rekening penerima sesuai sebelum mentransfer.
- **QRIS:** menampilkan kode QR bila bendahara sudah mengunggahnya. Bila belum, tampil "Kode QRIS untuk unit RT belum diunggah oleh bendahara." (di data demo, QRIS belum diunggah).
- **Cash:** penjelasan "Bayar Tunai Langsung (Cash)". Serahkan uang tunai kepada bendahara, lalu konfirmasi di sini. Untuk Cash tidak perlu mengunggah bukti.

> 📸 **SCREENSHOT W-32: Jendela pembayaran, tab Transfer Bank**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/tagihan
> - **Kondisi:** jendela **Pembayaran Iuran RT** terbuka pada tab **Transfer Bank**
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada nomor rekening, tombol salin, dan **Atas Nama**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-32-bayar-tab-transfer.png`, `W-32m-bayar-tab-transfer.png`

> 📸 **SCREENSHOT W-33: Jendela pembayaran, tab QRIS**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/tagihan
> - **Kondisi:** tab **QRIS** dipilih dan tampil keterangan QRIS belum diunggah
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada pesan "Kode QRIS untuk unit RT belum diunggah oleh bendahara."
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-33-bayar-tab-qris.png`

> 📸 **SCREENSHOT W-34: Jendela pembayaran, tab Cash**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/tagihan
> - **Kondisi:** tab **Cash** dipilih; tombol bawah berubah menjadi **Konfirmasi Penyerahan Cash**
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada teks "Bayar Tunai Langsung (Cash)" dan tombol **Konfirmasi Penyerahan Cash**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-34-bayar-tab-cash.png`

**4. Isi nominal.**
Kolom **Nominal Dibayar (Rp)** sudah berisi nominal tagihan (30.000). Karena nominal tidak dikunci, keterangannya "Minimal Rp 30.000. Anda boleh membayar lebih." Bila nominal dikunci, kolom hanya bisa dibaca ("Nominal sudah ditetapkan dan tidak dapat diubah.").

**5. Unggah bukti (Transfer dan QRIS).**
Pada **Unggah Bukti Pembayaran** (wajib), pilih foto atau tangkapan layar struk (JPG, PNG, atau WEBP, maksimal 5 MB). Pratinjau **Preview Bukti** muncul di bawahnya.

**6. Kirim.**
Tekan **Konfirmasi & Unggah Bukti** (atau **Konfirmasi Penyerahan Cash** pada tab Cash). Tombol **Batal** menutup jendela.

✅ **Hasilnya:**

- Muncul pesan "Pembayaran berhasil dikirim dan menunggu verifikasi bendahara."
- Status tagihan menjadi **Menunggu Konfirmasi** dan kolom aksi menampilkan "Sedang diverifikasi".

> 📸 **SCREENSHOT W-35: Tagihan Menunggu Konfirmasi**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/tagihan
> - **Kondisi:** setelah mengirim bukti; baris "Iuran Perbaikan Got" berstatus **Menunggu Konfirmasi**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada label **Menunggu Konfirmasi** dan tulisan "Sedang diverifikasi"
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-35-tagihan-menunggu-konfirmasi.png`, `W-35m-tagihan-menunggu-konfirmasi.png`

**7. Lihat hasil akhir.**
Setelah bendahara menyetujui, status menjadi **Lunas**. Bila ditolak, status kembali **Belum Lunas** dengan tulisan "Ditolak: ... Silakan bayar ulang." dan tombol **Bayar** muncul lagi.

> 📸 **SCREENSHOT W-36: Hasil akhir tagihan (Lunas)**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/tagihan
> - **Kondisi:** setelah disetujui bendahara; "Iuran Perbaikan Got" berstatus **Lunas** dan berada di bawah tagihan yang belum lunas
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada label **Lunas**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-36-tagihan-lunas.png`

⚠️ **Perhatian**

- **"Silakan unggah foto atau tangkapan layar bukti pembayaran (JPG, PNG, atau WEBP)."** Pada Transfer dan QRIS, bukti wajib. Pilih foto lalu kirim lagi.
- **"Nominal pembayaran minimal Rp 30.000."** Nominal yang diketik kurang dari tagihan. Naikkan sesuai minimal.
- **"Pilih metode pembayaran terlebih dahulu."** Pilih salah satu tab metode.
- **"Tagihan ini sudah lunas."** Tagihan sudah diverifikasi; tidak perlu dibayar lagi.
- **"Rekening bank untuk unit RT belum diatur oleh bendahara."** Bendahara belum mengisi rekening. Bayar tunai atau hubungi bendahara.
- **Tidak ada tombol resi pada tagihan Lunas.** Halaman ini hanya menampilkan status. Bukti pembayaran resmi dapat diminta ke bendahara.
- **Pesan "Tidak Ada Tagihan: Keluarga Anda tidak memiliki tagihan aktif saat ini."** Memang belum ada tagihan untuk keluarga Anda.

> 💡 **Tips:** Pengingat tagihan juga dikirim lewat lonceng notifikasi sebagai "Tagihan Belum Lunas" dan "Pengingat Iuran".

> ➡️ **Cerita berlanjut:** Bendahara RT 03 memeriksa pembayaran. Lihat `04-Op-Keuangan-RT.md`, cerita C-03 bagian 2.

---

## 6. Laporan Keuangan

Menu **Laporan Keuangan** (`/laporan-keuangan`) untuk Warga menampilkan halaman **Transparansi Keuangan**: ringkasan uang masuk dan uang keluar yang sudah **diterbitkan** pengurus. Warga hanya melihat angka ringkasan, tidak ada berkas laporan yang dapat diunduh.

Isi halaman:

- **Tab unit:** **RT** (dengan nomor RT Anda, misalnya RT 03), **RW**, dan **DKM**. Unit yang belum punya laporan terbit diberi tulisan "belum ada" dan tidak bisa dipilih.
- Kotak **Tahun** untuk berganti tahun.
- **Saldo Kas Terkini** (dengan keterangan "per" bulan laporan terbaru), **Pemasukan** tahun itu, dan **Pengeluaran** tahun itu.
- **Iuran saya** (bila tersedia): nama iuran, "n dari 12 bulan lunas", dan keterangan bulan yang menunggu verifikasi.
- **Tren Bulanan** (grafik), **Uang masuk dari mana**, **Uang dipakai untuk apa**, dan **Rincian per Bulan**. Bulan tanpa laporan tertulis "belum terbit".

> 👤 **SIMULASI W-I: Pak Budi Santoso memeriksa keuangan RW**
>
> Pak **Budi Santoso** ingin tahu ke mana iuran dipakai.
>
> - **Masuk sebagai:** `budisantoso`
> - **Yang dibuka:** tab **RW**, lalu tab **DKM**, lalu tab **RT 03**
> - **Catatan data demo:** laporan demo tersedia untuk RW, DKM, dan RT 01. RT 03 belum punya laporan terbit.

**1. Buka Laporan Keuangan.**
Klik **Laporan Keuangan** di menu samping.

> 📸 **SCREENSHOT W-37: Transparansi Keuangan, unit RW**
> - **Akun:** budisantoso (Warga)
> - **URL:** /laporan-keuangan?unit=RW
> - **Kondisi:** tab **RW** aktif, data laporan tampil
> - **Area:** satu layar penuh dari judul sampai kartu ringkasan dan awal grafik
> - **Sorot:** kotak merah pada tab unit, **Saldo Kas Terkini**, dan kotak **Tahun**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-37-laporan-keuangan-rw.png`, `W-37m-laporan-keuangan-rw.png`

**2. Pindah unit.**
Klik tab **DKM** untuk melihat kas masjid. Tab **RT 03** menunjukkan "Belum ada laporan terbit" bila RT Anda belum menerbitkan.

> 📸 **SCREENSHOT W-38: Unit RT belum punya laporan terbit**
> - **Akun:** budisantoso (Warga)
> - **URL:** /laporan-keuangan?unit=RT
> - **Kondisi:** tab **RT 03** dipilih (bertanda "belum ada")
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tulisan "Belum ada laporan terbit" dan keterangan "Laporan keuangan tahun ... untuk unit ini belum dipublikasikan."
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-38-laporan-rt-belum-ada.png`

**3. Gulir ke rincian.**
Lihat **Uang masuk dari mana**, **Uang dipakai untuk apa**, dan **Rincian per Bulan**.

> 📸 **SCREENSHOT W-39: Rincian kategori dan per bulan**
> - **Akun:** budisantoso (Warga)
> - **URL:** /laporan-keuangan?unit=RW
> - **Kondisi:** halaman digulir ke bagian rincian
> - **Area:** **Uang masuk dari mana**, **Uang dipakai untuk apa**, dan tabel **Rincian per Bulan**
> - **Sorot:** kotak merah pada satu kategori dan satu baris bulan
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-39-laporan-rincian.png`

✅ **Hasilnya:** Anda melihat kondisi kas yang sudah diumumkan pengurus.

⚠️ **Perhatian**

- **Kotak "Belum ada laporan terbit".** Laporan tahun itu belum diterbitkan. Pilih tahun atau unit lain.
- **Tidak ada tombol unduh.** Untuk Warga, laporan hanya berupa ringkasan angka. Pengurus yang dapat mengunduh berkas.

> ➡️ **Cerita berlanjut:** Laporan disusun dan diterbitkan oleh bendahara dan penyetuju. Lihat `04-Op-Keuangan-RT.md`, `08-Op-Keuangan-RW.md`, atau `09-DKM.md`, cerita C-11.

---

## 7. Pusat Informasi

Menu **Pusat Informasi** (`/warga/berita`) menyatukan kabar lingkungan dalam satu halaman. Subjudul: "Ikuti terus perkembangan terkini di komunitas kami."

Isi halaman:

- **Unggulan:** satu konten besar di bagian atas.
- **Tab kategori:** **Semua Pembaharuan**, **Berita**, **Pengumuman**, **UMKM News**, dan **Galeri Dokumentasi**. Bila tab kosong tampil "Belum ada konten di kategori ini."
- **Kartu konten** dengan tombol **Bagikan** dan tautan **Baca selengkapnya**.
- Panel samping **Pengumuman Penting** (pengumuman prioritas, bertanda **Prioritas Tinggi**; tautan **Lihat Semua Pengumuman**) dan **Sorotan UMKM** ("Produk Minggu ini", tombol **Lihat di Gallery**).

Klik satu kartu untuk membuka detail di halaman yang sama (dengan judul, penulis, tanggal, isi, dan tombol **Bagikan** ke WhatsApp). Untuk agenda, detail memuat keterangan **Konfirmasi Kehadiran: Aktif** bila RSVP dibuka, serta **Titik Lokasi Peta**.

> 👤 **SIMULASI W-J: Pak Budi Santoso membaca pengumuman penting**
>
> Pak **Budi Santoso** melihat ada pemberitahuan pemadaman listrik dan ingin membaca isinya.
>
> - **Masuk sebagai:** `budisantoso`
> - **Yang dibuka:** pengumuman "Pemadaman Listrik Terjadwal" (bertanda penting), lalu berita "Kerja Bakti Bersih Lingkungan Sukses Digelar"

**1. Buka Pusat Informasi.**
Klik **Pusat Informasi** di menu samping.

> 📸 **SCREENSHOT W-40: Halaman Pusat Informasi**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/berita
> - **Kondisi:** tab **Semua Pembaharuan** aktif
> - **Area:** satu layar penuh dari judul sampai kartu pertama dan panel **Pengumuman Penting**
> - **Sorot:** kotak merah pada deretan tab dan panel **Pengumuman Penting**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-40-pusat-informasi.png`, `W-40m-pusat-informasi.png`

**2. Pilih tab Pengumuman.**
Klik tab **Pengumuman** atau tombol **Lihat Semua Pengumuman**.

> 📸 **SCREENSHOT W-41: Tab Pengumuman**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/berita
> - **Kondisi:** tab **Pengumuman** aktif; "Pemadaman Listrik Terjadwal" dan "Waspada Penipuan Mengatasnamakan Pengurus" tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tab **Pengumuman** dan kartu "Pemadaman Listrik Terjadwal"
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-41-pusat-informasi-pengumuman.png`

**3. Buka satu konten.**
Pada tab **Berita**, klik **Baca selengkapnya** di "Kerja Bakti Bersih Lingkungan Sukses Digelar".

> 📸 **SCREENSHOT W-42: Detail berita di Pusat Informasi**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/berita (tampilan detail)
> - **Kondisi:** detail berita terbuka
> - **Area:** satu layar penuh dari tombol kembali sampai awal isi
> - **Sorot:** kotak merah pada tombol **Bagikan** dan tombol kembali
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-42-detail-berita.png`

✅ **Hasilnya:** Anda membaca isi lengkap. Tombol kembali (panah) di atas detail membawa Anda ke daftar.

> 💡 **Tips:** Konten RT Anda (misalnya berita "Penghijauan Taman RT 03") ikut tampil di sini dan di Dashboard, tetapi tidak terlihat oleh warga RT lain.

---

## 8. Agenda Warga dan Konfirmasi Kehadiran

Menu **Agenda Warga** (`/warga/agenda`) menampilkan kalender kegiatan RW dan RT Anda.

Isi halaman:

- **Daftar Agenda** di sisi kiri: daftar agenda bulan yang sedang dilihat, dengan jumlahnya. Bila kosong: "Tidak ada agenda bulan ini". Tiap kartu menampilkan tanggal, judul, jam, lokasi, dan tombol **Lihat Detail**. Untuk agenda yang membuka konfirmasi, ada tiga tombol kecil **Ya**, **Tidak**, **Ragu** dan jumlah yang hadir.
- **Kalender** di kanan dengan tombol **Hari Ini**, panah kiri dan kanan, judul bulan, kotak pemilih bulan, serta pilihan tampilan **Bulan** dan **Minggu**.
- Klik satu kegiatan di kalender untuk membuka jendela detail (kategori, **Tanggal**, **Waktu**, **Lokasi**, **Buka di Google Maps**, **Peta Lokasi**, **Keterangan**). Bila agenda membuka konfirmasi, bagian **Konfirmasi Kehadiran** berisi tombol **Hadir**, **Tidak**, dan **Ragu**.

Konfirmasi kehadiran (RSVP) hanya ada pada agenda yang diaktifkan operator konten. Dalam data demo, "Rapat Pengurus dan Warga RW 21" dan "Tabligh Akbar Akhir Bulan" membuka konfirmasi; "Senam Sehat Bersama" tidak.

> 👤 **SIMULASI C-09 (bagian 2 dari 3): Konfirmasi hadir rapat warga**
>
> Operator konten RW sudah menerbitkan agenda rapat. Pak **Budi Santoso** melihatnya dan menyatakan akan hadir.
>
> - **Masuk sebagai:** `budisantoso`, kata sandi `password`
> - **Agenda:** Rapat Pengurus dan Warga RW 21 (Balai RW 21, pukul 19.00, beberapa hari ke depan)
> - **Pilihan:** **Hadir**
> - **Sebelumnya:** Operator Konten RW menyiapkan agenda. Lihat `07-Op-Konten-RW.md`, cerita C-09 bagian 1.
> - **Selanjutnya:** Operator Konten RW melihat jumlah yang hadir dan mengunggah dokumentasi setelah acara. Lihat `07-Op-Konten-RW.md`, cerita C-09 bagian 3.

**1. Buka Agenda Warga.**
Klik **Agenda Warga** di menu samping.

> 📸 **SCREENSHOT W-43: Halaman Agenda Warga**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/agenda
> - **Kondisi:** tampilan **Bulan** pada bulan yang memuat "Rapat Pengurus dan Warga RW 21"
> - **Area:** satu layar penuh (**Daftar Agenda** dan kalender)
> - **Sorot:** kotak merah pada kartu "Rapat Pengurus dan Warga RW 21", tombol **Hari Ini**, dan pilihan **Bulan**/**Minggu**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-43-agenda-warga.png`, `W-43m-agenda-warga.png`

**2. Buka detail kegiatan.**
Klik kegiatan "Rapat Pengurus dan Warga RW 21" pada kalender.

> 📸 **SCREENSHOT W-44: Jendela detail agenda**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/agenda
> - **Kondisi:** jendela detail "Rapat Pengurus dan Warga RW 21" terbuka; belum ada pilihan kehadiran
> - **Area:** jendela detail sampai bagian **Konfirmasi Kehadiran**
> - **Sorot:** kotak merah pada tiga tombol **Hadir**, **Tidak**, dan **Ragu**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-44-detail-agenda-modal.png`

**3. Tekan Hadir.**
Tombol **Hadir** berubah hijau penuh dan jumlah yang hadir bertambah. Anda boleh mengganti pilihan kapan saja dengan menekan tombol lain.

> 📸 **SCREENSHOT W-45: Setelah menekan Hadir**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/agenda
> - **Kondisi:** tombol **Hadir** sudah hijau penuh; jumlah yang hadir bertambah
> - **Area:** bagian **Konfirmasi Kehadiran**
> - **Sorot:** kotak merah pada tombol **Hadir** yang terpilih dan angka jumlah hadir
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-45-rsvp-hadir.png`, `W-45m-rsvp-hadir.png`

**4. Buka halaman penuh (opsional).**
Tekan **Lihat Detail** pada kartu di sisi kiri untuk membuka halaman agenda lengkap. Di halaman ini ada alur "Beranda Warga > Agenda", jadwal lengkap (tanggal, jam WIB, lokasi), isi agenda, kotak **Konfirmasi Kehadiran** dengan tulisan "n Warga Hadir" dan pilihan **Hadir**, **Tidak Hadir**, **Ragu-ragu**, serta **Titik Lokasi Peta** dan **Buka di Google Maps** bila lokasi diisi.

> 📸 **SCREENSHOT W-46: Halaman detail agenda**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/agenda/ lalu nama agenda
> - **Kondisi:** halaman penuh "Rapat Pengurus dan Warga RW 21" dengan pilihan **Hadir** terpilih
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tulisan "n Warga Hadir" dan tiga tombol kehadiran
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-46-halaman-detail-agenda.png`

✅ **Hasilnya:** Pilihan kehadiran Anda tersimpan dan dihitung dalam jumlah **Warga Hadir**. Operator konten dapat melihat jumlahnya.

⚠️ **Perhatian**

- **Tidak ada tombol Hadir/Tidak/Ragu pada agenda.** Agenda itu tidak membuka konfirmasi kehadiran.
- **"Gagal menyimpan respons kehadiran."** Koneksi bermasalah. Coba tekan lagi.
- **Agenda RT lain tidak terlihat.** Anda hanya melihat agenda RW dan agenda RT Anda sendiri.
- **Agenda yang sudah lewat tampil abu-abu.** Itu menandakan kegiatan sudah selesai.

> ➡️ **Cerita berlanjut:** Operator Konten RW mengunggah dokumentasi setelah acara. Lihat `07-Op-Konten-RW.md`, cerita C-09 bagian 3.

---

## 9. Pojok UMKM

Menu **Pojok UMKM** (`/galeri`) adalah etalase usaha warga sekaligus tempat mendaftar dan mengelola usaha. Hanya usaha yang **disetujui RW dan aktif** yang tampil bagi umum.

### 9.1 Melihat produk dan toko

Isi halaman:

- Judul **Pojok UMKM** dan tombol hijau **Daftarkan Usaha Anda** serta **Kelola UMKM Anda** (khusus peran Warga).
- **Produk Unggulan RW 21** dan **Produk Terbaru WargaDigi**, masing-masing dengan tautan **Lihat Semua**.
- Kotak pencarian "Cari Toko / Produk Berdasarkan Nama" dan pilihan **Semua Kategori**.
- Kartu produk dan nomor halaman. Bila tidak ada hasil: "Tidak ada produk ditemukan" dengan tombol **Reset Pencarian**.

Klik produk untuk membuka detail (harga, deskripsi, usaha, dan tombol hubungi penjual lewat WhatsApp). Jumlah akses produk bertambah setiap kali produk dibuka (satu kali per sesi).

> 👤 **SIMULASI W-K: Pak Budi Santoso mencari kerajinan kayu**
>
> Pak **Budi Santoso** ingin membeli hadiah untuk kerabat.
>
> - **Masuk sebagai:** `budisantoso`
> - **Produk yang dilihat:** "Patung Kayu Jati Tradisional" (Rp350.000) dari usaha "Kriya Kayu Jati Tanimulya" milik Pak Budi Sampurno

**1. Buka Pojok UMKM.**
Klik **Pojok UMKM** di menu samping.

> 📸 **SCREENSHOT W-47: Pojok UMKM (tampilan Warga)**
> - **Akun:** budisantoso (Warga)
> - **URL:** /galeri
> - **Kondisi:** halaman baru dibuka
> - **Area:** satu layar penuh dengan tombol **Daftarkan Usaha Anda**, **Kelola UMKM Anda**, dan **Produk Unggulan RW 21**
> - **Sorot:** kotak merah pada kedua tombol hijau dan kotak pencarian
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-47-pojok-umkm-warga.png`, `W-47m-pojok-umkm-warga.png`

**2. Cari produk.**
Ketik `patung` pada kotak pencarian lalu tekan **Cari**. Klik produk "Patung Kayu Jati Tradisional".

> 📸 **SCREENSHOT W-48: Detail produk**
> - **Akun:** budisantoso (Warga)
> - **URL:** /galeri/produk/ lalu nomor produk (atau /produk/ lalu nomor produk)
> - **Kondisi:** detail "Patung Kayu Jati Tradisional" terbuka
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada harga dan tombol hubungi penjual
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-48-detail-produk.png`

✅ **Hasilnya:** Anda melihat informasi produk dan dapat menghubungi penjual langsung.

---

### 9.2 Mendaftarkan usaha

Pendaftaran usaha hanya untuk peran Warga. Usaha baru berstatus **Pending** sampai Admin RW menyetujuinya. Selama Pending, Anda belum dapat menambah produk.

> 👤 **SIMULASI C-05 (bagian 1 dari 3): Mendaftarkan usaha jasa service**
>
> Pak **Budi Sampurno** ingin mempromosikan jasa service elektroniknya di Pojok UMKM.
>
> - **Masuk sebagai:** `budisampurno`, kata sandi `password`, peran **Kepala Keluarga**
> - **Nama usaha:** Jasa Service Elektronik Pak Budi
> - **Kategori:** Jasa
> - **Deskripsi:** Perbaikan AC, mesin cuci, kulkas, dan peralatan elektronik rumah tangga dengan garansi pengerjaan.
> - **Alamat:** Gg. Kenanga No. 45, RT 03 / RW 21, Desa Tanimulya (dari profil keluarga)
> - **Catatan data demo:** pendaftaran ini sudah tercatat berstatus Pending. Untuk memotret formulir, isi dengan nama lain (misalnya `Contoh Usaha`) atau cukup buka formulir tanpa mengirim, agar data demo tidak bertambah.
> - **Selanjutnya:** Admin RW meninjau. Lihat `05-Admin-RW.md`, cerita C-05 bagian 2.

**1. Buka formulir pendaftaran.**
Di **Pojok UMKM**, tekan **Daftarkan Usaha Anda**. Jendela **Daftarkan Usaha Anda** terbuka.

**2. Isi Informasi Bisnis.**
**Nama Usaha**, **Kategori** (pilih dari daftar; kategori **Koperasi** sementara tidak bisa dipilih), dan **Deskripsi Singkat**.

**3. Isi Kontak & Lokasi.**
- **Alamat Usaha (RT/RW):** ketik alamat, atau centang **Gunakan alamat di profil** untuk memakai alamat KK Anda. Alamat profil tampil di samping kotak centang.
- **Nomor Kontak Bisnis (Nomor WA):** ketik nomor tanpa awalan 0 (awalan +62 sudah tertulis), atau centang **Gunakan nomor kontak di profil** untuk memakai nomor WhatsApp keluarga.

> 📸 **SCREENSHOT W-49: Jendela Daftarkan Usaha Anda**
> - **Akun:** budisampurno (peran Kepala Keluarga)
> - **URL:** /galeri (jendela terbuka; alamat /warga/galeri/daftar mengalihkan ke sini)
> - **Kondisi:** jendela terbuka dan formulir terisi nama usaha, kategori **Jasa**, dan deskripsi
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada kolom **Nama Usaha**, **Kategori**, dan tombol **Kirim Pendaftaran**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-49-daftar-usaha.png`, `W-49m-daftar-usaha.png`

> 📸 **SCREENSHOT W-50: Kotak centang alamat dan kontak dari profil**
> - **Akun:** budisampurno (peran Kepala Keluarga)
> - **URL:** /galeri (jendela **Daftarkan Usaha Anda**)
> - **Kondisi:** kedua kotak **Gunakan alamat di profil** dan **Gunakan nomor kontak di profil** dicentang
> - **Area:** bagian **Kontak & Lokasi**
> - **Sorot:** kotak merah pada kedua kotak centang dan alamat/nomor yang tampil di sampingnya
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-50-ceklis-profil.png`

**4. Tambahkan foto sampul (opsional).**
Pada **Media**, pilih **Upload Foto Sampul** (JPG, PNG, WEBP, atau GIF, maksimal 5 MB).

**5. Kirim.**
Tekan **Kirim Pendaftaran** (atau **Batal**).

✅ **Hasilnya:**

- Muncul pesan "Pendaftaran usaha (nama usaha) berhasil dikirim dan berstatus Pending menunggu verifikasi dari Pengurus RW."
- Anda dibawa ke halaman **Kelola UMKM**. Admin RW dan Ketua RW menerima notifikasi **UMKM Baru Menunggu Verifikasi**.
- Di Dashboard muncul kotak kuning **Pendaftaran Usaha Anda Sedang Ditinjau RW**.

⚠️ **Perhatian**

- **"Nomor WA di profil belum diisi. Isi nomor kontak bisnis secara manual."** Ceklis nomor profil tidak bisa dipakai karena nomor WA keluarga belum terisi. Ketik nomor sendiri.
- **"Alamat di profil belum diisi. Isi alamat usaha secara manual."** Ketik alamat usaha sendiri.
- **"Kategori usaha wajib dipilih."** Pilih salah satu kategori.
- **"Kategori usaha tidak valid atau sedang tidak dapat dipilih."** Kategori itu (misalnya Koperasi) sementara tidak tersedia. Pilih kategori lain.
- **"Anda harus login untuk mendaftarkan usaha."** Akun Anda belum tertaut ke data penduduk. Hubungi Ketua RT.
- **Tombol Daftarkan Usaha Anda tidak muncul.** Tombol hanya tampil bila peran aktif Anda adalah Warga. Untuk akun dua peran, gunakan **Ganti Peran**.

> ➡️ **Cerita berlanjut:** Admin RW meninjau dan menyetujui. Lihat `05-Admin-RW.md`, cerita C-05 bagian 2.

---

### 9.3 Mengelola usaha dan produk

Halaman **Kelola UMKM Saya** (`/warga/galeri/kelola`, juga lewat tombol **Kelola UMKM Anda**) adalah ruang kerja pemilik usaha.

Isi halaman:

- **Kepala usaha:** nama usaha, nama pemilik dan RT, serta label status (**Terverifikasi RW 12**, **Menunggu Verifikasi**, atau **Verifikasi Ditolak**).
- **Ganti Usaha (n):** daftar semua usaha Anda untuk berpindah toko. Usaha berstatus **Pending** diberi tanda dan belum bisa dibuka bila Anda sudah punya usaha yang disetujui; usaha ditolak diberi tanda **Ditolak**; usaha dinonaktifkan RW diberi tanda **Nonaktif**.
- Tombol **Kembali ke Pojok UMKM**.
- Ringkasan: **TOTAL PRODUK AKTIF**, **TOTAL PRODUK TIDAK AKTIF**, dan **PEMINAT WHATSAPP** (jumlah klik hubungi).
- Sampul toko dengan tulisan **Toko Sedang Aktif** atau **Toko Non-Aktif**, serta tombol **Edit Data UMKM** dan **Ganti Foto Sampul**.
- **Produk Teratas & Katalog Etalase** dengan tombol **Tambah Produk Baru**, kotak pencarian, filter **Semua Kategori** dan **Semua Status** (Aktif, Tidak Aktif, Stok Tersedia, Stok Menipis, Stok Habis).
- Kartu produk: status (Aktif atau Tidak Aktif), nama, deskripsi, **Harga**, **Status Stok**, tautan hubungi, ikon pensil **Edit Produk**, dan ikon tempat sampah **Hapus Produk**.

Usaha yang dinonaktifkan atau diaktifkan kembali hanya dapat dilakukan **Admin RW**. Pemilik tidak punya tombol untuk itu (lihat 9.4).

> 👤 **SIMULASI W-L: Pak Budi Santoso menambah produk baru**
>
> Pak **Budi Santoso** menambahkan menu baru ke Warung Nasi Ibu Ani.
>
> - **Masuk sebagai:** `budisantoso`
> - **Usaha:** Warung Nasi Ibu Ani (pilih lewat **Ganti Usaha** bila belum terpilih)
> - **Produk baru:** Nasi Liwet Komplit, kategori **Makanan**, harga `28000`, status stok **Tersedia**
> - **Deskripsi:** Nasi liwet dengan lauk ayam, tahu, tempe, dan lalapan.
> - **Foto:** satu foto JPG kecil

**1. Buka Kelola UMKM.**
Di **Pojok UMKM**, tekan **Kelola UMKM Anda**.

> 📸 **SCREENSHOT W-51: Halaman Kelola UMKM Saya**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola
> - **Kondisi:** usaha **Warung Nasi Ibu Ani** terpilih, daftar produk tampil
> - **Area:** satu layar penuh dari kepala usaha sampai ringkasan dan awal katalog
> - **Sorot:** kotak merah pada label status, tombol **Edit Data UMKM**, **Ganti Foto Sampul**, dan **Tambah Produk Baru**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-51-kelola-umkm.png`, `W-51m-kelola-umkm.png`

**2. Ganti usaha bila perlu.**
Tekan **Ganti Usaha (n)** dan pilih toko. Daftar menampilkan tanda **Pending**, **Ditolak**, atau **Nonaktif** pada usaha tertentu.

> 📸 **SCREENSHOT W-52: Daftar Ganti Usaha dengan tanda Pending**
> - **Akun:** budisampurno (peran Kepala Keluarga)
> - **URL:** /warga/galeri/kelola
> - **Kondisi:** daftar **Pilih Usaha Anda** terbuka; "Kriya Kayu Jati Tanimulya" terpilih dan "Jasa Service Elektronik Pak Budi" bertanda **Pending**
> - **Area:** daftar **Pilih Usaha Anda**
> - **Sorot:** kotak merah pada tanda **Pending**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-52-ganti-usaha.png`, `W-52m-ganti-usaha.png`

**3. Tekan Tambah Produk Baru.**
Jendela **Tambah Produk Baru** terbuka.

**4. Isi data produk.**
- **Unggah Foto Produk:** tarik gambar ke kotak atau pilih berkas (JPG, PNG, WEBP, atau GIF, maksimal 5 MB). Foto wajib.
- **Nama Produk**, **Status Stok** (Tersedia, Menipis, Habis), **Kategori**, **Harga**, dan **Deskripsi Produk**.
- **Kategori:** pilih yang sudah ada, atau pilih **Kategori Baru** dan ketik namanya; kategori baru otomatis dibuat dan tersimpan.

> 📸 **SCREENSHOT W-53: Jendela Tambah Produk Baru**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola
> - **Kondisi:** jendela terbuka dan formulir terisi data "Nasi Liwet Komplit"
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada **Unggah Foto Produk**, **Status Stok**, **Harga**, dan tombol **Simpan Produk**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-53-tambah-produk.png`, `W-53m-tambah-produk.png`

**5. Tekan Simpan Produk.**

✅ **Hasilnya:** Muncul pesan `Produk UMKM "Nasi Liwet Komplit" berhasil ditambahkan.` Produk tampil di katalog dan di Pojok UMKM.

**Mengubah produk.** Tekan ikon pensil **Edit Produk** pada kartu. Jendela **Edit Produk** memuat **Foto Produk** (tombol **Ganti Foto**), **Nama Produk**, **Status Produk** (Aktif atau Tidak Aktif), **Status Stok**, **Kategori**, **Harga (Rp)**, dan **Deskripsi Produk**. Tekan **Simpan Perubahan**. Pesan: `Produk UMKM "..." berhasil diperbarui.` Gunakan **Status Produk** Tidak Aktif untuk menyembunyikan produk tanpa menghapusnya.

> 📸 **SCREENSHOT W-54: Jendela Edit Produk**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola
> - **Kondisi:** jendela **Edit Produk** terbuka untuk "Paket Nasi Timbel Ayam Bakar"
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada **Status Produk**, **Status Stok**, dan tombol **Simpan Perubahan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-54-edit-produk.png`

**Menghapus produk.** Tekan ikon tempat sampah **Hapus Produk**. Jendela konfirmasi bertanya "Anda yakin untuk menghapus produk ini?" Konfirmasi untuk menghapus. Pesan: `Produk "..." berhasil dihapus.` Penghapusan tidak bisa dibatalkan.

> 📸 **SCREENSHOT W-55: Konfirmasi hapus produk**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola
> - **Kondisi:** ikon **Hapus Produk** pada "Es Cendol Dawet Ayu" sudah ditekan; jendela konfirmasi terbuka (jangan dikonfirmasi)
> - **Area:** jendela konfirmasi
> - **Sorot:** kotak merah pada tombol konfirmasi dan tombol batal
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-55-hapus-produk.png`

**Mengubah data usaha.** Tekan **Edit Data UMKM**. Isi: **Nama Usaha**, **Kategori**, **Alamat Usaha (RT/RW)**, **Nomor Kontak Bisnis (Nomor WA)**, dan **Deskripsi Usaha** (boleh memakai kotak centang profil seperti saat mendaftar). Tekan **Simpan Perubahan**. Pesan: "Data profil UMKM berhasil diperbarui."

> 📸 **SCREENSHOT W-56: Jendela Edit Data UMKM**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola
> - **Kondisi:** jendela **Edit Data UMKM** terbuka untuk "Warung Nasi Ibu Ani"
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada **Nama Usaha**, **Deskripsi Usaha**, dan tombol **Simpan Perubahan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-56-edit-data-umkm.png`

**Mengganti foto sampul.** Tekan **Ganti Foto Sampul**, pilih berkas pada **Pilih Foto Sampul Baru**, lalu tekan **Upload Sampul**. Pesan: "Foto sampul UMKM berhasil diperbarui."

> 📸 **SCREENSHOT W-57: Jendela Ganti Foto Sampul**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola
> - **Kondisi:** jendela **Ganti Foto Sampul** terbuka
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada **Pilih Foto Sampul Baru** dan tombol **Upload Sampul**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-57-ganti-sampul.png`

⚠️ **Perhatian**

- **"Usaha ... belum disetujui. Produk baru dapat ditambahkan setelah usaha disetujui oleh Admin RW."** Tombol tambah produk terkunci selama usaha Pending atau Ditolak. Tunggu persetujuan.
- **"Silakan daftarkan usaha terlebih dahulu sebelum menambah produk."** Anda belum punya usaha. Daftarkan dulu (9.2).
- **Pesan kolom foto wajib saat menambah produk.** Pilih foto produk.
- **Peringatan ukuran foto.** Batas 5 MB per foto.
- **Pesan validasi tampil dalam bahasa Inggris.** Itu bawaan sistem; artinya ada kolom wajib atau format yang salah. Periksa kolom bertanda bintang.
- **Anda tidak punya tombol Nonaktifkan.** Usaha hanya dinonaktifkan oleh Admin RW.
- **"Anda tidak memiliki hak akses ..."** Anda mencoba mengubah usaha atau produk milik orang lain.

---

### 9.4 Status usaha dan mengajukan ulang

Status usaha tampil pada label di kepala halaman Kelola UMKM dan pada tanda di daftar **Ganti Usaha**.

| Status | Tampilan | Artinya | Yang bisa Anda lakukan |
|---|---|---|---|
| **Terverifikasi** | Label hijau | Disetujui RW, tampil di Pojok UMKM | Kelola produk dan data |
| **Menunggu Verifikasi** (Pending) | Kotak "Pendaftaran Usaha ... Sedang Ditinjau RW", tombol **Penambahan Produk Dikunci** | Menunggu Admin RW | Tunggu persetujuan |
| **Verifikasi Ditolak** | Kotak merah "Pendaftaran Usaha ... Ditolak", **Alasan:** | Ditolak RW | Perbaiki lalu **Perbaiki & Ajukan Ulang** |
| **Dinonaktifkan oleh RW** | Kotak "Usaha Dinonaktifkan oleh RW" dengan alasan | Usaha dan produk disembunyikan dari Pojok UMKM | Hubungi pengurus RW; Admin RW yang mengaktifkan kembali |

Catatan: usaha berstatus Pending hanya bisa dibuka halaman kelolanya bila Anda belum punya usaha yang disetujui. Bila Anda sudah punya usaha yang disetujui, status Pending terlihat lewat tanda di **Ganti Usaha** dan kotak kuning di Dashboard.

> 👤 **SIMULASI C-06 (bagian 1 dari 3): Memperbaiki usaha yang ditolak**
>
> Pendaftaran **Koperasi Warga Tanimulya** milik Pak **Budi Santoso** ditolak karena kurang lengkap. Ia memperbaiki deskripsi lalu mengajukan ulang.
>
> - **Masuk sebagai:** `budisantoso`, kata sandi `password`
> - **Usaha:** Koperasi Warga Tanimulya (kategori Koperasi)
> - **Alasan penolakan (data demo):** Lengkapi deskripsi pengurus koperasi dan foto tempat usaha, lalu ajukan ulang.
> - **Perbaikan:** deskripsi diisi `Simpan pinjam warga dan penjualan sembako. Pengurus: Budi Santoso (ketua) dan Siti Rahma (bendahara).`, lalu foto sampul diganti
> - **Selanjutnya:** Admin RW meninjau pengajuan ulang. Lihat `05-Admin-RW.md`, cerita C-06 bagian 2.

**1. Pilih usaha yang ditolak.**
Di **Kelola UMKM Saya**, tekan **Ganti Usaha (3)**, lalu pilih **Koperasi Warga Tanimulya** (bertanda **Ditolak**).

**2. Baca alasan penolakan.**
Kotak merah memuat **Pendaftaran Usaha "Koperasi Warga Tanimulya" Ditolak**, **Alasan:**, dan kalimat "Perbaiki data usaha sesuai catatan, lalu ajukan ulang untuk ditinjau kembali oleh Admin RW."

> 📸 **SCREENSHOT W-58: Usaha ditolak beserta alasannya**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola (usaha "Koperasi Warga Tanimulya" terpilih)
> - **Kondisi:** kotak merah penolakan tampil sebelum diperbaiki; label **Verifikasi Ditolak**; tombol **Tambah Produk (Terkunci)**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada **Alasan:** dan tombol **Perbaiki & Ajukan Ulang**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-58-usaha-ditolak.png`, `W-58m-usaha-ditolak.png`

**3. Tekan Perbaiki & Ajukan Ulang.**
Jendela **Edit Data UMKM** terbuka dengan tombol akhir **Simpan & Ajukan Ulang**. Ubah **Deskripsi Usaha** sesuai catatan. Anda juga dapat mengganti foto lewat **Ganti Foto Sampul** (seperti 9.3).

> 📸 **SCREENSHOT W-59: Jendela Edit Data UMKM mode ajukan ulang**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola (usaha "Koperasi Warga Tanimulya")
> - **Kondisi:** jendela terbuka dengan **Deskripsi Usaha** sudah diperbaiki
> - **Area:** satu jendela penuh
> - **Sorot:** kotak merah pada **Deskripsi Usaha** dan tombol **Simpan & Ajukan Ulang**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `W-59-ajukan-ulang.png`, `W-59m-ajukan-ulang.png`

**4. Tekan Simpan & Ajukan Ulang.**

✅ **Hasilnya:**

- Muncul pesan "Usaha Koperasi Warga Tanimulya berhasil diajukan ulang dan menunggu verifikasi Admin RW."
- Status usaha kembali **Pending**, catatan penolakan dihapus, dan Admin RW serta Ketua RW menerima notifikasi **UMKM Diajukan Ulang**.

> 📸 **SCREENSHOT W-60: Setelah mengajukan ulang**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/galeri/kelola
> - **Kondisi:** jendela pesan keberhasilan tampil; di **Ganti Usaha**, "Koperasi Warga Tanimulya" kini bertanda **Pending**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan keberhasilan dan tanda **Pending**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `W-60-ajukan-ulang-berhasil.png`

⚠️ **Perhatian**

- **"Hanya usaha yang ditolak yang dapat diajukan ulang."** Usaha Anda sedang tidak berstatus ditolak.
- **Usaha dinonaktifkan RW.** Kotak "Usaha Dinonaktifkan oleh RW" muncul, dan usaha serta produk hilang dari Pojok UMKM. Hubungi pengurus RW; Warga tidak bisa mengaktifkannya sendiri.
- **Kategori "Koperasi" tidak muncul saat mendaftar usaha baru.** Kategori itu sementara dinonaktifkan; usaha lama yang sudah berkategori Koperasi tetap bisa diperbaiki dan diajukan ulang.

> ➡️ **Cerita berlanjut:** Admin RW meninjau pengajuan ulang. Lihat `05-Admin-RW.md`, cerita C-06 bagian 2.

---

## 10. Keluar

Tekan **Keluar** (merah) di bagian bawah menu samping. Di HP, tekan **☰** dulu supaya menu muncul. Sesi berakhir dan Anda kembali ke halaman masuk. Rincian ada di `00-Umum-Publik-dan-Login.md`, bab 3.4.

> 💡 **Tips:** Gunakan **Keluar** setiap kali selesai memakai HP atau komputer orang lain.

---

## 11. Cerita Lengkap (Lintas Peran)

Tabel ini memuat cerita yang melibatkan Warga. "Bagian Warga" adalah bagian yang ada di buku ini. Bagian peran lain ada di buku masing-masing.

| Kode | Cerita | Bagian Warga (buku ini) | Peran dan buku lain |
|---|---|---|---|
| **C-01** | Surat Keterangan Domisili untuk bank | Bab 4.2 (bagian 1), 4.4 dan 4.5 (bagian 3: pantau dan unduh PDF) | Ketua RT 03: `02-Ketua-RT.md` (bagian 2); pengurus RW mengesahkan: `05-Admin-RW.md` atau `06-Ketua-RW.md` |
| **C-02** | Surat ditolak lalu diajukan ulang | Bab 4.6 (bagian 1 mengajukan, bagian 3 memperbaiki dan mengajukan ulang) | Ketua RT menolak: `02-Ketua-RT.md` (bagian 2 dan 3) |
| **C-03** | Membayar Iuran Perbaikan Got | Bab 5.2 (bagian 1) | Bendahara RT 03 memeriksa: `04-Op-Keuangan-RT.md` (bagian 2); setoran RT ke RW: `08-Op-Keuangan-RW.md` (bagian 3) |
| **C-04** | Membayar Iuran Bulanan RT 03 | Bab 5.1 (bagian 1) | Bendahara RT 03 memeriksa: `04-Op-Keuangan-RT.md` (bagian 2); setoran ke RW: `08-Op-Keuangan-RW.md` |
| **C-05** | Usaha jasa service menunggu persetujuan | Bab 2.2, 9.2 (pemilik: Pak Budi Sampurno) | Admin RW menyetujui: `05-Admin-RW.md` (bagian 2) |
| **C-06** | Koperasi ditolak, diperbaiki, diajukan ulang | Bab 9.4 (bagian 1) | Admin RW meninjau: `05-Admin-RW.md` (bagian 2) |
| **C-09** | Agenda, konfirmasi hadir, dokumentasi | Bab 8 (bagian 2: konfirmasi hadir) | Operator Konten RW menyiapkan agenda (bagian 1) dan galeri (bagian 3): `07-Op-Konten-RW.md` |
| **C-10** | Perubahan data anggota keluarga | Bab 3.4 dan 3.5 (bagian 1) | Ketua RT meninjau: `02-Ketua-RT.md` (bagian 2); RW menyetujui: `05-Admin-RW.md` (bagian 3) |

---

## 12. Masalah yang Sering Terjadi (FAQ)

**Saya tidak bisa mengajukan surat: muncul "Data penduduk tidak ditemukan."**
Akun Anda belum tertaut ke data penduduk keluarga. Minta Ketua RT memeriksa data Anda.

**Tombol Ajukan Perubahan KK, Tambah Anggota, Detail, dan Edit tidak muncul di Profil Keluarga.**
Hanya pemegang akun keluarga yang boleh mengubah data. Periksa kotak **Akun Keluarga** untuk melihat siapa pemegang akunnya.

**Data diri di formulir surat salah dan tidak bisa saya ubah.**
Itu disengaja. Ajukan perubahan lewat **Profil Keluarga** (bab 3). Setelah RW menyetujui, data di formulir surat ikut berubah.

**Berapa lama surat selesai?**
Tergantung pemeriksaan Ketua RT dan RW. Pantau lewat kotak **Sedang Diproses**, Dashboard, atau lonceng. Anda menerima notifikasi **Surat Disetujui RT** dan **Surat Terbit**.

**Surat saya ditolak. Apa yang harus dilakukan?**
Baca alasannya di **Riwayat Pengajuan Surat** tab **Ditolak**, perbaiki data atau lampiran, lalu buat pengajuan baru dari formulir (bab 4.6).

**Saya tidak menemukan tombol unduh surat.**
Tombol **Unduh PDF** hanya ada pada surat berstatus **Selesai**, di jendela **Riwayat Pengajuan Surat**.

**Pembayaran saya ditolak.**
Baca alasannya di kartu bulan (Iuran Bulanan) atau di baris tagihan (Iuran Lainnya), lalu bayar atau ajukan ulang dengan bukti yang jelas.

**Kenapa iuran RW dibayar ke rekening RT?**
Beberapa iuran RW dikumpulkan lewat RT, ditandai "Dari RW, dibayar lewat RT". RT yang menerima, memeriksa, lalu menyetor secara kolektif ke RW.

**Bukti transfer saya tidak bisa diunggah.**
Pastikan formatnya JPG, PNG, atau WEBP (untuk Iuran Bulanan hanya JPG atau PNG) dan ukurannya tidak lebih dari 5 MB.

**Apakah saya bisa mengunduh laporan keuangan?**
Tidak. Warga hanya melihat ringkasan angka di menu **Laporan Keuangan**.

**Tab RT di Laporan Keuangan bertuliskan "belum ada".**
Laporan RT Anda belum diterbitkan oleh pengurus. Tab RW dan DKM tetap dapat dibuka bila sudah terbit.

**Saya tidak melihat tombol Hadir pada agenda.**
Agenda itu tidak membuka konfirmasi kehadiran.

**Usaha saya tidak muncul di Pojok UMKM.**
Usaha harus berstatus disetujui dan aktif. Lihat status di **Kelola UMKM Saya** (bab 9.4).

**Saya tidak bisa menambah produk.**
Penambahan produk terkunci selama usaha belum disetujui (Pending atau Ditolak).

**Pesan kesalahan tampil dalam bahasa Inggris.**
Beberapa pesan validasi bawaan sistem memang berbahasa Inggris. Isi artinya sama: ada kolom wajib yang kosong atau format salah. Periksa kolom bertanda bintang.

**Saya lupa kata sandi.**
Hubungi Ketua RT atau Admin Aplikasi. Tautan **Lupa password?** di halaman masuk belum aktif (lihat `00-Umum-Publik-dan-Login.md`, bab 3.5).

---

## 13. Lampiran: Daftar Gambar

| Kode | Judul | Perangkat | Nama file |
|---|---|---|---|
| W-01 | Peta menu Warga | Desktop, mobile | `W-01-peta-menu-warga.png`, `W-01m-peta-menu-warga.png` |
| W-02 | Dashboard Warga setelah masuk | Desktop, mobile | `W-02-dashboard-setelah-masuk.png`, `W-02m-dashboard-setelah-masuk.png` |
| W-03 | Dashboard Warga milik akun dua peran | Desktop, mobile | `W-03-dashboard-akun-dua-peran.png`, `W-03m-dashboard-akun-dua-peran.png` |
| W-04 | Dashboard bagian bawah (berita, pengumuman, kalender, UMKM) | Desktop, mobile | `W-04-dashboard-bagian-bawah.png`, `W-04m-dashboard-bagian-bawah.png` |
| W-05 | Halaman Profil Keluarga | Desktop, mobile | `W-05-profil-keluarga.png`, `W-05m-profil-keluarga.png` |
| W-06 | Formulir Ajukan Perubahan Data KK | Desktop | `W-06-ajukan-perubahan-kk.png` |
| W-07 | Formulir Tambah Anggota Keluarga | Desktop, mobile | `W-07-tambah-anggota.png`, `W-07m-tambah-anggota.png` |
| W-08 | Detail Penduduk | Desktop | `W-08-detail-penduduk.png` |
| W-09 | Formulir Ajukan Perubahan Data Penduduk | Desktop, mobile | `W-09-ajukan-perubahan-anggota.png`, `W-09m-ajukan-perubahan-anggota.png` |
| W-10 | Riwayat Pengajuan dan jendela Detail | Desktop, mobile | `W-10-riwayat-pengajuan-detail.png`, `W-10m-riwayat-pengajuan-detail.png` |
| W-11 | Formulir Pindahkan Pemegang Akun | Desktop | `W-11-pindah-pemegang-akun.png` |
| W-12 | Halaman Layanan Permohonan Surat | Desktop, mobile | `W-12-halaman-permohonan-surat.png`, `W-12m-halaman-permohonan-surat.png` |
| W-13 | Formulir: pemohon dan data diri | Desktop | `W-13-formulir-data-diri.png` |
| W-14 | Keperluan, isian tambahan, dan dokumen | Mobile | `W-14m-keperluan-dan-dokumen.png` |
| W-15 | Pernyataan dan tombol kirim | Desktop | `W-15-kirim-pengajuan.png` |
| W-16 | Pengajuan berhasil, status Menunggu RT | Desktop, mobile | `W-16-pengajuan-berhasil.png`, `W-16m-pengajuan-berhasil.png` |
| W-17 | Memilih pemohon anggota keluarga | Desktop | `W-17-pilih-pemohon-anggota.png` |
| W-18 | Isian tambahan khusus jenis surat | Mobile | `W-18m-isian-tambahan-surat.png` |
| W-19 | Status Peninjauan Surat, Menunggu RT | Desktop, mobile | `W-19-status-menunggu-rt.png`, `W-19m-status-menunggu-rt.png` |
| W-20 | Status Peninjauan Surat, Menunggu RW | Desktop | `W-20-status-menunggu-rw.png` |
| W-21 | Kotak pemberitahuan surat di Dashboard | Desktop, mobile | `W-21-dashboard-pemberitahuan-surat.png`, `W-21m-dashboard-pemberitahuan-surat.png` |
| W-22 | Riwayat Pengajuan Surat, tab Selesai | Desktop, mobile | `W-22-riwayat-surat-selesai.png`, `W-22m-riwayat-surat-selesai.png` |
| W-23 | Tombol Riwayat Pengajuan Surat | Desktop | `W-23-tombol-riwayat-surat.png` |
| W-24 | Contoh hasil PDF surat | Desktop | `W-24-contoh-hasil-pdf.png` |
| W-25 | Riwayat Pengajuan Surat, tab Ditolak | Desktop, mobile | `W-25-riwayat-surat-ditolak.png`, `W-25m-riwayat-surat-ditolak.png` |
| W-26 | Formulir pengajuan ulang setelah ditolak | Desktop | `W-26-formulir-ajukan-ulang.png` |
| W-27 | Iuran Bulanan, kartu 12 bulan | Desktop, mobile | `W-27-iuran-bulanan.png`, `W-27m-iuran-bulanan.png` |
| W-28 | Jendela Bayar Iuran | Desktop, mobile | `W-28-bayar-iuran-bulanan.png`, `W-28m-bayar-iuran-bulanan.png` |
| W-29 | Kartu bulan Menunggu verifikasi | Desktop, mobile | `W-29-iuran-menunggu-verifikasi.png`, `W-29m-iuran-menunggu-verifikasi.png` |
| W-30 | Kartu bulan Ditolak | Desktop | `W-30-iuran-ditolak.png` |
| W-31 | Daftar Iuran Lainnya | Desktop, mobile | `W-31-iuran-lainnya.png`, `W-31m-iuran-lainnya.png` |
| W-32 | Jendela pembayaran, tab Transfer Bank | Desktop, mobile | `W-32-bayar-tab-transfer.png`, `W-32m-bayar-tab-transfer.png` |
| W-33 | Jendela pembayaran, tab QRIS | Desktop | `W-33-bayar-tab-qris.png` |
| W-34 | Jendela pembayaran, tab Cash | Desktop | `W-34-bayar-tab-cash.png` |
| W-35 | Tagihan Menunggu Konfirmasi | Desktop, mobile | `W-35-tagihan-menunggu-konfirmasi.png`, `W-35m-tagihan-menunggu-konfirmasi.png` |
| W-36 | Hasil akhir tagihan (Lunas) | Desktop | `W-36-tagihan-lunas.png` |
| W-37 | Transparansi Keuangan, unit RW | Desktop, mobile | `W-37-laporan-keuangan-rw.png`, `W-37m-laporan-keuangan-rw.png` |
| W-38 | Unit RT belum punya laporan terbit | Desktop | `W-38-laporan-rt-belum-ada.png` |
| W-39 | Rincian kategori dan per bulan | Desktop | `W-39-laporan-rincian.png` |
| W-40 | Halaman Pusat Informasi | Desktop, mobile | `W-40-pusat-informasi.png`, `W-40m-pusat-informasi.png` |
| W-41 | Tab Pengumuman | Desktop | `W-41-pusat-informasi-pengumuman.png` |
| W-42 | Detail berita di Pusat Informasi | Desktop | `W-42-detail-berita.png` |
| W-43 | Halaman Agenda Warga | Desktop, mobile | `W-43-agenda-warga.png`, `W-43m-agenda-warga.png` |
| W-44 | Jendela detail agenda | Desktop | `W-44-detail-agenda-modal.png` |
| W-45 | Setelah menekan Hadir | Desktop, mobile | `W-45-rsvp-hadir.png`, `W-45m-rsvp-hadir.png` |
| W-46 | Halaman detail agenda | Desktop | `W-46-halaman-detail-agenda.png` |
| W-47 | Pojok UMKM (tampilan Warga) | Desktop, mobile | `W-47-pojok-umkm-warga.png`, `W-47m-pojok-umkm-warga.png` |
| W-48 | Detail produk | Desktop | `W-48-detail-produk.png` |
| W-49 | Jendela Daftarkan Usaha Anda | Desktop, mobile | `W-49-daftar-usaha.png`, `W-49m-daftar-usaha.png` |
| W-50 | Kotak centang alamat dan kontak dari profil | Desktop | `W-50-ceklis-profil.png` |
| W-51 | Halaman Kelola UMKM Saya | Desktop, mobile | `W-51-kelola-umkm.png`, `W-51m-kelola-umkm.png` |
| W-52 | Daftar Ganti Usaha dengan tanda Pending | Desktop, mobile | `W-52-ganti-usaha.png`, `W-52m-ganti-usaha.png` |
| W-53 | Jendela Tambah Produk Baru | Desktop, mobile | `W-53-tambah-produk.png`, `W-53m-tambah-produk.png` |
| W-54 | Jendela Edit Produk | Desktop | `W-54-edit-produk.png` |
| W-55 | Konfirmasi hapus produk | Desktop | `W-55-hapus-produk.png` |
| W-56 | Jendela Edit Data UMKM | Desktop | `W-56-edit-data-umkm.png` |
| W-57 | Jendela Ganti Foto Sampul | Desktop | `W-57-ganti-sampul.png` |
| W-58 | Usaha ditolak beserta alasannya | Desktop, mobile | `W-58-usaha-ditolak.png`, `W-58m-usaha-ditolak.png` |
| W-59 | Jendela Edit Data UMKM mode ajukan ulang | Desktop, mobile | `W-59-ajukan-ulang.png`, `W-59m-ajukan-ulang.png` |
| W-60 | Setelah mengajukan ulang | Desktop | `W-60-ajukan-ulang-berhasil.png` |
