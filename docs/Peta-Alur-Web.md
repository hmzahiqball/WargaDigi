# Peta Alur Utama WargaDigi

Dokumen ini berisi alur utama tiap peran pengguna WargaDigi, ditulis ringkas agar mudah dijadikan flow diagram untuk presentasi. Setiap langkah mewakili menu atau tombol yang ada di aplikasi.

---

## Alur Masuk (Semua Pengguna)

```
Buka halaman Login
 → Masukkan username & password
 → Pilih Peran (jika punya lebih dari satu peran)
 → Masuk ke Dashboard sesuai peran
```

---

## 1. Superadmin (Admin Aplikasi)

```
Login → Dashboard Admin
 → Pengaturan Sistem (nama instansi, logo, notifikasi, dll)
 → Manajemen Hak Akses
      → Tambah Akun Pengurus (buat akun + tentukan peran)
      → Ubah Peran Pengguna
 → Data Pejabat (data ketua RT/RW untuk surat)
 → Log Aktivitas (riwayat kegiatan sistem)
```

---

## 2. Pendataan Warga oleh RT

```
Login (Ketua RT) → Dashboard RT
 → Tambah Keluarga Baru
      → Isi Data Kartu Keluarga
      → Isi Data Kepala Keluarga
      → Buat Akun Login untuk Kepala Keluarga
      → Simpan
 → Tambah Anggota Keluarga (untuk anggota tanpa akun)
```

Lanjutan — Aktivasi akun oleh warga (Kepala Keluarga):

```
Buka halaman Registrasi/Aktivasi
 → Masukkan NIK & Nomor WhatsApp
 → Verifikasi Kode OTP
 → Buat Password
 → Akun Aktif → Login
```

---

## 3. Penambahan / Perubahan Data Warga oleh Warga

```
Login (Warga) → Menu Keluarga
 → Ajukan Tambah Anggota ATAU Ubah Data Anggota (unggah dokumen KK)
 → Pengajuan terkirim → Menunggu persetujuan RT

Ketua RT → Menu Mutasi Warga
 → Setujui  → diteruskan ke RW
 → Tolak    → selesai (dengan alasan)

Pengurus RW → Menu Mutasi Warga
 → Setujui  → Data warga diperbarui (selesai)
 → Tolak    → selesai (dengan alasan)
```

---

## 4. Pengajuan Dokumen (Surat) oleh Warga

```
Login (Warga) → Menu Permohonan Surat
 → Pilih Jenis Surat + isi keperluan + unggah KTP/KK
 → Kirim Pengajuan → Status: Diajukan

Ketua RT → Menu Persetujuan Dokumen
 → Setujui (tanda tangan + stempel RT) → diteruskan ke RW
 → Tolak (beri catatan) → kembali ke warga

Pengurus RW → Menu Persetujuan Dokumen
 → Sahkan (tanda tangan + stempel RW) → Status: Selesai
 → Tolak (beri catatan) → kembali ke warga

Warga → Unduh Surat (PDF) setelah status Selesai
```

---

## 5. Pengelolaan Laporan Keuangan (Operator Keuangan RT / RW / DKM)

```
Login (Operator Keuangan) → Dashboard Keuangan
 → Menu Transaksi → Catat Pemasukan/Pengeluaran
 → Menu Laporan
      → Buat Laporan (pilih bulan & tahun) → Status: Draft
      → Review lalu klik Ajukan → Status: Menunggu Persetujuan

Ketua RT / Pimpinan RW → Menu Persetujuan Keuangan
 → Setujui → Status: Disetujui
 → Tolak (beri catatan) → dikembalikan untuk diperbaiki

Operator Keuangan → Publikasikan Laporan (setelah disetujui)
 → Laporan tampil di menu Laporan Keuangan untuk semua warga
```

Terkait iuran/tagihan:

```
Operator Keuangan → Menu Iuran → Buat Tagihan untuk warga
Warga → Menu Tagihan → Bayar (unggah bukti) → Menunggu verifikasi
Operator Keuangan → Setujui / Tolak Pembayaran
Warga → Unduh Resi (setelah disetujui)
```

---

## 6. Pembuatan Konten (Berita, Agenda, Pengumuman) + Persetujuan

```
Login (Operator Konten) → Dashboard Konten
 → Pilih Menu: Berita / Agenda / Pengumuman
 → Buat Konten → Simpan sebagai Draft
 → Klik Ajukan → Status: Menunggu Review

Pengurus RW → Menu Pusat Informasi
 → Setujui → Status: Terbit (tampil ke publik)
 → Kembalikan untuk Revisi (beri catatan)

Operator Konten → Perbaiki konten Revisi → Ajukan ulang
```

---

## 7. Pengajuan & Pengelolaan UMKM

```
Login (Warga) → Menu Galeri UMKM
 → Daftarkan Usaha (isi data usaha + foto)
 → Kirim → Status: Menunggu Verifikasi

Pengurus RW → Menu Verifikasi UMKM
 → Setujui → Usaha tampil di galeri publik
 → Tolak (beri catatan)

Warga (usaha disetujui) → Menu Kelola UMKM
 → Ubah Profil Usaha
 → Tambah / Ubah / Hapus Produk

Publik / Pengunjung → Pojok UMKM
 → Jelajahi Galeri → Lihat Detail Produk → Hubungi via WhatsApp
```

---

## Catatan untuk Pembuatan Diagram

- Setiap peran bisa dibuat satu diagram terpisah agar tidak terlalu padat.
- Alur yang melibatkan persetujuan bertingkat (Surat, Mutasi Warga) paling cocok digambar dengan jalur Warga → RT → RW.
- Gunakan bentuk keputusan (ya/tidak atau setuju/tolak) pada titik persetujuan.
