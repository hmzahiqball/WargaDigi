<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use Illuminate\Database\Seeder;

/**
 * Seeder DEMO (showcase): memperbanyak data penduduk agar distribusi
 * demografi (gender, usia, sebaran RT) terisi pada halaman publik.
 *
 * Membuat beberapa Keluarga baru per RT (kode_rt 01-06) beserta 2-5 anggota
 * Penduduk masing-masing. NIK & no_kk memakai prefix '321702' yang berbeda
 * dari data existing ('3217010101010xxx' dan '3204xxx') sehingga tidak bentrok.
 *
 * Idempoten: jika jumlah penduduk sudah > 150, seeder dilewati agar tidak dobel.
 */
class PendudukDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Guard idempotensi: hindari penggandaan saat di-seed ulang.
        if (Penduduk::count() > 150) {
            return;
        }

        $nama = [
            'L' => [
                'Agus', 'Bayu', 'Cahyo', 'Dadang', 'Eko', 'Firman', 'Galih', 'Hadi',
                'Indra', 'Joko', 'Kurnia', 'Lukman', 'Mulyana', 'Nanang', 'Oman',
                'Prasetyo', 'Qomar', 'Rahmat', 'Supriyadi', 'Taufik', 'Usman',
                'Wawan', 'Yoga', 'Zainal', 'Arman', 'Bagas', 'Candra', 'Deni',
            ],
            'P' => [
                'Ani', 'Bunga', 'Citra', 'Dewi', 'Endah', 'Fitri', 'Gita', 'Hana',
                'Intan', 'Juwita', 'Kartika', 'Lestari', 'Maya', 'Nurul', 'Oktavia',
                'Puspita', 'Qotrunnada', 'Rahma', 'Sari', 'Tuti', 'Umi', 'Wulan',
                'Yuni', 'Zahra', 'Ayu', 'Bella', 'Cindy', 'Dinda',
            ],
        ];

        $marga = [
            'Wijaya', 'Santoso', 'Pratama', 'Nugroho', 'Kusuma', 'Hidayat',
            'Permana', 'Saputra', 'Gunawan', 'Lesmana', 'Setiawan', 'Firdaus',
            'Maulana', 'Ramadhan', 'Suryana', 'Hartono',
        ];

        $pekerjaanDewasa = [
            'Karyawan Swasta', 'Wiraswasta', 'Pedagang', 'Guru', 'Buruh Harian',
            'Pegawai Negeri Sipil', 'Petani', 'Sopir', 'Teknisi', 'Perawat',
            'Montir', 'Satpam', 'Pensiunan', 'Mengurus Rumah Tangga',
        ];

        $agamaList = ['Islam', 'Islam', 'Islam', 'Islam', 'Islam', 'Kristen', 'Katolik'];

        // Rentang kelahiran agar seluruh bucket usia pada halaman Informasi terisi.
        // (0-4, 5-14, 15-24, 25-34, 35-44, 45-54, 55+)
        $tahunBucket = [
            [2021, 2024], // balita
            [2011, 2020], // anak
            [2001, 2010], // remaja/dewasa muda
            [1991, 2000],
            [1981, 1990],
            [1971, 1980],
            [1945, 1969], // lansia
        ];

        $kkCounter = 1;    // untuk membangun no_kk & id keluarga unik
        $pendudukCounter = 1; // untuk membangun nik & id penduduk unik

        foreach (['01', '02', '03', '04', '05', '06'] as $kodeRt) {
            $rt = MasterRt::where('kode_rt', $kodeRt)->first();
            if (!$rt) {
                continue;
            }

            // 6-8 keluarga demo per RT -> total ~36-48 KK, 180-220 jiwa.
            $jumlahKk = random_int(6, 8);

            for ($k = 0; $k < $jumlahKk; $k++) {
                $noKk = '3217' . '02' . str_pad((string) $kkCounter, 10, '0', STR_PAD_LEFT);
                $nikKepala = $this->buildNik($pendudukCounter);

                $margaKeluarga = $marga[array_rand($marga)];

                $keluarga = Keluarga::updateOrCreate(
                    ['no_kk' => $noKk],
                    [
                        'nik_kepala_keluarga' => $nikKepala,
                        'alamat' => 'Jl. Demo No. ' . ($kkCounter) . ', RT ' . $kodeRt . ' / RW 21, Tanimulya',
                        'rt_id' => $rt->id,
                        'no_wa' => '0812' . str_pad((string) (3000000 + $kkCounter), 8, '0', STR_PAD_LEFT),
                        'status_aktivasi' => 'Active',
                    ]
                );

                // Jumlah anggota: 2-5 per KK.
                $jumlahAnggota = random_int(2, 5);

                for ($a = 0; $a < $jumlahAnggota; $a++) {
                    if ($a === 0) {
                        // Kepala keluarga (laki-laki, dewasa 25+ tahun).
                        $jk = 'L';
                        $hubungan = 'Kepala Keluarga';
                        $bucket = $tahunBucket[[3, 4, 5, 6][array_rand([3, 4, 5, 6])]];
                        $perkawinan = 'Kawin';
                        $pekerjaan = $pekerjaanDewasa[array_rand($pekerjaanDewasa)];
                        $nik = $nikKepala;
                        $namaLengkap = $nama['L'][array_rand($nama['L'])] . ' ' . $margaKeluarga;
                    } elseif ($a === 1) {
                        // Istri (perempuan, dewasa).
                        $jk = 'P';
                        $hubungan = 'Istri';
                        $bucket = $tahunBucket[[3, 4, 5][array_rand([3, 4, 5])]];
                        $perkawinan = 'Kawin';
                        $pekerjaan = $pekerjaanDewasa[array_rand($pekerjaanDewasa)];
                        $pendudukCounter++;
                        $nik = $this->buildNik($pendudukCounter);
                        $namaLengkap = $nama['P'][array_rand($nama['P'])] . ' ' . $margaKeluarga;
                    } else {
                        // Anak (gender campur, usia balita s/d dewasa muda 0-24 tahun).
                        $jk = random_int(0, 1) ? 'L' : 'P';
                        $hubungan = 'Anak';
                        $bucket = $tahunBucket[[0, 1, 2][array_rand([0, 1, 2])]];
                        $perkawinan = 'Belum Kawin';
                        $pekerjaan = ($bucket[1] >= 2010) ? 'Pelajar' : 'Belum/Tidak Bekerja';
                        $pendudukCounter++;
                        $nik = $this->buildNik($pendudukCounter);
                        $namaLengkap = $nama[$jk][array_rand($nama[$jk])] . ' ' . $margaKeluarga;
                    }

                    $tahunLahir = random_int($bucket[0], $bucket[1]);
                    $tanggalLahir = sprintf('%04d-%02d-%02d', $tahunLahir, random_int(1, 12), random_int(1, 28));

                    Penduduk::updateOrCreate(
                        ['nik' => $nik],
                        [
                            'keluarga_id' => $keluarga->id,
                            'nama_lengkap' => $namaLengkap,
                            'jenis_kelamin' => $jk,
                            'tempat_lahir' => 'Bandung Barat',
                            'tanggal_lahir' => $tanggalLahir,
                            'agama' => $agamaList[array_rand($agamaList)],
                            'pekerjaan' => $pekerjaan,
                            'status_hubungan_keluarga' => $hubungan,
                            'status_perkawinan' => $perkawinan,
                            'file_kk' => null,
                            'file_ktp' => null,
                        ]
                    );
                }

                $pendudukCounter++;
                $kkCounter++;
            }
        }
    }

    /**
     * Membangun NIK 16 digit unik dengan prefix '321702' + nomor urut.
     */
    private function buildNik(int $counter): string
    {
        // '321702' (6 digit) + 10 digit urut = 16 digit.
        return '321702' . str_pad((string) $counter, 10, '0', STR_PAD_LEFT);
    }
}
