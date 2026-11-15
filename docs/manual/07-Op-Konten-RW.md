# Manual Book Operator Konten RW

Panduan untuk **Operator Konten RW** di WargaDigi 21 (https://wargadigi21.com). Operator Konten RW menulis dan mengelola **berita**, **agenda**, **pengumuman**, dan **galeri dokumentasi** tingkat RW, serta mengatur **kategori konten** yang dipakai seluruh RW dan RT.

<!-- AI-INSTRUKSI
- Ikuti konvensi blok di README.md (SIMULASI, SCREENSHOT, Hasilnya, Perhatian, Tips, Cerita berlanjut).
- Kode gambar awalan OKW, nomor urut sesuai Lampiran. Semua gambar desktop 1366x768.
- Akun: opkontenrw21 / password. Tokoh: Mbak Laras (fiktif). Nama tokoh tidak tampil di aplikasi, yang tampil hanya username.
- Data demo sudah memuat berita "Rencana Pemasangan CCTV Lingkungan" (Ditinjau), agenda "Rapat Pengurus dan Warga RW 21", "Bazar UMKM Warga" dan seterusnya. Untuk gambar formulir terisi, isi form lalu ambil gambar. Bila ingin membuat data baru, judul yang sama tetap boleh (alamat/slug dibuat otomatis unik).
- Urutan pengambilan gambar mengikuti urutan bab, karena status konten berubah setelah diajukan.
- Teks tombol "Ajukan ..." atau "Terbitkan ..." bergantung pada pengaturan Admin Aplikasi. Pada data demo: Berita perlu persetujuan (tombol "Ajukan Berita"), Agenda dan Pengumuman langsung terbit (tombol "Terbitkan Agenda" dan "Terbitkan Pengumuman").
-->

## Daftar Isi

1. Kenalan Dulu
   - 1.1 Tugas Operator Konten RW
   - 1.2 Tokoh dalam Panduan Ini
   - 1.3 Peta Menu
2. Mulai Menggunakan
   - 2.1 Masuk ke Aplikasi
   - 2.2 Membaca Dasbor RW
3. Berita
   - 3.1 Melihat dan Mencari Berita
   - 3.2 Menulis Berita Baru dan Menyimpan Draf
   - 3.3 Mengajukan Berita untuk Persetujuan (C-07 bagian 1)
   - 3.4 Memperbaiki Berita yang Diminta Revisi
   - 3.5 Menarik Pengajuan, Mengarsipkan, dan Menghapus Berita
4. Agenda
   - 4.1 Membuat Agenda dengan Konfirmasi Kehadiran (C-09 bagian 1)
   - 4.2 Melihat Detail dan Jumlah Warga yang Hadir (C-09 bagian 2)
   - 4.3 Menerbitkan, Menarik, Memperbaiki, dan Menghapus Agenda
5. Pengumuman
   - 5.1 Membuat Pengumuman Prioritas
   - 5.2 Mengarsipkan dan Membuka Arsip Pengumuman
6. Galeri Dokumentasi
   - 6.1 Mengunggah Foto Dokumentasi Kegiatan (C-09 bagian 3)
   - 6.2 Mengubah atau Menghapus Dokumentasi
7. Kategori Konten
   - 7.1 Melihat dan Menambah Kategori
   - 7.2 Mengubah, Menonaktifkan, dan Menghapus Kategori
8. Cerita Lengkap (Lintas Peran)
9. Masalah yang Sering Terjadi (FAQ)
10. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### 1.1 Tugas Operator Konten RW

Sebagai Operator Konten RW, Anda bertugas:

- Menulis **berita** RW. Berita perlu disetujui **Admin RW** sebelum tampil ke warga (sesuai pengaturan awal aplikasi).
- Membuat **agenda** kegiatan RW, termasuk lokasi di peta dan konfirmasi kehadiran warga.
- Membuat **pengumuman** untuk seluruh warga RW 21, termasuk pengumuman penting yang selalu tampil di urutan teratas.
- Mengunggah **foto dokumentasi** setelah kegiatan selesai.
- Mengelola **kategori konten**: daftar kategori yang dipakai berita, agenda, dan pengumuman, baik di RW maupun di semua RT.

Konten yang Anda buat adalah **konten RW** dan tampil ke semua warga. Anda hanya bisa melihat dan mengubah konten yang Anda buat sendiri.

> 💡 **Tips:** Operator Konten RT juga memakai halaman Berita, Agenda, Pengumuman, dan Galeri yang sama, tetapi hanya untuk RT-nya. Menu **Kategori Konten** hanya ada pada akun Operator Konten RW. Lihat `03-Op-Konten-RT.md`.

### 1.2 Tokoh dalam Panduan Ini

> 👤 **SIMULASI: Mbak Laras, Operator Konten RW**
>
> **Mbak Laras** (tokoh fiktif) membantu pengurus RW 21 menyebarkan kabar ke warga lewat aplikasi. Ia menulis berita, membuat agenda, menerbitkan pengumuman, dan menyimpan foto kegiatan.
>
> - **Masuk sebagai:** `opkontenrw21`, kata sandi `password`
> - **Cerita yang dimainkan:** C-07 (berita CCTV menunggu persetujuan) dan C-09 (agenda, konfirmasi hadir warga, galeri dokumentasi)
> - **Tokoh lain:** Admin RW (Bu Rina, fiktif) yang menyetujui berita, dan Pak Budi Santoso, warga RT 03

### 1.3 Peta Menu

Setelah masuk, menu ada di samping kiri. Di HP, tekan dulu tombol **☰** di pojok kiri atas.

| Menu | Gunanya |
|---|---|
| **Dashboard** | Ringkasan konten Anda, antrean revisi, kalender agenda |
| **Berita** | Menulis dan mengelola berita |
| **Agenda** | Membuat dan mengelola agenda kegiatan |
| **Pengumuman** | Membuat dan mengelola pengumuman |
| **Kategori Konten** | Mengatur kategori konten (hanya Op Konten RW) |
| **Galeri Dokumentasi** | Mengunggah foto kegiatan yang sudah selesai |
| **Pojok UMKM** | Membuka halaman Pojok UMKM (daftar usaha warga). Penjelasannya ada di `01-Warga.md` |
| **Keluar** | Keluar dari aplikasi |

> 📸 **SCREENSHOT OKW-01: Menu samping Operator Konten RW**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/dashboard
> - **Kondisi:** halaman Dashboard terbuka, menu samping tampil lengkap
> - **Area:** menu samping kiri dari atas sampai **Keluar**
> - **Sorot:** kotak merah mengelilingi seluruh daftar menu, nomor 1 sampai 7 di samping tiap menu
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-01-menu-samping.png`

---

## 2. Mulai Menggunakan

### 2.1 Masuk ke Aplikasi

Buka https://wargadigi21.com, lalu masuk dengan akun `opkontenrw21` dan kata sandi `password`. Langkah masuk, memilih peran (bila akun punya lebih dari satu), dan keluar dijelaskan di `00-Umum-Publik-dan-Login.md`.

Akun ini hanya punya satu peran, jadi setelah masuk Anda langsung diarahkan ke halaman kerja Operator Konten.

### 2.2 Membaca Dasbor RW

Halaman **Dashboard** (judul **Dasbor RW**) memberi gambaran cepat. Yang tampil:

- **Empat kartu angka:** **Berita Terbit**, **Pengumuman Aktif**, **Agenda Mendatang**, dan **Butuh Tindakan** (jumlah konten berstatus Revisi).
- **Antrean Revisi:** tabel berisi **Tipe**, **Judul**, **Catatan Revisi**, dan tombol **Perbaiki**. Bila kosong tertulis *Kerja bagus! Tidak ada konten yang butuh direvisi.*
- **Aktivitas Terkini:** tabel **Tipe**, **Judul**, **Status**, **Pembaruan**.
- **Sorotan Pengumuman:** pengumuman prioritas yang sudah terbit.
- **Kalender Agenda** dan **Agenda Terdekat (7 Hari Kedepan)**. Tombol **Kelola** membuka halaman Agenda.

Semua angka hanya menghitung konten **milik akun Anda**.

> 📸 **SCREENSHOT OKW-02: Dasbor RW**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/dashboard
> - **Kondisi:** data demo awal, belum ada perubahan
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada empat kartu angka, tabel **Antrean Revisi**, dan **Kalender Agenda**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-02-dasbor-rw.png`

> 💡 **Tips:** Mulai hari kerja dari kartu **Butuh Tindakan**. Bila angkanya lebih dari nol, buka **Antrean Revisi** dan tekan **Perbaiki** pada konten yang dikembalikan Admin RW.

---

## 3. Berita

Menu **Berita** (judul halaman **Manajemen Berita**) dipakai untuk menulis artikel berita RW. Alurnya: **Draf**, lalu **Ditinjau** oleh Admin RW, lalu **Terbit**. Bila perlu diperbaiki, statusnya **Revisi**. Berita yang sudah terbit bisa **Diarsipkan**.

Arti status berita:

| Label di layar | Artinya | Yang bisa Anda lakukan |
|---|---|---|
| ⚪ **Draf** | Masih ditulis, belum diajukan | Edit Berita, Ajukan Berita, Hapus |
| 🟡 **Ditinjau** | Menunggu persetujuan Admin RW | Batalkan Pengajuan |
| 🔴 **Revisi** | Dikembalikan Admin RW dengan catatan | Edit Berita, Ajukan Berita |
| 🟢 **Terbit** | Sudah tayang di Pusat Informasi warga | Arsipkan |
| ⚫ **Diarsipkan** | Dicabut dari tampilan warga, data tetap ada | Buka Arsip |

### 3.1 Melihat dan Mencari Berita

Di atas tabel ada lima kartu hitungan: **DRAF**, **DITINJAU**, **REVISI**, **TERBIT**, dan **ARSIP**. Di bawahnya ada penyaring:

- **Cari Judul** (ketik, hasil menyesuaikan)
- **Kategori** (pilihan **Semua Kategori** atau salah satu kategori berita)
- **Status** (**Semua Status**, **Terbit**, **Ditinjau**, **Draf**, **Revisi**, **Diarsipkan**)

Tabel berisi kolom **Sampul**, **Judul Berita**, **Kategori**, **Penulis**, **Status**, dan **Aksi** (tombol **Lihat Detail**). Satu halaman memuat 10 berita.

> 📸 **SCREENSHOT OKW-03: Daftar Manajemen Berita**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/berita
> - **Kondisi:** data demo awal, tanpa penyaring
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Buat Berita Baru**, lima kartu hitungan, dan kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-03-daftar-berita.png`

### 3.2 Menulis Berita Baru dan Menyimpan Draf

> 👤 **SIMULASI C-07: Berita Rencana Pemasangan CCTV (bagian 1 dari 3)**
>
> Pengurus RW 21 sepakat memasang kamera CCTV di pintu masuk lingkungan. **Mbak Laras** diminta menulis beritanya supaya warga tahu rencana ini. Ia menulis dulu sebagai draf, memeriksa isinya, baru mengajukannya.
>
> - **Masuk sebagai:** `opkontenrw21`, kata sandi `password`
> - **Judul:** Rencana Pemasangan CCTV Lingkungan
> - **Kategori:** Keamanan
> - **Foto utama:** foto gerbang atau pos jaga (JPG, PNG, atau WEBP, maksimal 5 MB)
> - **Selanjutnya:** pengajuan dan persetujuan ada di 3.3. Admin RW memeriksa berita ini di `05-Admin-RW.md`, cerita C-07.

**Langkah-langkah**

**1. Buka menu Berita.**
Di menu samping, klik **Berita**.

**2. Tekan Buat Berita Baru.**
Tombol hijau di kanan atas. Jendela **Buat Konten Berita** terbuka.

**3. Isi Judul Berita.**
Ketik: *Rencana Pemasangan CCTV Lingkungan*.

**4. Pilih Kategori.**
Buka kotak **Kategori** (tertulis *Pilih kategori*), lalu pilih **Keamanan**. Kategori berita diambil dari menu **Kategori Konten** (lihat Bab 7). Kategori **Organisasi** tidak muncul di sini karena tidak berlaku untuk berita.

**5. Tulis Isi Konten.**
Ketik isi berita di kotak editor yang bertuliskan *Tuliskan isi berita di sini...*. Di atas kotak ada tombol format seperti tebal, miring, daftar, dan tautan.

**6. Unggah Foto Utama.**
Pada **Foto Utama** (wajib), tarik gambar ke kotak atau klik kotak untuk memilih file. Format yang diterima JPG, PNG, WEBP, maksimal 5 MB. Aplikasi mengubah gambar menjadi format **WebP** secara otomatis supaya ringan. Setelah dipilih, gambar kecil tampil sebagai pratinjau. Klik pratinjau untuk memperbesar, atau tekan tanda silang untuk menggantinya.

> 📸 **SCREENSHOT OKW-04: Formulir Buat Konten Berita terisi**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/berita (jendela **Buat Konten Berita**)
> - **Kondisi:** judul, kategori Keamanan, isi, dan foto utama sudah terisi, belum ditekan simpan
> - **Area:** seluruh jendela formulir
> - **Sorot:** kotak merah pada **Judul Berita**, **Kategori**, **Isi Konten**, **Foto Utama**, dan tombol **Simpan sebagai Draft**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-04-form-buat-berita.png`

**7. Simpan sebagai draf.**
Tekan **Simpan sebagai Draft**. Berita tersimpan dan belum dilihat siapa pun.

### ✅ Hasilnya

- Muncul pesan **"Draft berita berhasil disimpan!"**
- Berita muncul di tabel dengan label **Draf**, dan angka pada kartu **DRAF** bertambah satu.

> 📸 **SCREENSHOT OKW-05: Berita tersimpan sebagai Draf**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/berita
> - **Kondisi:** setelah menyimpan draf, pesan sukses masih terlihat
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses, baris berita baru, dan label **Draf**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-05-berita-draf.png`

### ⚠️ Perhatian

- **"Isi konten berita wajib diisi."** Kotak editor masih kosong. Tulis isi berita lalu simpan lagi.
- **"Foto utama wajib diunggah."** Berita baru harus punya foto utama. Pilih gambar JPG, PNG, atau WEBP.
- **Foto ditolak atau tidak terunggah.** Ukuran file melebihi 5 MB atau formatnya bukan JPG, PNG, WEBP. Kecilkan ukurannya atau ubah formatnya.
- **"Kategori wajib dipilih."** Pilih salah satu kategori pada kotak **Kategori**.
- **"Kategori tidak valid atau tidak berlaku untuk jenis konten ini."** Kategori itu sudah dinonaktifkan atau tidak berlaku untuk berita. Pilih kategori lain, atau minta pengaturannya diperiksa di menu **Kategori Konten**.
- **Muncul tanya "Buang Perubahan?" saat menutup jendela.** Ada isian yang belum disimpan. Pilih **Lanjutkan Mengedit** untuk kembali, atau **Buang Perubahan** bila memang tidak jadi.

> 💡 **Tips:** Selama masih **Draf**, berita boleh diedit kapan saja lewat **Lihat Detail**, lalu **Edit Berita**. Pakai draf untuk menyusun tulisan panjang sedikit demi sedikit.

> ➡️ **Cerita berlanjut:** Lanjut ke 3.3 untuk mengajukan berita ini ke Admin RW.

### 3.3 Mengajukan Berita untuk Persetujuan (C-07 bagian 1)

> 👤 **SIMULASI C-07: Berita Rencana Pemasangan CCTV (bagian 2 dari 3)**
>
> Mbak Laras membaca ulang drafnya, sudah puas, lalu mengajukannya ke Admin RW. Selama menunggu, berita ini belum tampil ke warga.
>
> - **Masuk sebagai:** `opkontenrw21`
> - **Berita:** Rencana Pemasangan CCTV Lingkungan (status **Draf**)
> - **Selanjutnya:** Admin RW (Bu Rina) menyetujui atau meminta revisi. Lihat `05-Admin-RW.md`, C-07.

**Langkah-langkah**

**1. Buka detail berita.**
Di tabel **Manajemen Berita**, klik **Lihat Detail** pada berita yang berstatus **Draf**.

**2. Periksa isi di jendela Detail Berita.**
Tombol **Pratinjau Tampilan Warga** menunjukkan bagaimana berita terlihat di layar warga.

**3. Tekan Ajukan Berita.**
Di bagian bawah jendela, tekan tombol hijau **Ajukan Berita**.

**4. Konfirmasi.**
Muncul pertanyaan **"Ajukan berita ke Admin RW?"** dengan keterangan *Berita akan masuk tahap review dan menunggu persetujuan Admin RW.* Tekan **Ya, ajukan!**.

> 📸 **SCREENSHOT OKW-06: Konfirmasi pengajuan berita**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/berita (jendela **Detail Berita**)
> - **Kondisi:** berita berstatus Draf, kotak konfirmasi **Ajukan berita ke Admin RW?** tampil
> - **Area:** jendela detail dan kotak konfirmasi
> - **Sorot:** kotak merah pada tombol **Ya, ajukan!**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-06-konfirmasi-ajukan-berita.png`

### ✅ Hasilnya

- Muncul pesan **"Berita berhasil diajukan dan menunggu persetujuan Admin RW."**
- Status berita menjadi **Ditinjau** dan angka pada kartu **DITINJAU** bertambah.
- Admin RW menerima notifikasi bahwa ada berita yang perlu diperiksa.

> 📸 **SCREENSHOT OKW-07: Berita berstatus Ditinjau**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/berita
> - **Kondisi:** berita CCTV berstatus **Ditinjau**, pesan sukses terlihat
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses, label **Ditinjau**, dan kartu **DITINJAU**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-07-berita-ditinjau.png`

### ⚠️ Perhatian

- **Tombol Ajukan Berita tidak ada.** Tombol hanya muncul untuk berita berstatus **Draf** atau **Revisi**. Berita yang sudah **Ditinjau** hanya bisa ditarik dengan **Batalkan Pengajuan**.
- **Tombol bertuliskan "Terbitkan Berita", bukan "Ajukan Berita".** Admin Aplikasi mematikan persetujuan berita. Berita akan langsung terbit tanpa pemeriksaan Admin RW. Berita tidak mengirim notifikasi ke warga, jadi warga baru melihatnya di **Pusat Informasi**.
- **Berita sudah diajukan tetapi ingin diubah.** Tekan **Batalkan Pengajuan** dulu (lihat 3.5), perbaiki, lalu ajukan lagi.

> 💡 **Tips:** Kalau sudah yakin dari awal, di jendela **Buat Konten Berita** tekan **Ajukan Berita** langsung tanpa menyimpan draf dulu. Hasilnya sama.

> ➡️ **Cerita berlanjut:** Admin RW memeriksa berita ini. Lihat **`05-Admin-RW.md`, cerita C-07**. Bila disetujui, berita terbit dan tampil di **Pusat Informasi** warga (lihat `01-Warga.md`). Bila diminta revisi, lanjut ke 3.4.

### 3.4 Memperbaiki Berita yang Diminta Revisi

> 👤 **SIMULASI C-07: Berita Rencana Pemasangan CCTV (bagian 3 dari 3)**
>
> Admin RW mengembalikan berita dengan catatan agar jumlah titik kamera dan jadwal pemasangan dicantumkan. **Mbak Laras** memperbaiki lalu mengajukannya lagi.
>
> - **Masuk sebagai:** `opkontenrw21`
> - **Berita:** Rencana Pemasangan CCTV Lingkungan (status **Revisi**)
> - **Selanjutnya:** Admin RW memeriksa ulang. Lihat `05-Admin-RW.md`, C-07.

**Langkah-langkah**

**1. Lihat kabar revisi.**
Berita yang dikembalikan muncul di **Antrean Revisi** pada Dashboard, lengkap dengan **Catatan Revisi**. Tekan **Perbaiki**, atau buka menu **Berita** dan klik **Lihat Detail** pada berita berlabel **Revisi**.

**2. Baca catatan.**
Di bagian atas jendela **Detail Berita** ada kotak merah **"Catatan Revisi dari Admin RW:"** berisi catatan.

**3. Tekan Edit Berita.**
Ubah judul, isi, kategori, atau foto sesuai catatan. Foto lama tetap dipakai kalau Anda tidak mengunggah foto baru. Di atas formulir juga tampil kotak **Catatan Perbaikan dari Admin RW**.

**4. Simpan Perubahan.**
Tekan **Simpan Perubahan**. Pesan **"Draft berita berhasil diperbarui!"** muncul dan statusnya kembali menjadi **Draf**.

**5. Ajukan ulang.**
Buka **Lihat Detail** lagi, lalu tekan **Ajukan Berita** dan **Ya, ajukan!**.

> 📸 **SCREENSHOT OKW-08: Berita berstatus Revisi dengan catatan**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/berita (jendela **Detail Berita**)
> - **Kondisi:** berita berstatus **Revisi**, kotak **Catatan Revisi dari Admin RW** terlihat
> - **Area:** seluruh jendela detail
> - **Sorot:** kotak merah pada catatan revisi dan tombol **Edit Berita**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-08-berita-revisi.png`

### ✅ Hasilnya

- Setelah **Simpan Perubahan**: pesan **"Draft berita berhasil diperbarui!"**, status **Draf**.
- Setelah **Ajukan Berita**: pesan **"Berita berhasil diajukan dan menunggu persetujuan Admin RW."**, status **Ditinjau**.

> 📸 **SCREENSHOT OKW-09: Edit berita setelah revisi**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/berita (jendela **Edit Berita**)
> - **Kondisi:** jendela berjudul **Edit Berita** terbuka, kotak **Catatan Perbaikan dari Admin RW** tampil
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada catatan perbaikan dan tombol **Simpan Perubahan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-09-edit-berita.png`

### ⚠️ Perhatian

- **"Berita yang berstatus ... tidak dapat diedit sepihak."** Edit hanya untuk **Draf** dan **Revisi**. Untuk berita **Ditinjau**, tarik dulu dengan **Batalkan Pengajuan**. Berita **Terbit** tidak bisa diedit, hanya diarsipkan.
- **Foto utama kosong saat menyimpan.** Bila berita belum punya foto, unggah satu gambar. Bila sudah punya, tidak perlu unggah ulang.

> 💡 **Tips:** Setelah revisi, ringkas apa yang Anda ubah di kepala Anda, lalu sampaikan ke Admin RW secara lisan bila perlu. Aplikasi tidak menyediakan kolom balasan untuk catatan revisi.

### 3.5 Menarik Pengajuan, Mengarsipkan, dan Menghapus Berita

Tombol di bagian bawah jendela **Detail Berita** menyesuaikan status:

| Status | Tombol yang tersedia |
|---|---|
| **Draf** | **Edit Berita**, **Ajukan Berita**, **Hapus** |
| **Ditinjau** | **Batalkan Pengajuan** |
| **Revisi** | **Edit Berita**, **Ajukan Berita** |
| **Terbit** | **Arsipkan** |
| **Diarsipkan** | **Buka Arsip** |

**Menarik pengajuan.** Tekan **Batalkan Pengajuan**, lalu pada pertanyaan **"Batalkan Pengajuan?"** (*Berita akan ditarik kembali menjadi status Draf.*) tekan **Ya, batalkan!**. Pesan: **"Berita berhasil ditarik kembali ke Draft."**

**Mengarsipkan.** Pada berita **Terbit**, tekan **Arsipkan**. Pesan: **"Berita berhasil diarsipkan."** Berita tidak tampil lagi ke warga, tetapi datanya aman. Untuk menampilkannya lagi, tekan **Buka Arsip**. Pesan: **"Arsip berita berhasil dibuka."**

**Menghapus.** Hanya berita **Draf** yang bisa dihapus. Tekan **Hapus**, lalu pada **"Hapus Berita Ini?"** (*Data yang dihapus tidak dapat dikembalikan!*) tekan **Ya, hapus!**. Pesan: **"Berita berhasil dihapus."**

> 📸 **SCREENSHOT OKW-10: Tombol aksi pada berita Terbit**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/berita (jendela **Detail Berita**)
> - **Kondisi:** berita berstatus **Terbit**, misalnya "Kerja Bakti Bersih Lingkungan Sukses Digelar"
> - **Area:** bagian bawah jendela detail
> - **Sorot:** kotak merah pada tombol **Arsipkan** dan **Pratinjau Tampilan Warga**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-10-aksi-berita-terbit.png`

### ⚠️ Perhatian

- **Berita Terbit tidak bisa dihapus.** Gunakan **Arsipkan** bila ingin menariknya dari tampilan warga.
- **Berita orang lain tidak muncul.** Halaman ini hanya menampilkan berita yang Anda buat sendiri.

> 💡 **Tips:** Berita demo seperti "Perbaikan Jalan Utama Komplek Rampung" berstatus **Terbit**. Gunakan sebagai contoh untuk melihat bagaimana tabel dan tombol aksi berubah menurut status.

> ➡️ **Cerita berlanjut:** Bab 4 membahas agenda.

---

## 4. Agenda

Menu **Agenda** (judul halaman **Manajemen Agenda**) dipakai untuk membuat jadwal kegiatan warga. Status agenda sama dengan berita, tetapi **tidak ada Arsip**. Pada pengaturan awal, **agenda langsung terbit** tanpa persetujuan, jadi tombolnya bertuliskan **Terbitkan Agenda**.

### 4.1 Membuat Agenda dengan Konfirmasi Kehadiran (C-09 bagian 1)

> 👤 **SIMULASI C-09: Rapat Pengurus dan Warga RW 21 (bagian 1 dari 4)**
>
> Pengurus akan mengadakan rapat RW tiga hari lagi di Balai RW 21. **Mbak Laras** membuat agendanya dan mengaktifkan konfirmasi kehadiran supaya panitia tahu berapa kursi dan konsumsi yang disiapkan.
>
> - **Masuk sebagai:** `opkontenrw21`, kata sandi `password`
> - **Judul:** Rapat Pengurus dan Warga RW 21
> - **Kategori:** Organisasi
> - **Waktu:** tiga hari dari hari ini, 19.00 sampai 21.00
> - **Lokasi:** Balai RW 21
> - **Konfirmasi Kehadiran:** aktif
> - **Selanjutnya:** Pak Budi Santoso memberi konfirmasi hadir. Lihat `01-Warga.md`, C-09. Mbak Laras lalu melihat jumlah hadir di 4.2.

**Langkah-langkah**

**1. Buka menu Agenda.**
Di menu samping, klik **Agenda**.

**2. Tekan Buat Agenda Baru.**
Jendela **Buat Agenda Baru** terbuka.

**3. Isi Judul Agenda dan Kategori Agenda.**
Ketik judul *Rapat Pengurus dan Warga RW 21*. Pada **Kategori Agenda** pilih **Organisasi**. Kategori **Organisasi** tersedia untuk agenda, tetapi tidak untuk berita.

**4. Atur Tanggal dan Waktu.**
Isi **Tanggal**, lalu **Waktu** mulai dan selesai (dua kolom jam). Bila acara berlangsung lebih dari sehari, centang **Agenda Lebih Dari Satu Hari**. Tulisan **Tanggal** berubah menjadi **Tanggal Mulai** dan muncul kolom **Tanggal Akhir**.

**5. Isi Lokasi.**
Ketik *Balai RW 21* pada kolom **Lokasi**.

**6. Aktifkan peta (bila perlu).**
Geser tombol **Aktifkan Peta Lokasi** ("Tampilkan peta interaktif untuk menentukan titik koordinat presisi."). Peta muncul dengan judul **Tentukan Titik Peta**. Cari alamat lewat ikon pencarian di peta, klik titik lokasi, atau geser pin. Bila tidak perlu peta, biarkan tombol mati. Warga akan melihat peta di halaman detail agenda hanya bila titik ini diisi.

**7. Aktifkan Konfirmasi Kehadiran.**
Geser tombol **Konfirmasi Kehadiran** ("Aktifkan agar warga dapat mengonfirmasi kehadiran mereka sebelum acara dimulai.").

**8. Tulis Deskripsi Agenda.**
Isi kotak **Deskripsi Agenda** (wajib) dengan acara, pembahasan, dan hal yang perlu dibawa warga.

**9. Unggah Foto Utama / Flyer Agenda.**
Pilih gambar flyer atau foto (wajib, JPG, PNG, WEBP, maksimal 5 MB). Gambar diubah otomatis menjadi **WebP**.

> 📸 **SCREENSHOT OKW-11: Formulir Buat Agenda Baru terisi**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/agenda (jendela **Buat Agenda Baru**)
> - **Kondisi:** semua kolom terisi, **Konfirmasi Kehadiran** aktif, peta mati
> - **Area:** seluruh jendela formulir (gulir bila perlu, ambil dua gambar `OKW-11` dan `OKW-11b`)
> - **Sorot:** kotak merah pada **Kategori Agenda**, **Tanggal** dan **Waktu**, **Konfirmasi Kehadiran**, dan tombol **Terbitkan Agenda**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-11-form-buat-agenda.png`, `OKW-11b-form-buat-agenda-bawah.png`

> 📸 **SCREENSHOT OKW-12: Peta lokasi pada formulir agenda**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/agenda (jendela **Buat Agenda Baru**)
> - **Kondisi:** tombol **Aktifkan Peta Lokasi** menyala, pin sudah diletakkan di Balai RW 21
> - **Area:** bagian **Lokasi** sampai peta
> - **Sorot:** kotak merah pada tombol **Aktifkan Peta Lokasi** dan pin di peta
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-12-peta-lokasi-agenda.png`

**10. Terbitkan.**
Tekan **Terbitkan Agenda**. Muncul pertanyaan **"Terbitkan agenda sekarang?"** (*Agenda akan langsung tayang bagi warga tanpa persetujuan.*). Tekan **Ya, terbitkan!**.

Bila ingin menyusun dulu, tekan **Simpan ke Draf**.

### ✅ Hasilnya

- Muncul pesan **"Agenda berhasil diterbitkan."** (atau **"Agenda berhasil disimpan sebagai draf."** bila disimpan sebagai draf).
- Agenda berstatus **Terbit** dan langsung tampil di **Agenda Warga**. Seluruh warga diberi tahu lewat notifikasi aplikasi.
- Pada Dashboard, kartu **Agenda Mendatang** bertambah.

> 📸 **SCREENSHOT OKW-13: Agenda berhasil terbit**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/agenda
> - **Kondisi:** agenda baru berstatus **Terbit**, pesan sukses terlihat
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses dan label **Terbit**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-13-agenda-terbit.png`

### ⚠️ Perhatian

- **"Foto utama (banner) agenda wajib diunggah."** Agenda baru harus punya gambar. Pilih file lalu coba lagi. Pada jendela juga bisa muncul peringatan **Validasi Gagal** dengan teks *Foto utama agenda wajib diunggah.*
- **"Deskripsi agenda wajib diisi."** Kotak deskripsi masih kosong.
- **Tanggal akhir ditolak.** **Tanggal Akhir** tidak boleh lebih awal dari **Tanggal** mulai.
- **Tombol bertuliskan "Ajukan Agenda".** Admin Aplikasi mengaktifkan persetujuan agenda. Agenda berstatus **Ditinjau** dan menunggu **Admin RW**.
- **Peta tidak muncul.** Pastikan perangkat tersambung internet, karena peta dimuat dari layanan peta daring.

> 💡 **Tips:** Aktifkan **Konfirmasi Kehadiran** untuk rapat, pengajian, dan acara dengan konsumsi. Untuk acara terbuka seperti senam atau bazar, boleh dibiarkan mati.

> ➡️ **Cerita berlanjut:** Pak Budi Santoso (warga RT 03) memberi konfirmasi kehadiran. Lihat **`01-Warga.md`, cerita C-09**. Mbak Laras melihat jumlah hadir di 4.2.

### 4.2 Melihat Detail dan Jumlah Warga yang Hadir (C-09 bagian 2)

> 👤 **SIMULASI C-09: Rapat Pengurus dan Warga RW 21 (bagian 2 dari 4)**
>
> Sehari sebelum rapat, **Mbak Laras** membuka detail agenda untuk melihat berapa warga yang sudah menyatakan hadir. Angkanya dipakai panitia untuk menyiapkan kursi.
>
> - **Masuk sebagai:** `opkontenrw21`
> - **Agenda:** Rapat Pengurus dan Warga RW 21 (Terbit, konfirmasi kehadiran aktif)
> - **Sebelumnya:** Pak Budi Santoso menekan **Hadir** (lihat `01-Warga.md`, C-09)

**Langkah-langkah**

**1. Buka menu Agenda.**
Klik **Agenda** di menu samping. Penyaring tersedia: **Cari Judul**, **Tanggal**, **Kategori**, dan **Status** (**Draf**, **Ditinjau**, **Revisi**, **Terbit**).

**2. Klik Lihat Detail.**
Pilih baris agenda "Rapat Pengurus dan Warga RW 21".

**3. Baca Konfirmasi Kehadiran.**
Di jendela **Detail Agenda** ada bagian **Konfirmasi Kehadiran** dengan keterangan **Aktif**, dan lencana jumlah, misalnya **3 Warga Hadir**. Angka ini hanya menghitung warga yang menekan **Hadir**. Pilihan **Tidak Hadir** dan **Ragu-ragu** tidak ikut dihitung di sini.

**4. Cek bagian lain.**
Di jendela yang sama terlihat **Kategori Agenda**, **Tanggal**, **Waktu**, **Lokasi**, **Titik Lokasi Peta** (bila diisi), **Deskripsi Agenda**, dan **Lampiran Flyer/Poster**.

> 📸 **SCREENSHOT OKW-14: Detail agenda dan jumlah warga hadir**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/agenda (jendela **Detail Agenda**)
> - **Kondisi:** agenda "Rapat Pengurus dan Warga RW 21", Pak Budi sudah menekan Hadir sehingga jumlah lebih dari nol
> - **Area:** seluruh jendela detail
> - **Sorot:** kotak merah pada bagian **Konfirmasi Kehadiran** dan lencana **Warga Hadir**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-14-detail-agenda-kehadiran.png`

### ✅ Hasilnya

- Jumlah **Warga Hadir** terlihat jelas dan bertambah ketika warga menekan **Hadir**.
- Bila **Konfirmasi Kehadiran** mati, bagian ini menampilkan **Tidak Aktif** dan lencana jumlah tidak tampil.

### ⚠️ Perhatian

- **Jumlah hadir 0.** Belum ada warga yang menekan **Hadir**. Angka baru bertambah setelah warga menjawab di **Agenda Warga**.
- **Hanya angka, bukan daftar nama.** Halaman Operator Konten menampilkan jumlah, bukan siapa saja yang hadir.
- **Jumlah ini bukan jumlah peserta sebenarnya.** Itu hanya konfirmasi sebelum acara. Jumlah peserta sebenarnya Anda isi sendiri saat mengunggah dokumentasi (lihat 6.1).

> 💡 **Tips:** Tombol **Pratinjau Tampilan Warga** di bagian bawah jendela menunjukkan tampilan agenda di layar warga.

> ➡️ **Cerita berlanjut:** Setelah acara selesai, lanjut ke 6.1 untuk mengunggah dokumentasi.

### 4.3 Menerbitkan, Menarik, Memperbaiki, dan Menghapus Agenda

Tombol di bagian bawah jendela **Detail Agenda**:

| Status | Tombol yang tersedia |
|---|---|
| **Draf** | **Edit Agenda**, **Terbitkan Agenda** (atau **Ajukan Agenda**), **Hapus** |
| **Ditinjau** | **Batalkan Pengajuan** |
| **Revisi** | **Edit Agenda**, **Terbitkan Agenda** (atau **Ajukan Agenda**) |
| **Terbit** | tidak ada tombol ubah |

Hal penting:

- **Draf diterbitkan:** tekan **Terbitkan Agenda**, lalu **Ya, terbitkan!**. Pesan: **"Agenda berhasil diterbitkan."**
- **Menarik pengajuan** (bila persetujuan agenda aktif): **Batalkan Pengajuan**, lalu **Ya, batalkan!**. Pesan: **"Pengajuan agenda berhasil ditarik kembali."**
- **Menghapus:** hanya agenda **Draf**. Tekan **Hapus**, lalu **Ya, hapus!** pada **"Hapus Agenda Ini?"**. Pesan: **"Agenda berhasil dihapus."**
- **Agenda yang berstatus Revisi:** setelah diperbaiki dan disimpan, agenda **otomatis diajukan kembali** (atau langsung terbit bila persetujuan dimatikan). Pesan: **"Agenda berhasil diperbarui dan diterbitkan."** atau **"Agenda berhasil diperbarui dan diajukan, menunggu persetujuan Admin RW."**
- **Agenda Terbit tidak bisa diedit atau dihapus.** Pesan penolakan: **"Agenda yang sudah berstatus Publish tidak dapat diubah secara sepihak."** Bila ada kekeliruan, buat agenda baru dengan data yang benar dan sampaikan koreksinya lewat **Pengumuman**.

> 📸 **SCREENSHOT OKW-15: Daftar Manajemen Agenda**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/agenda
> - **Kondisi:** data demo, tanpa penyaring
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Buat Agenda Baru**, penyaring, dan kolom **Tanggal & Waktu**, **Lokasi**, **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-15-daftar-agenda.png`

### ⚠️ Perhatian

- **"Akses Ditolak ... tidak dapat diubah secara sepihak."** Agenda sudah **Terbit** atau **Ditinjau**. Hanya **Draf** dan **Revisi** yang bisa diedit.
- **"Hanya agenda berstatus Draf yang dapat dihapus."** Agenda yang sudah terbit tidak dapat dihapus.

> 💡 **Tips:** Kategori agenda menentukan warna agenda di kalender warga. Atur warnanya di **Kategori Konten** (Bab 7).

> ➡️ **Cerita berlanjut:** Bab 5 membahas pengumuman.

---

## 5. Pengumuman

Menu **Pengumuman** (judul halaman **Manajemen Pengumuman**) untuk kabar singkat yang perlu cepat diketahui warga. Pada pengaturan awal, **pengumuman langsung terbit**. Pengumuman bisa ditandai **penting** sehingga selalu tampil di urutan teratas aplikasi warga.

### 5.1 Membuat Pengumuman Prioritas

> 👤 **SIMULASI: Pengumuman Pemadaman Listrik Terjadwal**
>
> PLN memberi tahu akan ada pemadaman listrik hari Sabtu. **Mbak Laras** segera menerbitkan pengumuman penting supaya warga bersiap.
>
> - **Masuk sebagai:** `opkontenrw21`
> - **Judul:** Pemadaman Listrik Terjadwal
> - **Kategori:** Infrastruktur
> - **Prioritas:** ditandai penting
> - **Isi:** pemadaman terjadwal Sabtu pukul 09.00 sampai 14.00 WIB

**Langkah-langkah**

**1. Buka menu Pengumuman.**
Di menu samping, klik **Pengumuman**. Kartu hitungan di atas: **DRAF**, **DITINJAU**, **REVISI**, **TERBIT**. Penyaring: **Cari Judul** (juga mencari di isi), **Status** (termasuk **Diarsipkan**), **Prioritas** (**Penting** atau **Biasa**), dan **Tanggal**.

**2. Tekan Buat Pengumuman Baru.**

**3. Isi Judul Pengumuman dan Kategori.**
Ketik judul. Pada **Kategori** pilih *Infrastruktur*.

**4. Tandai penting.**
Geser tombol **Tandai Sebagai Pengumuman Penting** ("Pengumuman penting akan selalu ditampilkan di urutan teratas pada aplikasi warga.").

**5. Tulis Isi Pengumuman.**
Isi pada kotak editor.

**6. Terbitkan.**
Tekan **Terbitkan Pengumuman**, lalu **Ya, terbitkan!** pada **"Terbitkan pengumuman sekarang?"** (*Pengumuman akan langsung tayang bagi warga tanpa persetujuan.*). Untuk menyusun dulu, tekan **Simpan ke Draf**.

> 📸 **SCREENSHOT OKW-16: Formulir Buat Pengumuman Baru**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/pengumuman (jendela **Buat Pengumuman Baru**)
> - **Kondisi:** formulir terisi, tombol **Tandai Sebagai Pengumuman Penting** menyala
> - **Area:** seluruh jendela formulir
> - **Sorot:** kotak merah pada **Kategori**, tombol **Tandai Sebagai Pengumuman Penting**, dan **Terbitkan Pengumuman**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-16-form-buat-pengumuman.png`

### ✅ Hasilnya

- Muncul pesan **"Pengumuman berhasil diterbitkan."** (bila disimpan sebagai draf: **"Pengumuman baru berhasil disimpan sebagai draf."**).
- Di daftar, pengumuman tampil dengan label **PENTING** pada kolom **Prioritas** dan status **Terbit**. Pengumuman biasa berlabel **Biasa**.
- Pengumuman prioritas muncul di **Sorotan Pengumuman** pada Dashboard Anda dan di aplikasi warga.

> 📸 **SCREENSHOT OKW-17: Daftar Manajemen Pengumuman**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/pengumuman
> - **Kondisi:** pengumuman "Pemadaman Listrik Terjadwal" berlabel **PENTING** dan **Terbit**; "Rencana Perubahan Jadwal Posyandu" berstatus **Draf**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada label **PENTING**, label **Draf**, dan tombol **Buat Pengumuman Baru**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-17-daftar-pengumuman.png`

### ⚠️ Perhatian

- **"Isi pengumuman wajib diisi."** Kotak isi masih kosong.
- **Pengumuman Terbit tidak bisa diedit.** Pesan: *"Pengumuman yang sudah berstatus Publish tidak dapat diubah sepihak."* Arsipkan pengumuman lama (5.2), lalu buat yang baru.
- **Hanya Draf atau Revisi yang bisa dihapus.** Pesan: *"Hanya pengumuman berstatus Draf atau Revisi yang dapat dihapus. Pengumuman yang sudah terbit dapat diarsipkan."* Pesan sukses hapus: **"Pengumuman berhasil dihapus permanen."**
- **Pengumuman berstatus Revisi.** Tombol simpan di formulir edit berubah menjadi **Simpan & Terbitkan** (atau **Simpan & Ajukan Ulang** bila persetujuan aktif). Menekannya langsung menerbitkan atau mengajukan ulang.

> 💡 **Tips:** Pakai **Penting** hanya untuk hal yang mendesak, misalnya pemadaman, penipuan, atau perubahan jadwal mendadak. Bila semua pengumuman penting, tidak ada yang terlihat penting.

> ➡️ **Cerita berlanjut:** Lanjut ke 5.2.

### 5.2 Mengarsipkan dan Membuka Arsip Pengumuman

Pengumuman yang sudah tidak berlaku sebaiknya diarsipkan, bukan dihapus.

**Langkah-langkah**

**1. Buka detail.**
Klik **Lihat Detail** pada pengumuman berstatus **Terbit**.

**2. Tekan Arsipkan.**
Pesan: **"Pengumuman berhasil diarsipkan."** Pengumuman tidak tampil lagi ke warga.

**3. Membuka kembali.**
Pada pengumuman **Diarsipkan**, tekan **Buka Arsip**. Pesan: **"Arsip pengumuman berhasil dibuka."** Pengumuman kembali berstatus **Terbit**.

**Menarik pengajuan** (hanya bila persetujuan pengumuman aktif): pada status **Ditinjau**, tekan **Batalkan Pengajuan** lalu **Ya, batalkan!**. Pesan: **"Pengajuan pengumuman berhasil dibatalkan."**

> 📸 **SCREENSHOT OKW-18: Detail pengumuman dengan tombol Arsipkan**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/pengumuman (jendela **Detail Pengumuman**)
> - **Kondisi:** pengumuman berstatus **Terbit** terbuka
> - **Area:** seluruh jendela detail
> - **Sorot:** kotak merah pada **Tingkat Prioritas** dan tombol **Arsipkan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-18-detail-pengumuman.png`

### ✅ Hasilnya

- Pengumuman berlabel **Diarsipkan** di daftar dan tidak tampil ke warga.

### ⚠️ Perhatian

- **"Hanya pengumuman yang sudah terbit yang dapat diarsipkan."** Arsip hanya untuk pengumuman berstatus **Terbit**.
- **"Hanya pengumuman yang diarsipkan yang dapat dibuka kembali."** Tombol **Buka Arsip** hanya untuk yang berstatus **Diarsipkan**.

> 💡 **Tips:** Cari pengumuman lama dengan penyaring **Status** lalu pilih **Diarsipkan**.

> ➡️ **Cerita berlanjut:** Bab 6 membahas galeri dokumentasi.

---

## 6. Galeri Dokumentasi

Menu **Galeri Dokumentasi** (judul halaman **Galeri Dokumentasi Kegiatan**) untuk mengunggah foto bukti kegiatan. Halaman ini hanya menampilkan agenda yang **sudah Terbit dan jadwalnya sudah lewat**. Dokumentasi yang diunggah **langsung tayang**, tanpa persetujuan.

Di atas ada empat kartu: **AGENDA SELESAI**, **SUDAH DOKUMENTASI**, **BELUM DOKUMENTASI**, **TOTAL FOTO**. Penyaring: **Cari Judul Agenda**, **Tanggal**, **Kategori**, **Dokumentasi** (**Semua**, **Sudah Terdokumentasi**, **Belum Terdokumentasi**).

### 6.1 Mengunggah Foto Dokumentasi Kegiatan (C-09 bagian 3)

> 👤 **SIMULASI C-09: Bazar UMKM Warga (bagian 3 dari 4)**
>
> Bazar UMKM Warga di halaman Balai RW 21 sudah selesai. **Mbak Laras** mengunggah foto-foto kegiatan sebagai arsip dan agar warga bisa melihatnya.
>
> - **Masuk sebagai:** `opkontenrw21`
> - **Agenda:** Bazar UMKM Warga (Terbit, jadwal sudah lewat)
> - **Jumlah Warga Hadir:** 60 (dihitung panitia di lokasi)
> - **Foto:** 3 foto kegiatan (JPG, PNG, atau WEBP, maksimal 5 MB per foto)
> - **Selanjutnya:** dokumentasi yang sudah diunggah tampil di **Pusat Informasi** warga. Lihat `01-Warga.md`.

**Langkah-langkah**

**1. Buka menu Galeri Dokumentasi.**
Klik **Galeri Dokumentasi** di menu samping. Kartu agenda yang selesai tampil dalam daftar, 9 per halaman, berlabel **SELESAI**.

**2. Cari agenda yang belum didokumentasi.**
Pilih **Dokumentasi** lalu **Belum Terdokumentasi**. Pilih kartu agenda yang dimaksud.

**3. Tekan Upload Bukti.**
Jendela **Unggah Dokumentasi** terbuka. Di bagian atas tampil judul, tanggal, dan waktu agenda.

**4. Isi Jumlah Warga Hadir.**
Ketik angka, misalnya *60*. Kolom ini boleh dikosongkan.

**5. Unggah foto.**
Pada **Upload Foto** (wajib), tarik foto ke kotak atau klik untuk memilih. Maksimal **10 foto**, masing-masing maksimal 5 MB (JPG, PNG, WEBP). Pratinjau tampil di **Foto Terpilih**, dan penghitung menunjukkan misalnya *3 /10 foto dipilih*. Foto yang salah bisa dibuang dari pratinjau.

**6. Tekan Unggah Sekarang.**
Foto diubah otomatis menjadi **WebP** lalu disimpan.

> 📸 **SCREENSHOT OKW-19: Daftar Galeri Dokumentasi Kegiatan**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/galeri
> - **Kondisi:** ada kartu agenda yang belum didokumentasi dengan tombol **Upload Bukti**, dan kartu yang sudah (tombol **Lihat Detail**)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada empat kartu hitungan, penyaring **Dokumentasi**, dan tombol **Upload Bukti**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-19-daftar-galeri.png`
> - **Catatan penyusun:** pada data demo awal, "Kerja Bakti Bulanan" dan "Pelatihan Pertolongan Pertama" sudah punya dokumentasi, sedangkan "Bazar UMKM Warga" baru selesai beberapa jam setelah data dibuat. Ambil gambar ini setelah jadwal Bazar lewat, atau buat satu agenda uji dengan tanggal kemarin yang langsung diterbitkan lalu hapus datanya setelah gambar diambil.

> 📸 **SCREENSHOT OKW-20: Jendela Unggah Dokumentasi**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/galeri (jendela **Unggah Dokumentasi**)
> - **Kondisi:** **Jumlah Warga Hadir** terisi 60, tiga foto terpilih dan tampil di **Foto Terpilih**
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Jumlah Warga Hadir**, penghitung foto, dan tombol **Unggah Sekarang**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-20-unggah-dokumentasi.png`

### ✅ Hasilnya

- Muncul pesan **"Dokumentasi berhasil diunggah dan langsung tayang!"**
- Anda diarahkan ke halaman detail dokumentasi agenda tersebut (judul **Galeri Dokumentasi**). Di sana terlihat foto, **Jumlah Peserta**, **Operator Entry**, dan **Tanggal Unggah**.
- Kartu agenda di daftar berubah memakai tombol **Lihat Detail**. Kartu **SUDAH DOKUMENTASI** dan **TOTAL FOTO** bertambah.

> 📸 **SCREENSHOT OKW-21: Detail dokumentasi kegiatan**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/galeri/{id}
> - **Kondisi:** dokumentasi baru berhasil diunggah, pesan sukses terlihat
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada foto, tombol **Edit Detail** dan **Upload Foto Baru**, serta kartu **Detail Kegiatan** (**Jumlah Peserta**)
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-21-detail-dokumentasi.png`

### ⚠️ Perhatian

- **"Minimal 1 foto harus diunggah."** Pilih setidaknya satu foto.
- **"Maksimal 10 foto yang dapat diunggah."** Kurangi jumlah foto.
- **"Ukuran foto tidak boleh melebihi 5MB."** Kecilkan foto yang terlalu besar.
- **"Format gambar harus JPG, JPEG, PNG, atau WEBP."** Ubah format file.
- **"Agenda belum memenuhi syarat untuk dokumentasi."** Agenda belum berstatus **Terbit** atau jadwal selesainya belum lewat.
- **"Dokumentasi untuk agenda ini sudah ada."** Satu agenda hanya punya satu dokumentasi. Tambah foto lewat **Upload Foto Baru** di halaman detail (6.2).
- **Agenda tidak muncul di daftar.** Agenda **Draf**, **Ditinjau**, atau yang jadwalnya belum lewat tidak ditampilkan.

> 💡 **Tips:** Ambil foto yang menunjukkan suasana dan jumlah peserta, dan hindari foto yang memperlihatkan wajah anak-anak dari dekat tanpa izin orang tuanya.

> ➡️ **Cerita berlanjut:** Dokumentasi tampil bagi warga di **Pusat Informasi** (lihat `01-Warga.md`). Cerita C-09 selesai di sini, ringkasannya ada di Bab 8.

### 6.2 Mengubah atau Menghapus Dokumentasi

Pada halaman detail dokumentasi:

- **Upload Foto Baru:** menambah foto (total maksimal 10). Tekan **Unggah Sekarang**.
- **Edit Detail:** mengubah **Jumlah Peserta** dan mengelola foto. Pada **Kelola Foto Saat Ini**, tandai foto yang ingin dibuang. Foto baru **benar-benar terhapus saat Anda menekan tombol "Simpan Perubahan"**. Pesan: **"Dokumentasi berhasil diperbarui."** Judul, tanggal, dan lokasi hanya tampil sebagai keterangan dan tidak bisa diubah di sini.
- **Hapus Semua Dokumentasi:** tombol di kotak **Zona Berbahaya**. Seluruh foto dihapus permanen. Pesan: **"Dokumentasi berhasil dihapus."**

> 📸 **SCREENSHOT OKW-22: Edit Detail Kegiatan dan Zona Berbahaya**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/galeri/{id} (jendela **Edit Detail Kegiatan**)
> - **Kondisi:** dokumentasi "Kerja Bakti Bulanan" terbuka, jendela edit tampil
> - **Area:** seluruh jendela edit
> - **Sorot:** kotak merah pada **Jumlah Peserta**, **Kelola Foto Saat Ini**, dan tombol **Simpan Perubahan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-22-edit-dokumentasi.png`

### ⚠️ Perhatian

- **Dokumentasi dibuat operator lain.** Hanya pembuatnya yang boleh mengubah atau menghapus. Pesan: *"Anda tidak memiliki hak akses untuk mengubah dokumentasi ini."*
- **Foto lama terhapus.** Menghapus foto atau seluruh dokumentasi tidak bisa dibatalkan. Pastikan Anda masih menyimpan file aslinya.

> 💡 **Tips:** Gunakan **Hapus Semua Dokumentasi** hanya bila foto salah semua. Untuk foto yang keliru satu atau dua, cukup buang lewat **Edit Detail**.

> ➡️ **Cerita berlanjut:** Bab 7 membahas kategori konten.

---

## 7. Kategori Konten

Menu **Kategori Konten** mengatur satu daftar kategori untuk berita, agenda, dan pengumuman. Daftar ini berlaku untuk **RW dan semua RT**. Menu ini hanya ada pada akun Operator Konten RW.

Kategori awal pada data demo: **Sosial**, **Infrastruktur**, **Hiburan**, **Kesehatan**, **Keamanan** (berlaku untuk berita, agenda, pengumuman), dan **Organisasi** (hanya agenda dan pengumuman).

### 7.1 Melihat dan Menambah Kategori

> 👤 **SIMULASI: Menambah kategori "Pendidikan"**
>
> RW 21 akan mengadakan kegiatan belajar bersama anak-anak, tetapi belum ada kategori yang cocok. **Mbak Laras** menambah kategori **Pendidikan** untuk agenda dan pengumuman.
>
> - **Masuk sebagai:** `opkontenrw21`
> - **Nama kategori:** Pendidikan
> - **Berlaku untuk:** Agenda dan Pengumuman
> - **Warna:** Oranye
> - **Status:** Aktif

**Langkah-langkah**

**1. Buka menu Kategori Konten.**
Tabel menampilkan kolom **Kategori**, **Berlaku untuk**, **Dipakai** (jumlah konten yang memakai), **Status**, dan **Aksi**.

**2. Tekan Tambah Kategori.**
Jendela **Tambah Kategori** terbuka.

**3. Isi Nama kategori.**
Ketik *Pendidikan* (maksimal 60 karakter, tidak boleh sama dengan kategori lain).

**4. Pilih Berlaku untuk.**
Centang jenis konten yang boleh memakai kategori ini: **Berita**, **Agenda**, **Pengumuman**. Pilih minimal satu. Untuk contoh ini centang **Agenda** dan **Pengumuman**, lepas **Berita**.

**5. Pilih Warna.**
Pilihan: **Hijau**, **Biru**, **Kuning**, **Ungu**, **Tosca**, **Merah muda**, **Oranye**, **Abu-abu**. Warna dipakai pada kalender agenda warga.

**6. Atur status Aktif.**
Biarkan tombol **Aktif (muncul sebagai pilihan baru)** menyala.

**7. Simpan.**
Tekan **Simpan**.

> 📸 **SCREENSHOT OKW-23: Daftar Kategori Konten**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/kategori-konten
> - **Kondisi:** data demo awal
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Tambah Kategori**, kolom **Berlaku untuk**, dan kolom **Dipakai**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-23-daftar-kategori.png`

> 📸 **SCREENSHOT OKW-24: Jendela Tambah Kategori**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/kategori-konten (jendela **Tambah Kategori**)
> - **Kondisi:** nama *Pendidikan*, **Agenda** dan **Pengumuman** tercentang, warna **Oranye**
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Berlaku untuk**, **Warna**, dan tombol **Simpan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-24-tambah-kategori.png`

### ✅ Hasilnya

- Muncul pesan **"Kategori berhasil ditambahkan."**
- Kategori **Pendidikan** tampil di tabel, berstatus **Aktif**, dan langsung muncul sebagai pilihan di formulir Agenda dan Pengumuman, termasuk milik Operator Konten RT. Urutan kategori mengikuti urutan penambahan (kategori baru di akhir).

### ⚠️ Perhatian

- **"Nama kategori wajib diisi."** Kolom nama kosong.
- **"Nama kategori sudah ada."** Ganti nama, atau ubah kategori yang sudah ada.
- **"Nama kategori maksimal 60 karakter."** Persingkat nama.
- **"Pilih minimal satu jenis konten."** Centang minimal satu pada **Berlaku untuk**.
- **"Warna wajib dipilih."** Pilih satu warna.

> 💡 **Tips:** Beri warna berbeda untuk tiap kategori supaya kalender agenda warga mudah dibaca sekilas.

> ➡️ **Cerita berlanjut:** Lanjut ke 7.2 untuk mengubah dan merapikan kategori.

### 7.2 Mengubah, Menonaktifkan, dan Menghapus Kategori

**Mengubah.** Tekan ikon pensil (**Ubah**) pada baris kategori. Jendela **Ubah Kategori** sama dengan jendela tambah. Ubah nama, **Berlaku untuk**, **Warna**, atau status, lalu **Simpan**. Pesan: **"Kategori berhasil diperbarui."**

**Menonaktifkan.** Matikan tombol **Aktif**, lalu **Simpan**. Kategori tidak muncul sebagai pilihan baru, tetapi konten lama tetap menampilkannya.

**Menghapus.** Tekan ikon tempat sampah (**Hapus**). Muncul **"Hapus kategori ... ?"**; tekan **Hapus**. Pesan: **"Kategori berhasil dihapus."** Tombol hapus **tidak aktif** bila kategori sudah dipakai konten (kolom **Dipakai** lebih dari 0).

Catatan penting:

- **Mengganti nama ikut mengubah semua konten** yang memakai kategori itu.
- Kategori yang sudah dipakai tidak bisa dihapus. Pesan: *"Kategori sudah dipakai oleh konten dan tidak dapat dihapus. Nonaktifkan saja agar tidak muncul di pilihan baru."*
- Mengurangi centang **Berlaku untuk** hanya menghilangkan kategori dari pilihan baru pada jenis konten itu. Konten lama yang sudah memakainya tidak berubah.

> 📸 **SCREENSHOT OKW-25: Ubah Kategori**
> - **Akun:** opkontenrw21 (Op Konten RW)
> - **URL:** /op-konten/kategori-konten (jendela **Ubah Kategori**)
> - **Kondisi:** kategori **Organisasi** terbuka pada jendela ubah, **Berita** tidak tercentang
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada **Berlaku untuk**, tombol **Aktif**, dan tombol hapus yang nonaktif pada baris kategori di belakang jendela
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKW-25-ubah-kategori.png`

### ✅ Hasilnya

- Perubahan langsung berlaku di formulir Berita, Agenda, dan Pengumuman, baik milik Operator Konten RW maupun RT.

### ⚠️ Perhatian

- **Tombol hapus abu-abu dan tidak bisa ditekan.** Kategori sudah dipakai konten. Nonaktifkan saja.
- **Kategori hilang dari pilihan Berita.** Cek kolom **Berlaku untuk** dan status **Aktif** pada kategori itu.
- **Ganti nama tanpa sengaja.** Ubah lagi ke nama semula, karena semua konten ikut berubah.

> 💡 **Tips:** Hindari mengganti nama kategori menjadi arti yang sangat berbeda. Konten lama akan ikut berubah makna.

> ➡️ **Cerita berlanjut:** Bab 8 meringkas cerita lintas peran.

---

## 8. Cerita Lengkap (Lintas Peran)

| Kode | Cerita | Peran Anda | Rujukan |
|---|---|---|---|
| C-07 | Berita "Rencana Pemasangan CCTV Lingkungan" menunggu persetujuan, lalu terbit | Op Konten RW menulis dan mengajukan, Admin RW memutuskan | 3.2, 3.3, 3.4 di file ini; `05-Admin-RW.md`, C-07; `01-Warga.md` untuk tampilan di Pusat Informasi |
| C-09 | Agenda RW dibuat, warga konfirmasi hadir, dokumentasi diunggah ke galeri | Op Konten RW membuat agenda dan mengunggah galeri, warga memberi konfirmasi | 4.1, 4.2, 6.1 di file ini; `01-Warga.md`, C-09 |

**Ringkasan C-07**
1. Mbak Laras menulis berita sebagai **Draf** (3.2).
2. Ia menekan **Ajukan Berita**, status menjadi **Ditinjau** (3.3).
3. Admin RW memeriksa. Bila perlu perbaikan, status menjadi **Revisi** dengan catatan, Mbak Laras memperbaiki dan mengajukan ulang (3.4).
4. Setelah disetujui, berita **Terbit** dan tampil di **Pusat Informasi** warga.

**Ringkasan C-09**
1. Mbak Laras membuat agenda dengan **Konfirmasi Kehadiran** aktif dan menerbitkannya (4.1).
2. Warga memilih **Hadir**, **Tidak Hadir**, atau **Ragu-ragu** di **Agenda Warga**.
3. Mbak Laras memantau angka **Warga Hadir** (4.2).
4. Setelah acara selesai, ia mengunggah foto dan **Jumlah Warga Hadir** di **Galeri Dokumentasi** (6.1).

> ➡️ **Cerita berlanjut:** Kembali ke `README.md` untuk daftar cerita lain.

---

## 9. Masalah yang Sering Terjadi (FAQ)

**Saya tidak bisa membuka menu Kategori Konten.**
Menu ini hanya untuk akun Operator Konten RW. Akun Operator Konten RT tidak memilikinya.

**Berita saya tidak tampil di halaman warga.**
Periksa statusnya. **Draf**, **Ditinjau**, **Revisi**, dan **Diarsipkan** tidak tampil. Hanya **Terbit** yang tampil. Bila **Ditinjau**, tunggu Admin RW.

**Tombol saya bertuliskan "Terbitkan", bukan "Ajukan".**
Admin Aplikasi mengatur apakah suatu jenis konten perlu persetujuan. Bila dimatikan, konten langsung terbit saat Anda menekan tombolnya.

**Konten milik teman tidak terlihat di tabel saya.**
Setiap Operator Konten hanya melihat konten buatan sendiri. Konten RW dan RT juga dipisah.

**Agenda yang sudah terbit salah tanggal. Bagaimana memperbaikinya?**
Agenda **Terbit** tidak bisa diubah. Buat agenda baru yang benar dan terbitkan **Pengumuman** untuk menjelaskan perubahan.

**Warga bilang tidak ada tombol Hadir pada agenda.**
Cek bagian **Konfirmasi Kehadiran** pada detail agenda. Bila tertulis **Tidak Aktif**, fitur itu mati untuk agenda tersebut. Agenda terbit tidak bisa diubah, jadi buat agenda baru dengan **Konfirmasi Kehadiran** aktif bila memang diperlukan.

**Agenda tidak muncul di Galeri Dokumentasi.**
Galeri hanya menampilkan agenda yang berstatus **Terbit** dan jadwal selesainya sudah lewat.

**Foto gagal diunggah.**
Pastikan formatnya JPG, PNG, atau WEBP, ukuran maksimal 5 MB per foto, dan galeri maksimal 10 foto per agenda.

**Muncul "Buang Perubahan?" saat menutup formulir.**
Ada isian yang belum disimpan. Pilih **Lanjutkan Mengedit** atau **Buang Perubahan**.

**Menu Bantuan di bagian bawah tidak membuka apa pun.**
Menu itu belum memiliki halaman. Hubungi Admin Aplikasi bila butuh bantuan.

---

## 10. Lampiran: Daftar Gambar

Semua gambar desktop 1366×768. Nama file mengikuti kolom terakhir.

| Kode | Judul | Bab | Nama file |
|---|---|---|---|
| OKW-01 | Menu samping Operator Konten RW | 1.3 | `OKW-01-menu-samping.png` |
| OKW-02 | Dasbor RW | 2.2 | `OKW-02-dasbor-rw.png` |
| OKW-03 | Daftar Manajemen Berita | 3.1 | `OKW-03-daftar-berita.png` |
| OKW-04 | Formulir Buat Konten Berita terisi | 3.2 | `OKW-04-form-buat-berita.png` |
| OKW-05 | Berita tersimpan sebagai Draf | 3.2 | `OKW-05-berita-draf.png` |
| OKW-06 | Konfirmasi pengajuan berita | 3.3 | `OKW-06-konfirmasi-ajukan-berita.png` |
| OKW-07 | Berita berstatus Ditinjau | 3.3 | `OKW-07-berita-ditinjau.png` |
| OKW-08 | Berita berstatus Revisi dengan catatan | 3.4 | `OKW-08-berita-revisi.png` |
| OKW-09 | Edit berita setelah revisi | 3.4 | `OKW-09-edit-berita.png` |
| OKW-10 | Tombol aksi pada berita Terbit | 3.5 | `OKW-10-aksi-berita-terbit.png` |
| OKW-11 | Formulir Buat Agenda Baru terisi | 4.1 | `OKW-11-form-buat-agenda.png`, `OKW-11b-form-buat-agenda-bawah.png` |
| OKW-12 | Peta lokasi pada formulir agenda | 4.1 | `OKW-12-peta-lokasi-agenda.png` |
| OKW-13 | Agenda berhasil terbit | 4.1 | `OKW-13-agenda-terbit.png` |
| OKW-14 | Detail agenda dan jumlah warga hadir | 4.2 | `OKW-14-detail-agenda-kehadiran.png` |
| OKW-15 | Daftar Manajemen Agenda | 4.3 | `OKW-15-daftar-agenda.png` |
| OKW-16 | Formulir Buat Pengumuman Baru | 5.1 | `OKW-16-form-buat-pengumuman.png` |
| OKW-17 | Daftar Manajemen Pengumuman | 5.1 | `OKW-17-daftar-pengumuman.png` |
| OKW-18 | Detail pengumuman dengan tombol Arsipkan | 5.2 | `OKW-18-detail-pengumuman.png` |
| OKW-19 | Daftar Galeri Dokumentasi Kegiatan | 6.1 | `OKW-19-daftar-galeri.png` |
| OKW-20 | Jendela Unggah Dokumentasi | 6.1 | `OKW-20-unggah-dokumentasi.png` |
| OKW-21 | Detail dokumentasi kegiatan | 6.1 | `OKW-21-detail-dokumentasi.png` |
| OKW-22 | Edit Detail Kegiatan dan Zona Berbahaya | 6.2 | `OKW-22-edit-dokumentasi.png` |
| OKW-23 | Daftar Kategori Konten | 7.1 | `OKW-23-daftar-kategori.png` |
| OKW-24 | Jendela Tambah Kategori | 7.1 | `OKW-24-tambah-kategori.png` |
| OKW-25 | Ubah Kategori | 7.2 | `OKW-25-ubah-kategori.png` |

Jumlah: 25 gambar (26 file, karena OKW-11 memuat dua bagian).
