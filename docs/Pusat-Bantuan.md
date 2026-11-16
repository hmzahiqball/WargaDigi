# Pusat Bantuan (Panduan Aplikasi Digital)

Menu **Pusat Bantuan** (bagian bawah menu samping, semua peran) menampilkan panduan aplikasi
yang bisa dicari. Isinya berasal dari Manual Book (Word) Buku 00 sampai 10.

## Perilaku

- Terbuka untuk publik di `/pusat-bantuan`, dengan tautan di footer.
  - Pengguna yang masuk: panduan peran aktif + panduan umum (tampilan dasbor).
  - Pengunjung: bisa memilih peran (`?peran=ketua-rt`). Panduan umum dan peran di
    `config('panduan.peran_publik')` (bawaan: Warga) bisa dibaca penuh. Topik peran lain hanya
    tampil judulnya (ikon gembok); saat dibuka diarahkan ke halaman masuk lalu kembali ke topik.
  - Pengguna masuk yang membuka topik peran lain diarahkan ke daftar dengan penjelasan.
- Isi, daftar topik, pencarian, dan halaman topik **hanya sesuai peran aktif** ditambah panduan
  umum (Buku 00). Topik peran lain menghasilkan 404.
- Pencarian memahami pertanyaan sehari-hari ("bagaimana cara mengajukan surat"), potongan kata
  ("ajukan" cocok "mengajukan"), dan sinonim (ttd, kk, login, bayar, dll.). Saran muncul saat
  mengetik.
- Halaman Pusat Informasi (Warga, RT, RW) memiliki tombol **Panduan Aplikasi** ke Pusat Bantuan.

## Memperbarui isi panduan

Jalankan di komputer pengembang setiap kali Manual Book direvisi:

```bash
php artisan panduan:impor "C:\path\ke\folder\WargaDigi21_Manual_Book_..."
```

Perintah ini:
1. membaca semua `WargaDigi21_Buku_XX_*.docx` (nomor buku menentukan peran, lihat `config/panduan.php`);
2. memecah isi menjadi topik (satu sub-bab atau satu pertanyaan FAQ per topik);
3. membuang bagian khusus buku cetak: daftar isi, daftar gambar, ringkasan alur, tokoh/data demo,
   blok simulasi ("Contoh penggunaan"), dan rujukan cerita;
4. menyesuaikan sisa nama tokoh/akun demo menjadi kalimat netral (`PenyesuaianTeksPanduan`) dan
   menampilkan peringatan bila masih ada yang tersisa;
5. mengonversi gambar ke WebP (lebar maks. 1366 px) ke `public/panduan/`;
6. menulis `database/data/panduan/panduan.json` dan memuatnya ke database lokal.

Lalu commit hasilnya (`database/data/panduan/` dan `public/panduan/`) dan deploy seperti biasa.
Server memuat versi baru ke tabel `panduan_topik` otomatis saat Pusat Bantuan pertama kali
dibuka; tidak ada langkah server tambahan. Bila perlu memuat manual: `php artisan panduan:muat`.

Tutup file Word sebelum impor bila muncul pesan tidak dapat membaca file.

## Struktur Word yang dikenali

| Unsur di Word | Hasil di Pusat Bantuan |
|---|---|
| Heading 1 | Bab (pengelompokan topik) |
| Heading 2 | Topik |
| Heading 3 | Subjudul di dalam topik |
| "Langkah-langkah", "Hasil yang terlihat", "Catatan penting" (atau gaya Hasil/Perhatian) | Kotak bagian |
| Daftar bernomor / gaya Step "1. ..." | Langkah bernomor |
| "Tips penggunaan: ..." / gaya Tips | Kotak tips |
| Gambar + Caption | Gambar dengan keterangan; pasangan desktop + HP tampil sesuai layar |
| Bab "Pertanyaan yang sering muncul" | Satu topik per pertanyaan (teks tebal di awal paragraf atau gaya FAQQuestion) |

## Berkas terkait

- `app/Services/Panduan/DocxPanduanParser.php`: membaca docx.
- `app/Services/Panduan/PanduanImportir.php`: impor folder, gambar, JSON.
- `app/Services/Panduan/PenyesuaianTeksPanduan.php`: aturan penggantian nama/akun demo.
- `app/Services/Panduan/PanduanRepository.php`: sinkronisasi JSON ke database, normalisasi peran.
- `app/Services/Panduan/PanduanPencarian.php`: pencarian dan sinonim.
- `app/Http/Controllers/PusatBantuanController.php`, `resources/views/pusat-bantuan/`.
- Test: `tests/Feature/PusatBantuanTest.php`, `tests/Feature/ImporPanduanTest.php`.
