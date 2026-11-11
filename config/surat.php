<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Surat Pengantar RT/RW
|--------------------------------------------------------------------------
| Sumber tunggal untuk template surat (pdf/surat.blade.php) dan form
| pengajuan warga. Kunci di 'jenis' HARUS sama dengan nilai
| App\Models\PengajuanSurat::TIPE_SURAT.
|
| Tiap jenis:
|  - judul          : judul surat yang dicetak.
|  - tujuan         : kalimat peruntukan. Token {keperluan} diganti isian
|                     "keperluan" pemohon (kosong -> titik-titik).
|  - alamat         : 'kk' = alamat sesuai KK, 'domisili' = alamat domisili.
|  - sebagai_pelapor: true bila pemohon hanya melaporkan peristiwa
|                     (kematian/kelahiran/ahli waris), bukan subjek surat.
|  - fields         : isian tambahan khusus jenis surat.
|        key, label, type (text|textarea|date|time|select), options?,
|        opsional? (true = tidak dicetak bila kosong).
*/

return [

    // Identitas pada kop surat dan penanda wilayah.
    'kop' => [
        'baris_1'      => 'RUKUN WARGA 21',
        'baris_2'      => 'KOMPLEK PURI CIPAGERAN INDAH II',
        'baris_3'      => 'DESA TANIMULYA, KECAMATAN NGAMPRAH, KABUPATEN BANDUNG BARAT',
        'kode_rw'      => '21',
        'tempat_surat' => 'Tanimulya',
        'logo'         => 'images/logo_rw21.png',
    ],

    'jenis' => [

        'Kartu Keluarga' => [
            'judul'  => 'SURAT PENGANTAR KARTU KELUARGA',
            'tujuan' => 'Surat keterangan ini diperlukan sebagai pengantar untuk pengurusan Kartu Keluarga (KK) pada instansi yang berwenang.',
            'alamat' => 'kk',
            'fields' => [
                ['key' => 'alasan', 'label' => 'Alasan Pengajuan', 'type' => 'select', 'opsional' => true,
                    'options' => ['Pembuatan baru', 'Perubahan data', 'Penggantian karena hilang/rusak']],
            ],
        ],

        'Kartu Tanda Penduduk' => [
            'judul'  => 'SURAT PENGANTAR KARTU TANDA PENDUDUK',
            'tujuan' => 'Surat keterangan ini diperlukan sebagai pengantar untuk pengurusan Kartu Tanda Penduduk (KTP) pada instansi yang berwenang.',
            'alamat' => 'kk',
            'fields' => [
                ['key' => 'alasan', 'label' => 'Alasan Pengajuan', 'type' => 'select', 'opsional' => true,
                    'options' => ['Pembuatan baru', 'Perpanjangan', 'Perubahan data', 'Penggantian karena hilang/rusak']],
            ],
        ],

        'Surat Keterangan Ahli Waris' => [
            'judul'            => 'SURAT KETERANGAN AHLI WARIS',
            'tujuan'           => 'Surat keterangan ini diperlukan sebagai pengantar untuk pengurusan Surat Keterangan Ahli Waris.',
            'alamat'           => 'kk',
            'sebagai_pelapor'  => true,
            'fields'           => [
                ['key' => 'nama_pewaris', 'label' => 'Nama Pewaris (yang meninggal)', 'type' => 'text'],
                ['key' => 'tanggal_wafat', 'label' => 'Tanggal Wafat', 'type' => 'date'],
                ['key' => 'daftar_ahli_waris', 'label' => 'Daftar Ahli Waris (satu nama per baris)', 'type' => 'textarea'],
            ],
        ],

        'Surat Kelahiran' => [
            'judul'            => 'SURAT KETERANGAN KELAHIRAN',
            'tujuan'           => 'Surat keterangan ini diperlukan sebagai pengantar untuk pengurusan Akta/Surat Kelahiran.',
            'alamat'           => 'kk',
            'sebagai_pelapor'  => true,
            'fields'           => [
                ['key' => 'nama_bayi', 'label' => 'Nama Bayi', 'type' => 'text'],
                ['key' => 'jenis_kelamin_bayi', 'label' => 'Jenis Kelamin Bayi', 'type' => 'select',
                    'options' => ['Laki-laki', 'Perempuan']],
                ['key' => 'tanggal_lahir_bayi', 'label' => 'Tanggal Lahir Bayi', 'type' => 'date'],
                ['key' => 'jam_lahir_bayi', 'label' => 'Jam Lahir', 'type' => 'time'],
                ['key' => 'tempat_lahir_bayi', 'label' => 'Tempat Lahir', 'type' => 'text'],
            ],
        ],

        'Surat Kematian' => [
            'judul'            => 'SURAT KETERANGAN KEMATIAN',
            'tujuan'           => 'Surat keterangan ini diperlukan sebagai pengantar untuk pengurusan Surat Keterangan Kematian.',
            'alamat'           => 'kk',
            'sebagai_pelapor'  => true,
            'fields'           => [
                ['key' => 'nama_almarhum', 'label' => 'Nama yang Meninggal', 'type' => 'text'],
                ['key' => 'tanggal_meninggal', 'label' => 'Tanggal Meninggal', 'type' => 'date'],
                ['key' => 'jam_meninggal', 'label' => 'Jam Meninggal', 'type' => 'time'],
                ['key' => 'tempat_meninggal', 'label' => 'Tempat Meninggal', 'type' => 'text'],
                ['key' => 'tempat_dimakamkan', 'label' => 'Dimakamkan di', 'type' => 'text'],
                ['key' => 'penyebab', 'label' => 'Penyebab', 'type' => 'text', 'opsional' => true],
            ],
        ],

        'Surat Keterangan Domisili' => [
            'judul'  => 'SURAT KETERANGAN DOMISILI',
            'tujuan' => 'Menerangkan bahwa yang bersangkutan benar berdomisili dan bertempat tinggal di alamat tersebut di atas. Surat keterangan ini diperlukan untuk {keperluan}.',
            'alamat' => 'domisili',
            'fields' => [
                ['key' => 'lama_tinggal', 'label' => 'Lama Tinggal', 'type' => 'text', 'opsional' => true],
            ],
        ],

        'Surat Keterangan Miskin / Tidak Mampu' => [
            'judul'  => 'SURAT KETERANGAN TIDAK MAMPU',
            'tujuan' => 'Menerangkan bahwa yang bersangkutan tergolong keluarga kurang mampu. Surat keterangan ini diperlukan untuk {keperluan}.',
            'alamat' => 'kk',
            'fields' => [
                ['key' => 'penghasilan', 'label' => 'Perkiraan Penghasilan per Bulan', 'type' => 'text', 'opsional' => true],
            ],
        ],

        'Surat Keterangan Serbaguna' => [
            'judul'  => 'SURAT KETERANGAN',
            'tujuan' => 'Surat keterangan ini diperlukan untuk {keperluan}.',
            'alamat' => 'kk',
            'fields' => [],
        ],

        'Surat Keterangan Kelakuan Baik / Catatan Kepolisian' => [
            'judul'  => 'SURAT KETERANGAN KELAKUAN BAIK',
            'tujuan' => 'Berdasarkan pengamatan dan sepengetahuan kami, yang bersangkutan benar warga di lingkungan kami, berkelakuan baik, serta belum pernah terlibat tindak pidana atau perkara kriminal. Surat keterangan ini dibuat untuk keperluan {keperluan}.',
            'alamat' => 'kk',
            'fields' => [],
        ],
    ],
];
