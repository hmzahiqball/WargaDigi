# Manual Book Operator Keuangan RT (Bendahara RT)

Panduan untuk **Operator Keuangan RT** atau **Bendahara RT** di WargaDigi 21 (https://wargadigi21.com). Operator Keuangan RT mencatat kas RT, mengelola iuran bulanan warga, memverifikasi bukti bayar warga, menyetor iuran ke RW, serta menyusun laporan keuangan bulanan RT yang diajukan ke Ketua RT.

<!-- AI-INSTRUKSI
- Ikuti konvensi blok di README.md (SIMULASI, SCREENSHOT, Hasilnya, Perhatian, Tips, Cerita berlanjut).
- Kode gambar awalan OUT, nomor urut sesuai Lampiran. Semua gambar desktop 1366x768.
- Akun utama: operator_rt3 / password (Bu Siti Aminah, Bendahara RT 03). Akun pendamping: opkeuanganrt01rw21 / password (Bu Wati, fiktif, Bendahara RT 01). Di aplikasi hanya username yang tampil.
- Pengambilan gambar memakai RT 03 (operator_rt3) kecuali dinyatakan lain. RT 01 dipakai untuk contoh laporan demo dan tagihan "Iuran Perbaikan Jalan".
- Data demo: master iuran RT 01 dan RT 03 "Iuran Bulanan Warga" Rp60.000 per bulan tahun berjalan. Rekening RT 01 BSI 7634523890 a.n. Budi Suseno. Rekening RT 03 BSI 0998988997 a.n. Bendahara RT 03 (tanpa QRIS). Setoran ke RW Rp50.000 per KK lunas per bulan. Iuran Lainnya RT: "Perbaikan Toa Masjid" Rp20.000 (RT 03) dan "Iuran Perbaikan Jalan" Rp40.000 (RT 01). Iuran Lainnya RW "Iuran Perbaikan Got" Rp30.000 dikumpulkan lewat RT (tampil di RT 01 dan RT 03).
- Semua pembayaran demo berstatus belum bayar. Simulasi C-03 dan C-04 baru bisa dijalankan setelah Pak Budi Santoso mengirim pembayaran (lihat 01-Warga.md, C-03 dan C-04 bagian 1). Setoran juga belum ada pada data demo.
- Data demo memuat laporan keuangan untuk RW, DKM, dan RT 01. RT 03 belum punya laporan, jadi simulasi C-11 diambil dengan akun operator_rt3. Gambar daftar laporan yang berisi data demo diambil dengan akun opkeuanganrt01rw21.
- Beberapa tulisan di layar memang berbahasa Inggris atau campuran (nama bulan pada kolom PERIODE laporan dan filter Bulan buku kas, status Draft/Approved). Tulis apa adanya.
- Jangan memuat menu "Bantuan" di bagian bawah menu samping (belum memiliki halaman).
-->

## Daftar Isi

1. Kenalan Dulu
   - 1.1 Tugas Operator Keuangan RT
   - 1.2 Tokoh dalam Panduan Ini
   - 1.3 Peta Menu
2. Mulai Menggunakan
   - 2.1 Masuk ke Aplikasi
   - 2.2 Membaca Dashboard Keuangan RT
3. Pengaturan Rekening
4. Iuran Bulanan (menu Iuran Wajib)
   - 4.1 Mengatur Iuran Bulanan Tahun Ini
   - 4.2 Membaca Rekap Warga dan Bulan
   - 4.3 Memverifikasi Bukti Bayar Warga (C-04 bagian 2)
   - 4.4 Mencatat Pembayaran Langsung
   - 4.5 Membatalkan Pembayaran dan Mengirim Pengingat
5. Iuran Lainnya
   - 5.1 Membuat Iuran Lainnya Khusus RT
   - 5.2 Memverifikasi Iuran RW yang Dikumpulkan Lewat RT (C-03 bagian 2)
   - 5.3 Mencatat Bayar Langsung dan Mengingatkan
6. Catat Transaksi (Buku Kas RT)
   - 6.1 Membaca Buku Kas
   - 6.2 Mencatat Transaksi Baru
   - 6.3 Mengubah dan Menghapus Transaksi
7. Setoran ke RW
   - 7.1 Membaca Kewajiban Setoran
   - 7.2 Menyetor Iuran Bulanan ke RW (C-04 bagian 3)
   - 7.3 Menyetor Iuran Lainnya dari RW (C-03 bagian 3)
   - 7.4 Setelah Setoran Dikirim: Menunggu, Ditolak, Dibatalkan
8. Kategori Transaksi
9. Laporan Keuangan
   - 9.1 Menyusun Laporan Bulanan (C-11 bagian 1)
   - 9.2 Mengajukan ke Ketua RT, Mempublikasikan, dan Mengunduh (C-11 bagian 2)
10. Cerita Lengkap (Lintas Peran)
11. Masalah yang Sering Terjadi (FAQ)
12. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### 1.1 Tugas Operator Keuangan RT

Sebagai Operator Keuangan RT, Anda bertugas:

- Menetapkan **iuran bulanan warga** RT dan memeriksa **bukti bayar** yang dikirim warga. Pembayaran yang disetujui otomatis masuk ke kas RT.
- Mencatat pembayaran iuran yang diterima langsung (tunai atau transfer) dari warga.
- Membuat **Iuran Lainnya** untuk warga RT (iuran sekali bayar), dan memverifikasi pembayaran **Iuran Lainnya dari RW** yang dikumpulkan lewat RT.
- Mencatat **pemasukan dan pengeluaran kas RT** pada buku kas.
- Menyetor iuran yang terkumpul ke RW lewat **Setoran ke RW**.
- Mengatur **rekening bank dan QRIS** RT yang dilihat warga saat membayar.
- Mengelola **kategori transaksi** khusus RT.
- Menyusun **laporan keuangan bulanan** RT, mengajukannya ke **Ketua RT**, lalu mempublikasikannya agar warga dapat melihat.

Anda hanya melihat dan mengubah data **RT Anda sendiri**. Kas RW, kas DKM, dan data RT lain tidak tampil di akun ini.

### 1.2 Tokoh dalam Panduan Ini

> 👤 **SIMULASI: Bu Siti Aminah, Bendahara RT 03**
>
> **Bu Siti Aminah** tercatat sebagai Bendahara RT 03 di data kepengurusan. Ia menerima iuran warga RT 03, memeriksa bukti bayar, dan menyetor sebagian ke RW.
>
> - **Masuk sebagai:** `operator_rt3`, kata sandi `password`
> - **Cerita yang dimainkan:** C-03 bagian 2 (Iuran Perbaikan Got), C-04 bagian 2 dan 3 (iuran bulanan Pak Budi Santoso dan setoran ke RW), dan C-11 (laporan keuangan RT)
> - **Tokoh lain:** Pak Budi Santoso (warga RT 03 yang membayar), Pak Budi Sampurno (Ketua RT 03 yang menyetujui laporan), dan Pak Hendra Gunawan (Bendahara RW yang mengonfirmasi setoran)

> 👤 **SIMULASI: Bu Wati, Bendahara RT 01 (fiktif)**
>
> **Bu Wati** adalah tokoh fiktif untuk akun Bendahara RT 01. Nama ini tidak tampil di aplikasi, hanya username-nya. Akunnya dipakai untuk melihat contoh data demo RT 01, misalnya laporan keuangan yang sudah ada.
>
> - **Masuk sebagai:** `opkeuanganrt01rw21`, kata sandi `password`
> - **Rekening RT 01:** BSI 7634523890 a.n. Budi Suseno

### 1.3 Peta Menu

| Menu | Gunanya |
|---|---|
| **Dashboard** | Ringkasan kas RT, iuran bulan ini, dan pekerjaan yang menunggu |
| **Catat Transaksi** | Buku kas RT: catat, ubah, hapus transaksi |
| **Laporan Keuangan** | Menyusun, mengajukan ke Ketua RT, mempublikasikan, dan mengunduh laporan |
| **Iuran Warga** | Grup menu berisi **Iuran Wajib**, **Iuran Lainnya**, dan **Setoran ke RW** |
| **Kategori Transaksi** | Mengatur kategori transaksi khusus RT |
| **Pengaturan Rekening** | Rekening bank dan QRIS RT |
| **Keluar** | Keluar dari aplikasi |

Submenu **Iuran Wajib** membuka halaman berjudul **Iuran Bulanan Warga**. Keduanya sama. Di akun warga, menu yang sama bernama **Iuran Bulanan**.

> 📸 **SCREENSHOT OUT-01: Menu samping Operator Keuangan RT**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/dashboard
> - **Kondisi:** grup **Iuran Warga** dibuka sehingga **Iuran Wajib**, **Iuran Lainnya**, dan **Setoran ke RW** terlihat
> - **Area:** menu samping kiri dari atas sampai **Keluar**
> - **Sorot:** kotak merah mengelilingi seluruh daftar menu, nomor 1 sampai 6 di samping tiap menu
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-01-menu-samping.png`

---

## 2. Mulai Menggunakan

### 2.1 Masuk ke Aplikasi

Buka https://wargadigi21.com, lalu masuk dengan akun `operator_rt3` dan kata sandi `password`. Langkah masuk, memilih peran, dan keluar dijelaskan di `00-Umum-Publik-dan-Login.md`. Akun ini punya satu peran, jadi Anda langsung masuk ke dashboard keuangan.

### 2.2 Membaca Dashboard Keuangan RT

Halaman **Dashboard Keuangan RT 03** menampilkan angka dari kas RT 03 saja. Isinya:

- **Perlu Tindakan:** daftar pekerjaan, misalnya **Pengajuan iuran bulanan menunggu verifikasi**, **Pembayaran Iuran Lainnya menunggu verifikasi**, **Setoran ke RW belum lunas**, **Setoran Iuran Lainnya dari RW belum lunas**, **Setoran ditolak RW, perlu diperbaiki**, **Laporan ... belum dibuat**, **Laporan ditolak, perlu revisi**, dan **Laporan draft belum diajukan**. Bila kosong tertulis *Tidak ada pekerjaan yang tertunda.*
- **Laporan Terakhir:** status laporan terbaru (**Draft, belum diajukan**, **Menunggu persetujuan Ketua RT**, **Disetujui**, atau **Ditolak**), dengan tautan **Kelola**.
- **Saldo Kas RT**, **Pemasukan** bulan ini, **Pengeluaran** bulan ini, dan **Selisih bulan ini**.
- **Iuran Bulanan (nama bulan):** jumlah KK lunas, persen, dana terkumpul dibanding target, daftar **KK dengan tunggakan**, dan tombol **Kirim pengingat**.
- **Iuran Lainnya Aktif:** progres tiap iuran. Iuran buatan RW diberi lencana **Dari RW**.
- Grafik **Pemasukan vs Pengeluaran (6 bulan)** dan **Pengeluaran per Kategori** bulan ini.
- **Transaksi Terbaru**, dengan tautan **Lihat semua**. Transaksi hasil iuran diberi lencana **Iuran**.

> 📸 **SCREENSHOT OUT-02: Dashboard Keuangan RT 03**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/dashboard
> - **Kondisi:** data demo awal
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada **Perlu Tindakan**, **Saldo Kas RT**, kotak **Iuran Bulanan**, dan **Iuran Lainnya Aktif**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-02-dashboard-keuangan-rt.png`

### ⚠️ Perhatian

- **Kotak "Iuran tahun ini belum diatur".** Anda belum menetapkan iuran bulanan tahun ini. Lihat 4.1.
- **Angka kas tampak kecil atau nol.** Dashboard hanya menghitung kas RT Anda. Uang iuran yang belum disetujui belum masuk kas.

> 💡 **Tips:** Buka dashboard setiap hari. Kotak **Perlu Tindakan** langsung menunjukkan bukti bayar warga yang menunggu diperiksa.

> ➡️ **Cerita berlanjut:** Bab 3 membahas rekening, karena warga melihat rekening ini saat membayar.

---

## 3. Pengaturan Rekening

Menu **Pengaturan Rekening** menyimpan rekening bank dan QRIS RT. Informasi ini muncul di akun warga pada kotak **Rekening RT** saat mereka membayar iuran bulanan dan Iuran Lainnya. Pada Iuran Lainnya dari RW yang dikumpulkan lewat RT, warga juga membayar ke rekening RT ini.

> 👤 **SIMULASI: Memeriksa rekening RT 03**
>
> Rekening RT 03 pada data demo sudah terisi. **Bu Siti** memeriksanya sebelum warga mulai membayar.
>
> - **Masuk sebagai:** `operator_rt3`
> - **Nama Bank:** BSI
> - **Nomor Rekening:** 0998988997
> - **Atas Nama:** Bendahara RT 03
> - **QRIS:** belum ada (opsional)

**Langkah-langkah**

**1. Buka menu Pengaturan Rekening.**
Judul halaman **Pengaturan Rekening**, dengan lencana **RT** di keterangan.

**2. Isi atau periksa kolom.**
**Nama Bank** (wajib), **Nomor Rekening** (wajib), **Atas Nama** (wajib).

**3. Unggah QRIS (opsional).**
Pada **Upload QRIS (Opsional)** pilih gambar JPG, PNG, atau WEBP, maksimal 5 MB. Pratinjau muncul pada **Preview QRIS Baru yang Dipilih**. QRIS yang tersimpan tampil pada **QRIS Saat Ini**.

**4. Tekan Simpan Pengaturan.**

> 📸 **SCREENSHOT OUT-03: Pengaturan Rekening RT 03**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/rekening
> - **Kondisi:** rekening BSI 0998988997 terisi
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada **Nama Bank**, **Nomor Rekening**, **Atas Nama**, **Upload QRIS (Opsional)**, dan tombol **Simpan Pengaturan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-03-pengaturan-rekening.png`

### ✅ Hasilnya

- Muncul pesan **"Pengaturan rekening berhasil disimpan."**
- Warga RT 03 melihat rekening ini di jendela bayar. Bila QRIS belum diunggah, warga melihat keterangan bahwa QRIS belum diunggah dan tetap bisa memakai transfer atau tunai.

### ⚠️ Perhatian

- **"Nama bank wajib diisi."**, **"Nomor rekening wajib diisi."**, **"Atas nama rekening wajib diisi."** Lengkapi kolom yang diminta.
- **"Gagal memproses file QRIS: ..."** File QRIS rusak atau terlalu besar. Unggah ulang gambar yang lebih kecil.
- **Rekening RT 01 berbeda.** Setiap RT punya rekening sendiri. Akun `opkeuanganrt01rw21` melihat BSI 7634523890 a.n. Budi Suseno, bukan rekening RT 03.

> 💡 **Tips:** Mintalah warga memeriksa nama penerima sebelum mentransfer. Setelah mengganti nomor rekening, umumkan perubahan itu ke warga lewat Ketua RT.

> ➡️ **Cerita berlanjut:** Bab 4 membahas iuran bulanan.

---

## 4. Iuran Bulanan (menu Iuran Wajib)

Menu **Iuran Warga**, lalu **Iuran Wajib**, membuka halaman **Iuran Bulanan Warga**. Halaman ini berisi tabel tahunan: tiap baris adalah satu KK, tiap kolom adalah satu bulan. Judul kecil di bawahnya: *Kelola iuran wajib bulanan per KK. Centang pembayaran, otomatis masuk ke kas RT.*

Di kanan atas ada kotak **Tahun** dan tombol **Pengaturan**. Di bawahnya, lencana menampilkan nama iuran dan nominalnya (misalnya **Rp 60.000 / bulan**). Ada dua tab: **Rekap & Input** dan **Verifikasi** (dengan lencana jumlah pengajuan yang menunggu).

### 4.1 Mengatur Iuran Bulanan Tahun Ini

> 👤 **SIMULASI: Memeriksa pengaturan iuran RT 03**
>
> Data demo sudah memuat iuran **Iuran Bulanan Warga** Rp60.000 per bulan untuk RT 03 pada tahun berjalan. **Bu Siti** memeriksa pengaturannya sebelum warga mulai membayar.
>
> - **Masuk sebagai:** `operator_rt3`
> - **Nama Iuran:** Iuran Bulanan Warga
> - **Nominal per Bulan (per KK):** Rp 60.000
> - **Kategori Kas (pemasukan):** Iuran Bulanan Warga
> - **Aktif:** ya

**Langkah-langkah**

**1. Buka Iuran Warga, lalu Iuran Wajib.**

**2. Pilih Tahun.**
Gunakan kotak **Tahun** di kanan atas. Bila tahun itu belum punya iuran, tampil **"Belum ada iuran untuk tahun ..."** dengan tombol **Atur Iuran (tahun)**.

**3. Tekan Pengaturan.**
Jendela **Pengaturan Iuran (tahun)** terbuka.

**4. Isi atau periksa kolom.**
**Nama Iuran**, **Nominal per Bulan (per KK)**, **Kategori Kas (pemasukan)** (dipakai saat pembayaran dicatat ke kas RT), dan kotak **Aktif**.

**5. Tekan Simpan.**

> 📸 **SCREENSHOT OUT-04: Jendela Pengaturan Iuran**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran-bulanan (jendela **Pengaturan Iuran**)
> - **Kondisi:** pengaturan demo terbuka, belum diubah
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Nama Iuran**, **Nominal per Bulan (per KK)**, **Kategori Kas (pemasukan)**, **Aktif**, dan tombol **Simpan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-04-pengaturan-iuran.png`

**Mengubah nominal.** Bila nominal diganti, muncul kotak kuning **Konfirmasi perubahan nominal**. Pilih **Nominal baru berlaku mulai bulan**, baca ringkasannya (bulan sebelumnya tetap memakai nominal lama), lalu centang pernyataan **"Saya mengerti dan mengonfirmasi perubahan nominal ini berlaku mulai bulan yang dipilih (cut off), tanpa mengubah bulan sebelumnya."** Bagian **Riwayat nominal (tahun)** menampilkan nominal yang pernah berlaku.

### ✅ Hasilnya

- Muncul pesan **"Pengaturan iuran berhasil disimpan."**
- Bila nominal diubah: pesan *"Nominal iuran Rp ... berlaku mulai (bulan) (tahun). Pembayaran dan perhitungan bulan sebelumnya tetap memakai Rp ..."*
- Warga RT 03 melihat nominal ini di menu **Iuran Bulanan** mereka.

### ⚠️ Perhatian

- **"Perubahan nominal tidak boleh berlaku surut. Pilih bulan mulai berlaku ... atau sesudahnya."** Bulan yang sudah memiliki pembayaran atau laporan tidak bisa diubah. Pilih bulan yang lebih akhir.
- **"Nominal iuran (tahun) tidak dapat diubah lagi karena seluruh bulan tahun ini sudah memiliki pembayaran atau laporan. Atur nominal pada iuran tahun berikutnya."** Semua bulan sudah terkunci. Kolom nominal tampil terkunci.
- **"Centang konfirmasi perubahan nominal terlebih dahulu."** Centang kotak konfirmasi.
- **Iuran tidak tampil di akun warga.** Cek kotak **Aktif** pada pengaturan.

> 💡 **Tips:** Tetapkan nominal di awal tahun, sebelum ada pembayaran, supaya berlaku mulai Januari.

> ➡️ **Cerita berlanjut:** Lanjut ke 4.2.

### 4.2 Membaca Rekap Warga dan Bulan

Tab **Rekap & Input** menampilkan tabel dengan kolom **WARGA** (nama kepala keluarga dan No. KK), **Jan** sampai **Des**, dan **Total**. Kotak **Cari warga / No. KK...** menyaring baris.

Arti warna sel:

| Tampilan sel | Artinya |
|---|---|
| Hijau dengan tanda centang | **Lunas** |
| Oranye dengan ikon jam pasir | **Menunggu verifikasi** (warga sudah mengirim bukti) |
| Abu-abu kosong | **Belum bayar** |

Kolom **Total** menjumlahkan nominal bulan yang sudah lunas pada baris itu.

> 📸 **SCREENSHOT OUT-05: Rekap Iuran Bulanan Warga**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran-bulanan
> - **Kondisi:** tab **Rekap & Input**; bila simulasi 4.3 belum dijalankan, semua sel abu-abu (belum bayar)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada lencana **Rp 60.000 / bulan**, tombol **Pengaturan**, penjelasan warna, dan tabel bulan
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-05-rekap-iuran-bulanan.png`

### ⚠️ Perhatian

- **Daftar warga kosong ("Tidak ada data warga.").** RT Anda belum punya data keluarga, atau kata pencarian tidak cocok.
- **Tabel melebar.** Geser tabel ke kanan untuk melihat bulan berikutnya. Kolom **WARGA** tetap terlihat di kiri.

> 💡 **Tips:** Gunakan kotak pencarian untuk langsung ke satu keluarga saat warga bertanya soal tunggakannya.

> ➡️ **Cerita berlanjut:** Lanjut ke 4.3 untuk memeriksa bukti bayar warga.

### 4.3 Memverifikasi Bukti Bayar Warga (C-04 bagian 2)

> 👤 **SIMULASI C-04 (bagian 2): Memeriksa iuran bulanan Pak Budi Santoso**
>
> Pak **Budi Santoso** sudah mentransfer Rp60.000 ke rekening BSI RT 03 dan mengunggah foto struk (lihat `01-Warga.md`, C-04 bagian 1). **Bu Siti** menerima pengajuan itu, mencocokkan bukti dengan mutasi rekening, lalu menyetujuinya.
>
> - **Masuk sebagai:** `operator_rt3`, kata sandi `password`
> - **Pengajuan:** Budi Santoso, 1 bulan (bulan berjalan), Rp 60.000, Transfer
> - **Sebelumnya:** Pak Budi mengirim pembayaran dari akunnya. Tanpa langkah itu, tab **Verifikasi** kosong
> - **Selanjutnya:** Bu Siti menyetor iuran yang terkumpul ke RW (7.2)

**Langkah-langkah**

**1. Buka tab Verifikasi.**
Di halaman **Iuran Bulanan Warga**, tekan tab **Verifikasi**. Lencana kuning di tab menunjukkan jumlah pengajuan.

**2. Baca kartu pengajuan.**
Tiap kartu menampilkan nama kepala keluarga, No. KK, jumlah bulan beserta namanya, **Total**, metode, tanggal bayar, dan tautan **Lihat bukti**. Satu kali kirim dari warga menjadi satu kartu, walau terdiri dari beberapa bulan.

**3. Tekan Periksa.**
Jendela **Detail Pembayaran Iuran** terbuka: **Warga**, **No. KK**, **Periode**, **Jumlah bulan**, **Total**, **Metode**, **Tanggal bayar**, dan **Catatan warga** (bila ada). Foto bukti tampil di bawahnya dan bisa dibuka lebih besar dengan menekannya. Jika tidak ada bukti tertulis *Tidak ada bukti pembayaran dilampirkan.*

**4. Setujui atau tolak.**
- **Setujui:** tekan **Setujui** bila bukti jelas dan nominal sesuai.
- **Tolak:** tekan **Tolak**, isi **Alasan penolakan** (misalnya *Bukti transfer buram atau nominal tidak sesuai*), lalu **Kirim Penolakan**.

> 📸 **SCREENSHOT OUT-06: Tab Verifikasi dengan pengajuan warga**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran-bulanan (tab **Verifikasi**)
> - **Kondisi:** pengajuan Pak Budi Santoso (1 bulan, Rp 60.000, Transfer) tampil sebagai kartu oranye
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada lencana jumlah di tab **Verifikasi**, tautan **Lihat bukti**, dan tombol **Periksa**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-06-tab-verifikasi-iuran-bulanan.png`

> 📸 **SCREENSHOT OUT-07: Jendela Detail Pembayaran Iuran**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran-bulanan (jendela **Detail Pembayaran Iuran**)
> - **Kondisi:** detail pengajuan Pak Budi terbuka, foto struk terlihat
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Total**, foto bukti, tombol **Tolak**, dan tombol **Setujui**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-07-detail-pembayaran-iuran.png`

### ✅ Hasilnya

- Setelah **Setujui**: pesan **"Pembayaran 1 bulan disetujui dan masuk ke kas."** (untuk pengajuan satu per satu: *"Pembayaran warga disetujui dan masuk ke kas."*). Sel bulan itu berubah hijau, dan tercatat satu transaksi pemasukan di kas RT dengan kategori iuran.
- Setelah **Tolak**: pesan **"Pengajuan pembayaran ditolak."** Warga menerima notifikasi **Pembayaran Ditolak** beserta alasannya, lalu bisa mengajukan ulang.
- Pak Budi melihat kartu bulannya berubah dari **Menunggu verifikasi** menjadi lunas (lihat `01-Warga.md`).

### ⚠️ Perhatian

- **Kartu tidak ada di tab Verifikasi.** Warga belum mengirim bukti, atau KK-nya milik RT lain. Anda hanya melihat KK di RT Anda.
- **Alasan penolakan tidak terkirim.** Kolom **Alasan penolakan** wajib diisi, maksimal 500 karakter.
- **Nominal pada bukti berbeda dari Total.** Tolak dengan alasan yang jelas, atau minta warga mengirim ulang. Aplikasi tidak mengubah nominal pengajuan.

> 💡 **Tips:** Cocokkan nominal dan tanggal pada struk dengan mutasi rekening BSI RT sebelum menekan **Setujui**.

> ➡️ **Cerita berlanjut:** Uang terkumpul tercatat di kas RT. Lanjut ke 7.2 untuk menyetor ke RW. Bila ada pembayaran tunai langsung, lihat 4.4.

### 4.4 Mencatat Pembayaran Langsung

Bila warga membayar tunai (atau transfer) langsung kepada Anda tanpa mengirim bukti lewat aplikasi, Anda mencatatnya sendiri.

> 👤 **SIMULASI: Warga membayar tunai di rumah bendahara**
>
> Seorang warga RT 03 datang membawa uang iuran tiga bulan sekaligus. **Bu Siti** mencatatnya supaya rekap dan kas langsung benar.
>
> - **Masuk sebagai:** `operator_rt3`
> - **Bulan:** tiga bulan berturut-turut (di muka)
> - **Metode:** Tunai
> - **Bukti:** tidak ada (opsional)

**Langkah-langkah**

**1. Buka tab Rekap & Input** dan klik sel abu-abu pada baris warga dan kolom bulan yang dibayar.

**2. Periksa jendela Catat Pembayaran Iuran.**
Tertulis **Warga:** (nama kepala keluarga). Bulan yang Anda klik sudah tercentang di **Bulan yang dibayar**. Centang bulan lain bila warga membayar di muka. Keterangan di bawahnya: *Centang lebih dari satu bulan untuk pembayaran di muka.*

**3. Isi tanggal dan metode.**
**Tanggal Bayar** (tidak boleh melewati hari ini) dan **Metode** (**Tunai** atau **Transfer**).

**4. Lampirkan bukti dan catatan (opsional).**
**Bukti (opsional)**: JPG atau PNG, maksimal 5 MB. **Catatan (opsional)**: maksimal 500 karakter.

**5. Tekan Simpan & Masuk Kas.**

> 📸 **SCREENSHOT OUT-08: Jendela Catat Pembayaran Iuran**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran-bulanan (jendela **Catat Pembayaran Iuran**)
> - **Kondisi:** tiga bulan dicentang, metode **Tunai**, tanggal hari ini
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Bulan yang dibayar**, **Metode**, dan tombol **Simpan & Masuk Kas**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-08-catat-pembayaran-iuran.png`

### ✅ Hasilnya

- Muncul pesan **"Pembayaran 3 bulan berhasil dicatat sebagai satu transaksi kas."** (angka mengikuti jumlah bulan).
- Ketiga sel berubah hijau dan kas RT bertambah sebesar total tiga bulan, dalam satu transaksi.

### ⚠️ Perhatian

- **"Tidak ada bulan baru yang dicatat (mungkin sudah lunas)."** Bulan yang dipilih sudah lunas.
- **Sel oranye tidak bisa diklik.** Bulan itu sedang menunggu verifikasi. Selesaikan di tab **Verifikasi** (4.3).
- **Tanggal Bayar tidak bisa memilih tanggal depan.** Batasnya hari ini.

> 💡 **Tips:** Catat pembayaran tunai pada hari yang sama saat uangnya diterima, supaya buku kas tidak selisih.

> ➡️ **Cerita berlanjut:** Lanjut ke 4.5.

### 4.5 Membatalkan Pembayaran dan Mengirim Pengingat

**Membatalkan pembayaran yang sudah lunas.** Bila ada kekeliruan, klik sel hijau pada tabel rekap. Muncul dialog **Batalkan Pembayaran** dengan nama bulan dan warga, dan keterangan: *Seluruh pembayaran dalam batch ini akan dibatalkan dan transaksi kas terkait dihapus. Lanjutkan?* Tekan **Ya, Batalkan**.

> 📸 **SCREENSHOT OUT-09: Dialog Batalkan Pembayaran**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran-bulanan
> - **Kondisi:** satu sel hijau diklik, dialog tampil (jangan ditekan **Ya, Batalkan** bila data harus tetap)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada sel hijau, teks keterangan, dan tombol **Ya, Batalkan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-09-batalkan-pembayaran.png`

**Mengirim pengingat.** Tombol **Kirim pengingat** ada di **Dashboard**, pada kotak **Iuran Bulanan (nama bulan)** (bukan di halaman iuran). Tombol ini muncul bila masih ada KK yang belum lunas bulan ini. Tekan, lalu pada dialog **Kirim Pengingat** (*Kirim pengingat ke N KK yang belum lunas bulan ini?*) tekan **Kirim**.

### ✅ Hasilnya

- Setelah pembatalan: pesan **"Pembayaran dibatalkan dan transaksi kas terkait dihapus."** Sel kembali abu-abu dan kas RT berkurang.
- Setelah pengingat: pesan **"Pengingat iuran terkirim ke N KK yang belum lunas bulan ini."** Warga menerima notifikasi **Pengingat Iuran Bulanan**.

### ⚠️ Perhatian

- **Pembatalan membatalkan seluruh kiriman.** Bila tiga bulan dicatat dalam satu kali simpan, ketiganya dibatalkan bersama.
- **Pengingat tidak sampai ke satu KK.** Pengingat hanya dikirim ke KK yang sudah punya akun.
- **Pembatalan mengubah saldo.** Bila laporan bulan itu sudah diajukan atau disetujui, bicarakan dulu dengan Ketua RT. Setoran ke RW yang sudah dibuat dari bulan itu juga perlu diperiksa (bab 7).

> 💡 **Tips:** Batalkan hanya bila benar-benar salah catat. Untuk bukti yang kurang jelas, lebih aman menolak pengajuan di tab **Verifikasi**.

> ➡️ **Cerita berlanjut:** Bab 5 membahas Iuran Lainnya.

---

## 5. Iuran Lainnya

Menu **Iuran Warga**, lalu **Iuran Lainnya**, berisi iuran sekali bayar di luar iuran bulanan (kegiatan, perbaikan, sumbangan). Di akun RT, daftar ini memuat dua jenis:

- **Iuran buatan RT Anda sendiri.** Disebar ke seluruh KK di RT Anda. Pada data demo RT 03: **Perbaikan Toa Masjid** Rp20.000. Pada RT 01: **Iuran Perbaikan Jalan** Rp40.000.
- **Iuran buatan RW yang dikumpulkan lewat RT.** Diberi lencana **Dari RW, setor ke RW**. Pada data demo: **Iuran Perbaikan Got** Rp30.000.

Tabel berisi **Judul Iuran**, **Nominal**, **Tenggat**, **Progress Terbayar** (misalnya 3/40), dan tombol **Detail**. Hitungan progres hanya untuk KK di RT Anda. Lencana **Nominal fix** artinya nominal tidak bisa diubah warga. Lencana **Minimal** artinya warga boleh membayar lebih.

> 📸 **SCREENSHOT OUT-10: Daftar Iuran Lainnya RT 03**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran
> - **Kondisi:** data demo awal, tampil "Perbaikan Toa Masjid" dan "Iuran Perbaikan Got" (lencana **Dari RW, setor ke RW**)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada lencana **Dari RW, setor ke RW**, kolom **Progress Terbayar**, dan tombol **Buat Iuran Lainnya**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-10-daftar-iuran-lainnya.png`

### 5.1 Membuat Iuran Lainnya Khusus RT

> 👤 **SIMULASI: Iuran perbaikan Toa Masjid (RT 03)**
>
> Toa masjid di lingkungan RT 03 rusak. Warga RT 03 sepakat patungan. **Bu Siti** membuat iurannya supaya pembayaran tercatat rapi. (Iuran ini sudah ada pada data demo. Untuk mencoba, buat iuran contoh baru dengan judul lain.)
>
> - **Masuk sebagai:** `operator_rt3`
> - **Judul Iuran:** Perbaikan Toa Masjid
> - **Nominal:** Rp 20.000
> - **Kategori Kas:** Sumbangan & Donasi
> - **Tenggat Waktu:** sekitar dua minggu dari hari ini

**Langkah-langkah**

**1. Buka Iuran Warga, lalu Iuran Lainnya.**

**2. Tekan Buat Iuran Lainnya.**
Jendela **Buat Iuran Lainnya** terbuka.

**3. Isi Judul Iuran dan Nominal (Rp).**
Contoh: *Perbaikan Toa Masjid* dan *20.000*. Nominal ini adalah **nominal minimal**. Centang **Nominal fix (sesuai tertera, tidak bisa diubah)** bila nominalnya pasti.

**4. Pilih Kategori Kas (pemasukan).**
Pilih kategori pemasukan yang sesuai, misalnya **Sumbangan & Donasi**.

**5. Isi Tenggat Waktu dan Tanggal Pengingat.**
Tenggat berupa tanggal. **Tanggal Pengingat** adalah tanggal tiap bulan (1 sampai 31) saat pengingat dikirim ke warga yang belum bayar.

**6. Isi Deskripsi (Opsional)** lalu tekan **Buat & Sebar**.

Pada akun RT tidak ada kotak **Dikumpulkan lewat RT**. Kotak itu hanya ada di akun RW. Iuran buatan RT selalu dibayar ke RT Anda.

> 📸 **SCREENSHOT OUT-11: Jendela Buat Iuran Lainnya**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran (jendela **Buat Iuran Lainnya**)
> - **Kondisi:** formulir terisi seperti simulasi (jangan disimpan bila iuran itu sudah ada pada data demo)
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Nominal (Rp)**, **Nominal fix**, **Kategori Kas (pemasukan)**, **Tanggal Pengingat**, dan tombol **Buat & Sebar**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-11-buat-iuran-lainnya.png`

### ✅ Hasilnya

- Muncul pesan **"Tagihan insidental berhasil dibuat dan disebar ke N warga."** (N = jumlah KK di RT Anda).
- Iuran tampil di daftar dengan progres 0 dari jumlah KK. Warga RT Anda melihatnya di menu **Iuran Lainnya** akun mereka.

### ⚠️ Perhatian

- **"Nominal wajib diisi sebagai nominal minimal iuran."** Kolom nominal masih kosong.
- **"Tagihan gagal dibuat: tidak ada warga/keluarga pada unit ini untuk ditagih."** RT Anda belum punya data KK.
- **"Tagihan gagal dibuat: RT untuk akun bendahara ini tidak dapat ditentukan."** Akun belum terhubung ke RT. Hubungi Admin Aplikasi.

> 💡 **Tips:** Isi **Tanggal Pengingat** supaya warga diingatkan otomatis tanpa Anda mengirim satu per satu.

> ➡️ **Cerita berlanjut:** Lanjut ke 5.2.

### 5.2 Memverifikasi Iuran RW yang Dikumpulkan Lewat RT (C-03 bagian 2)

> 👤 **SIMULASI C-03 (bagian 2): Memeriksa pembayaran Iuran Perbaikan Got**
>
> RW membuat **Iuran Perbaikan Got** Rp30.000 dan meminta warga membayar lewat RT masing-masing. Pak **Budi Santoso** sudah mentransfer ke rekening BSI RT 03 dan mengunggah struk (lihat `01-Warga.md`, C-03 bagian 1). **Bu Siti** memeriksanya. Pada iuran jenis ini, verifikasi per warga memang menjadi tugas Bendahara RT, bukan RW.
>
> - **Masuk sebagai:** `operator_rt3`, kata sandi `password`
> - **Iuran:** Iuran Perbaikan Got (Rp 30.000, **Dari RW, setor ke RW**)
> - **Pembayar:** Budi Santoso, metode Transfer, dengan foto bukti
> - **Sebelumnya:** Pak Hendra (Bendahara RW) membuat iuran ini. Lihat `08-Op-Keuangan-RW.md`, 5.1
> - **Selanjutnya:** Bu Siti menyetor yang terkumpul ke RW (7.3)

**Langkah-langkah**

**1. Buka detail iuran.**
Di **Iuran Lainnya**, tekan **Detail** pada "Iuran Perbaikan Got".

**2. Baca keterangan di atas.**
Kotak biru menjelaskan: *Iuran ini dibuat RW dan dikumpulkan lewat RT. Verifikasi pembayaran warga RT Anda di sini, lalu setor seluruh yang terkumpul ke RW lewat menu Setoran ke RW.*

**3. Buka tab Pembayaran Warga.**
Ada dua tab: **Daftar & Catat** (semua KK di RT Anda) dan **Pembayaran Warga** (pengajuan yang menunggu, dengan lencana jumlah). Baris Pak Budi berstatus **Menunggu Verifikasi**. Tekan **Periksa**.

**4. Periksa jendela Detail Pembayaran.**
Terlihat **Warga**, **No. KK**, **Tagihan**, **Nominal**, **Metode**, **Waktu kirim**, dan foto bukti. Jika tidak ada bukti tertulis *Tidak ada bukti pembayaran dilampirkan.*

**5. Setujui atau tolak.**
- **Setujui:** nominal masuk ke kas RT sebagai pemasukan titipan.
- **Tolak:** tekan **Tolak**, isi **Alasan penolakan**, lalu **Kirim Penolakan**.

> 📸 **SCREENSHOT OUT-12: Detail iuran dari RW dan tab Pembayaran Warga**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran/{id} (tab **Pembayaran Warga**)
> - **Kondisi:** "Iuran Perbaikan Got", kotak biru keterangan terlihat, baris Pak Budi berstatus **Menunggu Verifikasi**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kotak biru, lencana jumlah di tab **Pembayaran Warga**, dan tombol **Periksa**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-12-detail-iuran-via-rt.png`

> 📸 **SCREENSHOT OUT-13: Jendela Detail Pembayaran Iuran Perbaikan Got**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran/{id} (jendela **Detail Pembayaran**)
> - **Kondisi:** detail pengajuan Pak Budi terbuka dengan foto struk
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Nominal**, foto bukti, tombol **Tolak**, dan tombol **Setujui**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-13-detail-pembayaran-got.png`

### ✅ Hasilnya

- Setelah **Setujui**: pesan **"Pembayaran berhasil disetujui dan dicatat ke keuangan."** Status baris menjadi **Lunas** (**Disetujui**).
- Uang tercatat sebagai pemasukan **di kas RT 03** dengan keterangan titipan iuran RW yang disetor kolektif ke RW. Kas RW belum bertambah sampai setoran dikonfirmasi RW.
- Setelah **Tolak**: pesan **"Pembayaran berhasil ditolak."** Baris kembali **Belum Bayar** dengan tanda **Ditolak**, dan warga menerima notifikasi berisi alasan.
- Di akun RW, angka **Terkumpul di RT** untuk RT 03 bertambah (lihat `08-Op-Keuangan-RW.md`, 5.2).

### ⚠️ Perhatian

- **Anda hanya melihat KK di RT Anda.** Warga RT lain diverifikasi Bendahara RT mereka. Bila dicoba lewat tautan langsung, muncul pesan *"Pembayaran ini bukan wewenang akun Anda."*
- **Penolakan tidak terkirim.** Kolom **Alasan penolakan** wajib diisi, maksimal 500 karakter.
- **Tab Pembayaran Warga kosong.** Tertulis *Tidak ada pembayaran dari warga yang menunggu verifikasi.* Warga belum mengirim bukti.

> 💡 **Tips:** Setelah menyetujui, buka **Setoran ke RW** untuk memastikan uang titipan ini ikut disetor (7.3).

> ➡️ **Cerita berlanjut:** Lanjut ke 5.3.

### 5.3 Mencatat Bayar Langsung dan Mengingatkan

Untuk iuran buatan RT maupun iuran RW yang dikumpulkan lewat RT, Anda bisa mencatat pembayaran tunai atau transfer langsung dari tab **Daftar & Catat**.

**Langkah-langkah**

**1. Buka detail iuran** dan tab **Daftar & Catat**.
Kolom: **Kepala Keluarga**, **Status**, **Nominal**, **Metode**, **Waktu Bayar**, **Bukti**, **Aksi**.

**2. Pada baris warga berstatus Belum Bayar, tekan Catat Bayar.**
Jendela **Catat Pembayaran** terbuka.

**3. Isi nominal, tanggal, dan metode.**
**Nominal (Rp)** terkunci bila iuran **Nominal fix**. Bila tidak, minimal sama dengan nominal iuran. **Tanggal Bayar** dan **Metode** (**Tunai**, **Transfer**, **QRIS**). **Bukti (opsional)**: JPG atau PNG, maksimal 5 MB.

**4. Tekan Simpan & Masuk Kas.**

**Mengingatkan satu warga.** Tombol lonceng (**Kirim Pengingat (Broadcast)**) pada baris **Belum Bayar** mengirim pengingat ke warga itu.

> 📸 **SCREENSHOT OUT-14: Jendela Catat Pembayaran Iuran Lainnya**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/iuran/{id} (tab **Daftar & Catat**, jendela **Catat Pembayaran**)
> - **Kondisi:** iuran "Perbaikan Toa Masjid", satu KK berstatus **Belum Bayar**, jendela terisi nominal, tanggal, metode **Tunai**
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Nominal (Rp)**, **Metode**, dan tombol **Simpan & Masuk Kas**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-14-catat-pembayaran-iuran-lainnya.png`

### ✅ Hasilnya

- Setelah **Simpan & Masuk Kas**: pesan **"Pembayaran dicatat dan masuk ke kas."** Status warga menjadi **Lunas**, tercatat transaksi pemasukan di kas RT.
- Setelah pengingat: pesan konfirmasi dengan nama kepala keluarga.

### ⚠️ Perhatian

- **"Nominal pembayaran tidak boleh kurang dari nominal minimal (Rp ...)."** Isi nominal sama dengan atau lebih besar dari minimum.
- **"Pembayaran ini sudah lunas."** Baris itu sudah diselesaikan sebelumnya.
- **"Warga ini belum memiliki akun aktif, pengingat tidak dapat dikirim."** KK belum punya akun. Hubungi lewat cara lain.
- **"Notifikasi aplikasi sedang dinonaktifkan di Pengaturan Sistem."** Minta Admin Aplikasi mengaktifkan notifikasi.
- **"Tagihan sudah dibayar atau sedang diverifikasi."** Pengingat hanya untuk yang **Belum Bayar**.

> 💡 **Tips:** Untuk iuran Rp20.000 yang dibayar tunai di rumah, pilih metode **Tunai** dan tidak perlu bukti.

> ➡️ **Cerita berlanjut:** Bab 6 membahas buku kas RT.

---

## 6. Catat Transaksi (Buku Kas RT)

### 6.1 Membaca Buku Kas

Menu **Catat Transaksi** berisi buku kas RT. Catatan di bagian atas menjelaskan: **Debet** adalah uang masuk, **Kredit** adalah uang keluar, kolom **Saldo** berjalan sejak awal pencatatan.

Penyaring: **Tahun**, **Bulan**, **Tipe Transaksi** (**Semua Tipe**, **Pemasukan**, **Pengeluaran**), dan **Kategori**, dengan tombol **Filter** dan **Reset Filter**. Bila Anda belum memilih penyaring, halaman otomatis menampilkan **bulan berjalan**. Nama bulan pada penyaring **Bulan** tampil berbahasa Inggris (misalnya *October*).

Kolom tabel: **TANGGAL**, **KETERANGAN**, **DEBET**, **KREDIT**, **SALDO**, **AKSI**. Baris **Total periode terpilih** menunjukkan saldo awal periode, total debet, total kredit, dan **Saldo akhir**.

Transaksi yang berasal dari iuran warga dan setoran ke RW juga tampil di sini, dengan nominal otomatis. Transaksi itu tidak bisa diubah dari buku kas (lihat 6.3).

> 📸 **SCREENSHOT OUT-15: Buku kas RT 03**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** bulan berjalan; sebaiknya setelah simulasi 4.3 supaya ada transaksi iuran Pak Budi (bila kosong, pilih **Bulan** yang sesuai lalu **Filter**)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada penyaring, kolom **DEBET**/**KREDIT**/**SALDO**, dan baris **Total periode terpilih**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-15-buku-kas-rt.png`

### 6.2 Mencatat Transaksi Baru

> 👤 **SIMULASI: Membeli perlengkapan kerja bakti RT 03**
>
> RT 03 mengadakan kerja bakti. **Bu Siti** membeli sapu, karung, dan konsumsi dari kas RT, lalu mencatat pengeluarannya lengkap dengan foto bukti.
>
> - **Masuk sebagai:** `operator_rt3`
> - **Tipe:** Pengeluaran
> - **Judul:** Perlengkapan Kerja Bakti
> - **Kategori:** Kegiatan & Acara Warga
> - **Tanggal:** hari ini atau sebelumnya (tidak boleh tanggal mendatang)
> - **Jumlah:** Rp 150.000
> - **Deskripsi:** Sapu, karung, dan konsumsi kerja bakti
> - **Bukti:** foto struk (JPG, PNG, atau PDF, maksimal 5 MB)

**Langkah-langkah**

**1. Buka menu Catat Transaksi** lalu tekan **Catat Transaksi Baru**.

**2. Pilih tipe.**
Pada **Tipe Transaksi** pilih **Pengeluaran (-)**. Pilihan lainnya **Pemasukan (+)**.

**3. Isi Judul Transaksi.**
Ketik *Perlengkapan Kerja Bakti*.

**4. Pilih Kategori.**
Buka **-- Pilih kategori --**. Daftar menyesuaikan tipe yang dipilih. Pilih **Kegiatan & Acara Warga**. Akun RT melihat kategori umum dan kategori khusus RT. Kategori milik RW atau DKM tidak muncul, kecuali RW sengaja menerapkannya untuk RT (lihat bab 8).

**5. Isi Tanggal dan Jumlah (Rp).**
Isi tanggal, lalu jumlah *150.000*. Titik pemisah ribuan boleh dipakai.

**6. Isi Deskripsi (Opsional).**

**7. Lampirkan bukti (opsional).**
Tarik file ke kotak **Drag & drop file Anda disini atau klik untuk browse**, atau tekan **Ambil Foto** untuk memotret struk dari kamera. Format JPG, PNG, PDF, maksimal 5 MB. Tombol **Batal Pilih File** membuang pilihan.

**8. Tekan Simpan Transaksi.**

> 📸 **SCREENSHOT OUT-16: Jendela Catat Transaksi Baru**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/transaksi (jendela **Catat Transaksi Baru**)
> - **Kondisi:** formulir terisi lengkap, bukti sudah dipilih, belum disimpan
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Tipe Transaksi**, **Kategori**, **Jumlah (Rp)**, kotak bukti, dan tombol **Simpan Transaksi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-16-catat-transaksi-baru.png`

### ✅ Hasilnya

- Muncul pesan **"Transaksi berhasil dicatat."**
- Transaksi tampil di buku kas dengan kode berawalan `TRX-RT01-...`, nominal di kolom **KREDIT**, dan saldo berjalan berkurang. Awalan `RT01` memang selalu dipakai untuk semua RT, termasuk RT 03. Itu hanya penanda kode, bukan kesalahan data kas.
- Transaksi langsung berstatus sah (tidak menunggu persetujuan siapa pun).

> 📸 **SCREENSHOT OUT-17: Transaksi baru di buku kas**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** transaksi baru tampil dengan lencana **Lampiran Bukti**
> - **Area:** tabel buku kas
> - **Sorot:** kotak merah pada baris transaksi baru, lencana **Lampiran Bukti**, dan saldo
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-17-transaksi-baru.png`

### ⚠️ Perhatian

- **Muncul "Terjadi kesalahan validasi:".** Satu atau lebih isian kurang. Baca daftar di bawah tulisan itu.
- **"Kategori tidak valid atau tidak sesuai dengan tipe transaksi."** Kategori tidak cocok dengan tipe. Pilih ulang kategori setelah memilih tipe.
- **Tanggal tidak boleh melewati hari ini.** Kolom tanggal dibatasi sampai hari ini.
- **"Tidak dapat mencatat transaksi pada periode ... karena laporan keuangan periode tersebut sudah diajukan, disetujui, atau dipublikasikan."** Periode itu sudah terkunci karena laporannya sudah berjalan. Lihat 9.2.
- **Transaksi tidak terlihat setelah disimpan.** Halaman menampilkan bulan berjalan. Pilih **Bulan** dan **Tahun** sesuai tanggal transaksi.

> 💡 **Tips:** Selalu lampirkan foto bukti. Saat warga atau Ketua RT bertanya, bukti menjadi penolong.

> ➡️ **Cerita berlanjut:** Lanjut ke 6.3.

### 6.3 Mengubah dan Menghapus Transaksi

Pada kolom **AKSI**, tekan tombol titik tiga (⋮). Pilihannya:

- **Lihat Bukti:** membuka lampiran bukti di tab baru (bila ada).
- **Edit:** membuka jendela **Edit Transaksi**. Setelah selesai tekan **Simpan Perubahan**. Pesan: **"Transaksi berhasil diperbarui."**
- **Hapus:** muncul **Hapus Transaksi** ("Apakah Anda yakin ingin menghapus transaksi ini?"), tekan **Hapus**. Pesan: **"Transaksi berhasil dihapus."** Penghapusan bersifat permanen.

> 📸 **SCREENSHOT OUT-18: Menu aksi transaksi**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/transaksi
> - **Kondisi:** tombol titik tiga pada transaksi "Perlengkapan Kerja Bakti" sudah ditekan
> - **Area:** baris transaksi dan menu yang terbuka
> - **Sorot:** kotak merah pada **Lihat Bukti**, **Edit**, dan **Hapus**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-18-menu-aksi-transaksi.png`

### ⚠️ Perhatian

- **Edit dan Hapus abu-abu dengan lencana "Terkunci".** Laporan periode itu sudah diajukan, disetujui, atau dipublikasikan. Transaksinya tidak bisa diubah lagi.
- **"Transaksi ini berasal dari Iuran Warga atau Setoran RT. Ubah atau batalkan melalui menu Iuran Warga / Setoran."** Transaksi hasil iuran atau setoran tidak boleh diubah dari buku kas. Batalkan lewat menu asalnya: **Iuran Wajib** (4.5), **Iuran Lainnya**, atau **Setoran ke RW** (7.4).
- **Tanggal dipindahkan ke periode terkunci.** Pesan: *"Tanggal transaksi tidak dapat dipindahkan ke periode ... karena laporan periode tersebut sudah diajukan, disetujui, atau dipublikasikan."*

> 💡 **Tips:** Bila laporan ternyata salah setelah diajukan, transaksinya tidak bisa diubah. Minta Ketua RT menolak laporan itu. Laporan yang ditolak membuka kembali periodenya.

> ➡️ **Cerita berlanjut:** Bab 7 membahas setoran ke RW.

---

## 7. Setoran ke RW

Menu **Iuran Warga**, lalu **Setoran ke RW**. Warga membayar ke RT, dan sebagian uang itu harus disetor ke RW. Halaman ini menghitung **kewajiban setoran**, mencatat setoran yang Anda serahkan, dan menunjukkan statusnya. Judul halaman: **Setoran ke RW - RT 03**. Keterangan: *Warga membayar ke RT, lalu yang terkumpul disetor ke RW. Setelah dikonfirmasi RW, kas RT dan RW tercatat otomatis.*

Dua jenis setoran:

- **Iuran Bulanan:** kewajiban = **nominal setoran per KK × jumlah KK yang lunas** pada bulan itu. Nominal per KK ditetapkan RW (pada data demo Rp50.000).
- **Iuran Lainnya dari RW:** sebesar yang sudah diterima RT dan belum disetor.

Anda mencatat setoran setelah uangnya benar-benar diserahkan ke Bendahara RW. Operator Keuangan RW lalu **mengonfirmasi atau menolaknya**. Kas RT dan kas RW baru berubah setelah RW mengonfirmasi.

### 7.1 Membaca Kewajiban Setoran

Di kanan atas ada kotak **Tahun**. Di bawah judul tampil nominal setoran: *Nominal setoran iuran bulanan: Rp 50.000 per KK yang lunas, per bulan (ditetapkan RW).* Empat kartu ringkasan: **Kewajiban (tahun)**, **Sudah dikonfirmasi**, **Menunggu konfirmasi**, **Sisa kewajiban**.

Bagian **Kewajiban Iuran Bulanan per Bulan** adalah tabel dengan kolom **Bulan**, **KK lunas**, **Iuran terkumpul**, **Kewajiban**, **Disetor**, **Menunggu**, dan **Sisa**. Tombol **Setor** muncul pada bulan yang masih punya sisa yang bisa disetor.

Bagian **Iuran Lainnya dari RW** (hanya tampil bila ada iuran dari RW yang dikumpulkan lewat RT) menampilkan tiap iuran: **x dari y KK lunas**, **Terkumpul**, **Disetor**, dan **Sisa**.

Bagian **Riwayat Setoran (tahun)** menampilkan setiap setoran beserta statusnya.

> 📸 **SCREENSHOT OUT-19: Setoran ke RW, kewajiban per bulan**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/setoran
> - **Kondisi:** setelah simulasi 4.3 dan 5.2 (Pak Budi sudah lunas), sehingga bulan berjalan punya kewajiban dan **Iuran Perbaikan Got** punya jumlah diterima
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada empat kartu ringkasan, baris bulan berjalan dengan tombol **Setor**, dan kartu **Iuran Lainnya dari RW**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-19-setoran-kewajiban.png`

### ⚠️ Perhatian

- **"RW belum menetapkan nominal setoran per KK untuk tahun ..., sehingga kewajiban setoran iuran bulanan belum dapat dihitung."** Nominal diatur oleh Operator Keuangan RW (lihat `08-Op-Keuangan-RW.md`, 6.4). Pada data demo nominalnya sudah Rp50.000.
- **Tombol Setor tidak muncul.** Belum ada KK yang lunas, atau kewajiban bulan itu sudah disetor penuh atau sedang menunggu konfirmasi.
- **Kartu "Iuran Lainnya dari RW" tidak ada.** RW belum membuat iuran yang dikumpulkan lewat RT.

> 💡 **Tips:** Nilai **Sisa** yang berwarna merah berarti ada kewajiban yang belum disetor. Dashboard juga mengingatkannya pada **Perlu Tindakan**.

> ➡️ **Cerita berlanjut:** Lanjut ke 7.2.

### 7.2 Menyetor Iuran Bulanan ke RW (C-04 bagian 3)

> 👤 **SIMULASI C-04 (bagian 3): Setoran iuran bulanan RT 03 ke RW**
>
> Iuran bulan ini dari Pak Budi Santoso sudah disetujui (4.3). **Bu Siti** menyerahkan Rp50.000 (hak RW untuk satu KK lunas) kepada **Pak Hendra Gunawan**, lalu mencatat setorannya di aplikasi.
>
> - **Masuk sebagai:** `operator_rt3`, kata sandi `password`
> - **Jenis:** Iuran Bulanan, bulan berjalan
> - **Nominal disetor:** Rp 50.000 (satu KK lunas)
> - **Metode:** Tunai atau Transfer, dengan bukti bila ada
> - **Selanjutnya:** Pak Hendra mengonfirmasi. Lihat `08-Op-Keuangan-RW.md`, C-04 (bagian RW)

**Langkah-langkah**

**1. Buka Iuran Warga, lalu Setoran ke RW.**

**2. Pilih bulan yang disetor.**
Tekan **Setor** pada baris bulan berjalan. Atau centang beberapa bulan (**Pilih semua** untuk semua bulan yang punya sisa), lalu tekan **Setor Iuran Bulanan**. Sebuah bar di bawah layar menunjukkan jumlah bulan dan total yang dipilih, dengan tombol **Setor Seluruhnya**.

**3. Periksa jendela Setor Iuran Bulanan ke RW.**
Tabel rincian berisi **Bulan**, **KK lunas**, **Kewajiban**, **Sudah/menunggu**, dan **Disetor (Rp)**. Nominal terisi sebesar sisa. Anda boleh mengurangi nominal untuk setoran sebagian, tetapi tidak boleh melebihi sisa. Centang atau kosongkan bulan lewat kotak di kiri, **Pilih semua**, atau **Kosongkan**. **Total disetor** dihitung otomatis.

**4. Isi data setoran.**
**Tanggal setor** (tidak boleh melewati hari ini), **Metode** (**Tunai** atau **Transfer**), **Bukti (opsional)** (JPG atau PNG, maksimal 5 MB), dan **Catatan (opsional)**.

**5. Tekan Kirim ke RW.**
Tombol tidak aktif bila total nol.

> 📸 **SCREENSHOT OUT-20: Jendela Setor Iuran Bulanan ke RW**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/setoran (jendela **Setor Iuran Bulanan ke RW**)
> - **Kondisi:** bulan berjalan tercentang, **Disetor (Rp)** Rp 50.000, tanggal hari ini, metode **Tunai**
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada tabel rincian, **Total disetor**, **Tanggal setor**, **Metode**, dan tombol **Kirim ke RW**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-20-setor-iuran-bulanan.png`

### ✅ Hasilnya

- Muncul pesan *"Setoran (periode) sebesar Rp 50.000 dicatat dan menunggu konfirmasi Operator Keuangan RW."*
- Di **Riwayat Setoran**, setoran tampil dengan status **Menunggu konfirmasi**, dan kartu **Menunggu konfirmasi** bertambah.
- Operator Keuangan RW menerima notifikasi **Setoran RT Menunggu Konfirmasi**.
- **Kas RT belum berkurang.** Kas RT dan kas RW baru berubah setelah RW menekan **Konfirmasi**.

> 📸 **SCREENSHOT OUT-21: Riwayat setoran berstatus Menunggu konfirmasi**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/setoran (bagian **Riwayat Setoran**)
> - **Kondisi:** setoran baru berstatus **Menunggu konfirmasi**
> - **Area:** bagian **Riwayat Setoran** dan kartu ringkasan
> - **Sorot:** kotak merah pada lencana **Menunggu konfirmasi**, tombol hapus (ikon tempat sampah), dan kartu **Menunggu konfirmasi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-21-riwayat-setoran-menunggu.png`

### ⚠️ Perhatian

- **"Pilih minimal satu bulan dengan nominal setoran."** Tidak ada bulan tercentang atau semua nominal nol.
- **"Nominal setoran (bulan) melebihi sisa yang dapat disetor (Rp ...)."** Kurangi nominal sampai tidak lebih dari sisa.
- **"Bulan (bulan) (tahun) belum berjalan, belum dapat disetor."** Bulan mendatang tidak bisa disetor.
- **Nominal otomatis turun saat diketik.** Kotak membatasi isian sampai sisa yang diizinkan.

> 💡 **Tips:** Serahkan uangnya lebih dulu, baru catat setoran. Dengan begitu catatan di aplikasi selalu cocok dengan uang yang benar-benar pindah tangan.

> ➡️ **Cerita berlanjut:** Pak Hendra, Bendahara RW, memeriksa dan mengonfirmasi setoran. Lihat **`08-Op-Keuangan-RW.md`, 6.2 (C-04)**. Untuk setoran iuran dari RW, lanjut ke 7.3.

### 7.3 Menyetor Iuran Lainnya dari RW (C-03 bagian 3)

> 👤 **SIMULASI C-03 (bagian 3): Setoran Iuran Perbaikan Got**
>
> Pembayaran **Iuran Perbaikan Got** dari Pak Budi (Rp30.000) sudah **Bu Siti** setujui (5.2) dan tercatat di kas RT 03 sebagai titipan. Sekarang ia menyetor uang itu ke RW.
>
> - **Masuk sebagai:** `operator_rt3`
> - **Jenis:** Iuran Lainnya dari RW
> - **Iuran:** Iuran Perbaikan Got
> - **Nominal disetor:** Rp 30.000 (sebesar yang diterima RT dan belum disetor)

**Langkah-langkah**

**1. Buka Setoran ke RW** dan lihat kartu **Iuran Lainnya dari RW**.
Pada "Iuran Perbaikan Got" tertulis jumlah KK lunas, **Terkumpul**, dan **Sisa**.

**2. Tekan Setor Iuran Lainnya.**
Tombol abu-abu (tidak aktif) bila belum ada uang yang bisa disetor. Jendela **Setor Iuran Lainnya ke RW** terbuka.

**3. Periksa rincian.**
Tabel berisi **Iuran**, **Diterima**, **Sudah/menunggu**, dan **Disetor (Rp)**. Nominal terisi sebesar sisa dan boleh dikurangi untuk setoran sebagian.

**4. Isi data setoran dan tekan Kirim ke RW.**
**Tanggal setor**, **Metode**, **Bukti (opsional)**, **Catatan (opsional)**, sama seperti 7.2.

> 📸 **SCREENSHOT OUT-22: Jendela Setor Iuran Lainnya ke RW**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/setoran (jendela **Setor Iuran Lainnya ke RW**)
> - **Kondisi:** "Iuran Perbaikan Got" tercentang, **Disetor (Rp)** terisi sesuai sisa
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada baris **Iuran Perbaikan Got**, **Total disetor**, dan tombol **Kirim ke RW**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-22-setor-iuran-lainnya.png`

### ✅ Hasilnya

- Muncul pesan *"Setoran (nama iuran) sebesar Rp ... dicatat dan menunggu konfirmasi Operator Keuangan RW."*
- Setoran tampil di **Riwayat Setoran** berstatus **Menunggu konfirmasi**, dan RW menerima notifikasi.
- Setelah RW mengonfirmasi, kolom **Disetor ke RW** pada rekap iuran di akun RW bertambah (lihat `08-Op-Keuangan-RW.md`, 5.2).

### ⚠️ Perhatian

- **"Pilih minimal satu Iuran Lainnya dengan nominal setoran."** Tidak ada iuran tercentang atau nominalnya nol.
- **"Nominal melebihi sisa yang dapat disetor untuk ... (Rp ...)."** Kurangi nominal.
- **Hanya iuran buatan RW yang disetor lewat menu ini.** Iuran buatan RT sendiri (misalnya **Perbaikan Toa Masjid**) tetap di kas RT dan tidak disetor ke RW.

> 💡 **Tips:** Bila ada beberapa iuran dari RW, setor sekaligus dalam satu setoran. Pak Hendra cukup mengonfirmasi satu kali.

> ➡️ **Cerita berlanjut:** Lanjut ke 7.4.

### 7.4 Setelah Setoran Dikirim: Menunggu, Ditolak, Dibatalkan

Setiap setoran di **Riwayat Setoran** punya status:

| Label di layar | Artinya | Yang bisa Anda lakukan |
|---|---|---|
| **Menunggu konfirmasi** | Menunggu Operator Keuangan RW | Hapus catatan |
| **Dikonfirmasi** | Diterima RW, kas RT dan RW sudah tercatat | Hanya RW yang bisa membatalkan |
| **Ditolak** | RW menolak, **Alasan:** tampil merah di bawah catatan | Hapus catatan, lalu catat ulang setelah dibereskan |
| **Dibatalkan** | RW membatalkan setoran yang sudah dikonfirmasi, **Alasan:** tampil | Tidak ada, catat ulang bila perlu |

**Menghapus catatan setoran.** Pada setoran berstatus **Menunggu konfirmasi** atau **Ditolak**, tekan ikon tempat sampah. Muncul **Hapus Catatan Setoran** (*Hapus catatan setoran ini?*), tekan **Hapus**. Pesan: **"Catatan setoran dihapus."**

**Notifikasi.** Anda menerima notifikasi **Setoran Dikonfirmasi**, **Setoran Ditolak** (beserta alasan), atau **Setoran Dibatalkan** (beserta alasan) dari RW.

### ⚠️ Perhatian

- **"Setoran yang sudah dikonfirmasi hanya dapat dibatalkan oleh Operator Keuangan RW."** Hubungi Bendahara RW bila ada salah catat setelah dikonfirmasi.
- **Setoran ditolak.** Baca alasannya. Biasanya nominal pada bukti tidak cocok. Hapus catatan lama, perbaiki, lalu catat setoran baru. Dashboard menandainya pada **Perlu Tindakan** (*Setoran ditolak RW, perlu diperbaiki*).
- **Setoran dibatalkan RW.** Transaksi di kas RT dan kas RW ikut terhapus, dan kewajiban kembali menjadi sisa.

> 💡 **Tips:** Tulis catatan setoran yang jelas (misalnya "diserahkan langsung ke Pak Hendra") supaya RW mudah mencocokkan.

> ➡️ **Cerita berlanjut:** Bab 8 membahas kategori transaksi.

---

## 8. Kategori Transaksi

Menu **Kategori Transaksi** mengatur kategori untuk transaksi kas RT. Keterangan di atas halaman: *Kategori khusus unit RT Anda. Kategori umum dari Admin otomatis tersedia.*

Penyaring: **Cari Nama Kategori**, **Tipe** (**Pemasukan**, **Pengeluaran**, **Keduanya**), **Asal** (**Umum**, **Unit RT**), dan **Status** (**Aktif**, **Nonaktif**). Kolom tabel: **NAMA KATEGORI**, **TIPE**, **ASAL**, **STATUS**, **AKSI**.

Arti kolom **ASAL**:

| Tampilan | Artinya | Bisa Anda ubah? |
|---|---|---|
| **Umum** | Kategori bawaan buatan Admin Aplikasi (misalnya **Iuran Bulanan Warga**, **Operasional & ATK**, **Setoran ke RW**) | Tidak, tampil **Dikelola Admin** |
| **Unit RT** | Kategori buatan Operator Keuangan RT | Ya |
| **Dari RW** | Kategori buatan RW yang sengaja diterapkan untuk RT | Tidak, tampil **Dikelola RW** |

Pada data demo belum ada kategori khusus RT. Kategori RW demo (**Iuran Fasilitas**, **Donasi Kematian**) tidak diterapkan untuk RT, jadi tidak tampil di akun ini.

> 👤 **SIMULASI: Menambah kategori pengeluaran RT**
>
> **Bu Siti** sering membeli konsumsi pertemuan warga dan ingin kategori sendiri supaya laporan lebih jelas.
>
> - **Masuk sebagai:** `operator_rt3`
> - **Nama Kategori:** Konsumsi Pertemuan
> - **Tipe:** Pengeluaran
> - **Aktif:** ya

**Langkah-langkah**

**1. Buka Kategori Transaksi** lalu tekan **Tambah Kategori**.

**2. Isi Nama Kategori** (maksimal 100 karakter), lalu pilih **Tipe**.

**3. Biarkan Aktif tercentang** lalu tekan **Simpan**.

Kategori otomatis berlaku untuk RT Anda. Tidak ada kotak **Berlaku di** pada akun RT.

> 📸 **SCREENSHOT OUT-23: Daftar Kategori Transaksi dan jendela Tambah Kategori**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/kategori-transaksi (jendela **Tambah Kategori**)
> - **Kondisi:** daftar kategori umum terlihat di belakang, jendela terisi *Konsumsi Pertemuan*
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kolom **ASAL**, **Tipe**, dan tombol **Simpan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-23-tambah-kategori-transaksi.png`

### ✅ Hasilnya

- Pesan **"Kategori transaksi berhasil ditambahkan."**
- Kategori langsung tersedia di formulir **Catat Transaksi** dan, untuk kategori pemasukan, di **Kategori Kas (pemasukan)** pada pengaturan iuran dan **Buat Iuran Lainnya**.

**Mengubah dan menghapus**

- Ikon pensil membuka **Edit Kategori**. Pesan sukses: **"Kategori transaksi berhasil diperbarui."**
- Ikon tempat sampah membuka **Hapus Kategori**. Bila kategori sudah dipakai transaksi atau iuran, kategori **tidak dihapus**, melainkan dinonaktifkan. Pesan: **"Kategori sudah dipakai transaksi, sehingga dinonaktifkan (bukan dihapus) agar rekap tetap utuh."** Bila belum dipakai: **"Kategori transaksi berhasil dihapus."**

### ⚠️ Perhatian

- **Kategori RW atau kategori umum tidak bisa diubah.** Baris bertuliskan **Dikelola Admin** atau **Dikelola RW** hanya bisa dilihat.
- **Kategori RT lain tidak terlihat.** Setiap RT hanya melihat kategori miliknya.
- **Kategori yang Anda butuhkan tidak ada di daftar.** Gunakan **Tambah Kategori**, atau minta Operator Keuangan RW menerapkan kategori RW untuk RT.

> 💡 **Tips:** Kategori yang baik singkat dan konsisten, sehingga rekap bulanan mudah dibaca Ketua RT dan warga.

> ➡️ **Cerita berlanjut:** Bab 9 membahas laporan keuangan.

---

## 9. Laporan Keuangan

Menu **Laporan Keuangan** merangkum arus kas RT per bulan. Alurnya:

**Draft** (Anda susun dan periksa) → **Pending Review** (diajukan ke Ketua RT, menunggu persetujuan) → **Approved** (disetujui Ketua RT) → **Published** (dipublikasikan ke warga). Bila ditolak, statusnya **Ditolak** dan Anda perbaiki lalu ajukan lagi.

**Siapa yang menyetujui?** Laporan RT disetujui oleh **Ketua RT** dari RT yang sama, lewat menu **Persetujuan Keuangan** di akun Ketua RT. Setelah disetujui, laporan **belum tampil ke warga**. Anda masih harus menekan **Publikasikan ke warga**.

Arti status pada kolom **STATUS**:

| Label di layar | Artinya | Yang bisa Anda lakukan |
|---|---|---|
| **Draft** | Baru disusun, belum diajukan | Ajukan, Unduh, Hapus |
| **Pending Review** | Menunggu Ketua RT | Unduh, Hapus |
| **Ditolak** | Dikembalikan dengan catatan | Ajukan ulang, Unduh, Hapus |
| **Approved** | Disetujui, belum tampil ke warga | Publikasikan, Unduh |
| **Published** | Tampil ke warga | Unduh |

### 9.1 Menyusun Laporan Bulanan (C-11 bagian 1)

> 👤 **SIMULASI C-11 (versi RT): Laporan keuangan RT 03 (bagian 1 dari 3)**
>
> Awal bulan, **Bu Siti** menyusun laporan keuangan RT 03 untuk bulan lalu. RT 03 belum punya laporan pada data demo, jadi ia bisa memilih bulan yang mana pun yang transaksinya sudah lengkap.
>
> - **Masuk sebagai:** `operator_rt3`, kata sandi `password`
> - **Periode:** bulan lalu
> - **Selanjutnya:** ia mengajukan laporan (9.2). Ketua RT 03 menyetujuinya. Lihat `02-Ketua-RT.md`, C-11

**Langkah-langkah**

**1. Buka menu Laporan Keuangan.**
Judul **Laporan Keuangan**. Penyaring: **Tahun** (**Semua Tahun**, 2026, 2025) dan **Status**, dengan tombol **Filter**. Bila belum ada laporan, tertulis *Belum ada laporan yang ditemukan.*

**2. Tekan Generate Laporan Baru.**
Jendela **Generate Laporan Baru** terbuka. Kotak biru menjelaskan bahwa laporan dibuat berstatus **Draft** agar Anda dapat meninjau angka sebelum mengajukannya ke Ketua.

**3. Pilih Bulan dan isi Tahun.**
Pilihan awal: bulan **Oktober** dan tahun **2026**. Ubah sesuai periode.

**4. Tekan Generate Laporan.**

> 📸 **SCREENSHOT OUT-24: Daftar Laporan Keuangan RT 01 (data demo)**
> - **Akun:** opkeuanganrt01rw21 (Op Keuangan RT 01)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** data demo awal, laporan RT 01 tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Generate Laporan Baru**, penyaring, dan kolom **STATUS**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-24-daftar-laporan.png`

> 📸 **SCREENSHOT OUT-25: Jendela Generate Laporan Baru**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/laporan (jendela **Generate Laporan Baru**)
> - **Kondisi:** bulan dan tahun sudah dipilih
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada kotak informasi biru, **Bulan**, **Tahun**, dan tombol **Generate Laporan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-25-generate-laporan.png`

### ✅ Hasilnya

- Muncul pesan **"Laporan berhasil dibuat dalam status Draft. Silakan review laporan terlebih dahulu, lalu klik tombol "Ajukan" untuk mengirimkannya ke Ketua."**
- Laporan tampil di tabel dengan **PERIODE**, **JUDUL LAPORAN**, **TOTAL PEMASUKAN**, **TOTAL PENGELUARAN**, **SALDO AKHIR**, dan status **Draft**. Angka dihitung dari transaksi kas RT Anda pada bulan itu (di luar yang ditolak), dengan saldo awal dari bulan-bulan sebelumnya.

### ⚠️ Perhatian

- **"Laporan untuk periode ini sudah ada (Status: ...)."** Sudah ada laporan untuk bulan itu. Pilih bulan lain.
- **Nama bulan tampil berbahasa Inggris.** Kolom **PERIODE** dan judul laporan memakai nama bulan seperti *October 2026*. Itu perilaku aplikasi saat ini, bukan kesalahan data.
- **Pilihan Tahun pada penyaring hanya 2025 dan 2026.** Gunakan **Semua Tahun** untuk laporan di luar itu.
- **Laporan berstatus Ditolak lalu Anda generate ulang.** Laporan lama yang ditolak otomatis dihapus dan diganti laporan baru berstatus **Draft** (angka dihitung ulang).
- **Dashboard menandai "Laporan ... belum dibuat".** Itu pengingat untuk bulan lalu. Hilang setelah laporannya dibuat.

> 💡 **Tips:** Sebelum mengajukan, buka **Catat Transaksi** untuk bulan itu dan pastikan semua transaksi serta pembayaran iuran sudah tercatat. Setelah diajukan, transaksi bulan itu terkunci.

> ➡️ **Cerita berlanjut:** Lanjut ke 9.2.

### 9.2 Mengajukan ke Ketua RT, Mempublikasikan, dan Mengunduh (C-11 bagian 2)

> 👤 **SIMULASI C-11 (versi RT): Laporan keuangan RT 03 (bagian 2 dan 3)**
>
> **Bu Siti** meninjau draf laporan, lalu mengajukannya ke Ketua RT 03, **Pak Budi Sampurno**. Setelah Pak Budi Sampurno menyetujui, Bu Siti mempublikasikan laporan agar warga bisa membacanya di menu **Laporan Keuangan** mereka, dan mengunduh PDF untuk arsip rapat RT.
>
> - **Masuk sebagai:** `operator_rt3`
> - **Laporan:** Laporan Keuangan RT 03 untuk periode yang baru dibuat
> - **Sebelumnya:** laporan berstatus **Draft** (9.1)
> - **Selanjutnya:** Ketua RT 03 menyetujui di menu **Persetujuan Keuangan**. Lihat `02-Ketua-RT.md`, C-11. Warga membaca laporan. Lihat `01-Warga.md`

**Langkah-langkah**

**1. Ajukan.**
Pada baris laporan **Draft** atau **Ditolak**, tekan tombol ikon pesawat kertas (tooltip **Ajukan ke Ketua RT**). Muncul **Ajukan Laporan**: *Apakah Anda yakin ingin mengajukan laporan ini ke Ketua untuk persetujuan?* Tekan **Ajukan**.

**2. Tunggu persetujuan.**
Status berubah menjadi **Pending Review**. Ketua RT 03 menerima notifikasi **Laporan Keuangan Menunggu Persetujuan** dan menyetujui atau menolak. Bila ditolak, catatan penolakan tampil di bawah judul laporan (*Catatan: ...*) dan Anda menerima notifikasi **Laporan Keuangan Ditolak**. Bila disetujui, Anda menerima notifikasi **Laporan Keuangan Disetujui**.

**3. Publikasikan.**
Setelah status **Approved**, tombol ikon unggah (tooltip **Publikasikan ke warga**) muncul. Tekan, lalu pada **Publikasikan Laporan** (*Laporan yang dipublish dapat dilihat oleh Warga.*) tekan **Publikasikan**. Status menjadi **Published**.

**4. Unduh.**
Tekan ikon PDF (**Unduh PDF**, terbuka di tab baru) atau ikon lembar kerja (**Unduh Excel**). Tombol ini tersedia untuk semua status.

**5. Hapus (bila perlu).**
Ikon tempat sampah (**Hapus laporan**) tersedia untuk laporan yang belum **Approved** dan belum dipublikasikan. Muncul **Hapus Laporan**: tekan **Hapus**.

> 📸 **SCREENSHOT OUT-26: Konfirmasi Ajukan Laporan**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** laporan berstatus **Draft**, kotak **Ajukan Laporan** tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada ikon ajukan di kolom **AKSI** dan tombol **Ajukan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-26-ajukan-laporan.png`

> 📸 **SCREENSHOT OUT-27: Laporan Approved dengan tombol Publikasikan**
> - **Akun:** operator_rt3 (Op Keuangan RT)
> - **URL:** /op-keuangan/laporan
> - **Kondisi:** laporan berstatus **Approved** (belum dipublikasikan); bila tidak ada, minta Ketua RT 03 menyetujui laporan yang diajukan di langkah sebelumnya
> - **Area:** baris laporan dan kolom **AKSI**
> - **Sorot:** kotak merah pada ikon **Publikasikan ke warga**, **Unduh PDF**, dan **Unduh Excel**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OUT-27-laporan-approved-publikasi.png`

### ✅ Hasilnya

- Setelah ajukan: pesan **"Laporan berhasil diajukan ke Ketua untuk peninjauan dan persetujuan."**, status **Pending Review**.
- Setelah Ketua RT menyetujui: status **Approved**. Laporan belum terlihat warga.
- Setelah publikasi: pesan **"Laporan berhasil di-publish dan kini dapat dilihat oleh publik."**, status **Published**.
- Setelah hapus: pesan **"Laporan berhasil dihapus."**
- Warga membuka laporan melalui menu **Laporan Keuangan** pada akun mereka.

### ⚠️ Perhatian

- **"Hanya laporan berstatus Draft atau Rejected yang dapat diajukan."** Laporan sudah diajukan atau disetujui.
- **"Hanya laporan yang sudah Approved yang bisa di-publish."** Tunggu persetujuan Ketua RT dulu.
- **"Laporan ini sudah di-publish."** Tidak perlu dipublikasikan lagi.
- **"Laporan yang sudah disetujui tidak dapat dihapus."** dan **"Laporan yang sudah dipublikasikan tidak dapat dihapus."** Laporan yang disetujui tidak dihapus.
- **Ketua RT tidak menemukan laporan di Persetujuan Keuangan.** Pastikan laporan sudah berstatus **Pending Review**, dan RT-nya sama dengan RT Anda.
- **Angka laporan berubah.** Laporan berstatus **Draft** dan **Pending Review** otomatis menghitung ulang angkanya setiap halaman dibuka. Setelah **Approved**, angka tidak berubah lagi.

> 💡 **Tips:** Unduh PDF setelah laporan **Approved**, lalu simpan untuk rapat RT atau pemeriksaan.

> ➡️ **Cerita berlanjut:** Ketua RT 03 menyetujui laporan. Lihat **`02-Ketua-RT.md`, cerita C-11**. Versi RW ada di `08-Op-Keuangan-RW.md`, C-11.

---

## 10. Cerita Lengkap (Lintas Peran)

| Kode | Cerita | Peran Anda | Rujukan |
|---|---|---|---|
| C-03 | Pak Budi Santoso membayar "Iuran Perbaikan Got" | Op Keuangan RT 03 memverifikasi pembayaran warga RT-nya, lalu menyetor ke RW. Op Keuangan RW membuat iuran dan memantau rekap per RT | 5.2 dan 7.3 di file ini; `01-Warga.md`, C-03 bagian 1; `08-Op-Keuangan-RW.md`, 5.1 dan 5.2 |
| C-04 | Pak Budi Santoso membayar Iuran Bulanan RT 03, lalu RT menyetor ke RW | Op Keuangan RT 03 memverifikasi bukti, mencatat setoran ke RW | 4.3 dan 7.2 di file ini; `01-Warga.md`, C-04 bagian 1; `08-Op-Keuangan-RW.md`, 6.2 (C-04) |
| C-11 (versi RT) | Laporan keuangan RT disusun, diajukan ke Ketua RT, disetujui, lalu tampil di Transparansi | Op Keuangan RT menyusun, mengajukan, dan mempublikasikan | 9.1 dan 9.2 di file ini; `02-Ketua-RT.md`, C-11; `01-Warga.md` |

**Ringkasan C-03**
1. Pak Hendra (Op Keuangan RW) membuat "Iuran Perbaikan Got", dikumpulkan lewat RT.
2. Pak Budi membayar ke rekening RT 03, dan Bu Siti memeriksa lalu menyetujuinya (5.2). Uang masuk kas RT 03 sebagai titipan.
3. Bu Siti menyetor Rp30.000 ke RW (7.3). Pak Hendra mengonfirmasi, dan kas RW bertambah.

**Ringkasan C-04**
1. Pak Budi mengajukan pembayaran Iuran Bulanan Rp60.000 dengan bukti transfer.
2. Bu Siti memeriksa bukti di tab **Verifikasi** dan menyetujuinya (4.3). Kas RT 03 bertambah.
3. Bu Siti menyerahkan Rp50.000 kepada Pak Hendra dan mencatat setoran (7.2).
4. Pak Hendra mengonfirmasi di akun RW. Kas RT berkurang, kas RW bertambah.

**Ringkasan C-11 (versi RT)**
1. Bu Siti menyusun laporan bulanan (9.1) dan mengajukannya ke Ketua RT (9.2).
2. Ketua RT 03 menyetujui di menu **Persetujuan Keuangan**.
3. Bu Siti mempublikasikan, warga membaca di menu **Laporan Keuangan**.

> ➡️ **Cerita berlanjut:** Kembali ke `README.md` untuk daftar cerita lain.

---

## 11. Masalah yang Sering Terjadi (FAQ)

**Menu bernama "Iuran Wajib", tetapi halamannya berjudul "Iuran Bulanan Warga".**
Keduanya halaman yang sama. Di akun warga, menunya bernama **Iuran Bulanan**.

**Tab Verifikasi kosong padahal warga bilang sudah bayar.**
Warga mungkin belum menekan **Kirim** pada jendela **Bayar Iuran**, atau KK-nya milik RT lain. Anda hanya melihat KK di RT Anda. Bila warga membayar tunai kepada Anda, catat sendiri (4.4).

**Saya tidak bisa menyetujui pembayaran "Iuran Perbaikan Got" milik warga RT lain.**
Memang begitu. Setiap Bendahara RT hanya memverifikasi KK di RT-nya. RW tidak memverifikasi pembayaran per warga untuk iuran jenis ini.

**Tombol "Setor" tidak muncul untuk bulan berjalan.**
Belum ada KK lunas bulan itu, atau kewajibannya sudah disetor penuh atau sedang menunggu konfirmasi. Setujui dulu pengajuan warga di tab **Verifikasi**.

**Muncul "RW belum menetapkan nominal setoran per KK ...".**
Nominal setoran diatur Operator Keuangan RW. Hubungi Bendahara RW.

**Setoran sudah saya catat, tetapi kas RT belum berkurang.**
Kas RT dan kas RW baru berubah setelah RW mengonfirmasi setoran. Selama itu statusnya **Menunggu konfirmasi**.

**Transaksi tidak muncul di buku kas.**
Halaman menampilkan bulan berjalan. Atur **Tahun** dan **Bulan**, lalu tekan **Filter**.

**Transaksi tidak bisa diubah, ada tulisan "Terkunci".**
Laporan keuangan periode itu sudah diajukan, disetujui, atau dipublikasikan. Bila laporan ditolak Ketua RT, periodenya terbuka kembali.

**Transaksi dari iuran atau setoran tidak bisa diedit.**
Transaksi itu hanya bisa dibatalkan lewat menu asalnya (**Iuran Wajib**, **Iuran Lainnya**, atau minta RW membatalkan setoran).

**Kode transaksi saya berawalan TRX-RT01 padahal RT 03.**
Awalan kode memang sama untuk semua RT. Kas yang dicatat tetap milik RT Anda.

**Laporan sudah disetujui Ketua RT, tetapi warga belum melihatnya.**
Anda perlu menekan **Publikasikan ke warga** pada laporan berstatus **Approved** (9.2).

**Tooltip tombol ajukan bertuliskan "Ajukan ke Ketua RT".**
Itu benar untuk akun RT. Penyetujunya adalah Ketua RT dari RT yang sama.

**Dashboard menunjukkan "Pengeluaran menunggu persetujuan".**
Di aplikasi saat ini transaksi yang Anda catat langsung berstatus sah, jadi pesan itu tidak muncul pada pemakaian normal.

**Menu Bantuan di bagian bawah tidak membuka apa pun.**
Menu itu belum memiliki halaman.

---

## 12. Lampiran: Daftar Gambar

Semua gambar desktop 1366×768. Nama file mengikuti kolom terakhir.

| Kode | Judul | Bab | Nama file |
|---|---|---|---|
| OUT-01 | Menu samping Operator Keuangan RT | 1.3 | `OUT-01-menu-samping.png` |
| OUT-02 | Dashboard Keuangan RT 03 | 2.2 | `OUT-02-dashboard-keuangan-rt.png` |
| OUT-03 | Pengaturan Rekening RT 03 | 3 | `OUT-03-pengaturan-rekening.png` |
| OUT-04 | Jendela Pengaturan Iuran | 4.1 | `OUT-04-pengaturan-iuran.png` |
| OUT-05 | Rekap Iuran Bulanan Warga | 4.2 | `OUT-05-rekap-iuran-bulanan.png` |
| OUT-06 | Tab Verifikasi dengan pengajuan warga | 4.3 | `OUT-06-tab-verifikasi-iuran-bulanan.png` |
| OUT-07 | Jendela Detail Pembayaran Iuran | 4.3 | `OUT-07-detail-pembayaran-iuran.png` |
| OUT-08 | Jendela Catat Pembayaran Iuran | 4.4 | `OUT-08-catat-pembayaran-iuran.png` |
| OUT-09 | Dialog Batalkan Pembayaran | 4.5 | `OUT-09-batalkan-pembayaran.png` |
| OUT-10 | Daftar Iuran Lainnya RT 03 | 5 | `OUT-10-daftar-iuran-lainnya.png` |
| OUT-11 | Jendela Buat Iuran Lainnya | 5.1 | `OUT-11-buat-iuran-lainnya.png` |
| OUT-12 | Detail iuran dari RW dan tab Pembayaran Warga | 5.2 | `OUT-12-detail-iuran-via-rt.png` |
| OUT-13 | Jendela Detail Pembayaran Iuran Perbaikan Got | 5.2 | `OUT-13-detail-pembayaran-got.png` |
| OUT-14 | Jendela Catat Pembayaran Iuran Lainnya | 5.3 | `OUT-14-catat-pembayaran-iuran-lainnya.png` |
| OUT-15 | Buku kas RT 03 | 6.1 | `OUT-15-buku-kas-rt.png` |
| OUT-16 | Jendela Catat Transaksi Baru | 6.2 | `OUT-16-catat-transaksi-baru.png` |
| OUT-17 | Transaksi baru di buku kas | 6.2 | `OUT-17-transaksi-baru.png` |
| OUT-18 | Menu aksi transaksi | 6.3 | `OUT-18-menu-aksi-transaksi.png` |
| OUT-19 | Setoran ke RW, kewajiban per bulan | 7.1 | `OUT-19-setoran-kewajiban.png` |
| OUT-20 | Jendela Setor Iuran Bulanan ke RW | 7.2 | `OUT-20-setor-iuran-bulanan.png` |
| OUT-21 | Riwayat setoran berstatus Menunggu konfirmasi | 7.2 | `OUT-21-riwayat-setoran-menunggu.png` |
| OUT-22 | Jendela Setor Iuran Lainnya ke RW | 7.3 | `OUT-22-setor-iuran-lainnya.png` |
| OUT-23 | Daftar Kategori Transaksi dan jendela Tambah Kategori | 8 | `OUT-23-tambah-kategori-transaksi.png` |
| OUT-24 | Daftar Laporan Keuangan RT 01 (data demo) | 9.1 | `OUT-24-daftar-laporan.png` |
| OUT-25 | Jendela Generate Laporan Baru | 9.1 | `OUT-25-generate-laporan.png` |
| OUT-26 | Konfirmasi Ajukan Laporan | 9.2 | `OUT-26-ajukan-laporan.png` |
| OUT-27 | Laporan Approved dengan tombol Publikasikan | 9.2 | `OUT-27-laporan-approved-publikasi.png` |

Jumlah: 27 gambar.
