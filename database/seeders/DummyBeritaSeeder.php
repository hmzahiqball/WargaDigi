<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Berita;
use App\Models\User;
use Illuminate\Support\Str;

class DummyBeritaSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel (Hapus data berita lama)
        Berita::query()->delete();

        // Cari user
        $operator = User::where('role', 'like', '%Op Konten%')->first() ?? User::first();
        $reviewer = User::where('role', 'like', '%RW%')->first() ?? User::first();

        $kategori = ['Sosial', 'Infrastruktur', 'Hiburan', 'Kesehatan', 'Keamanan'];
        $status = ['Draft', 'Review', 'Revisi', 'Publish', 'Archive'];

        $beritaList = [];

        // Judul-judul yang bervariasi
        $judulSosial = ['Bantuan Sosial Tiba', 'Kerja Bakti Rutin', 'Penyuluhan Warga', 'Kunjungan Panti Asuhan'];
        $judulInfrastruktur = ['Perbaikan Jalan Aspal', 'Pembangunan Posko', 'Pemasangan Lampu Jalan', 'Normalisasi Selokan'];
        $judulHiburan = ['Lomba 17 Agustus', 'Pentas Seni Warga', 'Nonton Bareng FInal', 'Bazar Murah Akhir Pekan'];
        $judulKesehatan = ['Vaksinasi Massal', 'Pemeriksaan Lansia', 'Senam Pagi Bersama', 'Posyandu Balita'];
        $judulKeamanan = ['Jadwal Ronda Baru', 'Pemasangan CCTV', 'Sosialisasi Anti Curanmor', 'Patroli Malam Gabungan'];

        $semuaJudul = [
            'Sosial' => $judulSosial,
            'Infrastruktur' => $judulInfrastruktur,
            'Hiburan' => $judulHiburan,
            'Kesehatan' => $judulKesehatan,
            'Keamanan' => $judulKeamanan,
        ];

        for ($i = 1; $i <= 20; $i++) {
            $kat = $kategori[array_rand($kategori)];
            $stat = $status[array_rand($status)];
            
            $judulAcak = $semuaJudul[$kat][array_rand($semuaJudul[$kat])] . " " . $i;
            
            $isi = "<p>Dalam upaya meningkatkan kualitas lingkungan kita, kegiatan <strong>$judulAcak</strong> telah sukses diselenggarakan. Kegiatan ini ditujukan khusus bagi seluruh warga yang ingin berpartisipasi aktif dalam memajukan lingkungan.</p>";
            $isi .= "<h5 class=\"fw-bold mt-4 mb-2\">Detail Pelaksanaan</h5>";
            $isi .= "<ul class=\"mb-4\">";
            $isi .= "<li class=\"mb-2\"><strong>Lokasi:</strong> Balai Warga RW, Jl. Merdeka Bersama.</li>";
            $isi .= "<li class=\"mb-2\"><strong>Waktu Pelaksanaan:</strong> Sabtu, " . rand(1, 28) . " Agustus 2023, mulai pukul 08.00 WIB hingga selesai.</li>";
            $isi .= "<li class=\"mb-2\"><strong>Peserta:</strong> Seluruh warga lingkungan RW setempat.</li>";
            $isi .= "</ul>";
            $isi .= "<p>Antusiasme warga sangat luar biasa. Sejak pagi hari, antrean sudah terlihat memanjang namun tetap tertib berkat pengaturan dari panitia lokal dan petugas keamanan lingkungan.</p>";
            $isi .= "<blockquote>Bagi warga yang berhalangan hadir pada acara ini, dapat menghubungi panitia untuk mendapatkan informasi lebih lanjut terkait kegiatan susulan.</blockquote>";
            
            // Random Image (3 out of 4 chances to have an image)
            $thumbnail = (rand(1, 4) > 1) ? "https://picsum.photos/seed/{$i}55/400/300" : null;
            
            $item = [
                'id' => (string) Str::uuid(),
                'judul_berita' => $judulAcak,
                'slug' => Str::slug($judulAcak) . '-' . uniqid(),
                'isi_berita' => $isi,
                'kategori' => $kat,
                'featured_image' => $thumbnail,
                'status' => $stat,
                'operator_id' => $operator->id,
                'created_at' => now()->subDays(rand(1, 30))->subHours(rand(1, 24)),
                'updated_at' => now()->subHours(rand(1, 12)),
                'tanggal_publish' => null,
                'approval_id' => null,
                'catatan_revisi' => null,
            ];

            if ($stat === 'Publish') {
                $item['tanggal_publish'] = now()->subDays(rand(1, 10));
                $item['approval_id'] = $reviewer->id ?? $operator->id;
            } elseif ($stat === 'Revisi') {
                $item['catatan_revisi'] = "Ada beberapa foto yang kurang jelas, tolong perbaiki dan tambahkan sumbernya. Lalu lengkapi bagian penutup berita.";
            }

            $beritaList[] = $item;
        }

        Berita::insert($beritaList);
    }
}
