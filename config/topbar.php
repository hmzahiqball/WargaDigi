<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Topbar Configurations Per Role
    |--------------------------------------------------------------------------
    |
    | Defines topbar options, notification lists, search placeholders, settings
    | route/URL, and role display names for each role in WargaDigi.
    |
    */

    'Admin Aplikasi' => [
        'search_placeholder' => 'Search settings...',
        'notif_title'        => 'Notifikasi Administrator',
        'notif_badge'        => '3',
        'default_name'       => 'Administrator',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'success',
                'icon'   => 'bi-check-circle-fill',
                'text'   => 'Backup data berhasil diselesaikan',
                'time'   => '5 menit yang lalu',
                'unread' => true,
            ],
            [
                'type'   => 'warning',
                'icon'   => 'bi-exclamation-triangle-fill',
                'text'   => 'Storage mencapai 80% kapasitas',
                'time'   => '1 jam yang lalu',
                'unread' => true,
            ],
            [
                'type'   => 'info',
                'icon'   => 'bi-info-circle-fill',
                'text'   => 'Pengguna baru terdaftar: Ahmad S.',
                'time'   => '2 jam yang lalu',
                'unread' => false,
            ],
        ],
    ],

    'Admin RW' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi RW',
        'notif_badge'        => '3',
        'default_name'       => 'Ketua RW',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'warning',
                'icon'   => 'bi-exclamation-triangle-fill',
                'text'   => '12 dokumen menunggu verifikasi RW',
                'time'   => '10 menit yang lalu',
                'unread' => true,
            ],
            [
                'type'   => 'info',
                'icon'   => 'bi-info-circle-fill',
                'text'   => 'RT 04 telah mengunggah laporan keuangan',
                'time'   => '1 jam yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'Pimpinan RW' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi RW',
        'notif_badge'        => '2',
        'default_name'       => 'Pimpinan RW',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'warning',
                'icon'   => 'bi-exclamation-triangle-fill',
                'text'   => '12 dokumen menunggu verifikasi RW',
                'time'   => '10 menit yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'Ketua RT' => [
        'search_placeholder' => 'Search settings...',
        'notif_title'        => 'Notifikasi RT',
        'notif_badge'        => '2',
        'default_name'       => 'Admin RT',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'warning',
                'icon'   => 'bi-exclamation-triangle-fill',
                'text'   => '24 dokumen menunggu persetujuan RT',
                'time'   => '2 jam yang lalu',
                'unread' => true,
            ],
            [
                'type'   => 'success',
                'icon'   => 'bi-check-circle-fill',
                'text'   => 'Laporan iuran bulanan telah diterima',
                'time'   => '5 jam yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'Op Konten RW' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi Operator Konten',
        'notif_badge'        => '2',
        'default_name'       => 'OP Konten',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'warning',
                'icon'   => 'bi-exclamation-triangle-fill',
                'text'   => '24 konten artikel memerlukan tinjauan',
                'time'   => '1 jam yang lalu',
                'unread' => true,
            ],
            [
                'type'   => 'success',
                'icon'   => 'bi-check-circle-fill',
                'text'   => 'Agenda baru telah disetujui',
                'time'   => '3 jam yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'Op Konten RT' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi Operator Konten',
        'notif_badge'        => '1',
        'default_name'       => 'OP Konten RT',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'warning',
                'icon'   => 'bi-exclamation-triangle-fill',
                'text'   => 'Konten berita baru perlu ditinjau',
                'time'   => '1 jam yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'Op Keuangan RW' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi Operator Keuangan',
        'notif_badge'        => '2',
        'default_name'       => 'OP Keuangan',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'warning',
                'icon'   => 'bi-exclamation-triangle-fill',
                'text'   => 'Laporan keuangan pending verifikasi RT',
                'time'   => '2 jam yang lalu',
                'unread' => true,
            ],
            [
                'type'   => 'success',
                'icon'   => 'bi-check-circle-fill',
                'text'   => 'Pemasukan iuran warga telah dicatat',
                'time'   => '4 jam yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'Op Keuangan RT' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi Operator Keuangan',
        'notif_badge'        => '1',
        'default_name'       => 'OP Keuangan RT',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'warning',
                'icon'   => 'bi-exclamation-triangle-fill',
                'text'   => 'Laporan kas RT perlu diperbarui',
                'time'   => '1 jam yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'DKM' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi Keuangan DKM',
        'notif_badge'        => '1',
        'default_name'       => 'Pengurus DKM',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'info',
                'icon'   => 'bi-info-circle-fill',
                'text'   => 'Donasi kas masjid terbaru telah dicatat',
                'time'   => '30 menit yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'Warga' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi Warga',
        'notif_badge'        => '3',
        'default_name'       => 'Warga',
        'settings_route'     => 'warga.keluarga.index',
        'settings_url'       => '/warga/keluarga',
        'notifications'      => [
            [
                'type'   => 'success',
                'icon'   => 'bi-check-circle-fill',
                'text'   => 'Pengajuan surat keterangan telah disetujui',
                'time'   => '10 menit yang lalu',
                'unread' => true,
            ],
            [
                'type'   => 'info',
                'icon'   => 'bi-info-circle-fill',
                'text'   => 'Jadwal kerja bakti minggu ini telah diperbarui',
                'time'   => '2 jam yang lalu',
                'unread' => true,
            ],
        ],
    ],

    'default' => [
        'search_placeholder' => 'Cari di WargaDigi...',
        'notif_title'        => 'Notifikasi WargaDigi',
        'notif_badge'        => '1',
        'default_name'       => 'Pengguna',
        'settings_url'       => '#',
        'notifications'      => [
            [
                'type'   => 'info',
                'icon'   => 'bi-info-circle-fill',
                'text'   => 'Selamat datang di sistem WargaDigi',
                'time'   => 'Baru saja',
                'unread' => true,
            ],
        ],
    ],
];
