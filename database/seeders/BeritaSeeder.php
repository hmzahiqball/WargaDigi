<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('role', 'Op Konten RW')->first() ?? User::where('username', 'opkontenrw21')->first() ?? User::first();
        $approver = User::where('role', 'Admin RW')->first() ?? User::where('role', 'Pimpinan RW')->first() ?? User::first();

        $operatorId = $operator ? $operator->id : null;
        $approverId = $approver ? $approver->id : null;

        $beritaList = [
            [
                'id' => 'd1e2f3a4-0001-4000-8000-000000000001',
                'judul_berita' => 'Gotong Royong Kebersihan Lingkungan RW 21 Tanimulya Menjelang Musim Hujan',
                'slug' => 'gotong-royong-kebersihan-lingkungan-rw-21-tanimulya-menjelang-musim-hujan',
                'kategori' => 'Kegiatan',
                'isi_berita' => 'Dalam rangka mengantisipasi datangnya musim hujan dan menjaga kesehatan lingkungan pemukiman, Pengurus RW 21 Tanimulya bersama seluruh perwakilan RT 01 hingga RT 06 mengadakan kegiatan kerja bakti massal. Warga secara antusias membersihkan saluran drainase, memotong ranting pohon yang rawan tumbang, serta mengangkut sampah residu ke TPS terpadu. Ketua RW menyampaikan apresiasi setinggi-tingginya atas kekompakan warga.',
                'featured_image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&auto=format&fit=crop',
                'status' => 'Publish',
                'operator_id' => $operatorId,
                'approval_id' => $approverId,
                'tanggal_publish' => now()->subDays(3),
            ],
            [
                'id' => 'd1e2f3a4-0001-4000-8000-000000000002',
                'judul_berita' => 'Bazar UMKM & Pameran Produk Kreatif Warga RW 21 Siap Digelar Pekan Depan',
                'slug' => 'bazar-umkm-pameran-produk-kreatif-warga-rw-21-siap-digelar-pekan-depan',
                'kategori' => 'Ekonomi',
                'isi_berita' => 'Untuk mendukung perputaran ekonomi lokal, RW 21 Tanimulya bekerja sama dengan pelaku usaha mikro akan menyelenggarakan "Bazar UMKM WargaDigi 21". Acara ini menampilkan aneka kuliner tradisional, kerajinan tangan kayu jati, busana muslimah, serta stan pelayanan kesehatan gratis. Diharapkan seluruh warga dapat hadir untuk meramaikan dan melarisi produk tetangga sendiri.',
                'featured_image' => 'https://images.unsplash.com/photo-1533900298318-6b8da08a523e?w=800&auto=format&fit=crop',
                'status' => 'Publish',
                'operator_id' => $operatorId,
                'approval_id' => $approverId,
                'tanggal_publish' => now()->subDays(1),
            ],
            [
                'id' => 'd1e2f3a4-0001-4000-8000-000000000003',
                'judul_berita' => 'Sosialisasi Program Bank Sampah dan Pemilahan Sampah Organik Rumah Tangga',
                'slug' => 'sosialisasi-program-bank-sampah-dan-pemilahan-sampah-organik-rumah-tangga',
                'kategori' => 'Lingkungan',
                'isi_berita' => 'Pengurus lingkungan RW 21 meresmikan inisiatif Bank Sampah Mandiri. Melalui program ini, sampah bernilai ekonomis seperti botol plastik, kardus, dan minyak jelantah dapat ditukarkan menjadi saldo kas atau sembako murah. Edukasi pemilahan sampah organik untuk kompos juga mulai diperkenalkan di tingkat RT.',
                'featured_image' => 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=800&auto=format&fit=crop',
                'status' => 'Publish',
                'operator_id' => $operatorId,
                'approval_id' => $approverId,
                'tanggal_publish' => now()->subHours(12),
            ],
            [
                'id' => 'd1e2f3a4-0001-4000-8000-000000000004',
                'judul_berita' => 'Penyaluran Bantuan Sosial & Program Santunan Anak Yatim RW 21',
                'slug' => 'penyaluran-bantuan-sosial-program-santunan-anak-yatim-rw-21',
                'kategori' => 'Sosial',
                'isi_berita' => 'DKM Masjid RW 21 berkolaborasi dengan seksi sosial kemasyarakatan berhasil menyalurkan paket sembako dan santunan pendidikan kepada puluhan keluarga prasejahtera dan anak yatim di lingkungan RW 21. Dana kegiatan dihimpun secara transparan melalui donasi warga dan kas masjid.',
                'featured_image' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&auto=format&fit=crop',
                'status' => 'Publish',
                'operator_id' => $operatorId,
                'approval_id' => $approverId,
                'tanggal_publish' => now()->subDays(7),
            ],
            [
                'id' => 'd1e2f3a4-0001-4000-8000-000000000005',
                'judul_berita' => 'Rancangan Anggaran Pembangunan Pos Keamanan Terpadu RT 04',
                'slug' => 'rancangan-anggaran-pembangunan-pos-keamanan-terpadu-rt-04',
                'kategori' => 'Pengumuman',
                'isi_berita' => 'Berikut adalah draf rancangan biaya dan spesifikasi teknis renovasi pos kamling RT 04 yang sedang dalam tahap peninjauan oleh Pimpinan RW sebelum dimulai pengerjaan fisiknya pada bulan depan.',
                'featured_image' => null,
                'status' => 'Review',
                'operator_id' => $operatorId,
                'approval_id' => null,
                'tanggal_publish' => null,
            ],
        ];

        foreach ($beritaList as $berita) {
            if (!empty($berita['operator_id'])) {
                Berita::updateOrCreate(
                    ['slug' => $berita['slug']],
                    $berita
                );
            }
        }
    }
}
