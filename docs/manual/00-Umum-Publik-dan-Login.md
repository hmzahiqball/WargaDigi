# Manual Book WargaDigi 21: Umum, Halaman Publik, dan Cara Masuk

> **Untuk siapa:** semua orang. Pengunjung yang belum punya akun, warga, pengurus RT dan RW, operator, sampai pengelola aplikasi. Buku ini menjelaskan hal yang sama-sama dipakai semua peran: halaman yang bisa dibuka tanpa masuk, cara masuk dan keluar, memilih peran, notifikasi, dan memasang aplikasi di HP.
>
> **Alamat web:** https://wargadigi21.com
>
> **Dokumen pendamping:** setiap peran punya buku sendiri. Daftarnya ada di Bab 1.2.

<!-- AI-INSTRUKSI: Ubah file ini menjadi dokumen Word bergambar.
1. Judul "# ..." menjadi Judul Dokumen, "## Bab" menjadi Heading 1, "### Sub-bab" menjadi Heading 2.
2. Blok "> 👤 SIMULASI" menjadi kotak biru muda. Blok "✅ Hasilnya" menjadi kotak hijau muda. Blok "⚠️ Perhatian" menjadi kotak kuning. Blok "> 💡 Tips" menjadi kotak abu-abu. Blok "> ➡️ Cerita berlanjut" menjadi teks miring.
3. Setiap blok "> 📸 SCREENSHOT <KODE>" diganti gambar dengan nama file yang tertulis di baris "Nama file". Beri keterangan "Gambar <KODE>: <judul>" di bawah gambar. Jangan cetak isi daftar Akun/URL/Kondisi/Area/Sorot di dokumen akhir; itu hanya petunjuk pengambilan gambar. Bila ada dua nama file (desktop dan mobile), tempatkan keduanya berdampingan.
4. Komentar HTML seperti ini tidak dicetak.
5. Tabel dipertahankan sebagai tabel Word. Daftar Isi dibuat otomatis dari Heading.
6. Bab 5 (Panduan Pengambilan Screenshot) hanya untuk penyusun dokumen. Jangan sertakan di dokumen untuk pembaca umum. -->

---

## Daftar Isi

1. Mengenal WargaDigi 21
   - 1.1 Apa itu WargaDigi 21
   - 1.2 Peran dan buku panduannya
   - 1.3 Tokoh simulasi
   - 1.4 Daftar cerita
   - 1.5 Cara membaca buku ini
2. Halaman Publik (Tanpa Masuk)
   - 2.1 Menu navigasi dan footer
   - 2.2 Beranda
   - 2.3 Berita
   - 2.4 Agenda dan Kalender Kegiatan
   - 2.5 Layanan Mandiri
   - 2.6 Pojok UMKM
   - 2.7 Halaman Transparansi Keuangan
3. Masuk, Pilih Peran, dan Keluar
   - 3.1 Masuk ke aplikasi
   - 3.2 Memilih peran
   - 3.3 Mengganti peran
   - 3.4 Keluar
   - 3.5 Belum punya akun, aktivasi akun, dan lupa password
4. Mengenal Layar Setelah Masuk
   - 4.1 Menu samping dan bilah atas
   - 4.2 Notifikasi (lonceng)
   - 4.3 Memasang aplikasi di layar HP
5. Panduan Pengambilan Screenshot (untuk penyusun dokumen)
6. Cerita Lengkap (Lintas Peran)
7. Masalah yang Sering Terjadi (FAQ)
8. Lampiran: Daftar Gambar

---

## 1. Mengenal WargaDigi 21

### 1.1 Apa itu WargaDigi 21

WargaDigi 21 adalah aplikasi web milik **RW 21 Desa Tanimulya**, Kecamatan Ngamprah, Kabupaten Bandung Barat. Lewat aplikasi ini warga dapat:

- membaca berita, pengumuman, dan agenda kegiatan lingkungan,
- mengajukan surat pengantar RT/RW dari rumah dan mengunduhnya dalam bentuk PDF,
- membayar iuran bulanan dan iuran lainnya, lalu mengunggah bukti bayar,
- melihat ringkasan uang masuk dan uang keluar yang sudah diumumkan pengurus,
- mengelola data keluarga dan mendaftarkan usaha di Pojok UMKM.

Pengurus RT, pengurus RW, operator, dan pengelola aplikasi memakai aplikasi yang sama untuk memeriksa pengajuan, mengelola konten, dan mencatat keuangan.

Aplikasi dapat dibuka dari komputer maupun HP lewat browser. Bagian 4.3 menjelaskan cara memasangnya di layar HP seperti aplikasi biasa.

### 1.2 Peran dan buku panduannya

Satu akun punya satu peran, kecuali akun tertentu yang punya dua peran dan memilihnya saat masuk (lihat 3.2).

| File | Untuk peran | Akun demo |
|---|---|---|
| `00-Umum-Publik-dan-Login.md` | Semua orang (pengunjung, masuk, pilih peran) | - |
| `01-Warga.md` | Warga (Kepala Keluarga) | `budisantoso` |
| `02-Ketua-RT.md` | Ketua RT | `budisampurno` (RT 03), `ketuart01rw21` (RT 01) |
| `03-Op-Konten-RT.md` | Operator Konten RT | `opkonten_3` (RT 03), `opkontenrt01rw21` (RT 01) |
| `04-Op-Keuangan-RT.md` | Operator Keuangan / Bendahara RT | `operator_rt3` (RT 03), `opkeuanganrt01rw21` (RT 01) |
| `05-Admin-RW.md` | Admin RW (sekretariat RW) | `adminrw21` |
| `06-Ketua-RW.md` | Ketua RW (pimpinan) | `pimpinan` |
| `07-Op-Konten-RW.md` | Operator Konten RW | `opkontenrw21` |
| `08-Op-Keuangan-RW.md` | Operator Keuangan / Bendahara RW | `opkeuanganrw21` |
| `09-DKM.md` | Pengurus keuangan DKM (masjid) | `dkm` |
| `10-Admin-Aplikasi.md` | Admin Aplikasi (pengelola sistem) | `superadmin` |

Semua akun demo memakai kata sandi **`password`**.

### 1.3 Tokoh simulasi

Nama tokoh dipakai agar pembaca memahami konteks. Untuk akun operator dan admin, **nama tokoh adalah fiktif dan tidak tampil di aplikasi** (akun tersebut hanya memiliki username).

| Tokoh | Akun | Peran | Konteks dari data demo |
|---|---|---|---|
| Pak Budi Santoso | `budisantoso` | Warga RT 03 | Kepala keluarga; istri Siti Rahma, anak Andi Saputra. Pemilik "Warung Nasi Ibu Ani" dan "Fashion Muslimah Aini" (disetujui) serta "Koperasi Warga Tanimulya" (ditolak, bisa diajukan ulang). Sudah punya 9 contoh surat berstatus Selesai. |
| Pak Budi Sampurno | `budisampurno` | Ketua RT 03 + Warga | Akun dengan dua peran (memilih peran saat masuk). Pemilik "Kriya Kayu Jati Tanimulya" (disetujui). Usaha "Jasa Service Elektronik Pak Budi" menunggu persetujuan. |
| Pak Ahmad Zaki | `ketuart01rw21` | Ketua RT 01 | Tercatat sebagai Ketua RT 01 di Kepengurusan. |
| Pak Dedi Mulyadi | `pimpinan` | Ketua RW | Tercatat sebagai Ketua RW di Kepengurusan. |
| Bu Rina *(fiktif)* | `adminrw21` | Admin RW | Mengurus persetujuan konten, UMKM, dokumen, dan data warga tingkat RW. |
| Mbak Laras *(fiktif)* | `opkontenrw21` | Op Konten RW | Menulis berita, agenda, pengumuman, dan galeri RW. |
| Mas Dimas *(fiktif)* | `opkontenrt01rw21` | Op Konten RT 01 | Menulis konten khusus RT 01. |
| Mbak Nisa *(fiktif)* | `opkonten_3` | Op Konten RT 03 | Menulis konten khusus RT 03. |
| Pak Hendra Gunawan | `opkeuanganrw21` | Op Keuangan RW | Tercatat sebagai Bendahara RW di Kepengurusan. |
| Bu Wati *(fiktif)* | `opkeuanganrt01rw21` | Op Keuangan RT 01 | Bendahara RT 01. |
| Bu Siti Aminah | `operator_rt3` | Op Keuangan RT 03 | Tercatat sebagai Bendahara RT 03 di Kepengurusan. |
| Pak Haji Rahmat *(fiktif)* | `dkm` | DKM | Pengurus keuangan masjid (dana kematian, pengadaan). |
| Mas Fajar *(fiktif)* | `superadmin` | Admin Aplikasi | Pengelola sistem dan pengaturan aplikasi. |

### 1.4 Daftar cerita

Satu cerita dapat melibatkan beberapa peran. Rujukan antar-file memakai **kode cerita + nama file** (contoh: "lihat `02-Ketua-RT.md`, cerita C-01"). Setiap file memuat tabel cerita yang melibatkan perannya pada bab "Cerita Lengkap".

| Kode | Cerita | Alur peran |
|---|---|---|
| C-01 | Pak Budi Santoso mengajukan Surat Keterangan Domisili untuk membuka rekening bank | Warga → Ketua RT 03 → pengurus RW → Warga mengunduh PDF |
| C-02 | Pengajuan surat ditolak karena data kurang, lalu diajukan ulang | Warga → Ketua RT (tolak) → Warga |
| C-03 | Pak Budi Santoso membayar tagihan "Iuran Perbaikan Got" (iuran RW yang dikumpulkan lewat RT) | Warga → Op Keuangan RT 03 (memeriksa bukti) → setoran kolektif RT ke RW |
| C-04 | Pak Budi Santoso membayar Iuran Bulanan RT 03 | Warga → Op Keuangan RT 03 → (setoran ke RW) |
| C-05 | Usaha "Jasa Service Elektronik Pak Budi" menunggu persetujuan, lalu disetujui | Warga (Pak Budi Sampurno) → Admin RW → tampil di Pojok UMKM |
| C-06 | "Koperasi Warga Tanimulya" ditolak, diperbaiki, lalu diajukan ulang | Warga → Admin RW |
| C-07 | Berita "Rencana Pemasangan CCTV Lingkungan" menunggu persetujuan, lalu terbit | Op Konten RW → Admin RW → tampil di beranda |
| C-08 | Berita RT 03 "Perbaikan Saluran Air RT 03" dikembalikan untuk revisi | Op Konten RT 03 → (revisi) → kirim ulang → persetujuan |
| C-09 | Agenda kegiatan RW dibuat, warga konfirmasi hadir, lalu dokumentasi diunggah ke galeri | Op Konten RW → Warga → Op Konten RW |
| C-10 | Warga mengajukan perubahan data anggota keluarga | Warga → Ketua RT → pengurus RW |
| C-11 | Laporan keuangan disusun, diajukan, disetujui, lalu tampil di Transparansi | Op Keuangan RT/RW/DKM → penyetuju → publik |
| C-12 | Pengaturan kepengurusan (pejabat penanda tangan surat) dan Halaman Utama | Admin Aplikasi |

> 💡 **Tips:** Alur di atas sudah dicocokkan dengan kode aplikasi. Perbedaan dari rencana awal: pada C-03, iuran RW yang dikumpulkan lewat RT diperiksa oleh Bendahara RT (bukan Bendahara RW), karena warga membayar ke rekening RT dan RT yang menyetor kolektif ke RW. Pada C-10, pengajuan ditinjau Ketua RT lalu disetujui RW.

### 1.5 Cara membaca buku ini

Setiap fitur dijelaskan dengan pola yang sama:

- **👤 SIMULASI** berisi tokoh, akun yang dipakai, dan data yang diisi.
- **Langkah-langkah** bernomor, dengan kalimat tebal sebagai perintah.
- **📸 SCREENSHOT** menunjukkan tempat gambar disisipkan.
- **✅ Hasilnya** menjelaskan yang terlihat setelah langkah selesai.
- **⚠️ Perhatian** berisi masalah yang mungkin muncul beserta solusinya.
- **💡 Tips** berisi jalan pintas.
- **➡️ Cerita berlanjut** menunjuk buku peran berikutnya.

---

## 2. Halaman Publik (Tanpa Masuk)

Bagian ini bisa dibuka siapa saja, tanpa akun. Pengunjung yang belum masuk hanya melihat **konten tingkat RW**. Konten khusus satu RT (misalnya berita RT 03) baru tampil setelah warga RT itu masuk.

### 2.1 Menu navigasi dan footer

Di bagian atas setiap halaman publik ada bilah menu berisi:

| Menu | Alamat | Isi |
|---|---|---|
| **Home** | `/` | Beranda |
| **Berita** | `/berita` | Arsip berita |
| **Agenda** | `/agenda` | Kalender dan daftar kegiatan |
| **Layanan Mandiri** | `/layanan-mandiri` | Daftar jenis surat yang bisa diajukan |
| **Pojok UMKM** | `/pojok-umkm` | Usaha milik warga |
| **Masuk** (tombol hijau) | `/login` | Halaman masuk |

Di HP, menu disembunyikan di balik tombol tiga garis (☰) di pojok kanan atas. Tekan tombol itu untuk membuka daftar menu.

Di bagian paling bawah ada **footer** berisi nama aplikasi, deskripsi singkat, kontak (bila diisi), tautan cepat (**Berita**, **Agenda**, **Layanan Mandiri**, **Pojok UMKM**), alamat **Sekretariat**, dan logo mitra pendukung bila ada. Isi footer diatur oleh Admin Aplikasi.

> 📸 **SCREENSHOT UM-01: Bilah menu publik dan footer**
> - **Akun:** (belum masuk)
> - **URL:** /
> - **Kondisi:** halaman Beranda baru dibuka di browser baru (tanpa sesi masuk)
> - **Area:** bilah menu di atas, lalu footer di bawah (dua potongan gambar)
> - **Sorot:** kotak merah pada menu **Home**, **Berita**, **Agenda**, **Layanan Mandiri**, **Pojok UMKM**, tombol **Masuk**, dan blok **Tautan Cepat** di footer
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844 (dengan menu ☰ terbuka)
> - **Nama file:** `UM-01-menu-publik-dan-footer.png`, `UM-01m-menu-publik-dan-footer.png`

### 2.2 Beranda

Beranda adalah halaman pertama saat membuka https://wargadigi21.com. Isinya dari atas ke bawah:

1. **Banner utama.** Lencana "Halo Warga RW 21", judul "Digitalisasi Gotong Royong RW 21 Tanimulya", penjelasan singkat, dan tombol **Layanan Warga** yang menuju Layanan Mandiri. Teks banner bisa diubah Admin Aplikasi.
2. **Berita Terkini.** Tiga berita terbaru, masing-masing dengan tanggal, asal (RW atau RT), judul, cuplikan, dan tautan **Baca Selengkapnya**. Tombol **Lihat Semua** membuka halaman Berita. Bila belum ada berita terbit, tampil tulisan "Belum ada berita yang dipublikasikan."
3. **Kegiatan & Rencana RW 21.** Terdiri dari **Kalender Kegiatan** (tanggal yang punya kegiatan diberi tanda) dan daftar **Agenda Mendatang** (tanggal, judul, jam dan lokasi, tautan **Detail**). Tombol **Lihat Semua Agenda** membuka halaman Agenda.
4. **Pojok UMKM Warga.** Tiga produk dari usaha yang sudah disetujui, lengkap dengan harga dan asal RT. Tombol **Lihat Semua UMKM** membuka Pojok UMKM.
5. Footer.

> 👤 **SIMULASI UM-A: Pengunjung membuka Beranda**
>
> Ibu **Rina**, tetangga yang baru pindah ke Komplek Puri Cipageran Indah II, belum punya akun. Ia membuka https://wargadigi21.com dari HP untuk melihat kabar lingkungan.
>
> - **Masuk sebagai:** tidak masuk (pengunjung)
> - **Yang dilihat:** berita "Kerja Bakti Bersih Lingkungan Sukses Digelar", agenda "Rapat Pengurus dan Warga RW 21", dan produk dari Pojok UMKM
> - **Catatan data demo:** berita dan agenda RT 03 tidak tampil untuk pengunjung; hanya konten RW

**1. Buka alamat aplikasi.**
Ketik `wargadigi21.com` di browser, lalu tekan Enter.

> 📸 **SCREENSHOT UM-02: Beranda (bagian atas)**
> - **Akun:** (belum masuk)
> - **URL:** /
> - **Kondisi:** halaman baru dibuka, belum digulir
> - **Area:** satu layar penuh dari bilah menu sampai bagian awal **Berita Terkini**
> - **Sorot:** kotak merah pada tombol **Layanan Warga** dan judul banner
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-02-beranda-atas.png`, `UM-02m-beranda-atas.png`

**2. Gulir ke bawah untuk melihat berita dan agenda.**
Pada bagian **Berita Terkini**, klik **Baca Selengkapnya** untuk membuka satu berita. Pada bagian **Kegiatan & Rencana RW 21**, klik tanggal bertanda di kalender atau tombol **Detail** pada agenda.

> 📸 **SCREENSHOT UM-03: Beranda, Berita Terkini dan Kalender Kegiatan**
> - **Akun:** (belum masuk)
> - **URL:** /
> - **Kondisi:** halaman digulir sampai bagian **Berita Terkini** dan **Kalender Kegiatan** terlihat
> - **Area:** dua bagian itu dalam satu layar (atau dua potongan)
> - **Sorot:** kotak merah pada tombol **Lihat Semua** dan **Lihat Semua Agenda**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `UM-03-beranda-berita-dan-agenda.png`

**3. Lihat Pojok UMKM di bagian bawah.**
Tiga produk ditampilkan dengan harga. Klik **Lihat Semua UMKM** untuk membuka halaman Pojok UMKM lengkap (lihat 2.6).

✅ **Hasilnya:**

- Beranda menampilkan kabar terbaru tanpa perlu masuk.
- Berita atau agenda yang diklik terbuka di halaman detailnya.

⚠️ **Perhatian**

- **Tulisan "Belum ada berita yang dipublikasikan." atau "Belum ada agenda mendatang."** Artinya memang belum ada yang terbit. Setelah operator konten mengirim dan Admin RW menyetujui, konten akan tampil.
- **Berita RT tidak terlihat oleh pengunjung.** Itu disengaja. Masuklah dengan akun warga RT yang bersangkutan untuk melihatnya.

---

### 2.3 Berita

Halaman **Berita** (`/berita`) berjudul **Berita RW 21**. Isinya:

- kotak **Cari berita** untuk mencari berdasarkan kata,
- deretan tombol kategori, dimulai dari **Semua** lalu kategori lain seperti Sosial, Infrastruktur, Kesehatan, Keamanan, dan Hiburan,
- satu berita utama bertanda **Terbaru**, lalu daftar **Berita Lainnya**. Tiap berita menampilkan kategori, asal (RW atau RT), tanggal, lama baca (menit), judul, cuplikan, dan tautan **Baca selengkapnya**.

Bila pencarian tidak menemukan apa pun, tampil "Tidak ada berita yang cocok. Coba kata kunci atau kategori lain."

> 👤 **SIMULASI UM-B: Pengunjung membaca berita kerja bakti**
>
> Ibu **Rina** ingin tahu hasil kerja bakti bulan ini.
>
> - **Masuk sebagai:** tidak masuk
> - **Berita yang dibaca:** "Kerja Bakti Bersih Lingkungan Sukses Digelar" (kategori Sosial)
> - **Kata kunci pencarian:** `kerja bakti`

**1. Buka menu Berita.**
Klik **Berita** di bilah menu.

> 📸 **SCREENSHOT UM-04: Halaman Berita**
> - **Akun:** (belum masuk)
> - **URL:** /berita
> - **Kondisi:** halaman baru dibuka, kategori **Semua** terpilih
> - **Area:** satu layar penuh dari judul **Berita RW 21** sampai berita utama
> - **Sorot:** kotak merah pada kotak **Cari berita**, deretan kategori, dan penanda **Terbaru**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-04-halaman-berita.png`, `UM-04m-halaman-berita.png`

**2. Cari atau pilih kategori.**
Ketik `kerja bakti` pada kotak **Cari berita**, atau klik tombol kategori **Sosial**.

**3. Buka berita.**
Klik **Baca selengkapnya** pada "Kerja Bakti Bersih Lingkungan Sukses Digelar".

> 📸 **SCREENSHOT UM-05: Detail berita**
> - **Akun:** (belum masuk)
> - **URL:** /berita/ lalu judul berita (alamat memakai nama berita)
> - **Kondisi:** berita "Kerja Bakti Bersih Lingkungan Sukses Digelar" terbuka
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada tautan **Kembali ke Arsip Berita** dan label kategori
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `UM-05-detail-berita.png`

✅ **Hasilnya:** Isi berita tampil lengkap dengan kategori, asal, dan tanggal. Tautan **Kembali ke Arsip Berita** membawa Anda ke daftar.

---

### 2.4 Agenda dan Kalender Kegiatan

Halaman **Agenda** (`/agenda`) berjudul **Agenda Kegiatan**. Isinya:

- **Kalender Kegiatan.** Tanggal yang memiliki kegiatan diberi tanda ("Tanggal bertanda memiliki kegiatan.").
- **Agenda Mendatang.** Kartu kegiatan yang akan datang atau sedang berlangsung (diberi label **Sedang berlangsung**).
- **Kegiatan yang Telah Terlaksana.** Arsip kegiatan lampau, dengan nomor halaman di bawahnya.

Setiap kartu menampilkan tanggal, kategori, asal (RW atau RT), judul, waktu, dan lokasi. Klik kartu untuk membuka detail: **Informasi Kegiatan** (tanggal, waktu, lokasi), tombol **Buka di Google Maps** atau **Lihat Lokasi di Peta**, dan daftar **Agenda Lainnya**. Tombol **Kembali ke Agenda** ada di bagian bawah.

Pada halaman detail untuk pengunjung tampil keterangan "Warga dapat mengonfirmasi kehadiran setelah masuk." Konfirmasi kehadiran (RSVP) hanya bisa dilakukan warga yang sudah masuk (lihat `01-Warga.md`, cerita C-09).

> 👤 **SIMULASI UM-C: Pengunjung melihat jadwal rapat**
>
> Ibu **Rina** ingin menghadiri rapat warga.
>
> - **Masuk sebagai:** tidak masuk
> - **Agenda yang dibuka:** "Rapat Pengurus dan Warga RW 21" (di Balai RW 21, pukul 19.00, beberapa hari ke depan)
> - **Lanjutan cerita:** untuk ikut konfirmasi kehadiran, Ibu Rina harus punya akun warga. Lihat `01-Warga.md`, cerita C-09.

**1. Buka menu Agenda.**
Klik **Agenda** di bilah menu.

> 📸 **SCREENSHOT UM-06: Halaman Agenda**
> - **Akun:** (belum masuk)
> - **URL:** /agenda
> - **Kondisi:** halaman baru dibuka
> - **Area:** satu layar penuh dengan **Kalender Kegiatan** dan awal **Agenda Mendatang**
> - **Sorot:** kotak merah pada tanggal bertanda di kalender dan kartu "Rapat Pengurus dan Warga RW 21"
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-06-halaman-agenda.png`, `UM-06m-halaman-agenda.png`

**2. Pilih agenda.**
Klik kartu **Rapat Pengurus dan Warga RW 21**.

> 📸 **SCREENSHOT UM-07: Detail agenda untuk pengunjung**
> - **Akun:** (belum masuk)
> - **URL:** /agenda/ lalu nama agenda
> - **Kondisi:** agenda "Rapat Pengurus dan Warga RW 21" terbuka
> - **Area:** satu layar penuh sampai blok **Informasi Kegiatan**
> - **Sorot:** kotak merah pada blok **Informasi Kegiatan** dan keterangan "Warga dapat mengonfirmasi kehadiran setelah masuk."
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `UM-07-detail-agenda-publik.png`

✅ **Hasilnya:** Anda tahu tanggal, jam, dan lokasi kegiatan. Kalender menandai tanggal yang ada kegiatannya.

> ➡️ **Cerita berlanjut:** Warga yang sudah masuk dapat menekan **Hadir** pada agenda ini. Lihat `01-Warga.md`, cerita C-09.

---

### 2.5 Layanan Mandiri

Halaman **Layanan Mandiri** (`/layanan-mandiri`) menampilkan satu kartu untuk setiap jenis surat yang dapat diajukan. Ada 9 jenis:

1. Kartu Keluarga
2. Kartu Tanda Penduduk
3. Surat Keterangan Ahli Waris
4. Surat Kelahiran
5. Surat Kematian
6. Surat Keterangan Domisili
7. Surat Keterangan Miskin / Tidak Mampu
8. Surat Keterangan Serbaguna
9. Surat Keterangan Kelakuan Baik / Catatan Kepolisian

Setiap kartu memuat ikon, nama surat, dan penjelasan singkat. Di bawah kartu ada **Alur Pengajuan** tiga langkah: warga mengajukan dan melengkapi data, Ketua RT lalu Ketua RW memeriksa dan menyetujui, lalu surat yang sudah disahkan dapat diunduh sebagai PDF.

**Perilaku bagi pengunjung yang belum masuk:** di atas kartu muncul keterangan "Pengajuan surat memerlukan akun warga. Masuk terlebih dahulu." dan setiap kartu bertuliskan **Masuk untuk Mengajukan**. Bila diklik, Anda diarahkan ke halaman masuk. Setelah berhasil masuk sebagai warga, Anda dibawa ke formulir pengajuan dengan jenis surat yang tadi dipilih sudah terpilih.

**Perilaku bagi yang sudah masuk:** kartu bertuliskan **Buat Pengajuan** dan langsung membuka formulir `/warga/surat` dengan jenis surat terpilih.

> 👤 **SIMULASI UM-D: Pengunjung memilih jenis surat**
>
> Pak **Budi Santoso** belum masuk. Ia membuka Layanan Mandiri dari HP, memilih kartu **Surat Keterangan Domisili**, lalu diminta masuk.
>
> - **Masuk sebagai:** tidak masuk (lalu `budisantoso`, kata sandi `password`)
> - **Jenis surat:** Surat Keterangan Domisili
> - **Lanjutan cerita:** pengajuan lengkap ada di `01-Warga.md`, cerita C-01.

**1. Buka menu Layanan Mandiri.**
Klik **Layanan Mandiri** di bilah menu.

> 📸 **SCREENSHOT UM-08: Layanan Mandiri sebelum masuk**
> - **Akun:** (belum masuk)
> - **URL:** /layanan-mandiri
> - **Kondisi:** halaman baru dibuka, 9 kartu tampil
> - **Area:** satu layar penuh dari judul **Layanan Mandiri** sampai beberapa kartu pertama
> - **Sorot:** kotak merah pada keterangan "Pengajuan surat memerlukan akun warga" dan tombol **Masuk untuk Mengajukan** pada kartu **Surat Keterangan Domisili**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-08-layanan-mandiri.png`, `UM-08m-layanan-mandiri.png`

**2. Pilih jenis surat.**
Klik kartu **Surat Keterangan Domisili**. Karena belum masuk, Anda dibawa ke halaman masuk (lihat Bab 3).

✅ **Hasilnya:** Setelah masuk sebagai warga, formulir pengajuan terbuka dengan **Surat Keterangan Domisili** sudah terpilih.

> ➡️ **Cerita berlanjut:** lihat `01-Warga.md`, cerita C-01 untuk mengisi formulir pengajuan.

---

### 2.6 Pojok UMKM

**Pojok UMKM** (`/pojok-umkm`) adalah etalase usaha milik warga RW 21. Hanya usaha yang **sudah disetujui dan aktif** yang tampil.

- Kotak pencarian "Cari Toko / Usaha UMKM Berdasarkan Nama..." dan pilihan **Semua Kategori**.
- Kartu usaha: kategori, harga mulai, nama pemilik dan RT, nama usaha, deskripsi, jumlah produk, dan tombol **Kunjungi Toko**.
- Bila tidak ada hasil: "Tidak ada usaha atau toko ditemukan" dengan tombol **Reset Pencarian**.
- Nomor halaman di bawah daftar (9 usaha per halaman).

Klik **Kunjungi Toko** untuk membuka halaman usaha (daftar produk, pencarian produk, urutan harga). Klik satu produk untuk membuka detail produk dengan tombol hubungi penjual lewat WhatsApp (nama tombol mengikuti layanan pesan yang dipasang pengelola).

> 👤 **SIMULASI UM-E: Pengunjung mencari makanan**
>
> Ibu **Rina** ingin memesan nasi timbel.
>
> - **Masuk sebagai:** tidak masuk
> - **Usaha yang dikunjungi:** "Warung Nasi Ibu Ani" (kategori Kuliner, milik Pak Budi Santoso, RT 03)
> - **Produk yang dilihat:** "Paket Nasi Timbel Ayam Bakar" (Rp25.000)

**1. Buka Pojok UMKM.**
Klik **Pojok UMKM** di bilah menu.

> 📸 **SCREENSHOT UM-09: Pojok UMKM**
> - **Akun:** (belum masuk)
> - **URL:** /pojok-umkm
> - **Kondisi:** halaman baru dibuka, usaha yang disetujui tampil sebagai kartu
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kotak pencarian, pilihan kategori, dan tombol **Kunjungi Toko** pada "Warung Nasi Ibu Ani"
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-09-pojok-umkm.png`, `UM-09m-pojok-umkm.png`

**2. Cari usaha.**
Ketik `warung` pada kotak pencarian, lalu tekan **Cari**.

**3. Buka toko.**
Klik **Kunjungi Toko** pada "Warung Nasi Ibu Ani".

> 📸 **SCREENSHOT UM-10: Halaman usaha Warung Nasi Ibu Ani**
> - **Akun:** (belum masuk)
> - **URL:** /usaha/ lalu nomor usaha (atau /pojok-umkm/usaha/ lalu nomor usaha)
> - **Kondisi:** halaman usaha terbuka dengan produk "Paket Nasi Timbel Ayam Bakar" dan "Es Cendol Dawet Ayu"
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada nama usaha dan kartu produk
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `UM-10-halaman-usaha.png`

✅ **Hasilnya:** Anda melihat profil usaha dan produk yang aktif. Usaha yang masih menunggu persetujuan atau ditolak tidak tampil di sini.

⚠️ **Perhatian**

- **Halaman "Profil usaha tidak ditemukan atau belum disetujui."** Usaha itu belum disetujui RW atau sedang dinonaktifkan. Pemilik usaha masih bisa melihatnya sendiri setelah masuk.

---

### 2.7 Halaman Transparansi Keuangan

Halaman **Transparansi Keuangan** (`/transparansi`) menampilkan ringkasan kas RW 21: **Total Saldo Kas**, **Pemasukan** dan **Pengeluaran** bulan berjalan, **Grafik Arus Kas 6 Bulan Terakhir**, **Transaksi Terakhir**, dan **Laporan Keuangan Resmi** yang dapat diunduh sebagai PDF.

⚠️ **Perhatian**

- **Tidak ada tombol menu untuk halaman ini.** Tautan **Transparansi** di bilah menu publik sedang disembunyikan. Halaman tetap dapat dibuka bila alamat `/transparansi` diketik langsung. Warga yang sudah masuk melihat ringkasan keuangan lewat menu **Laporan Keuangan** (lihat `01-Warga.md`).

> 📸 **SCREENSHOT UM-11: Halaman Transparansi Keuangan**
> - **Akun:** (belum masuk)
> - **URL:** /transparansi (ketik langsung di browser)
> - **Kondisi:** halaman terbuka dengan data demo
> - **Area:** satu layar penuh dari judul sampai kartu **Total Saldo Kas** dan grafik
> - **Sorot:** kotak merah pada **Total Saldo Kas** dan bagian **Laporan Keuangan Resmi**
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `UM-11-transparansi-publik.png`

---

## 3. Masuk, Pilih Peran, dan Keluar

### 3.1 Masuk ke aplikasi

Halaman masuk berada di `/login` (tombol hijau **Masuk** di bilah menu). Judulnya **Masuk ke Akun**. Formulirnya punya dua isian:

- **Username atau Nomor WhatsApp.** Boleh diisi salah satu. Contoh isian yang ditampilkan: `Username` atau `081234567890`.
- **Password.** Ikon mata di ujung kanan kolom memperlihatkan atau menyembunyikan tulisan.

Ada juga kotak centang **Ingat saya**, tautan **Lupa password?**, dan tautan **Belum punya akun? Daftar di sini**. Tombol **Masuk** berubah menjadi "Memverifikasi..." sesaat setelah ditekan.

Penjelasan tambahan tentang isian pertama:

- Username tidak membedakan huruf besar dan kecil.
- Nomor WhatsApp yang dipakai adalah nomor WA **keluarga** yang tercatat di Kartu Keluarga. Awalan `62` dan `0` dianggap sama.
- Bila satu nomor WA dipakai lebih dari satu akun, aplikasi memilih akun yang **kata sandinya cocok**.

> 👤 **SIMULASI UM-F: Pak Budi Santoso masuk dari HP**
>
> Pak **Budi Santoso** ingin mengajukan surat. Ia sudah punya akun warga.
>
> - **Masuk sebagai:** `budisantoso`
> - **Kata sandi:** `password`
> - **Lanjutan cerita:** setelah masuk, ia diarahkan ke **Dashboard Warga**. Lihat `01-Warga.md`, bab "Mulai Menggunakan".

**1. Buka halaman masuk.**
Klik tombol hijau **Masuk** di kanan atas, atau ketik `wargadigi21.com/login`.

> 📸 **SCREENSHOT UM-12: Halaman Masuk ke Akun**
> - **Akun:** (belum masuk)
> - **URL:** /login
> - **Kondisi:** formulir kosong
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kolom **Username atau Nomor WhatsApp**, kolom **Password**, ikon mata, dan tombol **Masuk**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-12-halaman-masuk.png`, `UM-12m-halaman-masuk.png`

**2. Isi username atau nomor WhatsApp.**
Ketik `budisantoso` pada kolom pertama.

**3. Isi password.**
Ketik `password`. Tekan ikon mata bila ingin memeriksa tulisan Anda.

**4. Tekan Masuk.**
Tekan tombol hijau **Masuk**.

✅ **Hasilnya:**

- Untuk akun **satu peran** (seperti `budisantoso`), Anda langsung diarahkan ke halaman utama perannya. Untuk Warga, itu **Dashboard Warga** di `/warga/dashboard`.
- Untuk akun **dua peran** (seperti `budisampurno`), Anda diarahkan ke halaman **Pilih Peran** (lihat 3.2).
- Bila sebelumnya Anda membuka halaman yang membutuhkan masuk, aplikasi membawa Anda kembali ke halaman itu.

⚠️ **Perhatian**

- **"Username atau Nomor WhatsApp wajib diisi."** Kolom pertama masih kosong. Isi dulu.
- **"Username atau Nomor WhatsApp tidak terdaftar dalam sistem."** Periksa ejaan username atau nomor WA. Pastikan nomor WA adalah nomor yang tercatat di Kartu Keluarga.
- **"Password yang Anda masukkan salah."** Ketik ulang password. Perhatikan huruf besar dan kecil.
- **"Akun belum diaktivasi. Silakan lakukan aktivasi terlebih dahulu."** Akun dibuat Ketua RT dan belum diaktifkan. Minta link aktivasi ke Ketua RT (lihat 3.5).
- **"Akun ini telah dinonaktifkan dan tidak dapat digunakan untuk masuk."** Biasanya terjadi setelah hak pemegang akun keluarga dipindahkan ke anggota lain. Gunakan akun pemegang yang baru atau hubungi Ketua RT.
- **Halaman pemeliharaan (kode 503).** Saat mode pemeliharaan aktif, hanya Admin Aplikasi dan Admin RW yang dapat masuk. Coba lagi nanti.

> 📸 **SCREENSHOT UM-13: Pesan kesalahan saat password salah**
> - **Akun:** (belum masuk)
> - **URL:** /login (setelah mengirim password yang salah)
> - **Kondisi:** username `budisantoso` diisi, password diisi `salah123`, tombol **Masuk** ditekan
> - **Area:** formulir masuk beserta pesan merah
> - **Sorot:** kotak merah pada pesan "Password yang Anda masukkan salah."
> - **Perangkat:** desktop 1366×768
> - **Nama file:** `UM-13-masuk-password-salah.png`

> 💡 **Tips:** Centang **Ingat saya** di perangkat pribadi supaya tidak perlu masuk lagi tiap kali. Jangan centang di komputer umum.

> ➡️ **Cerita berlanjut:** lihat `01-Warga.md`, bab "Mulai Menggunakan" untuk langkah pertama setelah masuk sebagai warga.

---

### 3.2 Memilih peran

Sebagian akun memegang **dua peran**. Contohnya `budisampurno`: Pak Budi Sampurno adalah **Ketua RT 03** sekaligus **Warga** (kepala keluarga). Setelah memasukkan username dan password, aplikasi menampilkan halaman **Masuk Sebagai** (`/pilih-peran`) dengan keterangan "Akun Anda memiliki lebih dari satu peran. Pilih peran untuk melanjutkan."

Setiap peran tampil sebagai kartu:

| Kartu | Keterangan di kartu | Arah setelah dipilih |
|---|---|---|
| **Kepala Keluarga** (peran Warga) | Akses layanan warga & keluarga | Dashboard Warga |
| **Ketua RT** | Akses dashboard & tugas kepengurusan | Dashboard Ketua RT |

Di bagian bawah halaman ada tombol **Keluar** bila Anda ingin membatalkan.

> 👤 **SIMULASI UM-G: Pak Budi Sampurno memilih peran**
>
> Pak **Budi Sampurno** ingin menyetujui surat warga, jadi ia memilih peran Ketua RT. Besok ia akan memilih peran Warga untuk mengurus usaha jasa service miliknya.
>
> - **Masuk sebagai:** `budisampurno`, kata sandi `password`
> - **Peran yang dipilih:** Ketua RT (untuk `02-Ketua-RT.md`) atau Kepala Keluarga (untuk `01-Warga.md`)

**1. Masuk seperti biasa.**
Isi `budisampurno` dan `password`, lalu tekan **Masuk**.

**2. Pilih kartu peran.**
Pada halaman **Masuk Sebagai**, klik kartu **Ketua RT** atau kartu **Kepala Keluarga**.

> 📸 **SCREENSHOT UM-14: Halaman Pilih Peran**
> - **Akun:** budisampurno
> - **URL:** /pilih-peran
> - **Kondisi:** baru masuk, belum memilih peran; dua kartu tampil
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kartu **Kepala Keluarga** dan kartu **Ketua RT**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-14-pilih-peran.png`, `UM-14m-pilih-peran.png`

✅ **Hasilnya:** Aplikasi membuka halaman utama peran yang Anda pilih. Menu samping hanya menampilkan menu untuk peran itu.

⚠️ **Perhatian**

- **"Peran yang dipilih tidak valid untuk akun Anda."** Muncul bila pilihan tidak termasuk peran akun Anda. Muat ulang halaman dan pilih lagi.
- **Akun satu peran tidak melihat halaman ini.** Akun satu peran langsung dibawa ke halaman utamanya.

---

### 3.3 Mengganti peran

Untuk akun dua peran, Anda bisa berpindah peran **tanpa keluar**. Di bagian bawah menu samping ada tombol **Ganti Peran**. Tombol ini hanya tampil untuk akun yang punya lebih dari satu peran. Setelah ditekan, Anda kembali ke halaman **Masuk Sebagai**.

> 👤 **SIMULASI UM-H: Pak Budi Sampurno berganti peran**
>
> Setelah menyetujui surat sebagai Ketua RT, Pak **Budi Sampurno** ingin memeriksa status usahanya sebagai Warga.
>
> - **Masuk sebagai:** `budisampurno` (peran aktif Ketua RT)
> - **Tujuan:** peran Kepala Keluarga

**1. Buka menu samping.**
Di HP, tekan dulu tombol **☰** di pojok kiri atas bilah atas.

**2. Tekan Ganti Peran.**
Gulir ke bagian bawah menu samping, lalu tekan **Ganti Peran**.

**3. Pilih peran baru.**
Pada halaman **Masuk Sebagai**, klik kartu **Kepala Keluarga**.

> 📸 **SCREENSHOT UM-15: Tombol Ganti Peran di menu samping**
> - **Akun:** budisampurno (peran aktif Ketua RT)
> - **URL:** /rt/dashboard
> - **Kondisi:** menu samping terbuka dan tergulir ke bawah
> - **Area:** bagian bawah menu samping
> - **Sorot:** kotak merah pada tombol **Ganti Peran** dan **Keluar**
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-15-tombol-ganti-peran.png`, `UM-15m-tombol-ganti-peran.png`

✅ **Hasilnya:** Menu samping berubah mengikuti peran baru, dan Anda tiba di halaman utama peran itu.

---

### 3.4 Keluar

Tombol **Keluar** (warna merah) ada di bagian bawah menu samping. Satu kali ditekan, sesi Anda diakhiri dan Anda kembali ke halaman masuk. Tidak ada kotak konfirmasi tambahan.

**1. Buka menu samping.**
Di HP, tekan tombol **☰** terlebih dahulu.

**2. Tekan Keluar.**
Gulir ke bawah, lalu tekan **Keluar**.

✅ **Hasilnya:** Anda kembali ke halaman `/login`. Peran aktif juga dihapus, jadi pada masuk berikutnya Anda memilih peran lagi (untuk akun dua peran).

> 💡 **Tips:** Selalu tekan **Keluar** setelah memakai komputer umum atau HP orang lain.

---

### 3.5 Belum punya akun, aktivasi akun, dan lupa password

**Belum punya akun.** Warga tidak mendaftar sendiri. Akun warga **dibuat oleh Ketua RT** saat mendata keluarga. Tautan **Belum punya akun? Daftar di sini** membuka halaman **Aktivasi Akun** (`/register`) yang berisi penjelasan:

1. Hubungi Ketua RT untuk mendapatkan link aktivasi.
2. Buka link aktivasi di perangkat Anda.
3. Buat password, lalu masuk dengan username Anda.

Di halaman itu ada tombol **Kembali ke Halaman Masuk**.

**Aktivasi lewat link.** Link aktivasi dari Ketua RT berbentuk `https://wargadigi21.com/aktivasi/t/` diikuti kode unik. Halaman yang terbuka menyapa "Halo" diikuti nama Anda, lalu meminta tiga isian:

- **Username.** Wajib, unik, dan hanya boleh huruf, angka, titik, garis bawah, atau strip.
- **Password Baru.** Minimal 8 karakter.
- **Konfirmasi Password.** Harus sama dengan password baru.

Tekan **Aktifkan & Simpan Password**. Setelah berhasil, Anda dibawa ke halaman masuk dengan pesan "Aktivasi berhasil. Silakan masuk dengan password yang Anda buat."

> 👤 **SIMULASI UM-I: Mengaktifkan akun lewat link dari Ketua RT**
>
> Keluarga baru di RT 03 didata oleh Ketua RT. Ketua RT membagikan link aktivasi lewat WhatsApp. Kepala keluarga membukanya dari HP.
>
> - **Masuk sebagai:** belum masuk (membuka link aktivasi)
> - **Username yang dipilih:** contoh `keluargabaru03`
> - **Lanjutan cerita:** pembuatan keluarga dan link aktivasi dijelaskan di `02-Ketua-RT.md`, bab Pendataan Keluarga.

**1. Buka link aktivasi.**
Ketuk link dari Ketua RT.

> 📸 **SCREENSHOT UM-16: Halaman Aktivasi Akun lewat link**
> - **Akun:** (belum masuk)
> - **URL:** /aktivasi/t/ diikuti kode dari link aktivasi (token baru dari Ketua RT)
> - **Kondisi:** link aktivasi yang masih berlaku, formulir kosong
> - **Area:** satu layar penuh
> - **Sorot:** kotak merah pada kolom **Username**, **Password Baru**, **Konfirmasi Password**, dan tombol **Aktifkan & Simpan Password**
> - **Perangkat:** 📱 mobile 390×844
> - **Nama file:** `UM-16m-aktivasi-akun.png`

**2. Isi username dan password.**
Pilih username, ketik password baru (minimal 8 karakter), lalu ketik ulang di **Konfirmasi Password**.

**3. Tekan Aktifkan & Simpan Password.**

✅ **Hasilnya:** Akun aktif dan Anda masuk ke halaman masuk dengan pesan keberhasilan. Link aktivasi tidak bisa dipakai lagi.

⚠️ **Perhatian**

- **"Link aktivasi tidak valid atau akun sudah aktif."** Link sudah dipakai atau salah salin. Minta Ketua RT membuat ulang link aktivasi.
- **"Username sudah digunakan. Pilih username lain."** Ganti username.
- **"Username hanya boleh huruf, angka, titik, garis bawah, atau strip."** Hapus spasi dan tanda lain.

**Lupa password.** Tautan **Lupa password?** di halaman masuk **belum terhubung ke fitur apa pun** (tidak membuka halaman lain). Bila Anda lupa password, hubungi pengurus (Ketua RT atau Admin Aplikasi) untuk bantuan.

---

## 4. Mengenal Layar Setelah Masuk

### 4.1 Menu samping dan bilah atas

Setelah masuk, layar punya dua bagian tetap:

- **Menu samping (sidebar)** di kiri berisi menu sesuai peran. Di bagian bawah ada **Ganti Peran** (hanya akun dua peran) dan **Keluar**. Di HP, menu disembunyikan; tekan tombol **☰** di kiri atas untuk membukanya.
- **Bilah atas** berisi tombol ☰, kotak "Cari di WargaDigi...", ikon **lonceng** untuk notifikasi, ikon **roda gigi** (Pengaturan), serta lingkaran inisial dan nama Anda. Untuk Warga, ikon roda gigi membuka halaman **Profil Keluarga**.

Menu samping untuk Warga dijelaskan di `01-Warga.md`. Menu peran lain dijelaskan di buku masing-masing.

### 4.2 Notifikasi (lonceng)

Ikon **lonceng** di bilah atas menampilkan kabar yang ditujukan kepada Anda, misalnya "Surat Disetujui RT", "Surat Terbit", atau pemberitahuan status usaha UMKM. Aturannya:

- **Angka merah** di atas lonceng adalah jumlah notifikasi yang **belum dibaca**.
- Klik lonceng untuk membuka daftar bertuliskan judul sesuai peran (contoh: **Notifikasi Warga**). Delapan notifikasi pertama tampil; tombol **Tampilkan Lebih Banyak** membuka sisanya.
- Klik satu notifikasi untuk membukanya. Notifikasi itu otomatis dianggap sudah dibaca dan Anda dibawa ke halaman terkait.
- Tombol silang kecil (**Sembunyikan notifikasi ini**) menyembunyikan satu notifikasi dari daftar. Bila semua disembunyikan, muncul "Semua notifikasi telah disembunyikan" dan tombol **Pulihkan Notifikasi**.
- Jika kosong, tampil "Tidak ada notifikasi baru".
- Khusus Warga, tagihan yang **belum lunas** ikut muncul sebagai "Tagihan Belum Lunas: ..." dan membawa Anda ke halaman Iuran Lainnya.

Selain di dalam aplikasi, browser dapat meminta izin menampilkan notifikasi di perangkat. Bila diizinkan, kabar penting tampil juga di layar HP saat aplikasi tertutup. Fitur ini hanya aktif bila pengelola sudah menyiapkannya dan Anda memberi izin.

> 👤 **SIMULASI UM-J: Pak Budi Santoso melihat notifikasi**
>
> Pak **Budi Santoso** masuk dan melihat angka merah di lonceng, lalu membukanya.
>
> - **Masuk sebagai:** `budisantoso`, kata sandi `password`
> - **Yang terlihat:** notifikasi "Tagihan Belum Lunas" untuk "Perbaikan Toa Masjid", "Iuran Perbaikan Got", dan "Iuran Kematian"

**1. Tekan ikon lonceng.**
Ikon ada di bilah atas, sebelah kiri ikon roda gigi.

> 📸 **SCREENSHOT UM-17: Daftar notifikasi terbuka**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/dashboard
> - **Kondisi:** lonceng sudah ditekan; daftar notifikasi terbuka dan memuat tagihan belum lunas
> - **Area:** bilah atas dan panel notifikasi
> - **Sorot:** kotak merah pada angka di lonceng dan satu notifikasi "Tagihan Belum Lunas"
> - **Perangkat:** desktop 1366×768 dan 📱 mobile 390×844
> - **Nama file:** `UM-17-notifikasi.png`, `UM-17m-notifikasi.png`

**2. Klik salah satu notifikasi.**
Anda dibawa ke halaman yang berkaitan.

✅ **Hasilnya:** Notifikasi yang diklik tidak dihitung lagi di angka merah.

---

### 4.3 Memasang aplikasi di layar HP

WargaDigi 21 adalah **aplikasi web progresif (PWA)**. Artinya Anda dapat menaruh ikonnya di layar utama HP seperti aplikasi biasa, tanpa mengunduh dari toko aplikasi. Nama aplikasi saat terpasang adalah **WargaDigi**, tampil layar penuh, dan warna temanya hijau.

Cara memasang bergantung pada browser:

- **Chrome di Android:** buka https://wargadigi21.com, ketuk titik tiga (⋮) di kanan atas, lalu pilih **Instal aplikasi** atau **Tambahkan ke layar utama**.
- **Safari di iPhone:** buka https://wargadigi21.com, ketuk ikon **Bagikan**, lalu pilih **Tambahkan ke Layar Utama**.

Nama pilihan menu bisa sedikit berbeda antar versi browser. Aplikasi tidak menampilkan tombol pasang sendiri; pemasangan dilakukan lewat menu browser.

> 👤 **SIMULASI UM-K: Memasang WargaDigi di HP**
>
> Pak **Budi Santoso** ingin membuka WargaDigi dengan satu ketukan dari layar utama.
>
> - **Masuk sebagai:** tidak perlu masuk untuk memasang
> - **Perangkat:** HP Android dengan Chrome

**1. Buka aplikasi di Chrome.**
Ketik `wargadigi21.com`.

**2. Buka menu browser.**
Ketuk titik tiga (⋮) di kanan atas.

**3. Pilih Instal aplikasi.**
Konfirmasi dengan menekan **Instal**.

> 📸 **SCREENSHOT UM-18: Menu browser untuk memasang aplikasi**
> - **Akun:** (belum masuk)
> - **URL:** /
> - **Kondisi:** menu titik tiga Chrome terbuka dan pilihan **Instal aplikasi** terlihat
> - **Area:** satu layar penuh HP
> - **Sorot:** kotak merah pada pilihan **Instal aplikasi**
> - **Perangkat:** 📱 mobile 390×844
> - **Nama file:** `UM-18m-pasang-aplikasi.png`

✅ **Hasilnya:** Ikon **WargaDigi** muncul di layar utama HP. Saat dibuka, aplikasi tampil tanpa kolom alamat browser.

> 💡 **Tips:** Bila ikon lama masih tampil setelah pengelola mengganti logo, hapus aplikasi dari layar utama lalu pasang kembali.

---

## 5. Panduan Pengambilan Screenshot (untuk penyusun dokumen)

> Bab ini untuk penyusun dokumen, bukan untuk pembaca umum.

### 5.1 Persiapan

1. **Reset data demo di server** https://wargadigi21.com agar semua gambar berasal dari keadaan awal yang sama dan tidak memuat data warga asli:
   `sudo bash scripts/deploy-update.sh --reset-demo`
2. **Catat tanggal reset.** Tanggal pada data demo dihitung dari hari reset (agenda "Rapat Pengurus dan Warga RW 21" tiga hari setelah reset, "Tabligh Akbar Akhir Bulan" sepuluh hari setelah reset, tenggat tagihan, dan lain-lain). Ambil semua gambar pada hari yang sama bila memungkinkan.
3. **Browser:** Chrome, zoom 100%.
   - **Desktop:** jendela 1366×768.
   - **Mobile:** DevTools mode perangkat, ukuran 390×844.
4. **Satu akun satu jendela.** Pakai jendela penyamaran (incognito) terpisah atau profil browser berbeda untuk tiap akun, supaya tidak saling menimpa sesi.
5. **Siapkan berkas contoh** untuk unggahan: satu foto JPG kecil (di bawah 2 MB) sebagai bukti bayar, satu foto KTP palsu dan satu foto KK palsu untuk contoh (jangan memakai dokumen asli).
6. **Isian sensitif.** Semua NIK dan nomor WA pada gambar adalah data demo. Jangan memakai data warga asli.

### 5.2 Prinsip urutan

Simulasi mengubah data. Contoh: setelah Admin RW menyetujui usaha, status "menunggu" hilang dan tidak bisa diambil gambarnya lagi. Karena itu:

- **Tahap 1:** ambil dulu semua gambar **keadaan awal** (belum ada aksi apa pun).
- **Tahap 2:** jalankan cerita berurutan sesuai alur peran. Pada setiap cerita, ambil gambar keadaan **"menunggu / ditolak / perlu revisi"** sebelum peran penyetuju menyetujuinya.
- Jangan menjalankan reset demo di tengah tahap 2, kecuali mengulang dari tahap 1.

### 5.3 Tahap 1: keadaan awal (belum ada aksi)

Ambil berurutan. Semua tanpa mengubah data.

| Urutan | Buku | Yang diambil |
|---|---|---|
| 1 | `00-Umum-Publik-dan-Login.md` | Semua halaman publik tanpa masuk: UM-01 sampai UM-11 |
| 2 | `00-Umum-Publik-dan-Login.md` | Halaman masuk dan kesalahan: UM-12, UM-13 |
| 3 | `00-Umum-Publik-dan-Login.md` | Pilih peran dan Ganti Peran (`budisampurno`): UM-14, UM-15 |
| 4 | `00-Umum-Publik-dan-Login.md` | Notifikasi Warga (`budisantoso`): UM-17 |
| 5 | `01-Warga.md` | Keadaan awal semua menu Warga, tanpa mengirim apa pun. Gambar: W-01 sampai W-05 (menu, Dashboard, Profil Keluarga), W-06 sampai W-09 dan W-11 (formulir KK, tambah anggota, detail dan ubah anggota, pindah akun; isi formulir tetapi **jangan dikirim**), W-12 sampai W-15 (formulir surat sampai sebelum **Kirim Pengajuan**), W-17 dan W-18, W-22 sampai W-24 (riwayat 9 contoh surat Selesai dan PDF), W-27 dan W-28 (Iuran Bulanan semua "Belum bayar"), W-31 sampai W-34 (Iuran Lainnya, tiga tagihan "Belum Lunas"), W-37 sampai W-39 (Laporan Keuangan), W-40 sampai W-42 (Pusat Informasi), W-43, W-44, dan W-46 (Agenda Warga sebelum RSVP), W-47 dan W-48 (Pojok UMKM), W-49 dan W-50 (formulir daftar usaha, jangan dikirim), W-51 dan W-53 sampai W-59 (Kelola UMKM dan jendela-jendelanya, jangan disimpan), serta W-03 dan W-52 (akun `budisampurno`, usaha masih Pending) |
| 6 | `02-Ketua-RT.md` | Dashboard dan daftar persetujuan sebelum aksi (daftar surat masih kosong) |
| 7 | Buku peran lain | Dashboard dan daftar awal masing-masing: `03-Op-Konten-RT.md`, `04-Op-Keuangan-RT.md`, `05-Admin-RW.md` (usaha "Jasa Service Elektronik Pak Budi" masih Pending, berita "Rencana Pemasangan CCTV Lingkungan" masih Review), `06-Ketua-RW.md`, `07-Op-Konten-RW.md`, `08-Op-Keuangan-RW.md`, `09-DKM.md`, `10-Admin-Aplikasi.md` |

### 5.4 Tahap 2: cerita berurutan

Jalankan urut dari atas ke bawah. Pada kolom "Ambil dulu" tertulis keadaan yang harus difoto **sebelum** tahap berikutnya.

| Urut | Cerita | Langkah dan buku | Ambil dulu (sebelum disetujui/ditolak) |
|---|---|---|---|
| 1 | **C-01** Surat Domisili | (a) `01-Warga.md` bab 4.2: `budisantoso` mengirim pengajuan (W-16, W-19, W-21). (b) `02-Ketua-RT.md`: Ketua RT 03 menyetujui. (c) `01-Warga.md` bab 4.4: status "Menunggu RW" (W-20). (d) `05-Admin-RW.md` / `06-Ketua-RW.md`: RW mengesahkan. (e) `01-Warga.md` bab 4.5: status Selesai dan unduh PDF (ambil ulang W-22 dan W-24 bila ingin memperlihatkan surat baru). | Status "Menunggu RT" (W-16, W-19, W-21), lalu "Menunggu RW" setelah RT menyetujui (W-20), sebelum RW mengesahkan |
| 2 | **C-02** Surat ditolak | (a) `01-Warga.md` bab 4.6: ajukan Surat Keterangan Miskin / Tidak Mampu tanpa penghasilan dan tanpa foto KK. (b) `02-Ketua-RT.md`: Ketua RT 03 menolak dengan alasan. (c) `01-Warga.md`: lihat alasan di tab Ditolak (W-25), lalu isi ulang dan kirim (W-26). | Status "Menunggu RT" sebelum RT menolak (boleh memakai W-19) |
| 3 | **C-10** Perubahan data keluarga | (a) `01-Warga.md` bab 3.4: ajukan perubahan pekerjaan Siti Rahma (W-09 dikirim). (b) `02-Ketua-RT.md`: RT menyetujui. (c) `05-Admin-RW.md`: RW menyetujui. (d) `01-Warga.md` bab 3.5: lihat Riwayat Pengajuan (W-10). | Status "Menunggu RT" lalu "Menunggu RW" pada W-10 |
| 4 | **C-04** Iuran Bulanan | (a) `01-Warga.md` bab 5.1: `budisantoso` mengajukan pembayaran (W-28 dikirim). (b) `04-Op-Keuangan-RT.md`: Bendahara RT 03 memeriksa (`operator_rt3`). (c) `01-Warga.md`: kartu bulan berubah. | Kartu "Menunggu verifikasi" (W-29) sebelum bendahara menyetujui; ambil juga contoh "Ditolak" (W-30) dengan mengajukan satu bulan lain yang kemudian ditolak |
| 5 | **C-03** Iuran Perbaikan Got | (a) `01-Warga.md` bab 5.2: bayar "Iuran Perbaikan Got" lewat transfer. (b) `04-Op-Keuangan-RT.md`: Bendahara RT 03 menyetujui. (c) `01-Warga.md`: status Lunas. | Status "Menunggu Konfirmasi" (W-35) sebelum disetujui; hasil akhir W-36 sesudahnya |
| 6 | **C-07** Berita CCTV | (a) `07-Op-Konten-RW.md`: berita sudah berstatus Review. (b) `05-Admin-RW.md`: menyetujui. (c) Beranda publik (UM-02 dan UM-03) setelah terbit. | Gambar status Review sebelum disetujui |
| 7 | **C-08** Berita revisi RT 03 | `03-Op-Konten-RT.md` (`opkonten_3`): berita "Perbaikan Saluran Air RT 03" berstatus Revisi, perbaiki dan kirim ulang. `05-Admin-RW.md`: menyetujui. | Gambar status Revisi beserta catatan revisi sebelum dikirim ulang |
| 8 | **C-09** Agenda dan RSVP | `07-Op-Konten-RW.md`: agenda; `01-Warga.md` bab 8: RSVP "Rapat Pengurus dan Warga RW 21" (W-45); `07-Op-Konten-RW.md`: galeri dokumentasi. | Agenda sebelum RSVP (W-43, W-44, W-46); jumlah **Warga Hadir** sebelum dan sesudah menekan **Hadir** |
| 9 | **C-05** UMKM jasa service | `01-Warga.md` bab 9.2 dan 2.2 (login `budisampurno`, peran Kepala Keluarga): kotak "Pendaftaran Usaha Anda Sedang Ditinjau RW" (W-03) dan tanda Pending (W-52); `05-Admin-RW.md`: menyetujui; `01-Warga.md`: status berubah; `00` UM-09 untuk tampilan publik. | W-03 dan W-52 sebelum Admin RW menyetujui |
| 10 | **C-06** Koperasi ditolak | `05-Admin-RW.md` (alasan penolakan sudah ada di data demo); `01-Warga.md` bab 9.4: perbaiki dan ajukan ulang (W-58, W-59, W-60); `05-Admin-RW.md`: menyetujui. | Status "Verifikasi Ditolak" dan alasannya (W-58) sebelum diperbaiki |
| 11 | **C-11** Laporan keuangan | `04-Op-Keuangan-RT.md`, `08-Op-Keuangan-RW.md`, `09-DKM.md`: menyusun dan mengajukan laporan; penyetuju menyetujui; `01-Warga.md` bab Laporan Keuangan dan `00` UM-11 untuk tampilan akhir. | Status "diajukan" sebelum disetujui |
| 12 | **C-12** Kepengurusan dan Halaman Utama | `10-Admin-Aplikasi.md`. Selesaikan paling akhir karena mengubah tampilan beranda dan footer. | Beranda dan footer **sebelum** diubah (sudah terambil di Tahap 1) |
| 13 | Aktivasi akun | `02-Ketua-RT.md` bab Pendataan Keluarga membuat keluarga dan link aktivasi; `00` UM-16 memotret halaman aktivasi dari link itu. | Halaman aktivasi sebelum diisi |

### 5.5 Tahap 3: pemeriksaan akhir

1. Pastikan setiap kode gambar di tabel Lampiran tiap buku sudah punya berkas dengan nama yang tepat.
2. Pastikan tidak ada gambar yang memuat data warga asli atau kata sandi yang mudah ditebak selain `password` (akun demo).
3. Pastikan gambar mobile memiliki akhiran **m** pada nama file.

---

## 6. Cerita Lengkap (Lintas Peran)

Buku ini tidak mengikuti cerita tertentu secara penuh, tetapi memuat bagian-bagian awal berikut.

| Kode | Cerita | Bagian yang ada di buku ini | Lanjutan di buku lain |
|---|---|---|---|
| C-01 | Surat Keterangan Domisili | Memilih jenis surat dari Layanan Mandiri (2.5) dan masuk (3.1) | `01-Warga.md` (pengajuan), `02-Ketua-RT.md` (persetujuan RT), `05-Admin-RW.md` / `06-Ketua-RW.md` (pengesahan RW) |
| C-05 | Usaha jasa service menunggu persetujuan | Tampilan usaha yang sudah disetujui di Pojok UMKM publik (2.6) dan pilih peran Pak Budi Sampurno (3.2) | `01-Warga.md` (pendaftaran dan pemantauan), `05-Admin-RW.md` (persetujuan) |
| C-09 | Agenda dan konfirmasi hadir | Melihat agenda sebagai pengunjung (2.4) | `01-Warga.md` (RSVP), `07-Op-Konten-RW.md` (agenda dan galeri) |
| C-11 | Laporan keuangan tampil di publik | Halaman Transparansi (2.7) | `04-Op-Keuangan-RT.md`, `08-Op-Keuangan-RW.md`, `09-DKM.md` |
| C-12 | Pengaturan Halaman Utama | Tampilan banner dan footer beranda (2.1, 2.2) | `10-Admin-Aplikasi.md` |

---

## 7. Masalah yang Sering Terjadi (FAQ)

**Saya lupa password. Bagaimana?**
Tautan **Lupa password?** di halaman masuk belum berfungsi. Hubungi Ketua RT atau Admin Aplikasi untuk bantuan.

**Muncul "Akun belum diaktivasi."**
Akun Anda dibuat Ketua RT tetapi belum diaktifkan. Minta link aktivasi ke Ketua RT, buka linknya, buat username dan password (lihat 3.5).

**Muncul "Link aktivasi tidak valid atau akun sudah aktif."**
Link sudah pernah dipakai atau tersalin tidak lengkap. Minta Ketua RT membuat ulang link aktivasi.

**Saya mengetik nomor WhatsApp tetapi gagal masuk.**
Nomor harus sama dengan nomor WhatsApp keluarga di Kartu Keluarga. Coba pakai username. Bila satu nomor dipakai beberapa akun, pastikan password yang diketik adalah password akun yang ingin Anda buka.

**Kenapa menu saya berbeda dengan teman saya?**
Menu mengikuti peran. Warga, Ketua RT, operator, dan admin melihat menu yang berbeda.

**Akun saya punya dua peran. Bagaimana pindah tanpa keluar?**
Tekan **Ganti Peran** di bagian bawah menu samping (lihat 3.3).

**Saya tidak melihat berita RT saya di halaman publik.**
Pengunjung hanya melihat konten RW. Masuklah dengan akun warga RT Anda agar berita dan agenda RT ikut tampil.

**Menu Transparansi tidak ada di bilah menu.**
Benar, tautannya sedang disembunyikan. Halaman `/transparansi` masih bisa dibuka lewat alamat langsung. Warga yang sudah masuk memakai menu **Laporan Keuangan**.

**Halaman terasa lama atau tidak berubah setelah pengelola memperbarui aplikasi.**
Tutup lalu buka kembali aplikasi, atau muat ulang halaman (tarik layar ke bawah di HP). Bila tetap sama, hapus ikon aplikasi dari layar utama lalu pasang lagi.

**Notifikasi di HP tidak muncul.**
Izinkan notifikasi saat browser memintanya, dan pastikan notifikasi untuk browser atau aplikasi WargaDigi tidak dimatikan di pengaturan HP. Daftar notifikasi di lonceng tetap bisa dibuka walau izin ditolak.

**Muncul halaman pemeliharaan.**
Aplikasi sedang dalam pemeliharaan. Hanya Admin Aplikasi dan Admin RW yang dapat masuk. Coba lagi beberapa saat kemudian.

---

## 8. Lampiran: Daftar Gambar

| Kode | Judul | Perangkat | Nama file |
|---|---|---|---|
| UM-01 | Bilah menu publik dan footer | Desktop, mobile | `UM-01-menu-publik-dan-footer.png`, `UM-01m-menu-publik-dan-footer.png` |
| UM-02 | Beranda (bagian atas) | Desktop, mobile | `UM-02-beranda-atas.png`, `UM-02m-beranda-atas.png` |
| UM-03 | Beranda, Berita Terkini dan Kalender Kegiatan | Desktop | `UM-03-beranda-berita-dan-agenda.png` |
| UM-04 | Halaman Berita | Desktop, mobile | `UM-04-halaman-berita.png`, `UM-04m-halaman-berita.png` |
| UM-05 | Detail berita | Desktop | `UM-05-detail-berita.png` |
| UM-06 | Halaman Agenda | Desktop, mobile | `UM-06-halaman-agenda.png`, `UM-06m-halaman-agenda.png` |
| UM-07 | Detail agenda untuk pengunjung | Desktop | `UM-07-detail-agenda-publik.png` |
| UM-08 | Layanan Mandiri sebelum masuk | Desktop, mobile | `UM-08-layanan-mandiri.png`, `UM-08m-layanan-mandiri.png` |
| UM-09 | Pojok UMKM | Desktop, mobile | `UM-09-pojok-umkm.png`, `UM-09m-pojok-umkm.png` |
| UM-10 | Halaman usaha Warung Nasi Ibu Ani | Desktop | `UM-10-halaman-usaha.png` |
| UM-11 | Halaman Transparansi Keuangan | Desktop | `UM-11-transparansi-publik.png` |
| UM-12 | Halaman Masuk ke Akun | Desktop, mobile | `UM-12-halaman-masuk.png`, `UM-12m-halaman-masuk.png` |
| UM-13 | Pesan kesalahan saat password salah | Desktop | `UM-13-masuk-password-salah.png` |
| UM-14 | Halaman Pilih Peran | Desktop, mobile | `UM-14-pilih-peran.png`, `UM-14m-pilih-peran.png` |
| UM-15 | Tombol Ganti Peran di menu samping | Desktop, mobile | `UM-15-tombol-ganti-peran.png`, `UM-15m-tombol-ganti-peran.png` |
| UM-16 | Halaman Aktivasi Akun lewat link | Mobile | `UM-16m-aktivasi-akun.png` |
| UM-17 | Daftar notifikasi terbuka | Desktop, mobile | `UM-17-notifikasi.png`, `UM-17m-notifikasi.png` |
| UM-18 | Menu browser untuk memasang aplikasi | Mobile | `UM-18m-pasang-aplikasi.png` |
