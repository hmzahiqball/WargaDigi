# Manual Book WargaDigi 21

Kumpulan buku panduan penggunaan WargaDigi 21 (https://wargadigi21.com), dibagi per peran.
Setiap file dapat diubah menjadi dokumen Word bergambar oleh AI/penyusun dokumen dengan mengikuti
konvensi di bawah.

## Daftar file

| File | Untuk peran | Akun demo |
|---|---|---|
| `00-Umum-Publik-dan-Login.md` | Semua orang (pengunjung, login, pilih peran) + panduan pengambilan screenshot | - |
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

---

## Tokoh simulasi (persona)

Nama tokoh dipakai agar pembaca memahami konteks. Untuk akun operator/admin, **nama tokoh adalah
fiktif dan tidak tampil di aplikasi** (akun tersebut hanya memiliki username).

| Tokoh | Akun | Peran | Konteks dari data demo |
|---|---|---|---|
| Pak Budi Santoso | `budisantoso` | Warga RT 03 | Kepala keluarga; istri Siti Rahma, anak Andi Saputra. Pemilik "Warung Nasi Ibu Ani" dan "Fashion Muslimah Aini" (disetujui) serta "Koperasi Warga Tanimulya" (ditolak, bisa diajukan ulang). Sudah punya 9 contoh surat berstatus Selesai. |
| Pak Budi Sampurno | `budisampurno` | Ketua RT 03 + Warga | Akun dengan dua peran (memilih peran saat login). Usaha "Jasa Service Elektronik Pak Budi" menunggu persetujuan. |
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

---

## Cerita lintas peran

Satu cerita dapat melibatkan beberapa peran. Rujukan antar-file memakai **kode cerita + nama file**
(contoh: "lihat `02-Ketua-RT.md`, cerita C-01"). Setiap file memuat tabel cerita yang melibatkan
perannya pada bab "Cerita Lengkap".

| Kode | Cerita | Alur peran |
|---|---|---|
| C-01 | Pak Budi Santoso mengajukan Surat Keterangan Domisili untuk membuka rekening bank | Warga → Ketua RT 03 (setujui + tanda tangan) → Admin RW atau Ketua RW (sahkan) → Warga mengunduh PDF |
| C-02 | Pengajuan surat ditolak karena data kurang, lalu diajukan ulang | Warga → Ketua RT (tolak dengan alasan) → Warga |
| C-03 | Pak Budi Santoso membayar tagihan RW "Iuran Perbaikan Got" yang dikumpulkan lewat RT | Warga → Op Keuangan RT 03 (verifikasi) → Op Keuangan RW (rekap dan setoran) |
| C-04 | Pak Budi Santoso membayar Iuran Bulanan RT 03 | Warga → Op Keuangan RT 03 → setoran ke Op Keuangan RW |
| C-05 | Usaha "Jasa Service Elektronik Pak Budi" menunggu persetujuan, lalu disetujui | Warga (Pak Budi Sampurno) → Admin RW → tampil di Pojok UMKM (Ketua RW hanya memantau) |
| C-06 | "Koperasi Warga Tanimulya" ditolak, diperbaiki, lalu diajukan ulang | Warga → Admin RW |
| C-07 | Berita "Rencana Pemasangan CCTV Lingkungan" menunggu persetujuan, lalu terbit | Op Konten RW → Admin RW → tampil di beranda |
| C-08 | Berita RT 03 "Perbaikan Saluran Air RT 03" dikembalikan untuk revisi | Op Konten RT 03 → (revisi, kembali ke Draf) → ajukan ulang → Admin RW |
| C-09 | Agenda kegiatan RW dibuat, warga konfirmasi hadir, lalu dokumentasi diunggah ke galeri | Op Konten RW → Warga → Op Konten RW |
| C-10 | Warga mengajukan perubahan data anggota keluarga | Warga → Ketua RT → Admin RW (menu Pengajuan Data) |
| C-11 | Laporan keuangan disusun, diajukan, disetujui, lalu dipublikasikan ke warga | RT: Op Keuangan RT → Ketua RT → Op Keuangan RT (publikasikan). RW/DKM: lihat 08 dan 09 |
| C-12 | Pengaturan kepengurusan (pejabat penanda tangan surat) dan Halaman Utama | Admin Aplikasi |

Isi setiap file mengikuti alur yang **benar-benar ada di kode aplikasi**; bila ada perbedaan,
file peran yang menjadi acuan. Admin Aplikasi juga memakai cerita khusus A-00 sampai A-09
(lihat `10-Admin-Aplikasi.md`).

---

## Konvensi blok

| Blok | Fungsi | Saran tampilan di Word |
|---|---|---|
| `> 👤 **SIMULASI ...**` | Konteks cerita: siapa, kenapa, data demo yang dipakai, lanjutan cerita | Kotak berwarna biru muda |
| `> 📸 **SCREENSHOT <KODE>: <judul>**` | Instruksi pengambilan gambar; diganti dengan gambar bernama sama | Gambar + keterangan "Gambar <KODE>: <judul>" |
| `✅ **Hasilnya:**` | Yang terlihat setelah langkah selesai | Kotak hijau muda |
| `⚠️ **Perhatian**` | Hal yang bisa salah dan solusinya | Kotak kuning |
| `> 💡 **Tips:**` | Jalan pintas/kebiasaan baik | Kotak abu-abu |
| `> ➡️ **Cerita berlanjut:**` | Penunjuk ke file/peran berikutnya | Teks miring |
| `<!-- ... -->` | Catatan untuk penyusun dokumen; jangan dicetak | (tidak ditampilkan) |

### Format blok screenshot

```markdown
> 📸 **SCREENSHOT W-12: Halaman Layanan Permohonan Surat**
> - **Akun:** budisantoso (Warga)
> - **URL:** /warga/surat
> - **Kondisi:** keadaan layar/data sebelum diambil
> - **Area:** layar penuh / bagian tertentu
> - **Sorot:** elemen yang diberi kotak merah, nomor, atau anak panah
> - **Perangkat:** desktop 1366×768 dan/atau 📱 mobile 390×844
> - **Nama file:** `W-12-halaman-permohonan-surat.png` (mobile: akhiran `m`, mis. `W-12m-...png`)
```

Awalan kode gambar per file: `UM` (00), `W` (01), `KRT` (02), `OKT` (03), `OUT` (04), `ARW` (05),
`KRW` (06), `OKW` (07), `OUW` (08), `DKM` (09), `ADM` (10).

Perangkat: **Warga** memakai desktop dan mobile; **peran lain** memakai desktop.

---

## Persiapan pengambilan screenshot

1. Server **https://wargadigi21.com** di-reset ke data demo terbaru (`sudo bash scripts/deploy-update.sh --reset-demo`)
   agar semua gambar berasal dari keadaan awal yang sama dan tidak memuat data warga asli.
2. Ikuti **urutan pengambilan** di `00-Umum-Publik-dan-Login.md`. Urutan ini penting karena simulasi
   mengubah data (misalnya setelah usaha disetujui, status "menunggu" hilang).
3. Tanggal pada data demo mengikuti hari reset; manual sengaja tidak memakai tanggal pasti.
4. Gunakan browser Chrome dengan zoom 100%, jendela 1366×768 untuk desktop, dan mode perangkat
   (DevTools) 390×844 untuk mobile.

## Fitur yang tidak dimuat

Fitur yang sedang disembunyikan di aplikasi tidak dimasukkan: Arsip Data Warga, Manajemen Data,
Persetujuan Keuangan RW, dan menu "Pusat Bantuan" (belum memiliki halaman).
