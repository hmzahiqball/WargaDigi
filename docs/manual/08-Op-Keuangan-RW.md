# Manual Book Operator Keuangan RW (Bendahara RW)

Panduan untuk **Operator Keuangan RW** atau **Bendahara RW** di WargaDigi 21 (https://wargadigi21.com). Operator Keuangan RW mencatat kas RW, membuat iuran untuk warga, menerima setoran dari RT, mengatur rekening, serta menyusun laporan keuangan bulanan.

<!-- AI-INSTRUKSI
- Ikuti konvensi blok di README.md (SIMULASI, SCREENSHOT, Hasilnya, Perhatian, Tips, Cerita berlanjut).
- Kode gambar awalan OUW, nomor urut sesuai Lampiran. Semua gambar desktop 1366x768.
- Akun: opkeuanganrw21 / password. Tokoh: Pak Hendra Gunawan, Bendahara RW (di aplikasi hanya username yang tampil).
- Data demo: rekening RW BSI 19090980389 a.n. "Bendahara RW 21"; Iuran Lainnya "Iuran Perbaikan Got" Rp30.000 (dikumpulkan lewat RT); setoran RT ke RW Rp50.000 per KK lunas; transaksi demo RW "Pembayaran Listrik Fasum" Rp1.240.000.
- Data demo TIDAK memuat setoran RT. Setoran dicatat dulu oleh Op Keuangan RT (lihat 04-Op-Keuangan-RT.md, C-04), baru bisa dikonfirmasi di sini.
- Data demo sudah memuat laporan RW berstatus Approved dan Published untuk bulan ini dan dua bulan sebelumnya. Untuk menyusun laporan baru pakai bulan yang lebih lama.
- Beberapa tulisan di layar memang berbahasa Inggris atau campuran (nama bulan pada kolom PERIODE, status Draft/Approved). Tulis apa adanya.
-->

## Daftar Isi

1. Kenalan Dulu
   - 1.1 Tugas Operator Keuangan RW
   - 1.2 Tokoh dalam Panduan Ini
   - 1.3 Peta Menu
2. Mulai Menggunakan
   - 2.1 Masuk ke Aplikasi
   - 2.2 Membaca Dashboard Keuangan RW
3. Catat Transaksi (Buku Kas RW)
   - 3.1 Membaca Buku Kas
   - 3.2 Mencatat Transaksi Baru
   - 3.3 Mengubah dan Menghapus Transaksi
4. Pengaturan Rekening
5. Iuran Lainnya
   - 5.1 Membuat Iuran Lainnya
   - 5.2 Memantau Iuran yang Dikumpulkan Lewat RT (C-03)
   - 5.3 Memverifikasi Pembayaran Warga (iuran langsung ke RW)
6. Setoran dari RT
   - 6.1 Membaca Rekap per RT
   - 6.2 Mengonfirmasi atau Menolak Setoran (C-04)
   - 6.3 Membatalkan Setoran yang Sudah Dikonfirmasi
   - 6.4 Mengatur Nominal Setoran per KK
7. Kategori Transaksi
8. Laporan Keuangan
   - 8.1 Menyusun Laporan Bulanan (C-11 bagian 1)
   - 8.2 Mengajukan, Mempublikasikan, dan Mengunduh (C-11 bagian 2)
9. Cerita Lengkap (Lintas Peran)
10. Masalah yang Sering Terjadi (FAQ)
11. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### 1.1 Tugas Operator Keuangan RW

Sebagai Operator Keuangan RW, Anda bertugas:

- Mencatat **pemasukan dan pengeluaran kas RW** pada buku kas.
- Membuat **Iuran Lainnya** (iuran sekali bayar di luar iuran bulanan RT) untuk warga, dan memverifikasi pembayarannya bila iuran itu dibayar langsung ke RW.
- Menerima dan **mengonfirmasi setoran dari RT**, serta mengatur nominal setoran per KK.
- Mengatur **rekening bank dan QRIS** RW yang dilihat warga saat membayar.
- Mengelola **kategori transaksi** milik RW (termasuk kategori yang dipakai RT dan DKM).
- Menyusun **laporan keuangan bulanan** RW, mengajukannya untuk disetujui, lalu mempublikasikannya agar warga dapat melihat.

Anda hanya melihat dan mengubah data **unit RW**. Kas RT dan kas DKM tidak tampil di akun ini.

### 1.2 Tokoh dalam Panduan Ini

> 👤 **SIMULASI: Pak Hendra Gunawan, Bendahara RW**
>
> **Pak Hendra Gunawan** tercatat sebagai Bendahara RW di data kepengurusan. Ia memegang uang kas RW dan memastikan pencatatannya rapi.
>
> - **Masuk sebagai:** `opkeuanganrw21`, kata sandi `password`
> - **Cerita yang dimainkan:** C-03 (iuran Perbaikan Got), C-04 bagian setoran RT ke RW, dan C-11 (laporan keuangan)
> - **Tokoh lain:** Bu Siti Aminah (Bendahara RT 03), Pak Budi Santoso (warga RT 03), dan Ketua RW atau Admin RW yang menyetujui laporan

### 1.3 Peta Menu

| Menu | Gunanya |
|---|---|
| **Dashboard** | Ringkasan kas RW dan pekerjaan yang menunggu |
| **Catat Transaksi** | Buku kas RW: catat, ubah, hapus transaksi |
| **Laporan Keuangan** | Menyusun, mengajukan, mempublikasikan, dan mengunduh laporan |
| **Iuran Warga** | Grup menu berisi **Iuran Lainnya** dan **Setoran dari RT** |
| **Kategori Transaksi** | Mengatur kategori transaksi |
| **Pengaturan Rekening** | Rekening bank dan QRIS RW |
| **Keluar** | Keluar dari aplikasi |

Menu **Iuran Wajib** (iuran bulanan warga) hanya ada pada akun Bendahara RT, jadi tidak tampil di sini.

> 📸 **SCREENSHOT OUW-01: Menu samping Operator Keuangan RW**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/dashboard
> - **Kondisi:** grup **Iuran Warga** dibuka sehingga **Iuran Lainnya** dan **Setoran dari RT** terlihat
> - **Area:** menu samping kiri dari atas sampai **Keluar**
> - **Sorot:** kotak merah mengelilingi seluruh daftar menu, nomor 1 sampai 6 di samping tiap menu
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-01-menu-samping.png`

---

## 2. Mulai Menggunakan

### 2.1 Masuk ke Aplikasi

Buka https://wargadigi21.com, lalu masuk dengan akun `opkeuanganrw21` dan kata sandi `password`. Langkah masuk, memilih peran, dan keluar dijelaskan di `00-Umum-Publik-dan-Login.md`. Akun ini punya satu peran, jadi Anda langsung masuk ke dashboard keuangan.

### 2.2 Membaca Dashboard Keuangan RW

Halaman **Dashboard Keuangan RW 21** menampilkan angka dari kas RW saja. Isinya:

- **Perlu Tindakan:** daftar pekerjaan, misalnya **Setoran RT menunggu konfirmasi**, **RT yang belum menyetor penuh**, **Laporan ... belum dibuat**, **Laporan ditolak, perlu revisi**, dan **Laporan draft belum diajukan**. Bila kosong tertulis *Tidak ada pekerjaan yang tertunda.*
- **Laporan Terakhir:** status laporan terbaru, dengan tautan **Kelola**.
- **Saldo Kas RW**, **Pemasukan** bulan ini, **Pengeluaran** bulan ini, dan **Selisih bulan ini**.
- **Setoran RT** bulan ini: tabel **RT**, **Kewajiban**, **Disetor**, **Status**, **Sisa s.d. bulan ini**.
- **Iuran Lainnya Aktif:** progres tiap iuran.
- Grafik **Pemasukan vs Pengeluaran Kas RW (6 bulan)**, serta **Pemasukan per Kategori** dan **Pengeluaran per Kategori** bulan ini.
- **Transaksi Terbaru**, dengan tautan **Lihat semua**.

> 📸 **SCREENSHOT OUW-02: Dashboard Keuangan RW 21**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/dashboard
> - **Kondisi:** data demo awal
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada **Perlu Tindakan**, **Saldo Kas RW**, dan tabel **Setoran RT**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-02-dashboard-keuangan-rw.png`

> 💡 **Tips:** Buka dashboard setiap awal bulan. Kotak **Perlu Tindakan** mengingatkan laporan bulan lalu yang belum dibuat.

---

## 3. Catat Transaksi (Buku Kas RW)

### 3.1 Membaca Buku Kas

Menu **Catat Transaksi** berisi buku kas RW. Catatan di bagian atas menjelaskan: **Debet** adalah uang masuk, **Kredit** adalah uang keluar, kolom **Saldo** berjalan sejak awal pencatatan.

Penyaring: **Tahun**, **Bulan**, **Tipe Transaksi** (**Semua Tipe**, **Pemasukan**, **Pengeluaran**), dan **Kategori**, dengan tombol **Filter** dan **Reset Filter**. Bila Anda belum memilih penyaring, halaman otomatis menampilkan **bulan berjalan**.

Kolom tabel: **TANGGAL**, **KETERANGAN**, **DEBET**, **KREDIT**, **SALDO**, **AKSI**. Baris **Total periode terpilih** menunjukkan saldo awal periode, total debet, total kredit, dan **Saldo akhir**.

> 📸 **SCREENSHOT OUW-03: Buku kas RW**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** bulan berjalan, ada transaksi "Pembayaran Listrik Fasum" (bila transaksi demo jatuh di bulan sebelumnya, pilih **Bulan** yang sesuai lalu **Filter**)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada penyaring, kolom **DEBET**/**KREDIT**/**SALDO**, dan baris **Total periode terpilih**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-03-buku-kas-rw.png`

### 3.2 Mencatat Transaksi Baru

> 👤 **SIMULASI: Mencatat pengeluaran listrik fasilitas umum**
>
> Tagihan listrik lampu jalan dan Balai RW bulan ini datang. **Pak Hendra** membayarnya dari kas RW dan mencatatnya lengkap dengan foto bukti.
>
> - **Masuk sebagai:** `opkeuanganrw21`
> - **Tipe:** Pengeluaran
> - **Judul:** Pembayaran Listrik Fasum
> - **Kategori:** Operasional & ATK
> - **Tanggal:** hari ini atau sebelumnya (tidak boleh tanggal mendatang)
> - **Jumlah:** Rp 1.240.000
> - **Deskripsi:** Bayar listrik fasilitas umum RW 21
> - **Bukti:** foto struk (JPG, PNG, atau PDF, maksimal 5 MB)

**Langkah-langkah**

**1. Buka menu Catat Transaksi** lalu tekan **Catat Transaksi Baru**.

**2. Pilih tipe.**
Pada **Tipe Transaksi** pilih **Pengeluaran (-)**. Pilihan lainnya **Pemasukan (+)**.

**3. Isi Judul Transaksi.**
Ketik *Pembayaran Listrik Fasum*.

**4. Pilih Kategori.**
Buka **-- Pilih kategori --**. Daftar menyesuaikan tipe yang dipilih. Untuk pengeluaran, pilih **Operasional & ATK**. Akun RW melihat kategori umum dan kategori milik RW (misalnya **Iuran Fasilitas** untuk pemasukan). Kategori khusus DKM tidak muncul di sini.

**5. Isi Tanggal dan Jumlah (Rp).**
Isi tanggal, lalu jumlah *1.240.000*. Titik pemisah ribuan boleh dipakai.

**6. Isi Deskripsi (Opsional).**

**7. Lampirkan bukti (opsional).**
Tarik file ke kotak **Drag & drop file Anda disini atau klik untuk browse**, atau tekan **Ambil Foto** untuk memotret struk dari kamera. Format JPG, PNG, PDF, maksimal 5 MB. Tombol **Batal Pilih File** membuang pilihan.

**8. Tekan Simpan Transaksi.**

> 📸 **SCREENSHOT OUW-04: Jendela Catat Transaksi Baru**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/transaksi (jendela **Catat Transaksi Baru**)
> - **Kondisi:** formulir terisi lengkap, bukti sudah dipilih, belum disimpan
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Tipe Transaksi**, **Kategori**, **Jumlah (Rp)**, kotak bukti, dan tombol **Simpan Transaksi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-04-catat-transaksi-baru.png`

### ✅ Hasilnya

- Muncul pesan **"Transaksi berhasil dicatat."**
- Transaksi tampil di buku kas dengan kode `TRX-RW-...`, nominal di kolom **KREDIT**, dan saldo berjalan berkurang.
- Transaksi langsung berstatus sah (tidak menunggu persetujuan siapa pun).

> 📸 **SCREENSHOT OUW-05: Transaksi baru di buku kas**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** transaksi baru tampil dengan lencana **Lampiran Bukti**
> - **Area:** tabel buku kas
> - **Sorot:** kotak merah pada baris transaksi baru, lencana **Lampiran Bukti**, dan saldo
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-05-transaksi-baru.png`

### ⚠️ Perhatian

- **Muncul "Terjadi kesalahan validasi:".** Satu atau lebih isian kurang. Baca daftar di bawah tulisan itu.
- **"Kategori tidak valid atau tidak sesuai dengan tipe transaksi."** Kategori tidak cocok dengan tipe (misalnya kategori pemasukan dipakai untuk pengeluaran). Pilih ulang kategori setelah memilih tipe.
- **Tanggal tidak boleh melewati hari ini.** Kolom tanggal dibatasi sampai hari ini.
- **"Tidak dapat mencatat transaksi pada periode ... karena laporan keuangan periode tersebut sudah diajukan, disetujui, atau dipublikasikan."** Periode itu sudah terkunci karena laporannya sudah berjalan. Lihat 8.2.
- **Transaksi tidak terlihat setelah disimpan.** Halaman menampilkan bulan berjalan. Pilih **Bulan** dan **Tahun** sesuai tanggal transaksi.

> 💡 **Tips:** Selalu lampirkan foto bukti. Saat ada pertanyaan warga atau pemeriksaan, bukti menjadi penolong.

> ➡️ **Cerita berlanjut:** Lanjut ke 3.3.

### 3.3 Mengubah dan Menghapus Transaksi

Pada kolom **AKSI**, tekan tombol titik tiga (⋮). Pilihannya:

- **Lihat Bukti:** membuka lampiran bukti di tab baru (bila ada).
- **Edit:** membuka jendela **Edit Transaksi**. Setelah selesai tekan **Simpan Perubahan**. Pesan: **"Transaksi berhasil diperbarui."**
- **Hapus:** muncul **Hapus Transaksi** ("Apakah Anda yakin ingin menghapus transaksi ini?"), tekan **Hapus**. Pesan: **"Transaksi berhasil dihapus."** Penghapusan bersifat permanen.

> 📸 **SCREENSHOT OUW-06: Menu aksi transaksi**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** tombol titik tiga pada satu transaksi sudah ditekan
> - **Area:** baris transaksi dan menu yang terbuka
> - **Sorot:** kotak merah pada **Lihat Bukti**, **Edit**, dan **Hapus**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-06-menu-aksi-transaksi.png`

### ⚠️ Perhatian

- **Edit dan Hapus abu-abu dengan lencana "Terkunci".** Laporan periode itu sudah diajukan, disetujui, atau dipublikasikan. Transaksinya tidak bisa diubah lagi.
- **"Transaksi ini berasal dari Iuran Warga atau Setoran RT. Ubah atau batalkan melalui menu Iuran Warga / Setoran."** Transaksi hasil iuran atau setoran tidak boleh diubah dari buku kas. Batalkan lewat menu asalnya (lihat 6.3).
- **Tanggal dipindahkan ke periode terkunci.** Pesan: *"Tanggal transaksi tidak dapat dipindahkan ke periode ... karena laporan periode tersebut sudah diajukan, disetujui, atau dipublikasikan."*

> 💡 **Tips:** Bila laporan ternyata salah setelah diajukan, Anda tidak bisa mengubah transaksinya. Tunggu laporan **Ditolak** atau hubungi pihak yang menyetujui. Laporan yang ditolak membuka kembali periodenya.

> ➡️ **Cerita berlanjut:** Bab 4 membahas rekening.

---

## 4. Pengaturan Rekening

Menu **Pengaturan Rekening** menyimpan rekening bank dan QRIS RW. Informasi ini dilihat warga saat membayar iuran RW secara langsung.

> 👤 **SIMULASI: Memeriksa rekening RW**
>
> Rekening RW pada data demo sudah terisi. **Pak Hendra** memeriksa dan, bila ada perubahan, memperbaikinya.
>
> - **Nama Bank:** BSI
> - **Nomor Rekening:** 19090980389
> - **Atas Nama:** Bendahara RW 21
> - **QRIS:** belum ada (opsional)

**Langkah-langkah**

**1. Buka menu Pengaturan Rekening.**
Judul halaman **Pengaturan Rekening**.

**2. Isi atau periksa kolom.**
**Nama Bank** (wajib), **Nomor Rekening** (wajib), **Atas Nama** (wajib).

**3. Unggah QRIS (opsional).**
Pada **Upload QRIS (Opsional)** pilih gambar JPG, PNG, atau WEBP, maksimal 5 MB. Pratinjau muncul pada **Preview QRIS Baru yang Dipilih**. QRIS yang tersimpan tampil pada **QRIS Saat Ini**.

**4. Tekan Simpan Pengaturan.**

> 📸 **SCREENSHOT OUW-07: Pengaturan Rekening RW**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/rekening
> - **Kondisi:** rekening BSI 19090980389 terisi
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada **Nama Bank**, **Nomor Rekening**, **Atas Nama**, **Upload QRIS (Opsional)**, dan tombol **Simpan Pengaturan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-07-pengaturan-rekening.png`

### ✅ Hasilnya

- Muncul pesan **"Pengaturan rekening berhasil disimpan."**
- Warga melihat rekening ini saat membayar iuran yang ditujukan ke RW.

### ⚠️ Perhatian

- **"Nama bank wajib diisi."**, **"Nomor rekening wajib diisi."**, **"Atas nama rekening wajib diisi."** Lengkapi kolom yang diminta.
- **"Gagal memproses file QRIS: ..."** File QRIS rusak atau terlalu besar. Unggah ulang gambar yang lebih kecil.

> 💡 **Tips:** Setelah mengganti nomor rekening, periksa ulang dengan Ketua RW sebelum mengumumkannya, supaya warga tidak salah transfer.

> ➡️ **Cerita berlanjut:** Bab 5 membahas iuran.

---

## 5. Iuran Lainnya

Menu **Iuran Warga**, lalu **Iuran Lainnya**, berisi iuran sekali bayar di luar iuran bulanan (misalnya perbaikan fasilitas). Setiap iuran disebar ke **seluruh KK** di RW.

Tabel berisi **Judul Iuran**, **Nominal**, **Tenggat**, **Progress Terbayar** (misalnya 3/40), dan tombol **Detail**. Lencana pada judul menunjukkan jenis nominal: **Nominal fix** (tidak bisa diubah warga) atau **Minimal** (warga boleh membayar lebih), dan **Lewat RT** bila iuran dikumpulkan lewat RT.

### 5.1 Membuat Iuran Lainnya

> 👤 **SIMULASI C-03: Iuran Perbaikan Got (bagian RW)**
>
> Got utama lingkungan tersumbat dan perlu diperbaiki. Pengurus RW sepakat meminta Rp30.000 dari tiap KK. **Pak Hendra** membuat iurannya dan memilih agar dikumpulkan lewat RT masing-masing, supaya warga cukup membayar ke bendahara RT-nya.
>
> - **Masuk sebagai:** `opkeuanganrw21`, kata sandi `password`
> - **Judul Iuran:** Iuran Perbaikan Got
> - **Nominal:** Rp 30.000 (minimal, bukan nominal fix)
> - **Dikumpulkan lewat RT:** dicentang
> - **Kategori Kas:** Iuran Fasilitas
> - **Selanjutnya:** Pak Budi Santoso (RT 03) membayar ke Bendahara RT 03. Lihat `01-Warga.md`, C-03 dan `04-Op-Keuangan-RT.md`.

**Langkah-langkah**

**1. Buka Iuran Warga, lalu Iuran Lainnya.**

**2. Tekan Buat Iuran Lainnya.**
Jendela **Buat Iuran Lainnya** terbuka.

**3. Isi Judul Iuran dan Nominal (Rp).**
Contoh: *Iuran Perbaikan Got* dan *30.000*. Nominal ini adalah **nominal minimal** iuran. Centang **Nominal fix (sesuai tertera, tidak bisa diubah)** bila nominalnya pasti.

**4. Tentukan Dikumpulkan lewat RT.**
Kotak ini otomatis tercentang pada akun RW. Bila dicentang, warga membayar ke rekening RT, Operator Keuangan RT yang memverifikasi, lalu RT menyetor seluruh yang terkumpul ke RW. Bila dihilangkan, warga membayar langsung ke RW dan **Anda yang memverifikasi** (lihat 5.3). Pilihan ini **tidak dapat diubah setelah iuran dibuat**.

**5. Pilih Kategori Kas (pemasukan).**
Pilih **Iuran Fasilitas**.

**6. Isi Tenggat Waktu dan Tanggal Pengingat.**
Tenggat berupa tanggal. **Tanggal Pengingat** adalah tanggal tiap bulan (1 sampai 31) saat pengingat dikirim ke warga yang belum bayar.

**7. Isi Deskripsi (Opsional)** lalu tekan **Buat & Sebar**.

> 📸 **SCREENSHOT OUW-08: Jendela Buat Iuran Lainnya**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/iuran (jendela **Buat Iuran Lainnya**)
> - **Kondisi:** formulir terisi seperti simulasi, kotak **Dikumpulkan lewat RT** tercentang (jangan disimpan bila iuran sudah ada pada data demo)
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Nominal (Rp)**, **Nominal fix**, **Dikumpulkan lewat RT**, **Kategori Kas (pemasukan)**, dan tombol **Buat & Sebar**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-08-buat-iuran-lainnya.png`

### ✅ Hasilnya

- Muncul pesan **"Tagihan insidental berhasil dibuat dan disebar ke N warga."** (N = jumlah KK).
- Iuran tampil di daftar dengan lencana **Lewat RT** dan progres 0 dari jumlah KK.
- Warga melihat iuran ini di menu **Iuran Lainnya** pada akun mereka.

> 📸 **SCREENSHOT OUW-09: Daftar Iuran Lainnya**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/iuran
> - **Kondisi:** "Iuran Perbaikan Got" tampil dengan lencana **Lewat RT**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada lencana **Lewat RT**, **Progress Terbayar**, dan tombol **Detail**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-09-daftar-iuran-lainnya.png`

### ⚠️ Perhatian

- **"Nominal wajib diisi sebagai nominal minimal iuran."** Kolom nominal masih kosong.
- **"Tagihan gagal dibuat: tidak ada warga/keluarga pada unit ini untuk ditagih."** Belum ada data KK.
- **Salah memilih Dikumpulkan lewat RT.** Pilihan ini tidak bisa diubah. Bila keliru, buat iuran baru dengan pilihan yang benar.

> 💡 **Tips:** Pilih **lewat RT** untuk iuran yang cocok dikumpulkan dekat rumah warga. Pilih **langsung ke RW** bila jumlah iuran kecil atau warga lebih mudah transfer ke rekening RW.

> ➡️ **Cerita berlanjut:** Lanjut ke 5.2 untuk memantau iuran yang dikumpulkan lewat RT.

### 5.2 Memantau Iuran yang Dikumpulkan Lewat RT (C-03)

> 👤 **SIMULASI C-03: Iuran Perbaikan Got (bagian pemantauan RW)**
>
> Pak Budi Santoso (RT 03) sudah membayar Rp30.000 untuk Iuran Perbaikan Got dan Bendahara RT 03 telah menyetujuinya. **Pak Hendra** memantau seberapa banyak yang sudah terkumpul di tiap RT.
>
> - **Masuk sebagai:** `opkeuanganrw21`
> - **Iuran:** Iuran Perbaikan Got
> - **Sebelumnya:** pembayaran Pak Budi diverifikasi Bu Siti Aminah. Lihat `04-Op-Keuangan-RT.md`, C-03.

**Langkah-langkah**

**1. Buka Iuran Lainnya** dan tekan **Detail** pada "Iuran Perbaikan Got".

**2. Baca keterangan di atas.**
Kotak biru menjelaskan: *Iuran ini dikumpulkan lewat RT. Verifikasi pembayaran warga dilakukan oleh Operator Keuangan RT; RW mengonfirmasi setoran dari tiap RT.*

**3. Baca ringkasan.**
Empat kartu: **KK lunas**, **Terkumpul di RT**, **Sudah disetor ke RW**, **Belum disetor**. Di bawahnya tabel **Rekap per RT** berisi **RT**, **KK lunas**, **Terkumpul**, **Disetor**, **Menunggu**, **Sisa**.

> 📸 **SCREENSHOT OUW-10: Detail iuran yang dikumpulkan lewat RT**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/iuran/{id}
> - **Kondisi:** "Iuran Perbaikan Got", RT 03 sudah ada KK yang lunas (setelah pembayaran Pak Budi disetujui)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kotak informasi biru, empat kartu ringkasan, dan tabel **Rekap per RT**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-10-detail-iuran-via-rt.png`

### ✅ Hasilnya

- Anda melihat angka **agregat per RT**, bukan daftar per warga. Rincian per KK hanya dilihat dan diverifikasi oleh Bendahara RT.
- Uang yang terkumpul **masih tercatat di kas RT**. Kas RW baru bertambah ketika setoran RT dikonfirmasi (lihat 6.2).

### ⚠️ Perhatian

- **Tidak ada tombol Setujui atau Tolak untuk warga.** Itu memang benar. Untuk iuran yang dikumpulkan lewat RT, verifikasi per warga bukan wewenang RW. Bila dicoba lewat tautan langsung, muncul pesan *"Pembayaran ini bukan wewenang akun Anda."*
- **Catatan di bawah tabel menyebut "menu Iuran Bulanan".** Maksudnya konfirmasi setoran RT. Di akun RW, menunya bernama **Setoran dari RT** (lihat Bab 6).

> 💡 **Tips:** Kolom **Sisa** yang berwarna merah berarti uang sudah terkumpul di RT tetapi belum disetor ke RW. Hubungi Bendahara RT terkait.

> ➡️ **Cerita berlanjut:** Setoran dari RT dikonfirmasi di Bab 6. Untuk iuran yang dibayar langsung ke RW, lihat 5.3.

### 5.3 Memverifikasi Pembayaran Warga (iuran langsung ke RW)

Bagian ini berlaku bila iuran dibuat **tanpa** centang **Dikumpulkan lewat RT**. Pada iuran seperti ini warga membayar ke rekening RW, dan **Anda** yang memverifikasi.

> 👤 **SIMULASI: Iuran Pengecatan Gapura (dibayar langsung ke RW)**
>
> RW membuat iuran kecil Rp25.000 untuk pengecatan gapura dan meminta warga transfer langsung ke rekening RW. **Pak Budi Santoso** mengirim bukti transfer, lalu **Pak Hendra** memeriksa bukti itu.
>
> - **Masuk sebagai:** `opkeuanganrw21`
> - **Iuran:** Iuran Pengecatan Gapura (contoh baru, buat dengan cara 5.1 tanpa mencentang **Dikumpulkan lewat RT**)
> - **Pembayar:** Budi Santoso, metode Transfer, dengan foto bukti
> - **Sebelumnya:** Pak Budi mengajukan pembayaran di menu **Iuran Lainnya** akunnya (lihat `01-Warga.md`)

**Langkah-langkah**

**1. Buka detail iuran.**
Di **Iuran Lainnya**, tekan **Detail** pada iuran itu. Ada dua tab: **Daftar & Catat** (semua KK) dan **Pembayaran Warga** (pengajuan yang menunggu, dengan lencana jumlah).

**2. Buka tab Pembayaran Warga.**
Baris warga yang mengajukan berstatus **Menunggu Verifikasi**. Tekan **Periksa**.

**3. Periksa jendela Detail Pembayaran.**
Terlihat **Warga**, **No. KK**, **Tagihan**, **Nominal**, **Metode**, **Waktu kirim**, dan bukti pembayaran. Bukti bisa dibuka lebih besar lewat **Buka Gambar Penuh**. Jika tidak ada bukti tertulis *Tidak ada bukti pembayaran dilampirkan.*

**4. Setujui atau tolak.**
- **Setujui:** nominal masuk ke kas RW sebagai pemasukan.
- **Tolak:** tekan **Tolak**, isi **Alasan penolakan** (misalnya *Bukti transfer buram atau nominal tidak sesuai*), lalu **Kirim Penolakan**. Warga diberi tahu alasannya.

> 📸 **SCREENSHOT OUW-11: Tab Pembayaran Warga dan jendela Detail Pembayaran**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/iuran/{id} (tab **Pembayaran Warga**, jendela **Detail Pembayaran**)
> - **Kondisi:** pengajuan Pak Budi berstatus **Menunggu Verifikasi**, bukti terlihat
> - **Area:** jendela detail pembayaran
> - **Sorot:** kotak merah pada bukti pembayaran, tombol **Tolak**, dan tombol **Setujui**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-11-verifikasi-pembayaran.png`

**Mencatat pembayaran tunai.** Bila warga membayar tunai kepada Anda, buka tab **Daftar & Catat**, lalu pada baris warga yang berstatus **Belum Bayar** tekan **Catat Bayar**. Isi **Nominal (Rp)**, **Tanggal Bayar**, **Metode** (**Tunai**, **Transfer**, **QRIS**), bukti (opsional), lalu **Simpan & Masuk Kas**.

**Mengingatkan.** Tombol lonceng (**Kirim Pengingat (Broadcast)**) mengirim pengingat ke warga yang **Belum Bayar**.

### ✅ Hasilnya

- Setelah **Setujui**: pesan **"Pembayaran berhasil disetujui dan dicatat ke keuangan."** Status warga menjadi **Lunas**, tercatat transaksi pemasukan di kas RW (judul iuran diikuti nama kepala keluarga).
- Setelah **Tolak**: pesan **"Pembayaran berhasil ditolak."** Status kembali **Belum Bayar** dengan keterangan **Ditolak**.
- Setelah **Catat Bayar**: pesan **"Pembayaran dicatat dan masuk ke kas."**
- Setelah pengingat: pesan konfirmasi dengan nama kepala keluarga.

> 📸 **SCREENSHOT OUW-12: Jendela Catat Pembayaran**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/iuran/{id} (tab **Daftar & Catat**, jendela **Catat Pembayaran**)
> - **Kondisi:** satu KK berstatus **Belum Bayar**, jendela terisi nominal, tanggal, metode **Tunai**
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Nominal (Rp)**, **Metode**, dan tombol **Simpan & Masuk Kas**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-12-catat-pembayaran.png`

### ⚠️ Perhatian

- **"Nominal pembayaran tidak boleh kurang dari nominal minimal (Rp ...)."** Isi nominal sama dengan atau lebih besar dari minimum. Pada iuran **Nominal fix**, nominal terkunci sama dengan tagihan.
- **"Pembayaran ini sudah lunas."** Baris itu sudah diselesaikan sebelumnya.
- **Penolakan tidak terkirim.** Kolom **Alasan penolakan** wajib diisi, maksimal 500 karakter.
- **"Warga ini belum memiliki akun aktif, pengingat tidak dapat dikirim."** KK belum punya akun. Hubungi lewat cara lain.
- **"Notifikasi aplikasi sedang dinonaktifkan di Pengaturan Sistem."** Minta Admin Aplikasi mengaktifkan notifikasi.
- **"Tagihan sudah dibayar atau sedang diverifikasi."** Pengingat hanya untuk yang **Belum Bayar**.

> 💡 **Tips:** Cocokkan nominal pada bukti dengan mutasi rekening BSI RW sebelum menekan **Setujui**.

> ➡️ **Cerita berlanjut:** Bab 6 membahas setoran dari RT.

---

## 6. Setoran dari RT

Menu **Iuran Warga**, lalu **Setoran dari RT**. Uang iuran warga tercatat di kas RT. Setiap bulan RT menyetor sebagian ke RW berdasarkan **nominal setoran per KK lunas**. Halaman ini membantu Anda memantau dan mengonfirmasi setoran itu. Judul halaman: **Setoran dari RT**.

Di kanan atas ada pilihan **Bulan** dan **Tahun**, serta tombol **Nominal Setoran**. Empat kartu ringkasan: **Iuran terkumpul (semua RT)**, **Kewajiban setoran**, **Setoran diterima**, **Sisa s.d. (bulan)**.

Empat tab: **Rekap per RT**, **Iuran Lainnya** (hanya bila ada iuran yang dikumpulkan lewat RT), **Konfirmasi** (dengan lencana jumlah setoran menunggu), dan **Riwayat**.

### 6.1 Membaca Rekap per RT

Tab **Rekap per RT** menampilkan tabel **RT**, **KK lunas**, **Terkumpul**, **Setoran/KK**, **Kewajiban**, **Disetor**, **Menunggu**, **Status**, dan **Sisa s.d. (bulan)**. Status yang mungkin: **Lunas**, **Sebagian**, **Menunggu konfirmasi**, **Belum setor**, **Tidak ada kewajiban**.

Rumusnya: **Kewajiban = nominal setoran per KK × jumlah KK yang lunas pada bulan itu.** Hanya angka agregat per RT yang ditampilkan.

> 📸 **SCREENSHOT OUW-13: Rekap per RT**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/setoran
> - **Kondisi:** tab **Rekap per RT**, bulan berjalan
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada empat kartu ringkasan, kolom **Kewajiban**, **Disetor**, dan **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-13-rekap-per-rt.png`

### ⚠️ Perhatian

- **"Nominal setoran per KK untuk (tahun) belum ditetapkan, sehingga kewajiban setoran bernilai nol."** Atur lewat tombol **Nominal Setoran** (6.4). Pada data demo, nominal Rp50.000 per KK sudah terisi.
- **Kolom Setoran/KK kosong atau nol.** Sama seperti di atas.

> 💡 **Tips:** Ganti **Bulan** dan **Tahun** untuk melihat bulan sebelumnya. Nilai **Sisa** adalah kumulatif sampai bulan yang dipilih.

> ➡️ **Cerita berlanjut:** Lanjut ke 6.2.

### 6.2 Mengonfirmasi atau Menolak Setoran (C-04)

> 👤 **SIMULASI C-04: Setoran RT 03 ke RW (bagian RW)**
>
> **Bu Siti Aminah**, Bendahara RT 03, mencatat setoran iuran bulanan RT 03 ke RW. Uangnya sudah ia serahkan kepada **Pak Hendra**. Pak Hendra memeriksa bukti lalu mengonfirmasi, sehingga kas RW bertambah.
>
> - **Masuk sebagai:** `opkeuanganrw21`, kata sandi `password`
> - **Setoran:** dicatat oleh akun `operator_rt3` (RT 03), misalnya untuk satu bulan iuran, dengan bukti
> - **Sebelumnya:** Bu Siti mencatat setoran. Lihat `04-Op-Keuangan-RT.md`, C-04
> - **Catatan:** data demo belum memuat setoran. Setoran harus dicatat dulu oleh Bendahara RT

**Langkah-langkah**

**1. Buka Setoran dari RT.**
Setelah Bu Siti mencatat setoran, Anda menerima notifikasi **Setoran RT Menunggu Konfirmasi**.

**2. Buka tab Konfirmasi.**
Lencana kuning menunjukkan jumlah setoran menunggu. Tiap baris berisi RT, periode, dan nominal.

**3. Tekan Periksa.**
Jendela **Detail Setoran RT 03** tampil: **Setoran untuk (periode)**, **Total**, **Tanggal setor**, **Metode**, **Dicatat oleh**, dan bukti setoran.

**4. Konfirmasi atau tolak.**
- **Konfirmasi:** bila uang sudah Anda terima dan sesuai.
- **Tolak:** tekan **Tolak**, isi **Alasan penolakan** (misalnya *Nominal di bukti tidak sesuai*), lalu **Kirim Penolakan**.

> 📸 **SCREENSHOT OUW-14: Tab Konfirmasi setoran**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/setoran (tab **Konfirmasi**)
> - **Kondisi:** ada satu setoran RT 03 yang menunggu
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada lencana jumlah di tab **Konfirmasi** dan tombol **Periksa**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-14-tab-konfirmasi-setoran.png`

> 📸 **SCREENSHOT OUW-15: Jendela Detail Setoran RT**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/setoran (jendela **Detail Setoran RT 03**)
> - **Kondisi:** detail setoran terbuka dengan bukti
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Total**, bukti setoran, tombol **Tolak**, dan tombol **Konfirmasi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-15-detail-setoran-rt.png`

### ✅ Hasilnya

- Setelah **Konfirmasi**: pesan **"Setoran dikonfirmasi dan tercatat di kas RT (pengeluaran) dan kas RW (pemasukan)."** Kas RW bertambah, kas RT berkurang, dan Bendahara RT menerima notifikasi **Setoran Dikonfirmasi**.
- Setelah **Tolak**: pesan **"Setoran ditolak dan RT diberi tahu."** Bendahara RT menerima notifikasi **Setoran Ditolak** beserta alasan.
- Setoran yang selesai pindah ke tab **Riwayat** dengan status **Dikonfirmasi**, **Ditolak**, atau **Dibatalkan**.

### ⚠️ Perhatian

- **"Setoran ini sudah diproses."** Setoran sudah dikonfirmasi atau ditolak sebelumnya, mungkin oleh Anda dari tab lain. Muat ulang halaman.
- **Alasan penolakan wajib.** Kolom tidak boleh kosong, maksimal 500 karakter.
- **Tab Konfirmasi kosong.** Tertulis *Tidak ada setoran yang menunggu konfirmasi.* RT belum mencatat setoran.

> 💡 **Tips:** Konfirmasi hanya setelah uang benar-benar Anda terima atau terlihat di mutasi rekening RW.

> ➡️ **Cerita berlanjut:** Kas RW bertambah, dan angka itu masuk ke laporan keuangan (Bab 8).

### 6.3 Membatalkan Setoran yang Sudah Dikonfirmasi

Bila setoran dikonfirmasi karena salah, buka tab **Riwayat** dan tekan **Batalkan setoran** pada baris itu.

**Langkah-langkah**

**1. Buka tab Riwayat** dan cari setoran berstatus **Dikonfirmasi**.

**2. Tekan Batalkan setoran.**
Jendela **Batalkan Setoran** tampil: *Setoran RT ... akan dibatalkan seluruhnya. Transaksi di kas RT dan kas RW ikut dihapus.*

**3. Isi Alasan** (wajib) lalu tekan **Batalkan Setoran**.

> 📸 **SCREENSHOT OUW-16: Jendela Batalkan Setoran**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/setoran (tab **Riwayat**, jendela **Batalkan Setoran**)
> - **Kondisi:** satu setoran berstatus **Dikonfirmasi**, alasan sudah diketik
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada peringatan, kolom **Alasan**, dan tombol **Batalkan Setoran**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-16-batalkan-setoran.png`

### ✅ Hasilnya

- Pesan **"Setoran dibatalkan dan transaksi kas terkait dihapus."**
- Transaksi pemasukan di kas RW dan pengeluaran di kas RT ikut terhapus. Bendahara RT menerima notifikasi **Setoran Dibatalkan** beserta alasan.

### ⚠️ Perhatian

- **"Hanya setoran yang sudah dikonfirmasi yang dapat dibatalkan."** Setoran yang masih menunggu atau sudah ditolak tidak perlu dibatalkan.
- **Pembatalan mengubah saldo kas.** Periksa laporan bulan terkait. Bila laporan periode itu sudah **Pending Review** atau **Approved**, koordinasikan dengan penyetuju.

> 💡 **Tips:** Selalu tulis alasan yang jelas, karena Bendahara RT akan membacanya.

> ➡️ **Cerita berlanjut:** Lanjut ke 6.4.

### 6.4 Mengatur Nominal Setoran per KK

Tekan **Nominal Setoran** di kanan atas. Jendela **Nominal Setoran per KK (tahun)** terbuka.

**Langkah-langkah**

**1. Isi nominal untuk semua RT.**
Kolom **Nominal untuk semua RT (Rp per KK lunas per bulan)**. Pada data demo: Rp 50.000.

**2. Isi nominal khusus RT (opsional).**
Pada **Khusus RT tertentu (opsional)**, isi nominal berbeda untuk satu RT. Dikosongkan berarti RT itu **ikut default**.

**3. Pilih Nominal berlaku mulai bulan.**
Perubahan hanya berlaku dari bulan yang dipilih ke depan dan tidak mengubah bulan sebelumnya.

**4. Centang konfirmasi.**
Bila ini pertama kali mengubah nominal pada tahun itu, centang **Saya mengerti dan mengonfirmasi perubahan ini berlaku mulai bulan yang dipilih (cut off), tanpa mengubah kewajiban setoran bulan sebelumnya.** Ringkasan perubahan tampil otomatis di bawahnya.

**5. Tekan Simpan.**

Bagian **Riwayat nominal (tahun)** di jendela menampilkan nominal yang pernah berlaku.

> 📸 **SCREENSHOT OUW-17: Jendela Nominal Setoran per KK**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/setoran (jendela **Nominal Setoran per KK**)
> - **Kondisi:** nominal default terisi, riwayat nominal terlihat
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada nominal untuk semua RT, daftar **Khusus RT tertentu**, **Nominal berlaku mulai bulan**, dan tombol **Simpan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-17-nominal-setoran.png`

### ✅ Hasilnya

- Pesan **"Nominal setoran per KK berlaku mulai (bulan) (tahun). Kewajiban bulan sebelumnya tidak berubah."**
- Bila tidak ada yang diubah: **"Tidak ada perubahan nominal setoran."**

### ⚠️ Perhatian

- **"Perubahan nominal setoran tidak boleh berlaku surut. Pilih bulan mulai berlaku ... atau sesudahnya."** Bulan yang sudah memiliki setoran tidak bisa diubah. Pilih bulan yang lebih akhir.
- **"Nominal setoran ... tidak dapat diubah lagi karena seluruh bulan tahun ini sudah memiliki setoran. Atur nominal pada tahun berikutnya."** Semua bulan tahun ini sudah terkunci.
- **"Centang konfirmasi perubahan nominal setoran terlebih dahulu."** Centang kotak konfirmasi.

> 💡 **Tips:** Atur nominal di awal tahun sebelum RT mulai mencatat setoran supaya bisa berlaku mulai Januari.

> ➡️ **Cerita berlanjut:** Bab 7 membahas kategori transaksi.

---

## 7. Kategori Transaksi

Menu **Kategori Transaksi** mengatur kategori untuk transaksi kas. Anda mengelola kategori **buatan RW**, sekaligus menentukan di unit mana kategori itu berlaku: **RW**, **RT**, atau **DKM**. Kategori umum buatan Admin Aplikasi hanya tampil sebagai referensi.

Penyaring: **Cari Nama Kategori**, **Tipe** (**Pemasukan**, **Pengeluaran**, **Keduanya**), **Asal** (**Umum**, **Buatan DKM**, **Dari RW**, **Berlaku di RW**, **Berlaku di RT**, **Berlaku di DKM**, **Unit RW**), dan **Status**. Kolom tabel: **NAMA KATEGORI**, **TIPE**, **ASAL**, **STATUS**, **AKSI**.

Pada data demo, kategori buatan RW antara lain:

| Kategori | Tipe | Berlaku di |
|---|---|---|
| **Iuran Fasilitas** | Pemasukan | RW |
| **Donasi Kematian** | Pemasukan | DKM |
| **Penyaluran Dana Kematian** | Pengeluaran | DKM |

Kategori **Pengadaan barang** dibuat DKM dan tampil di akun RW dengan keterangan **Dikelola DKM**.

> 👤 **SIMULASI: Menambah kategori pengeluaran RW**
>
> **Pak Hendra** sering membeli perlengkapan kegiatan dan ingin kategori sendiri.
>
> - **Masuk sebagai:** `opkeuanganrw21`
> - **Nama Kategori:** Perlengkapan Kegiatan
> - **Tipe:** Pengeluaran
> - **Berlaku di:** RW
> - **Aktif:** ya

**Langkah-langkah**

**1. Buka Kategori Transaksi** lalu tekan **Tambah Kategori**.

**2. Isi Nama Kategori** (maksimal 100 karakter), pilih **Tipe**.

**3. Centang Berlaku di.**
Pilih **RW**, **RT**, dan/atau **DKM** (minimal satu), atau **Pilih semua**. Hanya akun RW yang bisa menentukan ini.

**4. Biarkan Aktif tercentang** lalu tekan **Simpan**.

> 📸 **SCREENSHOT OUW-18: Daftar Kategori Transaksi dan jendela Tambah Kategori**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/kategori-transaksi (jendela **Tambah Kategori**)
> - **Kondisi:** daftar kategori demo terlihat di belakang, jendela terisi *Perlengkapan Kegiatan*
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada **Tipe**, **Berlaku di** (RW, RT, DKM, **Pilih semua**), dan tombol **Simpan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-18-tambah-kategori-transaksi.png`

### ✅ Hasilnya

- Pesan **"Kategori transaksi berhasil ditambahkan."**
- Kategori langsung tersedia di formulir **Catat Transaksi** pada unit yang dicentang. Kategori yang berlaku di RT juga muncul pada Bendahara RT, dan kategori yang berlaku di DKM muncul pada akun DKM.

**Mengubah dan menghapus**

- Ikon pensil membuka **Edit Kategori**. Pesan sukses: **"Kategori transaksi berhasil diperbarui."**
- Ikon tempat sampah membuka **Hapus Kategori**. Bila kategori sudah dipakai transaksi atau iuran, kategori **tidak dihapus**, melainkan dinonaktifkan. Pesan: **"Kategori sudah dipakai transaksi, sehingga dinonaktifkan (bukan dihapus) agar rekap tetap utuh."** Bila belum dipakai: **"Kategori transaksi berhasil dihapus."**

### ⚠️ Perhatian

- **"Pilih minimal satu unit tempat kategori ini berlaku (RW, RT, atau DKM)."** Centang minimal satu unit.
- **"Unit ... tidak dapat dilepas karena kategori ini sudah dipakai di .... Nonaktifkan kategori ini bila tidak ingin dipakai lagi."** Anda boleh menambah unit, tetapi tidak boleh melepas unit yang sudah memakai kategori itu.
- **Kategori lain tidak bisa diubah.** Baris bertuliskan **Dikelola Admin** atau **Dikelola DKM** hanya bisa dilihat.

> 💡 **Tips:** Kategori yang baik singkat dan konsisten, sehingga rekap bulanan mudah dibaca.

> ➡️ **Cerita berlanjut:** Bab 8 membahas laporan keuangan.

---

## 8. Laporan Keuangan

Menu **Laporan Keuangan** merangkum arus kas RW per bulan. Alurnya:

**Draft** (Anda susun dan periksa) → **Pending Review** (diajukan, menunggu persetujuan) → **Approved** (disetujui) → **Published** (dipublikasikan ke warga). Bila ditolak, statusnya **Ditolak** dan Anda perbaiki lalu ajukan lagi.

**Siapa yang menyetujui?** Menurut kode aplikasi, laporan RW (dan DKM) disetujui oleh **Ketua RW atau Admin RW**. Persetujuan RT dilakukan Ketua RT. Notifikasi pengajuan dikirim ke Ketua RW dan Admin RW.

Arti status pada kolom **STATUS**:

| Label di layar | Artinya | Yang bisa Anda lakukan |
|---|---|---|
| **Draft** | Baru disusun, belum diajukan | Ajukan, Unduh, Hapus |
| **Pending Review** | Menunggu Ketua RW atau Admin RW | Unduh, Hapus |
| **Ditolak** | Dikembalikan dengan catatan | Ajukan ulang, Unduh, Hapus |
| **Approved** | Disetujui, belum tampil ke warga | Publikasikan, Unduh |
| **Published** | Tampil ke warga | Unduh |

### 8.1 Menyusun Laporan Bulanan (C-11 bagian 1)

> 👤 **SIMULASI C-11: Laporan keuangan RW (bagian 1 dari 3)**
>
> Awal bulan, **Pak Hendra** menyusun laporan keuangan RW untuk bulan yang sudah lewat. Data demo sudah memuat laporan yang disetujui untuk tiga bulan terakhir, jadi ia menyusun laporan untuk bulan yang lebih lama.
>
> - **Masuk sebagai:** `opkeuanganrw21`
> - **Periode:** empat bulan sebelum bulan ini (di luar tiga laporan demo)
> - **Selanjutnya:** ia mengajukan laporan (8.2). Ketua RW atau Admin RW menyetujuinya. Lihat `06-Ketua-RW.md` dan `05-Admin-RW.md`, C-11.

**Langkah-langkah**

**1. Buka menu Laporan Keuangan.**
Judul **Laporan Keuangan**. Penyaring: **Tahun** (**Semua Tahun**, 2026, 2025) dan **Status**, dengan tombol **Filter**.

**2. Tekan Generate Laporan Baru.**
Jendela **Generate Laporan Baru** terbuka. Kotak biru menjelaskan bahwa laporan dibuat berstatus **Draft** agar Anda dapat meninjau angka sebelum mengajukannya.

**3. Pilih Bulan dan isi Tahun.**
Pilihan awal: bulan **Oktober** dan tahun **2026**. Ubah sesuai periode.

**4. Tekan Generate Laporan.**

> 📸 **SCREENSHOT OUW-19: Daftar Laporan Keuangan**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** data demo awal, tiga laporan RW berstatus **Published**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Generate Laporan Baru**, penyaring, dan kolom **STATUS**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-19-daftar-laporan.png`

> 📸 **SCREENSHOT OUW-20: Jendela Generate Laporan Baru**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/laporan (jendela **Generate Laporan Baru**)
> - **Kondisi:** bulan dan tahun sudah dipilih
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada kotak informasi biru, **Bulan**, **Tahun**, dan tombol **Generate Laporan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-20-generate-laporan.png`

### ✅ Hasilnya

- Muncul pesan **"Laporan berhasil dibuat dalam status Draft. Silakan review laporan terlebih dahulu, lalu klik tombol "Ajukan" untuk mengirimkannya ke Ketua."**
- Laporan tampil di tabel dengan **PERIODE**, **JUDUL LAPORAN**, **TOTAL PEMASUKAN**, **TOTAL PENGELUARAN**, **SALDO AKHIR**, dan status **Draft**. Angka dihitung dari transaksi kas RW pada bulan itu (di luar yang ditolak) dengan saldo awal dari bulan-bulan sebelumnya.

### ⚠️ Perhatian

- **"Laporan untuk periode ini sudah ada (Status: ...)."** Sudah ada laporan untuk bulan itu. Pada data demo, bulan ini dan dua bulan sebelumnya sudah memiliki laporan. Pilih bulan lain.
- **Nama bulan tampil berbahasa Inggris.** Kolom **PERIODE** dan judul laporan memakai nama bulan seperti *October 2026*. Itu perilaku aplikasi saat ini, bukan kesalahan data.
- **Pilihan Tahun pada penyaring hanya 2025 dan 2026.** Gunakan **Semua Tahun** untuk laporan di luar itu.
- **Laporan berstatus Ditolak lalu Anda generate ulang.** Laporan lama yang ditolak otomatis dihapus dan diganti laporan baru berstatus **Draft** (angka dihitung ulang).

> 💡 **Tips:** Sebelum mengajukan, buka **Catat Transaksi** untuk bulan itu dan pastikan semua transaksi sudah tercatat dan benar. Setelah diajukan, transaksi pada bulan itu terkunci.

> ➡️ **Cerita berlanjut:** Lanjut ke 8.2.

### 8.2 Mengajukan, Mempublikasikan, dan Mengunduh (C-11 bagian 2)

> 👤 **SIMULASI C-11: Laporan keuangan RW (bagian 2 dan 3)**
>
> **Pak Hendra** meninjau draf laporan, lalu mengajukannya. Setelah Ketua RW atau Admin RW menyetujui, ia mempublikasikan laporan agar warga bisa membacanya di menu **Laporan Keuangan** mereka, dan mengunduh PDF untuk arsip rapat.
>
> - **Masuk sebagai:** `opkeuanganrw21`
> - **Laporan:** Laporan Keuangan RW untuk periode yang baru dibuat
> - **Sebelumnya:** laporan berstatus **Draft** (8.1)
> - **Selanjutnya:** warga membaca laporan. Lihat `01-Warga.md`

**Langkah-langkah**

**1. Ajukan.**
Pada baris laporan **Draft** atau **Ditolak**, tekan tombol ikon pesawat kertas (tooltip **Ajukan ke Ketua RT**; maksudnya mengajukan ke penyetuju). Muncul **Ajukan Laporan**: *Apakah Anda yakin ingin mengajukan laporan ini ke Ketua untuk persetujuan?* Tekan **Ajukan**.

**2. Tunggu persetujuan.**
Status berubah menjadi **Pending Review**. Ketua RW atau Admin RW memeriksa dan menyetujui atau menolak. Bila ditolak, catatan penolakan tampil di bawah judul laporan (*Catatan: ...*) dan Anda menerima notifikasi **Laporan Keuangan Ditolak**.

**3. Publikasikan.**
Setelah status **Approved**, tombol ikon unggah (tooltip **Publikasikan ke warga**) muncul. Tekan, lalu pada **Publikasikan Laporan** (*Laporan yang dipublish dapat dilihat oleh Warga.*) tekan **Publikasikan**. Status menjadi **Published**.

**4. Unduh.**
Tekan ikon PDF (**Unduh PDF**, terbuka di tab baru) atau ikon lembar kerja (**Unduh Excel**). Tombol ini tersedia untuk semua status.

**5. Hapus (bila perlu).**
Ikon tempat sampah (**Hapus laporan**) tersedia untuk laporan yang belum **Approved** dan belum dipublikasikan. Muncul **Hapus Laporan**: tekan **Hapus**.

> 📸 **SCREENSHOT OUW-21: Konfirmasi Ajukan Laporan**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** laporan berstatus **Draft**, kotak **Ajukan Laporan** tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada ikon ajukan di kolom **AKSI** dan tombol **Ajukan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-21-ajukan-laporan.png`

> 📸 **SCREENSHOT OUW-22: Laporan Approved dengan tombol Publikasikan**
> - **Akun:** opkeuanganrw21 (Op Keuangan RW)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** laporan berstatus **Approved** (belum dipublikasikan); bila tidak ada, minta Ketua RW atau Admin RW menyetujui laporan yang diajukan di langkah sebelumnya
> - **Area:** baris laporan dan kolom **AKSI**
> - **Sorot:** kotak merah pada ikon **Publikasikan ke warga**, **Unduh PDF**, dan **Unduh Excel**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUW-22-laporan-approved-publikasi.png`

### ✅ Hasilnya

- Setelah ajukan: pesan **"Laporan berhasil diajukan ke Ketua untuk peninjauan dan persetujuan."**, status **Pending Review**.
- Setelah publikasi: pesan **"Laporan berhasil di-publish dan kini dapat dilihat oleh publik."**, status **Published**.
- Setelah hapus: pesan **"Laporan berhasil dihapus."**
- Warga membuka laporan melalui menu **Laporan Keuangan** pada akun mereka.

### ⚠️ Perhatian

- **"Hanya laporan berstatus Draft atau Rejected yang dapat diajukan."** Laporan sudah diajukan atau disetujui.
- **"Hanya laporan yang sudah Approved yang bisa di-publish."** Tunggu persetujuan dulu.
- **"Laporan ini sudah di-publish."** Tidak perlu dipublikasikan lagi.
- **"Laporan yang sudah disetujui tidak dapat dihapus."** dan **"Laporan yang sudah dipublikasikan tidak dapat dihapus."** Laporan yang disetujui tidak dihapus.
- **Halaman persetujuan tidak ada di menu.** Menu **Persetujuan Keuangan** untuk Ketua RW dan Admin RW saat ini disembunyikan dari menu samping, sehingga tidak tampil di panduan ini. Tanyakan kepada Admin Aplikasi bila laporan lama tidak kunjung diputuskan.
- **Angka laporan berubah.** Laporan berstatus **Draft** dan **Pending Review** otomatis menghitung ulang angkanya setiap halaman dibuka. Setelah **Approved**, angka tidak berubah lagi.

> 💡 **Tips:** Unduh PDF setelah laporan **Approved**, lalu simpan untuk rapat RW atau pemeriksaan.

> ➡️ **Cerita berlanjut:** Ketua RW atau Admin RW menyetujui laporan. Lihat **`06-Ketua-RW.md` dan `05-Admin-RW.md`, cerita C-11**. Versi DKM ada di `09-DKM.md`, C-11.

---

## 9. Cerita Lengkap (Lintas Peran)

| Kode | Cerita | Peran Anda | Rujukan |
|---|---|---|---|
| C-03 | Pak Budi Santoso membayar "Iuran Perbaikan Got" | Op Keuangan RW membuat iuran dan memantau rekap per RT. Verifikasi pembayaran warga dilakukan Op Keuangan RT 03 | 5.1 dan 5.2 di file ini; `01-Warga.md`, C-03; `04-Op-Keuangan-RT.md`, C-03 |
| C-04 | Setoran iuran bulanan RT 03 ke RW | Op Keuangan RW mengonfirmasi setoran | 6.2 di file ini; `04-Op-Keuangan-RT.md`, C-04 |
| C-11 | Laporan keuangan disusun, diajukan, disetujui, lalu tampil di Transparansi | Op Keuangan RW menyusun, mengajukan, dan mempublikasikan | 8.1 dan 8.2 di file ini; `06-Ketua-RW.md`, `05-Admin-RW.md`, C-11; `01-Warga.md` |

**Ringkasan C-03**
1. Pak Hendra membuat "Iuran Perbaikan Got", dikumpulkan lewat RT (5.1).
2. Pak Budi membayar dan Bu Siti Aminah (RT 03) memverifikasi.
3. Pak Hendra memantau **Rekap per RT** (5.2). Uang baru masuk kas RW lewat setoran (C-04).

**Ringkasan C-04**
1. Bu Siti mencatat setoran ke RW dengan bukti.
2. Pak Hendra memeriksa di tab **Konfirmasi**, lalu **Konfirmasi** (6.2).
3. Kas RT berkurang, kas RW bertambah.

**Ringkasan C-11**
1. Pak Hendra menyusun laporan bulanan (8.1) dan mengajukannya (8.2).
2. Ketua RW atau Admin RW menyetujui.
3. Pak Hendra mempublikasikan, warga membaca di menu **Laporan Keuangan**.

> ➡️ **Cerita berlanjut:** Kembali ke `README.md` untuk daftar cerita lain.

---

## 10. Masalah yang Sering Terjadi (FAQ)

**Saya tidak melihat menu Iuran Wajib.**
Menu itu khusus Bendahara RT. Akun RW memiliki **Iuran Lainnya** dan **Setoran dari RT**.

**Saya tidak bisa menyetujui pembayaran warga pada Iuran Perbaikan Got.**
Iuran itu dikumpulkan lewat RT, sehingga yang memverifikasi adalah Bendahara RT pemilik KK. RW hanya melihat rekap dan mengonfirmasi setoran.

**Transaksi saya tidak muncul di buku kas.**
Halaman menampilkan bulan berjalan. Atur **Tahun** dan **Bulan**, lalu tekan **Filter**.

**Transaksi tidak bisa diubah, ada tulisan "Terkunci".**
Laporan keuangan periode itu sudah diajukan, disetujui, atau dipublikasikan.

**Tidak bisa mencatat transaksi dengan tanggal bulan lalu.**
Kemungkinan laporan bulan lalu sudah diajukan atau disetujui, sehingga periodenya terkunci.

**Transaksi dari iuran tidak bisa diedit.**
Transaksi hasil iuran atau setoran hanya bisa dibatalkan lewat menu asalnya (**Iuran Lainnya** atau **Setoran dari RT**).

**Saldo kas RW tidak naik setelah Pak Budi bayar iuran.**
Untuk iuran lewat RT, uang tercatat di kas RT dulu. Kas RW naik setelah setoran RT dikonfirmasi.

**Tombol ajukan laporan bertuliskan "Ajukan ke Ketua RT".**
Itu hanya tulisan tooltip. Untuk laporan RW, penyetujunya adalah Ketua RW atau Admin RW.

**Saya tidak menemukan halaman untuk menyetujui pengeluaran atau transaksi Pending.**
Fitur persetujuan pengeluaran tidak tersedia di akun ini. Transaksi yang Anda catat langsung berstatus sah.

**Menu Bantuan di bagian bawah tidak membuka apa pun.**
Menu itu belum memiliki halaman.

---

## 11. Lampiran: Daftar Gambar

Semua gambar desktop 1366×768. Nama file mengikuti kolom terakhir.

| Kode | Judul | Bab | Nama file |
|---|---|---|---|
| OUW-01 | Menu samping Operator Keuangan RW | 1.3 | `OUW-01-menu-samping.png` |
| OUW-02 | Dashboard Keuangan RW 21 | 2.2 | `OUW-02-dashboard-keuangan-rw.png` |
| OUW-03 | Buku kas RW | 3.1 | `OUW-03-buku-kas-rw.png` |
| OUW-04 | Jendela Catat Transaksi Baru | 3.2 | `OUW-04-catat-transaksi-baru.png` |
| OUW-05 | Transaksi baru di buku kas | 3.2 | `OUW-05-transaksi-baru.png` |
| OUW-06 | Menu aksi transaksi | 3.3 | `OUW-06-menu-aksi-transaksi.png` |
| OUW-07 | Pengaturan Rekening RW | 4 | `OUW-07-pengaturan-rekening.png` |
| OUW-08 | Jendela Buat Iuran Lainnya | 5.1 | `OUW-08-buat-iuran-lainnya.png` |
| OUW-09 | Daftar Iuran Lainnya | 5.1 | `OUW-09-daftar-iuran-lainnya.png` |
| OUW-10 | Detail iuran yang dikumpulkan lewat RT | 5.2 | `OUW-10-detail-iuran-via-rt.png` |
| OUW-11 | Tab Pembayaran Warga dan jendela Detail Pembayaran | 5.3 | `OUW-11-verifikasi-pembayaran.png` |
| OUW-12 | Jendela Catat Pembayaran | 5.3 | `OUW-12-catat-pembayaran.png` |
| OUW-13 | Rekap per RT | 6.1 | `OUW-13-rekap-per-rt.png` |
| OUW-14 | Tab Konfirmasi setoran | 6.2 | `OUW-14-tab-konfirmasi-setoran.png` |
| OUW-15 | Jendela Detail Setoran RT | 6.2 | `OUW-15-detail-setoran-rt.png` |
| OUW-16 | Jendela Batalkan Setoran | 6.3 | `OUW-16-batalkan-setoran.png` |
| OUW-17 | Jendela Nominal Setoran per KK | 6.4 | `OUW-17-nominal-setoran.png` |
| OUW-18 | Daftar Kategori Transaksi dan jendela Tambah Kategori | 7 | `OUW-18-tambah-kategori-transaksi.png` |
| OUW-19 | Daftar Laporan Keuangan | 8.1 | `OUW-19-daftar-laporan.png` |
| OUW-20 | Jendela Generate Laporan Baru | 8.1 | `OUW-20-generate-laporan.png` |
| OUW-21 | Konfirmasi Ajukan Laporan | 8.2 | `OUW-21-ajukan-laporan.png` |
| OUW-22 | Laporan Approved dengan tombol Publikasikan | 8.2 | `OUW-22-laporan-approved-publikasi.png` |

Jumlah: 22 gambar.
