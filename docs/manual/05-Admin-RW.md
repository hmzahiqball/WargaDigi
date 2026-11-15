# Manual Book WargaDigi 21: Admin RW

Buku panduan untuk **Admin RW** (sekretariat RW) pengguna aplikasi WargaDigi 21 di https://wargadigi21.com.
Tokoh simulasi: **Bu Rina** *(fiktif)*, akun `adminrw21`, kata sandi demo `password`.

<!-- AI-INSTRUKSI: Ubah file ini menjadi dokumen Word bergambar mengikuti konvensi blok di README.md.
Blok "SCREENSHOT" diganti gambar bernama sama (awalan ARW, desktop 1366×768) dengan keterangan "Gambar <KODE>: <judul>".
Komentar HTML jangan dicetak. Semua label di dokumen ini diambil dari kode aplikasi; jangan diubah. -->

## Daftar Isi

1. Kenalan Dulu
2. Mulai Menggunakan
3. Dashboard RW
4. Manajemen Penduduk
5. Manajemen RT
6. Persetujuan Dokumen (mengesahkan surat)
7. Persetujuan Konten (berita, agenda, pengumuman)
8. Persetujuan UMKM
9. Pojok UMKM
10. Pengajuan Data (perubahan data warga)
11. Pusat Informasi
12. Rekap Iuran RT
13. Laporan Keuangan
14. Cerita Lengkap (Lintas Peran)
15. Masalah yang Sering Terjadi (FAQ)
16. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### Tugas Admin RW

Admin RW adalah sekretariat RW. Pekerjaan utamanya adalah memeriksa dan mengesahkan hal-hal yang sudah naik dari RT atau dari warga:

- Mengesahkan surat pengantar yang sudah disetujui Ketua RT.
- Menyetujui berita, agenda, dan pengumuman sebelum tayang (dari RW maupun dari semua RT).
- Memverifikasi pendaftaran usaha warga untuk Pojok UMKM, serta menonaktifkan atau mengaktifkan usaha yang sudah tampil.
- Menyetujui pengajuan perubahan data warga (tahap RW).
- Memantau data penduduk, data RT, rekap iuran RT, dan laporan keuangan.

### Tokoh simulasi

> 👤 **SIMULASI: Mengenal Bu Rina**
>
> **Bu Rina** *(fiktif)* bertugas di sekretariat RW 21 Tanimulya. Setiap hari ia membuka WargaDigi 21 untuk melihat apa saja yang menunggu pengesahan. Ia memakai akun `adminrw21`. Akun ini tidak tertaut ke data penduduk, jadi yang tampil hanyalah nama pengguna.
>
> Pengurus lain yang sering ia temui di cerita: Pak **Dedi Mulyadi** (Ketua RW, akun `pimpinan`), Pak **Budi Sampurno** (Ketua RT 03, akun `budisampurno`), dan Pak **Budi Santoso** (warga RT 03, akun `budisantoso`).

### Peta menu Admin RW

Menu ada di sisi kiri layar. Di HP, tekan tombol **☰** di pojok kiri atas supaya menu muncul.

| Menu | Isinya | Bab |
|---|---|---|
| **Dashboard** | Ringkasan pekerjaan yang menunggu | 3 |
| **Manajemen Penduduk** | Data keluarga dan penduduk seluruh RW (hanya lihat dan unduh) | 4 |
| **Manajemen RT** | Daftar RT, tambah, ubah, hapus | 5 |
| **Persetujuan** | Induk dari empat submenu di bawah | 6 sampai 10 |
| ↳ **Persetujuan Dokumen** | Mengesahkan surat | 6 |
| ↳ **Persetujuan Konten** | Berita, agenda, pengumuman | 7 |
| ↳ **Persetujuan UMKM** | Pendaftaran usaha warga | 8 |
| ↳ **Pengajuan Data** | Perubahan data anggota keluarga | 10 |
| **Laporan Keuangan** | Laporan bulanan RT, RW, dan DKM yang sudah terbit | 13 |
| **Rekap Iuran RT** | Iuran dan setoran tiap RT (hanya lihat) | 12 |
| **Pusat Informasi** | Konten yang sudah terbit | 11 |
| **Pojok UMKM** | Usaha yang sudah disetujui | 9 |
| **Keluar** | Keluar dari aplikasi | 2 |

> 📸 **SCREENSHOT ARW-01: Peta menu Admin RW**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/dashboard
> - **Kondisi:** menu **Persetujuan** terbuka sehingga empat submenu terlihat
> - **Area:** sisi kiri layar (sidebar) dari atas sampai bawah
> - **Sorot:** kotak merah pada **Persetujuan** dan nomor 1 sampai 4 pada submenunya
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-01-peta-menu-admin-rw.png`

⚠️ **Perhatian**

- Menu **Pusat Bantuan** terlihat di bagian bawah menu, tetapi belum memiliki halaman. Abaikan dulu.
- Menu **Persetujuan Konten** hanya ada pada Admin RW. Ketua RW tidak memilikinya.
- Admin RW tidak memiliki menu **Tanda Tangan**. Menu itu hanya untuk Ketua RT dan Ketua RW.

---

## 2. Mulai Menggunakan

Cara masuk, memilih peran, dan keluar dibahas lengkap di `00-Umum-Publik-dan-Login.md`. Ringkasnya:

1. Buka https://wargadigi21.com lalu masuk dengan username `adminrw21` dan kata sandi `password`.
2. Anda langsung tiba di **Dashboard RW**. Akun ini hanya punya satu peran, jadi tidak ada halaman pilih peran.
3. Untuk keluar, klik **Keluar** di bagian bawah menu.

### Notifikasi (ikon lonceng)

Ikon lonceng di bagian atas layar menampilkan notifikasi untuk Anda. Isi yang sering muncul pada Admin RW:

- **Surat Menunggu Penerbitan**: "Surat ... dari ... sudah disetujui RT dan menunggu penerbitan RW."
- **{Jenis} Menunggu Persetujuan** (Berita, Agenda, atau Pengumuman): konten dari operator menunggu persetujuan Anda.
- **UMKM Baru Menunggu Verifikasi** dan **UMKM Diajukan Ulang**.
- **Pengajuan Perubahan Data**: ada pengajuan data warga yang menunggu RW.

Klik sebuah notifikasi untuk langsung membuka halaman terkait. Tombol ikon mata silang pada notifikasi hanya menyembunyikan notifikasi itu dari daftar. Bila semua disembunyikan, muncul tombol **Pulihkan Notifikasi**.

> 📸 **SCREENSHOT ARW-02: Daftar notifikasi**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/dashboard
> - **Kondisi:** ikon lonceng diklik dan beberapa notifikasi tampil
> - **Area:** sudut kanan atas layar beserta daftar notifikasi
> - **Sorot:** kotak merah pada ikon lonceng dan pada satu notifikasi
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-02-daftar-notifikasi.png`

💡 **Tips:** Kalau daftar kosong, tampil tulisan **Tidak ada notifikasi baru**. Artinya belum ada pekerjaan baru untuk Anda.

---

## 3. Dashboard RW

Halaman pertama setelah masuk. Judul halamannya **Dashboard RW**, dengan heading **Ringkasan** dan label **STATUS: AKTIF**.

> 👤 **SIMULASI: Pagi di sekretariat RW**
>
> Bu Rina membuka laptop dan melihat dashboard sebelum mengerjakan apa pun. Dari sini ia tahu mana yang harus diurus lebih dulu.
>
> - **Masuk sebagai:** `adminrw21`
> - **Yang dicari:** pekerjaan yang menunggu (surat, usaha warga, konten, pengajuan data)

### Langkah-langkah

**1. Baca banner pemberitahuan di bagian atas.**
Banner hanya tampil bila ada pekerjaan:

- **Pemberitahuan UMKM Baru Menunggu Verifikasi**, dengan tombol **Tinjau UMKM Sekarang**.
- **Terdapat Dokumen Surat yang Perlu Disahkan**, dengan tombol **Tinjau Dokumen Sekarang**.

**2. Lihat empat kartu statistik.** Semua kartu bisa diklik.

| Kartu | Isinya | Klik membuka |
|---|---|---|
| **JUMLAH PENDUDUK** | Total penduduk dan tambahan bulan ini | Manajemen Penduduk |
| **DOKUMEN PENDING** | Surat berstatus Disetujui RT ditambah Diajukan | Persetujuan Dokumen |
| **UMKM BARU** | **Menunggu Verifikasi** atau **Semua Terverifikasi** | Persetujuan UMKM |
| **KONTEN DITINJAU** | Berita, agenda, dan pengumuman berstatus Ditinjau. Teksnya **Perlu Ditinjau** atau **Semua sudah ditinjau** | Persetujuan Konten |

> 📸 **SCREENSHOT ARW-03: Dashboard bagian atas**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/dashboard
> - **Kondisi:** data demo awal; banner UMKM baru tampil karena ada usaha Pending
> - **Area:** dari judul **Ringkasan** sampai empat kartu statistik
> - **Sorot:** kotak merah pada banner UMKM dan pada kartu **KONTEN DITINJAU**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-03-dashboard-atas.png`

**3. Gulir ke bawah untuk panel lainnya.**

- **Kas RW**: **Saldo kas**, **Pemasukan** dan **Pengeluaran** bulan berjalan, laporan RW terakhir, tautan **Lihat Laporan Keuangan** dan **Rekap iuran per RT**. Panel ini hanya baca.
- **AKTIVITAS MENUNGGU TINDAKAN**: maksimal 6 baris terbaru, dengan lencana **PERLU VERIFIKASI** (UMKM), **PERLU PENGESAHAN** (surat), dan **PERLU PERSETUJUAN** (pengajuan data). Kalau kosong: "Tidak ada aktivitas yang menunggu tindakan."
- **Aksi Cepat**: **Tinjau Dokumen**, **Verifikasi UMKM**, **Periksa Konten**, dan **Manajemen Penduduk**.
- **Konten Terbaru**: empat konten terbaru. Admin RW melihat yang berlabel **TERBIT** dan **MENUNGGU TINJAUAN**. Tautan **Lihat Semua** membuka Pusat Informasi.
- **Demografi Penduduk**: grafik **Kelompok Usia**, **Pendidikan Terakhir**, **Pekerjaan**, **Jenis Kelamin**, **Status Perkawinan**, **Status Warga**, dan **Sebaran Penduduk per RT**.

> 📸 **SCREENSHOT ARW-04: Dashboard bagian bawah**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/dashboard (gulir ke bawah)
> - **Kondisi:** data demo awal
> - **Area:** panel **Kas RW**, **AKTIVITAS MENUNGGU TINDAKAN**, **Aksi Cepat**, **Konten Terbaru**
> - **Sorot:** kotak merah pada **Aksi Cepat** dan pada lencana **PERLU VERIFIKASI**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-04-dashboard-bawah.png`

### ✅ Hasilnya

Anda tahu persis berapa surat, usaha, konten, dan pengajuan data yang menunggu, dan bisa langsung membukanya dari kartu atau banner.

### ⚠️ Perhatian

- **Kartu DOKUMEN PENDING tidak nol padahal banner surat tidak muncul.** Angkanya juga menghitung surat berstatus **Diajukan** (masih di tangan Ketua RT). Banner hanya menghitung yang sudah disetujui RT.
- **Daftar demo awal tidak punya surat yang menunggu RW.** Surat contoh Pak Budi Santoso sudah berstatus **Selesai**. Surat baru muncul setelah ada pengajuan dan RT menyetujuinya (lihat 6.1).

> 💡 **Tips:** Mulai hari dari dashboard, lalu kerjakan dari yang bertanda lencana.

> ➡️ **Cerita berlanjut:** Mulai dari bab 6 untuk mengesahkan surat.

---

## 4. Manajemen Penduduk

Menu **Manajemen Penduduk** menampilkan data keluarga dan penduduk di **seluruh RW**. Admin RW hanya bisa **melihat dan mengunduh**. Tidak ada tombol tambah, ubah, atau hapus di halaman ini. Perubahan data dilakukan warga lewat pengajuan (bab 10) atau pendataan oleh Ketua RT.

### 4.1 Mencari Keluarga atau Penduduk

> 👤 **SIMULASI: Mencari keluarga Pak Budi Santoso**
>
> Petugas bank menelepon sekretariat untuk memastikan Pak **Budi Santoso** memang warga RW 21. Bu Rina mencari datanya.
>
> - **Masuk sebagai:** `adminrw21`
> - **Yang dicari:** keluarga atas nama Budi Santoso, RT 03

#### Langkah-langkah

**1. Buka menu Manajemen Penduduk.**
Klik **Manajemen Penduduk** di menu samping. Judul halaman **Manajemen Penduduk** dan keterangan "Data penduduk di seluruh RW." tampil, dengan lencana **Total: N jiwa**.

**2. Pilih tab.**
Ada dua tab: **Profil Keluarga** dan **Daftar Semua Penduduk**.

**3. Cari di tab Profil Keluarga.**
Ketik pada kotak **Cari Keluarga (No. KK / Nama Kepala / Alamat)**, misalnya *Budi Santoso*, lalu tekan **Filter**.

**4. Batasi per RT bila perlu.**
Pada pilihan **RT**, pilih **RT 03** (default **Semua RT**), lalu **Filter**. Keterangan halaman berubah menjadi "Data penduduk di RT 03." Tombol X (**Reset Filter**) mengembalikan semua.

Kolom tabel keluarga: **NO. KK**, **KEPALA KELUARGA**, **RT**, **ALAMAT**, **STATUS WARGA** (**Warga Tetap** atau **Bukan Warga Tetap**), **PEMILIK RUMAH** (**Ya** atau **Bukan**), **ANGGOTA** (misalnya "4 jiwa"), dan **AKSI** (**Detail**).

> 📸 **SCREENSHOT ARW-05: Manajemen Penduduk, tab Profil Keluarga**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/penduduk
> - **Kondisi:** tab **Profil Keluarga** terbuka, tanpa filter
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kotak pencarian, pilihan **RT**, dan tombol **Export**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-05-manajemen-penduduk-keluarga.png`

> 📸 **SCREENSHOT ARW-06: Hasil filter RT 03**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/penduduk?rt_id=... (pilih RT 03 lalu **Filter**)
> - **Kondisi:** pilihan RT bernilai **RT 03**, pencarian berisi *Budi*
> - **Area:** kotak filter dan tabel hasil
> - **Sorot:** kotak merah pada pilihan **RT 03** dan baris Budi Santoso
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-06-filter-rt-03.png`

**5. Cari di tab Daftar Semua Penduduk.**
Pindah ke tab ini, ketik nama atau NIK di kotak **Cari Penduduk (Nama / NIK)**. Kolomnya: **NAMA**, **NIK**, **L/P**, **HUBUNGAN KELUARGA**, **NO. KK**, **RT**, **AKSI**.

#### ✅ Hasilnya

Baris yang cocok tampil, dengan keterangan "Menampilkan X - Y dari N keluarga" (atau "penduduk"). Tiap halaman memuat 15 baris.

#### ⚠️ Perhatian

- **"Tidak ada keluarga yang cocok dengan pencarian."** (atau "Tidak ada penduduk yang cocok dengan pencarian.") Cek ejaan, atau pilih **Semua RT**.
- **Pencarian tab keluarga dan tab penduduk terpisah.** Kata yang diketik di satu tab tidak ikut ke tab satunya.

> 💡 **Tips:** Mencari dengan NIK paling cepat dan paling tepat, karena nama bisa kembar.

> ➡️ **Cerita berlanjut:** Klik **Detail** untuk melihat satu keluarga (4.2).

### 4.2 Melihat Detail Keluarga dan Penduduk

#### Langkah-langkah

**1. Klik Detail pada baris keluarga.**
Halaman **Detail Keluarga** tampil dengan keterangan "Data Kartu Keluarga dan anggota penduduk."

**2. Baca kartu-kartunya.**

- **Profil Kartu Keluarga**: **Nomor KK**, **Kepala Keluarga**, **Alamat** (sesuai KK), **Alamat Domisili**, **Status KK**, **Pemilik Rumah**.
- **Akun Keluarga**: **Username** dan **Status Akun** (**Aktif** atau **Belum Diaktivasi**). Bila belum aktif, tampil link aktivasi dengan tombol **Salin**. Untuk Admin RW, tombol pembuat ulang link tidak ada; tampil teks "Link aktivasi dibuat ulang oleh Ketua RT terkait."
- **Anggota Keluarga (N jiwa)**: kolom **NAMA**, **NIK**, **L/P**, **HUBUNGAN**, **AKSI**.

> 📸 **SCREENSHOT ARW-07: Detail Keluarga**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/keluarga/{id} (dari tombol **Detail** keluarga Budi Santoso)
> - **Kondisi:** keluarga Budi Santoso terbuka
> - **Area:** kartu **Profil Kartu Keluarga** sampai daftar anggota
> - **Sorot:** kotak merah pada **Nomor KK** dan pada tombol **Detail** anggota
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-07-detail-keluarga.png`

**3. Buka detail seorang anggota.**
Klik **Detail** pada anggota. Halaman **Detail Penduduk** menampilkan **Nama Lengkap**, **NIK**, **Nomor WA**, **Jenis Kelamin**, **Tempat, Tanggal Lahir**, **Agama**, **Pekerjaan**, **Pendidikan Terakhir**, **Status Perkawinan**, **Kewarganegaraan**, **Hubungan Keluarga**, **Status Warga**, **Nomor KK**, **Alamat**, dan **Alamat Domisili**. Kartu **Kartu Tanda Penduduk** punya tombol **Lihat KTP (PDF)** atau gambar yang bisa diperbesar.

> 📸 **SCREENSHOT ARW-08: Detail Penduduk**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/penduduk/{id}
> - **Kondisi:** penduduk Budi Santoso terbuka
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Kembali** dan kartu **Kartu Tanda Penduduk**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-08-detail-penduduk.png`

#### ✅ Hasilnya

Anda melihat data lengkap tanpa bisa mengubahnya.

#### ⚠️ Perhatian

- **"Belum ada file KTP yang diunggah."** Warga belum mengunggah KTP. Tidak ada yang bisa Anda lakukan dari sini.
- **Data salah.** Admin RW tidak bisa memperbaikinya langsung. Minta kepala keluarga mengajukan perubahan (bab 10).

> 💡 **Tips:** Tombol **Kembali** membawa Anda ke halaman sebelumnya lengkap dengan filter yang tadi dipakai.

### 4.3 Mengunduh Data Penduduk (Excel)

> 👤 **SIMULASI: Data untuk rapat RW**
>
> Bu Rina diminta Pak **Dedi Mulyadi** menyiapkan daftar penduduk RT 03 untuk rapat. Ia hanya butuh nama, NIK, dan alamat.
>
> - **Masuk sebagai:** `adminrw21`
> - **Filter:** RT 03

#### Langkah-langkah

**1. Atur filter.** Di tab **Daftar Semua Penduduk**, pilih **RT 03** dan tekan **Filter**. Bila perlu isi pencarian. Unduhan mengikuti **RT** dan pencarian nama/NIK yang aktif.

**2. Klik Export.** Modal **Export Data Penduduk (RT 03)** tampil dengan teks: "Pilih kolom data yang ingin diekspor ke Excel."

**3. Pilih kolom.** Semua kolom tercentang di awal. Tekan **Kosongkan**, lalu centang **NIK**, **Nama Lengkap**, dan **Alamat (KTP)**. Pilihan lain: **Nomor WA**, **Jenis Kelamin**, **Tempat Lahir**, **Tanggal Lahir**, **Agama**, **Pekerjaan**, **Pendidikan Terakhir**, **Hubungan Keluarga**, **Status Perkawinan**, **Kewarganegaraan**, **Status Warga**, **Alamat Domisili**, **Nama Ayah**, **Nama Ibu**, **No. KK**, **RT**. Tombol **Pilih Semua** mencentang semuanya.

**4. Tekan Export Excel.**

> 📸 **SCREENSHOT ARW-09: Modal Export Data Penduduk**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/penduduk (tab **Daftar Semua Penduduk**)
> - **Kondisi:** modal terbuka, hanya tiga kolom tercentang
> - **Area:** modal beserta tombol **Batal** dan **Export Excel**
> - **Sorot:** kotak merah pada tombol **Kosongkan** dan **Export Excel**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-09-modal-export-penduduk.png`

#### ✅ Hasilnya

Browser mengunduh file Excel bernama `data-penduduk-TTTTBBHH-JJMMDD.xlsx` (tanggal dan jam unduh).

#### ⚠️ Perhatian

- **"Pilih minimal satu kolom untuk diekspor."** Anda belum mencentang kolom. Centang minimal satu.
- **File berisi NIK dan data pribadi.** Simpan dan bagikan seperlunya.

> 💡 **Tips:** Filter pencarian di tab keluarga (**Cari Keluarga**) tidak ikut ke unduhan. Untuk unduhan, atur filter di tab **Daftar Semua Penduduk**.

---

## 5. Manajemen RT

Menu **Manajemen RT** (judul halaman **Manajemen Data RT**) berisi daftar Rukun Tetangga di RW. Di sini Admin RW dapat menambah, mengubah, dan menghapus RT.

> 👤 **SIMULASI: Menambah RT baru**
>
> Pengurus memutuskan menambah RT baru untuk wilayah yang berkembang. Bu Rina diminta mencatatnya. Data demo sudah memuat RT 01 sampai RT 06, jadi contoh ini memakai kode **07**.
>
> - **Masuk sebagai:** `adminrw21`
> - **Kode RT:** 07
> - **Nama RT:** 07

### 5.1 Melihat Daftar RT

**1.** Klik **Manajemen RT**. Keterangan halaman: "Kelola daftar Rukun Tetangga (RT) di lingkungan RW."
**2.** Kolom tabel: **No**, **Kode RT** (tampil "RT 03"), **Nama RT**, **Jumlah Keluarga** (tampil "N KK"), **Aksi**.
**3.** Cari dengan kotak "Cari kode atau nama RT..." lalu **Cari**. Tiap halaman memuat 10 RT.

> 📸 **SCREENSHOT ARW-10: Manajemen Data RT**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/rt
> - **Kondisi:** data demo (RT 01 sampai RT 06)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Tambah RT** dan dua tombol ikon di kolom **Aksi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-10-manajemen-rt.png`

### 5.2 Menambah RT

**1.** Klik **Tambah RT**. Modal **Tambah RT Baru** tampil.
**2.** Isi **Kode RT** (wajib, maksimal 10 karakter), misalnya *07*.
**3.** Isi **Nama RT** (wajib, maksimal 50 karakter), misalnya *07*.
**4.** Klik **Tambah RT**.

> 📸 **SCREENSHOT ARW-11: Modal Tambah RT Baru**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/rt
> - **Kondisi:** modal terbuka, **Kode RT** dan **Nama RT** terisi "07"
> - **Area:** modal
> - **Sorot:** kotak merah pada kedua kolom dan tombol **Tambah RT**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-11-modal-tambah-rt.png`

#### ✅ Hasilnya

Muncul pesan **"Data RT 07 berhasil ditambahkan."** dan RT 07 ada di daftar dengan **0 KK**.

### 5.3 Mengubah RT

Klik tombol ikon pensil pada baris RT. Modal **Edit RT {kode}** muncul. Ubah **Kode RT** atau **Nama RT**, lalu **Simpan Perubahan**. Pesan: **"Data RT {kode} berhasil diperbarui."**

### 5.4 Menghapus RT

Klik tombol ikon tempat sampah. Modal **Hapus RT** bertanya "Yakin ingin menghapus RT {kode} - {nama}? Tindakan ini tidak dapat dibatalkan." Tekan **Hapus**. Pesan: **"Data RT {kode} berhasil dihapus."**

> 📸 **SCREENSHOT ARW-12: Modal Hapus RT yang masih punya keluarga**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/rt
> - **Kondisi:** tombol hapus pada **RT 03** diklik (RT 03 punya keluarga)
> - **Area:** modal
> - **Sorot:** kotak merah pada teks "RT ini masih memiliki ... data keluarga dan tidak dapat dihapus." dan pada tombol **Hapus** yang nonaktif
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-12-modal-hapus-rt.png`

### ⚠️ Perhatian

- **"Kode RT tersebut sudah terdaftar."** Kode itu sudah dipakai. Pakai kode lain.
- **Tombol Hapus abu-abu dan tidak bisa ditekan.** RT masih punya data keluarga ("RT ini masih memiliki {n} data keluarga dan tidak dapat dihapus."). Pindahkan atau selesaikan datanya dulu. Jika dipaksa lewat cara lain, tampil: "RT {kode_rt} tidak dapat dihapus karena masih memiliki {n} data keluarga."
- **Formulir kembali terbuka dengan kotak merah.** Ada isian yang belum valid. Baca daftar kesalahan di atas formulir.

> 💡 **Tips:** Hati-hati mengubah **Kode RT**. Kode itu dipakai di banyak tempat (misalnya "RT 03" pada surat dan laporan).

---

## 6. Persetujuan Dokumen (mengesahkan surat)

Menu **Persetujuan** lalu **Persetujuan Dokumen** (judul halaman **Persetujuan Dokumen**, heading **Portal Persetujuan Dokumen**: "Permohonan dokumen tahap akhir menunggu pengesahan RW."). Di sini surat pengantar yang sudah disetujui Ketua RT diperiksa dan disahkan.

Daftarnya berupa kartu. Surat berstatus **Menunggu Pengesahan RW** (sudah disetujui RT) ada di atas. Surat berstatus **Menunggu RT** (belum diperiksa RT) ikut tampil di bawahnya hanya sebagai pantauan.

### 6.1 Memeriksa dan Mengesahkan Surat

> 👤 **SIMULASI C-01: Surat Keterangan Domisili (bagian 3 dari 3)**
>
> Pak **Budi Santoso** mengajukan Surat Keterangan Domisili untuk membuka rekening bank, dan Pak **Budi Sampurno** (Ketua RT 03) sudah menyetujuinya. Sekarang surat itu sampai ke RW. Bu Rina mendapat notifikasi **Surat Menunggu Penerbitan**.
>
> - **Masuk sebagai:** `adminrw21`
> - **Surat:** Surat Keterangan Domisili, pemohon Budi Santoso, RT 03
> - **Nomor urut yang dipakai:** 012 *(contoh)*
> - **Sebelumnya:** Warga mengajukan (lihat *01-Warga.md, C-01 bagian 1*), Ketua RT menyetujui (lihat *02-Ketua-RT.md, C-01 bagian 2*)
> - **Selanjutnya:** Pak Budi Santoso mengunduh PDF-nya.

#### Langkah-langkah

**1. Buka Persetujuan Dokumen.**
Klik **Persetujuan** lalu **Persetujuan Dokumen**, atau klik banner **Tinjau Dokumen Sekarang** di dashboard.

> 📸 **SCREENSHOT ARW-13: Portal Persetujuan Dokumen**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-dokumen
> - **Kondisi:** surat Budi Santoso berstatus **Menunggu Pengesahan RW** tampil di bagian atas
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Riwayat Pengajuan**, lencana status, dan tombol **Detail Permintaan**, **Tanda Tangan & Sahkan**, **Tolak**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-13-portal-persetujuan-dokumen.png`

**2. Periksa dulu dengan Detail Permintaan.**
Klik **Detail Permintaan**. Modal **Detail Permintaan Dokumen** memuat:

- Kartu pemohon dan **Tipe Surat**.
- **Data Lengkap Pemohon** (jenis kelamin, tempat dan tanggal lahir, agama, pendidikan terakhir, pekerjaan, status perkawinan, kewarganegaraan, alamat).
- **Lampiran Dokumen (KTP)** dengan tautan **Lihat Kartu Keluarga** dan **Lihat KTP** serta tombol **Lihat Dokumen**. Bila belum ada tertulis **Belum diunggah**.
- **Preview Surat**: tampilan surat seperti hasil cetak.

> 📸 **SCREENSHOT ARW-14: Detail Permintaan Dokumen**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-dokumen (modal **Detail Permintaan Dokumen**)
> - **Kondisi:** modal terbuka pada surat Budi Santoso
> - **Area:** modal, termasuk **Preview Surat**
> - **Sorot:** kotak merah pada **Data Lengkap Pemohon**, **Lampiran Dokumen (KTP)**, dan tombol **Sahkan Dokumen**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-14-detail-permintaan-dokumen.png`

**3. Buka formulir pengesahan.**
Klik **Sahkan Dokumen** di modal itu, atau **Tanda Tangan & Sahkan** pada kartu. Modal **Tanda Tangan & Sahkan** tampil: "Bubuhkan tanda tangan sebagai Ketua RW untuk mensahkan surat ...".

**4. Isi Nomor Urut Surat.**
Ketik bagian urut saja, misalnya *012* (placeholder "Contoh: 012", maksimal 30 karakter). Bagian lain nomor surat (SK, RT, RW, bulan, tahun) disusun otomatis.

**5. Bubuhkan tanda tangan.**
Pada **Tanda Tangan RW**, coretkan tanda tangan di kotak bertulisan "Coretan tanda tangan di sini" memakai mouse, jari, atau pena layar. Tombol **Ulangi** menghapus coretan. Jika akun Anda punya tanda tangan tersimpan, tersedia pilihan **TTD Tersimpan** dan **Gambar Manual**.

**6. Tambahkan stempel (opsional).**
Pada **Stempel RW (Opsional)** unggah gambar lewat **Unggah Stempel (PNG, Max 2MB)**. Tombol **Hapus Stempel** membatalkan pilihan.

**7. Cek Pratinjau Surat, lalu sahkan.**
Panel **Pratinjau Surat** langsung berubah mengikuti nomor, tanda tangan, dan stempel. Bila sudah benar, klik **Sahkan & Selesai**.

> 📸 **SCREENSHOT ARW-15: Modal Tanda Tangan & Sahkan**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-dokumen (modal **Tanda Tangan & Sahkan**)
> - **Kondisi:** **Nomor Urut Surat** berisi "012", tanda tangan sudah dicoret, pratinjau surat tampil
> - **Area:** modal penuh
> - **Sorot:** kotak merah pada **Nomor Urut Surat**, kotak tanda tangan, **Pratinjau Surat**, dan tombol **Sahkan & Selesai**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-15-modal-tanda-tangan-sahkan.png`

#### ✅ Hasilnya

- Muncul pesan **"Dokumen berhasil disahkan dan dikirim ke pemohon."**
- Status surat menjadi **Selesai**, tanggal selesai terisi, dan salinan data surat disimpan.
- Pak Budi Santoso menerima notifikasi **Surat Terbit**: "Surat ... Anda sudah terbit dan dapat diunduh." Ia dapat mengunduh PDF dari menu Permohonan Surat (lihat *01-Warga.md, C-01 bagian 3*).
- Kartu surat hilang dari Portal Persetujuan Dokumen dan pindah ke Riwayat Pengajuan.

> 📸 **SCREENSHOT ARW-16: Pesan surat berhasil disahkan**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-dokumen (sesaat setelah **Sahkan & Selesai**)
> - **Kondisi:** jendela **Berhasil!** berisi pesan pengesahan tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan "Dokumen berhasil disahkan dan dikirim ke pemohon."
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-16-surat-berhasil-disahkan.png`

#### ⚠️ Perhatian

- **Halaman kosong: "Belum Ada Pengajuan" / "Belum ada dokumen yang perlu disahkan saat ini."** Tidak ada surat yang menunggu. Pada data demo awal memang kosong; surat baru muncul setelah ada pengajuan.
- **Kartu bertombol "Menunggu Verifikasi RT".** Surat itu belum disetujui RT, jadi belum bisa Anda sahkan. Tombol pengesahan baru muncul setelah RT menyetujui.
- **Peringatan "Format harus PNG!" atau "Max 2MB!" saat unggah stempel.** Pakai file PNG dengan ukuran paling besar 2 MB.
- **Tanda tangan kosong.** Formulir tidak menolak tanda tangan kosong, dan surat tetap tersahkan tanpa tanda tangan. Pastikan sudah mencoret sebelum menekan **Sahkan & Selesai**.
- **Tidak ada kolom catatan pada modal pengesahan.** Catatan hanya ada pada penolakan (6.2).
- **Kesalahan data pemohon pada surat.** Jangan sahkan. Tolak dengan catatan (6.2) agar pemohon memperbaiki.

> 💡 **Tips:** Selalu lihat **Preview Surat** sebelum mengesahkan. Setelah status **Selesai**, surat tidak dapat dikembalikan dari layar ini.

> ➡️ **Cerita berlanjut:** Pak Budi Santoso mengunduh surat. Lihat *01-Warga.md, C-01 bagian 3*.

### 6.2 Menolak Surat dengan Catatan

> 👤 **SIMULASI C-02 (sisi RW): Surat ditolak di RW**
>
> Bayangkan Bu Rina menemukan alamat pada surat tidak sama dengan lampiran Kartu Keluarga. Ia menolak surat dan menulis alasan agar pemohon bisa memperbaiki. (Penolakan di tahap RT dibahas di *02-Ketua-RT.md, C-02*.)

#### Langkah-langkah

**1.** Pada kartu surat, klik **Tolak** (atau **Tolak & Beri Catatan** di dalam modal detail).
**2.** Modal **Tolak Pengajuan Surat** muncul dengan pesan "Mohon berikan alasan penolakan atau catatan koreksi agar pemohon dapat melakukan perbaikan pada pengajuan ini."
**3.** Isi **Alasan Penolakan / Catatan Koreksi** (wajib). Contoh: *Alamat pada surat tidak sesuai dengan Kartu Keluarga.*
**4.** Klik **Tolak & Kirim Catatan**.

> 📸 **SCREENSHOT ARW-17: Modal Tolak Pengajuan Surat**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-dokumen (modal **Tolak Pengajuan Surat**)
> - **Kondisi:** alasan penolakan sudah diketik
> - **Area:** modal
> - **Sorot:** kotak merah pada kolom alasan dan tombol **Tolak & Kirim Catatan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-17-modal-tolak-surat.png`

#### ✅ Hasilnya

- Muncul pesan **"Catatan penolakan telah dikirim."**
- Status surat menjadi **Ditolak RW**. Pemohon menerima notifikasi **Surat Ditolak RW**: "Pengajuan ... Anda ditolak di tingkat RW. Alasan: ..."
- Alasan dapat dibaca kembali lewat tombol **Lihat Alasan Penolakan RW** (kartu) atau riwayat.

#### ⚠️ Perhatian

- **Kolom alasan wajib dan maksimal 1000 karakter.** Jika kosong, browser menahan pengiriman.
- **Penolakan tidak bisa dibatalkan dari layar ini.** Pemohon harus mengajukan ulang (lihat *01-Warga.md, C-02*).

> 💡 **Tips:** Tulis alasan yang spesifik dan sopan. Contoh bagus: "Data KTP tidak sesuai dengan yang terdaftar, mohon unggah ulang."

### 6.3 Melihat Riwayat Pengajuan

#### Langkah-langkah

**1.** Pada Portal Persetujuan Dokumen, klik tombol **Riwayat Pengajuan** di kanan atas. (Riwayat tidak ada di menu samping; hanya dibuka lewat tombol ini.)
**2.** Halaman **Riwayat Pengajuan** menampilkan semua pengajuan berbagai status: "Daftar seluruh pengajuan surat beserta statusnya."
**3.** Cari lewat kotak "Cari nama, NIK, atau jenis surat...", dan saring dengan pilihan **Semua Status**, **Diajukan**, **Disetujui RT**, **Selesai**, **Ditolak RT**, atau **Ditolak RW**, lalu **Filter**. Tombol reset mengembalikan semua.
**4.** Kolom: **Tanggal**, **Pemohon** (nama dan NIK), **Jenis Surat**, **RT**, **Status**. Tombol **Kembali ke Persetujuan** membawa Anda kembali.

> 📸 **SCREENSHOT ARW-18: Riwayat Pengajuan Dokumen**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/riwayat-dokumen
> - **Kondisi:** data demo; sembilan contoh surat Budi Santoso berstatus **Selesai** tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pilihan **Semua Status** dan kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-18-riwayat-pengajuan.png`

#### ✅ Hasilnya

Anda bisa menelusuri surat yang pernah diproses, 15 baris per halaman. Pada data demo terlihat 9 contoh surat Pak Budi Santoso berstatus **Selesai**. Bila kosong tertulis "Belum ada pengajuan."

> 💡 **Tips:** Pakai filter **Selesai** untuk menjawab pertanyaan "surat si A sudah terbit belum?".

---

## 7. Persetujuan Konten (berita, agenda, pengumuman)

Menu **Persetujuan** lalu **Persetujuan Konten**. Sesuai aplikasi, **semua** berita, agenda, dan pengumuman, baik dari RW maupun dari RT mana pun, disetujui oleh **Admin RW**. Ketua RW dan Ketua RT tidak menyetujui konten.

Pengaturan bawaan data demo: **berita perlu persetujuan**, sedangkan **agenda dan pengumuman langsung terbit** saat operator mengajukan. (Admin Aplikasi dapat mengubahnya di Pengaturan Sistem; lihat `10-Admin-Aplikasi.md`.) Karena itu antrean Anda umumnya berisi berita.

Halaman ini berjudul **Persetujuan Konten** dengan keterangan "Meninjau pengajuan Berita, Agenda, & Pengumuman sebelum tayang. Anda menyetujui semua berita, agenda, dan pengumuman, baik dari RT maupun RW." Antrean memuat status **Ditinjau** dan **Revisi**.

### 7.1 Menyetujui Berita

> 👤 **SIMULASI C-07: Berita CCTV menunggu persetujuan**
>
> **Mbak Laras** *(fiktif, akun `opkontenrw21`)* menulis berita **"Rencana Pemasangan CCTV Lingkungan"** dan mengajukannya. Bu Rina menerima notifikasi **Berita Menunggu Persetujuan**. Ia memeriksa isinya sebelum tayang.
>
> - **Masuk sebagai:** `adminrw21`
> - **Berita:** Rencana Pemasangan CCTV Lingkungan (asal **RW**, kategori Keamanan, status **Ditinjau**)
> - **Sebelumnya:** Op Konten RW menulis dan mengajukan (lihat *07-Op-Konten-RW.md, C-07*)
> - **Selanjutnya:** Berita tampil di beranda dan Pusat Informasi.

#### Langkah-langkah

**1. Buka Persetujuan Konten.**
Klik **Persetujuan Konten**. Kartu-kartu konten tampil dengan lencana status, asal (**RW** atau **RT 03**), tanggal, dan pratinjau isi.

**2. Saring bila perlu.**
Tab: **Lihat Semua**, **Berita Komunitas**, **Agenda Kegiatan**, **Pengumuman**. Kotak **Cari...** mencocokkan judul dan nama operator. Pilihan filter status: **Semua Status**, **Ditinjau**, **Revisi**.

> 📸 **SCREENSHOT ARW-19: Antrean Persetujuan Konten**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-konten
> - **Kondisi:** data demo; tampil berita "Rencana Pemasangan CCTV Lingkungan" (Ditinjau), agenda "Syukuran Pos Ronda Baru RT 03" (Ditinjau), dan berita "Perbaikan Saluran Air RT 03" (Revisi)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tab, filter status, dan kartu berita CCTV
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-19-antrean-persetujuan-konten.png`

**3. Buka kartu berita CCTV.**
Klik kartunya. Halaman detail menampilkan breadcrumb **Persetujuan Konten**, ID (contoh `BERITA-2026-0001`), kategori, status, penulis, tanggal, dan isi lengkap. Baca isinya dengan teliti.

> 📸 **SCREENSHOT ARW-20: Detail berita menunggu persetujuan**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-konten (detail berita CCTV)
> - **Kondisi:** detail terbuka, kotak **Catatan Revisi** dan dua tombol tampil di bawah
> - **Area:** satu layar penuh, gulir sampai tombol
> - **Sorot:** kotak merah pada isi berita, kotak **Catatan Revisi**, dan tombol **Tolak & Revisi** dan **Setujui Pengajuan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-20-detail-berita-ditinjau.png`

**4. Klik Setujui Pengajuan.**
Dialog **Setujui Pengajuan?** muncul: "Anda akan menyetujui dan mempublikasikan **Rencana Pemasangan CCTV Lingkungan**." Klik **Ya, Setujui** (atau **Batal**).

> 📸 **SCREENSHOT ARW-21: Dialog Setujui Pengajuan**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-konten
> - **Kondisi:** dialog konfirmasi tampil
> - **Area:** dialog di tengah layar
> - **Sorot:** kotak merah pada tombol **Ya, Setujui**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-21-dialog-setujui-konten.png`

#### ✅ Hasilnya

- Tampil **Berhasil!** dengan pesan **"Berita berhasil disetujui!"**, lalu halaman memuat ulang.
- Status berita menjadi **Terbit**, tanggal terbit terisi, dan berita tampil di beranda dan Pusat Informasi.
- Berita **tidak** mengirim notifikasi ke warga (kebijakan aplikasi). Warga melihatnya di halaman beranda. Untuk **agenda** dan **pengumuman** yang disetujui, warga menerima notifikasi **Agenda Baru** atau **Pengumuman Baru**.

#### ⚠️ Perhatian

- **Tombol Setujui dan Tolak tidak muncul.** Tombol hanya ada pada konten berstatus **Ditinjau**. Konten **Revisi** menunggu operator memperbaikinya.
- **Gagal dengan pesan "Status tidak valid".** Konten itu sudah diproses (misalnya oleh orang lain). Muat ulang halaman.
- **Agenda Syukuran Pos Ronda Baru RT 03 ikut di antrean.** Itu hanya karena data demo sengaja dibuat berstatus Ditinjau. Pada pengaturan bawaan, agenda yang diajukan operator langsung terbit.

> 💡 **Tips:** Perhatikan lencana asal (**RW** atau **RT 03**). Konten RT hanya tampil ke warga RT itu, konten RW tampil ke semua warga.

> ➡️ **Cerita berlanjut:** Warga membaca berita di beranda (lihat *01-Warga.md, C-07*).

### 7.2 Mengembalikan Konten untuk Revisi

> 👤 **SIMULASI C-08 (sisi RW): Catatan revisi**
>
> Misalkan Bu Rina menemukan berita belum lengkap. Ia mengembalikannya ke operator dengan catatan. (Pada data demo, berita "Perbaikan Saluran Air RT 03" memang sudah berstatus **Revisi** dengan catatan "Mohon lengkapi waktu dan lokasi kegiatan." Cerita C-08 dari sisi operator ada di *03-Op-Konten-RT.md*.)

#### Langkah-langkah

**1.** Buka detail konten berstatus **Ditinjau**.
**2.** Di kotak **Catatan Revisi** ("Pilih atau ketik alasan penolakan jika Anda meminta revisi untuk pengajuan ini."), ketik pada kolom "Ketik catatan perbaikan secara detail...". Anda bisa mengklik catatan siap pakai yang menambah teks otomatis, misalnya untuk berita: **Perbaiki tata bahasa**, **Konten kurang relevan**, **Ganti gambar resolusi tinggi**, **Sumber tidak valid**. Untuk agenda: **Waktu tidak sesuai**, **Lokasi belum jelas**, **Deskripsi kurang lengkap**, **Perbaiki poster/flyer**. Untuk pengumuman: **Informasi kurang jelas**, **Kurangi teks yang terlalu panjang**, **Bukan prioritas saat ini**.
**3.** Klik **Tolak & Revisi**. Dialog **Tolak & Revisi?** muncul: "Pengajuan ini akan dikembalikan ke operator dengan catatan revisi Anda." Klik **Ya, Tolak**.

> 📸 **SCREENSHOT ARW-22: Mengisi Catatan Revisi**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-konten (detail konten berstatus Ditinjau)
> - **Kondisi:** catatan sudah diketik dan satu catatan siap pakai sudah diklik
> - **Area:** kotak **Catatan Revisi** dan tombol di bawahnya
> - **Sorot:** kotak merah pada kolom catatan dan tombol **Tolak & Revisi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-22-catatan-revisi.png`

#### ✅ Hasilnya

- Pesan **"Berita ditolak dan dikembalikan untuk revisi."** (atau Agenda, Pengumuman).
- Status menjadi **Revisi**. Operator pembuat menerima notifikasi **Berita Perlu Revisi** yang memuat catatan Anda.

#### ⚠️ Perhatian

- **Kolom catatan menjadi merah dan dialog tidak muncul**, dengan pesan "Catatan perbaikan wajib diisi jika Anda menolak." Isi dulu catatannya.
- **Tidak ada tombol di konten berstatus Revisi.** Itu wajar. Yang tampil adalah kotak merah **Catatan Revisi Terakhir** berisi catatan Anda.

> 💡 **Tips:** Gabungkan catatan siap pakai dengan kalimat sendiri, supaya operator tahu bagian mana yang harus diperbaiki.

### 7.3 Menyetujui Setelah Revisi

> 👤 **SIMULASI C-08: Berita RT 03 setelah diperbaiki**
>
> **Mbak Nisa** *(fiktif, akun `opkonten_3`)* memperbaiki berita **"Perbaikan Saluran Air RT 03"** sesuai catatan, lalu mengajukannya lagi. Penyetujunya tetap Admin RW (bukan Ketua RT), jadi Bu Rina yang memeriksa ulang.
>
> - **Masuk sebagai:** `adminrw21`
> - **Berita:** Perbaikan Saluran Air RT 03 (asal **RT 03**)
> - **Sebelumnya:** Op Konten RT 03 merevisi dan mengajukan ulang (lihat *03-Op-Konten-RT.md, C-08*)

#### Langkah-langkah

**1.** Sebelum diajukan ulang, berita tampil di antrean dengan status **Revisi**. Pilih filter **Revisi** untuk melihatnya, dan buka detailnya untuk membaca **Catatan Revisi Terakhir**.
**2.** Setelah operator mengajukan ulang, notifikasi **Berita Menunggu Persetujuan** masuk dan statusnya kembali **Ditinjau**.
**3.** Buka detail, periksa bahwa waktu dan lokasi sudah dilengkapi, lalu klik **Setujui Pengajuan** dan **Ya, Setujui**.

> 📸 **SCREENSHOT ARW-23: Filter Revisi dan catatan revisi terakhir**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-konten
> - **Kondisi:** filter status **Revisi** dipilih, detail berita "Perbaikan Saluran Air RT 03" terbuka
> - **Area:** daftar dan kotak merah **Catatan Revisi Terakhir**
> - **Sorot:** kotak merah pada filter dan catatan "Mohon lengkapi waktu dan lokasi kegiatan."
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-23-filter-revisi.png`

#### ✅ Hasilnya

Pesan **"Berita berhasil disetujui!"**. Berita terbit dan hanya tampil ke warga RT 03.

#### ⚠️ Perhatian

- **Berita belum muncul di antrean setelah operator bilang sudah mengajukan.** Ia mungkin hanya menyimpan sebagai draf. Draf tidak tampil untuk Anda.

> ➡️ **Cerita berlanjut:** Warga RT 03 membaca beritanya di Pusat Informasi (lihat *01-Warga.md*).

---

## 8. Persetujuan UMKM

Menu **Persetujuan** lalu **Persetujuan UMKM** (judul **Persetujuan UMKM**): "Tinjau pendaftaran usaha baru dan arsip pengajuan yang ditolak." Ada dua tab:

- **Persetujuan UMKM** (usaha berstatus Pending, ditandai angka merah jumlah antrean).
- **Pengajuan Ditolak** (arsip usaha berstatus Rejected, dengan angka jumlahnya).

Satu halaman memuat 4 kartu. Di panel samping ada **Proses Persetujuan UMKM** dan kotak **Alur Persetujuan**: usaha yang disetujui tampil di **Pojok UMKM** dan pemilik dapat mengelola produknya sendiri.

### 8.1 Menyetujui Pendaftaran Usaha

> 👤 **SIMULASI C-05: Jasa Service Elektronik Pak Budi**
>
> Pak **Budi Sampurno** (akun `budisampurno`) mendaftarkan usaha **"Jasa Service Elektronik Pak Budi"** (kategori Jasa): perbaikan AC, mesin cuci, kulkas, dan peralatan elektronik rumah tangga. Statusnya **Pending**. Bu Rina menerima notifikasi **UMKM Baru Menunggu Verifikasi**.
>
> - **Masuk sebagai:** `adminrw21`
> - **Usaha:** Jasa Service Elektronik Pak Budi, alamat Gg. Kenanga No. 45, RT 03 / RW 21
> - **Sebelumnya:** Warga mendaftarkan usaha (lihat *01-Warga.md, C-05*)
> - **Selanjutnya:** Usaha tampil di Pojok UMKM dan pemilik menerima notifikasi.

#### Langkah-langkah

**1. Buka Persetujuan UMKM.**
Klik **Persetujuan UMKM** (atau **Tinjau UMKM Sekarang** di dashboard).

> 📸 **SCREENSHOT ARW-24: Antrean Persetujuan UMKM**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-umkm
> - **Kondisi:** data demo; kartu "Jasa Service Elektronik Pak Budi" berlencana **Pending Review**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada dua tab, angka merah antrean, dan tombol **Reject** dan **Approve Profile** pada kartu
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-24-antrean-persetujuan-umkm.png`

**2. Lihat rincian pengaju.**
Klik kartu usaha. Modal **Detail Pengajuan UMKM** ("Rincian data pengaju dan profil bisnis warga") menampilkan **Status Pengajuan** dan **Informasi Pemohon**: **NIK**, **Nama Lengkap**, **Nama Usaha**, **Kategori Usaha**, **Deskripsi Usaha**, **Alamat & Lokasi**, **No. WhatsApp**, **Status Approval**. Di bagian bawah ada waktu pengajuan.

> 📸 **SCREENSHOT ARW-25: Detail Pengajuan UMKM**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-umkm (modal **Detail Pengajuan UMKM**)
> - **Kondisi:** modal terbuka pada "Jasa Service Elektronik Pak Budi"
> - **Area:** modal penuh
> - **Sorot:** kotak merah pada **Informasi Pemohon** dan tombol **Tolak** dan **Setujui Profil**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-25-detail-pengajuan-umkm.png`

**3. Setujui.**
Klik **Setujui Profil** (di modal) atau **Approve Profile** (di kartu).

#### ✅ Hasilnya

- Pesan **"Profil UMKM Jasa Service Elektronik Pak Budi berhasil disetujui."**
- Status usaha menjadi **Approved** dan aktif. Usaha pindah dari antrean ke **Pojok UMKM**.
- Pak Budi Sampurno menerima notifikasi **Pendaftaran UMKM Disetujui**: "Selamat! Pendaftaran usaha '...' telah disetujui oleh RW."

#### ⚠️ Perhatian

- **"Hanya pengajuan UMKM dengan status Pending yang dapat disetujui."** Usaha itu sudah diproses. Muat ulang halaman.
- **Klik dua kali tidak membuat dobel.** Jika sudah disetujui, tampil "Profil UMKM ... sudah disetujui."
- **403 "Verifikasi UMKM hanya dapat dilakukan oleh Admin RW."** Anda masuk dengan peran lain.

> 💡 **Tips:** Cek **Kategori Usaha** dan deskripsi. Pedoman di layar: pastikan nama dan kategori usaha sesuai pedoman komunitas.

> ➡️ **Cerita berlanjut:** Usaha muncul di Pojok UMKM warga (lihat *01-Warga.md, C-05*).

### 8.2 Menolak Pendaftaran Usaha

> 👤 **SIMULASI C-06 (bagian 1): Koperasi Warga Tanimulya**
>
> Pengajuan **"Koperasi Warga Tanimulya"** (pemilik Pak **Budi Santoso**, kategori Koperasi) belum lengkap. Bu Rina menolaknya dengan catatan. Pada data demo, usaha ini sudah berstatus **Rejected** dengan catatan *"Lengkapi deskripsi pengurus koperasi dan foto tempat usaha, lalu ajukan ulang."* Langkah di bawah menunjukkan cara membuatnya.

#### Langkah-langkah

**1.** Pada kartu usaha Pending, klik **Reject** (atau buka rincian lalu **Tolak**).
**2.** Modal **Tolak Pendaftaran UMKM** muncul (nama usaha dan pemilik tertulis di bawah judul). Pesan: pengajuan yang ditolak diarsipkan ke tab **Pengajuan Ditolak** dan catatan ditampilkan kepada warga pemohon dan pengurus RW.
**3.** Isi **Alasan Penolakan / Catatan Verifikasi** (maksimal 500 karakter). Contoh: *Lengkapi deskripsi pengurus koperasi dan foto tempat usaha, lalu ajukan ulang.*
**4.** Klik **Tolak Pengajuan**.

> 📸 **SCREENSHOT ARW-26: Modal Tolak Pendaftaran UMKM**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-umkm (modal **Tolak Pendaftaran UMKM**)
> - **Kondisi:** catatan penolakan sudah diketik
> - **Area:** modal
> - **Sorot:** kotak merah pada kolom alasan dan tombol **Tolak Pengajuan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-26-modal-tolak-umkm.png`

#### ✅ Hasilnya

- Pesan **"Profil UMKM ... telah ditolak."** Usaha pindah ke tab **Pengajuan Ditolak**.
- Pemilik menerima notifikasi **Pendaftaran UMKM Ditolak**: "... ditolak oleh RW. Alasan: ... Perbaiki data lalu ajukan ulang."

#### ⚠️ Perhatian

- **Kolom alasan dikosongkan.** Server tetap menolak, dan memakai teks bawaan "Pendaftaran UMKM belum memenuhi persyaratan RW." Warga tidak tahu bagian mana yang salah. Selalu isi alasan yang jelas.
- **"Hanya pengajuan UMKM dengan status Pending yang dapat diproses."** Sudah diproses sebelumnya.

> 💡 **Tips:** Sebutkan dengan tepat apa yang harus dilengkapi, supaya pengajuan ulang langsung tepat.

### 8.3 Pengajuan Ulang

> 👤 **SIMULASI C-06 (bagian 2): Koperasi diajukan ulang**
>
> Pak **Budi Santoso** melengkapi deskripsi pengurus dan foto tempat usaha, lalu menekan ajukan ulang dari menu kelola usahanya. Bu Rina menerima notifikasi **UMKM Diajukan Ulang**: "Usaha "Koperasi Warga Tanimulya" telah diperbaiki dan diajukan ulang, menunggu verifikasi RW."
>
> - **Masuk sebagai:** `adminrw21`
> - **Sebelumnya:** lihat *01-Warga.md, C-06*

#### Langkah-langkah

**1.** Buka **Persetujuan UMKM**. Kartu Koperasi kini ada di tab **Persetujuan UMKM** berstatus **Pending Review** (pengajuan ulang kembali ke tab ini).
**2.** Buka rincian, periksa bahwa deskripsi pengurus dan foto sudah dilengkapi, lalu klik **Setujui Profil**.

#### ✅ Hasilnya

Pesan **"Profil UMKM Koperasi Warga Tanimulya berhasil disetujui."** Usaha tampil di Pojok UMKM, dan catatan penolakan lama hilang dari tab **Pengajuan Ditolak**.

#### ⚠️ Perhatian

- **Koperasi belum ada di antrean.** Pemilik belum menekan ajukan ulang. Selama masih berstatus Rejected, usaha ada di tab **Pengajuan Ditolak**.

### 8.4 Melihat Arsip Pengajuan Ditolak

Klik tab **Pengajuan Ditolak**. Banner **Daftar Pengajuan UMKM yang Ditolak** menjelaskan bahwa pemilik dapat memperbaiki data lalu mengajukan ulang. Kartu menampilkan "Alasan: ..." (dipotong 50 karakter) dan tautan **Lihat Rincian Penolakan** yang membuka rincian dengan **Alasan Penolakan** lengkap. Kalau kosong: **Tidak Ada Pengajuan Ditolak**.

> 📸 **SCREENSHOT ARW-27: Tab Pengajuan Ditolak**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/persetujuan-umkm?tab=ditolak
> - **Kondisi:** data demo; kartu "Koperasi Warga Tanimulya" tampil dengan alasan penolakan
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tab **Pengajuan Ditolak** dan tautan **Lihat Rincian Penolakan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-27-pengajuan-ditolak.png`

---

## 9. Pojok UMKM

Menu **Pojok UMKM** (judul **Pojok UMKM**: "Daftar usaha warga yang sudah disetujui RW, beserta pengelolaan status tampilnya.") berisi usaha berstatus **Approved**. Dari sini Admin RW dapat menonaktifkan atau mengaktifkan kembali usaha. Satu halaman memuat 6 kartu.

### 9.1 Melihat Usaha dan Produknya

#### Langkah-langkah

**1.** Klik **Pojok UMKM**. Pada data demo tampil tiga usaha: Kriya Kayu Jati Tanimulya, Warung Nasi Ibu Ani, dan Fashion Muslimah Aini (ditambah usaha yang baru Anda setujui).
**2.** Cari lewat kotak "Cari nama usaha, pemilik, atau kategori UMKM..." lalu **Cari**. Saring dengan pilihan **Semua Kategori** (Kuliner, Fashion, Jasa, Kerajinan, Koperasi) dan **Semua Status** (**Aktif**, **Tidak Aktif**, **Stok Tersedia**, **Stok Menipis**, **Stok Habis**). Pilihan langsung berlaku.
**3.** Kartu memuat lencana kategori, lencana **Aktif** atau **Non-Aktif**, nama usaha, pemilik dan RT, deskripsi, alamat, dan jumlah produk ("N Produk"). Klik **Detail & Produk**.
**4.** Halaman **Detail UMKM** menampilkan lencana **Mode Peninjauan RW**, empat kartu (**Produk Aktif**, **Produk Non-Aktif**, **Kategori Produk**, **Stok Menipis**), serta **Katalog Produk & Etalase Usaha** dengan pencarian dan filter produk. Tombol **Hubungi Pemilik Usaha** membuka WhatsApp. Tombol **Kembali ke Pojok UMKM** kembali ke daftar.

> 📸 **SCREENSHOT ARW-28: Pojok UMKM (RW)**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/umkm
> - **Kondisi:** data demo
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Persetujuan UMKM**, filter, dan tombol **Nonaktifkan** pada kartu
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-28-pojok-umkm-rw.png`

> 📸 **SCREENSHOT ARW-30: Detail UMKM**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/umkm/usaha/{id} (dari tombol **Detail & Produk** pada Warung Nasi Ibu Ani)
> - **Kondisi:** halaman detail terbuka
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada lencana **Mode Peninjauan RW** dan kartu statistik
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-30-detail-umkm.png`

### 9.2 Menonaktifkan dan Mengaktifkan Usaha

> 👤 **SIMULASI: Usaha dinonaktifkan sementara**
>
> Bayangkan ada laporan bahwa informasi sebuah usaha tidak sesuai. Bu Rina menonaktifkannya sementara dengan alasan jelas. Produk tidak dihapus.
>
> - **Masuk sebagai:** `adminrw21`
> - **Usaha:** contoh, Fashion Muslimah Aini *(hanya contoh; data demo bawaan semua aktif)*

#### Langkah-langkah

**1.** Pada kartu usaha aktif, klik **Nonaktifkan**. Modal **Nonaktifkan Usaha** muncul: "Usaha ... tidak akan tampil di Pojok UMKM sampai diaktifkan kembali. Produk tetap tersimpan dan pemilik menerima notifikasi beserta alasannya."
**2.** Isi **Alasan penonaktifan** (wajib, maksimal 500 karakter). Contoh: *Informasi usaha tidak sesuai atau melanggar ketentuan RW.*
**3.** Klik **Nonaktifkan**.
**4.** Untuk mengaktifkan kembali, klik **Aktifkan** pada kartu usaha berlabel **Non-Aktif**, lalu **Ya, aktifkan**.

> 📸 **SCREENSHOT ARW-29: Modal Nonaktifkan Usaha**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/umkm (modal **Nonaktifkan Usaha**)
> - **Kondisi:** alasan sudah diketik
> - **Area:** modal
> - **Sorot:** kotak merah pada kolom **Alasan penonaktifan** dan tombol **Nonaktifkan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-29-modal-nonaktifkan-usaha.png`

#### ✅ Hasilnya

- Nonaktif: pesan **"Usaha ... telah dinonaktifkan dan tidak tampil di Pojok UMKM."** Pemilik menerima notifikasi **Usaha Dinonaktifkan** berikut alasannya. Di halaman detail tampil "Usaha ini sedang dinonaktifkan dan tidak tampil di Pojok UMKM." beserta alasan.
- Aktif kembali: pesan **"Usaha ... telah diaktifkan kembali."**, catatan alasan dihapus, dan pemilik menerima **Usaha Diaktifkan Kembali**.

#### ⚠️ Perhatian

- **"Alasan penonaktifan wajib diisi."** Isi alasan.
- **"Hanya usaha yang sudah disetujui dan sedang aktif yang dapat dinonaktifkan."** Usaha sudah nonaktif atau bukan Approved.

> 💡 **Tips:** Untuk usaha baru yang ingin Anda tolak, pakai Persetujuan UMKM (bab 8), bukan Nonaktifkan.

---

## 10. Pengajuan Data (perubahan data warga)

Menu **Persetujuan** lalu **Pengajuan Data** (judul tab **Persetujuan Pengajuan Data**, heading **Verifikasi Pengajuan Data**: "Daftar pengajuan penambahan/perubahan data anggota keluarga."). Hanya pengajuan yang sudah disetujui Ketua RT dan berstatus menunggu RW yang tampil. Persetujuan Anda bersifat final dan langsung memperbarui data penduduk.

Jenis pengajuan di kolom **JENIS PENGAJUAN**: **Tambah Anggota Baru**, **Edit Data Anggota**, **Perubahan Data KK**. (Jenis **Pindah Pemegang Akun** selesai di tahap RT dan tidak datang ke RW.)

> 👤 **SIMULASI C-10 (tahap RW): Perubahan data anggota keluarga**
>
> Pak **Budi Santoso** mengajukan perubahan data anggota keluarganya lewat Profil Keluarga. Pak **Budi Sampurno** (Ketua RT 03) sudah menyetujuinya. Bu Rina menerima notifikasi **Pengajuan Perubahan Data**: "Ada pengajuan perubahan data warga yang menunggu persetujuan RW."
>
> - **Masuk sebagai:** `adminrw21`
> - **Sebelumnya:** lihat *01-Warga.md* dan *02-Ketua-RT.md, C-10*
> - **Catatan:** data demo bawaan belum memuat pengajuan seperti ini, jadi daftar kosong sampai ada yang mengajukan.

#### Langkah-langkah

**1.** Klik **Pengajuan Data** (atau lencana **PERLU PERSETUJUAN** di dashboard). Kolom: **WAKTU PENGAJUAN**, **KEPALA KELUARGA** (nama dan "KK: nomor"), **JENIS PENGAJUAN**, **DETAIL AJUAN** (nama dan NIK), **AKSI**.

> 📸 **SCREENSHOT ARW-31: Verifikasi Pengajuan Data**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/mutasi-warga
> - **Kondisi:** minimal satu pengajuan menunggu RW
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kolom **JENIS PENGAJUAN** dan tombol **Detail**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-31-verifikasi-pengajuan-data.png`

**2.** Klik **Detail**. Modal **Detail Pengajuan Data** memuat:

- **Perubahan Data**: tabel **Field**, **Nilai Lama**, **Nilai Baru** (baris yang berubah diwarnai kuning). Untuk **Tambah Anggota Baru** hanya kolom **Nilai Baru** ("Data anggota baru").
- **Riwayat Proses**: **Diajukan oleh warga**, **Persetujuan Ketua RT**, **Persetujuan RW**, lalu **Ditolak** atau **Selesai**, lengkap dengan waktu dan "Oleh: ...".
- **File Bukti (Scan KK)** dengan tombol **Lihat Dokumen** bila ada.

> 📸 **SCREENSHOT ARW-32: Detail Pengajuan Data**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/mutasi-warga (modal **Detail Pengajuan Data**)
> - **Kondisi:** modal terbuka pada satu pengajuan jenis **Edit Data Anggota**
> - **Area:** modal penuh
> - **Sorot:** kotak merah pada baris berwarna kuning, **Riwayat Proses**, dan tombol **Setujui Final (Simpan ke Data Penduduk)**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-32-detail-pengajuan-data.png`

**3.** Setujui atau tolak.

- Setujui: klik **Setujui Final (Simpan ke Data Penduduk)**.
- Tolak: klik **Tolak**, isi **Alasan penolakan** (placeholder "Tuliskan alasan penolakan agar warga dapat memperbaiki pengajuan."), lalu **Tolak Pengajuan**.

> 📸 **SCREENSHOT ARW-33: Modal Tolak Pengajuan**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/mutasi-warga (modal **Tolak Pengajuan**)
> - **Kondisi:** alasan sudah diketik
> - **Area:** modal
> - **Sorot:** kotak merah pada kolom alasan dan tombol **Tolak Pengajuan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-33-modal-tolak-pengajuan-data.png`

#### ✅ Hasilnya

- Disetujui: pesan **"Pengajuan data disetujui dan data penduduk telah diperbarui."** Data penduduk atau KK berubah. Pengaju menerima **Perubahan Data Disetujui**: "Pengajuan perubahan data Anda disetujui di tingkat RW."
- Ditolak: pesan **"Pengajuan data ditolak."** Pengaju menerima **Perubahan Data Ditolak** beserta alasan.
- Daftar kosong menampilkan: "Belum ada pengajuan mutasi baru".

#### ⚠️ Perhatian

- **"NIK sudah terdaftar pada data penduduk."** (jenis Tambah) atau **"NIK sudah terdaftar pada data penduduk lain."** (jenis Edit). NIK bentrok dengan data yang ada. Tolak dan minta warga memeriksa NIK. Pesan ini bisa tidak terlihat di halaman, jadi bila persetujuan tampak tidak berhasil, periksa apakah NIK-nya bentrok.
- **"Status pengajuan tidak valid untuk diproses oleh RW."** Pengajuan sudah diproses atau belum disetujui RT.
- **Perubahan Data KK tidak mengubah Nomor KK, NIK kepala keluarga, dan RT.** Yang berubah hanya alamat, kontak, dan data wilayah.

> 💡 **Tips:** Bandingkan **Nilai Lama** dan **Nilai Baru** dengan teliti, lalu cek **File Bukti (Scan KK)** sebelum menyetujui.

> ➡️ **Cerita berlanjut:** Pengaju melihat hasilnya di Profil Keluarga (lihat *01-Warga.md, C-10*).

---

## 11. Pusat Informasi

Menu **Pusat Informasi** berisi seluruh konten (RW dan semua RT) yang sudah **terbit**: "Seluruh konten (RW dan semua RT) yang sudah terbit dan tampil bagi warga." Halaman ini hanya untuk dilihat; tidak ada tombol persetujuan.

#### Langkah-langkah

**1.** Klik **Pusat Informasi**.
**2.** Pilih tab **Lihat Semua**, **Berita Komunitas**, **Agenda Kegiatan**, atau **Pengumuman**, atau cari lewat kotak **Cari...**.
**3.** Klik sebuah kartu untuk membaca detailnya. Agenda menampilkan **Konfirmasi Kehadiran**, jumlah **Warga Hadir**, dan **Titik Lokasi Peta**.

> 📸 **SCREENSHOT ARW-34: Pusat Informasi**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/pusat-informasi
> - **Kondisi:** data demo; konten terbit tampil dengan lencana **Terbit**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tab dan lencana asal (**RW**, **RT 01**, **RT 03**)
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-34-pusat-informasi.png`

#### ✅ Hasilnya

Anda melihat apa yang sedang dilihat warga. Setiap halaman memuat 10 konten.

#### ⚠️ Perhatian

- **Konten yang baru Anda ajukan revisi tidak ada di sini.** Pusat Informasi hanya memuat yang sudah **Terbit**. Konten yang belum terbit ada di Persetujuan Konten.

> 💡 **Tips:** Dari halaman ini Anda bisa mengecek apakah berita yang baru disetujui sudah tayang.

---

## 12. Rekap Iuran RT

Menu **Rekap Iuran RT** (judul **Rekap Iuran RT**): "Iuran warga tercatat di kas RT. Kas RW bertambah saat setoran RT dikonfirmasi." Admin RW hanya melihat. Konfirmasi setoran dilakukan Operator Keuangan RW (lihat `08-Op-Keuangan-RW.md`).

> 👤 **SIMULASI: Mengecek setoran RT**
>
> Bu Rina menyiapkan bahan rapat dan ingin tahu RT mana yang sudah menyetor kewajibannya ke RW.
>
> - **Masuk sebagai:** `adminrw21`
> - **Yang dilihat:** bulan dan tahun berjalan

#### Langkah-langkah

**1.** Klik **Rekap Iuran RT**.
**2.** Pilih **Bulan** dan **Tahun**. Pilihan langsung berlaku.
**3.** Baca empat kartu: **Iuran terkumpul (semua RT)**, **Kewajiban setoran**, **Setoran diterima**, dan **Sisa s.d. {bulan}**.
**4.** Tab **Rekap per RT**: kolom **RT**, **KK lunas** (bentuk "x / total"), **Terkumpul**, **Setoran/KK**, **Kewajiban**, **Disetor**, **Menunggu**, **Status**, dan **Sisa s.d. {bulan}**. Status: **Lunas**, **Sebagian**, **Menunggu konfirmasi**, **Belum setor**, **Tidak ada kewajiban**.
**5.** Tab **Riwayat** memuat setoran berstatus **Dikonfirmasi**, **Ditolak**, atau **Dibatalkan**, dengan tautan **Lihat bukti**. Tab **Iuran Lainnya** tampil bila ada tagihan RW yang dikumpulkan lewat RT.

> 📸 **SCREENSHOT ARW-35: Rekap Iuran RT**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /rw/rekap-iuran
> - **Kondisi:** data demo; tab **Rekap per RT** terbuka
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pilihan **Bulan** dan **Tahun**, empat kartu, dan kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-35-rekap-iuran-rt.png`

#### ✅ Hasilnya

Anda tahu RT mana yang lunas, sebagian, atau belum menyetor.

#### ⚠️ Perhatian

- **Banner kuning: "Nominal setoran per KK untuk {tahun} belum ditetapkan, sehingga kewajiban setoran bernilai nol."** Nominal ditetapkan Operator Keuangan RW. Hubungi beliau.
- **Tab Riwayat: "Belum ada riwayat setoran pada tahun {tahun}."** Belum ada setoran yang tercatat. Pada data demo awal memang belum ada.
- **Tidak ada tombol Konfirmasi atau Nominal Setoran.** Itu wewenang Operator Keuangan RW.

> 💡 **Tips:** Dari dashboard, tautan **Rekap iuran per RT** di panel **Kas RW** membuka halaman ini.

---

## 13. Laporan Keuangan

Menu **Laporan Keuangan** (judul **Transparansi Keuangan**: "Laporan keuangan bulanan RT, RW, dan DKM yang telah dipublikasikan.") Admin RW melihat dua tab: **Daftar Laporan** dan **Statistik**.

> 👤 **SIMULASI: Mengambil laporan kas RW**
>
> Bu Rina membutuhkan laporan kas RW bulan lalu untuk arsip sekretariat.
>
> - **Masuk sebagai:** `adminrw21`
> - **Laporan:** Laporan Keuangan RW (data demo memuat tiga periode terakhir untuk RW, DKM, dan RT 01)

#### Langkah-langkah

**1.** Klik **Laporan Keuangan**. Tab **Daftar Laporan** menampilkan laporan yang sudah disetujui dan diterbitkan, 12 per halaman.
**2.** Saring dengan **Tahun**, **Bulan**, dan **Unit** (**Semua Unit (RT, RW, DKM)**, **Hanya RT**, **Hanya RW**, **Hanya DKM**), lalu **Filter**.
**3.** Tabel memuat **Periode**, **Unit**, **Pemasukan**, **Pengeluaran**, **Saldo Akhir**, **Aksi**. Klik ikon **Unduh PDF** untuk membuka PDF di tab baru.

> 📸 **SCREENSHOT ARW-36: Daftar Laporan Keuangan**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /laporan-keuangan
> - **Kondisi:** data demo; laporan RW, DKM, dan RT 01 tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada filter **Unit** dan ikon **Unduh PDF**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-36-daftar-laporan-keuangan.png`

**4.** Klik tab **Statistik** untuk melihat gambaran keuangan: pemilihan unit (RT, RW, DKM) dan tahun, penilaian level (**Kondisi baik**, **Perlu diperhatikan**, **Perlu tindakan**, **Belum ada data**), daftar **Perlu perhatian**, grafik **Tren Kas {tahun}**, **Sumber pemasukan**, **Penggunaan dana**, serta **Iuran wajib dan setoran ke RW** dengan kepatuhan KK (**Tepat waktu**, **Menunggak 1 bulan**, **Menunggak 2 bulan atau lebih**).

> 📸 **SCREENSHOT ARW-37: Tab Statistik Laporan Keuangan**
> - **Akun:** adminrw21 (Admin RW)
> - **URL:** /laporan-keuangan?tab=statistik
> - **Kondisi:** unit RW dipilih, tahun berjalan
> - **Area:** satu layar penuh, boleh digabung dua tangkapan
> - **Sorot:** kotak merah pada penilaian level dan grafik **Tren Kas**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `ARW-37-statistik-laporan-keuangan.png`

#### ✅ Hasilnya

PDF laporan terunduh atau terbuka dengan nama `Laporan_Keuangan_{unit}_{tahun}_{bulan}.pdf`.

#### ⚠️ Perhatian

- **"Belum ada laporan yang dipublikasikan."** Bendahara belum menerbitkan laporan.
- **Tidak ada tombol Excel di layar.** Hanya PDF yang tersedia di daftar.
- **Admin RW tidak menyetujui laporan keuangan.** Menu **Persetujuan Keuangan** disembunyikan dari aplikasi.

> 💡 **Tips:** Tab **Statistik** paling berguna untuk rapat, karena menjelaskan kondisi kas tanpa harus membuka satu per satu laporan.

---

## 14. Cerita Lengkap (Lintas Peran)

| Kode | Cerita | Peran Admin RW | Sub-bab |
|---|---|---|---|
| C-01 | Surat Keterangan Domisili Pak Budi Santoso | Mengesahkan surat, lalu warga mengunduh PDF | 6.1 |
| C-02 | Surat ditolak karena data kurang | Menolak di tahap RW dengan catatan | 6.2 |
| C-05 | Jasa Service Elektronik Pak Budi disetujui | Menyetujui pendaftaran usaha | 8.1 |
| C-06 | Koperasi Warga Tanimulya ditolak lalu diajukan ulang | Menolak, lalu menyetujui pengajuan ulang | 8.2, 8.3 |
| C-07 | Berita Rencana Pemasangan CCTV Lingkungan | Menyetujui berita sebelum terbit | 7.1 |
| C-08 | Berita RT 03 dikembalikan untuk revisi | Mengembalikan dengan catatan, lalu menyetujui | 7.2, 7.3 |
| C-10 | Perubahan data anggota keluarga | Persetujuan final tahap RW | 10 |

Ringkasan C-01 dari awal sampai akhir: Pak Budi Santoso mengajukan lewat menu Permohonan Surat (`01-Warga.md`), Pak Budi Sampurno menyetujui sebagai Ketua RT 03 (`02-Ketua-RT.md`), Bu Rina mengesahkan di Persetujuan Dokumen (bab 6), lalu Pak Budi Santoso mengunduh PDF (`01-Warga.md`).

---

## 15. Masalah yang Sering Terjadi (FAQ)

**Saya masuk tetapi menu Persetujuan Konten tidak ada.**
Itu hanya ada untuk Admin RW. Pastikan Anda masuk sebagai `adminrw21`, bukan akun Ketua RW.

**Muncul "Anda tidak memiliki hak akses untuk membuka halaman tersebut."**
Halaman itu bukan untuk peran Anda. Kembali ke Dashboard dan buka lewat menu.

**Muncul 403 "Verifikasi UMKM hanya dapat dilakukan oleh Admin RW."**
Aksi UMKM hanya untuk Admin RW. Jika ini terjadi pada akun Admin RW, keluar lalu masuk lagi.

**Portal Persetujuan Dokumen kosong padahal warga bilang sudah mengajukan.**
Surat mungkin masih menunggu Ketua RT. Surat baru masuk ke tahap RW setelah RT menyetujui. Surat yang masih menunggu RT tetap tampil sebagai kartu **Menunggu RT** bila statusnya **Diajukan**.

**Saya sudah mengesahkan tetapi warga belum bisa mengunduh.**
Pastikan status surat sudah **Selesai** di Riwayat Pengajuan. Unduhan hanya bisa untuk surat berstatus **Selesai**; jika belum, warga melihat "Surat belum selesai diproses."

**Konten tidak muncul di antrean Persetujuan Konten.**
Operator mungkin masih menyimpan draf, atau jenis konten itu (agenda, pengumuman) sudah langsung terbit sesuai pengaturan. Cek di Pusat Informasi.

**Notifikasi hilang dari lonceng.**
Mungkin tersembunyi. Buka lonceng dan klik **Pulihkan Notifikasi**.

**Mengapa saya tidak bisa membuat link aktivasi baru untuk warga?**
Itu dilakukan Ketua RT pada keluarga di RT-nya. Admin RW hanya bisa melihat link yang ada.

---

## 16. Lampiran: Daftar Gambar

Total **37 gambar** (semua desktop 1366×768).

| Kode | Judul | Bab |
|---|---|---|
| ARW-01 | Peta menu Admin RW | 1 |
| ARW-02 | Daftar notifikasi | 2 |
| ARW-03 | Dashboard bagian atas | 3 |
| ARW-04 | Dashboard bagian bawah | 3 |
| ARW-05 | Manajemen Penduduk, tab Profil Keluarga | 4.1 |
| ARW-06 | Hasil filter RT 03 | 4.1 |
| ARW-07 | Detail Keluarga | 4.2 |
| ARW-08 | Detail Penduduk | 4.2 |
| ARW-09 | Modal Export Data Penduduk | 4.3 |
| ARW-10 | Manajemen Data RT | 5.1 |
| ARW-11 | Modal Tambah RT Baru | 5.2 |
| ARW-12 | Modal Hapus RT yang masih punya keluarga | 5.4 |
| ARW-13 | Portal Persetujuan Dokumen | 6.1 |
| ARW-14 | Detail Permintaan Dokumen | 6.1 |
| ARW-15 | Modal Tanda Tangan & Sahkan | 6.1 |
| ARW-16 | Pesan surat berhasil disahkan | 6.1 |
| ARW-17 | Modal Tolak Pengajuan Surat | 6.2 |
| ARW-18 | Riwayat Pengajuan Dokumen | 6.3 |
| ARW-19 | Antrean Persetujuan Konten | 7.1 |
| ARW-20 | Detail berita menunggu persetujuan | 7.1 |
| ARW-21 | Dialog Setujui Pengajuan | 7.1 |
| ARW-22 | Mengisi Catatan Revisi | 7.2 |
| ARW-23 | Filter Revisi dan catatan revisi terakhir | 7.3 |
| ARW-24 | Antrean Persetujuan UMKM | 8.1 |
| ARW-25 | Detail Pengajuan UMKM | 8.1 |
| ARW-26 | Modal Tolak Pendaftaran UMKM | 8.2 |
| ARW-27 | Tab Pengajuan Ditolak | 8.4 |
| ARW-28 | Pojok UMKM (RW) | 9.1 |
| ARW-29 | Modal Nonaktifkan Usaha | 9.2 |
| ARW-30 | Detail UMKM | 9.1 |
| ARW-31 | Verifikasi Pengajuan Data | 10 |
| ARW-32 | Detail Pengajuan Data | 10 |
| ARW-33 | Modal Tolak Pengajuan | 10 |
| ARW-34 | Pusat Informasi | 11 |
| ARW-35 | Rekap Iuran RT | 12 |
| ARW-36 | Daftar Laporan Keuangan | 13 |
| ARW-37 | Tab Statistik Laporan Keuangan | 13 |
