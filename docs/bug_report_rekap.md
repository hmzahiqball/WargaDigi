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

| EV | Modul | AC | Bug Title | Priority |
|----|-------|----|-----------|----------|
| **EV-009** | M4 | AC-04.3 & 04.4 | **Celah Manipulasi:** Transaksi di dalam laporan berstatus Pending/Submitted **masih bisa diubah dan dihapus** — laporan bersifat dinamis bukan snapshot. | High |
| **EV-011** | M4 | AC-04.6 s.d. 04.12 | **Broken Access Control:** Bendahara RT dapat melihat dan **menghapus** laporan keuangan milik DKM maupun RW. | High |
| **EV-012** | M4 | AC-04.8 | **[BLOCKER]** Menu Persetujuan Keuangan pada sidebar akun Ketua RW **tidak merespon** saat ditekan (mati/dead link). | High |
| **EV-013** | M4 | AC-04.9 | **[BLOCKER]** Terjadi **Error Server (Illuminate Exception / HTTP 500)** saat Ketua RT mencoba melakukan Approve laporan. | High |
| **EV-017** | M5 | AC-05.7 | **Broken Access Control:** Data pengaturan rekening bank antara unit **RW dan DKM saling menimpa** — 1 row database dipakai bersama. RT sudah terisolasi dengan baik. | High |
| **EV-021** | M5 | AC-05.3 & 05.4 | **[BLOCKER]** Warga **gagal melakukan pembayaran non-tunai** (Transfer/QRIS) karena komponen **Upload Bukti Pembayaran tidak berfungsi** (mati). Hanya metode Cash yang bisa dipakai. | High |

---

## 🟠 MAJOR (4 Bug)

| EV | Modul | AC | Bug Title | Priority | Status |
|----|-------|----|-----------|----------|--------|
| **EV-003** | M4 | AC-04.1 | Status Sheet Saldo selalu tampil **'Balanced'** meskipun saldo bernilai minus/defisit (nilai hardcoded, tidak dinamis). | High | 🟢 **FIXED** |
| **EV-004** | M4 | AC-04.2 | **Ghost value +150.000:** Saldo RT di dashboard akun RW & DKM tidak sinkron — nilai dari data seeder nyasar ke kalkulasi lintas unit. | High | 🟢 **FIXED** |
| **EV-007** | M4 | AC-04.6 & 04.7 | Alur laporan **melompati fase 'Draft'** — laporan langsung berstatus Pending/Submitted tanpa ada fase review mandiri terlebih dahulu. | Medium | Open |
| **EV-016** | M5 | AC-05.7 & 05.8 | Upload gambar **QRIS gagal tersimpan** di pengaturan rekening — setelah disimpan, gambar hilang dan hanya menyisakan data teks. | High | Open |

---

## 🟡 MINOR (8 Bug)

| EV | Modul | AC | Bug Title | Priority |
|----|-------|----|-----------|----------|
| **EV-001** | M4 | AC-04.1 | Overview hanya tampilkan total saldo gabungan — tidak ada **breakdown pemasukan & pengeluaran**. | Low |
| **EV-002** | M4 | AC-04.1 | **Tombol mati** di sudut kanan atas komponen Sheet Saldo (tidak ada aksi apapun saat diklik). | Medium |
| **EV-005** | M4 | AC-04.2 | **Timezone UTC** — jam transaksi tercatat mundur 7 jam dari waktu WIB aktual. | Medium |
| **EV-006** | M4 | AC-04.2 | Fitur **upload lampiran** Bukti Transaksi Kas tidak berfungsi saat menyimpan. | Medium |
| **EV-008** | M4 | AC-04.7 & 04.10 | Opsi filter status laporan **tidak memiliki pilihan 'Rejected'**. | Low |
| **EV-010** | M4 | AC-04.4 | Opsi **'Hapus' hilang** dari dropdown ketika hanya tersisa 1 transaksi di tabel. | Medium |
| **EV-015** | M5 | AC-05.1 | Bendahara RT tidak menemukan **satu warga pun** di detail tagihan insidental — warga belum di-assign ke RT terkait di database. | Medium |
| **EV-019** | M5 | AC-05.2 | **Ikon lonceng (bel) miring/menceng** ke kanan pada tampilan notifikasi pengingat — CSS misaligned. | Low |

---

## 🟢 LOW (3 Bug)

| EV | Modul | AC | Bug Title | Priority |
|----|-------|----|-----------|----------|
| **EV-014** | M5 | AC-05.1 | **Tagihan Rutin otomatis belum diimplementasi** — hanya tagihan insidental yang tersedia (seharusnya auto-generate tiap tgl 1). | Low |
| **EV-018** | M5 | AC-05.2 | **Belum diuji behavior notifikasi jika jumlah banyak** — baru ada 1 notifikasi dalam pengujian. | Low |
| **EV-020** | M5 | M5-36 | Fitur **pengiriman notifikasi ke WhatsApp belum diimplementasi** (tidak tercantum di AC sehingga tidak mempengaruhi status UAT utama). | Medium |

---

## 📊 Rekap Final

| Severity | Jumlah | EV IDs |
|----------|--------|--------|
| 🔴 Critical | **6** | EV-009, EV-011, EV-012, EV-013, EV-017, EV-021 |
| 🟠 Major | **4** | EV-003, EV-004, EV-007, EV-016 |
| 🟡 Minor | **8** | EV-001, EV-002, EV-005, EV-006, EV-008, EV-010, EV-015, EV-019 |
| 🟢 Low | **3** | EV-014, EV-018, EV-020 |
| **TOTAL** | **21** | EV-001 s.d. EV-021 |

---

## 🚀 Rencana Fixing & Status Eksekusi

### FASE 1 — Unblock Testing M4 [Sub Nomor 29-32]
Fokus prioritas utama untuk membuka blocker approval laporan keuangan Ketua RW dan Ketua RT:

| # | EV | Severity | Status | Deskripsi Singkat |
|---|-----|----------|--------|-------------------|
| 1 | **EV-012** | Critical | 🟢 **FIXED** (Ready to Test) | Fix link menu Persetujuan Keuangan di sidebar Ketua RW & routing `rw.persetujuan-keuangan.index` |
| 2 | **EV-013** | Critical | 🟢 **FIXED** (Ready to Test) | Fix HTTP 500 (`QueryException` enum violation status `'Disetujui RT'` -> `'Approved'`) & scope `rt_id` |

> [!NOTE]
> **EV-011** (Cross-tenant access control laporan Bendahara RT) dipending ke fase berikutnya sesuai arahan user.
> Pengujian untuk **AC-04.8 s.d. AC-04.12** dapat segera dieksekusi.

---

### FASE 2 — Bug Fixing Bertahap (Minor → Critical) & EV-021 Prioritas Tinggi

| Urutan | EV | Severity | Bug Title Singkat | Catatan |
|--------|----|----------|-------------------|---------|
| 1 | **EV-021** | Critical | Implementasi Upload Bukti untuk metode Transfer & QRIS | **Prioritas Utama Fase 2** (Unblock testing warga Transfer/QRIS) |
| 2 | EV-005 | Minor | Fix Timezone (1 baris di `config/app.php`) | Quick win |
| 3 | EV-019 | Minor | Fix CSS ikon bel miring | Quick win |
| 4 | EV-001 | Minor | Tambah breakdown pemasukan & pengeluaran di overview | Quick win |
| 5 | EV-002 | Minor | Fix/remove tombol mati di Sheet Saldo | Quick win |
| 6 | EV-006 | Minor | Fix upload lampiran bukti transaksi kas | |
| 7 | EV-008 | Minor | Tambah opsi filter 'Rejected' di laporan | |
| 8 | EV-010 | Minor | Fix logic dropdown agar Hapus muncul di transaksi terakhir | |
| 9 | EV-015 | Minor | Assign warga ke RT di database (KeluargaSeeder) | |
| 10 | EV-016 | Major | Fix upload gambar QRIS di pengaturan rekening | |
| 11 | **EV-003** | Major | 🟢 **FIXED** — Status Sheet Saldo dibuat dinamis ('Balanced' / 'Defisit') | Terselesaikan bersama EV-004 |
| 12 | **EV-004** | Major | 🟢 **FIXED** — Isolasi aset per unit kerja (ghost value +150.000 hilang dari RW & DKM) | Selesai |
| 13 | EV-007 | Major | Implementasi fase Draft sebelum Submit laporan | |
| 14 | EV-011 | Critical | Fix query Broken Access Control laporan Bendahara RT | Dari Fase 1 |
| 15 | EV-017 | Critical | Fix isolasi row rekening Bendahara RW & DKM | |
| 16 | EV-009 | Critical | Lock edit/delete transaksi jika laporan sudah Submitted/Approved | |

> [!NOTE]
> **EV-014** (Tagihan Rutin), **EV-018** (test banyak notif), **EV-020** (WA notif) → konfirmasi ke dosen dulu apakah masuk scope atau skip.
