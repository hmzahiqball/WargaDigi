# 📋 Rekapitulasi Bug Report — UAT WargaDigi-v2
**Total Bug: 21** | **Modul: M4 (Keuangan) + M5 (Tagihan & Pembayaran)**
**Versi: Final & Terverifikasi dari Transcript — 13 Oktober 2026**

---

## 🔍 Klarifikasi Inkonsistensi Sebelumnya

> [!IMPORTANT]
> Dokumen ini adalah versi **koreksi final**. Ada 3 masalah di dokumen sebelumnya:
> 1. **EV-021 tidak ada dalam percakapan asli.** Bug "Transfer/QRIS warga gagal bayar" dicatat di percakapan sebagai bagian dari AC-05.3 tanpa nomor EV tersendiri. Nomor EV-021 saya tambahkan secara sepihak — ini salah.
> 2. **Nomor EV-017 vs EV-018:** Bug "rekening RW & DKM tidak terisolasi" di transcript asli adalah **EV-017**, bukan EV-018.
> 3. **EV-011 muncul dua kali** di dua fase rencana fixing — ini duplikasi yang sudah dikoreksi.
>
> **Dari transcript yang terverifikasi:** EV-001 s.d. EV-020 = 20 bug. Ditambah 1 bug "Transfer/QRIS" yang dicatat tanpa nomor → total **21 bug**, dengan bug ke-21 diberi nomor **EV-021** secara resmi di dokumen ini.

---

## 🔴 CRITICAL (6 Bug)

| EV | Modul | AC | Bug Title | Priority | Status |
|----|-------|----|-----------|----------|--------|
| **EV-009** | M4 | AC-04.3 & 04.4 | **Celah Manipulasi:** Transaksi di dalam laporan berstatus Submitted, Approved, atau Published **terkunci rapat** (tidak dapat ditambah, diedit, atau dihapus). | High | 🟢 **FIXED** |
| **EV-011** | M4 | AC-04.6 s.d. 04.12 | **Broken Access Control:** Bendahara RT terisolasi ketat hanya pada lingkup RT-nya, tidak dapat melihat maupun menghapus laporan milik DKM atau RW. | High | 🟢 **FIXED** |
| **EV-012** | M4 | AC-04.8 | **[BLOCKER]** Menu Persetujuan Keuangan pada sidebar akun Ketua RW **tidak merespon** saat ditekan (mati/dead link). | High | 🟢 **FIXED** |
| **EV-013** | M4 | AC-04.9 | **[BLOCKER]** Terjadi **Error Server (Illuminate Exception / HTTP 500)** saat Ketua RT mencoba melakukan Approve laporan. | High | 🟢 **FIXED** |
| **EV-017** | M5 | AC-05.7 | **Broken Access Control:** Data pengaturan rekening bank antara unit **RW dan DKM saling menimpa** — 1 row database dipakai bersama. RT sudah terisolasi dengan baik. | High | 🟢 **FIXED** |
| **EV-021** | M5 | AC-05.3 & 05.4 | **[BLOCKER]** Warga **gagal melakukan pembayaran non-tunai** (Transfer/QRIS) karena komponen **Upload Bukti Pembayaran tidak berfungsi** (mati). Hanya metode Cash yang bisa dipakai. | High | 🟢 **FIXED** |

---

## 🟠 MAJOR (4 Bug)

| EV | Modul | AC | Bug Title | Priority | Status |
|----|-------|----|-----------|----------|--------|
| **EV-003** | M4 | AC-04.1 | Status Sheet Saldo selalu tampil **'Balanced'** meskipun saldo bernilai minus/defisit (nilai hardcoded, tidak dinamis). | High | 🟢 **FIXED** |
| **EV-004** | M4 | AC-04.2 | **Ghost value +150.000:** Saldo RT di dashboard akun RW & DKM tidak sinkron — nilai dari data seeder nyasar ke kalkulasi lintas unit. | High | 🟢 **FIXED** |
| **EV-007** | M4 | AC-04.6 & 04.7 | Alur laporan **melompati fase 'Draft'** — laporan langsung berstatus Pending/Submitted tanpa ada fase review mandiri terlebih dahulu. | Medium | 🟢 **FIXED** |
| **EV-016** | M5 | AC-05.7 & 05.8 | Upload gambar **QRIS gagal tersimpan** di pengaturan rekening — setelah disimpan, gambar hilang dan hanya menyisakan data teks. | High | 🟢 **FIXED** |

---

## 🟡 MINOR (8 Bug)

| EV | Modul | AC | Bug Title | Priority | Status |
|----|-------|----|-----------|----------|--------|
| **EV-001** | M4 | AC-04.1 | Overview hanya tampilkan total saldo gabungan — tidak ada **breakdown pemasukan & pengeluaran**. | Low | 🟢 **FIXED** |
| **EV-002** | M4 | AC-04.1 | **Tombol mati** di sudut kanan atas komponen Sheet Saldo (tidak ada aksi apapun saat diklik). | Medium | 🟢 **FIXED** |
| **EV-005** | M4 | AC-04.2 | **Timezone UTC** — jam transaksi tercatat mundur 7 jam dari waktu WIB aktual. | Medium | 🟢 **FIXED** |
| **EV-006** | M4 | AC-04.2 | Fitur **upload lampiran** Bukti Transaksi Kas tidak berfungsi saat menyimpan. | Medium | 🟢 **FIXED** |
| **EV-008** | M4 | AC-04.7 & 04.10 | Opsi filter status laporan **tidak memiliki pilihan 'Rejected'**. | Low | 🟢 **FIXED** |
| **EV-010** | M4 | AC-04.4 | Opsi **'Hapus' hilang** dari dropdown ketika hanya tersisa 1 transaksi di tabel. | Medium | 🟢 **FIXED** |
| **EV-015** | M5 | AC-05.1 | Bendahara RT tidak menemukan **satu warga pun** di detail tagihan insidental — warga belum di-assign ke RT terkait di database. | Medium | Open |
| **EV-019** | M5 | AC-05.2 | **Ikon lonceng (bel) miring/menceng** ke kanan pada tampilan notifikasi pengingat — CSS misaligned. | Low | 🟢 **FIXED** |

---

## 🟢 LOW (3 Bug)

| EV | Modul | AC | Bug Title | Priority | Status |
|----|-------|----|-----------|----------|--------|
| **EV-014** | M5 | AC-05.1 | **Tagihan Rutin otomatis belum diimplementasi** — hanya tagihan insidental yang tersedia (seharusnya auto-generate tiap tgl 1). | Low | Open |
| **EV-018** | M5 | AC-05.2 | **Sinkronisasi notifikasi tagihan warga** — jumlah notifikasi harus tepat sesuai dengan tagihan aktif yang diberikan ke warga terkait. | Low | 🟢 **FIXED** |
| **EV-020** | M5 | M5-36 | Fitur **pengiriman notifikasi ke WhatsApp belum diimplementasi** (tidak tercantum di AC sehingga tidak mempengaruhi status UAT utama). | Medium | Open |

---

## 📊 Rekap Final Status

| Severity | Total | Fixed | Open |
|----------|-------|-------|------|
| 🔴 Critical | 6 | **6** (EV-009, EV-011, EV-012, EV-013, EV-017, EV-021) | 0 |
| 🟠 Major | 4 | **4** (EV-003, EV-004, EV-007, EV-016) | 0 |
| 🟡 Minor | 8 | **7** (EV-001, EV-002, EV-005, EV-006, EV-008, EV-010, EV-019) | 1 (EV-015) |
| 🟢 Low | 3 | **1** (EV-018) | 2 (EV-014, EV-020) |
| **TOTAL** | **21** | **18 FIXED** | **3 OPEN** |

---

## 🚀 Rencana Fixing & Status Eksekusi

### FASE 1 — Unblock Testing M4 [Sub Nomor 29-32]
Fokus prioritas utama untuk membuka blocker approval laporan keuangan Ketua RW dan Ketua RT:

| # | EV | Severity | Status | Deskripsi Singkat |
|---|-----|----------|--------|-------------------|
| 1 | **EV-012** | Critical | 🟢 **FIXED** | Fix link menu Persetujuan Keuangan di sidebar Ketua RW & routing `rw.persetujuan-keuangan.index` |
| 2 | **EV-013** | Critical | 🟢 **FIXED** | Fix HTTP 500 (`QueryException` enum violation status `'Disetujui RT'` -> `'Approved'`) & scope `rt_id` |

---

### FASE 2 — Bug Fixing Tagihan, Rekening, Pembayaran Warga, & Minor UI

| Urutan | EV | Severity | Status | Bug Title Singkat | Catatan |
|--------|----|----------|--------|-------------------|---------|
| 1 | **EV-021** | Critical | 🟢 **FIXED** | Implementasi Upload Bukti Transfer & QRIS | Form upload bersih, live image preview, validation feedback & direct modal support |
| 2 | **EV-016** | Major | 🟢 **FIXED** | Upload gambar QRIS di Pengaturan Rekening | Storage fallback route di `web.php` & upload preview berfungsi normal |
| 3 | **EV-017** | Critical | 🟢 **FIXED** | Isolasi Rekening Bendahara RW & DKM | Query role terisolasi tegas (`whereNull('rt_id')`), controller warga mengambil ketiga unit, modal bayar terpetakan dinamis |
| 4 | **EV-019** | Minor | 🟢 **FIXED** | Ikon lonceng (bel) miring/menceng | CSS alignment flex & vertical-align diperbaiki di topbar & tabel pengingat |
| 5 | **EV-018** | Low | 🟢 **FIXED** | Sinkronisasi jumlah notifikasi tagihan warga | Query tagihan unpaid realtime di topbar warga (badge sesuai jumlah tagihan) |
| 6 | **EV-003** | Major | 🟢 **FIXED** | Status Sheet Saldo & Liabilitas dinamis | Revisi IR06 dospem selesai: nilai liabilitas dinamis (BUKAN Rp 0) & status icon dinamis (peringatan merah saat defisit, centang hijau saat balanced) |
| 7 | **EV-004** | Major | 🟢 **FIXED** | Isolasi aset per unit kerja | Selesai di iterasi sebelumnya |
| 8 | **EV-005** | Minor | 🟢 **FIXED** | Timezone Asia/Jakarta (WIB) | APP_TIMEZONE, session database pgsql, Carbon locale & explicit conversion WIB |
| 9 | **EV-001** | Minor | 🟢 **FIXED** | Breakdown pemasukan & pengeluaran di overview | Tiga Top Stat Cards (Kas RW, Kas RT, Dana DKM) menampilkan rincian Masuk & Keluar secara transparan |
| 10 | **EV-002** | Minor | 🟢 **FIXED** | Tombol Sheet Saldo aktif | Tombol icon external link di kanan atas Sheet Saldo kini fungsional mengarah ke Laporan Keuangan |
| 11 | **EV-006** | Minor | 🟢 **FIXED** | Fix upload lampiran bukti transaksi kas | Integrasi Base64 payload (anti-gagal Windows temp directory), dual-write storage, alert banner sukses & validasi, serta preview JPG/PNG/PDF |
| 12 | **EV-008** | Minor | 🟢 **FIXED** | Tambah opsi filter 'Rejected' di laporan | Opsi 'Rejected' ditambahkan ke dropdown filter, dibungkus form GET fungsional terhubung ke query controller |
| 13 | **EV-010** | Minor | 🟢 **FIXED** | Fix dropdown agar Hapus muncul di transaksi terakhir | Popper strategy fixed & CSS min-height table-responsive mencegah menu dropdown terpotong saat baris tersisa 1 |
| 14 | EV-015 | Minor | Open | Assign warga ke RT di database (KeluargaSeeder) | |
| 15 | **EV-007** | Major | 🟢 **FIXED** | Implementasi fase Draft sebelum Submit laporan | Status default pembuatan laporan baru adalah Draft, ditambahkan tombol & endpoint 'Ajukan' untuk review mandiri sebelum submit |
| 16 | **EV-011** | Critical | 🟢 **FIXED** | Fix query Broken Access Control laporan Bendahara RT | Scope `unit = 'RT'` dan `rt_id` pada query dan handler `laporanDestroy` memastikan Bendahara RT tidak bisa melihat atau menghapus laporan RW/DKM |
| 17 | **EV-009** | Critical | 🟢 **FIXED** | Lock transaksi jika laporan sudah Submitted/Approved/Published | Proteksi backend & UI: transaksi pada periode yang laporannya Submitted, Approved, atau Published dikunci (tidak bisa ditambah, diedit, atau dihapus) |
