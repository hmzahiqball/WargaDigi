# Manual Book WargaDigi 21: Ketua RW

Buku panduan untuk **Ketua RW** (pimpinan RW) pengguna aplikasi WargaDigi 21 di https://wargadigi21.com.
Tokoh simulasi: Pak **Dedi Mulyadi**, akun `pimpinan`, kata sandi demo `password`.

<!-- AI-INSTRUKSI: Ubah file ini menjadi dokumen Word bergambar mengikuti konvensi blok di README.md.
Blok "SCREENSHOT" diganti gambar bernama sama (awalan KRW, desktop 1366×768) dengan keterangan "Gambar <KODE>: <judul>".
Komentar HTML jangan dicetak. Semua label di dokumen ini diambil dari kode aplikasi; jangan diubah. -->

## Daftar Isi

1. Kenalan Dulu
2. Mulai Menggunakan
3. Dashboard RW
4. Memantau Penduduk
5. Manajemen RT
6. Mengesahkan Surat (Persetujuan Dokumen)
7. Memantau Persetujuan UMKM dan Pojok UMKM
8. Memantau Konten (Pusat Informasi)
9. Pengajuan Data
10. Rekap Iuran RT
11. Laporan Keuangan dan Statistik
12. Pengaturan Tanda Tangan dan Stempel
13. Cerita Lengkap (Lintas Peran)
14. Masalah yang Sering Terjadi (FAQ)
15. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### Peran Ketua RW

Ketua RW adalah pimpinan. Dalam aplikasi, perannya lebih banyak **memantau**: melihat ringkasan, data penduduk, usaha warga, konten yang sudah terbit, setoran RT, dan laporan keuangan. Di beberapa halaman Anda juga dapat bertindak. Yang bisa dan tidak bisa dilakukan Ketua RW dijelaskan jelas di bawah ini.

| Bagian | Ketua RW dapat | Catatan |
|---|---|---|
| Dashboard RW | Melihat | Mirip Admin RW |
| Manajemen Penduduk | Melihat dan mengunduh | Hanya baca |
| Manajemen RT | Melihat; tombol tambah, ubah, hapus ikut tampil | Lihat bab 5 |
| Persetujuan Dokumen | Melihat dan **mengesahkan atau menolak surat** | Lihat bab 6 |
| Persetujuan UMKM | **Hanya memantau** riwayat | Persetujuan oleh Admin RW |
| Pojok UMKM | **Hanya melihat** | Tombol Nonaktifkan dan Aktifkan tidak ada |
| Persetujuan Konten | **Tidak ada** | Menu tidak tampil; hanya Admin RW |
| Pusat Informasi | Melihat konten terbit | Hanya baca |
| Pengajuan Data | Melihat dan **menyetujui atau menolak** | Lihat bab 9 |
| Rekap Iuran RT | Melihat | Hanya baca |
| Laporan Keuangan | Melihat, mengunduh PDF, melihat **Statistik** | Persetujuan Keuangan disembunyikan |
| Tanda Tangan | Mengatur tanda tangan dan stempel default | Hanya Ketua RT dan Ketua RW |

### Tokoh simulasi

> 👤 **SIMULASI: Mengenal Pak Dedi**
>
> Pak **Dedi Mulyadi** adalah Ketua RW 21 Tanimulya. Ia memakai akun `pimpinan`. Setiap pagi ia membuka dashboard untuk melihat kondisi RW, dan sesekali mengesahkan surat bila Admin RW berhalangan. Akun ini tidak tertaut ke data penduduk, jadi yang tampil hanyalah nama pengguna.
>
> Pengurus lain di cerita: **Bu Rina** *(fiktif)* Admin RW (`adminrw21`), Pak **Hendra Gunawan** Bendahara RW (`opkeuanganrw21`), Pak **Budi Sampurno** Ketua RT 03 (`budisampurno`), dan Pak **Budi Santoso** warga RT 03 (`budisantoso`).

### Peta menu Ketua RW

Menu ada di sisi kiri layar. Di HP, tekan tombol **☰** di pojok kiri atas.

| Menu | Bab |
|---|---|
| **Dashboard** | 3 |
| **Manajemen Penduduk** | 4 |
| **Manajemen RT** | 5 |
| **Persetujuan**, berisi **Persetujuan Dokumen**, **Persetujuan UMKM**, **Pengajuan Data** | 6, 7, 9 |
| **Laporan Keuangan** | 11 |
| **Rekap Iuran RT** | 10 |
| **Pusat Informasi** | 8 |
| **Pojok UMKM** | 7 |
| **Tanda Tangan** (bagian bawah) | 12 |
| **Keluar** (bagian bawah) | 2 |

> 📸 **SCREENSHOT KRW-01: Peta menu Ketua RW**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/dashboard
> - **Kondisi:** menu **Persetujuan** terbuka sehingga tiga submenu terlihat
> - **Area:** sisi kiri layar (sidebar) dari atas sampai bawah
> - **Sorot:** kotak merah pada **Persetujuan** dan **Tanda Tangan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-01-peta-menu-ketua-rw.png`

⚠️ **Perhatian**

- Menu **Persetujuan Konten** tidak ada untuk Ketua RW. Hanya Admin RW yang menyetujui berita, agenda, dan pengumuman.
- Menu **Pusat Bantuan** terlihat di bagian bawah tetapi belum memiliki halaman. Abaikan dulu.

---

## 2. Mulai Menggunakan

Cara masuk, memilih peran, dan keluar dibahas lengkap di `00-Umum-Publik-dan-Login.md`. Ringkasnya:

1. Buka https://wargadigi21.com, masuk dengan username `pimpinan` dan kata sandi `password`.
2. Anda langsung tiba di **Dashboard RW**.
3. Untuk keluar, klik **Keluar** di bagian bawah menu.

Ikon lonceng di atas layar menampilkan notifikasi. Yang sering muncul pada Ketua RW adalah **Surat Menunggu Penerbitan** (surat sudah disetujui RT), **UMKM Baru Menunggu Verifikasi**, dan **Pengajuan Perubahan Data**. Klik notifikasi untuk membuka halamannya. Tombol ikon mata silang hanya menyembunyikan notifikasi itu; **Pulihkan Notifikasi** mengembalikannya.

---

## 3. Dashboard RW

Judul halaman **Dashboard RW**, heading **Ringkasan**, label **STATUS: AKTIF**.

> 👤 **SIMULASI: Pagi Pak Dedi**
>
> Pak Dedi ingin tahu kondisi RW sebelum rapat pengurus: berapa warga, apa yang menunggu pengesahan, dan bagaimana kas RW.
>
> - **Masuk sebagai:** `pimpinan`

### Langkah-langkah

**1. Baca banner dan kartu statistik.**
Banner **Pemberitahuan UMKM Baru Menunggu Verifikasi** dan **Terdapat Dokumen Surat yang Perlu Disahkan** tampil hanya bila ada pekerjaan. Empat kartu bisa diklik:

| Kartu | Isinya | Klik membuka |
|---|---|---|
| **JUMLAH PENDUDUK** | Total penduduk dan tambahan bulan ini | Manajemen Penduduk |
| **DOKUMEN PENDING** | Surat Disetujui RT ditambah Diajukan | Persetujuan Dokumen |
| **UMKM BARU** | **Menunggu Verifikasi** atau **Semua Terverifikasi** | Persetujuan UMKM |
| **KONTEN DITINJAU** | Lihat catatan di bawah | Pusat Informasi |

> 📸 **SCREENSHOT KRW-02: Dashboard Ketua RW bagian atas**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/dashboard
> - **Kondisi:** data demo awal
> - **Area:** dari judul **Ringkasan** sampai empat kartu
> - **Sorot:** kotak merah pada kartu **KONTEN DITINJAU** dan **UMKM BARU**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-02-dashboard-atas.png`

**2. Gulir ke bawah.**

- **Kas RW** (hanya baca): **Saldo kas**, **Pemasukan** dan **Pengeluaran** bulan berjalan, laporan RW terakhir, tautan **Lihat Laporan Keuangan** dan **Rekap iuran per RT**.
- **AKTIVITAS MENUNGGU TINDAKAN**: lencana **PERLU VERIFIKASI** (UMKM), **PERLU PENGESAHAN** (surat), **PERLU PERSETUJUAN** (pengajuan data).
- **Aksi Cepat**: **Tinjau Dokumen**, **Verifikasi UMKM**, **Pusat Informasi**, **Manajemen Penduduk**. (Admin RW melihat **Periksa Konten** pada posisi **Pusat Informasi**.)
- **Konten Terbaru**: hanya yang berlabel **TERBIT**. Tautan **Lihat Semua** membuka Pusat Informasi.
- **Demografi Penduduk**: **Kelompok Usia**, **Pendidikan Terakhir**, **Pekerjaan**, **Jenis Kelamin**, **Status Perkawinan**, **Status Warga**, **Sebaran Penduduk per RT**.

> 📸 **SCREENSHOT KRW-03: Dashboard Ketua RW bagian bawah**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/dashboard (gulir ke bawah)
> - **Kondisi:** data demo awal
> - **Area:** panel **Kas RW**, **Aksi Cepat**, **Konten Terbaru**, **Demografi Penduduk**
> - **Sorot:** kotak merah pada panel **Kas RW** dan tombol **Pusat Informasi** di **Aksi Cepat**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-03-dashboard-bawah.png`

### ✅ Hasilnya

Anda mendapat gambaran cepat kondisi RW tanpa membuka banyak halaman.

### ⚠️ Perhatian

- **Kartu KONTEN DITINJAU selalu menunjukkan 0 pada Ketua RW.** Aplikasi hanya menghitung antrean konten untuk Admin RW, karena penyetujunya Admin RW. Kartu ini membuka Pusat Informasi (konten yang sudah terbit). Untuk tahu antrean persetujuan konten, tanyakan Admin RW.
- **Kartu DOKUMEN PENDING bukan hanya surat yang menunggu Anda.** Angkanya juga memuat surat yang masih di tangan Ketua RT.

> 💡 **Tips:** Panel **Kas RW** memudahkan Anda mengecek saldo sebelum rapat. Rinciannya ada di Laporan Keuangan (bab 11).

---

## 4. Memantau Penduduk

Menu **Manajemen Penduduk** menampilkan data keluarga dan penduduk **seluruh RW**. Halaman ini hanya untuk melihat dan mengunduh. Tidak ada tombol tambah, ubah, atau hapus.

> 👤 **SIMULASI: Data warga untuk rapat**
>
> Pak Dedi ingin melihat berapa keluarga di RT 03 dan memastikan Pak **Budi Santoso** tercatat sebagai warga.
>
> - **Masuk sebagai:** `pimpinan`

### Langkah-langkah

**1.** Klik **Manajemen Penduduk**. Keterangan halaman: "Data penduduk di seluruh RW." dengan lencana **Total: N jiwa**.
**2.** Tab **Profil Keluarga**: ketik di **Cari Keluarga (No. KK / Nama Kepala / Alamat)**. Pilih **RT** (misalnya **RT 03**, default **Semua RT**), lalu **Filter**. Tombol X (**Reset Filter**) mengembalikan semua.
**3.** Kolom keluarga: **NO. KK**, **KEPALA KELUARGA**, **RT**, **ALAMAT**, **STATUS WARGA** (**Warga Tetap** atau **Bukan Warga Tetap**), **PEMILIK RUMAH**, **ANGGOTA**, **AKSI**.
**4.** Tab **Daftar Semua Penduduk**: cari lewat **Cari Penduduk (Nama / NIK)**. Kolom: **NAMA**, **NIK**, **L/P**, **HUBUNGAN KELUARGA**, **NO. KK**, **RT**, **AKSI**.
**5.** Klik **Detail** untuk membuka **Detail Keluarga** (**Profil Kartu Keluarga**, **Akun Keluarga**, **Anggota Keluarga**) atau **Detail Penduduk** (identitas lengkap dan **Kartu Tanda Penduduk**).
**6.** Untuk mengunduh, klik **Export**, pilih kolom pada modal **Export Data Penduduk**, lalu **Export Excel**. Pilihan **Pilih Semua** dan **Kosongkan** tersedia. Unduhan mengikuti filter **RT** dan pencarian penduduk yang aktif.

> 📸 **SCREENSHOT KRW-04: Manajemen Penduduk (Ketua RW)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/penduduk
> - **Kondisi:** tab **Profil Keluarga**, filter **RT 03** aktif
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pilihan **RT**, tombol **Export**, dan tombol **Detail**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-04-manajemen-penduduk.png`

> 📸 **SCREENSHOT KRW-05: Detail Keluarga**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/keluarga/{id}
> - **Kondisi:** keluarga Budi Santoso terbuka
> - **Area:** kartu **Profil Kartu Keluarga** sampai **Anggota Keluarga**
> - **Sorot:** kotak merah pada teks "Link aktivasi dibuat ulang oleh Ketua RT terkait." (bila akun belum aktif) atau pada **Status Akun**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-05-detail-keluarga.png`

### ✅ Hasilnya

Data tampil 15 baris per halaman. Unduhan Excel bernama `data-penduduk-TTTTBBHH-JJMMDD.xlsx`.

### ⚠️ Perhatian

- **"Pilih minimal satu kolom untuk diekspor."** Centang minimal satu kolom.
- **"Belum ada file KTP yang diunggah."** Warga belum mengunggah KTP.
- **Unduhan berisi NIK dan data pribadi.** Simpan dan bagikan seperlunya.

> 💡 **Tips:** Filter di tab keluarga tidak ikut ke unduhan. Atur filter di tab **Daftar Semua Penduduk** sebelum mengekspor.

---

## 5. Manajemen RT

Menu **Manajemen RT** (judul **Manajemen Data RT**) menampilkan daftar RT: **Kode RT**, **Nama RT**, **Jumlah Keluarga** (tampil "N KK"), dan **Aksi**. Pencarian lewat "Cari kode atau nama RT...". Pada data demo ada RT 01 sampai RT 06.

> 👤 **SIMULASI: Mengecek daftar RT**
>
> Pak Dedi ingin memastikan daftar RT di aplikasi sama dengan susunan RT di lapangan.
>
> - **Masuk sebagai:** `pimpinan`

### Langkah-langkah

**1.** Klik **Manajemen RT**.
**2.** Baca tabel. Gunakan pencarian bila perlu.

> 📸 **SCREENSHOT KRW-06: Manajemen Data RT (Ketua RW)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/rt
> - **Kondisi:** data demo (RT 01 sampai RT 06)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kolom **Jumlah Keluarga**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-06-manajemen-rt.png`

### ✅ Hasilnya

Anda melihat semua RT dan jumlah KK masing-masing.

### ⚠️ Perhatian

- **Tombol Tambah RT dan ikon ubah/hapus tetap tampil untuk Ketua RW.** Aplikasi tidak membatasinya, tetapi pembagian kerja yang dimaksud adalah Admin RW yang mengelola data RT (lihat `05-Admin-RW.md`, bab Manajemen RT). Bila Anda perlu mengubah RT, koordinasikan dengan Admin RW. Pesan yang muncul sama seperti Admin RW, misalnya "Kode RT tersebut sudah terdaftar." dan "RT {kode_rt} tidak dapat dihapus karena masih memiliki {n} data keluarga."

> 💡 **Tips:** Kalau hanya ingin memantau, cukup lihat tabel dan jangan tekan tombol aksi.

---

## 6. Mengesahkan Surat (Persetujuan Dokumen)

Menu **Persetujuan** lalu **Persetujuan Dokumen** (judul **Portal Persetujuan Dokumen**). Berbeda dengan Persetujuan UMKM, halaman ini **tidak** membedakan Admin RW dan Ketua RW: tombol **Tanda Tangan & Sahkan** dan **Tolak** tampil untuk Anda, dan bila surat Anda sahkan, hasilnya sama seperti disahkan Admin RW. Notifikasi **Surat Menunggu Penerbitan** juga dikirim ke Admin RW dan Ketua RW sekaligus, jadi siapa pun yang lebih dulu memproses, surat selesai.

### 6.1 Mengesahkan Surat

> 👤 **SIMULASI C-01: Surat Keterangan Domisili (bagian 3 dari 3)**
>
> Pak **Budi Santoso** mengajukan Surat Keterangan Domisili untuk membuka rekening bank. Pak **Budi Sampurno** (Ketua RT 03) sudah menyetujuinya. Admin RW sedang tidak di tempat, jadi Pak Dedi yang membuka notifikasi **Surat Menunggu Penerbitan** dan mengesahkannya.
>
> - **Masuk sebagai:** `pimpinan`
> - **Surat:** Surat Keterangan Domisili, pemohon Budi Santoso, RT 03
> - **Nomor urut:** 012 *(contoh)*
> - **Sebelumnya:** lihat *01-Warga.md, C-01 bagian 1* dan *02-Ketua-RT.md, C-01 bagian 2*
> - **Selanjutnya:** Pak Budi Santoso mengunduh PDF.

#### Langkah-langkah

**1.** Klik **Persetujuan Dokumen**. Kartu surat berlencana **Menunggu Pengesahan RW** ada di atas.

> 📸 **SCREENSHOT KRW-07: Portal Persetujuan Dokumen (Ketua RW)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/persetujuan-dokumen
> - **Kondisi:** surat Budi Santoso berstatus **Menunggu Pengesahan RW**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Detail Permintaan**, **Tanda Tangan & Sahkan**, dan **Tolak**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-07-portal-persetujuan-dokumen.png`

**2.** Klik **Detail Permintaan** untuk memeriksa **Data Lengkap Pemohon**, lampiran (**Lihat Kartu Keluarga**, **Lihat KTP**), dan **Preview Surat**.

**3.** Klik **Tanda Tangan & Sahkan**. Modal berbunyi "Bubuhkan tanda tangan sebagai Ketua RW untuk mensahkan surat ...".

**4.** Isi **Nomor Urut Surat** (bagian urut saja, contoh *012*).

**5.** Pilih tanda tangan. Karena Anda sudah menyimpan tanda tangan di menu **Tanda Tangan** (bab 12), pilihan **TTD Tersimpan** sudah terpilih. Pilih **Gambar Manual** bila ingin mencoret baru (tombol **Ulangi** menghapus coretan).

**6.** Pada **Stempel RW (Opsional)**, centang **Gunakan stempel tersimpan** bila tersedia, atau unggah lewat **Unggah Stempel (PNG, Max 2MB)**.

**7.** Periksa **Pratinjau Surat**, lalu klik **Sahkan & Selesai**.

> 📸 **SCREENSHOT KRW-08: Modal Tanda Tangan & Sahkan memakai TTD tersimpan**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/persetujuan-dokumen (modal **Tanda Tangan & Sahkan**)
> - **Kondisi:** **TTD Tersimpan** terpilih, **Nomor Urut Surat** berisi "012", pratinjau tampil
> - **Area:** modal penuh
> - **Sorot:** kotak merah pada pilihan **TTD Tersimpan**, **Gunakan stempel tersimpan**, dan tombol **Sahkan & Selesai**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-08-modal-sahkan-ttd-tersimpan.png`

#### ✅ Hasilnya

- Pesan **"Dokumen berhasil disahkan dan dikirim ke pemohon."**
- Status surat menjadi **Selesai**. Pak Budi Santoso menerima notifikasi **Surat Terbit** dan dapat mengunduh PDF (lihat *01-Warga.md, C-01 bagian 3*).

### 6.2 Menolak Surat

Klik **Tolak** (atau **Tolak & Beri Catatan** di modal detail). Pada modal **Tolak Pengajuan Surat** isi **Alasan Penolakan / Catatan Koreksi** (wajib, maksimal 1000 karakter), lalu **Tolak & Kirim Catatan**. Pesan: **"Catatan penolakan telah dikirim."** Status menjadi **Ditolak RW** dan pemohon menerima **Surat Ditolak RW** beserta alasannya.

### 6.3 Riwayat Pengajuan

Klik tombol **Riwayat Pengajuan** di kanan atas portal (riwayat tidak ada di menu samping). Halaman **Riwayat Pengajuan** menampilkan semua pengajuan. Cari lewat "Cari nama, NIK, atau jenis surat...", saring status (**Diajukan**, **Disetujui RT**, **Selesai**, **Ditolak RT**, **Ditolak RW**), lalu **Filter**. Kolom: **Tanggal**, **Pemohon**, **Jenis Surat**, **RT**, **Status**. Pada data demo tampil 9 contoh surat Pak Budi Santoso berstatus **Selesai**.

> 📸 **SCREENSHOT KRW-09: Riwayat Pengajuan Dokumen**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/riwayat-dokumen
> - **Kondisi:** data demo, sembilan surat berstatus **Selesai**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada filter status dan kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-09-riwayat-pengajuan.png`

### ⚠️ Perhatian

- **Halaman kosong: "Belum Ada Pengajuan" / "Belum ada dokumen yang perlu disahkan saat ini."** Tidak ada surat yang menunggu. Data demo awal memang tidak punya surat menunggu RW.
- **Kartu bertombol "Menunggu Verifikasi RT".** Surat belum disetujui RT, jadi belum bisa disahkan.
- **"Format harus PNG!" atau "Max 2MB!"** saat unggah stempel. Pakai PNG paling besar 2 MB.
- **Tanda tangan kosong tidak ditolak.** Pastikan tanda tangan sudah dipilih atau dicoret sebelum **Sahkan & Selesai**.
- **Mengapa Admin RW bisa menyelesaikan lebih dulu?** Sekali status **Selesai**, surat hilang dari antrean Anda. Cek **Riwayat Pengajuan** untuk memastikan.

> 💡 **Tips:** Simpan tanda tangan dan stempel default di menu **Tanda Tangan** supaya tidak mencoret ulang tiap surat.

> ➡️ **Cerita berlanjut:** Pak Budi Santoso mengunduh surat (lihat *01-Warga.md, C-01 bagian 3*).

---

## 7. Memantau Persetujuan UMKM dan Pojok UMKM

Untuk UMKM, Ketua RW **hanya memantau**. Menyetujui, menolak, menonaktifkan, dan mengaktifkan usaha dilakukan Admin RW. Bila Anda mencoba lewat alamat langsung, tampil 403 "Verifikasi UMKM hanya dapat dilakukan oleh Admin RW."

### 7.1 Memantau Riwayat Persetujuan UMKM

> 👤 **SIMULASI C-05 dan C-06 (sisi pemantau): Usaha warga**
>
> Pak Dedi ingin tahu usaha apa saja yang diajukan warga dan bagaimana nasibnya: **Jasa Service Elektronik Pak Budi** (Pending), **Koperasi Warga Tanimulya** (Rejected, dengan catatan), serta tiga usaha yang sudah disetujui.
>
> - **Masuk sebagai:** `pimpinan`
> - **Sebelumnya:** warga mendaftar (lihat *01-Warga.md, C-05 dan C-06*), Admin RW memproses (lihat *05-Admin-RW.md, C-05 dan C-06*)

#### Langkah-langkah

**1.** Klik **Persetujuan** lalu **Persetujuan UMKM**. Subjudul: "Pantau riwayat pengajuan UMKM warga RW." Banner **Mode Monitoring Ketua RW** menjelaskan bahwa seluruh riwayat (Pending, Approved, dan Rejected) ditampilkan.

**2.** Tab pertama berlabel **Riwayat Persetujuan UMKM** (bukan "Persetujuan UMKM" seperti Admin RW), memuat semua pengajuan. Lencana pada kartu: **Pending Review**, **Disetujui**, atau **Ditolak**.

**3.** Klik sebuah kartu (judul "Klik untuk melihat detail pengaju") untuk membuka **Detail Pengajuan UMKM**: **NIK**, **Nama Lengkap**, **Nama Usaha**, **Kategori Usaha**, **Deskripsi Usaha**, **Alamat & Lokasi**, **No. WhatsApp**, **Status Approval**, dan **Alasan Penolakan** bila ditolak. Tombol aksi tidak ada.

**4.** Tab **Pengajuan Ditolak** menampilkan arsip yang ditolak, misalnya Koperasi Warga Tanimulya dengan alasan "Lengkapi deskripsi pengurus koperasi dan foto tempat usaha, lalu ajukan ulang." Klik **Lihat Rincian Penolakan** untuk membaca lengkap.

> 📸 **SCREENSHOT KRW-10: Riwayat Persetujuan UMKM (mode monitoring)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/persetujuan-umkm
> - **Kondisi:** data demo; banner **Mode Monitoring Ketua RW** dan kartu berlencana **Pending Review**, **Disetujui**, **Ditolak**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada banner monitoring dan tab **Riwayat Persetujuan UMKM**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-10-riwayat-persetujuan-umkm.png`

> 📸 **SCREENSHOT KRW-11: Detail Pengajuan UMKM tanpa tombol aksi**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/persetujuan-umkm (modal **Detail Pengajuan UMKM**)
> - **Kondisi:** modal terbuka pada "Jasa Service Elektronik Pak Budi"
> - **Area:** modal penuh
> - **Sorot:** kotak merah pada **Informasi Pemohon** dan area bawah modal yang tidak punya tombol **Tolak** atau **Setujui Profil**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-11-detail-umkm-tanpa-aksi.png`

### 7.2 Melihat Pojok UMKM

#### Langkah-langkah

**1.** Klik **Pojok UMKM**. Subjudul: "Daftar usaha warga yang sudah disetujui RW." Hanya usaha **Approved** tampil, 6 per halaman.
**2.** Cari lewat "Cari nama usaha, pemilik, atau kategori UMKM...", saring dengan **Semua Kategori** dan **Semua Status** (**Aktif**, **Tidak Aktif**, **Stok Tersedia**, **Stok Menipis**, **Stok Habis**).
**3.** Klik **Detail & Produk** untuk melihat **Detail UMKM** dengan **Katalog Produk & Etalase Usaha**. Tombol **Persetujuan UMKM** di kanan atas membuka halaman riwayat.

> 📸 **SCREENSHOT KRW-12: Pojok UMKM (Ketua RW)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/umkm
> - **Kondisi:** data demo; tiga usaha tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kartu usaha, tombol **Detail & Produk**, dan fakta bahwa tombol **Nonaktifkan** tidak ada
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-12-pojok-umkm.png`

### ✅ Hasilnya

Anda bisa memantau usaha dan nasib setiap pengajuan tanpa risiko mengubah data.

### ⚠️ Perhatian

- **Tidak ada tombol Setujui, Tolak, Nonaktifkan, atau Aktifkan.** Itu disengaja; ajukan permintaan ke Admin RW.
- **Halaman detail usaha menulis "Terverifikasi RW 12".** Itu teks tetap dari aplikasi dan tidak mengikuti data RW 21. Abaikan.

> 💡 **Tips:** Hubungi Admin RW bila usaha tertentu perlu ditinjau ulang.

---

## 8. Memantau Konten (Pusat Informasi)

Ketua RW tidak menyetujui konten dan tidak punya menu **Persetujuan Konten**. Bila Anda membuka alamat itu langsung, tampil 403 "Persetujuan konten hanya dapat dilakukan oleh Admin RW." Yang tersedia adalah **Pusat Informasi**: seluruh konten (RW dan semua RT) yang sudah **terbit**.

> 👤 **SIMULASI C-07 (sisi pemantau): Berita CCTV terbit**
>
> Admin RW menyetujui berita **"Rencana Pemasangan CCTV Lingkungan"** (lihat *05-Admin-RW.md, C-07*). Pak Dedi ingin memastikan berita itu sudah tayang untuk warga.
>
> - **Masuk sebagai:** `pimpinan`

### Langkah-langkah

**1.** Klik **Pusat Informasi** (atau **Lihat Semua** pada panel **Konten Terbaru**).
**2.** Pilih tab **Lihat Semua**, **Berita Komunitas**, **Agenda Kegiatan**, atau **Pengumuman**, atau cari lewat **Cari...**.
**3.** Klik kartu untuk membaca detail. Lencana asal menunjukkan **RW**, **RT 01**, atau **RT 03**.

> 📸 **SCREENSHOT KRW-13: Pusat Informasi (Ketua RW)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/pusat-informasi
> - **Kondisi:** berita "Rencana Pemasangan CCTV Lingkungan" sudah terbit dan tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada lencana **Terbit** dan lencana asal
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-13-pusat-informasi.png`

### ✅ Hasilnya

Anda melihat apa yang dilihat warga.

### ⚠️ Perhatian

- **Berita yang belum disetujui tidak tampil.** Pusat Informasi hanya memuat yang **Terbit**. Antrean persetujuan hanya terlihat oleh Admin RW.

> 💡 **Tips:** Agenda menampilkan **Konfirmasi Kehadiran** dan jumlah **Warga Hadir**, berguna untuk memperkirakan jumlah peserta.

---

## 9. Pengajuan Data

Menu **Persetujuan** lalu **Pengajuan Data** (heading **Verifikasi Pengajuan Data**). Daftarnya sama dengan Admin RW: pengajuan perubahan data anggota keluarga yang sudah disetujui Ketua RT dan menunggu RW. Dalam aplikasi, Ketua RW dan Admin RW sama-sama dapat menyetujui atau menolak, dan notifikasi **Pengajuan Perubahan Data** dikirim ke keduanya.

> 👤 **SIMULASI C-10 (tahap RW): Perubahan data anggota keluarga**
>
> Pak **Budi Santoso** mengajukan perubahan data anggota keluarganya. Pak **Budi Sampurno** (Ketua RT 03) sudah menyetujui. Pengajuan menunggu RW. Pak Dedi membukanya.
>
> - **Masuk sebagai:** `pimpinan`
> - **Sebelumnya:** lihat *01-Warga.md* dan *02-Ketua-RT.md, C-10*
> - **Catatan:** data demo bawaan belum memuat pengajuan seperti ini, jadi daftar kosong sampai ada yang mengajukan.

### Langkah-langkah

**1.** Klik **Pengajuan Data**. Kolom: **WAKTU PENGAJUAN**, **KEPALA KELUARGA**, **JENIS PENGAJUAN** (**Tambah Anggota Baru**, **Edit Data Anggota**, **Perubahan Data KK**), **DETAIL AJUAN**, **AKSI**.
**2.** Klik **Detail**. Periksa tabel **Perubahan Data** (baris kuning berarti berubah), **Riwayat Proses**, dan **File Bukti (Scan KK)** (**Lihat Dokumen**).
**3.** Klik **Setujui Final (Simpan ke Data Penduduk)**, atau **Tolak** lalu isi **Alasan penolakan** dan klik **Tolak Pengajuan**.

> 📸 **SCREENSHOT KRW-14: Verifikasi Pengajuan Data (Ketua RW)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/mutasi-warga
> - **Kondisi:** minimal satu pengajuan menunggu RW, modal **Detail Pengajuan Data** terbuka
> - **Area:** modal penuh
> - **Sorot:** kotak merah pada **Nilai Lama**, **Nilai Baru**, dan tombol **Setujui Final (Simpan ke Data Penduduk)**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-14-verifikasi-pengajuan-data.png`

### ✅ Hasilnya

- Disetujui: **"Pengajuan data disetujui dan data penduduk telah diperbarui."** Pengaju menerima **Perubahan Data Disetujui**.
- Ditolak: **"Pengajuan data ditolak."** Pengaju menerima **Perubahan Data Ditolak** beserta alasan.

### ⚠️ Perhatian

- **"NIK sudah terdaftar pada data penduduk."** atau **"... pada data penduduk lain."** NIK bentrok. Tolak dan minta warga memeriksa NIK. Pesan ini bisa tidak terlihat di layar.
- **"Status pengajuan tidak valid untuk diproses oleh RW."** Pengajuan sudah diproses.
- **Daftar kosong: "Belum ada pengajuan mutasi baru".** Tidak ada pengajuan menunggu RW.

> 💡 **Tips:** Karena Admin RW juga bisa memprosesnya, cek dulu Admin RW agar pengajuan tidak diproses dua kali.

---

## 10. Rekap Iuran RT

Menu **Rekap Iuran RT**: "Iuran warga tercatat di kas RT. Kas RW bertambah saat setoran RT dikonfirmasi." Halaman hanya baca. Konfirmasi setoran dilakukan Operator Keuangan RW.

> 👤 **SIMULASI: Mengecek setoran RT**
>
> Pak Dedi ingin tahu RT mana yang sudah menyetor kewajibannya sebelum rapat.
>
> - **Masuk sebagai:** `pimpinan`

### Langkah-langkah

**1.** Klik **Rekap Iuran RT** (atau tautan **Rekap iuran per RT** di dashboard).
**2.** Pilih **Bulan** dan **Tahun**.
**3.** Baca kartu **Iuran terkumpul (semua RT)**, **Kewajiban setoran**, **Setoran diterima**, dan **Sisa s.d. {bulan}**.
**4.** Tab **Rekap per RT**: **KK lunas** (bentuk "x / total"), **Terkumpul**, **Setoran/KK**, **Kewajiban**, **Disetor**, **Menunggu**, **Status** (**Lunas**, **Sebagian**, **Menunggu konfirmasi**, **Belum setor**, **Tidak ada kewajiban**). Tab **Riwayat** memuat setoran **Dikonfirmasi**, **Ditolak**, **Dibatalkan** dengan tautan **Lihat bukti**.

> 📸 **SCREENSHOT KRW-15: Rekap Iuran RT (Ketua RW)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /rw/rekap-iuran
> - **Kondisi:** data demo; tab **Rekap per RT**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kartu ringkasan dan kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-15-rekap-iuran-rt.png`

### ✅ Hasilnya

Anda melihat kepatuhan setoran tiap RT.

### ⚠️ Perhatian

- **Banner kuning: "Nominal setoran per KK untuk {tahun} belum ditetapkan, sehingga kewajiban setoran bernilai nol."** Nominal ditetapkan Operator Keuangan RW.
- **"Belum ada riwayat setoran pada tahun {tahun}."** Belum ada setoran. Pada data demo awal memang belum ada.
- **Tidak ada tombol Konfirmasi atau Nominal Setoran.** Itu wewenang Operator Keuangan RW.

> 💡 **Tips:** Lihat tab **Iuran Lainnya** bila ada tagihan RW yang dikumpulkan lewat RT.

---

## 11. Laporan Keuangan dan Statistik

Menu **Laporan Keuangan** (judul **Transparansi Keuangan**) menampilkan laporan bulanan RT, RW, dan DKM yang sudah disetujui dan diterbitkan. Ketua RW melihat dua tab: **Daftar Laporan** dan **Statistik** (**Statistik** hanya untuk pengurus RW).

> 👤 **SIMULASI C-11 (sisi pimpinan): Memeriksa kondisi keuangan**
>
> Bendahara RW, Pak **Hendra Gunawan**, menyusun dan menerbitkan laporan bulanan (lihat *08-Op-Keuangan-RW.md, C-11*). Pak Dedi membuka laporan dan tab **Statistik** untuk menilai kondisi kas sebelum rapat.
>
> - **Masuk sebagai:** `pimpinan`
> - **Data demo:** tiga periode terakhir laporan RW, DKM, dan RT 01

### Langkah-langkah

**1.** Klik **Laporan Keuangan**. Di tab **Daftar Laporan**, saring dengan **Tahun**, **Bulan**, dan **Unit** (**Semua Unit (RT, RW, DKM)**, **Hanya RT**, **Hanya RW**, **Hanya DKM**), lalu **Filter**.
**2.** Kolom: **Periode**, **Unit**, **Pemasukan**, **Pengeluaran**, **Saldo Akhir**, **Aksi**. Klik ikon **Unduh PDF** untuk membuka laporan di tab baru.

> 📸 **SCREENSHOT KRW-16: Daftar Laporan Keuangan (Ketua RW)**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /laporan-keuangan
> - **Kondisi:** data demo; laporan RW, DKM, dan RT 01 tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada filter **Unit**, tab **Statistik**, dan ikon **Unduh PDF**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-16-daftar-laporan-keuangan.png`

**3.** Klik tab **Statistik**. Pilih unit (RT, RW, atau DKM; untuk Ketua RW awalnya RW) dan **Tahun**. Isinya:

- Penilaian level: **Kondisi baik**, **Perlu diperhatikan**, **Perlu tindakan**, atau **Belum ada data**, dengan daftar **Perlu perhatian**.
- Grafik **Tren Kas {tahun}** (batang pemasukan dan pengeluaran, garis saldo akhir).
- **Sumber pemasukan** dan **Penggunaan dana**.
- **Iuran wajib dan setoran ke RW** dengan kepatuhan KK: **Tepat waktu**, **Menunggak 1 bulan**, **Menunggak 2 bulan atau lebih**.

> 📸 **SCREENSHOT KRW-17: Tab Statistik Laporan Keuangan**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /laporan-keuangan?tab=statistik
> - **Kondisi:** unit RW, tahun berjalan
> - **Area:** satu layar penuh (boleh dua tangkapan)
> - **Sorot:** kotak merah pada penilaian level dan grafik **Tren Kas**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-17-statistik-laporan-keuangan.png`

### ✅ Hasilnya

PDF laporan terbuka dengan nama `Laporan_Keuangan_{unit}_{tahun}_{bulan}.pdf`, dan tab **Statistik** memberi gambaran sehat atau tidaknya kas.

### ⚠️ Perhatian

- **"Belum ada laporan yang dipublikasikan."** Bendahara belum menerbitkan laporan.
- **Tidak ada tombol Excel di layar.** Hanya PDF.
- **Menu Persetujuan Keuangan tidak ada.** Menu itu disembunyikan dari aplikasi, jadi Ketua RW tidak menyetujui laporan lewat aplikasi. (Data demo tetap mencatat laporan sebagai sudah disetujui oleh akun `pimpinan`.)

> 💡 **Tips:** Mulai dari tab **Statistik** untuk gambaran umum, lalu buka PDF bila perlu rincian.

---

## 12. Pengaturan Tanda Tangan dan Stempel

Menu **Tanda Tangan** (di bagian bawah menu, hanya untuk Ketua RT dan Ketua RW). Judul halaman **Pengaturan Tanda Tangan**, heading **Pengaturan Tanda Tangan & Stempel**: "Simpan tanda tangan dan stempel default agar tidak perlu mengisi ulang setiap menyetujui surat."

> 👤 **SIMULASI: Menyimpan tanda tangan default**
>
> Pak Dedi akan sering mengesahkan surat. Ia menyimpan tanda tangan dan stempel RW supaya cukup sekali.
>
> - **Masuk sebagai:** `pimpinan`
> - **Berkas:** PNG tanda tangan (latar transparan lebih baik) dan PNG stempel RW

### Langkah-langkah

**1.** Klik **Tanda Tangan** di menu samping.
**2.** Kartu **Tanda Tangan Default**: pilih tab **Unggah File** (**File PNG (disarankan latar transparan, maks 2MB)**) atau tab **Gambar** (coret langsung pada **Gambar tanda tangan**; tombol **Ulangi** menghapus). Teks "Hasil gambar akan disimpan saat Anda menekan tombol Simpan." Bila sudah ada, tampil "Tanda tangan tersimpan saat ini:" dengan tombol **Hapus**.
**3.** Kartu **Stempel Default**: unggah PNG stempel. Stempel bersifat opsional.
**4.** Klik **Simpan Pengaturan**.

> 📸 **SCREENSHOT KRW-18: Pengaturan Tanda Tangan & Stempel**
> - **Akun:** pimpinan (Ketua RW)
> - **URL:** /pengaturan/tanda-tangan
> - **Kondisi:** tanda tangan dan stempel sudah tersimpan
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tab **Unggah File** dan **Gambar**, tombol **Hapus**, dan tombol **Simpan Pengaturan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRW-18-pengaturan-tanda-tangan.png`

### ✅ Hasilnya

Pesan **"Pengaturan tanda tangan dan stempel berhasil disimpan."** Saat mengesahkan surat (bab 6), pilihan **TTD Tersimpan** dan **Gunakan stempel tersimpan** tersedia.

### ⚠️ Perhatian

- **"Tanda tangan harus berupa file PNG."** atau **"Stempel harus berupa file PNG."** Gunakan format PNG.
- **"Ukuran tanda tangan maksimal 2MB."** atau **"Ukuran stempel maksimal 2MB."** Kecilkan ukuran berkas.
- **Menghapus**: tombol **Hapus** menampilkan "Hapus tanda tangan default tersimpan?" (atau stempel). Tekan **Hapus** untuk melanjutkan.
- **Tanda tangan adalah data pribadi Anda.** File tersimpan privat dan hanya bisa dibuka oleh akun Anda sendiri.

> 💡 **Tips:** Tanda tangan dengan latar transparan terlihat lebih rapi pada surat.

---

## 13. Cerita Lengkap (Lintas Peran)

| Kode | Cerita | Peran Ketua RW | Sub-bab |
|---|---|---|---|
| C-01 | Surat Keterangan Domisili Pak Budi Santoso | Dapat mengesahkan surat (sama seperti Admin RW) | 6.1 |
| C-02 | Surat ditolak karena data kurang | Dapat menolak di tahap RW dengan catatan | 6.2 |
| C-05 | Jasa Service Elektronik Pak Budi disetujui | Memantau di riwayat dan Pojok UMKM | 7.1, 7.2 |
| C-06 | Koperasi Warga Tanimulya ditolak lalu diajukan ulang | Memantau di tab Pengajuan Ditolak | 7.1 |
| C-07 | Berita Rencana Pemasangan CCTV Lingkungan | Memantau setelah terbit di Pusat Informasi | 8 |
| C-10 | Perubahan data anggota keluarga | Dapat menyetujui atau menolak tahap RW | 9 |
| C-11 | Laporan keuangan terbit | Melihat laporan dan statistik | 11 |

---

## 14. Masalah yang Sering Terjadi (FAQ)

**Saya tidak melihat menu Persetujuan Konten.**
Benar. Hanya Admin RW yang menyetujui konten. Ketua RW memantau lewat **Pusat Informasi**.

**Tombol Setujui dan Tolak UMKM tidak ada.**
Itu disengaja. Persetujuan UMKM dilakukan Admin RW; Ketua RW memantau lewat **Riwayat Persetujuan UMKM**.

**Muncul 403 "Verifikasi UMKM hanya dapat dilakukan oleh Admin RW." atau "Persetujuan konten hanya dapat dilakukan oleh Admin RW."**
Aksi itu hanya untuk Admin RW. Minta Admin RW memprosesnya.

**Muncul "Anda tidak memiliki hak akses untuk membuka halaman tersebut."**
Halaman itu bukan untuk peran Anda. Kembali ke **Dashboard** dan buka lewat menu.

**Kartu KONTEN DITINJAU menunjukkan 0 padahal ada berita menunggu.**
Kartu itu hanya menghitung untuk Admin RW. Antrean konten tidak terlihat dari akun Ketua RW.

**Portal Persetujuan Dokumen kosong padahal warga bilang sudah mengajukan.**
Surat masih menunggu Ketua RT. Surat baru masuk ke tahap RW setelah RT menyetujui.

**Surat sudah saya sahkan tetapi tidak ada di antrean.**
Wajar. Statusnya sudah **Selesai** dan pindah ke **Riwayat Pengajuan**.

**Menu Persetujuan Keuangan tidak ada.**
Disembunyikan dari aplikasi. Laporan yang sudah terbit dapat dilihat di **Laporan Keuangan**.

**Notifikasi hilang dari lonceng.**
Mungkin tersembunyi. Buka lonceng dan klik **Pulihkan Notifikasi**.

---

## 15. Lampiran: Daftar Gambar

Total **18 gambar** (semua desktop 1366×768).

| Kode | Judul | Bab |
|---|---|---|
| KRW-01 | Peta menu Ketua RW | 1 |
| KRW-02 | Dashboard Ketua RW bagian atas | 3 |
| KRW-03 | Dashboard Ketua RW bagian bawah | 3 |
| KRW-04 | Manajemen Penduduk (Ketua RW) | 4 |
| KRW-05 | Detail Keluarga | 4 |
| KRW-06 | Manajemen Data RT (Ketua RW) | 5 |
| KRW-07 | Portal Persetujuan Dokumen (Ketua RW) | 6.1 |
| KRW-08 | Modal Tanda Tangan & Sahkan memakai TTD tersimpan | 6.1 |
| KRW-09 | Riwayat Pengajuan Dokumen | 6.3 |
| KRW-10 | Riwayat Persetujuan UMKM (mode monitoring) | 7.1 |
| KRW-11 | Detail Pengajuan UMKM tanpa tombol aksi | 7.1 |
| KRW-12 | Pojok UMKM (Ketua RW) | 7.2 |
| KRW-13 | Pusat Informasi (Ketua RW) | 8 |
| KRW-14 | Verifikasi Pengajuan Data (Ketua RW) | 9 |
| KRW-15 | Rekap Iuran RT (Ketua RW) | 10 |
| KRW-16 | Daftar Laporan Keuangan (Ketua RW) | 11 |
| KRW-17 | Tab Statistik Laporan Keuangan | 11 |
| KRW-18 | Pengaturan Tanda Tangan & Stempel | 12 |
