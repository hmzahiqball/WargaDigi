# Manual Book WargaDigi 21: Ketua RT

> **Untuk siapa:** Ketua RT. Buku ini menjelaskan tugas Ketua RT di aplikasi: memeriksa dan menandatangani pengajuan surat warga, mendata keluarga baru, memeriksa pengajuan perubahan data keluarga, meninjau laporan keuangan RT, dan memantau informasi yang dilihat warga.
>
> **Alamat web:** https://wargadigi21.com
>
> **Akun demo:** `budisampurno` (Ketua RT 03, juga punya peran Warga) dan `ketuart01rw21` (Ketua RT 01). Kata sandi keduanya `password`.
>
> **Dokumen pendamping:** `00-Umum-Publik-dan-Login.md` (masuk dan pilih peran), `01-Warga.md` (sisi warga), `04-Op-Keuangan-RT.md` (sisi Bendahara RT), `05-Admin-RW.md` dan `06-Ketua-RW.md` (sisi RW).

<!-- AI-INSTRUKSI: Ubah file ini menjadi dokumen Word bergambar.
1. Judul "# ..." menjadi Judul Dokumen, "## Bab" menjadi Heading 1, "### Sub-bab" menjadi Heading 2.
2. Blok "> 👤 SIMULASI" menjadi kotak biru muda. Blok "✅ Hasilnya" menjadi kotak hijau muda. Blok "⚠️ Perhatian" menjadi kotak kuning. Blok "> 💡 Tips" menjadi kotak abu-abu. Blok "> ➡️ Cerita berlanjut" menjadi teks miring.
3. Setiap blok "> 📸 SCREENSHOT <KODE>" diganti gambar dengan nama file pada baris "Nama file". Beri keterangan "Gambar <KODE>: <judul>" di bawah gambar. Jangan cetak isi daftar Akun/URL/Kondisi/Area/Sorot di dokumen akhir; itu hanya petunjuk pengambilan gambar.
4. Komentar HTML seperti ini tidak dicetak.
5. Tabel dipertahankan sebagai tabel Word. Daftar Isi dibuat otomatis dari Heading.
6. Semua gambar buku ini berukuran desktop 1366×768. Gambar boleh diperkecil agar muat satu halaman. -->

---

## Daftar Isi

1. Kenalan Dulu
   - 1.1 Tugas Ketua RT
   - 1.2 Tokoh simulasi
   - 1.3 Peta menu Ketua RT
2. Mulai Menggunakan
   - 2.1 Masuk dan memilih peran Ketua RT
   - 2.2 Dashboard RT
3. Data Penduduk dan Keluarga
   - 3.1 Melihat, mencari, dan mengekspor data penduduk
   - 3.2 Melihat detail penduduk dan detail keluarga
   - 3.3 Mendata keluarga baru (Tambah Keluarga)
   - 3.4 Membagikan dan membuat ulang link aktivasi
4. Persetujuan Surat Warga
   - 4.1 Memeriksa Pengajuan Surat (C-01 bagian 2)
   - 4.2 Menolak pengajuan surat dengan alasan (C-02)
   - 4.3 Melihat Riwayat Pengajuan
5. Persetujuan Pengajuan Data Keluarga
   - 5.1 Memeriksa perubahan data keluarga (C-10)
   - 5.2 Menolak pengajuan data
   - 5.3 Menyetujui pindah pemegang akun
6. Keuangan RT
   - 6.1 Persetujuan Keuangan (C-11 sisi Ketua RT)
   - 6.2 Laporan Keuangan
7. Pusat Informasi
8. Pengaturan Tanda Tangan dan Stempel
9. Cerita Lengkap (Lintas Peran)
10. Masalah yang Sering Terjadi (FAQ)
11. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### 1.1 Tugas Ketua RT

Ketua RT adalah pintu pertama untuk urusan warga di RT-nya. Di WargaDigi 21, tugas Ketua RT ada lima:

| Tugas | Isinya | Bab |
|---|---|---|
| Mendata keluarga | Menambah keluarga baru beserta anggotanya, lalu membagikan link aktivasi akun kepada pemegang akun | 3 |
| Memeriksa surat | Membaca pengajuan surat warga, lalu **menyetujui dengan tanda tangan** atau **menolak dengan alasan** | 4 |
| Memeriksa perubahan data | Menyetujui atau menolak pengajuan tambah anggota, perubahan data KK, perubahan data anggota, dan pindah pemegang akun | 5 |
| Meninjau laporan keuangan RT | Menyetujui atau menolak laporan bulanan yang diajukan Bendahara RT | 6 |
| Memantau | Melihat dashboard, daftar penduduk RT, dan konten yang sudah terbit | 2, 3, 7 |

Alur surat secara umum (rujukan: `01-Warga.md`, cerita C-01):

1. Warga mengajukan surat. Status: **Diajukan**, label di layar **Menunggu RT**.
2. Ketua RT menyetujui dengan tanda tangan. Status: **Disetujui RT**, label **Menunggu RW**. Atau Ketua RT menolak dengan alasan. Status: **Ditolak RT**.
3. Pengurus RW (Admin RW atau Ketua RW) mengesahkan. Status: **Selesai**, warga mengunduh PDF.

### 1.2 Tokoh simulasi

| Tokoh | Akun | Peran dalam buku ini |
|---|---|---|
| Pak **Budi Sampurno** | `budisampurno` | Ketua RT 03 sekaligus warga (kepala keluarga). Keluarganya: Siti Aminah, Rizky Pratama Sampurno, Doni Setiawan. Tokoh utama buku ini. |
| Pak **Ahmad Zaki** | `ketuart01rw21` | Ketua RT 01. Dipakai sebagai pembanding: ia hanya mengurus RT 01, sehingga daftar penduduk dan pengajuan datanya berbeda dari Pak Budi Sampurno. |
| Pak **Budi Santoso** | `budisantoso` | Warga RT 03 yang mengajukan surat dan pengajuan data (istri Siti Rahma, anak Andi Saputra). Ia muncul di cerita C-01, C-02, dan C-10. |
| Bu **Siti Aminah** | `operator_rt3` | Bendahara RT 03 (Op Keuangan RT). Ia mengajukan laporan keuangan ke Ketua RT. |

> 💡 **Tips:** Nama tokoh hanya untuk memudahkan membaca cerita. Akun operator tidak menampilkan nama tokoh di layar.

### 1.3 Peta menu Ketua RT

Setelah masuk sebagai Ketua RT, menu samping berisi:

| Menu | Alamat | Isi |
|---|---|---|
| **Dashboard** | `/rt/dashboard` | Ringkasan warga, surat menunggu, laporan menunggu, grafik saldo, dan demografi |
| **Manajemen Penduduk** | `/rt/penduduk` | Daftar keluarga dan penduduk di RT, tombol **Export** dan **Tambah Keluarga** |
| **Persetujuan** (induk menu) | | Membuka tiga submenu di bawah |
| - **Persetujuan Dokumen** | `/rt/persetujuan-dokumen` | Pengajuan surat warga |
| - **Persetujuan Keuangan** | `/rt/persetujuan-keuangan` | Laporan keuangan RT yang diajukan Bendahara |
| - **Pengajuan Data** | `/rt/mutasi-warga` | Pengajuan perubahan data keluarga |
| **Laporan Keuangan** | `/laporan-keuangan` | Laporan keuangan yang sudah terbit, dengan tab **Statistik** untuk Ketua RT |
| **Pusat Informasi** | `/rt/pusat-informasi` | Berita, agenda, dan pengumuman yang sudah terbit (hanya dilihat) |
| **Pojok UMKM** | `/galeri` | Etalase usaha warga |
| **Tanda Tangan** (bagian bawah) | `/pengaturan/tanda-tangan` | Menyimpan tanda tangan dan stempel bawaan |
| **Keluar** (bagian bawah) | | Mengakhiri sesi. Akun dua peran juga punya **Ganti Peran** |

Halaman **Riwayat Pengajuan** (`/rt/riwayat-dokumen`) tidak ada di menu samping. Ia dibuka lewat tombol **Riwayat Pengajuan** di halaman Persetujuan Dokumen.

> 📸 **SCREENSHOT KRT-01: Menu samping Ketua RT**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/dashboard
> - **Kondisi:** submenu **Persetujuan** terbuka sehingga tiga submenu terlihat
> - **Area:** menu samping dari atas sampai **Pojok UMKM**, ditambah bagian bawah (**Tanda Tangan**, **Ganti Peran**, **Keluar**) sebagai potongan kedua
> - **Sorot:** kotak merah pada **Manajemen Penduduk**, **Persetujuan Dokumen**, **Persetujuan Keuangan**, **Pengajuan Data**, dan **Tanda Tangan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-01-menu-samping-ketua-rt.png`

---

## 2. Mulai Menggunakan

### 2.1 Masuk dan memilih peran Ketua RT

Cara masuk dijelaskan lengkap di `00-Umum-Publik-dan-Login.md` (bab Masuk, Pilih Peran, dan Keluar). Ringkasnya:

- `ketuart01rw21` hanya punya satu peran. Setelah **Masuk**, Pak Ahmad Zaki langsung tiba di **Dashboard RT**.
- `budisampurno` punya dua peran. Setelah **Masuk**, tampil halaman **Masuk Sebagai** dengan dua kartu: **Ketua RT** ("Akses dashboard & tugas kepengurusan") dan **Kepala Keluarga** ("Akses layanan warga & keluarga").

> 👤 **SIMULASI KRT-A: Pak Budi Sampurno masuk sebagai Ketua RT**
>
> Pagi hari Pak **Budi Sampurno** ingin memeriksa pengajuan surat warga RT 03.
>
> - **Masuk sebagai:** `budisampurno`, kata sandi `password`
> - **Peran yang dipilih:** **Ketua RT**
> - **Catatan:** untuk mengurus usaha atau surat pribadinya, ia nanti memilih kartu **Kepala Keluarga** (lihat `01-Warga.md`)

**1. Buka halaman masuk.**
Ketik `wargadigi21.com/login`, atau klik tombol hijau **Masuk**.

**2. Isi username dan password.**
Ketik `budisampurno` pada kolom **Username atau Nomor WhatsApp**, lalu `password` pada kolom **Password**. Tekan **Masuk**.

**3. Pilih kartu Ketua RT.**
Pada halaman **Masuk Sebagai**, klik kartu **Ketua RT**.

> 📸 **SCREENSHOT KRT-02: Memilih peran Ketua RT**
> - **Akun:** budisampurno
> - **URL:** /pilih-peran
> - **Kondisi:** baru masuk, dua kartu tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kartu **Ketua RT**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-02-pilih-peran-ketua-rt.png`

✅ **Hasilnya:** Anda tiba di **Dashboard RT** (`/rt/dashboard`) dan menu samping memuat menu Ketua RT.

⚠️ **Perhatian**

- **Menu yang tampil adalah menu Warga, bukan menu Ketua RT.** Anda memilih kartu **Kepala Keluarga**. Tekan **Ganti Peran** di bagian bawah menu samping, lalu pilih kartu **Ketua RT**.
- **"Akun belum diaktivasi."** Akun Ketua RT dibuat oleh pengelola aplikasi. Hubungi Admin Aplikasi bila akun Anda belum aktif.

> ➡️ **Cerita berlanjut:** setelah masuk, lanjut ke ringkasan di Dashboard RT (2.2).

---

### 2.2 Dashboard RT

Halaman **Dashboard RT** berjudul **Ringkasan**. Isinya dari atas ke bawah:

1. **Pengingat surat.** Bila ada pengajuan surat yang menunggu, muncul kotak kuning **Terdapat Dokumen yang Perlu Diverifikasi** dengan tombol **Tinjau Dokumen Sekarang**. Tombol itu membuka Persetujuan Dokumen.
2. **Tiga kartu angka:**
   - **TOTAL WARGA** (jumlah penduduk, dengan rincian Laki-laki dan Perempuan).
   - **DOKUMEN MENUNGGU** (jumlah surat berstatus Diajukan, dengan rincian Surat Domisili dan Kartu Keluarga).
   - **PERSETUJUAN KEUANGAN** (jumlah laporan RT yang menunggu, dengan rincian Iuran Bulanan dan Laporan Kas).
3. **Tren Saldo Keuangan RT (6 Bulan Terakhir).** Grafik garis saldo kas RT.
4. **Aktivitas Laporan Keuangan.** Tiga laporan terbaru beserta statusnya. Tautan **Lihat Semua** membuka Persetujuan Keuangan.
5. **Aktivitas Laporan Dokumen.** Lima pengajuan surat terbaru berstatus **Menunggu** atau **Disetujui RT**.
6. **Data demografi.** Ringkasan penduduk di lingkup RT Anda.

> 👤 **SIMULASI KRT-B: Pak Budi Sampurno membuka Dashboard**
>
> Setelah memilih peran Ketua RT, Pak **Budi Sampurno** melihat ringkasan sebelum bekerja.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Data demo:** jumlah warga mengikuti data penduduk demo. Data demo tidak memiliki surat berstatus Diajukan, jadi kotak kuning dan angka **DOKUMEN MENUNGGU** baru berisi setelah Pak Budi Santoso mengajukan surat (cerita C-01)

**1. Buka Dashboard.**
Klik **Dashboard** di menu samping.

> 📸 **SCREENSHOT KRT-03: Dashboard RT bagian atas**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/dashboard
> - **Kondisi:** ada satu pengajuan Surat Keterangan Domisili berstatus Diajukan (setelah Pak Budi Santoso mengirim pengajuan di `01-Warga.md`, cerita C-01), sehingga kotak kuning tampil
> - **Area:** judul **Ringkasan**, kotak kuning, dan tiga kartu angka
> - **Sorot:** kotak merah pada tombol **Tinjau Dokumen Sekarang** dan kartu **DOKUMEN MENUNGGU**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-03-dashboard-atas.png`

**2. Gulir ke bawah.**
Lihat grafik **Tren Saldo Keuangan RT (6 Bulan Terakhir)**, dua kotak aktivitas, dan data demografi.

> 📸 **SCREENSHOT KRT-04: Dashboard RT bagian bawah**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/dashboard (digulir ke bawah)
> - **Kondisi:** kotak **Aktivitas Laporan Dokumen** berisi pengajuan Pak Budi Santoso dengan label **Menunggu**
> - **Area:** grafik, kotak **Aktivitas Laporan Keuangan** dan **Aktivitas Laporan Dokumen**
> - **Sorot:** kotak merah pada label **Menunggu** dan tautan **Lihat Semua** di kotak keuangan
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-04-dashboard-bawah.png`

✅ **Hasilnya:** Anda tahu pekerjaan yang menunggu: surat untuk ditandatangani dan laporan keuangan untuk ditinjau.

⚠️ **Perhatian**

- **Kotak kuning tidak muncul.** Tidak ada pengajuan berstatus Diajukan. Itu normal pada data demo yang baru.
- **Tulisan "Belum ada aktivitas laporan keuangan RT."** Bendahara RT belum membuat laporan.
- **Tautan "Lihat Semua" di kotak Aktivitas Laporan Dokumen belum berfungsi.** Untuk melihat semua pengajuan, buka **Persetujuan Dokumen**, lalu tekan **Riwayat Pengajuan** (lihat 4.3).
- **Pilihan "Tahun Ini" dan "Tahun Lalu" di atas grafik belum mengubah grafik.** Grafik selalu menampilkan 6 bulan terakhir.

> 💡 **Tips:** Angka **TOTAL WARGA** dan **DOKUMEN MENUNGGU** di dashboard dihitung dari seluruh data aplikasi, bukan hanya RT Anda. Untuk daftar penduduk khusus RT Anda, buka **Manajemen Penduduk** (bab 3).

> ➡️ **Cerita berlanjut:** untuk memeriksa surat yang menunggu, lihat 4.1.

---

## 3. Data Penduduk dan Keluarga

Menu **Manajemen Penduduk** menampilkan data penduduk **di RT Anda saja**. Ketua RT 03 tidak melihat penduduk RT 01, dan sebaliknya. Judul halamannya **Manajemen Penduduk**, dengan keterangan "Data penduduk di" diikuti nama RT Anda. Di kanan atas ada lencana **Total: ... jiwa**, tombol **Export**, dan tombol **Tambah Keluarga**.

### 3.1 Melihat, mencari, dan mengekspor data penduduk

Halaman punya dua tab:

| Tab | Isi | Kolom | Kolom pencarian |
|---|---|---|---|
| **Profil Keluarga** | Satu baris per keluarga | NO. KK, KEPALA KELUARGA, ALAMAT, STATUS WARGA, PEMILIK RUMAH, ANGGOTA, AKSI | **Cari Keluarga (No. KK / Nama Kepala / Alamat)** |
| **Daftar Semua Penduduk** | Satu baris per orang | NAMA, NIK, L/P, HUBUNGAN KELUARGA, NO. KK, AKSI | **Cari Penduduk (Nama / NIK)** |

Setiap baris punya tombol **Detail**. Tombol **Filter** menjalankan pencarian dan tombol silang merah (**Reset**) membersihkannya. Daftar dibagi per 15 baris dengan nomor halaman di bawahnya.

> 👤 **SIMULASI KRT-C: Pak Budi Sampurno mencari data warga**
>
> Seorang warga menelepon dan menanyakan apakah keluarganya sudah tercatat. Pak **Budi Sampurno** mencarinya dari komputer.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Kata kunci:** `Santoso` pada tab **Daftar Semua Penduduk**
> - **Data demo:** keluarga Budi Santoso (Siti Rahma, Andi Saputra) dan keluarga Budi Sampurno (Siti Aminah, Rizky Pratama Sampurno, Doni Setiawan) ada di RT 03

**1. Buka Manajemen Penduduk.**
Klik **Manajemen Penduduk** di menu samping. Tab **Profil Keluarga** terbuka lebih dulu.

> 📸 **SCREENSHOT KRT-05: Manajemen Penduduk, tab Profil Keluarga**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/penduduk
> - **Kondisi:** halaman baru dibuka, tab **Profil Keluarga** aktif, daftar keluarga RT 03 tampil
> - **Area:** satu layar penuh dari judul sampai beberapa baris tabel
> - **Sorot:** kotak merah pada lencana **Total**, tombol **Export**, tombol **Tambah Keluarga**, dan kolom pencarian
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-05-manajemen-penduduk-keluarga.png`

**2. Pindah ke tab Daftar Semua Penduduk.**
Klik tab **Daftar Semua Penduduk**.

**3. Cari nama.**
Ketik `Santoso` pada kotak **Cari Penduduk (Nama / NIK)**, lalu tekan **Filter**.

> 📸 **SCREENSHOT KRT-06: Hasil pencarian penduduk**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/penduduk?tab=penduduk&q=Santoso
> - **Kondisi:** tab **Daftar Semua Penduduk** aktif, kata kunci `Santoso` terisi, hasil tampil
> - **Area:** kolom pencarian dan tabel hasil
> - **Sorot:** kotak merah pada kolom pencarian, tombol **Filter**, dan tombol **Detail** pada baris Budi Santoso
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-06-hasil-cari-penduduk.png`

**4. Ekspor ke Excel (bila perlu).**
Tekan tombol **Export**. Jendela **Export Data Penduduk** terbuka dengan nama RT Anda. Semua kolom tercentang. Gunakan **Pilih Semua** atau **Kosongkan**, lalu centang kolom yang Anda perlukan. Pilihan kolom: NIK, Nama Lengkap, Nomor WA, Jenis Kelamin, Tempat Lahir, Tanggal Lahir, Agama, Pekerjaan, Pendidikan Terakhir, Hubungan Keluarga, Status Perkawinan, Kewarganegaraan, Status Warga, Alamat (KTP), Alamat Domisili, Nama Ayah, Nama Ibu, No. KK, dan RT. Tekan **Export Excel**.

> 📸 **SCREENSHOT KRT-07: Jendela Export Data Penduduk**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/penduduk (jendela terbuka)
> - **Kondisi:** tombol **Export** ditekan, semua kolom tercentang
> - **Area:** jendela **Export Data Penduduk** penuh
> - **Sorot:** kotak merah pada **Pilih Semua**, **Kosongkan**, dan tombol **Export Excel**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-07-export-penduduk.png`

✅ **Hasilnya:**

- Daftar menyempit sesuai kata kunci. Tulisan "Tidak ada penduduk yang cocok dengan pencarian." muncul bila tidak ada yang cocok.
- Tombol **Export Excel** mengunduh berkas `.xlsx` bernama `data-penduduk-rt-` diikuti tanggal dan jam. Isinya hanya penduduk RT Anda dan mengikuti kata kunci pencarian yang sedang aktif.

⚠️ **Perhatian**

- **"Belum ada data penduduk di ..." atau "Belum ada data keluarga di ...".** RT Anda belum punya data. Tambahkan lewat **Tambah Keluarga** (3.3).
- **Jendela Export menampilkan pesan kolom.** Centang minimal satu kolom. Aplikasi menolak ekspor tanpa kolom.
- **Halaman menolak dengan pesan "RT untuk akun Anda tidak terdefinisi."** Akun Anda belum dikaitkan ke RT. Hubungi Admin Aplikasi.

> 💡 **Tips:** File ekspor memuat NIK dan nomor WA warga. Simpan di perangkat pribadi dan jangan dibagikan sembarangan.

> ➡️ **Cerita berlanjut:** untuk melihat satu keluarga lebih rinci, lanjut ke 3.2.

---

### 3.2 Melihat detail penduduk dan detail keluarga

**Detail Penduduk.** Klik **Detail** pada baris penduduk. Halaman **Detail Penduduk** ("Informasi identitas dan hubungan keluarga penduduk.") menampilkan Nama Lengkap, NIK, Nomor WA, Jenis Kelamin, Tempat, Tanggal Lahir, Agama, Pekerjaan, Pendidikan Terakhir, Status Perkawinan, Kewarganegaraan, Hubungan Keluarga, Status Warga, Nomor KK, Alamat (sesuai KTP), dan Alamat Domisili. Di sisi kanan ada kartu **Kartu Tanda Penduduk** dengan tombol untuk membuka berkas KTP. Bila belum ada berkas, tampil "Belum ada file KTP yang diunggah."

**Detail Keluarga.** Klik **Detail** pada baris di tab **Profil Keluarga**. Halaman **Detail Keluarga** ("Data Kartu Keluarga dan anggota penduduk.") berisi:

- **Profil Kartu Keluarga:** Nomor KK, Kepala Keluarga, Alamat (sesuai KK), Alamat Domisili, Status KK (**Warga Tetap** atau **Bukan Warga Tetap**), dan Pemilik Rumah.
- **Akun Keluarga:** Username, Status Akun (**Aktif** atau **Belum Diaktivasi**), dan bila belum aktif, link aktivasi beserta tombol **Buat Ulang Link Aktivasi**.
- **Anggota Keluarga (n jiwa):** tabel NAMA, NIK, L/P, HUBUNGAN, dan tombol **Detail** per anggota.

Anda hanya bisa membuka keluarga dan penduduk di RT sendiri. Mencoba membuka RT lain menghasilkan pesan "Keluarga ini berada di luar RT Anda." (halaman terlarang, kode 403).

> 👤 **SIMULASI KRT-D: Pak Budi Sampurno membuka data keluarga Budi Santoso**
>
> Sebelum menyetujui surat, Pak **Budi Sampurno** ingin memastikan data keluarga Pak Budi Santoso.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Keluarga yang dibuka:** keluarga Budi Santoso (istri Siti Rahma, anak Andi Saputra)

**1. Buka Detail Keluarga.**
Pada tab **Profil Keluarga**, cari `Santoso` lalu klik **Detail** pada barisnya.

> 📸 **SCREENSHOT KRT-08: Detail Keluarga**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/keluarga/ lalu nomor keluarga (alamat memakai kode keluarga)
> - **Kondisi:** keluarga Budi Santoso terbuka
> - **Area:** kartu **Profil Kartu Keluarga**, **Akun Keluarga**, dan tabel **Anggota Keluarga**
> - **Sorot:** kotak merah pada **Status Akun** dan tombol **Detail** pada anggota Siti Rahma
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-08-detail-keluarga.png`

**2. Buka salah satu anggota.**
Klik **Detail** pada baris **Siti Rahma**.

**3. Kembali.**
Gunakan tombol kembali pada halaman detail untuk kembali ke daftar.

✅ **Hasilnya:** Anda melihat data resmi keluarga dan anggotanya, termasuk status akun pemegang akun.

⚠️ **Perhatian**

- **Halaman terlarang (403) dengan pesan "Keluarga ini berada di luar RT Anda."** Keluarga itu tercatat di RT lain. Hanya Ketua RT yang bersangkutan yang dapat membukanya.
- **"Belum ada anggota."** Keluarga tersebut baru punya data profil.

> ➡️ **Cerita berlanjut:** untuk membuat keluarga baru, lanjut ke 3.3.

---

### 3.3 Mendata keluarga baru (Tambah Keluarga)

Tombol **Tambah Keluarga** membuka halaman **Tambah Keluarga** ("Unggah KK untuk mengisi otomatis, atau isi data secara manual."). Halaman ini punya tiga bagian:

1. **Profil Keluarga:** data Kartu Keluarga.
2. **Data Penduduk:** satu kartu per orang, minimal satu.
3. **Akun Keluarga:** penjelasan bahwa username dan password tidak diisi Ketua RT.

Pembacaan otomatis (OCR) membantu mengisi data dari foto KK dan KTP. Hasilnya selalu perlu diperiksa.

> 👤 **SIMULASI KRT-E: Pak Budi Sampurno mendata keluarga baru**
>
> Keluarga baru pindah ke RT 03. Pak **Budi Sampurno** mendatanya dengan KK yang mereka bawa. Contoh data di bawah adalah data contoh untuk buku ini, bukan data penduduk nyata.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Berkas:** foto Kartu Keluarga dan foto KTP kepala keluarga
> - **Data contoh:** kepala keluarga "Hasan Basri" (hubungan **Kepala Keluarga**), istri "Aisyah" (hubungan **Istri**), dengan Hasan sebagai **pemegang akun keluarga** dan nomor WA `081200000001`
> - **Lanjutan cerita:** link aktivasi dibagikan ke Hasan, lalu Hasan mengaktifkan akun (lihat `00-Umum-Publik-dan-Login.md`, bab aktivasi akun)

**1. Buka Tambah Keluarga.**
Di **Manajemen Penduduk**, tekan tombol hijau **Tambah Keluarga**.

**2. Unggah Kartu Keluarga.**
Pada **Upload Kartu Keluarga (KK)**, pilih foto atau scan KK. Tombol **Ambil Data KK dari File** menjadi aktif. Tekan tombol itu. Sebagai pilihan lain, tekan **Ambil KK dari Kamera**, arahkan kamera ke KK, lalu tekan **Jepret**.

> 📸 **SCREENSHOT KRT-09: Tambah Keluarga, Profil Keluarga**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/keluarga/create
> - **Kondisi:** foto KK sudah dipilih dan tombol **Ambil Data KK dari File** aktif
> - **Area:** kartu **Profil Keluarga** dari kotak unggah sampai kolom **Alamat Domisili**
> - **Sorot:** kotak merah pada kotak **Upload Kartu Keluarga (KK)**, tombol **Ambil Data KK dari File**, dan tombol **Ambil KK dari Kamera**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-09-tambah-keluarga-profil.png`

**3. Periksa hasil bacaan.**
Bila terbaca, tampil keterangan "Data KK terbaca. Periksa & lengkapi bila perlu." dan kolom terisi: **Nomor KK**, **Alamat (sesuai KK)**, **Provinsi**, **Kabupaten/Kota**, **Kecamatan**, **Kelurahan/Desa**, **RT**, **RW**, **Kode Pos**. Bila tidak terbaca, tampil "KK tidak dapat dibaca otomatis. Silakan isi manual." dan Anda mengetik sendiri. Nomor KK (16 digit) dan Alamat wajib diisi (tanda **\***).

**4. Lengkapi Alamat Domisili, Status KK, dan Pemilik rumah.**
Isi **Alamat Domisili (tempat tinggal)** bila berbeda dari alamat KK. Pilih **Status KK**: **Bukan Warga Tetap** (bawaan) atau **Warga Tetap**. Centang **Pemilik rumah** bila sesuai.

**5. Isi Data Penduduk.**
Satu kartu **Penduduk #1** sudah tersedia. Untuk kepala keluarga: pada **Upload KTP**, pilih foto KTP lalu tekan **Baca KTP** (atau **Ambil KTP dari Kamera**). Periksa kolom yang terisi, terutama **NIK**, karena angka mudah salah baca. Lengkapi kolom bertanda **\***: **NIK**, **Nama Lengkap**, **Jenis Kelamin**, **Tempat Lahir**, **Tanggal Lahir**, **Agama**, **Pekerjaan**, **Status Kawin**, dan **Hubungan Keluarga**. Kolom lain: **Nomor WA**, **Alamat (sesuai KTP)**, **Alamat Domisili**, **Kewarganegaraan**, **Pendidikan**, **Status Warga**.

**6. Tambah anggota lain.**
Tekan **Tambah Penduduk** untuk setiap anggota. Ikon tempat sampah merah menghapus satu kartu.

**7. Tentukan pemegang akun.**
Pada kartu orang yang akan memegang akun, centang **Pemegang akun keluarga**. Nama orang itu muncul di bagian **Akun Keluarga** (menggantikan "- belum dipilih -"). Isi **Nomor WA**-nya, karena wajib untuk pemegang akun.

> 📸 **SCREENSHOT KRT-10: Data Penduduk dan pemegang akun**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/keluarga/create (digulir ke bagian **Data Penduduk**)
> - **Kondisi:** dua kartu penduduk terisi (Hasan Basri dan Aisyah), **Pemegang akun keluarga** tercentang pada Hasan Basri
> - **Area:** bagian **Data Penduduk** sampai bagian **Akun Keluarga** dan tombol **Simpan Keluarga**
> - **Sorot:** kotak merah pada tombol **Baca KTP**, tombol **Tambah Penduduk**, centang **Pemegang akun keluarga**, dan nama pemegang di bagian **Akun Keluarga**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-10-tambah-keluarga-penduduk.png`

**8. Simpan.**
Tekan **Simpan Keluarga**.

✅ **Hasilnya:**

- Anda kembali ke **Manajemen Penduduk** dengan pesan "Data keluarga berhasil disimpan. Bagikan link aktivasi di bawah kepada pemegang akun agar dapat membuat username & password sendiri."
- Di atas halaman tampil kotak hijau **Link Aktivasi Akun Warga** berisi link dan tombol **Salin**.
- Keluarga baru muncul di tab **Profil Keluarga**. Akun pemegang berstatus **Belum Diaktivasi** sampai warga membuka link.
- Status warga otomatis **Warga Tetap** bila RW 21 dan kelurahan Tanimulya, kecuali Anda memilih sendiri.

> 📸 **SCREENSHOT KRT-11: Link aktivasi setelah keluarga disimpan**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/penduduk (sesaat setelah **Simpan Keluarga**)
> - **Kondisi:** pesan sukses dan kotak **Link Aktivasi Akun Warga** tampil
> - **Area:** pesan sukses, kotak hijau, dan awal tabel
> - **Sorot:** kotak merah pada link dan tombol **Salin**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-11-link-aktivasi-baru.png`

⚠️ **Perhatian**

- **"Nomor KK sudah terdaftar."** Keluarga itu sudah tercatat. Cari lewat tab **Profil Keluarga**.
- **"NIK sudah terdaftar pada data penduduk."** NIK itu sudah ada di keluarga lain. Periksa ulang angka NIK, atau cari orangnya di **Daftar Semua Penduduk**.
- **"Terdapat NIK ganda pada daftar penduduk."** Dua kartu memakai NIK yang sama.
- **"Harus ada tepat satu penduduk dengan hubungan "Kepala Keluarga"."** Pastikan hanya satu kartu berhubungan **Kepala Keluarga**.
- **"Pilih satu penduduk sebagai pemegang akun keluarga."** Centang **Pemegang akun keluarga** pada salah satu kartu.
- **"Nomor WA wajib diisi untuk pemegang akun keluarga."** Isi nomor WA pemegang akun.
- **"Nomor WA hanya boleh angka, spasi, tanda + atau -."** Hapus huruf atau simbol lain dari nomor WA.
- **"Berkas dokumen tidak valid atau sudah kedaluwarsa. Silakan unggah ulang."** Foto KK atau KTP yang dibaca sudah terlalu lama. Unggah ulang dan simpan lebih cepat.
- **"Gagal membaca dokumen. Isi manual."** atau **"KTP tidak dapat dibaca otomatis. Silakan isi manual."** Pembacaan otomatis gagal. Ketik datanya sendiri. Foto yang terang, lurus, dan tidak buram membantu.
- **"Gagal mengakses kamera. Pastikan izin kamera diberikan."** Izinkan akses kamera di browser, atau unggah file saja.
- **Pesan lain berbahasa Inggris** (misalnya untuk tanggal atau kolom wajib yang kosong) berasal dari pemeriksaan bawaan. Isi kolom bertanda **\*** lalu simpan lagi.

> 💡 **Tips:** Foto dan hasil bacaan hanya alat bantu. Periksa **NIK** dan **Nomor KK** angka demi angka sebelum menyimpan.

> ➡️ **Cerita berlanjut:** Hasan Basri membuka link aktivasi dan membuat username serta password. Lihat `00-Umum-Publik-dan-Login.md`, bab Masuk, Pilih Peran, dan Keluar, bagian aktivasi akun.

---

### 3.4 Membagikan dan membuat ulang link aktivasi

Link aktivasi hanya muncul sekali setelah **Simpan Keluarga**. Bila Anda belum sempat menyalinnya, atau link hilang, buat ulang dari **Detail Keluarga**.

> 👤 **SIMULASI KRT-F: Pak Budi Sampurno membuat ulang link aktivasi**
>
> Link yang dibagikan ke Hasan Basri tidak sengaja terhapus dari WhatsApp. Pak **Budi Sampurno** membuat link baru.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Keluarga:** keluarga Hasan Basri (akun masih **Belum Diaktivasi**)

**1. Buka Detail Keluarga.**
Di tab **Profil Keluarga**, klik **Detail** pada keluarga Hasan Basri.

**2. Lihat kartu Akun Keluarga.**
Status Akun tertulis **Belum Diaktivasi**. Di bawahnya ada keterangan "Akun belum diaktivasi. Bagikan link berikut kepada pemegang akun agar dapat membuat password sendiri." dan link beserta tombol **Salin**.

**3. Buat ulang bila perlu.**
Tekan **Buat Ulang Link Aktivasi**. Link lama tidak berlaku lagi dan link baru tampil.

> 📸 **SCREENSHOT KRT-12: Akun Keluarga belum diaktivasi**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/keluarga/ lalu kode keluarga Hasan Basri
> - **Kondisi:** kartu **Akun Keluarga** menampilkan **Belum Diaktivasi**, link, dan tombol **Buat Ulang Link Aktivasi**
> - **Area:** kartu **Akun Keluarga** saja
> - **Sorot:** kotak merah pada label **Belum Diaktivasi**, tombol **Salin**, dan tombol **Buat Ulang Link Aktivasi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-12-akun-keluarga-belum-aktif.png`

✅ **Hasilnya:** Pesan "Link aktivasi berhasil dibuat ulang." tampil. Bagikan link baru lewat WhatsApp atau pesan lain.

⚠️ **Perhatian**

- **"Akun sudah aktif; tidak perlu link aktivasi."** Pemegang akun sudah mengaktifkan akunnya. Bagian link tidak muncul untuk akun aktif.
- **"Akun pemegang keluarga tidak ditemukan."** Pemegang akun belum terhubung ke akun login. Hubungi Admin Aplikasi.

> 💡 **Tips:** Link aktivasi bersifat rahasia keluarga. Kirim hanya ke pemegang akun yang bersangkutan.

> ➡️ **Cerita berlanjut:** cara warga mengaktifkan akun ada di `00-Umum-Publik-dan-Login.md`.

---

## 4. Persetujuan Surat Warga

Menu **Persetujuan Dokumen** (`/rt/persetujuan-dokumen`) berjudul **Portal Persetujuan Dokumen** dengan keterangan "Dokumen pengajuan warga yang menunggu persetujuan RT." Halaman ini hanya memuat pengajuan berstatus **Diajukan**. Setiap pengajuan tampil sebagai kartu berisi nama pemohon, NIK, tanggal pengajuan, **Jenis Surat**, **Alamat**, **Pekerjaan**, dan label **Menunggu Verifikasi RT**. Di kanan atas ada tombol **Riwayat Pengajuan**.

Setiap kartu punya tiga tombol:

| Tombol | Fungsi |
|---|---|
| **Detail Permintaan** | Membuka data lengkap pemohon dan lampiran KTP/KK |
| **Tanda Tangan & Setujui** | Membuka jendela tanda tangan dengan pratinjau surat |
| **Tolak** | Membuka jendela alasan penolakan |

Bila belum ada pengajuan, halaman menampilkan "Belum Ada Pengajuan" dan "Belum ada dokumen yang perlu diverifikasi saat ini."

### 4.1 Memeriksa Pengajuan Surat (C-01 bagian 2)

Cerita C-01 dimulai di `01-Warga.md`: Pak **Budi Santoso** mengajukan Surat Keterangan Domisili. Bagian 2 cerita ini ada di sisi Ketua RT: memeriksa data, lalu menyetujui dengan tanda tangan.

> 👤 **SIMULASI KRT-G: Pak Budi Sampurno menyetujui surat domisili Pak Budi Santoso**
>
> Pak **Budi Sampurno** menerima pengajuan Surat Keterangan Domisili dari Pak **Budi Santoso**. Ia memeriksa data dan lampiran, lalu menandatanganinya.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Prasyarat:** Pak Budi Santoso sudah mengirim pengajuan (`01-Warga.md`, cerita C-01). Data demo tidak punya pengajuan berstatus **Diajukan**, jadi cerita ini baru bisa dijalankan setelah pengajuan itu ada
> - **Nomor urut surat (contoh):** `012`

**1. Buka Persetujuan Dokumen.**
Klik **Persetujuan** di menu samping, lalu **Persetujuan Dokumen**. Anda juga bisa menekan **Tinjau Dokumen Sekarang** di Dashboard.

> 📸 **SCREENSHOT KRT-13: Portal Persetujuan Dokumen**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/persetujuan-dokumen
> - **Kondisi:** satu kartu pengajuan Surat Keterangan Domisili atas nama Budi Santoso, label **Menunggu Verifikasi RT**
> - **Area:** judul halaman sampai kartu pengajuan
> - **Sorot:** kotak merah pada tombol **Riwayat Pengajuan**, **Detail Permintaan**, **Tanda Tangan & Setujui**, dan **Tolak**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-13-portal-persetujuan-dokumen.png`

**2. Periksa data pemohon.**
Tekan **Detail Permintaan**. Jendela **Detail Permintaan Dokumen** menampilkan dua bagian. Kiri: **Pemohon** (nama, NIK, RT/RW, **Tipe Surat**, tanggal diajukan). Kanan: **Data Lengkap Pemohon** (Jenis Kelamin, Tempat, Tgl Lahir, Agama, Pendidikan Terakhir, Jenis Pekerjaan, Status Perkawinan, Kewarganegaraan, Alamat).

**3. Periksa lampiran.**
Di bagian **Lampiran Dokumen (KTP)** tampil foto KTP. Tekan **Lihat Kartu Keluarga →** untuk berganti ke KK, dan **Lihat KTP →** untuk kembali. Tekan **Lihat Dokumen** atau klik gambarnya untuk memperbesar. Bila belum ada berkas, tampil "Belum diunggah".

> 📸 **SCREENSHOT KRT-14: Detail Permintaan Dokumen**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/persetujuan-dokumen (jendela **Detail Permintaan Dokumen** terbuka)
> - **Kondisi:** pengajuan Budi Santoso, lampiran KTP tampil
> - **Area:** jendela penuh dari judul sampai tombol bawah
> - **Sorot:** kotak merah pada **Data Lengkap Pemohon**, tautan **Lihat Kartu Keluarga →**, dan tombol **Setujui Dokumen**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-14-detail-permintaan-dokumen.png`

**4. Mulai menyetujui.**
Tekan **Setujui Dokumen** di bagian bawah jendela (atau **Tanda Tangan & Setujui** di kartu). Jendela **Tanda Tangan & Setujui** terbuka. Kiri berisi isian, kanan berisi **Pratinjau Surat**.

**5. Isi Nomor Urut Surat.**
Ketik `012` pada kolom **Nomor Urut Surat** (placeholder "Contoh: 012"). Keterangan di bawahnya: "Hanya bagian urut. Sisanya (SK/RT/RW/bulan/tahun) disusun otomatis." Pratinjau surat ikut berubah. Kolom ini boleh dikosongkan.

**6. Bubuhkan tanda tangan.**
Ada dua kemungkinan, tergantung apakah Anda sudah menyimpan tanda tangan bawaan (bab 8):

- **Sudah tersimpan:** pilihan **TTD Tersimpan** terpilih lebih dulu dan gambar tanda tangan Anda tampil. Pilih **Gambar Manual** bila ingin menggambar ulang.
- **Belum tersimpan:** tampil kotak "Coretan tanda tangan di sini". Gambar tanda tangan dengan mouse atau jari. Tombol **Ulangi** menghapus coretan. Centang **Simpan jadi default** bila ingin memakainya lagi di surat berikutnya.

**7. Pilih stempel (opsional).**
Bagian **Stempel RT (Opsional)**: bila sudah ada stempel tersimpan, centang **Gunakan stempel tersimpan**. Bila belum, klik kotak **Unggah Stempel (PNG, Max 2MB)**, lalu pilih berkas PNG. Tombol **Hapus Stempel** membatalkan pilihan. Centang **Simpan jadi default** bila ingin menyimpannya.

**8. Lihat pratinjau.**
Pastikan tanda tangan dan stempel muncul di kolom RT pada **Pratinjau Surat**.

> 📸 **SCREENSHOT KRT-15: Tanda Tangan dan Setujui**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/persetujuan-dokumen (jendela **Tanda Tangan & Setujui** terbuka)
> - **Kondisi:** nomor urut `012` terisi, tanda tangan tergambar, pratinjau surat memuat tanda tangan di kolom RT
> - **Area:** jendela penuh (isian kiri, pratinjau kanan)
> - **Sorot:** kotak merah pada kolom **Nomor Urut Surat**, kotak tanda tangan, bagian **Stempel RT (Opsional)**, dan tombol **Setujui & Teruskan ke RW**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-15-ttd-dan-setujui.png`

**9. Kirim.**
Tekan **Setujui & Teruskan ke RW**.

✅ **Hasilnya:**

- Anda kembali ke Portal Persetujuan Dokumen dengan pesan "Dokumen berhasil disetujui dan diteruskan ke RW."
- Kartu pengajuan itu hilang dari daftar, karena halaman ini hanya memuat status **Diajukan**.
- Status surat menjadi **Disetujui RT** (label **Menunggu RW**). Pak Budi Santoso dan pengurus RW mendapat pemberitahuan.
- Pengurus RW (Admin RW atau Ketua RW) kemudian mengesahkan surat sampai **Selesai**, dan warga mengunduh PDF.

⚠️ **Perhatian**

- **Tanda tangan harus tampak di pratinjau sebelum Anda menekan Setujui.** Tanda bintang pada **Tanda Tangan RT** menandakan wajib, tetapi saat menulis buku ini aplikasi tidak menolak persetujuan tanpa tanda tangan. Surat akan lanjut ke RW dengan kolom tanda tangan RT kosong. Pastikan selalu menggambar atau memilih tanda tangan tersimpan.
- **"Format harus PNG!"** atau **"Max 2MB!"**. Berkas stempel harus PNG dan tidak lebih dari 2 MB.
- **Coretan tanda tangan hilang saat jendela diubah ukurannya.** Gambar ulang dengan **Ulangi**.
- **Pratinjau surat kosong atau lambat tampil.** Tunggu sebentar. Pratinjau memuat surat dari server.
- **Kartu tidak muncul di daftar.** Pengajuan itu sudah diproses, atau belum dikirim oleh warga. Cek di **Riwayat Pengajuan** (4.3).

> 💡 **Tips:** Siapkan tanda tangan dan stempel bawaan lebih dulu (bab 8). Dengan begitu setiap persetujuan cukup dua klik: **Tanda Tangan & Setujui**, lalu **Setujui & Teruskan ke RW**.

> ➡️ **Cerita berlanjut:** pengajuan yang tidak layak disetujui ditolak dengan alasan, lihat 4.2.

---

### 4.2 Menolak pengajuan surat dengan alasan (C-02)

Cerita C-02 dimulai di `01-Warga.md`: pengajuan Pak **Budi Santoso** ditolak karena ada data yang tidak cocok. Berikut sisi Ketua RT.

> 👤 **SIMULASI KRT-H: Pak Budi Sampurno menolak pengajuan surat**
>
> Pak **Budi Sampurno** memeriksa pengajuan dan menemukan data yang tidak sesuai. Ia menolak dan menjelaskan alasannya agar warga bisa memperbaiki.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Prasyarat:** ada pengajuan berstatus **Diajukan** (`01-Warga.md`, cerita C-02)
> - **Alasan (contoh):** `Alamat di pengajuan tidak sama dengan alamat di KK. Mohon perbaiki alamat lalu ajukan ulang.`

**1. Buka jendela penolakan.**
Di kartu pengajuan, tekan **Tolak**. Atau buka **Detail Permintaan**, lalu tekan **Tolak & Beri Catatan**. Jendela **Tolak Pengajuan Surat** terbuka. Kotak kuning berisi petunjuk: "Mohon berikan alasan penolakan atau catatan koreksi agar pemohon dapat melakukan perbaikan pada pengajuan ini."

**2. Tulis alasan.**
Isi kolom **Alasan Penolakan / Catatan Koreksi** (placeholder "Contoh: Data KTP tidak sesuai dengan yang terdaftar..."). Kolom ini wajib dan maksimal 1000 karakter.

> 📸 **SCREENSHOT KRT-16: Tolak Pengajuan Surat**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/persetujuan-dokumen (jendela **Tolak Pengajuan Surat** terbuka)
> - **Kondisi:** alasan penolakan contoh sudah diketik
> - **Area:** jendela penuh
> - **Sorot:** kotak merah pada kolom **Alasan Penolakan / Catatan Koreksi** dan tombol **Tolak & Kirim Catatan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-16-tolak-pengajuan-surat.png`

**3. Kirim.**
Tekan **Tolak & Kirim Catatan**.

✅ **Hasilnya:**

- Pesan "Catatan penolakan telah dikirim ke pemohon." tampil.
- Status surat menjadi **Ditolak RT**. Pak Budi Santoso mendapat pemberitahuan dan dapat membaca alasan Anda di halaman surat miliknya, lalu mengajukan ulang.
- Kartu hilang dari daftar. Pengajuan itu masih terlihat di **Riwayat Pengajuan** dengan status **Ditolak RT**.

⚠️ **Perhatian**

- **Browser meminta kolom diisi.** Alasan tidak boleh kosong. Tulis alasan yang jelas, bukan satu kata.
- **Alasan terlalu panjang.** Batas 1000 karakter. Ringkas isinya.
- **Penolakan tidak bisa dibatalkan dari layar ini.** Bila Anda keliru, minta warga mengajukan ulang, lalu setujui pengajuan barunya.

> 💡 **Tips:** Tulis alasan yang bisa langsung ditindaklanjuti, misalnya data mana yang salah dan apa yang harus diperbaiki.

> ➡️ **Cerita berlanjut:** untuk melihat seluruh pengajuan beserta statusnya, lihat 4.3.

---

### 4.3 Melihat Riwayat Pengajuan

Halaman **Riwayat Pengajuan** (`/rt/riwayat-dokumen`) dengan keterangan "Daftar seluruh pengajuan surat beserta statusnya." Dibuka lewat tombol **Riwayat Pengajuan** di Portal Persetujuan Dokumen. Tombol **Kembali ke Persetujuan** di kanan atas membawa Anda kembali.

Tabel berisi kolom **Tanggal**, **Pemohon** (nama dan NIK), **Jenis Surat**, kolom RT, dan **Status**. Status yang tampil:

| Status di tabel | Arti |
|---|---|
| **Menunggu RT** | Diajukan warga, menunggu Anda |
| **Menunggu RW** | Sudah Anda setujui, menunggu pengesahan RW |
| **Selesai** | Sudah disahkan RW, warga bisa mengunduh |
| **Ditolak RT** | Anda menolak |
| **Ditolak RW** | RW menolak |

Di atas tabel ada kolom pencarian "Cari nama, NIK, atau jenis surat...", pilihan **Semua Status** (isinya Diajukan, Disetujui RT, Selesai, Ditolak RT, Ditolak RW), tombol **Filter**, dan tombol silang (**Reset**) yang muncul bila ada filter aktif. Daftar dibagi per 15 baris. Bila kosong, tampil "Belum ada pengajuan."

> 👤 **SIMULASI KRT-I: Pak Budi Sampurno mengecek nasib surat yang sudah diproses**
>
> Pak **Budi Santoso** bertanya apakah surat domisilinya sudah disahkan RW. Pak **Budi Sampurno** mengeceknya di riwayat.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Kata kunci:** `Santoso`
> - **Prasyarat:** pengajuan sudah ada (cerita C-01 atau C-02)

**1. Buka Riwayat Pengajuan.**
Di Portal Persetujuan Dokumen, tekan **Riwayat Pengajuan**.

**2. Cari pemohon.**
Ketik `Santoso` pada kolom pencarian, lalu tekan **Filter**.

**3. Saring berdasarkan status (bila perlu).**
Pilih **Disetujui RT** pada pilihan status, lalu tekan **Filter**.

> 📸 **SCREENSHOT KRT-17: Riwayat Pengajuan**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/riwayat-dokumen?q=Santoso
> - **Kondisi:** kata kunci `Santoso` terisi, beberapa baris dengan status berbeda
> - **Area:** judul sampai tabel
> - **Sorot:** kotak merah pada kolom pencarian, pilihan status, tombol **Filter**, dan label status di kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-17-riwayat-pengajuan.png`

✅ **Hasilnya:** Anda melihat tahap terakhir setiap pengajuan. Surat berstatus **Menunggu RW** berarti Anda sudah selesai. Surat berstatus **Selesai** sudah bisa diunduh warga.

⚠️ **Perhatian**

- **Tulisan "Belum ada pengajuan."** Tidak ada baris yang cocok dengan kata kunci atau status. Tekan tombol silang untuk mengosongkan filter.
- **Riwayat memuat pengajuan dari semua RT, bukan hanya RT Anda.** Gunakan kolom RT untuk menyaring secara visual. Data RT lain hanya untuk dilihat.
- **Tidak ada tombol aksi di halaman ini.** Menyetujui atau menolak hanya dari Portal Persetujuan Dokumen, dan hanya untuk status **Diajukan**.

> ➡️ **Cerita berlanjut:** pengajuan perubahan data keluarga dibahas di bab 5.

---

## 5. Persetujuan Pengajuan Data Keluarga

Menu **Pengajuan Data** (`/rt/mutasi-warga`) berjudul **Verifikasi Pengajuan Data** dengan keterangan "Daftar pengajuan penambahan/perubahan data anggota keluarga." Halaman ini hanya memuat pengajuan dari keluarga di RT Anda yang berstatus menunggu Ketua RT.

Tabel berisi kolom **WAKTU PENGAJUAN**, **KEPALA KELUARGA** (nama dan nomor KK), **JENIS PENGAJUAN**, **DETAIL AJUAN** (nama dan NIK yang diajukan), dan **AKSI** (tombol **Detail**). Jenis pengajuan:

| Label | Arti |
|---|---|
| **Tambah Anggota Baru** | Warga menambah anggota keluarga |
| **Perubahan Data KK** | Warga mengubah data Kartu Keluarga |
| **Edit Data Anggota** | Warga mengubah data seorang anggota |
| **Pindah Pemegang Akun** | Warga memindahkan akun keluarga ke anggota lain |

Bila tidak ada pengajuan, tampil "Belum ada pengajuan mutasi baru".

Alur persetujuan: pengajuan tambah anggota, perubahan data KK, dan edit anggota melewati **dua tahap**: Ketua RT, lalu RW (Admin RW lewat menu **Pengajuan Data**). Pindah pemegang akun cukup **satu tahap**, yaitu persetujuan Ketua RT.

### 5.1 Memeriksa perubahan data keluarga (C-10)

Cerita C-10 dimulai di `01-Warga.md`: Pak **Budi Santoso** mengajukan perubahan data keluarganya. Alurnya: Warga, Ketua RT, Admin RW, lalu disetujui.

> 👤 **SIMULASI KRT-J: Pak Budi Sampurno memeriksa pengajuan perubahan data**
>
> Pak **Budi Santoso** mengajukan perubahan data keluarganya. Pak **Budi Sampurno** memeriksa data lama dan data baru, lalu meneruskannya ke RW.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Prasyarat:** pengajuan sudah dibuat warga (`01-Warga.md`, cerita C-10). Data demo tidak punya data pengajuan, jadi halaman ini kosong sebelum warga mengajukan

**1. Buka Pengajuan Data.**
Klik **Persetujuan** di menu samping, lalu **Pengajuan Data**.

> 📸 **SCREENSHOT KRT-18: Verifikasi Pengajuan Data**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/mutasi-warga
> - **Kondisi:** satu pengajuan dari keluarga Budi Santoso (cerita C-10)
> - **Area:** judul sampai tabel
> - **Sorot:** kotak merah pada kolom **JENIS PENGAJUAN** dan tombol **Detail**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-18-verifikasi-pengajuan-data.png`

**2. Buka detail.**
Tekan **Detail** pada baris pengajuan. Jendela **Detail Pengajuan Data** terbuka dengan tiga bagian:

- **Perubahan Data:** tabel perbandingan data lama dan data baru. Periksa teliti.
- **Riwayat Proses:** tahap pengajuan (diajukan warga, persetujuan Ketua RT, persetujuan RW) beserta waktu dan petugas.
- **File Bukti (Scan KK):** bila warga melampirkan, tampil tombol **Lihat Dokumen** yang membuka berkas di tab baru.

**3. Setujui.**
Tekan tombol hijau **Setujui (Teruskan ke RW)**.

> 📸 **SCREENSHOT KRT-19: Detail Pengajuan Data**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/mutasi-warga (jendela **Detail Pengajuan Data** terbuka)
> - **Kondisi:** pengajuan perubahan data dengan tabel data lama dan data baru, serta **Riwayat Proses**
> - **Area:** jendela penuh
> - **Sorot:** kotak merah pada bagian **Perubahan Data**, **Riwayat Proses**, tombol **Tolak**, dan tombol **Setujui (Teruskan ke RW)**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-19-detail-pengajuan-data.png`

✅ **Hasilnya:**

- Pesan "Pengajuan data disetujui. Menunggu persetujuan RW." tampil dan pengajuan hilang dari daftar Anda.
- Pengajuan berpindah ke Admin RW (menu **Pengajuan Data** milik RW). Setelah RW menyetujui, data keluarga resmi berubah.
- Pak Budi Santoso dan RW mendapat pemberitahuan.

⚠️ **Perhatian**

- **"Status pengajuan tidak valid untuk diproses oleh RT."** Pengajuan itu sudah diproses (misalnya oleh Anda di tab lain), atau milik keluarga di RT lain. Muat ulang halaman.
- **Daftar kosong, "Belum ada pengajuan mutasi baru".** Belum ada pengajuan dari warga RT Anda, atau semuanya sudah Anda proses.
- **Anda tidak bisa mengubah isi pengajuan.** Bila ada yang salah, tolak dengan alasan (5.2) agar warga mengajukan ulang.

> 💡 **Tips:** Bandingkan data baru dengan KK yang dilampirkan. Pastikan NIK dan nama sama persis dengan berkas.

> ➡️ **Cerita berlanjut:** untuk menolak, lihat 5.2. Untuk pindah pemegang akun, lihat 5.3.

---

### 5.2 Menolak pengajuan data

> 👤 **SIMULASI KRT-K: Pak Budi Sampurno menolak pengajuan data**
>
> Data baru yang diajukan tidak cocok dengan KK yang dilampirkan. Pak **Budi Sampurno** menolak dan meminta warga memperbaiki.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Prasyarat:** ada pengajuan di daftar **Pengajuan Data**
> - **Alasan (contoh):** `NIK anggota tidak sama dengan KK yang dilampirkan. Mohon periksa lalu ajukan ulang.`

**1. Buka detail.**
Tekan **Detail** pada baris pengajuan.

**2. Tekan Tolak.**
Tekan tombol **Tolak** di bagian bawah jendela. Jendela **Tolak Pengajuan** terbuka.

**3. Tulis alasan.**
Isi kolom **Alasan penolakan** (placeholder "Tuliskan alasan penolakan agar warga dapat memperbaiki pengajuan."). Kolom ini wajib.

> 📸 **SCREENSHOT KRT-20: Tolak Pengajuan Data**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/mutasi-warga (jendela **Tolak Pengajuan** terbuka)
> - **Kondisi:** alasan contoh sudah diketik
> - **Area:** jendela penuh
> - **Sorot:** kotak merah pada kolom **Alasan penolakan** dan tombol **Tolak Pengajuan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-20-tolak-pengajuan-data.png`

**4. Kirim.**
Tekan **Tolak Pengajuan**.

✅ **Hasilnya:** Pesan "Pengajuan data ditolak." tampil. Pengajuan hilang dari daftar, dan pengaju mendapat pemberitahuan berisi alasan Anda. Data keluarga tidak berubah.

⚠️ **Perhatian**

- **Browser meminta kolom diisi.** Alasan wajib ditulis.
- **"Status pengajuan tidak valid untuk diproses oleh RT."** Pengajuan sudah diproses atau bukan milik RT Anda.
- **Penolakan bersifat final untuk pengajuan itu.** Warga harus membuat pengajuan baru.

> ➡️ **Cerita berlanjut:** jenis pengajuan khusus, pindah pemegang akun, dibahas di 5.3.

---

### 5.3 Menyetujui pindah pemegang akun

Pindah pemegang akun adalah pengajuan khusus. Warga meminta agar akun keluarga dipegang anggota lain. Misalnya kepala keluarga berhalangan. Persetujuan **hanya dari Ketua RT**, tidak diteruskan ke RW.

Saat Anda menyetujui, aplikasi melakukan hal berikut:

1. **Akun pemegang lama:** peran **Warga** dicabut. Bila akun itu hanya punya peran Warga, akun **dinonaktifkan** (tidak dihapus). Bila punya peran lain (misalnya Ketua RT), akun tetap aktif dengan peran lainnya.
2. **Pemegang akun keluarga** diganti menjadi anggota tujuan.
3. **Akun tujuan** disiapkan. Bila belum punya akun, dibuatkan akun baru yang perlu diaktivasi. Bila sudah punya akun aktif, cukup diberi peran **Warga** tanpa aktivasi ulang.

> 👤 **SIMULASI KRT-L: Pak Budi Sampurno menyetujui pindah pemegang akun**
>
> Keluarga Pak **Budi Santoso** meminta akun dipindah ke istrinya, Bu **Siti Rahma**. Pak **Budi Sampurno** memeriksa lalu menyetujuinya.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Prasyarat:** pengajuan **Pindah Pemegang Akun** sudah dibuat warga. Data demo tidak punya pengajuan ini
> - **Anggota tujuan:** Siti Rahma

**1. Buka detail.**
Di **Pengajuan Data**, cari baris berlabel **Pindah Pemegang Akun**, lalu tekan **Detail**.

**2. Periksa isi pengajuan.**
Di bagian **Perubahan Data**, lihat siapa pemegang lama ("Dari: ...") dan siapa tujuannya. Pastikan sesuai permintaan keluarga.

> 📸 **SCREENSHOT KRT-21: Detail Pindah Pemegang Akun**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/mutasi-warga (jendela **Detail Pengajuan Data** terbuka)
> - **Kondisi:** pengajuan **Pindah Pemegang Akun** dengan anggota tujuan Siti Rahma
> - **Area:** jendela penuh
> - **Sorot:** kotak merah pada label **Pindah Pemegang Akun**, bagian **Perubahan Data**, dan tombol **Setujui Pemindahan Akun**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-21-detail-pindah-akun.png`

**3. Setujui.**
Tekan tombol **Setujui Pemindahan Akun** (tombol ini menggantikan **Setujui (Teruskan ke RW)** untuk jenis ini).

**4. Bagikan link aktivasi (bila tampil).**
Bila Siti Rahma belum punya akun aktif, kotak hijau **Link Aktivasi Akun Warga** muncul di atas tabel: "Bagikan link berikut kepada pemegang akun baru (Siti Rahma) agar dapat membuat username & password sendiri. Link berlaku sampai akun diaktifkan." Tekan **Salin** (tulisan berubah menjadi **Tersalin**), lalu kirim lewat WhatsApp.

> 📸 **SCREENSHOT KRT-22: Link aktivasi pemegang akun baru**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/mutasi-warga (sesaat setelah **Setujui Pemindahan Akun**)
> - **Kondisi:** pesan sukses dan kotak **Link Aktivasi Akun Warga** tampil
> - **Area:** pesan sukses dan kotak hijau
> - **Sorot:** kotak merah pada link dan tombol **Salin**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-22-link-aktivasi-pindah-akun.png`

✅ **Hasilnya:**

- Bila akun tujuan perlu aktivasi, pesan "Pemindahan akun disetujui. Bagikan link aktivasi kepada pemegang akun baru." tampil bersama link.
- Bila akun tujuan sudah aktif, pesan "Pemindahan akun disetujui. Akun tujuan sudah aktif sebelumnya, cukup diberi akses Warga tanpa perlu aktivasi ulang." tampil tanpa link.
- Akun lama tidak lagi bisa dipakai sebagai Warga (atau nonaktif). Siti Rahma menjadi pemegang akun keluarga setelah aktivasi.

⚠️ **Perhatian**

- **Link aktivasi hanya tampil sekali.** Salin sebelum meninggalkan halaman. Bila hilang, buat ulang dari **Detail Keluarga** (3.4).
- **"Data pengajuan pemindahan akun tidak lengkap."** Pengajuan tidak memuat anggota tujuan. Tolak dan minta warga mengajukan ulang.
- **"Anggota tujuan tidak ditemukan pada keluarga ini."** Anggota tujuan tidak tercatat di keluarga itu. Tolak dengan alasan.
- **Pengaju mendapat akun yang tidak bisa masuk lagi.** Itu disengaja. Akun pemegang lama dinonaktifkan agar tidak ada dua pemegang akun. Jelaskan ini kepada keluarga sebelum menyetujui.

> 💡 **Tips:** Bila Anda sendiri pemegang akun yang dipindahkan (misalnya Pak Budi Sampurno yang juga warga), peran Ketua RT Anda tetap aktif. Hanya peran Warga yang dicabut.

> ➡️ **Cerita berlanjut:** urusan keuangan RT dibahas di bab 6.

---

## 6. Keuangan RT

Ada dua halaman keuangan untuk Ketua RT:

| Halaman | Alamat | Fungsi |
|---|---|---|
| **Persetujuan Keuangan** | `/rt/persetujuan-keuangan` | Menyetujui atau menolak laporan bulanan yang diajukan Bendahara RT |
| **Laporan Keuangan** | `/laporan-keuangan` | Melihat laporan yang sudah terbit, dan tab **Statistik** |

Cerita C-11 dimulai di `04-Op-Keuangan-RT.md`: Bu **Siti Aminah** (Bendahara RT 03) mengajukan laporan bulanan. Setelah Anda menyetujui, Bendahara perlu **mempublikasikan** laporan agar warga bisa melihatnya.

### 6.1 Persetujuan Keuangan (C-11 sisi Ketua RT)

Halaman **Persetujuan Keuangan** ("Tinjau dan setujui laporan keuangan bulanan yang diajukan oleh Bendahara.") menampilkan lencana **Ketua RT** di kanan atas dan dua kartu:

- **Menunggu Persetujuan** ("n laporan menunggu ditinjau"): kolom **PERIODE** (bulan, tahun, judul), **UNIT**, **DIBUAT OLEH**, **PEMASUKAN**, **PENGELUARAN**, **SALDO AKHIR**, **AKSI**. Aksinya: ikon PDF (**Lihat PDF**), **Setujui**, dan **Tolak**.
- **Riwayat Persetujuan** ("Laporan yang sudah diproses"): kolom **PERIODE**, **UNIT**, **DIBUAT OLEH**, **STATUS** (**Disetujui** atau **Ditolak**), **DIPROSES PADA**, **CATATAN**, dan ikon PDF.

Anda hanya memproses laporan unit **RT** milik RT Anda.

> 👤 **SIMULASI KRT-M: Pak Budi Sampurno menyetujui laporan keuangan RT 03**
>
> Bu **Siti Aminah** (Bendahara RT 03) mengajukan laporan keuangan bulanan. Pak **Budi Sampurno** memeriksanya, lalu menyetujuinya.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Prasyarat:** laporan sudah diajukan Bendahara (`04-Op-Keuangan-RT.md`, cerita C-11). Laporan demo hanya ada untuk RW, DKM, dan RT 01, jadi untuk RT 03 laporan perlu dibuat dulu oleh Bendahara

**1. Buka Persetujuan Keuangan.**
Klik **Persetujuan** di menu samping, lalu **Persetujuan Keuangan**. Atau tekan **Lihat Semua** di kotak Aktivitas Laporan Keuangan pada Dashboard.

> 📸 **SCREENSHOT KRT-23: Persetujuan Keuangan**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/persetujuan-keuangan
> - **Kondisi:** satu laporan RT 03 pada kartu **Menunggu Persetujuan**, riwayat di bawahnya
> - **Area:** judul sampai kartu **Menunggu Persetujuan** dan awal **Riwayat Persetujuan**
> - **Sorot:** kotak merah pada kolom **SALDO AKHIR**, ikon PDF, tombol **Setujui**, dan tombol **Tolak**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-23-persetujuan-keuangan.png`

**2. Periksa laporan.**
Tekan ikon PDF untuk membuka laporan di tab baru. Periksa pemasukan, pengeluaran, dan saldo akhir.

**3. Tekan Setujui.**
Jendela **Setujui Laporan** terbuka. Ia menampilkan judul, periode, dan tiga angka: **Pemasukan**, **Pengeluaran**, **Saldo Akhir**. Isi **Catatan (opsional)** bila perlu.

> 📸 **SCREENSHOT KRT-24: Setujui Laporan**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/persetujuan-keuangan (jendela **Setujui Laporan** terbuka)
> - **Kondisi:** tiga angka tampil, kolom catatan boleh kosong
> - **Area:** jendela penuh
> - **Sorot:** kotak merah pada ketiga angka dan tombol **Ya, Setujui Laporan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-24-setujui-laporan.png`

**4. Konfirmasi.**
Tekan **Ya, Setujui Laporan**.

✅ **Hasilnya:**

- Pesan "Laporan berhasil disetujui." tampil.
- Laporan pindah dari **Menunggu Persetujuan** ke **Riwayat Persetujuan** dengan status **Disetujui**.
- Bu Siti Aminah mendapat pemberitahuan. Status laporan menjadi **Approved**, tetapi **belum tampil untuk warga**. Bendahara masih harus menekan **Publikasikan ke warga** di menu laporannya (lihat `04-Op-Keuangan-RT.md`).

**Menolak laporan.** Tekan **Tolak**. Jendela **Tolak Laporan** terbuka. Isi **Alasan Penolakan** (wajib, placeholder "Jelaskan alasan penolakan laporan ini...", maksimal 500 karakter), lalu tekan **Ya, Tolak Laporan**.

> 📸 **SCREENSHOT KRT-25: Tolak Laporan**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/persetujuan-keuangan (jendela **Tolak Laporan** terbuka)
> - **Kondisi:** alasan contoh sudah diketik, misalnya `Bukti pengeluaran bulan ini belum lengkap. Mohon dilengkapi.`
> - **Area:** jendela penuh
> - **Sorot:** kotak merah pada kolom **Alasan Penolakan** dan tombol **Ya, Tolak Laporan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-25-tolak-laporan.png`

Hasilnya: pesan "Laporan berhasil ditolak." tampil dan laporan masuk riwayat dengan status **Ditolak** beserta catatannya. Bendahara memperbaiki lalu mengajukan ulang.

⚠️ **Perhatian**

- **"Semua laporan sudah ditinjau. Tidak ada yang menunggu persetujuan."** Tidak ada laporan berstatus Submitted. Normal bila Bendahara belum mengajukan.
- **"Laporan ini tidak dalam status Submitted."** Laporan sudah diproses. Muat ulang halaman.
- **"Catatan penolakan wajib diisi."** Alasan penolakan tidak boleh kosong.
- **Halaman terlarang (403) "Anda tidak memiliki akses untuk laporan RT lain."** Laporan itu milik RT lain.
- **Warga belum melihat laporan walau sudah disetujui.** Laporan harus dipublikasikan Bendahara. Hubungi Bu Siti Aminah.
- **Persetujuan tidak bisa dibatalkan.** Periksa angka sebelum menekan **Ya, Setujui Laporan**.

> 💡 **Tips:** Buka PDF dulu sebelum memutuskan. Angka di jendela hanya ringkasan.

> ➡️ **Cerita berlanjut:** untuk melihat laporan yang sudah terbit dan membaca statistiknya, lihat 6.2.

---

### 6.2 Laporan Keuangan

Menu **Laporan Keuangan** (`/laporan-keuangan`) berjudul **Transparansi Keuangan** ("Laporan keuangan bulanan RT, RW, dan DKM yang telah dipublikasikan."). Hanya laporan berstatus disetujui **dan** sudah dipublikasikan yang tampil di sini. Untuk Ketua RT ada dua tab: **Daftar Laporan** dan **Statistik**.

**Tab Daftar Laporan.** Filter di atas: **Tahun** (**Semua Tahun**), **Bulan** (**Semua Bulan**), **Unit** (**Semua Unit (RT, RW, DKM)**, **Hanya RT**, **Hanya RW**, **Hanya DKM**), dan tombol **Filter**. Tabel berisi **Periode**, **Unit**, **Pemasukan**, **Pengeluaran**, **Saldo Akhir**, dan ikon **Unduh PDF**. Bila belum ada, tampil "Belum ada laporan yang dipublikasikan".

**Tab Statistik.** Berisi analisis otomatis dari laporan yang sudah disetujui:

- **Pemilih unit:** tombol **RT**, **RW**, **DKM**, dan pemilih **Tahun**. Ketua RT terkunci pada RT-nya sendiri.
- **Penilaian ringkas:** kotak berwarna dengan salah satu status **Kondisi baik**, **Perlu diperhatikan**, **Perlu tindakan**, atau **Belum ada data**, disertai kalimat penjelasan.
- **Empat kartu:** **Saldo kas**, **Cadangan kas** (dalam bulan), **Pengeluaran vs pemasukan** (surplus atau defisit), dan **Pengumpulan iuran wajib**.
- **Perlu perhatian:** daftar hal yang perlu ditindaklanjuti, bila ada.
- **Tren Kas:** grafik batang pemasukan dan pengeluaran, serta garis saldo akhir per bulan.
- **Sumber pemasukan** dan **Penggunaan dana**, masing-masing dengan persen per kategori.
- **Iuran wajib dan setoran ke RW:** kepatuhan KK (**Tepat waktu**, **Menunggak 1 bulan**, **Menunggak 2 bulan atau lebih**), serta **Terkumpul**, **Kewajiban setoran**, **Sudah disetor**, dan **Belum disetor**.
- **Ketertiban pengelolaan:** **Pengeluaran berbukti**, **Pengeluaran belum diputuskan**, **Laporan terlambat disetujui**, dan **Laporan pernah ditolak**.

> 👤 **SIMULASI KRT-N: Pak Ahmad Zaki meninjau laporan dan statistik RT 01**
>
> Pak **Ahmad Zaki** (Ketua RT 01) ingin melihat laporan RT-nya yang sudah terbit dan kondisi kas RT.
>
> - **Masuk sebagai:** `ketuart01rw21` (Ketua RT 01)
> - **Alasan memakai akun ini:** laporan demo hanya ada untuk RW, DKM, dan RT 01. Pak Budi Sampurno (RT 03) baru punya laporan setelah cerita C-11 selesai dan laporan dipublikasikan

**1. Buka Laporan Keuangan.**
Klik **Laporan Keuangan** di menu samping. Tab **Daftar Laporan** terbuka lebih dulu.

**2. Saring laporan RT.**
Pilih **Hanya RT** pada **Unit**, lalu tekan **Filter**.

> 📸 **SCREENSHOT KRT-26: Laporan Keuangan, Daftar Laporan**
> - **Akun:** ketuart01rw21
> - **URL:** /laporan-keuangan?unit=RT
> - **Kondisi:** filter **Hanya RT** aktif, beberapa laporan RT 01 tampil
> - **Area:** judul, dua tab, filter, dan tabel
> - **Sorot:** kotak merah pada tab **Statistik**, pilihan **Unit**, dan ikon **Unduh PDF**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-26-laporan-keuangan-daftar.png`

**3. Buka tab Statistik.**
Klik tab **Statistik**. Pilih **Tahun** bila perlu. Baca kotak penilaian di atas, lalu empat kartu.

> 📸 **SCREENSHOT KRT-27: Statistik, penilaian dan kartu kunci**
> - **Akun:** ketuart01rw21
> - **URL:** /laporan-keuangan?tab=statistik&unit=RT
> - **Kondisi:** unit **RT** terpilih, penilaian dan empat kartu tampil
> - **Area:** pemilih unit dan tahun, kotak penilaian, empat kartu, dan awal grafik
> - **Sorot:** kotak merah pada tombol unit **RT**, kotak penilaian, dan kartu **Cadangan kas**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-27-statistik-atas.png`

**4. Gulir ke bawah.**
Lihat **Tren Kas**, **Sumber pemasukan**, **Penggunaan dana**, **Iuran wajib dan setoran ke RW**, dan **Ketertiban pengelolaan**.

> 📸 **SCREENSHOT KRT-28: Statistik, tren dan rincian**
> - **Akun:** ketuart01rw21
> - **URL:** /laporan-keuangan?tab=statistik&unit=RT (digulir ke bawah)
> - **Kondisi:** grafik tren dan kartu rincian tampil
> - **Area:** grafik **Tren Kas**, dua kartu komposisi, dan kartu iuran serta ketertiban
> - **Sorot:** kotak merah pada grafik **Tren Kas**, bagian **Iuran wajib dan setoran ke RW**, dan **Ketertiban pengelolaan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-28-statistik-bawah.png`

✅ **Hasilnya:** Anda mendapat gambaran kas RT: saldo, cadangan, kepatuhan iuran, dan ketertiban laporan. Laporan PDF dapat diunduh dari tab **Daftar Laporan**.

⚠️ **Perhatian**

- **"Belum ada data untuk dianalisis."** Belum ada laporan RT yang disetujui pada tahun terpilih. Statistik muncul setelah ada laporan disetujui.
- **Bulan berjalan tidak ikut dihitung.** Tertulis "belum final (laporan belum disetujui)" di bawah grafik. Angka final baru masuk setelah laporan bulan itu disetujui.
- **Statistik RW dan DKM hanya memakai laporan yang sudah dipublikasikan.** Tertulis "Angka unit ini dihitung dari laporan yang sudah dipublikasikan."
- **Daftar Laporan memuat laporan RT dari semua RT yang sudah terbit,** bukan hanya RT Anda. Gunakan tab **Statistik** untuk melihat RT Anda saja.
- **Penilaian otomatis bukan keputusan.** Tertulis di bagian bawah: "Penilaian dibuat otomatis dari aturan sederhana dan bukan pengganti pertimbangan pengurus."
- **Laporan yang baru Anda setujui belum tampil di Daftar Laporan.** Laporan harus dipublikasikan Bendahara dulu (6.1).

> 💡 **Tips:** Kartu **Cadangan kas** menunjukkan berapa bulan kas cukup bila pengeluaran seperti rata-rata. Bila angkanya di bawah satu bulan, bicarakan dengan Bendahara.

> ➡️ **Cerita berlanjut:** untuk memantau informasi yang dilihat warga, lihat bab 7.

---

## 7. Pusat Informasi

Menu **Pusat Informasi** (`/rt/pusat-informasi`) berjudul **Pusat Informasi RT**. Keterangannya: "Konten RW dan konten RT Anda yang sudah terbit dan tampil bagi warga." Halaman ini **hanya untuk dilihat**. Ketua RT tidak menyetujui konten di sini. Persetujuan berita, agenda, dan pengumuman dilakukan Admin RW.

Isi halaman:

- **Tab:** **Lihat Semua**, **Berita Komunitas**, **Agenda Kegiatan**, dan **Pengumuman**.
- **Pencarian:** kolom "Cari..." untuk judul atau penulis.
- **Daftar:** kartu konten dengan judul, ringkasan, label asal (RW atau RT), tanggal, dan lencana status **Terbit**. Daftar dibagi per 10 konten, dengan keterangan "Menampilkan n-n dari n konten".
- **Detail:** klik kartu untuk membuka isi lengkap. Bila konten agenda, tampil juga **Konfirmasi Kehadiran** dan jumlah **Warga Hadir**, serta peta lokasi bila ada. Tombol panah kembali menutup detail.

> 👤 **SIMULASI KRT-O: Pak Budi Sampurno memantau pengumuman yang dilihat warga**
>
> Seorang warga bertanya soal pengumuman kerja bakti. Pak **Budi Sampurno** mengecek apa yang tampil di sisi warga.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Tab:** **Pengumuman**

**1. Buka Pusat Informasi.**
Klik **Pusat Informasi** di menu samping.

**2. Pilih tab.**
Klik tab **Pengumuman**. Ketik kata kunci di kolom pencarian bila perlu.

**3. Buka detail.**
Klik satu kartu untuk membaca isinya. Tekan panah kembali untuk menutup.

> 📸 **SCREENSHOT KRT-29: Pusat Informasi RT**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/pusat-informasi
> - **Kondisi:** tab **Pengumuman** aktif, beberapa kartu berstatus **Terbit**
> - **Area:** judul, tab, pencarian, dan daftar kartu
> - **Sorot:** kotak merah pada keterangan halaman, tab **Pengumuman**, dan lencana **Terbit**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-29-pusat-informasi-rt.png`

✅ **Hasilnya:** Anda melihat konten yang sama seperti yang dibaca warga RT Anda: konten RW dan konten RT Anda sendiri.

⚠️ **Perhatian**

- **"Tidak ada data ditemukan."** Tidak ada konten yang cocok dengan kata kunci atau tab. Kosongkan pencarian.
- **Tidak ada tombol Setujui atau Tolak.** Itu memang bukan tugas Ketua RT di aplikasi ini. Konten yang belum terbit tidak tampil di sini.
- **Halaman terlarang (403) "Akun Anda belum dikaitkan dengan RT. Hubungi admin."** Akun belum dikaitkan ke RT. Hubungi Admin Aplikasi.

> ➡️ **Cerita berlanjut:** untuk mempercepat persetujuan surat, atur tanda tangan bawaan di bab 8.

---

## 8. Pengaturan Tanda Tangan dan Stempel

Menu **Tanda Tangan** di bagian bawah menu samping (`/pengaturan/tanda-tangan`) berjudul **Pengaturan Tanda Tangan & Stempel**. Keterangannya: "Simpan tanda tangan dan stempel default agar tidak perlu mengisi ulang setiap menyetujui surat."

Halaman punya dua kartu:

| Kartu | Isi |
|---|---|
| **Tanda Tangan Default** | Tab **Unggah File** (PNG, maksimal 2 MB) dan tab **Gambar** (menggambar langsung) |
| **Stempel Default** | Unggah berkas PNG, maksimal 2 MB. Bersifat opsional |

Bila sudah ada yang tersimpan, kartu menampilkan gambarnya ("Tanda tangan tersimpan saat ini:" atau "Stempel tersimpan saat ini:") beserta tombol **Hapus**.

> 👤 **SIMULASI KRT-P: Pak Budi Sampurno menyimpan tanda tangan dan stempel**
>
> Agar tidak menggambar tanda tangan setiap kali menyetujui surat, Pak **Budi Sampurno** menyimpannya satu kali.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Berkas:** tanda tangan digambar langsung, stempel berupa berkas PNG latar transparan (contoh `stempel-rt03.png`)

**1. Buka Tanda Tangan.**
Klik **Tanda Tangan** di bagian bawah menu samping.

**2. Pilih cara tanda tangan.**
Pada kartu **Tanda Tangan Default**, pilih tab **Gambar**. Gambar tanda tangan di kotak. Tombol **Ulangi** menghapus coretan. Keterangan: "Hasil gambar akan disimpan saat Anda menekan tombol Simpan." Atau pilih tab **Unggah File** lalu pilih berkas PNG.

> 📸 **SCREENSHOT KRT-30: Pengaturan Tanda Tangan, tab Gambar**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /pengaturan/tanda-tangan
> - **Kondisi:** tab **Gambar** aktif dan tanda tangan sudah tergambar
> - **Area:** dua kartu dan tombol **Simpan Pengaturan**
> - **Sorot:** kotak merah pada tab **Gambar**, tombol **Ulangi**, kolom **Stempel Default**, dan tombol **Simpan Pengaturan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-30-pengaturan-tanda-tangan.png`

**3. Pilih stempel.**
Pada kartu **Stempel Default**, klik kolom berkas lalu pilih `stempel-rt03.png`.

**4. Simpan.**
Tekan **Simpan Pengaturan**.

> 📸 **SCREENSHOT KRT-31: Tanda tangan dan stempel tersimpan**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /pengaturan/tanda-tangan (setelah disimpan)
> - **Kondisi:** pesan sukses, tanda tangan dan stempel tersimpan tampil dengan tombol **Hapus**
> - **Area:** pesan sukses dan dua kartu
> - **Sorot:** kotak merah pada pesan sukses dan dua tombol **Hapus**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `KRT-31-ttd-stempel-tersimpan.png`

✅ **Hasilnya:**

- Pesan "Pengaturan tanda tangan dan stempel berhasil disimpan." tampil.
- Pada jendela **Tanda Tangan & Setujui** (4.1), pilihan **TTD Tersimpan** dan **Gunakan stempel tersimpan** kini tersedia.

**Menghapus.** Tekan **Hapus** pada kartu yang bersangkutan, lalu konfirmasi pada jendela **Hapus Tanda Tangan** ("Hapus tanda tangan default tersimpan?") atau **Hapus Stempel** ("Hapus stempel default tersimpan?").

⚠️ **Perhatian**

- **"Tanda tangan harus berupa file PNG."** Berkas bukan PNG. Ubah dulu ke PNG.
- **"Ukuran tanda tangan maksimal 2MB."** atau **"Ukuran stempel maksimal 2MB."** Perkecil berkas.
- **"Stempel harus berupa file PNG."** Gunakan PNG.
- **Tanda tangan tidak tersimpan setelah menggambar.** Pastikan tab **Gambar** sedang aktif saat menekan **Simpan Pengaturan**, dan kotak tidak kosong. Bila Anda berpindah ke tab **Unggah File** dan memilih berkas, berkas itu yang dipakai.
- **Mengganti tanda tangan tidak mengubah surat lama.** Surat yang sudah disetujui menyimpan salinan tanda tangan saat itu.
- **Berkas tersimpan privat.** Hanya akun Anda yang dapat melihatnya. Tidak ada tautan publik.

> 💡 **Tips:** Gunakan PNG berlatar transparan agar tanda tangan tidak menutupi tulisan di surat.

> ➡️ **Cerita berlanjut:** seluruh alur dari awal sampai akhir ada di bab 9.

---

## 9. Cerita Lengkap (Lintas Peran)

Bab ini merangkum tiga cerita utama dari sisi Ketua RT. Setiap cerita melibatkan peran lain, sehingga detail sisi peran itu ada di buku pendampingnya.

> 👤 **SIMULASI KRT-Q: Surat Keterangan Domisili Pak Budi Santoso (C-01 dan C-02)**
>
> | Langkah | Tokoh | Akun | Tindakan | Bab |
> |---|---|---|---|---|
> | 1 | Pak Budi Santoso | `budisantoso` | Mengajukan Surat Keterangan Domisili. Status **Diajukan** (**Menunggu RT**) | `01-Warga.md` |
> | 2 | Pak Budi Sampurno | `budisampurno` | Memeriksa data dan lampiran di **Detail Permintaan** | 4.1 |
> | 3a | Pak Budi Sampurno | `budisampurno` | Bila data benar: **Tanda Tangan & Setujui**, lalu **Setujui & Teruskan ke RW**. Status **Disetujui RT** (**Menunggu RW**) | 4.1 |
> | 3b | Pak Budi Sampurno | `budisampurno` | Bila data salah: **Tolak**, tulis alasan, **Tolak & Kirim Catatan**. Status **Ditolak RT** | 4.2 |
> | 4 | Pengurus RW | Admin RW atau Ketua RW | Mengesahkan surat. Status **Selesai** | `05-Admin-RW.md`, `06-Ketua-RW.md` |
> | 5 | Pak Budi Santoso | `budisantoso` | Mengunduh PDF surat, atau memperbaiki dan mengajukan ulang bila ditolak | `01-Warga.md` |

> 👤 **SIMULASI KRT-R: Perubahan data keluarga (C-10)**
>
> | Langkah | Tokoh | Akun | Tindakan | Bab |
> |---|---|---|---|---|
> | 1 | Pak Budi Santoso | `budisantoso` | Mengajukan perubahan data keluarga | `01-Warga.md` |
> | 2 | Pak Budi Sampurno | `budisampurno` | Memeriksa **Perubahan Data** dan **Riwayat Proses**, lalu **Setujui (Teruskan ke RW)** | 5.1 |
> | 3 | Admin RW | Admin RW | Menyetujui lewat menu **Pengajuan Data**. Data keluarga resmi berubah | `05-Admin-RW.md` |
> | 4 | Pak Budi Santoso | `budisantoso` | Menerima pemberitahuan hasil | `01-Warga.md` |
>
> Bila Ketua RT menolak di langkah 2, proses berhenti dan data tidak berubah (5.2). Untuk **Pindah Pemegang Akun**, proses selesai di langkah 2 dan tidak ke RW (5.3).

> 👤 **SIMULASI KRT-S: Laporan keuangan RT 03 sampai terbaca warga (C-11)**
>
> | Langkah | Tokoh | Akun | Tindakan | Bab |
> |---|---|---|---|---|
> | 1 | Bu Siti Aminah | `operator_rt3` | Menyusun dan mengajukan laporan keuangan bulanan. Status Submitted | `04-Op-Keuangan-RT.md` |
> | 2 | Pak Budi Sampurno | `budisampurno` | Memeriksa PDF, lalu **Setujui** atau **Tolak** dengan alasan | 6.1 |
> | 3 | Bu Siti Aminah | `operator_rt3` | Menekan **Publikasikan ke warga** pada laporan yang disetujui | `04-Op-Keuangan-RT.md` |
> | 4 | Pak Budi Sampurno | `budisampurno` | Melihat laporan di **Daftar Laporan** dan membaca tab **Statistik** | 6.2 |
> | 5 | Warga | Akun Warga | Membaca ringkasan transparansi keuangan | `01-Warga.md` |

✅ **Hasilnya:** Anda melihat peran Ketua RT sebagai tahap pertama pada surat dan data keluarga, serta tahap pemeriksa pada laporan keuangan RT.

> ➡️ **Cerita berlanjut:** bila ada kendala, lihat bab 10.

---

## 10. Masalah yang Sering Terjadi (FAQ)

**Masuk dan peran**

- **Menu yang tampil adalah menu Warga.** Anda memilih kartu **Kepala Keluarga**. Tekan **Ganti Peran**, lalu pilih kartu **Ketua RT** (2.1).
- **"Akun belum diaktivasi."** Hubungi Admin Aplikasi (2.1).
- **"RT untuk akun Anda tidak terdefinisi." atau "Akun Anda belum dikaitkan dengan RT. Hubungi admin."** Akun belum dikaitkan ke RT. Hubungi Admin Aplikasi.

**Data penduduk**

- **Keluarga tidak ditemukan.** Cari lewat tab **Profil Keluarga** dengan Nomor KK, nama kepala, atau alamat (3.1).
- **"Keluarga ini berada di luar RT Anda."** Hanya Ketua RT yang bersangkutan yang dapat membukanya (3.2).
- **Link aktivasi hilang.** Buat ulang lewat **Detail Keluarga**, tombol **Buat Ulang Link Aktivasi** (3.4).
- **"Nomor KK sudah terdaftar." atau "NIK sudah terdaftar pada data penduduk."** Data sudah ada. Cari dulu sebelum mendata ulang (3.3).

**Surat**

- **Tidak ada pengajuan di Persetujuan Dokumen.** Warga belum mengajukan, atau pengajuan sudah Anda proses. Cek **Riwayat Pengajuan** (4.3).
- **Surat sudah disetujui tetapi warga belum bisa mengunduh.** Surat berstatus **Menunggu RW**. Warga baru bisa mengunduh setelah RW mengesahkan (status **Selesai**).
- **Tanda tangan tidak muncul di pratinjau.** Gambar tanda tangan atau pilih **TTD Tersimpan**. Pratinjau butuh beberapa detik (4.1).
- **Salah menolak.** Penolakan tidak bisa dibatalkan. Minta warga mengajukan ulang (4.2).
- **Ingin mengubah tanda tangan bawaan.** Buka **Tanda Tangan** di menu samping (bab 8).

**Pengajuan data keluarga**

- **"Status pengajuan tidak valid untuk diproses oleh RT."** Pengajuan sudah diproses atau milik RT lain. Muat ulang halaman (5.1).
- **Link aktivasi pindah akun tidak muncul.** Akun tujuan sudah aktif, jadi tidak perlu link. Pesan sukses menyebutnya (5.3).
- **Pengajuan berstatus menunggu RW tidak ada di daftar saya.** Memang begitu. Setelah Anda setujui, pengajuan ada di Admin RW (5.1).

**Keuangan**

- **Laporan tidak ada di Persetujuan Keuangan.** Bendahara belum mengajukan, atau laporan sudah diproses. Cek **Riwayat Persetujuan** (6.1).
- **Laporan sudah saya setujui tetapi warga belum melihatnya.** Bendahara harus menekan **Publikasikan ke warga** (6.1).
- **Tab Statistik kosong.** Belum ada laporan disetujui pada tahun terpilih. Pilih tahun lain (6.2).
- **Daftar Laporan memuat laporan RT lain.** Daftar ini memuat laporan terbit seluruh unit. Statistik RT Anda hanya dari RT Anda sendiri (6.2).

**Informasi**

- **Konten tidak ada di Pusat Informasi.** Hanya konten berstatus **Terbit** yang tampil. Persetujuan konten oleh Admin RW (bab 7).

**Tampilan dan perangkat**

- **Jendela tanda tangan sulit dipakai di ponsel.** Buku ini memakai tampilan desktop. Di ponsel atau tablet, gambar tanda tangan dengan jari dan pastikan layar tidak berputar saat menggambar.
- **Halaman terasa lambat saat pratinjau surat.** Tunggu pratinjau selesai dimuat sebelum menekan **Setujui & Teruskan ke RW**.

---

## 11. Lampiran: Daftar Gambar

Semua gambar berukuran desktop 1366×768.

| Kode | Judul | Perangkat | Nama file |
|---|---|---|---|
| KRT-01 | Menu samping Ketua RT | Desktop 1366×768 | `KRT-01-menu-samping-ketua-rt.png` |
| KRT-02 | Memilih peran Ketua RT | Desktop 1366×768 | `KRT-02-pilih-peran-ketua-rt.png` |
| KRT-03 | Dashboard RT bagian atas | Desktop 1366×768 | `KRT-03-dashboard-atas.png` |
| KRT-04 | Dashboard RT bagian bawah | Desktop 1366×768 | `KRT-04-dashboard-bawah.png` |
| KRT-05 | Manajemen Penduduk, tab Profil Keluarga | Desktop 1366×768 | `KRT-05-manajemen-penduduk-keluarga.png` |
| KRT-06 | Hasil pencarian penduduk | Desktop 1366×768 | `KRT-06-hasil-cari-penduduk.png` |
| KRT-07 | Jendela Export Data Penduduk | Desktop 1366×768 | `KRT-07-export-penduduk.png` |
| KRT-08 | Detail Keluarga | Desktop 1366×768 | `KRT-08-detail-keluarga.png` |
| KRT-09 | Tambah Keluarga, Profil Keluarga | Desktop 1366×768 | `KRT-09-tambah-keluarga-profil.png` |
| KRT-10 | Data Penduduk dan pemegang akun | Desktop 1366×768 | `KRT-10-tambah-keluarga-penduduk.png` |
| KRT-11 | Link aktivasi setelah keluarga disimpan | Desktop 1366×768 | `KRT-11-link-aktivasi-baru.png` |
| KRT-12 | Akun Keluarga belum diaktivasi | Desktop 1366×768 | `KRT-12-akun-keluarga-belum-aktif.png` |
| KRT-13 | Portal Persetujuan Dokumen | Desktop 1366×768 | `KRT-13-portal-persetujuan-dokumen.png` |
| KRT-14 | Detail Permintaan Dokumen | Desktop 1366×768 | `KRT-14-detail-permintaan-dokumen.png` |
| KRT-15 | Tanda Tangan dan Setujui | Desktop 1366×768 | `KRT-15-ttd-dan-setujui.png` |
| KRT-16 | Tolak Pengajuan Surat | Desktop 1366×768 | `KRT-16-tolak-pengajuan-surat.png` |
| KRT-17 | Riwayat Pengajuan | Desktop 1366×768 | `KRT-17-riwayat-pengajuan.png` |
| KRT-18 | Verifikasi Pengajuan Data | Desktop 1366×768 | `KRT-18-verifikasi-pengajuan-data.png` |
| KRT-19 | Detail Pengajuan Data | Desktop 1366×768 | `KRT-19-detail-pengajuan-data.png` |
| KRT-20 | Tolak Pengajuan Data | Desktop 1366×768 | `KRT-20-tolak-pengajuan-data.png` |
| KRT-21 | Detail Pindah Pemegang Akun | Desktop 1366×768 | `KRT-21-detail-pindah-akun.png` |
| KRT-22 | Link aktivasi pemegang akun baru | Desktop 1366×768 | `KRT-22-link-aktivasi-pindah-akun.png` |
| KRT-23 | Persetujuan Keuangan | Desktop 1366×768 | `KRT-23-persetujuan-keuangan.png` |
| KRT-24 | Setujui Laporan | Desktop 1366×768 | `KRT-24-setujui-laporan.png` |
| KRT-25 | Tolak Laporan | Desktop 1366×768 | `KRT-25-tolak-laporan.png` |
| KRT-26 | Laporan Keuangan, Daftar Laporan | Desktop 1366×768 | `KRT-26-laporan-keuangan-daftar.png` |
| KRT-27 | Statistik, penilaian dan kartu kunci | Desktop 1366×768 | `KRT-27-statistik-atas.png` |
| KRT-28 | Statistik, tren dan rincian | Desktop 1366×768 | `KRT-28-statistik-bawah.png` |
| KRT-29 | Pusat Informasi RT | Desktop 1366×768 | `KRT-29-pusat-informasi-rt.png` |
| KRT-30 | Pengaturan Tanda Tangan, tab Gambar | Desktop 1366×768 | `KRT-30-pengaturan-tanda-tangan.png` |
| KRT-31 | Tanda tangan dan stempel tersimpan | Desktop 1366×768 | `KRT-31-ttd-stempel-tersimpan.png` |

Total: 31 gambar.
