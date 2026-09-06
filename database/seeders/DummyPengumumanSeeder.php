<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyPengumumanSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan operator konten (Op Konten RW)
        $operator = User::where('role', 'Op Konten RW')->first();
        if (!$operator) {
            $operator = User::factory()->create(['role' => 'Op Konten RW']);
        }

        // Temukan ketua RW (Admin RW / Pimpinan RW)
        $approval = User::where('role', 'Admin RW')->first();

        $pengumumans = [
            [
                'judul_pengumuman' => 'Jadwal Kerja Bakti Rutin RT 03',
                'isi_pengumuman' => '<p>Diberitahukan bahwa akan dilaksanakan kegiatan kerja bakti rutin bulanan untuk membersihkan lingkungan sekitar dan memperbaiki fasilitas umum.</p><ul><li>Waktu: Minggu, 12 November 2023</li><li>Pukul: 07:00 WIB - Selesai</li></ul>',
                'is_priority' => false,
                'status' => 'Publish',
            ],
            [
                'judul_pengumuman' => 'Pemadaman Listrik Bergilir (Darurat)',
                'isi_pengumuman' => '<p><strong>INFO PENTING!</strong> PLN akan melakukan pemadaman listrik bergilir di area RW 21 karena ada perbaikan gardu induk.</p><p>Estimasi pemadaman: 13:00 - 16:00 WIB.</p>',
                'is_priority' => true,
                'status' => 'Publish',
            ],
            [
                'judul_pengumuman' => 'Distribusi Bantuan Sosial Bulan Ini',
                'isi_pengumuman' => '<p>Bantuan sosial bulan ini akan dibagikan di Balai RW. Harap membawa fotokopi KK dan KTP.</p>',
                'is_priority' => true,
                'status' => 'Review',
            ],
            [
                'judul_pengumuman' => 'Draft: Lomba 17 Agustus',
                'isi_pengumuman' => '<p>Pengumuman mengenai pendaftaran lomba masih disusun dan menunggu rapat panitia.</p>',
                'is_priority' => false,
                'status' => 'Draft',
            ],
            [
                'judul_pengumuman' => 'Peringatan Keamanan Lingkungan',
                'isi_pengumuman' => '<p>Belakangan ini sering terjadi pencurian motor. Dimohon seluruh warga untuk meningkatkan kewaspadaan dan memastikan kendaraan terkunci ganda saat parkir.</p>',
                'is_priority' => true,
                'status' => 'Revisi',
                'catatan_revisi' => 'Tolong tambahkan himbauan untuk melapor ke pos satpam jika melihat orang mencurigakan.',
            ]
        ];

        // Generate the rest to make it 20
        $statuses = ['Draft', 'Review', 'Revisi', 'Publish'];

        for ($i = 6; $i <= 20; $i++) {
            $status = $statuses[array_rand($statuses)];
            $is_priority = (rand(1, 10) > 8); // 20% chance to be priority

            $pengumumans[] = [
                'judul_pengumuman' => 'Pengumuman Dummy Ke-' . $i,
                'isi_pengumuman' => '<p>Ini adalah isi pengumuman <strong>ke-' . $i . '</strong> yang digenerate secara otomatis untuk keperluan testing pagination dan fungsi lainnya.</p>',
                'is_priority' => $is_priority,
                'status' => $status,
                'catatan_revisi' => ($status === 'Revisi') ? 'Harap lengkapi detail informasi pada paragraf kedua.' : null,
            ];
        }

        // Insert into database
        $baseDate = Carbon::now()->subDays(30);

        foreach ($pengumumans as $index => $item) {
            $created_at = (clone $baseDate)->addDays($index);
            
            $data = [
                'judul_pengumuman' => $item['judul_pengumuman'],
                'isi_pengumuman' => $item['isi_pengumuman'],
                'is_priority' => $item['is_priority'],
                'status' => $item['status'],
                'operator_id' => $operator->id,
                'created_at' => $created_at,
                'updated_at' => $created_at,
            ];

            if ($item['status'] === 'Publish') {
                $data['tanggal_publish'] = $created_at;
                $data['approval_id'] = $approval ? $approval->id : null;
            } elseif ($item['status'] === 'Revisi') {
                $data['catatan_revisi'] = $item['catatan_revisi'] ?? 'Revisi ulang pengumuman ini.';
                $data['approval_id'] = $approval ? $approval->id : null;
            }

            Pengumuman::create($data);
        }
    }
}
