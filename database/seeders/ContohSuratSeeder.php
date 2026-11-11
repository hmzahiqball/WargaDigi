<?php

namespace Database\Seeders;

use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Services\SuratDataBuilder;
use Illuminate\Database\Seeder;

/**
 * Membuat satu contoh pengajuan surat berstatus "Selesai" untuk setiap jenis
 * surat, atas nama Lukmannul Hakim Firdaus, agar hasil PDF-nya bisa langsung
 * diunduh dan dilihat dari akun warga tersebut.
 *
 * Jalankan: php artisan db:seed --class=ContohSuratSeeder
 */
class ContohSuratSeeder extends Seeder
{
    public function run(): void
    {
        $penduduk = Penduduk::with('keluarga')
            ->where('nama_lengkap', 'Lukmannul Hakim Firdaus')
            ->first();

        if (! $penduduk) {
            $this->command?->warn('Penduduk "Lukmannul Hakim Firdaus" tidak ditemukan. Seeder dilewati.');

            return;
        }

        // Contoh isian tambahan per jenis (sekadar data demo untuk pratinjau).
        $contohTambahan = [
            'Surat Keterangan Ahli Waris' => [
                'nama_pewaris' => 'H. Abdullah Firdaus',
                'tanggal_wafat' => '2026-03-14',
                'daftar_ahli_waris' => "Lukmannul Hakim Firdaus (Anak)\nSiti Aminah (Anak)\nRahmawati (Anak)",
            ],
            'Surat Kelahiran' => [
                'nama_bayi' => 'Muhammad Al Fatih Firdaus',
                'jenis_kelamin_bayi' => 'Laki-laki',
                'tanggal_lahir_bayi' => '2026-09-01',
                'jam_lahir_bayi' => '04:30',
                'tempat_lahir_bayi' => 'RSUD Cibabat, Cimahi',
            ],
            'Surat Kematian' => [
                'nama_almarhum' => 'Hj. Mariam Firdaus',
                'tanggal_meninggal' => '2026-08-20',
                'jam_meninggal' => '21:15',
                'tempat_meninggal' => 'Rumah, Komplek Puri Cipageran Indah II',
                'tempat_dimakamkan' => 'TPU Cimahi',
                'penyebab' => 'Sakit',
            ],
            'Surat Keterangan Domisili' => [
                'lama_tinggal' => '12 tahun',
            ],
            'Surat Keterangan Miskin / Tidak Mampu' => [
                'penghasilan' => 'Rp 1.200.000',
            ],
            'Kartu Keluarga' => [
                'alasan' => 'Perubahan data',
            ],
            'Kartu Tanda Penduduk' => [
                'alasan' => 'Pembuatan baru',
            ],
        ];

        $punyaKolomTambahan = \Illuminate\Support\Facades\Schema::hasColumn('pengajuan_surat', 'data_tambahan');

        // Hapus contoh lama agar tidak menumpuk saat seeder dijalankan ulang.
        PengajuanSurat::where('penduduk_id', $penduduk->id)
            ->where('catatan_rw', 'Contoh surat (seeder) untuk pratinjau.')
            ->delete();

        $urut = 100;
        foreach (PengajuanSurat::TIPE_SURAT as $tipe) {
            $atribut = [
                'penduduk_id' => $penduduk->id,
                'tipe_surat' => $tipe,
                'nomor_urut' => (string) $urut,
                'keterangan_tambahan' => 'keperluan administrasi',
                'status' => 'Selesai',
                'tanggal_disetujui_rt' => now()->subDay(),
                'tanggal_selesai' => now(),
                'catatan_rt' => 'Contoh surat (seeder) untuk pratinjau.',
                'catatan_rw' => 'Contoh surat (seeder) untuk pratinjau.',
            ];

            if ($punyaKolomTambahan && isset($contohTambahan[$tipe])) {
                $atribut['data_tambahan'] = $contohTambahan[$tipe];
            }

            $surat = PengajuanSurat::create($atribut);

            // Simpan snapshot agar PDF konsisten (seperti alur pengesahan RW).
            SuratDataBuilder::simpanSnapshot($surat);

            $urut++;
        }

        $this->command?->info('Berhasil membuat ' . count(PengajuanSurat::TIPE_SURAT) . ' contoh surat untuk Lukmannul Hakim Firdaus.');
    }
}
