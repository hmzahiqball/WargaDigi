<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            ['label' => 'AKTIF HARI INI', 'value' => 1, 'total' => 416, 'icon' => 'bi-check-circle', 'color' => '#43A047'],
            ['label' => 'TOTAL KEPALA KELUARGA', 'value' => 416, 'icon' => 'bi-people-fill', 'color' => '#FF9800'],
            ['label' => 'TOTAL PENDUDUK', 'value' => 1446, 'icon' => 'bi-person-badge-fill', 'color' => '#0288D1'],
        ];

        $alerts = [
            [
                'title' => 'Instans Diaktifkan',
                'desc' => 'RW 21 Tanimulya telah berhasil menyelesaikan proses pembaruan DNS.',
                'time' => '2 jam yang lalu',
                'type' => 'success',
            ],
        ];

        $instances = [
            [
                'name' => 'RW 21 Tanimulya',
                'domain' => 'rw21.wargadigi.id',
                'status' => 'Active',
                'users' => 452,
            ],
        ];

        return view('admin.dashboard', compact('stats', 'alerts', 'instances'));
    }

    public function pengaturanSistem()
    {
        $settings = [
            'instance_name' => 'RW 21 Tanimulya',
            'domain' => 'rw21.wargadigi.id',
            'description' => 'Platform digital untuk warga RW 21 Desa Tanimulya',
            'notif_whatsapp' => true,
            'notif_email' => true,
            'notif_push' => false,
            'two_factor' => true,
            'session_timeout' => 30,
            'maintenance_mode' => false,
        ];

        return view('admin.pengaturan-sistem', compact('settings'));
    }

    public function manajemenHakAkses()
    {
        $users = [
            [
                'name' => 'Budi Santoso',
                'nik' => '1234567898912345 6',
                'role' => 'Ketua RW',
                'role_color' => '#43A047',
                'access' => ['op_keuangan' => true, 'op_konten' => false, 'admin_rw' => false, 'pimpinan_rt' => false, 'pimpinan_rw' => false],
            ],
            [
                'name' => 'Siti Aminah',
                'nik' => '1234567898912345 6',
                'role' => 'Operator Konten',
                'role_color' => '#0288D1',
                'access' => ['op_keuangan' => true, 'op_konten' => false, 'admin_rw' => false, 'pimpinan_rt' => false, 'pimpinan_rw' => false],
            ],
            [
                'name' => 'Agus Supriyadi',
                'nik' => '1234567898912345 6',
                'role' => 'Bendahara RW',
                'role_color' => '#FF9800',
                'access' => ['op_keuangan' => true, 'op_konten' => false, 'admin_rw' => false, 'pimpinan_rt' => false, 'pimpinan_rw' => false],
            ],
        ];

        return view('admin.manajemen-hak-akses', compact('users'));
    }

    public function logAktivitas()
    {
        $logs = [
            [
                'date' => '26 Agu 2026',
                'time' => '14:32:05',
                'name' => 'Budi Waluyo',
                'initials' => 'BW',
                'avatar_bg' => '#bdbdbd',
                'role' => 'RW ADMIN',
                'role_bg' => '#1B5E20',
                'role_color' => '#ffffff',
                'activity_type' => 'Membuat Data Baru',
                'activity_icon' => 'bi-file-earmark-plus',
                'description' => 'Menambahkan data warga baru (ID: W-2026-984) ke sistem.',
                'status' => 'Sukses',
                'status_class' => 'success',
                'category' => 'data',
            ],
            [
                'date' => '26 Agu 2026',
                'time' => '10:15:22',
                'name' => 'Siti Aminah',
                'initials' => 'SA',
                'avatar_bg' => '#bdbdbd',
                'role' => 'OPR KEUANGAN',
                'role_bg' => '#43A047',
                'role_color' => '#ffffff',
                'activity_type' => 'Memasukkan data transaksi',
                'activity_icon' => 'bi-wallet2',
                'description' => 'Mencatat pembayaran iuran bulanan RT 02 (Nominal: Rp 450.000).',
                'status' => 'Sukses',
                'status_class' => 'success',
                'category' => 'transaksi',
            ],
            [
                'date' => '25 Agu 2026',
                'time' => '19:45:10',
                'name' => 'Agus Riyadi',
                'initials' => 'AR',
                'avatar_bg' => '#bdbdbd',
                'role' => 'RT LEADER',
                'role_bg' => '#546E7A',
                'role_color' => '#ffffff',
                'activity_type' => 'Mengubah status persetujuan',
                'activity_icon' => 'bi-check2-square',
                'description' => 'Menyetujui permohonan surat pengantar (Ref: SP-092-23).',
                'status' => 'Sukses',
                'status_class' => 'success',
                'category' => 'persetujuan',
            ],
            [
                'date' => '24 Agu 2026',
                'time' => '16:20:45',
                'name' => 'Dian Kartika',
                'initials' => 'DK',
                'avatar_bg' => '#bdbdbd',
                'role' => 'OPR KONTEN',
                'role_bg' => '#2E7D32',
                'role_color' => '#ffffff',
                'activity_type' => 'Membuat Data Publikasi',
                'activity_icon' => 'bi-newspaper',
                'description' => 'Mempublikasikan artikel berita: "Jadwal Kerja Bakti Minggu Ini".',
                'status' => 'Sukses',
                'status_class' => 'success',
                'category' => 'publikasi',
            ],
            [
                'date' => '24 Agu 2026',
                'time' => '09:05:33',
                'name' => 'Budi Waluyo',
                'initials' => 'BW',
                'avatar_bg' => '#bdbdbd',
                'role' => 'RW ADMIN',
                'role_bg' => '#1B5E20',
                'role_color' => '#ffffff',
                'activity_type' => 'Mengubah Pengaturan',
                'activity_icon' => 'bi-gear',
                'description' => 'Mengaktifkan mode verifikasi 2 langkah untuk semua akun pengurus.',
                'status' => 'Sukses',
                'status_class' => 'success',
                'category' => 'settings',
            ],
        ];

        $totalLogs = 240;

        return view('admin.log-aktivitas', compact('logs', 'totalLogs'));
    }

    public function arsipDataWarga()
    {
        $arsip = [
            [
                'name' => 'Budi Santoso',
                'nik' => '3273012345678901',
                'date' => '12 Oct 2012',
                'reason' => 'Pindah',
                'status' => '> 10 tahun',
                'status_color' => '#78909c',
            ],
            [
                'name' => 'Siti Aminah',
                'nik' => '3273019876543210',
                'date' => '05 Mar 2008',
                'reason' => 'Almarhum',
                'status' => 'Menunggu Penghapusan',
                'status_color' => '#FF9800',
            ],
            [
                'name' => 'Agus Wijaya',
                'nik' => '3273011223344 55',
                'date' => '20 Jan 2020',
                'reason' => 'Cadangkan Sistem',
                'status' => 'Dapat Dipulihkan',
                'status_color' => '#43A047',
            ],
        ];

        $storage = [
            'used' => 1.2,
            'total' => 5.0,
            'breakdown' => [
                ['label' => 'Backup Data', 'size' => '800MB', 'color' => '#43A047'],
                ['label' => 'Catatan Almarhum', 'size' => '400MB', 'color' => '#FF9800'],
            ],
        ];

        return view('admin.arsip-data-warga', compact('arsip', 'storage'));
    }

    public function manajemenData()
    {
        $systemHealth = [
            'status' => 'healthy',
            'last_backup' => 'Hari ini, 08:30 WIB',
        ];

        $backup = [
            'total_size' => '1.2 GB',
            'schedule' => 'Setiap Minggu, 00:00',
        ];

        $archiveData = [
            [
                'category' => 'Warga Meninggal (>10 Tahun)',
                'count' => 42,
                'action' => 'Penghapusan Permanen',
                'action_color' => '#E53935',
                'status' => 'Review Needed',
                'status_color' => '#E53935',
            ],
            [
                'category' => 'Warga Pindah (>5 Tahun)',
                'count' => 128,
                'action' => 'Arsip Dingin',
                'action_color' => '#555',
                'status' => 'Archived',
                'status_color' => '#43A047',
            ],
        ];

        return view('admin.manajemen-data', compact('systemHealth', 'backup', 'archiveData'));
    }
}
