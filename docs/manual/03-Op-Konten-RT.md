# Manual Book Operator Konten RT

Panduan untuk **Operator Konten RT** di WargaDigi 21 (https://wargadigi21.com). Operator Konten RT menulis dan mengelola **berita**, **agenda**, **pengumuman**, dan **galeri dokumentasi** untuk **RT-nya sendiri**. Isinya hanya tampil ke warga RT tersebut (berita terbit juga tampil di beranda dengan keterangan asal RT). Semua pengajuan konten diperiksa oleh **Admin RW**.

<!-- AI-INSTRUKSI
- Ikuti konvensi blok di README.md (SIMULASI, SCREENSHOT, Hasilnya, Perhatian, Tips, Cerita berlanjut).
- Kode gambar awalan OKT, nomor urut sesuai Lampiran. Semua gambar desktop 1366x768.
- Akun: opkonten_3 (Mbak Nisa, fiktif, RT 03) dan opkontenrt01rw21 (Mas Dimas, fiktif, RT 01), kata sandi password. Nama tokoh tidak tampil di aplikasi, yang tampil hanya username.
- Data demo RT 03: berita "Penghijauan Taman RT 03" (Terbit), "Perbaikan Saluran Air RT 03" (Revisi, catatan "Mohon lengkapi waktu dan lokasi kegiatan."), agenda "Syukuran Pos Ronda Baru RT 03" (Ditinjau), pengumuman "Pendataan Warga Baru RT 03" (Terbit).
- Data demo RT 01: berita "Gotong Royong Pengecatan Pos RT 01" (Terbit), "Pemeriksaan Jentik Nyamuk RT 01" (Terbit), "Lomba Kebersihan Antar Gang" (Draf), agenda "Arisan Warga RT 01" (Terbit), pengumuman "Jadwal Pengangkutan Sampah RT 01" (Terbit).
- Teks tombol "Ajukan ..." atau "Terbitkan ..." bergantung pada pengaturan Admin Aplikasi. Pada data demo: Berita perlu persetujuan (tombol "Ajukan Berita"), Agenda dan Pengumuman langsung terbit (tombol "Terbitkan Agenda" dan "Terbitkan Pengumuman").
- Urutan pengambilan gambar mengikuti urutan bab, karena status konten berubah setelah diajukan. Gambar OKT-03, OKT-06 sampai OKT-09 memakai akun opkonten_3. Gambar OKT-10 dan OKT-11 memakai opkontenrt01rw21 (sebelum draf diajukan).
- Galeri Dokumentasi pada data demo kosong untuk kedua akun RT, karena belum ada agenda RT yang jadwalnya sudah lewat. Lihat catatan penyusun pada OKT-19.
-->

## Daftar Isi

1. Kenalan Dulu
   - 1.1 Tugas Operator Konten RT
   - 1.2 Tokoh dalam Panduan Ini
   - 1.3 Peta Menu
2. Mulai Menggunakan
   - 2.1 Masuk ke Aplikasi
   - 2.2 Membaca Dasbor RT
3. Berita RT
   - 3.1 Melihat dan Mencari Berita
   - 3.2 Menulis Berita Baru dan Menyimpan Draf
   - 3.3 Memperbaiki Berita yang Diminta Revisi (C-08 bagian 1)
   - 3.4 Melanjutkan Draf Berita (Contoh Mas Dimas)
   - 3.5 Menarik Pengajuan, Mengarsipkan, dan Menghapus Berita
4. Agenda RT
   - 4.1 Membuat Agenda RT
   - 4.2 Memantau Agenda yang Ditinjau dan Mengelola Agenda
5. Pengumuman RT
   - 5.1 Membuat Pengumuman RT (Contoh Mas Dimas)
   - 5.2 Mengarsipkan dan Membuka Arsip Pengumuman
6. Galeri Dokumentasi
7. Menu Lain dan Perbedaan dengan Operator Konten RW
8. Cerita Lengkap (Lintas Peran)
9. Masalah yang Sering Terjadi (FAQ)
10. Lampiran: Daftar Gambar

---

## 1. Kenalan Dulu

### 1.1 Tugas Operator Konten RT

Sebagai Operator Konten RT, Anda bertugas:

- Menulis **berita** untuk RT Anda. Berita perlu disetujui **Admin RW** sebelum tampil (sesuai pengaturan awal aplikasi).
- Membuat **agenda** kegiatan RT, termasuk lokasi di peta dan konfirmasi kehadiran warga.
- Membuat **pengumuman** untuk warga RT Anda, termasuk pengumuman penting yang tampil di urutan teratas.
- Mengunggah **foto dokumentasi** setelah kegiatan RT selesai.

Perbedaan penting dengan Operator Konten RW:

- **Konten Anda adalah konten RT.** Konten itu ditandai dengan asal **RT 03** (atau **RT 01**, sesuai akun) dan hanya tampil ke warga RT tersebut di **Pusat Informasi** dan **Agenda Warga**. Konten RW tampil ke semua warga, sedangkan konten RT tidak.
- **Yang menyetujui tetap Admin RW**, bukan Ketua RT. Ketua RT tidak menyetujui konten, hanya bisa memantau yang sudah terbit.
- **Tidak ada menu Kategori Konten.** Anda hanya memakai daftar kategori yang diatur Operator Konten RW (lihat Bab 7).
- Anda hanya melihat dan mengubah konten **yang Anda buat sendiri** di RT Anda.

> 💡 **Tips:** Halaman Berita, Agenda, Pengumuman, dan Galeri sama dengan milik Operator Konten RW. Bila ada bagian yang kurang jelas, lihat juga `07-Op-Konten-RW.md`.

### 1.2 Tokoh dalam Panduan Ini

> 👤 **SIMULASI: Mbak Nisa dan Mas Dimas, Operator Konten RT**
>
> **Mbak Nisa** (tokoh fiktif) memegang konten RT 03. Ia menulis berita dan agenda untuk warga RT 03, antara lain berita "Perbaikan Saluran Air RT 03". **Mas Dimas** (tokoh fiktif) memegang konten RT 01. Ia melanjutkan draf lomba kebersihan dan membuat pengumuman untuk warga RT 01.
>
> - **Mbak Nisa masuk sebagai:** `opkonten_3`, kata sandi `password`
> - **Mas Dimas masuk sebagai:** `opkontenrt01rw21`, kata sandi `password`
> - **Cerita yang dimainkan:** C-08 (berita RT 03 diperbaiki dan diajukan ulang), serta contoh draf berita dan pengumuman RT 01
> - **Tokoh lain:** Admin RW (Bu Rina, fiktif) yang memeriksa konten, dan Pak Budi Santoso, warga RT 03

### 1.3 Peta Menu

Setelah masuk, menu ada di samping kiri. Di HP, tekan dulu tombol **☰** di pojok kiri atas.

| Menu | Gunanya |
|---|---|
| **Dashboard** | Ringkasan konten Anda, antrean revisi, kalender agenda |
| **Berita** | Menulis dan mengelola berita RT |
| **Agenda** | Membuat dan mengelola agenda RT |
| **Pengumuman** | Membuat dan mengelola pengumuman RT |
| **Galeri Dokumentasi** | Mengunggah foto kegiatan RT yang sudah selesai |
| **Pojok UMKM** | Membuka halaman Pojok UMKM (daftar usaha warga). Penjelasannya ada di `01-Warga.md` |
| **Keluar** | Keluar dari aplikasi |

Menu **Kategori Konten** yang ada pada Operator Konten RW **tidak tampil** di akun Anda.

> 📸 **SCREENSHOT OKT-01: Menu samping Operator Konten RT**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/dashboard
> - **Kondisi:** halaman Dashboard terbuka, menu samping tampil lengkap
> - **Area:** menu samping kiri dari atas sampai **Keluar**
> - **Sorot:** kotak merah mengelilingi seluruh daftar menu, nomor 1 sampai 6 di samping tiap menu
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-01-menu-samping.png`

---

## 2. Mulai Menggunakan

### 2.1 Masuk ke Aplikasi

Buka https://wargadigi21.com, lalu masuk dengan akun `opkonten_3` (RT 03) atau `opkontenrt01rw21` (RT 01) dan kata sandi `password`. Langkah masuk, memilih peran (bila akun punya lebih dari satu), dan keluar dijelaskan di `00-Umum-Publik-dan-Login.md`.

Akun ini hanya punya satu peran, jadi setelah masuk Anda langsung diarahkan ke halaman kerja Operator Konten.

### 2.2 Membaca Dasbor RT

Halaman **Dashboard** berjudul **Dasbor** diikuti lencana asal, misalnya **RT 03**. Lencana ini mengingatkan bahwa Anda sedang mengelola konten RT tersebut. Yang tampil:

- **Empat kartu angka:** **Berita Terbit**, **Pengumuman Aktif**, **Agenda Mendatang**, dan **Butuh Tindakan** (jumlah konten berstatus Revisi).
- **Antrean Revisi:** tabel berisi **Tipe**, **Judul**, **Catatan Revisi**, dan tombol **Perbaiki**. Bila kosong tertulis *Kerja bagus! Tidak ada konten yang butuh direvisi.*
- **Aktivitas Terkini:** tabel **Tipe**, **Judul**, **Status**, **Pembaruan**.
- **Sorotan Pengumuman:** pengumuman prioritas yang sudah terbit.
- **Kalender Agenda** dan **Agenda Terdekat (7 Hari Kedepan)**. Tombol **Kelola** membuka halaman Agenda.

Semua angka hanya menghitung konten **milik akun Anda**.

Pada data demo awal, Mbak Nisa melihat **Butuh Tindakan** bernilai 1, karena berita "Perbaikan Saluran Air RT 03" berstatus Revisi. Mas Dimas melihat **Berita Terbit** bernilai 2 dan **Butuh Tindakan** bernilai 0.

> 📸 **SCREENSHOT OKT-02: Dasbor RT 03**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/dashboard
> - **Kondisi:** data demo awal, belum ada perubahan
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada lencana **RT 03** di judul, kartu **Butuh Tindakan**, dan tabel **Antrean Revisi** (baris "Perbaikan Saluran Air RT 03")
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-02-dasbor-rt.png`

> 💡 **Tips:** Mulai hari kerja dari kartu **Butuh Tindakan**. Bila angkanya lebih dari nol, buka **Antrean Revisi** dan tekan **Perbaiki** pada konten yang dikembalikan Admin RW.

---

## 3. Berita RT

Menu **Berita** (judul halaman **Manajemen Berita**) dipakai untuk menulis berita RT. Alurnya: **Draf**, lalu **Ditinjau** oleh Admin RW, lalu **Terbit**. Bila perlu diperbaiki, statusnya **Revisi**. Berita yang sudah terbit bisa **Diarsipkan**.

Arti status berita:

| Label di layar | Artinya | Yang bisa Anda lakukan |
|---|---|---|
| ⚪ **Draf** | Masih ditulis, belum diajukan | Edit Berita, Ajukan Berita, Hapus |
| 🟡 **Ditinjau** | Menunggu persetujuan Admin RW | Batalkan Pengajuan |
| 🔴 **Revisi** | Dikembalikan Admin RW dengan catatan | Edit Berita, Ajukan Berita |
| 🟢 **Terbit** | Sudah tayang untuk warga RT Anda | Arsipkan |
| ⚫ **Diarsipkan** | Dicabut dari tampilan warga, data tetap ada | Buka Arsip |

### 3.1 Melihat dan Mencari Berita

Di atas tabel ada lima kartu hitungan: **DRAF**, **DITINJAU**, **REVISI**, **TERBIT**, dan **ARSIP**. Di bawahnya ada penyaring:

- **Cari Judul** (ketik, hasil menyesuaikan)
- **Kategori** (pilihan **Semua Kategori** atau salah satu kategori berita)
- **Status** (**Semua Status**, **Terbit**, **Ditinjau**, **Draf**, **Revisi**, **Diarsipkan**)

Tabel berisi kolom **Sampul**, **Judul Berita**, **Kategori**, **Penulis**, **Status**, dan **Aksi** (tombol **Lihat Detail**). Satu halaman memuat 10 berita. Tabel hanya memuat berita buatan akun Anda di RT Anda. Bila belum ada, tampil tulisan *Belum ada berita yang dibuat.*

> 📸 **SCREENSHOT OKT-03: Daftar Manajemen Berita RT 03**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/berita
> - **Kondisi:** data demo awal, tanpa penyaring. Tampil "Penghijauan Taman RT 03" (Terbit) dan "Perbaikan Saluran Air RT 03" (Revisi)
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Buat Berita Baru**, lima kartu hitungan, dan kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-03-daftar-berita-rt.png`

### 3.2 Menulis Berita Baru dan Menyimpan Draf

> 👤 **SIMULASI: Berita Kerja Bakti Selokan RT 03**
>
> Warga RT 03 akan kerja bakti membersihkan selokan akhir pekan ini. **Mbak Nisa** menulis dulu beritanya sebagai draf, memeriksa isinya, baru mengajukannya ke Admin RW.
>
> - **Masuk sebagai:** `opkonten_3`, kata sandi `password`
> - **Judul:** Kerja Bakti Membersihkan Selokan RT 03
> - **Kategori:** Sosial
> - **Foto utama:** foto selokan atau warga yang bergotong royong (JPG, PNG, atau WEBP, maksimal 5 MB)
> - **Selanjutnya:** pengajuan ke Admin RW sama seperti di 3.3 (langkah **Ajukan Berita**)

**Langkah-langkah**

**1. Buka menu Berita.**
Di menu samping, klik **Berita**.

**2. Tekan Buat Berita Baru.**
Tombol hijau di kanan atas. Jendela **Buat Konten Berita** terbuka.

**3. Isi Judul Berita.**
Ketik: *Kerja Bakti Membersihkan Selokan RT 03*.

**4. Pilih Kategori.**
Buka kotak **Kategori** (tertulis *Pilih kategori*), lalu pilih **Sosial**. Daftar kategori berasal dari menu **Kategori Konten** milik Operator Konten RW, dan Anda tidak bisa menambah atau mengubahnya. Kategori **Organisasi** tidak muncul untuk berita karena tidak berlaku untuk berita.

**5. Tulis Isi Konten.**
Ketik isi berita di kotak editor yang bertuliskan *Tuliskan isi berita di sini...*. Cantumkan hari, jam, dan tempat kumpul supaya warga tidak bertanya lagi.

**6. Unggah Foto Utama.**
Pada **Foto Utama** (wajib), tarik gambar ke kotak atau klik kotak untuk memilih file. Aplikasi mengubah gambar menjadi format **WebP** secara otomatis supaya ringan. Setelah dipilih, gambar kecil tampil sebagai pratinjau. Klik pratinjau untuk memperbesar, atau tekan tanda silang untuk menggantinya.

> 📸 **SCREENSHOT OKT-04: Formulir Buat Konten Berita terisi**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/berita (jendela **Buat Konten Berita**)
> - **Kondisi:** judul, kategori Sosial, isi, dan foto utama sudah terisi, belum ditekan simpan
> - **Area:** seluruh jendela formulir
> - **Sorot:** kotak merah pada **Judul Berita**, **Kategori**, **Isi Konten**, **Foto Utama**, dan tombol **Simpan sebagai Draft**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-04-form-buat-berita.png`

**7. Simpan sebagai draf.**
Tekan **Simpan sebagai Draft**. Berita tersimpan dan belum dilihat siapa pun.

### ✅ Hasilnya

- Muncul pesan **"Draft berita berhasil disimpan!"**
- Berita muncul di tabel dengan label **Draf**, dan angka pada kartu **DRAF** bertambah satu.
- Berita otomatis bertanda sebagai konten RT Anda. Anda tidak perlu memilih RT sendiri.

> 📸 **SCREENSHOT OKT-05: Berita RT tersimpan sebagai Draf**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/berita
> - **Kondisi:** setelah menyimpan draf, pesan sukses masih terlihat
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses, baris berita baru, dan label **Draf**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-05-berita-draf.png`

### ⚠️ Perhatian

- **"Isi konten berita wajib diisi."** Kotak editor masih kosong. Tulis isi berita lalu simpan lagi.
- **"Foto utama wajib diunggah."** Berita baru harus punya foto utama. Pilih gambar JPG, PNG, atau WEBP.
- **Foto ditolak atau tidak terunggah.** Ukuran file melebihi 5 MB atau formatnya bukan JPG, PNG, WEBP.
- **"Kategori wajib dipilih."** Pilih salah satu kategori pada kotak **Kategori**.
- **"Kategori tidak valid atau tidak berlaku untuk jenis konten ini."** Kategori itu sudah dinonaktifkan atau tidak berlaku untuk berita. Pilih kategori lain. Bila kategori yang Anda butuhkan tidak ada, minta Operator Konten RW menambahkannya.
- **Muncul halaman "Akun Anda belum dikaitkan dengan RT. Hubungi admin."** Akun belum terhubung ke RT mana pun, sehingga tidak bisa mengelola konten. Hubungi Admin Aplikasi.
- **Muncul tanya "Buang Perubahan?" saat menutup jendela.** Ada isian yang belum disimpan. Pilih **Lanjutkan Mengedit** untuk kembali, atau **Buang Perubahan** bila memang tidak jadi.

> 💡 **Tips:** Selama masih **Draf**, berita boleh diedit kapan saja lewat **Lihat Detail**, lalu **Edit Berita**. Pakai draf untuk menyusun tulisan panjang sedikit demi sedikit.

> ➡️ **Cerita berlanjut:** Lanjut ke 3.3 untuk memperbaiki berita yang dikembalikan Admin RW dan mengajukannya ulang.

### 3.3 Memperbaiki Berita yang Diminta Revisi (C-08 bagian 1)

> 👤 **SIMULASI C-08: Perbaikan Saluran Air RT 03 (bagian 1 dari 2)**
>
> Admin RW mengembalikan berita **"Perbaikan Saluran Air RT 03"** dengan catatan *Mohon lengkapi waktu dan lokasi kegiatan.* **Mbak Nisa** melihatnya di Dashboard, menambahkan waktu dan lokasi perbaikan, lalu mengajukannya lagi.
>
> - **Masuk sebagai:** `opkonten_3`, kata sandi `password`
> - **Berita:** Perbaikan Saluran Air RT 03 (status **Revisi**, kategori Infrastruktur)
> - **Perubahan:** tambahkan waktu dan lokasi, misalnya *Sabtu pukul 07.00 WIB di gang utama RT 03*
> - **Selanjutnya:** Admin RW memeriksa ulang. Lihat `05-Admin-RW.md`, C-08.

**Langkah-langkah**

**1. Lihat kabar revisi.**
Berita yang dikembalikan muncul di **Antrean Revisi** pada Dashboard, lengkap dengan **Catatan Revisi**. Tekan **Perbaiki**, atau buka menu **Berita** dan klik **Lihat Detail** pada berita berlabel **Revisi**.

**2. Baca catatan.**
Di bagian atas jendela **Detail Berita** ada kotak merah **"Catatan Revisi dari Admin RW:"** berisi *Mohon lengkapi waktu dan lokasi kegiatan.*

> 📸 **SCREENSHOT OKT-06: Berita RT berstatus Revisi dengan catatan**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/berita (jendela **Detail Berita**)
> - **Kondisi:** berita "Perbaikan Saluran Air RT 03" berstatus **Revisi**, kotak **Catatan Revisi dari Admin RW** terlihat
> - **Area:** seluruh jendela detail
> - **Sorot:** kotak merah pada catatan revisi dan tombol **Edit Berita**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-06-berita-revisi.png`

**3. Tekan Edit Berita.**
Jendela **Edit Berita** terbuka, dengan kotak **Catatan Perbaikan dari Admin RW** di atas formulir. Tambahkan waktu dan lokasi pada isi berita. Foto lama tetap dipakai kalau Anda tidak mengunggah foto baru.

**4. Simpan Perubahan.**
Tekan **Simpan Perubahan**. Pesan **"Draft berita berhasil diperbarui!"** muncul dan statusnya kembali menjadi **Draf**. Berita **belum** terkirim ke Admin RW pada tahap ini.

> 📸 **SCREENSHOT OKT-07: Edit berita RT setelah revisi**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/berita (jendela **Edit Berita**)
> - **Kondisi:** jendela berjudul **Edit Berita** terbuka, kotak **Catatan Perbaikan dari Admin RW** tampil, isi berita sudah ditambah waktu dan lokasi
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada catatan perbaikan, bagian isi yang baru ditambah, dan tombol **Simpan Perubahan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-07-edit-berita.png`

**5. Ajukan ulang.**
Buka **Lihat Detail** lagi pada berita yang kini berlabel **Draf**, lalu tekan **Ajukan Berita**. Pada pertanyaan **"Ajukan berita ke Admin RW?"** (*Berita akan masuk tahap review dan menunggu persetujuan Admin RW.*) tekan **Ya, ajukan!**.

> 📸 **SCREENSHOT OKT-08: Konfirmasi pengajuan berita ke Admin RW**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/berita (jendela **Detail Berita**)
> - **Kondisi:** berita berstatus Draf, kotak konfirmasi **Ajukan berita ke Admin RW?** tampil
> - **Area:** jendela detail dan kotak konfirmasi
> - **Sorot:** kotak merah pada tombol **Ya, ajukan!**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-08-konfirmasi-ajukan-berita.png`

### ✅ Hasilnya

- Setelah **Simpan Perubahan**: pesan **"Draft berita berhasil diperbarui!"**, status **Draf**.
- Setelah **Ajukan Berita**: pesan **"Berita berhasil diajukan dan menunggu persetujuan Admin RW."**, status **Ditinjau**.
- Admin RW menerima notifikasi *Berita Menunggu Persetujuan* yang menyebut asal **RT 03**.
- Selama **Ditinjau**, berita belum tampil ke warga RT 03.

> 📸 **SCREENSHOT OKT-09: Berita RT berstatus Ditinjau**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/berita
> - **Kondisi:** "Perbaikan Saluran Air RT 03" berstatus **Ditinjau**, pesan sukses terlihat
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses, label **Ditinjau**, dan kartu **DITINJAU**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-09-berita-ditinjau.png`

### ⚠️ Perhatian

- **Berita langsung kembali ke Draf setelah Simpan Perubahan.** Ini normal untuk berita. Ingat menekan **Ajukan Berita** lagi, kalau tidak Admin RW tidak akan melihatnya. (Agenda dan pengumuman berbeda, lihat Bab 4 dan 5.)
- **"Berita yang berstatus ... tidak dapat diedit sepihak."** Edit hanya untuk **Draf** dan **Revisi**. Untuk berita **Ditinjau**, tarik dulu dengan **Batalkan Pengajuan**. Berita **Terbit** tidak bisa diedit, hanya diarsipkan.
- **Tombol bertuliskan "Terbitkan Berita", bukan "Ajukan Berita".** Admin Aplikasi mematikan persetujuan berita. Berita akan langsung terbit. Berita tidak mengirim notifikasi ke warga, jadi warga baru melihatnya di **Pusat Informasi**.
- **Foto utama kosong saat menyimpan.** Bila berita belum punya foto, unggah satu gambar. Bila sudah punya, tidak perlu unggah ulang.

> 💡 **Tips:** Isi berita sebaiknya langsung menjawab catatan Admin RW, misalnya dengan menulis hari, jam, dan lokasi secara jelas. Aplikasi tidak menyediakan kolom balasan untuk catatan revisi.

> ➡️ **Cerita berlanjut:** Admin RW memeriksa ulang berita ini. Lihat **`05-Admin-RW.md`, cerita C-08** (bagian 2). Bila disetujui, berita terbit dan tampil bagi warga RT 03 di **Pusat Informasi** dengan keterangan asal **RT 03** (lihat `01-Warga.md`).

### 3.4 Melanjutkan Draf Berita (Contoh Mas Dimas)

> 👤 **SIMULASI: Draf "Lomba Kebersihan Antar Gang" (RT 01)**
>
> Di RT 01 ada draf berita **"Lomba Kebersihan Antar Gang"** yang belum selesai. **Mas Dimas** melengkapi isinya dengan jadwal lomba dan kriteria penilaian, lalu mengajukannya ke Admin RW.
>
> - **Masuk sebagai:** `opkontenrt01rw21`, kata sandi `password`
> - **Berita:** Lomba Kebersihan Antar Gang (status **Draf**, kategori Sosial)
> - **Perubahan:** tambahkan tanggal lomba dan kriteria penilaian
> - **Selanjutnya:** Admin RW menyetujui atau meminta revisi di menu **Persetujuan Konten** (lihat `05-Admin-RW.md`)

**Langkah-langkah**

**1. Buka menu Berita.**
Mas Dimas melihat tiga berita RT 01 di tabel: "Gotong Royong Pengecatan Pos RT 01" dan "Pemeriksaan Jentik Nyamuk RT 01" (**Terbit**), serta "Lomba Kebersihan Antar Gang" (**Draf**). Berita RW dan berita RT lain tidak tampil.

> 📸 **SCREENSHOT OKT-10: Daftar Manajemen Berita RT 01**
> - **Akun:** opkontenrt01rw21 (Op Konten RT 01)
> - **URL:** /op-konten/berita
> - **Kondisi:** data demo awal, tanpa penyaring
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada baris "Lomba Kebersihan Antar Gang" dengan label **Draf**, dan kartu **DRAF** serta **TERBIT**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-10-daftar-berita-rt01.png`

**2. Buka draf.**
Klik **Lihat Detail** pada baris "Lomba Kebersihan Antar Gang", lalu tekan **Edit Berita**.

**3. Lengkapi isi.**
Tambahkan tanggal lomba dan kriteria penilaian pada kotak isi. Judul, kategori, dan foto utama boleh dibiarkan.

**4. Simpan atau langsung ajukan.**
Ada dua pilihan. Tekan **Simpan Perubahan** bila belum selesai, hasilnya pesan **"Draft berita berhasil diperbarui!"**. Bila sudah selesai, simpan dulu lalu buka **Lihat Detail** dan tekan **Ajukan Berita**, lalu **Ya, ajukan!**. Pesannya **"Berita berhasil diajukan dan menunggu persetujuan Admin RW."**

> 📸 **SCREENSHOT OKT-11: Edit draf Lomba Kebersihan Antar Gang**
> - **Akun:** opkontenrt01rw21 (Op Konten RT 01)
> - **URL:** /op-konten/berita (jendela **Edit Berita**)
> - **Kondisi:** jendela **Edit Berita** terbuka pada draf "Lomba Kebersihan Antar Gang", isi sudah ditambah
> - **Area:** seluruh jendela
> - **Sorot:** kotak merah pada kotak isi dan tombol **Simpan Perubahan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-11-edit-draf-lomba.png`

### ✅ Hasilnya

- Berita berpindah dari **Draf** ke **Ditinjau**, dan kartu **DITINJAU** bertambah.
- Admin RW menerima notifikasi bahwa ada berita dari **RT 01** yang menunggu persetujuan.

### ⚠️ Perhatian

- **Berita RT 03 tidak terlihat oleh Mas Dimas.** Setiap akun hanya melihat konten buatannya sendiri di RT-nya.
- **Tombol Ajukan Berita tidak ada pada jendela Edit.** Tombol itu hanya ada di jendela **Detail Berita** (untuk berita yang sudah tersimpan) dan di jendela **Buat Konten Berita** (untuk berita baru).

> 💡 **Tips:** Untuk berita yang sudah pasti siap, tekan **Ajukan Berita** langsung di jendela **Buat Konten Berita** tanpa menyimpan draf dulu. Hasilnya sama.

> ➡️ **Cerita berlanjut:** Admin RW memeriksa di `05-Admin-RW.md`. Setelah terbit, berita tampil untuk warga RT 01.

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

> 📸 **SCREENSHOT OKT-12: Tombol aksi pada berita RT yang Terbit**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/berita (jendela **Detail Berita**)
> - **Kondisi:** berita "Penghijauan Taman RT 03" berstatus **Terbit**
> - **Area:** bagian bawah jendela detail
> - **Sorot:** kotak merah pada tombol **Arsipkan** dan **Pratinjau Tampilan Warga**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-12-aksi-berita-terbit.png`

### ⚠️ Perhatian

- **Berita Terbit tidak bisa dihapus.** Gunakan **Arsipkan** bila ingin menariknya dari tampilan warga.
- **Pratinjau Tampilan Warga** menunjukkan tampilan berita di layar warga, bukan daftar warga yang bisa melihatnya. Berita RT hanya tampil untuk warga RT yang sama.

> 💡 **Tips:** Berita "Penghijauan Taman RT 03" berstatus **Terbit**. Gunakan sebagai contoh untuk melihat bagaimana tabel dan tombol aksi berubah menurut status.

> ➡️ **Cerita berlanjut:** Bab 4 membahas agenda.

---

## 4. Agenda RT

Menu **Agenda** (judul halaman **Manajemen Agenda**) dipakai untuk membuat jadwal kegiatan RT. Status agenda sama dengan berita, tetapi **tidak ada Arsip**. Pada pengaturan awal, **agenda langsung terbit** tanpa persetujuan, jadi tombolnya bertuliskan **Terbitkan Agenda**. Setelah terbit, notifikasi dikirim **hanya ke warga RT Anda**.

### 4.1 Membuat Agenda RT

> 👤 **SIMULASI: Senam Pagi Warga RT 03**
>
> **Mbak Nisa** ingin mengajak warga RT 03 senam pagi bersama di lapangan dekat pos ronda. Ia membuat agendanya dan mengaktifkan konfirmasi kehadiran supaya panitia tahu berapa orang yang datang.
>
> - **Masuk sebagai:** `opkonten_3`, kata sandi `password`
> - **Judul:** Senam Pagi Warga RT 03
> - **Kategori:** Kesehatan
> - **Waktu:** akhir pekan depan, 06.00 sampai 08.00
> - **Lokasi:** Lapangan dekat Pos Ronda RT 03
> - **Konfirmasi Kehadiran:** aktif

**Langkah-langkah**

**1. Buka menu Agenda dan tekan Buat Agenda Baru.**
Penyaring di halaman daftar: **Cari Judul**, **Tanggal**, **Kategori**, dan **Status**. Jendela **Buat Agenda Baru** terbuka setelah tombol ditekan.

**2. Isi Judul Agenda dan Kategori Agenda.**
Ketik judul *Senam Pagi Warga RT 03*. Pada **Kategori Agenda** pilih **Kesehatan**.

**3. Atur Tanggal dan Waktu.**
Isi **Tanggal**, lalu **Waktu** mulai dan selesai (dua kolom jam). Bila acara berlangsung lebih dari sehari, centang **Agenda Lebih Dari Satu Hari**. Tulisan **Tanggal** berubah menjadi **Tanggal Mulai** dan muncul kolom **Tanggal Akhir**.

**4. Isi Lokasi.**
Ketik *Lapangan dekat Pos Ronda RT 03* pada kolom **Lokasi**.

**5. Aktifkan peta (bila perlu).**
Geser tombol **Aktifkan Peta Lokasi** ("Tampilkan peta interaktif untuk menentukan titik koordinat presisi."). Peta muncul dengan judul **Tentukan Titik Peta**. Cari alamat lewat ikon pencarian di peta, klik titik lokasi, atau geser pin. Warga akan melihat peta di halaman detail agenda hanya bila titik ini diisi.

**6. Aktifkan Konfirmasi Kehadiran.**
Geser tombol **Konfirmasi Kehadiran** ("Aktifkan agar warga dapat mengonfirmasi kehadiran mereka sebelum acara dimulai.").

**7. Tulis Deskripsi Agenda.**
Isi kotak **Deskripsi Agenda** (wajib) dengan acara dan hal yang perlu dibawa warga, misalnya matras dan botol minum.

**8. Unggah Foto Utama / Flyer Agenda.**
Pilih gambar flyer atau foto (wajib, JPG, PNG, WEBP, maksimal 5 MB). Gambar diubah otomatis menjadi **WebP**.

> 📸 **SCREENSHOT OKT-13: Formulir Buat Agenda Baru terisi**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/agenda (jendela **Buat Agenda Baru**)
> - **Kondisi:** semua kolom terisi, **Konfirmasi Kehadiran** aktif, peta mati
> - **Area:** seluruh jendela formulir (gulir bila perlu, ambil dua gambar `OKT-13` dan `OKT-13b`)
> - **Sorot:** kotak merah pada **Kategori Agenda**, **Tanggal** dan **Waktu**, **Konfirmasi Kehadiran**, dan tombol **Terbitkan Agenda**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-13-form-buat-agenda.png`, `OKT-13b-form-buat-agenda-bawah.png`

**9. Terbitkan.**
Tekan **Terbitkan Agenda**. Muncul pertanyaan **"Terbitkan agenda sekarang?"** (*Agenda akan langsung tayang bagi warga tanpa persetujuan.*). Tekan **Ya, terbitkan!**.

Bila ingin menyusun dulu, tekan **Simpan ke Draf**.

### ✅ Hasilnya

- Muncul pesan **"Agenda berhasil diterbitkan."** (atau **"Agenda berhasil disimpan sebagai draf."** bila disimpan sebagai draf).
- Agenda berstatus **Terbit** dan tampil di **Agenda Warga** milik warga RT 03, dengan keterangan asal RT. Warga RT 03 diberi tahu lewat notifikasi aplikasi. Warga RT lain tidak menerimanya.
- Pada Dashboard, kartu **Agenda Mendatang** bertambah.

> 📸 **SCREENSHOT OKT-14: Agenda RT berhasil terbit**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/agenda
> - **Kondisi:** agenda "Senam Pagi Warga RT 03" berstatus **Terbit**, pesan sukses terlihat
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada pesan sukses dan label **Terbit**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-14-agenda-terbit.png`

### ⚠️ Perhatian

- **"Foto utama (banner) agenda wajib diunggah."** Agenda baru harus punya gambar. Pilih file lalu coba lagi.
- **"Deskripsi agenda wajib diisi."** Kotak deskripsi masih kosong.
- **Tanggal akhir ditolak.** **Tanggal Akhir** tidak boleh lebih awal dari **Tanggal** mulai.
- **Tombol bertuliskan "Ajukan Agenda".** Admin Aplikasi mengaktifkan persetujuan agenda. Agenda berstatus **Ditinjau** dan menunggu **Admin RW**.
- **Peta tidak muncul.** Pastikan perangkat tersambung internet, karena peta dimuat dari layanan peta daring.

> 💡 **Tips:** Aktifkan **Konfirmasi Kehadiran** untuk arisan, rapat RT, dan acara dengan konsumsi. Untuk acara terbuka, boleh dibiarkan mati. Jumlah warga yang menekan **Hadir** terlihat di jendela **Detail Agenda** pada bagian **Konfirmasi Kehadiran**.

> ➡️ **Cerita berlanjut:** Lanjut ke 4.2 untuk memantau agenda yang masih ditinjau dan mengelola agenda.

### 4.2 Memantau Agenda yang Ditinjau dan Mengelola Agenda

Pada data demo, agenda **"Syukuran Pos Ronda Baru RT 03"** berstatus **Ditinjau**. Artinya agenda itu sudah diajukan dan menunggu Admin RW. Selama **Ditinjau**, Mbak Nisa tidak bisa mengubahnya. Satu-satunya tombol adalah **Batalkan Pengajuan**.

Tombol di bagian bawah jendela **Detail Agenda**:

| Status | Tombol yang tersedia |
|---|---|
| **Draf** | **Edit Agenda**, **Terbitkan Agenda** (atau **Ajukan Agenda**), **Hapus** |
| **Ditinjau** | **Batalkan Pengajuan** |
| **Revisi** | **Edit Agenda**, **Terbitkan Agenda** (atau **Ajukan Agenda**) |
| **Terbit** | tidak ada tombol ubah |

Hal penting:

- **Menarik pengajuan:** **Batalkan Pengajuan**, lalu **Ya, batalkan!**. Pesan: **"Pengajuan agenda berhasil ditarik kembali."** Agenda kembali menjadi **Draf**.
- **Menghapus:** hanya agenda **Draf**. Tekan **Hapus**, lalu **Ya, hapus!** pada **"Hapus Agenda Ini?"**. Pesan: **"Agenda berhasil dihapus."**
- **Agenda yang berstatus Revisi:** setelah diperbaiki dan disimpan, agenda **otomatis diajukan kembali** (atau langsung terbit bila persetujuan dimatikan). Pesan: **"Agenda berhasil diperbarui dan diterbitkan."** atau **"Agenda berhasil diperbarui dan diajukan, menunggu persetujuan Admin RW."** Berbeda dengan berita, Anda tidak perlu menekan tombol ajukan lagi.
- **Agenda Terbit tidak bisa diedit atau dihapus.** Pesan penolakan: **"Agenda yang sudah berstatus Publish tidak dapat diubah secara sepihak."** Bila ada kekeliruan, buat agenda baru dengan data yang benar dan sampaikan koreksinya lewat **Pengumuman**.

> 📸 **SCREENSHOT OKT-15: Daftar Manajemen Agenda RT 03**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/agenda
> - **Kondisi:** data demo awal. Tampil "Syukuran Pos Ronda Baru RT 03" berstatus **Ditinjau**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tombol **Buat Agenda Baru**, penyaring, baris "Syukuran Pos Ronda Baru RT 03", dan kolom **Status**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-15-daftar-agenda-rt.png`

### ⚠️ Perhatian

- **"Akses Ditolak ... tidak dapat diubah secara sepihak."** Agenda sudah **Terbit** atau **Ditinjau**. Hanya **Draf** dan **Revisi** yang bisa diedit.
- **"Hanya agenda berstatus Draf yang dapat dihapus."** Agenda yang sudah terbit tidak dapat dihapus.
- **Agenda RW tidak ada di tabel.** Agenda RW hanya bisa dikelola Operator Konten RW. Agenda RT lain juga tidak terlihat.

> 💡 **Tips:** Kategori agenda menentukan warna agenda di kalender warga. Warna diatur Operator Konten RW di menu **Kategori Konten**.

> ➡️ **Cerita berlanjut:** Bab 5 membahas pengumuman.

---

## 5. Pengumuman RT

Menu **Pengumuman** (judul halaman **Manajemen Pengumuman**) untuk kabar singkat yang perlu cepat diketahui warga RT. Pada pengaturan awal, **pengumuman langsung terbit**, dan notifikasi dikirim **hanya ke warga RT Anda**. Pengumuman bisa ditandai **penting** sehingga tampil di urutan teratas aplikasi warga.

### 5.1 Membuat Pengumuman RT (Contoh Mas Dimas)

> 👤 **SIMULASI: Pengumuman Pembagian Kantong Sampah RT 01**
>
> RT 01 akan membagikan kantong sampah organik dan anorganik hari Minggu. **Mas Dimas** menerbitkan pengumuman supaya warga RT 01 mengambil jatahnya di rumah Ketua RT. Pengumuman ini penting, jadi ia menandainya sebagai prioritas.
>
> - **Masuk sebagai:** `opkontenrt01rw21`, kata sandi `password`
> - **Judul:** Pembagian Kantong Sampah RT 01
> - **Kategori:** Sosial
> - **Prioritas:** ditandai penting
> - **Isi:** pembagian hari Minggu pukul 08.00 sampai 11.00 di rumah Ketua RT 01, satu kantong per keluarga

**Langkah-langkah**

**1. Buka menu Pengumuman.**
Di menu samping, klik **Pengumuman**. Kartu hitungan di atas: **DRAF**, **DITINJAU**, **REVISI**, **TERBIT**. Penyaring: **Cari Judul** (juga mencari di isi), **Status** (termasuk **Diarsipkan**), **Prioritas** (**Penting** atau **Biasa**), dan **Tanggal**.

**2. Tekan Buat Pengumuman Baru.**

**3. Isi Judul Pengumuman dan Kategori.**
Ketik judul. Pada **Kategori** pilih *Sosial*.

**4. Tandai penting.**
Geser tombol **Tandai Sebagai Pengumuman Penting** ("Pengumuman penting akan selalu ditampilkan di urutan teratas pada aplikasi warga.").

**5. Tulis Isi Pengumuman.**
Isi pada kotak editor.

**6. Terbitkan.**
Tekan **Terbitkan Pengumuman**, lalu **Ya, terbitkan!** pada **"Terbitkan pengumuman sekarang?"** (*Pengumuman akan langsung tayang bagi warga tanpa persetujuan.*). Untuk menyusun dulu, tekan **Simpan ke Draf**.

> 📸 **SCREENSHOT OKT-16: Formulir Buat Pengumuman Baru RT 01**
> - **Akun:** opkontenrt01rw21 (Op Konten RT 01)
> - **URL:** /op-konten/pengumuman (jendela **Buat Pengumuman Baru**)
> - **Kondisi:** formulir terisi, tombol **Tandai Sebagai Pengumuman Penting** menyala
> - **Area:** seluruh jendela formulir
> - **Sorot:** kotak merah pada **Kategori**, tombol **Tandai Sebagai Pengumuman Penting**, dan **Terbitkan Pengumuman**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-16-form-buat-pengumuman.png`

### ✅ Hasilnya

- Muncul pesan **"Pengumuman berhasil diterbitkan."** (bila disimpan sebagai draf: **"Pengumuman baru berhasil disimpan sebagai draf."**).
- Di daftar, pengumuman tampil dengan label **PENTING** pada kolom **Prioritas** dan status **Terbit**. Pengumuman biasa berlabel **Biasa**.
- Warga RT 01 menerima notifikasi *Pengumuman Baru*. Warga RT lain tidak menerimanya dan tidak melihat pengumuman ini di **Pusat Informasi**.
- Pengumuman prioritas muncul di **Sorotan Pengumuman** pada Dashboard Mas Dimas.

> 📸 **SCREENSHOT OKT-17: Daftar Manajemen Pengumuman RT 01**
> - **Akun:** opkontenrt01rw21 (Op Konten RT 01)
> - **URL:** /op-konten/pengumuman
> - **Kondisi:** "Pembagian Kantong Sampah RT 01" berlabel **PENTING** dan **Terbit**; "Jadwal Pengangkutan Sampah RT 01" berlabel **Biasa** dan **Terbit**
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada label **PENTING**, label **Biasa**, dan tombol **Buat Pengumuman Baru**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-17-daftar-pengumuman-rt.png`

### ⚠️ Perhatian

- **"Isi pengumuman wajib diisi."** Kotak isi masih kosong.
- **Pengumuman Terbit tidak bisa diedit.** Pesan: *"Pengumuman yang sudah berstatus Publish tidak dapat diubah sepihak."* Arsipkan pengumuman lama (5.2), lalu buat yang baru.
- **Hanya Draf atau Revisi yang bisa dihapus.** Pesan: *"Hanya pengumuman berstatus Draf atau Revisi yang dapat dihapus. Pengumuman yang sudah terbit dapat diarsipkan."* Pesan sukses hapus: **"Pengumuman berhasil dihapus permanen."**
- **Pengumuman berstatus Revisi.** Tombol simpan di formulir edit berubah menjadi **Simpan & Terbitkan** (atau **Simpan & Ajukan Ulang** bila persetujuan aktif). Menekannya langsung menerbitkan atau mengajukan ulang.
- **Pengumuman tidak sampai ke warga RT lain.** Ini memang disengaja. Untuk kabar yang berlaku bagi seluruh RW, minta Operator Konten RW membuatnya.

> 💡 **Tips:** Pakai **Penting** hanya untuk hal yang mendesak, misalnya perubahan jadwal mendadak atau penipuan. Bila semua pengumuman penting, tidak ada yang terlihat penting.

> ➡️ **Cerita berlanjut:** Lanjut ke 5.2.

### 5.2 Mengarsipkan dan Membuka Arsip Pengumuman

Pengumuman yang sudah tidak berlaku sebaiknya diarsipkan, bukan dihapus. Misalnya pengumuman "Jadwal Pengangkutan Sampah RT 01" setelah jadwalnya berganti.

**Langkah-langkah**

**1. Buka detail.**
Klik **Lihat Detail** pada pengumuman berstatus **Terbit**.

**2. Tekan Arsipkan.**
Pesan: **"Pengumuman berhasil diarsipkan."** Pengumuman tidak tampil lagi ke warga.

**3. Membuka kembali.**
Pada pengumuman **Diarsipkan**, tekan **Buka Arsip**. Pesan: **"Arsip pengumuman berhasil dibuka."** Pengumuman kembali berstatus **Terbit**.

**Menarik pengajuan** (hanya bila persetujuan pengumuman aktif): pada status **Ditinjau**, tekan **Batalkan Pengajuan** lalu **Ya, batalkan!**. Pesan: **"Pengajuan pengumuman berhasil dibatalkan."**

> 📸 **SCREENSHOT OKT-18: Detail pengumuman RT dengan tombol Arsipkan**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/pengumuman (jendela **Detail Pengumuman**)
> - **Kondisi:** pengumuman "Pendataan Warga Baru RT 03" berstatus **Terbit** terbuka
> - **Area:** seluruh jendela detail
> - **Sorot:** kotak merah pada **Tingkat Prioritas** dan tombol **Arsipkan**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-18-detail-pengumuman.png`

### ✅ Hasilnya

- Pengumuman berlabel **Diarsipkan** di daftar dan tidak tampil ke warga.

### ⚠️ Perhatian

- **"Hanya pengumuman yang sudah terbit yang dapat diarsipkan."** Arsip hanya untuk pengumuman berstatus **Terbit**.
- **"Hanya pengumuman yang diarsipkan yang dapat dibuka kembali."** Tombol **Buka Arsip** hanya untuk yang berstatus **Diarsipkan**.

> 💡 **Tips:** Cari pengumuman lama dengan penyaring **Status** lalu pilih **Diarsipkan**.

> ➡️ **Cerita berlanjut:** Bab 6 membahas galeri dokumentasi.

---

## 6. Galeri Dokumentasi

Menu **Galeri Dokumentasi** (judul halaman **Galeri Dokumentasi Kegiatan**) tersedia juga untuk Operator Konten RT. Cara kerjanya sama dengan Operator Konten RW: halaman ini hanya menampilkan agenda **RT Anda** yang **sudah Terbit dan jadwalnya sudah lewat**. Dokumentasi yang diunggah **langsung tayang**, tanpa persetujuan.

Di atas ada empat kartu: **AGENDA SELESAI**, **SUDAH DOKUMENTASI**, **BELUM DOKUMENTASI**, **TOTAL FOTO**. Penyaring: **Cari Judul Agenda**, **Tanggal**, **Kategori**, **Dokumentasi** (**Semua**, **Sudah Terdokumentasi**, **Belum Terdokumentasi**).

Pada data demo awal, kedua akun RT melihat halaman kosong dengan tulisan **"Belum ada agenda yang telah selesai."** dan keterangan *Agenda yang berstatus "Terbit" dan jadwalnya sudah lewat akan muncul di sini.* Penyebabnya, agenda RT pada data demo ("Arisan Warga RT 01" dan "Syukuran Pos Ronda Baru RT 03") belum berstatus Terbit dengan jadwal yang sudah lewat. Agenda RW tidak ditampilkan di halaman ini.

Setelah ada agenda RT yang selesai, langkah mengunggah foto sama dengan yang dijelaskan di `07-Op-Konten-RW.md`, bab 6:

**1.** Klik **Galeri Dokumentasi**, pilih kartu agenda yang berlabel **SELESAI**, lalu tekan **Upload Bukti**.
**2.** Isi **Jumlah Warga Hadir** (boleh kosong), lalu pilih foto pada **Upload Foto** (wajib, maksimal 10 foto, masing-masing maksimal 5 MB, format JPG, PNG, atau WEBP).
**3.** Tekan **Unggah Sekarang**. Pesan: **"Dokumentasi berhasil diunggah dan langsung tayang!"** Anda diarahkan ke halaman detail dokumentasi.
**4.** Di halaman detail, **Upload Foto Baru** menambah foto, **Edit Detail** mengubah **Jumlah Peserta** dan membuang foto (tekan **Simpan Perubahan**), dan **Hapus Semua Dokumentasi** menghapus seluruh foto secara permanen.

> 📸 **SCREENSHOT OKT-19: Galeri Dokumentasi Kegiatan RT (kosong pada data demo)**
> - **Akun:** opkonten_3 (Op Konten RT 03)
> - **URL:** /op-konten/galeri
> - **Kondisi:** data demo awal, belum ada agenda RT yang selesai
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada empat kartu hitungan (semua bernilai 0) dan tulisan **Belum ada agenda yang telah selesai.**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `OKT-19-galeri-kosong.png`
> - **Catatan penyusun:** bila ingin gambar galeri yang berisi, buat satu agenda RT uji dengan tanggal kemarin lalu terbitkan, ambil gambar daftar dan jendela **Unggah Dokumentasi**, lalu hapus data ujinya. Gambar jendela unggah yang sama sudah ada di `07-Op-Konten-RW.md` (OKW-19 sampai OKW-21).

### ⚠️ Perhatian

- **Agenda tidak muncul di daftar.** Agenda **Draf**, **Ditinjau**, atau yang jadwalnya belum lewat tidak ditampilkan. Agenda RW dan agenda RT lain juga tidak ditampilkan.
- **"Agenda belum memenuhi syarat untuk dokumentasi."** Agenda belum berstatus **Terbit** atau jadwal selesainya belum lewat.
- **"Dokumentasi untuk agenda ini sudah ada."** Satu agenda hanya punya satu dokumentasi. Tambah foto lewat **Upload Foto Baru** di halaman detail.
- **"Minimal 1 foto harus diunggah."**, **"Maksimal 10 foto yang dapat diunggah."**, **"Ukuran foto tidak boleh melebihi 5MB."**, dan **"Format gambar harus JPG, JPEG, PNG, atau WEBP."** adalah pesan bila foto tidak sesuai aturan.
- **Foto yang dihapus tidak bisa dikembalikan.** Simpan file aslinya.

> 💡 **Tips:** Hindari foto yang memperlihatkan wajah anak-anak dari dekat tanpa izin orang tuanya.

> ➡️ **Cerita berlanjut:** Bab 7 membahas menu lain dan perbedaan dengan Operator Konten RW.

---

## 7. Menu Lain dan Perbedaan dengan Operator Konten RW

**Kategori Konten.** Menu ini hanya ada pada akun Operator Konten RW, dan tidak muncul di menu samping Anda. Daftar kategori (**Sosial**, **Infrastruktur**, **Hiburan**, **Kesehatan**, **Keamanan**, **Organisasi**) berlaku untuk RW dan semua RT. Anda hanya memilih dari daftar itu di formulir Berita, Agenda, dan Pengumuman. Bila butuh kategori baru, misalnya **Pendidikan**, sampaikan ke Operator Konten RW. Kategori baru langsung muncul di formulir Anda.

**Pojok UMKM.** Menu ini membuka halaman Pojok UMKM (daftar usaha warga) yang sama dengan yang dilihat warga. Penjelasannya ada di `01-Warga.md`.

Ringkasan perbedaan:

| Hal | Operator Konten RT | Operator Konten RW |
|---|---|---|
| Asal konten | RT akun Anda (misalnya **RT 03**) | **RW** |
| Yang bisa melihat konten | Warga RT yang sama | Semua warga |
| Notifikasi agenda dan pengumuman | Hanya warga RT Anda | Semua warga |
| Yang menyetujui | Admin RW | Admin RW |
| Kategori Konten | Hanya memakai | Mengelola |
| Galeri Dokumentasi | Hanya agenda RT sendiri | Hanya agenda RW sendiri |
| Lencana pada Dasbor | **RT 03** atau **RT 01** | **RW** |

> 📸 Tidak ada gambar khusus di bab ini.

> ➡️ **Cerita berlanjut:** Bab 8 meringkas cerita lintas peran.

---

## 8. Cerita Lengkap (Lintas Peran)

| Kode | Cerita | Peran Anda | Rujukan |
|---|---|---|---|
| C-08 | Berita RT 03 "Perbaikan Saluran Air RT 03" dikembalikan untuk revisi, lalu diajukan ulang | Op Konten RT memperbaiki dan mengajukan ulang, Admin RW memutuskan | 3.3 di file ini; `05-Admin-RW.md`, C-08; `01-Warga.md` untuk tampilan di Pusat Informasi warga RT 03 |

**Ringkasan C-08**
1. Admin RW mengembalikan berita dengan catatan *Mohon lengkapi waktu dan lokasi kegiatan.* Status berita menjadi **Revisi**.
2. Mbak Nisa melihatnya di **Antrean Revisi** Dashboard, menekan **Edit Berita**, menambahkan waktu dan lokasi, lalu **Simpan Perubahan**. Status menjadi **Draf**.
3. Ia membuka **Lihat Detail**, menekan **Ajukan Berita** dan **Ya, ajukan!**. Status menjadi **Ditinjau**.
4. Admin RW memeriksa ulang (lihat `05-Admin-RW.md`, C-08). Bila disetujui, berita **Terbit** dan tampil bagi warga RT 03.

**Contoh tambahan (tanpa kode cerita)**
- **Draf berita RT 01:** Mas Dimas melengkapi dan mengajukan "Lomba Kebersihan Antar Gang" (3.4).
- **Pengumuman RT 01:** Mas Dimas menerbitkan "Pembagian Kantong Sampah RT 01" sebagai pengumuman penting (5.1).

> ➡️ **Cerita berlanjut:** Kembali ke `README.md` untuk daftar cerita lain.

---

## 9. Masalah yang Sering Terjadi (FAQ)

**Saya tidak bisa membuka menu Kategori Konten.**
Menu ini hanya untuk akun Operator Konten RW. Akun Operator Konten RT tidak memilikinya. Minta Operator Konten RW menambahkan kategori yang Anda butuhkan.

**Muncul pesan "Akun Anda belum dikaitkan dengan RT. Hubungi admin."**
Akun Anda belum terhubung ke RT. Konten RT hanya bisa dikelola oleh akun yang terhubung ke satu RT. Hubungi Admin Aplikasi.

**Berita saya tidak tampil di halaman warga.**
Periksa statusnya. **Draf**, **Ditinjau**, **Revisi**, dan **Diarsipkan** tidak tampil. Hanya **Terbit** yang tampil. Berita RT hanya tampil di **Pusat Informasi** warga RT yang sama.

**Warga RT lain tidak bisa melihat pengumuman atau agenda RT saya.**
Itu memang aturannya. Konten RT hanya untuk warga RT tersebut. Konten untuk seluruh RW dibuat oleh Operator Konten RW.

**Berita saya sudah diperbaiki tetapi tidak dilihat Admin RW.**
Setelah **Simpan Perubahan**, berita kembali menjadi **Draf**. Buka **Lihat Detail**, lalu tekan **Ajukan Berita**.

**Tombol saya bertuliskan "Terbitkan", bukan "Ajukan".**
Admin Aplikasi mengatur apakah suatu jenis konten perlu persetujuan. Bila dimatikan, konten langsung terbit saat Anda menekan tombolnya.

**Siapa yang menyetujui konten RT, Ketua RT atau Admin RW?**
Admin RW. Ketua RT tidak menyetujui konten, hanya memantau yang sudah terbit.

**Konten milik teman tidak terlihat di tabel saya.**
Setiap Operator Konten hanya melihat konten buatan sendiri. Konten RW dan RT juga dipisah.

**Agenda yang sudah terbit salah tanggal. Bagaimana memperbaikinya?**
Agenda **Terbit** tidak bisa diubah. Buat agenda baru yang benar dan terbitkan **Pengumuman** untuk menjelaskan perubahan.

**Galeri Dokumentasi saya kosong.**
Galeri hanya menampilkan agenda RT Anda yang berstatus **Terbit** dan jadwal selesainya sudah lewat. Agenda RW tidak masuk ke galeri RT.

**Foto gagal diunggah.**
Pastikan formatnya JPG, PNG, atau WEBP, ukuran maksimal 5 MB per foto, dan galeri maksimal 10 foto per agenda.

**Muncul "Buang Perubahan?" saat menutup formulir.**
Ada isian yang belum disimpan. Pilih **Lanjutkan Mengedit** atau **Buang Perubahan**.

---

## 10. Lampiran: Daftar Gambar

Semua gambar desktop 1366×768. Nama file mengikuti kolom terakhir.

| Kode | Judul | Bab | Nama file |
|---|---|---|---|
| OKT-01 | Menu samping Operator Konten RT | 1.3 | `OKT-01-menu-samping.png` |
| OKT-02 | Dasbor RT 03 | 2.2 | `OKT-02-dasbor-rt.png` |
| OKT-03 | Daftar Manajemen Berita RT 03 | 3.1 | `OKT-03-daftar-berita-rt.png` |
| OKT-04 | Formulir Buat Konten Berita terisi | 3.2 | `OKT-04-form-buat-berita.png` |
| OKT-05 | Berita RT tersimpan sebagai Draf | 3.2 | `OKT-05-berita-draf.png` |
| OKT-06 | Berita RT berstatus Revisi dengan catatan | 3.3 | `OKT-06-berita-revisi.png` |
| OKT-07 | Edit berita RT setelah revisi | 3.3 | `OKT-07-edit-berita.png` |
| OKT-08 | Konfirmasi pengajuan berita ke Admin RW | 3.3 | `OKT-08-konfirmasi-ajukan-berita.png` |
| OKT-09 | Berita RT berstatus Ditinjau | 3.3 | `OKT-09-berita-ditinjau.png` |
| OKT-10 | Daftar Manajemen Berita RT 01 | 3.4 | `OKT-10-daftar-berita-rt01.png` |
| OKT-11 | Edit draf Lomba Kebersihan Antar Gang | 3.4 | `OKT-11-edit-draf-lomba.png` |
| OKT-12 | Tombol aksi pada berita RT yang Terbit | 3.5 | `OKT-12-aksi-berita-terbit.png` |
| OKT-13 | Formulir Buat Agenda Baru terisi | 4.1 | `OKT-13-form-buat-agenda.png`, `OKT-13b-form-buat-agenda-bawah.png` |
| OKT-14 | Agenda RT berhasil terbit | 4.1 | `OKT-14-agenda-terbit.png` |
| OKT-15 | Daftar Manajemen Agenda RT 03 | 4.2 | `OKT-15-daftar-agenda-rt.png` |
| OKT-16 | Formulir Buat Pengumuman Baru RT 01 | 5.1 | `OKT-16-form-buat-pengumuman.png` |
| OKT-17 | Daftar Manajemen Pengumuman RT 01 | 5.1 | `OKT-17-daftar-pengumuman-rt.png` |
| OKT-18 | Detail pengumuman RT dengan tombol Arsipkan | 5.2 | `OKT-18-detail-pengumuman.png` |
| OKT-19 | Galeri Dokumentasi Kegiatan RT (kosong pada data demo) | 6 | `OKT-19-galeri-kosong.png` |

Jumlah: 19 gambar (20 file, karena OKT-13 memuat dua bagian).
