<?php

namespace App\Services\Panduan;

/**
 * Menyesuaikan teks Manual Book (yang ditulis dengan tokoh dan akun demo) agar cocok
 * dibaca pengguna sungguhan di Pusat Bantuan:
 * - nama tokoh dan username demo diganti padanan netral (mis. "Warga pemohon"),
 * - kalimat yang hanya menggambarkan data demo dihapus,
 * - rujukan ke buku/cerita lain dihapus,
 * - pernyataan yang sudah tidak berlaku (menu Bantuan belum punya halaman) diperbarui.
 * Diterapkan pada HTML hasil parser; teks di dalam tag <strong> ikut ditangani.
 */
class PenyesuaianTeksPanduan
{
    /** Topik (judul) yang dibuang seluruhnya. */
    private const TOPIK_DIBUANG = [
        '/^Menu (Pusat )?Bantuan\b.*(tidak membuka|belum)/iu',
        // Pertanyaan FAQ yang hanya membahas kondisi data demo.
        '/\bdemo\b/iu',
    ];

    /** Awal kalimat yang kalimatnya dihapus seluruhnya. */
    private const KALIMAT_DIHAPUS = [
        'Pada data demo',
        'Surat contoh Pak Budi Santoso',
        'Mas Dimas melihat',
        'Lihat Buku ',
    ];

    /** Penggantian frasa (urutan penting: yang lebih spesifik lebih dulu). */
    private const GANTI = [
        // Username demo pada panduan masuk.
        'Ketik budisantoso pada kolom' => 'Ketik username Anda pada kolom',
        'Ketik budisampurno pada kolom' => 'Ketik username Anda pada kolom',
        'Masukkan budisampurno dan password' => 'Masukkan username dan password Anda',
        ', seperti budisantoso,' => ',',
        ', seperti budisampurno,' => ' (misalnya Ketua RT yang juga warga),',
        'akun budisampurno mencoba masuk' => 'pengguna mencoba masuk',
        'Contohnya budisampurno, yang digunakan sebagai Ketua RT 03 sekaligus Warga.' => 'Contohnya akun Ketua RT yang juga terdaftar sebagai warga.',
        'Akun budisampurno memiliki dua peran: Ketua RT 03 dan Warga.' => 'Contohnya akun Ketua RT yang juga terdaftar sebagai warga.',
        '(seperti budisampurno)' => '(misalnya Ketua RT yang juga warga)',
        'budisampurno punya dua peran.' => 'Akun Ketua RT yang juga warga punya dua peran.',

        // Tokoh demo.
        '(misalnya Pak Budi Sampurno yang juga warga)' => '(misalnya Ketua RT yang juga warga)',
        'Pak Budi Sampurno menerima notifikasi' => 'Pemilik usaha menerima notifikasi',
        'Profil UMKM Jasa Service Elektronik Pak Budi berhasil' => 'Profil UMKM [nama usaha] berhasil',
        'Pak Budi Santoso dan pengurus RW' => 'Warga pemohon dan pengurus RW',
        'Pak Budi Santoso dan RW' => 'Warga pemohon dan RW',
        'Pak Budi Santoso mendapat' => 'Warga pemohon mendapat',
        'Pak Budi Santoso menerima' => 'Warga pemohon menerima',
        'Pak Budi melihat kartu bulannya' => 'Warga melihat kartu bulannya',
        'Baris Pak Budi berstatus' => 'Baris warga tersebut berstatus',
        'setelah Pak Budi bayar iuran' => 'setelah warga membayar iuran',
        'Pak Ahmad Zaki' => 'Ketua RT',
        'Bu Siti Aminah' => 'Bendahara RT',
        'Bu Siti' => 'Bendahara RT',
        'Pak Hendra' => 'Bendahara RW',
        'Berita RT 03 tidak terlihat oleh Mas Dimas.' => 'Berita RT lain tidak terlihat oleh Anda.',
        'pada Dashboard Mas Dimas' => 'pada Dashboard Anda',
        'Mbak Nisa tidak bisa' => 'Anda tidak bisa',
        ' (Contoh Mas Dimas)' => '',

        // Pernyataan lama tentang menu bantuan.
        'Menu Pusat Bantuan terlihat di bagian bawah tetapi belum memiliki halaman.' => 'Menu Pusat Bantuan di bagian bawah membuka panduan ini.',
    ];

    /**
     * Frasa yang membuat kalimatnya dihapus seluruhnya (kalimat yang hanya menjelaskan kondisi
     * data demo, mis. "Data demo awal memang tidak punya surat menunggu RW.").
     */
    private const KALIMAT_MENGANDUNG_DIHAPUS = [
        'data demo', 'demo awal', 'Berita demo', 'Transaksi demo', 'Daftar demo',
        'Ringkasnya: ketuart01rw21', 'Rekening RT 01 berbeda', 'Pada RT 01:',
        'Agenda Syukuran Pos Ronda Baru RT 03 ikut',
    ];

    /**
     * Penggantian berbasis pola, diterapkan setelah GANTI. Urutan penting.
     * Tujuan: data demo (kredensial, nama, nominal, nomor RT, judul contoh) menjadi kalimat umum
     * atau jelas ditandai sebagai contoh.
     */
    private const POLA = [
        // ---- Kredensial demo (jangan pernah tampil di aplikasi sungguhan).
        '/,?\s*(lalu\s+)?masuk dengan (akun|username)(?:\s|<\/?strong>)+[\w.]+(?:(?:\s|<\/?strong>)+\([^)]*\))?(?:(?:\s|<\/?strong>)+atau(?:\s|<\/?strong>)+[\w.]+(?:(?:\s|<\/?strong>)+\([^)]*\))?)?(?:\s|<\/?strong>)+dan kata sandi(?:\s|<\/?strong>)+password(<\/strong>)?/u' => ', lalu masuk dengan username dan kata sandi Anda',
        '/Isi username(?:\s|<\/?strong>)+superadmin(?:\s|<\/?strong>)+dan kata sandi(?:\s|<\/?strong>)+password(<\/strong>)?(?:\s*\(akun demo\))?\.?/u' => 'Isi username dan kata sandi akun Admin Aplikasi Anda.',
        '/Pastikan Anda masuk sebagai(?:\s|<\/?strong>)+adminrw21(<\/strong>)?,/u' => 'Pastikan Anda masuk dengan akun Admin RW,',
        '/Akun(?:\s|<\/?strong>)+superadmin(<\/strong>)? hanya punya satu peran/u' => 'Akun Admin Aplikasi biasanya hanya punya satu peran',
        '/kata sandi(?:\s|<\/?strong>)+superadmin/u' => 'kata sandi Admin Aplikasi',
        '/\((?:<strong>)?superadmin(?:<\/strong>)?\)/u' => '(akun Anda)',
        '/Ketik(?:\s|<\/?strong>)+operator_rt3(<\/strong>)? pada kolom/u' => 'Ketik username akun yang dicari (misalnya operator_rt3) pada kolom',
        '/Peran operator_rt3 berhasil/u' => 'Peran [username] berhasil',
        '/(akun|Edit Peran|Saat)(?:\s|<\/?strong>)+operator_rt3(<\/strong>)?/u' => '$1 tersebut',
        '/Ketik(?:\s|<\/?strong>)+donisetiawan(<\/strong>)? pada kolom/u' => 'Ketik username akun yang dicari pada kolom',
        '/Akun nonaktif donisetiawan/u' => 'Akun nonaktif',
        '/akun(?:\s|<\/?strong>)+donisetiawan(<\/strong>)?/u' => 'akun tersebut',
        '/Pak Doni/u' => 'pemilik akun',
        '/Ketua RT 03 untuk proses akun keluarganya/u' => 'Ketua RT-nya untuk proses akun keluarganya',
        '/Akun(?:\s|<\/?strong>)+opkeuanganrt01rw21(?:[^.<]|<\/?strong>)*?BSI(?:[^<]|<\/?strong>)*?RT 03\.\s*/u' => '',
        '/Username(?:<\/strong>)?:(?:\s|<\/?strong>)+opkontenrt05rw21/u' => 'Username</strong>: misalnya opkontenrt05rw21',

        // ---- Nama warga demo.
        '/biarkan(?:\s|<\/?strong>)+Budi Santoso(<\/strong>)?/u' => 'biarkan nama Anda',
        '/pilih(?:\s|<\/?strong>)+Andi Saputra(<\/strong>)?\s*\(Anak\)/u' => 'pilih nama anggota keluarga, misalnya anak Anda',
        '/misalnya(?:\s|<\/?strong>)+Budi Santoso(<\/strong>)?/u' => 'misalnya nama kepala keluarga',
        '/(pada )?baris(?:\s|<\/?strong>)+Siti Rahma(<\/strong>)?/u' => '$1baris anggota yang dimaksud',
        '/keluarga(?:\s|<\/?strong>)+Hasan Basri(<\/strong>)?/u' => 'keluarga yang dimaksud',
        '/Bila(?:\s|<\/?strong>)+Siti Rahma(<\/strong>)? belum/u' => 'Bila pemegang akun baru belum',
        '/\(Siti Rahma\)/u' => '(nama pemegang akun)',
        '/(?:<strong>)?Siti Rahma(?:<\/strong>)? menjadi pemegang/u' => 'Anggota tersebut menjadi pemegang',
        '/untuk(?:\s|<\/?strong>)+Siti Rahma(<\/strong>)?/u' => 'untuk anggota tersebut',
        '/pilih(?:\s|<\/?strong>)+Bambang Sutrisno(<\/strong>)?/u' => 'pilih nama pejabat baru',
        '/menampilkan(?:\s|<\/?strong>)+Bambang Sutrisno(<\/strong>)?/u' => 'menampilkan nama pejabat tersebut',
        '/\((?:<strong>)?3 jiwa(?:<\/strong>)?\)/u' => '',

        // ---- Usaha dan produk demo.
        '/pada(?:\s|<\/?strong>)+Warung Nasi Ibu Ani(<\/strong>)?/u' => 'pada usaha yang ingin dilihat',
        '/Halaman usaha Warung Nasi Ibu Ani/u' => 'Halaman usaha',
        '/Klik produk(?:\s|<\/?strong>)+“Patung Kayu Jati Tradisional”(<\/strong>)?/u' => 'Klik salah satu produk',
        '/pilih(?:\s|<\/?strong>)+Koperasi Warga Tanimulya(<\/strong>)?\s*\(bertanda Ditolak\)/u' => 'pilih usaha yang bertanda Ditolak',
        '/(?:<strong>)?Koperasi Warga Tanimulya(?:<\/strong>)?/u' => '[nama usaha]',

        // ---- Iuran, nominal, rekening.
        '/nama iuran \((?:<strong>)?Iuran Bulanan Warga(?:<\/strong>)?\) dan nominal \((?:<strong>)?Rp 60\.000 \/ bulan(?:<\/strong>)? untuk RT 03\)/u' => 'nama iuran (misalnya Iuran Bulanan Warga) dan nominal per bulan (misalnya Rp 60.000 / bulan)',
        '/<p>(?:[^<]|<\/?strong>)*data demo(?:[^<]|<\/?strong>)*:\s*<\/p>\s*<div class="table-responsive">.*?<\/div>/siu' => '',
        '/Pada baris(?:\s|<\/?strong>)+Iuran Perbaikan Got(<\/strong>)?, tekan/u' => 'Pada baris iuran yang ingin dibayar, tekan',
        '/Detail Pembayaran Iuran Perbaikan Got/u' => 'Detail Pembayaran Iuran',
        '/pembayaran “Iuran Perbaikan Got” milik warga RT lain/u' => 'pembayaran iuran RW milik warga RT lain',
        '/pembayaran warga pada Iuran Perbaikan Got/u' => 'pembayaran warga pada iuran RW yang dikumpulkan lewat RT',
        '/Untuk iuran(?:\s|<\/?strong>)+Rp ?20\.000(<\/strong>)? yang dibayar/u' => 'Untuk iuran yang dibayar',
        '/Minimal Rp 30\.000/u' => 'Minimal Rp (nominal)',
        '/minimal Rp 30\.000/u' => 'minimal Rp (nominal)',
        '/Rp 50\.000 per KK/u' => 'Rp (nominal) per KK',
        '/sebesar Rp 50\.000 dicatat/u' => 'sebesar Rp (jumlah) dicatat',
        '/rekening(?:\s|<\/?strong>)+(BSI|BCA)(<\/strong>)?\s+(RT|RW|DKM)/u' => 'rekening $3',
        '/dari \d+ (pengguna|kategori)/u' => 'dari (jumlah) $1',

        // ---- Nomor RT demo menjadi umum.
        '/Ketua RT 03 tidak melihat penduduk RT 01, dan sebaliknya\./u' => 'Ketua RT hanya melihat penduduk di RT-nya sendiri.',
        '/\(Tagihan “[^”]*” milik RT 0\d tidak tampil untuk keluarga RT 0\d\.\)/u' => '(Tagihan iuran milik RT lain tidak tampil untuk keluarga Anda.)',
        '/Tab(?:\s|<\/?strong>)+RT 0\d(<\/strong>)? menunjukkan/u' => 'Tab RT Anda menunjukkan',
        '/asal(?:\s|<\/?strong>)+RT 03(<\/strong>)?\s*\(atau(?:\s|<\/?strong>)+RT 01(<\/strong>)?, sesuai akun\)/u' => 'asal RT akun Anda',
        '/menyebut asal(?:\s|<\/?strong>)+RT 0\d(<\/strong>)?/u' => 'menyebut asal RT-nya',
        '/pilih(?:\s|<\/?strong>)+RT 0\d(<\/strong>)? dan tekan/u' => 'pilih RT yang diinginkan dan tekan',
        '/Lencana asal menunjukkan(?:\s|<\/?strong>)+RW(<\/strong>)?,(?:\s|<\/?strong>)+RT 01(<\/strong>)?, atau(?:\s|<\/?strong>)+RT 03(<\/strong>)?\.?/u' => 'Lencana asal menunjukkan RW atau nomor RT asal konten.',
        '/\((?:<strong>)?RW(?:<\/strong>)? atau(?:\s|<\/?strong>)+RT 03(<\/strong>)?\)/u' => '(RW atau nomor RT)',
        '/mempublikasikan(?:\s|<\/?strong>)+Rencana Pemasangan CCTV Lingkungan(<\/strong>)?\.?/u' => 'mempublikasikan [judul konten].',
        '/Kategori(?:\s|<\/?strong>)+Dana Kerja Bakti(<\/strong>)? tampil/u' => 'Kategori baru tampil',
        '/Kategori RW demo \((?:[^()]|<\/?strong>)*\) tidak diterapkan untuk RT, jadi tidak tampil di akun ini/u' => 'Kategori RW yang tidak diterapkan untuk RT (misalnya Iuran Fasilitas) tidak tampil di akun ini',
        '/ditandai dengan asal RT 03 \(atau RT 01, sesuai akun\)/u' => 'ditandai dengan asal RT akun Anda',
        '/termasuk RT 03\./u' => 'termasuk RT lain.',
        '/padahal RT 03\./u' => 'padahal bukan RT 01.',
        '/Lencana asal menunjukkan RW, RT 01, atau RT 03\./u' => 'Lencana asal menunjukkan RW atau nomor RT asal konten.',
        '/\(RW atau RT 03\)/u' => '(RW atau nomor RT)',
        '/Pastikan terpilih(?:\s|<\/?strong>)+RT 03(<\/strong>)?\./u' => 'Pastikan RT yang benar sudah terpilih.',
        '/Mengganti Ketua RT 03\?/u' => 'Mengganti Ketua RT?',
        '/pada baris(?:\s|<\/?strong>)+RT 03(<\/strong>)?/u' => 'pada baris RT yang bersangkutan',
        '/pilih(?:\s|<\/?strong>)+RT 03(<\/strong>)?\s*\(default/u' => 'pilih RT yang diinginkan (default',
        '/Data penduduk di RT 03\./u' => 'Data penduduk di RT (nomor RT).',
        '/Export Data Penduduk \(RT 03\)/u' => 'Export Data Penduduk (nomor RT)',
        '/untuk RT 03 bertambah/u' => 'untuk RT Anda bertambah',
        '/Setoran ke RW - RT 03/u' => 'Setoran ke RW - RT (nomor RT)',
        '/Sebagai warga RT 0\d\b/u' => 'Sebagai warga RT',
        '/(Kerja Bakti Membersihkan Selokan|Senam Pagi Warga|Lapangan dekat Pos Ronda) RT 03/u' => '$1 RT',
        '/Ketua RT 0\d\b/u' => 'Ketua RT',
        '/\b([Ww]arga) RT 0\d\b/u' => '$1 RT tersebut',
        '/\b(kas|Keuangan|Dasbor|Berita|Agenda|Pengumuman|Pengumuman Baru|Iuran Lainnya|Buku kas|Laporan Keuangan|Rekening|Detail Setoran|filter|asal) RT 0\d\b/u' => '$1 RT',
        '/(berita|konten) dari(?:\s|<\/?strong>)+RT 0\d\b(<\/strong>)?/u' => '$1 dari RT',

        // ---- Contoh isian: tandai sebagai contoh.
        '/Ketik:\s+(?=(?:<strong>)?(Rencana Pemasangan|Kerja Bakti Membersihkan|Pembukaan rekening))/u' => 'Ketik judul atau isian, misalnya ',
        '/Ketik judul(?:\s|<\/?strong>)+(?=(?:<strong>)?(Rapat Pengurus|Senam Pagi))/u' => 'Ketik judul, misalnya ',
        '/Ketik(?:\s|<\/?strong>)+(?=(?:<strong>)?(Lapangan dekat|Perlengkapan Kerja|Pembayaran Listrik|Sumbangan Dana|Dana Kerja Bakti))/u' => 'Ketik isian, misalnya ',
        '/ketik(?:\s|<\/?strong>)+(?=(?:<strong>)?Dana Kerja Bakti)/u' => 'ketik, misalnya, ',
        '/Edit draf Lomba Kebersihan Antar Gang/u' => 'Edit draf berita',
        // Judul konten/iuran demo yang dikutip: beri keterangan "(contoh)" bila belum ditandai.
        '/(?<!misalnya |seperti |Contoh: |contoh )“((?:Penghijauan Taman|Pendataan Warga Baru|Kerja Bakti Bersih Lingkungan|Rapat Pengurus dan Warga|Tabligh Akbar|Senam Sehat|Lomba Kebersihan|Jadwal Pengangkutan|Perbaikan Jalan Utama|Iuran Perbaikan Got|Perbaikan Toa Masjid|Iuran Perbaikan Jalan)[^”]*)”/u' => '“$1” (contoh)',
    ];

    /** Nama yang tidak boleh tersisa; dipakai untuk peringatan saat impor. */
    public const NAMA_DEMO = '/Pak Budi|Bu Siti|Mbak Nisa|Mas Dimas|Mbak Laras|Bu Rina|Bu Wati|Mas Fajar|Pak Haji Rahmat|Pak Dedi|Pak Hendra|Pak Ahmad|budisantoso|budisampurno|sandi password|kata sandi password|data demo|Siti Rahma|Hasan Basri|Andi Saputra|Budi Suseno|Bambang Sutrisno|Warung Nasi Ibu Ani|Koperasi Warga|7634523890|0998988997|19090980389|34521980/u';

    public function buangTopik(string $judul): bool
    {
        foreach (self::TOPIK_DIBUANG as $pola) {
            if (preg_match($pola, $judul)) {
                return true;
            }
        }

        return false;
    }

    public function judul(string $judul): string
    {
        return trim($this->pola($this->ganti($judul)));
    }

    public function html(string $html): string
    {
        foreach (self::KALIMAT_DIHAPUS as $awal) {
            $html = $this->hapusKalimat($html, $awal);
        }
        // Spasi di ujung teks tebal ("RT 03 </strong>(atau") dipindah ke luar tag agar pola cocok.
        $html = preg_replace('#\s+</strong>(?=[\p{L}\p{N}(“])#u', '</strong> ', $html);
        $html = preg_replace('#\s+</strong>#u', '</strong>', $html);
        $html = $this->ganti($html);

        // Data bawaan aplikasi (bukan data demo) cukup disebut "bawaan".
        $html = preg_replace('/Pengaturan bawaan data demo:/u', 'Pengaturan bawaan:', $html);
        $html = preg_replace('/Kategori awal pada data demo:/u', 'Kategori awal bawaan aplikasi:', $html);
        $html = preg_replace('/Pada data demo, kategori buatan RW antara lain:/u', 'Kategori bawaan untuk RW antara lain:', $html);
        // Keterangan "(pada data demo ...)" di dalam kalimat dibuang, kalimatnya dipertahankan.
        $html = preg_replace('/\s*\((?:[^()<]|<\/?strong>)*\b(data demo|akun demo)\b(?:[^()<]|<\/?strong>)*\)/iu', '', $html);
        $html = $this->pola($html);
        foreach (self::KALIMAT_MENGANDUNG_DIHAPUS as $frasa) {
            $html = $this->hapusKalimatMengandung($html, $frasa);
        }

        // Rujukan ke buku/cerita lain tidak relevan di Pusat Bantuan.
        $html = preg_replace('/\s*\((lihat\s+)?Buku\s+\d+[^)]*\)/iu', '', $html);
        $html = preg_replace('/,?\s*cerita\s+C-\d+(\s+bagian\s+\d+)?/iu', '', $html);

        $html = $this->seimbangkanTebal($html);

        // Bagian yang menjadi kosong setelah penyesuaian dibuang.
        $html = preg_replace('#<div class="pb-tips"><strong>Tips:</strong>\s*</div>#u', '', $html);
        $html = preg_replace('#<(p|li|strong)>\s*</\1>#u', '', $html);
        $html = preg_replace('#<(ul|ol)( [^>]*)?>\s*</\1>#u', '', $html);

        return $html;
    }

    private function pola(string $teks): string
    {
        foreach (self::POLA as $pola => $ganti) {
            $teks = preg_replace($pola, $ganti, $teks);
        }

        return $teks;
    }

    /** Hapus kalimat (dibatasi titik atau akhir paragraf/butir) yang memuat frasa tertentu. */
    private function hapusKalimatMengandung(string $html, string $frasa): string
    {
        $isi = '(?:[^.<]|<\/?strong>)*?';
        $pola = '/(?<=^|>|\.\s|\.)\s*' . $isi . preg_quote($frasa, '/') . '(?:[^.<]|<\/?strong>)*(?:\.|(?=<\/(?:p|li|td|th|div|figcaption)>))\s*/iu';

        return preg_replace($pola, '', $html);
    }

    /**
     * Rapikan tag <strong> setelah penggantian: buang penutup tanpa pembuka dan tutup tag yang
     * masih terbuka di akhir blok, agar teks sesudahnya tidak ikut tebal.
     */
    private function seimbangkanTebal(string $html): string
    {
        $bagian = preg_split('#(</?strong>|</?(?:p|li|td|th|div|h3|h4|figcaption|ul|ol|section|figure)\b[^>]*>)#u', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        $hasil = '';
        $terbuka = false;
        foreach ($bagian as $b) {
            if ($b === '<strong>') {
                if (! $terbuka) {
                    $hasil .= $b;
                    $terbuka = true;
                }
            } elseif ($b === '</strong>') {
                if ($terbuka) {
                    $hasil .= $b;
                    $terbuka = false;
                }
            } elseif (preg_match('#^</?(?:p|li|td|th|div|h3|h4|figcaption|ul|ol|section|figure)\b#u', $b)) {
                if ($terbuka) {
                    $hasil .= '</strong>';
                    $terbuka = false;
                }
                $hasil .= $b;
            } else {
                $hasil .= $b;
            }
        }

        return $hasil . ($terbuka ? '</strong>' : '');
    }

    private function ganti(string $teks): string
    {
        foreach (self::GANTI as $cari => $ganti) {
            // Nama di dalam <strong> tetap tertangkap: sisipkan pola tag opsional di antara kata.
            $awalSpasi = preg_match('/^\s/u', $cari) ? '\s*' : '';
            $pola = $awalSpasi . implode('(?:\s*<\/?strong>\s*|\s+)', array_map(fn ($k) => preg_quote($k, '/'), preg_split('/\s+/u', trim($cari))));
            $teks = preg_replace('/' . $pola . '/u', $ganti, $teks);
        }

        return $teks;
    }

    private function hapusKalimat(string $html, string $awal): string
    {
        $isi = '(?:[^.<]|<\/?strong>)*';

        return preg_replace('/(?<=^|>|\.\s|\.)\s*' . preg_quote($awal, '/') . $isi . '\.\s*/u', '', $html);
    }
}
