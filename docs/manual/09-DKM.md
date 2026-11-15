# Manual Book Pengurus Keuangan DKM

Panduan untuk **pengurus keuangan DKM** (Dewan Kemakmuran Masjid) di WargaDigi 21 (https://wargadigi21.com). Akun DKM mengelola **dana kematian** warga RW 21: mencatat donasi dan penyaluran, menagih **Iuran Kematian**, mengatur rekening, serta menyusun laporan keuangan DKM.

<!-- AI-INSTRUKSI
- Ikuti konvensi blok di README.md (SIMULASI, SCREENSHOT, Hasilnya, Perhatian, Tips, Cerita berlanjut).
- Kode gambar awalan DKM, nomor urut sesuai Lampiran. Semua gambar desktop 1366x768.
- Akun: dkm / password. Tokoh: Pak Haji Rahmat (fiktif). Di aplikasi hanya username yang tampil.
- Data demo: rekening DKM BCA 34521980 a.n. "DKM RW 21"; Iuran Lainnya "Iuran Kematian" Rp20.000 (nominal minimal), kategori Donasi Kematian, tenggat sekitar 7 hari dari tanggal data dibuat; transaksi demo DKM "Sumbangan Dana Kematian" Rp500.000 (pemasukan, Donasi Kematian); laporan DKM berstatus Approved dan Published untuk bulan ini dan dua bulan sebelumnya.
- Kategori yang berlaku di DKM pada data demo: Donasi Kematian (pemasukan), Penyaluran Dana Kematian (pengeluaran), Pengadaan barang (pengeluaran). Kategori umum (mis. Operasional & ATK) TIDAK tampil di akun DKM.
- Contoh nominal pada simulasi penyaluran dan pengadaan hanya ilustrasi untuk gambar, bukan data demo.
- Tulisan seperti Draft, Pending Review, Approved, dan nama bulan Inggris pada kolom PERIODE memang tampil apa adanya di layar.
-->

## Daftar Isi

1. Kenalan Dulu
   - 1.1 Tugas Pengurus Keuangan DKM
   - 1.2 Tokoh dalam Panduan Ini
   - 1.3 Peta Menu
2. Mulai Menggunakan
   - 2.1 Masuk ke Aplikasi
   - 2.2 Membaca Dashboard Keuangan DKM
3. Catat Transaksi (Buku Kas DKM)
   - 3.1 Membaca Buku Kas
   - 3.2 Mencatat Donasi Dana Kematian
   - 3.3 Menyalurkan Dana Kematian dan Mencatat Pengadaan Barang
   - 3.4 Mengubah dan Menghapus Transaksi
4. Iuran Kematian
   - 4.1 Melihat dan Membuat Iuran Lainnya
   - 4.2 Memverifikasi Pembayaran Warga
5. Pengaturan Rekening
6. Kategori Transaksi DKM
7. Laporan Keuangan DKM
   - 7.1 Menyusun Laporan Bulanan (C-11 bagian 1)
   - 7.2 Mengajukan, Mempublikasikan, dan Mengunduh (C-11 bagian 2)
8. Cerita Lengkap (Lintas Peran)
9. Masalah yang Sering Terjadi (FAQ)
10. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### 1.1 Tugas Pengurus Keuangan DKM

Sebagai pengurus keuangan DKM, Anda bertugas:

- Mencatat **donasi dana kematian** yang masuk.
- Mencatat **penyaluran dana kematian** kepada keluarga warga yang berduka, dan **pengadaan barang** untuk keperluan DKM.
- Membuat dan memantau **Iuran Kematian** untuk seluruh KK, serta memverifikasi pembayarannya.
- Mengatur **rekening bank dan QRIS** DKM yang dilihat warga saat membayar.
- Menyusun **laporan keuangan DKM** bulanan, mengajukannya, dan mempublikasikannya.

Anda hanya melihat dan mengubah data **unit DKM**. Kas RW dan kas RT tidak tampil di akun ini.

### 1.2 Tokoh dalam Panduan Ini

> 👤 **SIMULASI: Pak Haji Rahmat, pengurus keuangan DKM**
>
> **Pak Haji Rahmat** (tokoh fiktif) memegang dana kematian warga RW 21. Setiap ada kabar duka, ia menyalurkan santunan dan mencatatnya agar warga bisa melihat pertanggungjawabannya.
>
> - **Masuk sebagai:** `dkm`, kata sandi `password`
> - **Cerita yang dimainkan:** C-11 (laporan keuangan versi DKM) dan simulasi penyaluran dana kematian
> - **Tokoh lain:** Pak Budi Santoso (warga RT 03 yang membayar Iuran Kematian), serta Ketua RW atau Admin RW yang menyetujui laporan

### 1.3 Peta Menu

| Menu | Gunanya |
|---|---|
| **Dashboard** | Ringkasan kas DKM dan pekerjaan yang menunggu |
| **Catat Transaksi** | Buku kas DKM: catat, ubah, hapus transaksi |
| **Laporan Keuangan** | Menyusun, mengajukan, mempublikasikan, dan mengunduh laporan |
| **Iuran Warga** | Grup menu yang di akun DKM hanya berisi **Iuran Lainnya** |
| **Kategori Transaksi** | Mengatur kategori pengeluaran DKM |
| **Pengaturan Rekening** | Rekening bank dan QRIS DKM |
| **Keluar** | Keluar dari aplikasi |

Menu **Iuran Wajib** dan **Setoran** tidak ada pada akun DKM. Iuran Wajib bulanan dan setoran adalah urusan RT dan RW.

> 📸 **SCREENSHOT DKM-01: Menu samping DKM**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/dashboard
> - **Kondisi:** grup **Iuran Warga** dibuka sehingga **Iuran Lainnya** terlihat
> - **Area:** menu samping kiri dari atas sampai **Keluar**
> - **Sorot:** kotak merah mengelilingi seluruh daftar menu, nomor 1 sampai 6 di samping tiap menu
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-01-menu-samping.png`

---

## 2. Mulai Menggunakan

### 2.1 Masuk ke Aplikasi

Buka https://wargadigi21.com, lalu masuk dengan akun `dkm` dan kata sandi `password`. Langkah masuk, memilih peran, dan keluar dijelaskan di `00-Umum-Publik-dan-Login.md`. Akun ini punya satu peran, jadi Anda langsung masuk ke dashboard keuangan.

### 2.2 Membaca Dashboard Keuangan DKM

Halaman **Dashboard Keuangan DKM** menampilkan angka dari kas DKM saja:

- **Perlu Tindakan:** misalnya **Pembayaran iuran DKM menunggu verifikasi**, **Laporan ... belum dibuat**, **Laporan ditolak, perlu revisi**, **Laporan draft belum diajukan**. Bila kosong tertulis *Tidak ada pekerjaan yang tertunda.*
- **Laporan Terakhir** dengan tautan **Kelola**.
- **Saldo Kas DKM**, **Pemasukan** bulan ini, **Pengeluaran** bulan ini, **Selisih bulan ini**.
- Grafik **Pemasukan vs Pengeluaran Kas DKM (6 bulan)**.
- **Iuran DKM Aktif** dengan progres (tautan **Kelola**).
- **Pemasukan per Kategori** dan **Pengeluaran per Kategori** bulan ini.
- **Transaksi Terbaru** (tautan **Lihat semua**).

> 📸 **SCREENSHOT DKM-02: Dashboard Keuangan DKM**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/dashboard
> - **Kondisi:** data demo awal
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada **Perlu Tindakan**, **Saldo Kas DKM**, dan **Iuran DKM Aktif**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-02-dashboard-keuangan-dkm.png`

> 💡 **Tips:** Lihat **Saldo Kas DKM** sebelum menyalurkan dana. Aplikasi tidak menolak pengeluaran yang melebihi saldo, jadi Andalah yang menjaga agar kas tidak minus.

---

## 3. Catat Transaksi (Buku Kas DKM)

### 3.1 Membaca Buku Kas

Menu **Catat Transaksi** berisi buku kas DKM. **Debet** berarti uang masuk, **Kredit** berarti uang keluar, kolom **Saldo** berjalan sejak awal pencatatan.

Penyaring: **Tahun**, **Bulan**, **Tipe Transaksi** (**Semua Tipe**, **Pemasukan**, **Pengeluaran**), dan **Kategori**, dengan tombol **Filter** dan **Reset Filter**. Tanpa penyaring, halaman menampilkan **bulan berjalan**.

Kolom tabel: **TANGGAL**, **KETERANGAN**, **DEBET**, **KREDIT**, **SALDO**, **AKSI**. Baris **Total periode terpilih** memuat saldo awal, total debet, total kredit, dan **Saldo akhir**.

> 📸 **SCREENSHOT DKM-03: Buku kas DKM**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** bulan berjalan, transaksi demo "Sumbangan Dana Kematian" terlihat (bila jatuh di bulan sebelumnya, pilih **Bulan** yang sesuai lalu **Filter**)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada penyaring, kolom **DEBET**/**KREDIT**/**SALDO**, dan baris **Total periode terpilih**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-03-buku-kas-dkm.png`

### 3.2 Mencatat Donasi Dana Kematian

> 👤 **SIMULASI: Donasi dana kematian dari donatur**
>
> Seorang donatur menyerahkan sumbangan untuk dana kematian lewat **Pak Haji Rahmat**. Ia mencatatnya sebagai pemasukan DKM.
>
> - **Masuk sebagai:** `dkm`, kata sandi `password`
> - **Tipe:** Pemasukan
> - **Judul:** Sumbangan Dana Kematian
> - **Kategori:** Donasi Kematian
> - **Tanggal:** hari ini atau sebelumnya
> - **Jumlah:** Rp 500.000
> - **Deskripsi:** Sumbangan dari donatur anonim
> - **Catatan:** transaksi dengan judul dan nominal ini sudah ada pada data demo. Untuk gambar, cukup isi formulir dan ambil gambar sebelum menekan simpan.

**Langkah-langkah**

**1. Buka Catat Transaksi** dan tekan **Catat Transaksi Baru**.

**2. Pilih tipe.**
Pada **Tipe Transaksi** pilih **Pemasukan (+)**.

**3. Isi Judul Transaksi.**
Ketik *Sumbangan Dana Kematian*.

**4. Pilih Kategori.**
Buka **-- Pilih kategori --**. Untuk pemasukan, pada akun DKM hanya tersedia **Donasi Kematian**. Kategori umum seperti **Iuran Bulanan Warga** tidak muncul di akun DKM.

**5. Isi Tanggal dan Jumlah (Rp).**
Tanggal tidak boleh melewati hari ini. Jumlah: *500.000*.

**6. Isi Deskripsi (Opsional)** dan lampirkan bukti bila ada. Tarik file ke kotak **Drag & drop file Anda disini atau klik untuk browse**, atau tekan **Ambil Foto**. Format JPG, PNG, PDF, maksimal 5 MB.

**7. Tekan Simpan Transaksi.**

> 📸 **SCREENSHOT DKM-04: Catat donasi dana kematian**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/transaksi (jendela **Catat Transaksi Baru**)
> - **Kondisi:** **Pemasukan (+)** terpilih, kategori **Donasi Kematian**, formulir terisi
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Tipe Transaksi**, **Kategori**, **Jumlah (Rp)**, dan tombol **Simpan Transaksi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-04-catat-donasi.png`

### ✅ Hasilnya

- Muncul pesan **"Transaksi berhasil dicatat."**
- Transaksi tampil di buku kas dengan kode `TRX-DKM-...`, nominal di kolom **DEBET**, dan saldo berjalan bertambah.

### ⚠️ Perhatian

- **"Terjadi kesalahan validasi:"** Satu atau lebih isian kurang. Baca daftar di bawah tulisan itu.
- **"Kategori tidak valid atau tidak sesuai dengan tipe transaksi."** Kategori tidak cocok dengan tipe. Pilih ulang kategori setelah memilih tipe.
- **Kategori yang Anda cari tidak ada.** Akun DKM hanya memakai kategori yang berlaku di DKM (lihat Bab 6).
- **"Tidak dapat mencatat transaksi pada periode ... karena laporan keuangan periode tersebut sudah diajukan, disetujui, atau dipublikasikan."** Periode itu terkunci. Lihat 7.2.

> 💡 **Tips:** Untuk donatur yang ingin dirahasiakan, tulis "donatur anonim" pada **Deskripsi** dan jangan cantumkan nama pada **Judul Transaksi**, karena laporan yang dipublikasikan dapat dibaca warga.

> ➡️ **Cerita berlanjut:** Lanjut ke 3.3 untuk penyaluran dana.

### 3.3 Menyalurkan Dana Kematian dan Mencatat Pengadaan Barang

> 👤 **SIMULASI: Penyaluran dana kematian**
>
> Seorang warga RT 03 meninggal dunia. Pengurus DKM sepakat menyalurkan santunan dari dana kematian kepada keluarga yang berduka. **Pak Haji Rahmat** menyerahkan uangnya, meminta tanda terima, lalu mencatat pengeluaran itu.
>
> - **Masuk sebagai:** `dkm`
> - **Tipe:** Pengeluaran
> - **Judul:** Santunan keluarga almarhum warga RT 03
> - **Kategori:** Penyaluran Dana Kematian
> - **Tanggal:** hari penyerahan
> - **Jumlah:** Rp 1.000.000 (contoh)
> - **Deskripsi:** Santunan dana kematian sesuai kesepakatan pengurus
> - **Bukti:** foto tanda terima dari keluarga

**Langkah-langkah**

**1. Cek saldo.**
Lihat **Saldo Kas DKM** di Dashboard. Pastikan dana mencukupi.

**2. Buka Catat Transaksi Baru** dan pilih **Pengeluaran (-)**.

**3. Isi Judul Transaksi** seperlunya.

**4. Pilih Kategori.**
Untuk pengeluaran, akun DKM melihat dua pilihan: **Penyaluran Dana Kematian** (santunan) dan **Pengadaan barang** (pembelian keperluan DKM). Pilih **Penyaluran Dana Kematian**.

**5. Isi Tanggal dan Jumlah (Rp), lampirkan bukti**, lalu tekan **Simpan Transaksi**.

**Pengadaan barang** dicatat dengan cara yang sama. Contoh: judul *Pengadaan perlengkapan pemulasaraan*, kategori **Pengadaan barang**, tipe **Pengeluaran (-)**.

> 📸 **SCREENSHOT DKM-05: Catat penyaluran dana kematian**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/transaksi (jendela **Catat Transaksi Baru**)
> - **Kondisi:** **Pengeluaran (-)** terpilih, daftar kategori terbuka menunjukkan **Penyaluran Dana Kematian** dan **Pengadaan barang**
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada daftar **Kategori** (dua pilihan pengeluaran) dan kotak bukti
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-05-catat-penyaluran.png`

### ✅ Hasilnya

- Pesan **"Transaksi berhasil dicatat."**
- Nominal tampil di kolom **KREDIT**, saldo berjalan berkurang, dan **Pengeluaran per Kategori** di Dashboard memuat **Penyaluran Dana Kematian**.

> 📸 **SCREENSHOT DKM-06: Penyaluran tercatat di buku kas**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** transaksi penyaluran tampil dengan lencana kategori dan **Lampiran Bukti**
> - **Area:** tabel buku kas
> - **Sorot:** kotak merah pada baris penyaluran, kolom **KREDIT**, dan **SALDO**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-06-penyaluran-tercatat.png`

### ⚠️ Perhatian

- **Saldo menjadi merah atau minus.** Aplikasi tidak menolak pengeluaran yang melebihi saldo. Periksa lagi nominal dan saldo.
- **Kategori "Penyaluran Dana Kematian" tidak muncul.** Pastikan **Tipe Transaksi** sudah **Pengeluaran (-)**. Kategori ini tidak tampil untuk pemasukan.
- **Kategori itu dibuat Operator Keuangan RW.** Bila hilang dari daftar, kategori mungkin dinonaktifkan atau tidak lagi berlaku di DKM. Hubungi Operator Keuangan RW (`08-Op-Keuangan-RW.md`).

> 💡 **Tips:** Selalu minta tanda terima dari penerima santunan. Foto tanda terima menjadi bukti yang kuat saat laporan diperiksa.

> ➡️ **Cerita berlanjut:** Lanjut ke 3.4.

### 3.4 Mengubah dan Menghapus Transaksi

Pada kolom **AKSI**, tekan tombol titik tiga (⋮):

- **Lihat Bukti:** membuka lampiran bukti.
- **Edit:** membuka jendela **Edit Transaksi**; tekan **Simpan Perubahan**. Pesan: **"Transaksi berhasil diperbarui."**
- **Hapus:** muncul **Hapus Transaksi** ("Apakah Anda yakin ingin menghapus transaksi ini?"), tekan **Hapus**. Pesan: **"Transaksi berhasil dihapus."** Penghapusan permanen.

> 📸 **SCREENSHOT DKM-07: Menu aksi transaksi**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** tombol titik tiga pada satu transaksi sudah ditekan
> - **Area:** baris transaksi dan menu yang terbuka
> - **Sorot:** kotak merah pada **Lihat Bukti**, **Edit**, dan **Hapus**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-07-menu-aksi-transaksi.png`

### ⚠️ Perhatian

- **Edit dan Hapus abu-abu dengan lencana "Terkunci".** Laporan keuangan periode itu sudah diajukan, disetujui, atau dipublikasikan.
- **"Transaksi ini berasal dari Iuran Warga atau Setoran RT. Ubah atau batalkan melalui menu Iuran Warga / Setoran."** Pembayaran Iuran Kematian yang disetujui menghasilkan transaksi otomatis. Transaksi itu tidak diubah dari buku kas.
- **"Tanggal transaksi tidak dapat dipindahkan ke periode ..."** Periode tujuan sudah terkunci.

> 💡 **Tips:** Bila ada salah catat setelah laporan diajukan, tunggu laporan **Ditolak** supaya periodenya terbuka lagi, lalu perbaiki.

> ➡️ **Cerita berlanjut:** Bab 4 membahas Iuran Kematian.

---

## 4. Iuran Kematian

Menu **Iuran Warga**, lalu **Iuran Lainnya**, dipakai untuk iuran sekali bayar. Pada data demo ada satu iuran DKM: **Iuran Kematian** dengan nominal minimal **Rp 20.000**. Iuran disebar ke seluruh KK di RW dan dibayar **langsung ke DKM** (rekening DKM), jadi Andalah yang memverifikasi pembayarannya.

### 4.1 Melihat dan Membuat Iuran Lainnya

Tabel berisi **Judul Iuran**, **Nominal**, **Tenggat**, **Progress Terbayar** (misalnya 5/40), dan tombol **Detail**. Lencana **Nominal fix** atau **Minimal** menunjukkan jenis nominal.

> 👤 **SIMULASI: Iuran Kematian**
>
> Dana kematian menipis setelah beberapa penyaluran. **Pak Haji Rahmat** membuat **Iuran Kematian** Rp20.000 per KK agar dana terisi kembali.
>
> - **Masuk sebagai:** `dkm`
> - **Judul Iuran:** Iuran Kematian
> - **Nominal:** Rp 20.000 (minimal)
> - **Kategori Kas:** Donasi Kematian
> - **Tenggat:** sekitar seminggu ke depan
> - **Catatan:** iuran ini sudah ada pada data demo. Untuk gambar, isi formulir dan ambil gambar sebelum menekan **Buat & Sebar**.
> - **Selanjutnya:** Pak Budi Santoso (warga) membayar. Lihat `01-Warga.md`, bagian Iuran Lainnya.

**Langkah-langkah**

**1. Buka Iuran Warga, lalu Iuran Lainnya**, kemudian tekan **Buat Iuran Lainnya**.

**2. Isi Judul Iuran dan Nominal (Rp).**
Nominal adalah **nominal minimal**. Centang **Nominal fix (sesuai tertera, tidak bisa diubah)** bila nominalnya pasti. Warga boleh membayar lebih bila tidak fix.

**3. Pilih Kategori Kas (pemasukan).**
Pada akun DKM pilih **Donasi Kematian**.

**4. Isi Tenggat Waktu dan Tanggal Pengingat.**
**Tanggal Pengingat** adalah tanggal tiap bulan (1 sampai 31) saat pengingat dikirim ke warga yang belum bayar.

**5. Isi Deskripsi (Opsional)** lalu tekan **Buat & Sebar**.

Pilihan **Dikumpulkan lewat RT** tidak ada pada akun DKM. Iuran DKM selalu dibayar langsung ke DKM.

> 📸 **SCREENSHOT DKM-08: Daftar Iuran Lainnya DKM**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/iuran
> - **Kondisi:** "Iuran Kematian" tampil dengan lencana **Minimal Rp 20.000**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Buat Iuran Lainnya**, lencana **Minimal**, **Progress Terbayar**, dan tombol **Detail**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-08-daftar-iuran-kematian.png`

> 📸 **SCREENSHOT DKM-09: Jendela Buat Iuran Lainnya**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/iuran (jendela **Buat Iuran Lainnya**)
> - **Kondisi:** formulir terisi seperti simulasi, tanpa pilihan **Dikumpulkan lewat RT**
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Nominal (Rp)**, **Kategori Kas (pemasukan)**, **Tenggat Waktu**, dan tombol **Buat & Sebar**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-09-buat-iuran.png`

### ✅ Hasilnya

- Muncul pesan **"Tagihan insidental berhasil dibuat dan disebar ke N warga."** (N = jumlah KK).
- Iuran tampil di daftar dengan progres 0 dari jumlah KK, dan di Dashboard bagian **Iuran DKM Aktif**.
- Warga melihat iuran ini di menu **Iuran Lainnya** pada akun mereka.

### ⚠️ Perhatian

- **"Nominal wajib diisi sebagai nominal minimal iuran."** Isi kolom nominal.
- **"Tagihan gagal dibuat: tidak ada warga/keluarga pada unit ini untuk ditagih."** Belum ada data KK.
- **Iuran sudah disebar tidak bisa dibatalkan dari halaman ini.** Periksa nominal dan judul sebelum menekan **Buat & Sebar**.

> 💡 **Tips:** Pilih **Nominal fix** bila besaran iuran sudah disepakati, supaya laporan lebih rapi. Biarkan tidak fix bila warga yang mampu ingin menyumbang lebih.

> ➡️ **Cerita berlanjut:** Lanjut ke 4.2.

### 4.2 Memverifikasi Pembayaran Warga

> 👤 **SIMULASI: Pak Budi Santoso membayar Iuran Kematian**
>
> **Pak Budi Santoso** mentransfer Rp20.000 ke rekening DKM lalu mengirim fotonya lewat menu **Iuran Lainnya** di akunnya. **Pak Haji Rahmat** memeriksa bukti dan menyetujuinya.
>
> - **Masuk sebagai:** `dkm`
> - **Iuran:** Iuran Kematian
> - **Pembayar:** Budi Santoso, metode Transfer, dengan foto bukti
> - **Sebelumnya:** Pak Budi mengajukan pembayaran. Lihat `01-Warga.md`

**Langkah-langkah**

**1. Buka detail iuran.**
Di **Iuran Lainnya**, tekan **Detail** pada "Iuran Kematian". Ada dua tab: **Daftar & Catat** (semua KK) dan **Pembayaran Warga** (pengajuan yang menunggu, dengan lencana jumlah).

**2. Buka tab Pembayaran Warga.**
Baris warga berstatus **Menunggu Verifikasi**. Tekan **Periksa**.

**3. Periksa jendela Detail Pembayaran.**
Terlihat **Warga**, **No. KK**, **Tagihan**, **Nominal**, **Metode**, **Waktu kirim**, dan bukti. Bukti bisa dibuka lebih besar lewat **Buka Gambar Penuh**. Bila tidak ada bukti tertulis *Tidak ada bukti pembayaran dilampirkan.*

**4. Setujui atau tolak.**
- **Setujui:** nominal masuk ke kas DKM sebagai pemasukan.
- **Tolak:** tekan **Tolak**, isi **Alasan penolakan** (misalnya *Bukti transfer buram atau nominal tidak sesuai*), lalu **Kirim Penolakan**.

**Mencatat pembayaran tunai.** Pada tab **Daftar & Catat**, baris berstatus **Belum Bayar** punya tombol **Catat Bayar**. Isi **Nominal (Rp)**, **Tanggal Bayar**, **Metode** (**Tunai**, **Transfer**, **QRIS**), bukti (opsional), lalu **Simpan & Masuk Kas**.

**Mengingatkan.** Tombol lonceng (**Kirim Pengingat (Broadcast)**) mengirim pengingat ke KK yang **Belum Bayar**.

> 📸 **SCREENSHOT DKM-10: Verifikasi pembayaran Iuran Kematian**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/iuran/{id} (tab **Pembayaran Warga**, jendela **Detail Pembayaran**)
> - **Kondisi:** pengajuan Pak Budi berstatus **Menunggu Verifikasi**, bukti terlihat
> - **Area:** jendela detail pembayaran
> - **Sorot:** kotak merah pada bukti pembayaran, tombol **Tolak**, dan tombol **Setujui**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-10-verifikasi-pembayaran.png`

> 📸 **SCREENSHOT DKM-11: Daftar KK dan tombol Catat Bayar**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/iuran/{id} (tab **Daftar & Catat**)
> - **Kondisi:** ada KK berstatus **Lunas**, **Menunggu Verifikasi**, dan **Belum Bayar**
> - **Area:** tabel daftar KK
> - **Sorot:** kotak merah pada kolom **Status** dan tombol **Catat Bayar** serta lonceng pengingat
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-11-daftar-kk-iuran.png`

### ✅ Hasilnya

- Setelah **Setujui**: pesan **"Pembayaran berhasil disetujui dan dicatat ke keuangan."** Status warga menjadi **Lunas**, dan transaksi pemasukan tercatat di kas DKM dengan kategori **Donasi Kematian**.
- Setelah **Tolak**: pesan **"Pembayaran berhasil ditolak."** Status kembali **Belum Bayar** dengan keterangan **Ditolak**, dan warga diberi tahu alasannya.
- Setelah **Catat Bayar**: pesan **"Pembayaran dicatat dan masuk ke kas."**

### ⚠️ Perhatian

- **"Nominal pembayaran tidak boleh kurang dari nominal minimal (Rp ...)."** Isi nominal minimal sama dengan tagihan. Pada iuran **Nominal fix**, nominal terkunci.
- **"Pembayaran ini sudah lunas."** Baris itu sudah diselesaikan.
- **"Warga ini belum memiliki akun aktif, pengingat tidak dapat dikirim."** KK belum punya akun.
- **"Notifikasi aplikasi sedang dinonaktifkan di Pengaturan Sistem."** Minta Admin Aplikasi mengaktifkannya.
- **"Tagihan sudah dibayar atau sedang diverifikasi."** Pengingat hanya untuk KK yang **Belum Bayar**.
- **Penolakan tidak terkirim.** Kolom **Alasan penolakan** wajib diisi, maksimal 500 karakter.

> 💡 **Tips:** Cocokkan nominal pada bukti dengan mutasi rekening BCA DKM sebelum menekan **Setujui**.

> ➡️ **Cerita berlanjut:** Bab 5 membahas rekening.

---

## 5. Pengaturan Rekening

Menu **Pengaturan Rekening** menyimpan rekening bank dan QRIS DKM yang dilihat warga saat membayar Iuran Kematian.

> 👤 **SIMULASI: Memeriksa rekening DKM**
>
> **Pak Haji Rahmat** memastikan data rekening DKM masih benar.
>
> - **Nama Bank:** BCA
> - **Nomor Rekening:** 34521980
> - **Atas Nama:** DKM RW 21
> - **QRIS:** belum ada (opsional)

**Langkah-langkah**

**1. Buka Pengaturan Rekening.**

**2. Isi atau periksa kolom** **Nama Bank**, **Nomor Rekening**, **Atas Nama** (semuanya wajib).

**3. Unggah QRIS (opsional).**
Pada **Upload QRIS (Opsional)** pilih gambar JPG, PNG, atau WEBP, maksimal 5 MB. Pratinjau muncul pada **Preview QRIS Baru yang Dipilih**. QRIS tersimpan tampil pada **QRIS Saat Ini**.

**4. Tekan Simpan Pengaturan.**

> 📸 **SCREENSHOT DKM-12: Pengaturan Rekening DKM**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/rekening
> - **Kondisi:** rekening BCA 34521980 terisi
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada **Nama Bank**, **Nomor Rekening**, **Atas Nama**, **Upload QRIS (Opsional)**, dan tombol **Simpan Pengaturan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-12-pengaturan-rekening.png`

### ✅ Hasilnya

- Pesan **"Pengaturan rekening berhasil disimpan."**

### ⚠️ Perhatian

- **"Nama bank wajib diisi."**, **"Nomor rekening wajib diisi."**, **"Atas nama rekening wajib diisi."** Lengkapi kolom yang diminta.
- **"Gagal memproses file QRIS: ..."** File QRIS rusak atau terlalu besar. Unggah ulang gambar yang lebih kecil.

> 💡 **Tips:** Umumkan perubahan rekening lewat pengurus supaya warga tidak transfer ke rekening lama.

> ➡️ **Cerita berlanjut:** Bab 6 membahas kategori transaksi.

---

## 6. Kategori Transaksi DKM

Menu **Kategori Transaksi** menampilkan kategori yang bisa dipakai pada kas DKM. Aturannya berbeda dengan RT dan RW:

- Kategori **umum** buatan Admin Aplikasi (misalnya **Operasional & ATK**) **tidak tampil** di akun DKM.
- Kategori **pemasukan** DKM dibuat oleh **Operator Keuangan RW**, lalu tampil di akun DKM dengan lencana **Dari RW** dan keterangan **Dikelola RW**. Contoh: **Donasi Kematian**.
- Kategori **pengeluaran** DKM bisa dibuat sendiri oleh DKM. Tipe dikunci **Pengeluaran**. Contoh: **Pengadaan barang**.
- **Penyaluran Dana Kematian** (pengeluaran) dibuat oleh Operator Keuangan RW dan berlaku di DKM.

Penyaring: **Cari Nama Kategori**, **Tipe**, **Asal**, dan **Status**. Kolom: **NAMA KATEGORI**, **TIPE**, **ASAL**, **STATUS**, **AKSI**.

| Kategori | Tipe | Asal di akun DKM |
|---|---|---|
| **Donasi Kematian** | Pemasukan | Dari RW (dikelola RW) |
| **Penyaluran Dana Kematian** | Pengeluaran | Dari RW (dikelola RW) |
| **Pengadaan barang** | Pengeluaran | Unit DKM (bisa Anda ubah) |

> 👤 **SIMULASI: Menambah kategori pengeluaran DKM**
>
> DKM sering membiayai konsumsi tahlilan. **Pak Haji Rahmat** ingin kategori sendiri agar laporan lebih rinci.
>
> - **Masuk sebagai:** `dkm`
> - **Nama Kategori:** Konsumsi Tahlilan
> - **Tipe:** Pengeluaran (terkunci)
> - **Aktif:** ya

**Langkah-langkah**

**1. Buka Kategori Transaksi** dan tekan **Tambah Kategori**.

**2. Isi Nama Kategori** (maksimal 100 karakter). **Tipe** otomatis **Pengeluaran** dengan keterangan *Kategori DKM hanya untuk pengeluaran. Kategori pemasukan DKM dibuat oleh Operator Keuangan RW.*

**3. Biarkan Aktif tercentang** lalu tekan **Simpan**.

> 📸 **SCREENSHOT DKM-13: Daftar Kategori Transaksi DKM**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/kategori-transaksi
> - **Kondisi:** tiga kategori demo tampil, dengan keterangan **Dikelola RW** dan tombol ubah/hapus hanya pada **Pengadaan barang**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kolom **ASAL** dan kolom **AKSI**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-13-daftar-kategori.png`

> 📸 **SCREENSHOT DKM-14: Jendela Tambah Kategori DKM**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/kategori-transaksi (jendela **Tambah Kategori**)
> - **Kondisi:** nama *Konsumsi Tahlilan* terisi, **Tipe** terkunci **Pengeluaran**
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Nama Kategori**, kotak **Tipe** yang terkunci, dan tombol **Simpan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-14-tambah-kategori-dkm.png`

### ✅ Hasilnya

- Pesan **"Kategori transaksi berhasil ditambahkan."**
- Kategori langsung muncul sebagai pilihan pengeluaran di **Catat Transaksi** DKM. Kategori DKM tidak berlaku di RW.
- Ikon pensil membuka **Edit Kategori** (pesan sukses **"Kategori transaksi berhasil diperbarui."**). Ikon tempat sampah membuka **Hapus Kategori**. Bila kategori sudah dipakai transaksi atau iuran, kategori **dinonaktifkan** (pesan **"Kategori sudah dipakai transaksi, sehingga dinonaktifkan (bukan dihapus) agar rekap tetap utuh."**), bukan dihapus. Bila belum dipakai: **"Kategori transaksi berhasil dihapus."**

### ⚠️ Perhatian

- **Tombol ubah dan hapus tidak ada pada Donasi Kematian atau Penyaluran Dana Kematian.** Kategori itu milik RW. Minta Operator Keuangan RW mengubahnya.
- **Tidak bisa membuat kategori pemasukan.** Tipe DKM dikunci **Pengeluaran**. Minta Operator Keuangan RW membuat kategori pemasukan dan mencentang **DKM** pada **Berlaku di**.
- **Kategori "Iuran Kematian" tidak ada.** Itu nama iuran, bukan kategori. Kategori kas untuk iuran ini adalah **Donasi Kematian**.

> 💡 **Tips:** Beri nama kategori yang jelas dan singkat. Rekap per kategori di Dashboard dan laporan memakai nama ini.

> ➡️ **Cerita berlanjut:** Bab 7 membahas laporan keuangan DKM.

---

## 7. Laporan Keuangan DKM

Menu **Laporan Keuangan** merangkum arus kas DKM per bulan. Alurnya:

**Draft** (Anda susun dan periksa) → **Pending Review** (diajukan, menunggu persetujuan) → **Approved** (disetujui) → **Published** (dipublikasikan ke warga). Bila ditolak, statusnya **Ditolak**, Anda memperbaiki lalu mengajukan lagi.

**Siapa yang menyetujui?** Menurut kode aplikasi, laporan DKM disetujui oleh **Ketua RW atau Admin RW** (sama dengan laporan RW). Notifikasi pengajuan dikirim ke Ketua RW dan Admin RW.

| Label di layar | Artinya | Yang bisa Anda lakukan |
|---|---|---|
| **Draft** | Baru disusun, belum diajukan | Ajukan, Unduh, Hapus |
| **Pending Review** | Menunggu Ketua RW atau Admin RW | Unduh, Hapus |
| **Ditolak** | Dikembalikan dengan catatan | Ajukan ulang, Unduh, Hapus |
| **Approved** | Disetujui, belum tampil ke warga | Publikasikan, Unduh |
| **Published** | Tampil ke warga | Unduh |

### 7.1 Menyusun Laporan Bulanan (C-11 bagian 1)

> 👤 **SIMULASI C-11 (versi DKM): Laporan dana kematian (bagian 1 dari 3)**
>
> Awal bulan, **Pak Haji Rahmat** menyusun laporan keuangan DKM untuk bulan yang sudah lewat, supaya warga tahu dana kematian masuk dan keluar ke mana. Data demo sudah memuat laporan yang disetujui untuk tiga bulan terakhir, jadi ia menyusun laporan untuk bulan yang lebih lama.
>
> - **Masuk sebagai:** `dkm`, kata sandi `password`
> - **Periode:** empat bulan sebelum bulan ini (di luar tiga laporan demo)
> - **Selanjutnya:** ia mengajukan laporan (7.2). Ketua RW atau Admin RW menyetujuinya. Lihat `06-Ketua-RW.md` dan `05-Admin-RW.md`, C-11.

**Langkah-langkah**

**1. Buka Laporan Keuangan.**
Penyaring: **Tahun** (**Semua Tahun**, 2026, 2025) dan **Status**, dengan tombol **Filter**.

**2. Tekan Generate Laporan Baru.**
Kotak biru menjelaskan bahwa laporan dibuat berstatus **Draft** supaya Anda dapat meninjau angka sebelum mengajukannya.

**3. Pilih Bulan dan isi Tahun.**
Pilihan awal: bulan **Oktober**, tahun **2026**. Ubah sesuai periode.

**4. Tekan Generate Laporan.**

> 📸 **SCREENSHOT DKM-15: Daftar Laporan Keuangan DKM**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** data demo awal, tiga laporan DKM berstatus **Published**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Generate Laporan Baru**, penyaring, dan kolom **STATUS**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-15-daftar-laporan.png`

> 📸 **SCREENSHOT DKM-16: Jendela Generate Laporan Baru**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/laporan (jendela **Generate Laporan Baru**)
> - **Kondisi:** bulan dan tahun sudah dipilih
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada kotak informasi biru, **Bulan**, **Tahun**, dan tombol **Generate Laporan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-16-generate-laporan.png`

### ✅ Hasilnya

- Muncul pesan **"Laporan berhasil dibuat dalam status Draft. Silakan review laporan terlebih dahulu, lalu klik tombol "Ajukan" untuk mengirimkannya ke Ketua."**
- Laporan tampil dengan **PERIODE**, **JUDUL LAPORAN**, **TOTAL PEMASUKAN**, **TOTAL PENGELUARAN**, **SALDO AKHIR**, dan status **Draft**. Angka dihitung dari transaksi kas DKM pada bulan itu, dengan saldo awal dari bulan-bulan sebelumnya.

### ⚠️ Perhatian

- **"Laporan untuk periode ini sudah ada (Status: ...)."** Sudah ada laporan untuk bulan itu. Pilih bulan lain.
- **Nama bulan berbahasa Inggris.** Kolom **PERIODE** dan judul laporan memakai nama seperti *October 2026*. Itu perilaku aplikasi saat ini.
- **Pilihan Tahun pada penyaring hanya 2025 dan 2026.** Gunakan **Semua Tahun** untuk yang lain.
- **Laporan yang ditolak lalu di-generate ulang.** Laporan lama yang ditolak otomatis dihapus dan diganti **Draft** baru dengan angka terbaru.

> 💡 **Tips:** Periksa dulu **Catat Transaksi** bulan itu: apakah semua donasi dan penyaluran sudah tercatat lengkap dengan bukti. Setelah diajukan, transaksi pada bulan itu terkunci.

> ➡️ **Cerita berlanjut:** Lanjut ke 7.2.

### 7.2 Mengajukan, Mempublikasikan, dan Mengunduh (C-11 bagian 2)

> 👤 **SIMULASI C-11 (versi DKM): Laporan dana kematian (bagian 2 dan 3)**
>
> **Pak Haji Rahmat** meninjau draf, lalu mengajukannya. Setelah Ketua RW atau Admin RW menyetujui, ia mempublikasikannya agar warga bisa membaca di menu **Laporan Keuangan** akun mereka, dan mengunduh PDF untuk arsip DKM.
>
> - **Masuk sebagai:** `dkm`
> - **Laporan:** Laporan Keuangan DKM untuk periode yang baru dibuat
> - **Selanjutnya:** warga membaca laporan. Lihat `01-Warga.md`

**Langkah-langkah**

**1. Ajukan.**
Pada baris laporan **Draft** atau **Ditolak**, tekan ikon pesawat kertas (tooltip **Ajukan ke Ketua RT**; maksudnya mengajukan ke penyetuju). Muncul **Ajukan Laporan**: *Apakah Anda yakin ingin mengajukan laporan ini ke Ketua untuk persetujuan?* Tekan **Ajukan**.

**2. Tunggu persetujuan.**
Status menjadi **Pending Review**. Bila ditolak, catatan penolakan tampil di bawah judul laporan (*Catatan: ...*), dan Anda menerima notifikasi **Laporan Keuangan Ditolak**.

**3. Publikasikan.**
Setelah **Approved**, tekan ikon unggah (tooltip **Publikasikan ke warga**). Pada **Publikasikan Laporan** (*Laporan yang dipublish dapat dilihat oleh Warga.*) tekan **Publikasikan**.

**4. Unduh.**
Ikon PDF (**Unduh PDF**, terbuka di tab baru) dan ikon lembar kerja (**Unduh Excel**) tersedia untuk semua status.

**5. Hapus (bila perlu).**
Ikon tempat sampah (**Hapus laporan**) untuk laporan yang belum **Approved** dan belum dipublikasikan. Tekan **Hapus** pada **Hapus Laporan**.

> 📸 **SCREENSHOT DKM-17: Konfirmasi Ajukan Laporan**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** laporan berstatus **Draft**, kotak **Ajukan Laporan** tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada ikon ajukan di kolom **AKSI** dan tombol **Ajukan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-17-ajukan-laporan.png`

> 📸 **SCREENSHOT DKM-18: Laporan Approved dengan tombol Publikasikan**
> - **Akun:** dkm (DKM)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** laporan berstatus **Approved** (belum dipublikasikan); bila tidak ada, minta Ketua RW atau Admin RW menyetujui laporan yang diajukan sebelumnya
> - **Area:** baris laporan dan kolom **AKSI**
> - **Sorot:** kotak merah pada ikon **Publikasikan ke warga**, **Unduh PDF**, dan **Unduh Excel**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `DKM-18-laporan-approved-publikasi.png`

### ✅ Hasilnya

- Setelah ajukan: pesan **"Laporan berhasil diajukan ke Ketua untuk peninjauan dan persetujuan."**, status **Pending Review**.
- Setelah publikasi: pesan **"Laporan berhasil di-publish dan kini dapat dilihat oleh publik."**, status **Published**.
- Setelah hapus: pesan **"Laporan berhasil dihapus."**

### ⚠️ Perhatian

- **"Hanya laporan berstatus Draft atau Rejected yang dapat diajukan."** Laporan sudah diajukan atau disetujui.
- **"Hanya laporan yang sudah Approved yang bisa di-publish."** Tunggu persetujuan.
- **"Laporan ini sudah di-publish."** Tidak perlu diulang.
- **"Laporan yang sudah disetujui tidak dapat dihapus."** dan **"Laporan yang sudah dipublikasikan tidak dapat dihapus."**
- **Laporan lama tidak kunjung diputuskan.** Halaman persetujuan Ketua RW dan Admin RW tidak tersedia di menu samping saat ini. Hubungi Admin Aplikasi bila laporan menunggu terlalu lama.
- **Angka laporan berubah.** Laporan **Draft** dan **Pending Review** menghitung ulang angkanya tiap halaman dibuka. Setelah **Approved**, angka tidak berubah.

> 💡 **Tips:** Unduh PDF setelah **Approved** dan simpan sebagai arsip pertanggungjawaban dana kematian.

> ➡️ **Cerita berlanjut:** Ketua RW atau Admin RW menyetujui laporan. Lihat **`06-Ketua-RW.md` dan `05-Admin-RW.md`, cerita C-11**. Versi RW ada di `08-Op-Keuangan-RW.md`, C-11.

---

## 8. Cerita Lengkap (Lintas Peran)

| Kode | Cerita | Peran Anda | Rujukan |
|---|---|---|---|
| C-11 (versi DKM) | Laporan keuangan DKM disusun, diajukan, disetujui, lalu tampil di Transparansi | DKM menyusun, mengajukan, dan mempublikasikan | 7.1 dan 7.2 di file ini; `06-Ketua-RW.md`, `05-Admin-RW.md`, C-11; `01-Warga.md` |
| Simulasi dana kematian | Donasi masuk, santunan disalurkan, Iuran Kematian ditagih | DKM mencatat dan memverifikasi | 3.2, 3.3, dan 4.2 di file ini; `01-Warga.md` |

**Ringkasan C-11 (versi DKM)**
1. Pak Haji Rahmat menyusun laporan bulanan (7.1) dan mengajukannya (7.2).
2. Ketua RW atau Admin RW menyetujui.
3. Pak Haji Rahmat mempublikasikan, warga membaca di menu **Laporan Keuangan**.

**Ringkasan simulasi dana kematian**
1. Donasi dicatat sebagai pemasukan kategori **Donasi Kematian** (3.2).
2. Warga membayar **Iuran Kematian**, DKM memverifikasi, dan nominalnya masuk ke kas (4.2).
3. Santunan disalurkan dan dicatat sebagai pengeluaran kategori **Penyaluran Dana Kematian** (3.3).
4. Semua transaksi bulan itu masuk ke laporan DKM (7.1).

> ➡️ **Cerita berlanjut:** Kembali ke `README.md` untuk daftar cerita lain.

---

## 9. Masalah yang Sering Terjadi (FAQ)

**Kategori umum seperti Operasional & ATK tidak ada di akun saya.**
Memang begitu. Akun DKM hanya memakai kategori yang berlaku di DKM (**Donasi Kematian**, **Penyaluran Dana Kematian**, **Pengadaan barang**, dan kategori pengeluaran buatan DKM).

**Saya perlu kategori pemasukan baru untuk DKM.**
Minta Operator Keuangan RW membuatnya dan mencentang **DKM** pada **Berlaku di**. Lihat `08-Op-Keuangan-RW.md`.

**Transaksi tidak muncul di buku kas.**
Halaman menampilkan bulan berjalan. Atur **Tahun** dan **Bulan**, lalu tekan **Filter**.

**Transaksi tidak bisa diubah, ada tulisan "Terkunci".**
Laporan keuangan periode itu sudah diajukan, disetujui, atau dipublikasikan.

**Transaksi dari Iuran Kematian tidak bisa diedit.**
Transaksi itu dibuat otomatis saat pembayaran disetujui. Perubahannya lewat menu **Iuran Lainnya**.

**Saldo menjadi minus setelah penyaluran.**
Aplikasi tidak memblokir pengeluaran yang melebihi saldo. Periksa lagi nominalnya, atau tunggu dana masuk dari Iuran Kematian.

**Transaksi demo "Sumbangan Dana Kematian" tidak punya penanda status.**
Buku kas tidak menampilkan kolom status. Semua transaksi yang tampil dihitung dalam saldo.

**Iuran Kematian tidak bisa dikumpulkan lewat RT.**
Pilihan itu hanya ada pada iuran buatan RW. Iuran DKM dibayar langsung ke DKM.

**Menu Bantuan di bagian bawah tidak membuka apa pun.**
Menu itu belum memiliki halaman.

---

## 10. Lampiran: Daftar Gambar

Semua gambar desktop 1366×768. Nama file mengikuti kolom terakhir.

| Kode | Judul | Bab | Nama file |
|---|---|---|---|
| DKM-01 | Menu samping DKM | 1.3 | `DKM-01-menu-samping.png` |
| DKM-02 | Dashboard Keuangan DKM | 2.2 | `DKM-02-dashboard-keuangan-dkm.png` |
| DKM-03 | Buku kas DKM | 3.1 | `DKM-03-buku-kas-dkm.png` |
| DKM-04 | Catat donasi dana kematian | 3.2 | `DKM-04-catat-donasi.png` |
| DKM-05 | Catat penyaluran dana kematian | 3.3 | `DKM-05-catat-penyaluran.png` |
| DKM-06 | Penyaluran tercatat di buku kas | 3.3 | `DKM-06-penyaluran-tercatat.png` |
| DKM-07 | Menu aksi transaksi | 3.4 | `DKM-07-menu-aksi-transaksi.png` |
| DKM-08 | Daftar Iuran Lainnya DKM | 4.1 | `DKM-08-daftar-iuran-kematian.png` |
| DKM-09 | Jendela Buat Iuran Lainnya | 4.1 | `DKM-09-buat-iuran.png` |
| DKM-10 | Verifikasi pembayaran Iuran Kematian | 4.2 | `DKM-10-verifikasi-pembayaran.png` |
| DKM-11 | Daftar KK dan tombol Catat Bayar | 4.2 | `DKM-11-daftar-kk-iuran.png` |
| DKM-12 | Pengaturan Rekening DKM | 5 | `DKM-12-pengaturan-rekening.png` |
| DKM-13 | Daftar Kategori Transaksi DKM | 6 | `DKM-13-daftar-kategori.png` |
| DKM-14 | Jendela Tambah Kategori DKM | 6 | `DKM-14-tambah-kategori-dkm.png` |
| DKM-15 | Daftar Laporan Keuangan DKM | 7.1 | `DKM-15-daftar-laporan.png` |
| DKM-16 | Jendela Generate Laporan Baru | 7.1 | `DKM-16-generate-laporan.png` |
| DKM-17 | Konfirmasi Ajukan Laporan | 7.2 | `DKM-17-ajukan-laporan.png` |
| DKM-18 | Laporan Approved dengan tombol Publikasikan | 7.2 | `DKM-18-laporan-approved-publikasi.png` |

Jumlah: 18 gambar.
