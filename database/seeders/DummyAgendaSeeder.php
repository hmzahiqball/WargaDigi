<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Agenda;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyAgendaSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel (Hapus data agenda lama)
        Agenda::query()->delete();

        // Cari user
        $operator = User::where('role', 'like', '%Op Konten%')->first() ?? User::first();
        $reviewer = User::where('role', 'like', '%RW%')->first() ?? User::first();

        $kategori = ['Sosial', 'Infrastruktur', 'Hiburan', 'Kesehatan', 'Keamanan', 'Organisasi'];
        $status = ['Draft', 'Review', 'Revisi', 'Publish'];

        $agendaList = [];

        // Judul-judul yang bervariasi
        $judulSosial = ['Bantuan Sosial Tiba', 'Kerja Bakti Rutin', 'Penyuluhan Warga', 'Kunjungan Panti Asuhan'];
        $judulInfrastruktur = ['Perbaikan Jalan Aspal', 'Pembangunan Posko', 'Pemasangan Lampu Jalan', 'Normalisasi Selokan'];
        $judulHiburan = ['Lomba 17 Agustus', 'Pentas Seni Warga', 'Nonton Bareng Final', 'Bazar Murah Akhir Pekan'];
        $judulKesehatan = ['Vaksinasi Massal', 'Pemeriksaan Lansia', 'Senam Pagi Bersama', 'Posyandu Balita'];
        $judulKeamanan = ['Jadwal Ronda Baru', 'Pemasangan CCTV', 'Sosialisasi Anti Curanmor', 'Patroli Malam Gabungan'];
        $judulOrganisasi = ['Rapat RT/RW Bulanan', 'Pemilihan Ketua Pemuda', 'Evaluasi Kinerja Pengurus', 'Musyawarah Warga'];

        $semuaJudul = [
            'Sosial' => $judulSosial,
            'Infrastruktur' => $judulInfrastruktur,
            'Hiburan' => $judulHiburan,
            'Kesehatan' => $judulKesehatan,
            'Keamanan' => $judulKeamanan,
            'Organisasi' => $judulOrganisasi,
        ];

        for ($i = 1; $i <= 20; $i++) {
            $kat = $kategori[array_rand($kategori)];
            $stat = $status[array_rand($status)];
            
            $judulAcak = $semuaJudul[$kat][array_rand($semuaJudul[$kat])] . " " . $i;
            
            $isi = "Agenda <strong>$judulAcak</strong> akan diselenggarakan dalam waktu dekat. Diharapkan seluruh warga dapat berpartisipasi karena kegiatan ini sangat penting untuk kelancaran lingkungan kita.\n\nDemikian agenda ini kami sampaikan agar menjadi perhatian seluruh warga.";
            
            // Random Image (3 out of 4 chances to have an image)
            $thumbnail = (rand(1, 4) > 1) ? "https://picsum.photos/seed/{$i}66/400/300" : null;

            // Waktu pelaksanaan random (beberapa hari ke depan atau ke belakang)
            $tanggalMulai = Carbon::now()->addDays(rand(-5, 15))->setHour(rand(7, 19))->setMinute(0)->setSecond(0);
            $tanggalSelesai = (clone $tanggalMulai)->addHours(rand(2, 5));
            
            $item = [
                'id' => (string) Str::uuid(),
                'judul_agenda' => $judulAcak,
                'kategori' => $kat,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'lokasi' => 'Balai Pertemuan RW 01',
                'detail_pengumuman' => $isi,
                'banner_flyer' => $thumbnail,
                'status' => $stat,
                'operator_id' => $operator->id,
                'created_at' => now()->subDays(rand(1, 30))->subHours(rand(1, 24)),
                'updated_at' => now()->subHours(rand(1, 12)),
                'approval_id' => null,
                'catatan_revisi' => null,
            ];

            if ($stat === 'Publish') {
                $item['approval_id'] = $reviewer->id ?? $operator->id;
            } elseif ($stat === 'Revisi') {
                $item['catatan_revisi'] = "Tolong tambahkan rundown acara secara lebih mendetail pada deskripsi agenda. Dan pastikan lokasi acara sudah di-booking.";
            }

            $agendaList[] = $item;
        }

        Agenda::insert($agendaList);
    }
}
