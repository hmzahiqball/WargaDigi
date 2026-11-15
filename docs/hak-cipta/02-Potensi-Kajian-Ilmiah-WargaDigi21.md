# Potensi Kajian Karya Ilmiah dari WargaDigi 21

Dokumen ini memetakan topik jurnal dan artikel PkM yang dapat dikembangkan dari aplikasi
WargaDigi 21 (RW 21 Desa Tanimulya, PkM Politeknik Negeri Bandung). Setiap topik memuat rumusan
masalah, metode, data yang tersedia, kebaruan, dan sasaran publikasi.

**Tim:** Lukmannul Hakim Firdaus, S.Kom., M.T.; Bambang Wisnuadhi, S.Si., M.T.; Asri Maspupah,
S.ST., M.T.; Rahil Jumiyani, SST., M.Sc.; Santi Sundari, S.Si., M.T.; Zulkifli Arsyad, S.Kom., M.T.;
Joe Lian Min, M.Eng.

## Keterangan kesiapan data

| Label | Arti |
|---|---|
| **Siap** | Data atau artefak sudah tersedia di aplikasi/repositori |
| **Survei** | Perlu kuesioner atau wawancara ke warga/pengurus |
| **Eksperimen** | Perlu pengujian terkontrol (dataset uji, pengukuran) |
| **Log** | Perlu masa pemakaian nyata untuk mengumpulkan data penggunaan |

Data pengguna nyata hanya dipakai dengan persetujuan pengurus RW dan warga, dianonimkan, dan tidak
memuat NIK, nama, atau foto dokumen (lihat bagian Etika).

---

## Ringkasan topik

| No | Topik | Jenis luaran | Kesiapan | Prioritas |
|---|---|---|---|---|
| 1 | Penerapan dan penerimaan layanan RW digital oleh warga (UTAUT) | Artikel PkM + jurnal SI | Survei, Log | Tinggi |
| 2 | Evaluasi kegunaan aplikasi multi-peran (SUS dan uji tugas) | Jurnal HCI/SI | Survei, Eksperimen | Tinggi |
| 3 | Rancangan surat pengantar berjenjang dengan tanda tangan digital dan snapshot data | Jurnal RPL/e-government | Siap, Log | Tinggi |
| 4 | Model transparansi keuangan komunitas tiga unit (RT, RW, DKM) | Jurnal SI/akuntabilitas | Siap, Survei | Tinggi |
| 5 | Akurasi OCR KK dan KTP Indonesia dengan layanan OCR gratis dan parser berbasis aturan | Jurnal informatika | Eksperimen | Sedang |
| 6 | Dampak konversi WebP terhadap penyimpanan dan waktu muat di HP | Jurnal/prosiding teknik | Siap, Eksperimen | Sedang |
| 7 | Perlindungan data pribadi pada aplikasi tingkat RT/RW menurut UU PDP | Jurnal keamanan/hukum TI | Siap | Sedang |
| 8 | PWA dan Web Push untuk warga dengan perangkat beragam | Prosiding | Eksperimen, Log | Rendah |
| 9 | Pengembangan perangkat lunak berbantuan AI pada tim PkM perguruan tinggi | Jurnal RPL/pendidikan | Siap | Sedang |
| 10 | Etalase UMKM tingkat RW untuk pemberdayaan ekonomi warga | Artikel PkM | Log, Survei | Sedang |

---

## Topik 1. Penerapan dan Penerimaan Layanan RW Digital oleh Warga

**Judul usulan:** *Penerapan Platform Layanan RW Digital dan Analisis Penerimaannya oleh Warga:
Studi Kasus RW 21 Desa Tanimulya*

- **Rumusan masalah:** Faktor apa yang memengaruhi niat dan pemakaian aplikasi layanan RW oleh warga
  dengan latar usia dan literasi digital yang beragam?
- **Metode:** UTAUT (performance expectancy, effort expectancy, social influence, facilitating
  conditions) dengan kuesioner skala Likert, analisis PLS-SEM. Untuk versi PkM: pre-test dan
  post-test pelatihan, ditambah data pemakaian.
- **Data yang tersedia:** jumlah akun aktif per RT, aktivasi akun, pengajuan surat, pembayaran,
  RSVP (tabel `users`, `pengajuan_surat`, `pembayaran_tagihan`, `agenda_kehadiran`, `activity_log`).
- **Kebaruan:** konteks RW di wilayah peri-urban dengan akun per keluarga (bukan per individu).
- **Luaran:** artikel PkM (Jurnal Pengabdian kepada Masyarakat terakreditasi SINTA) dan artikel
  jurnal SI.
- **Kesiapan:** Survei, Log.

## Topik 2. Evaluasi Kegunaan Aplikasi Multi-Peran

**Judul usulan:** *Evaluasi Usability Aplikasi Administrasi RW Multi-Peran Menggunakan System
Usability Scale dan Task-Based Testing*

- **Rumusan masalah:** Apakah tingkat kegunaan berbeda antarperan (warga, Ketua RT, bendahara,
  admin), dan tugas mana yang paling menyulitkan?
- **Metode:** uji tugas per peran (waktu, tingkat keberhasilan, jumlah galat) memakai skenario dari
  manual book, kuesioner SUS, wawancara singkat; perbandingan antarperan.
- **Data yang tersedia:** 10 peran, skenario lintas peran (cerita C-01 sampai C-12) dan data demo
  yang konsisten di `docs/manual/`; tampilan desktop dan HP.
- **Kebaruan:** evaluasi pada satu sistem dengan peran berlapis dan akun yang memegang dua peran.
- **Luaran:** jurnal HCI/SI.
- **Kesiapan:** Survei, Eksperimen.

## Topik 3. Surat Pengantar Berjenjang dengan Tanda Tangan Digital dan Snapshot Data

**Judul usulan:** *Rancang Bangun Layanan Surat Pengantar RT/RW Berjenjang dengan Tanda Tangan
Digital dan Snapshot Data untuk Menjaga Integritas Dokumen*

- **Rumusan masalah:** Bagaimana merancang alur surat yang cepat bagi warga, tetapi dokumennya tetap
  konsisten dan dapat ditelusuri ketika data penduduk dan kepengurusan berubah?
- **Metode:** rancang bangun (requirement, desain state machine, implementasi, pengujian otomatis);
  evaluasi waktu layanan sebelum dan sesudah (wawancara pengurus untuk kondisi manual, data waktu
  `created_at` sampai `tanggal_selesai` untuk kondisi digital).
- **Data yang tersedia:** 9 jenis surat, alur Diajukan → Disetujui RT → Selesai, snapshot data
  surat, riwayat pejabat (`pejabat`), pengujian otomatis.
- **Kebaruan:** snapshot data dan pejabat aktif per masa jabatan pada layanan tingkat RT/RW.
- **Luaran:** jurnal RPL atau e-government.
- **Kesiapan:** Siap (rancangan), Log (waktu layanan).

## Topik 4. Transparansi Keuangan Komunitas Tiga Unit

**Judul usulan:** *Model Sistem Informasi Keuangan Komunitas untuk Transparansi Iuran Warga pada
Tingkat RT, RW, dan DKM*

- **Rumusan masalah:** Bagaimana memodelkan pembukuan tiga unit yang saling terkait (iuran RT,
  tagihan RW yang dikumpulkan lewat RT, setoran per KK, dana kematian DKM) agar akuntabel dan dapat
  dilihat warga?
- **Metode:** pemodelan proses (BPMN) dan data, implementasi, evaluasi persepsi transparansi dan
  kepercayaan warga melalui kuesioner sebelum dan sesudah penerapan.
- **Data yang tersedia:** tabel `transaksi_keuangan`, `master_iuran`, `tagihan`,
  `pembayaran_tagihan`, `setoran_rt`, `laporan_keuangan`; alur persetujuan lalu publikasi laporan.
- **Kebaruan:** pemisahan unit dalam satu sistem dengan alur setoran dan publikasi bertahap.
- **Luaran:** jurnal SI atau akuntansi sektor publik/komunitas.
- **Kesiapan:** Siap (model), Survei (persepsi).

## Topik 5. Akurasi OCR KK dan KTP Indonesia

**Judul usulan:** *Evaluasi Akurasi Ekstraksi Data Kartu Keluarga dan KTP Menggunakan OCR Daring
dan Parser Berbasis Aturan*

- **Rumusan masalah:** Seberapa akurat ekstraksi tiap kolom KK dan KTP dari foto HP, dan faktor apa
  (pencahayaan, resolusi, kompresi) yang paling berpengaruh?
- **Metode:** dataset uji dari dokumen **sintetis atau contoh berizin** (bukan dokumen warga),
  variasi kondisi foto; metrik akurasi per kolom, Character Error Rate, dan tingkat isian manual;
  pembanding dengan OCR lokal (misalnya Tesseract) bila memungkinkan.
- **Data yang tersedia:** `OcrService`, `KkParser`, `KtpParser`, alur resize gambar sebelum OCR.
- **Kebaruan:** evaluasi pada format dokumen kependudukan Indonesia dengan layanan OCR gratis.
- **Luaran:** jurnal informatika.
- **Kesiapan:** Eksperimen.

## Topik 6. Konversi WebP untuk Aplikasi Komunitas

**Judul usulan:** *Pengaruh Konversi Otomatis Gambar ke WebP terhadap Kebutuhan Penyimpanan dan
Waktu Muat Aplikasi Web Komunitas pada Perangkat Seluler*

- **Rumusan masalah:** Berapa penghematan penyimpanan dan percepatan waktu muat yang diperoleh, dan
  bagaimana pengaruhnya terhadap keterbacaan foto dokumen?
- **Metode:** eksperimen ukuran berkas dan waktu muat (Lighthouse, jaringan 3G/4G tersimulasi) pada
  berbagai kualitas WebP; penilaian keterbacaan foto KTP/KK oleh penilai.
- **Data yang tersedia:** `KonversiGambarWebp` dengan profil kualitas, uji awal penghematan sekitar
  85%, pengujian otomatis.
- **Luaran:** prosiding atau jurnal teknik.
- **Kesiapan:** Siap, Eksperimen.

## Topik 7. Perlindungan Data Pribadi pada Aplikasi RT/RW

**Judul usulan:** *Analisis Kesesuaian Aplikasi Administrasi RT/RW terhadap Undang-Undang
Pelindungan Data Pribadi*

- **Rumusan masalah:** Risiko apa yang muncul ketika pengurus lingkungan mengelola data NIK, KK, dan
  foto KTP, dan kontrol apa yang memadai?
- **Metode:** analisis kesenjangan terhadap UU No. 27 Tahun 2022 dan pedoman keamanan aplikasi
  (misalnya OWASP ASVS); identifikasi risiko pemrosesan oleh pihak ketiga (OCR daring).
- **Data yang tersedia:** pembagian penyimpanan saat ini (tanda tangan bawaan di penyimpanan
  privat; foto KTP/KK dan bukti bayar di penyimpanan publik aplikasi dengan nama acak), pembatasan
  data per RT, berkas sementara milik pengunggah, log aktivitas, rekap perbaikan keamanan
  (Oktober 2026), dan temuan pada manual book (misalnya persetujuan surat yang belum dibatasi per RT).
  Kondisi nyata ini cocok sebagai studi kasus analisis kesenjangan sebelum dan sesudah perbaikan.
- **Luaran:** jurnal keamanan informasi atau hukum TI.
- **Kesiapan:** Siap.

## Topik 8. PWA dan Web Push untuk Warga

**Judul usulan:** *Efektivitas Notifikasi Web Push pada Progressive Web App Layanan Warga*

- **Rumusan masalah:** Apakah notifikasi browser mempercepat respons warga dan pengurus dibanding
  notifikasi di dalam aplikasi saja, dan bagaimana dukungannya di Android dan iOS?
- **Metode:** pengukuran waktu respons (misalnya dari notifikasi persetujuan sampai unduhan surat)
  antara kelompok dengan dan tanpa langganan push; uji kompatibilitas perangkat.
- **Data yang tersedia:** `push_subscriptions`, `notifications`, service worker.
- **Luaran:** prosiding.
- **Kesiapan:** Eksperimen, Log.

## Topik 9. Pengembangan Perangkat Lunak Berbantuan AI pada Tim PkM

**Judul usulan:** *Praktik Pengembangan Perangkat Lunak Berbantuan Asisten AI dalam Tim Pengabdian
kepada Masyarakat: Studi Kasus WargaDigi 21*

- **Rumusan masalah:** Bagaimana asisten AI dipakai dalam siklus kebutuhan, desain, implementasi,
  pengujian, dokumentasi, dan penerapan, serta di mana peran keputusan manusia tetap diperlukan?
- **Metode:** studi kasus kualitatif dengan analisis riwayat Git (104 commit, pola perbaikan bug,
  pengujian), catatan keputusan desain, dan refleksi tim.
- **Data yang tersedia:** riwayat Git, 463 kasus uji, dokumen teknis, manual book yang disusun dari
  kode, skrip penerapan.
- **Kebaruan:** dokumentasi praktik nyata pada proyek komunitas dengan sumber daya terbatas.
- **Luaran:** jurnal RPL atau pendidikan vokasi.
- **Kesiapan:** Siap.

## Topik 10. Etalase UMKM Tingkat RW

**Judul usulan:** *Pemberdayaan UMKM Warga melalui Etalase Digital Tingkat RW yang Dikurasi
Pengurus*

- **Rumusan masalah:** Apakah etalase digital tingkat RW meningkatkan keterjangkauan usaha warga di
  lingkungannya sendiri?
- **Metode:** data jumlah usaha, produk, jumlah akses produk, dan klik WhatsApp selama periode
  tertentu; wawancara pemilik usaha.
- **Data yang tersedia:** `umkm_usaha` (kolom `klikWA`), `umkm_produk` (kolom `jumlah_akses`),
  alur kurasi Admin RW.
- **Luaran:** artikel PkM.
- **Kesiapan:** Log, Survei.

---

## Rekomendasi penggabungan dan urutan publikasi

| Tahap | Luaran | Topik yang digabung |
|---|---|---|
| 1 (segera) | Artikel PkM: penerapan WargaDigi 21 di RW 21 | Topik 1 (bagian PkM), 3, 4, 10 sebagai deskripsi solusi dan dampak awal |
| 2 | Jurnal SI/RPL | Topik 3 atau 4 sebagai artikel rancang bangun |
| 3 | Jurnal HCI/SI | Topik 2 (setelah uji kegunaan) |
| 4 | Jurnal SI | Topik 1 (setelah pemakaian 3 sampai 6 bulan) |
| Paralel | Jurnal informatika/teknik | Topik 5, 6, 7, 9 |

---

## Etika dan izin data

1. Izin tertulis dari pengurus RW 21 untuk memakai data pemakaian aplikasi.
2. Persetujuan responden (informed consent) untuk survei dan uji kegunaan.
3. Data diambil dalam bentuk agregat atau dianonimkan; NIK, nama, nomor WhatsApp, dan foto
   dokumen tidak dipublikasikan.
4. Eksperimen OCR memakai dokumen sintetis atau contoh berizin, tidak memakai dokumen warga.
5. Tangkapan layar artikel diambil dari server demo dengan data fiktif (`docs/manual/Daftar-Akun-Demo.md`).

## Instrumen yang perlu disiapkan

| Instrumen | Topik | Catatan |
|---|---|---|
| Kuesioner UTAUT | 1 | Adaptasi ke bahasa sehari-hari |
| Kuesioner SUS + skenario tugas per peran | 2 | Skenario diambil dari manual book |
| Kuesioner persepsi transparansi keuangan | 4 | Sebelum dan sesudah publikasi laporan |
| Dataset uji KK/KTP sintetis | 5 | Variasi pencahayaan dan resolusi |
| Skrip ekspor data pemakaian teranonim | 1, 3, 8, 10 | Agregat per bulan dan per RT |
