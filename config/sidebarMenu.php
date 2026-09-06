<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sidebar Menu Configurations
    |--------------------------------------------------------------------------
    |
    | Dynamic menu configuration for WargaDigi layout sidebar.
    | Menu items will be rendered dynamically based on user role.
    |
    */

    // --- ADMIN APLIKASI ---
    [
        'title' => 'Dashboard',
        'route' => 'admin.dashboard',
        'url'   => '/admin/dashboard',
        'icon'  => 'bi-grid-1x2-fill',
        'roles' => ['Admin Aplikasi'],
        'active' => ['admin.dashboard', 'admin/dashboard'],
    ],
    [
        'title' => 'Pengaturan Sistem',
        'route' => 'admin.pengaturan-sistem',
        'url'   => '/admin/pengaturan-sistem',
        'icon'  => 'bi-gear-fill',
        'roles' => ['Admin Aplikasi'],
        'active' => ['admin.pengaturan-sistem', 'admin/pengaturan-sistem*'],
    ],
    [
        'title' => 'Manajemen Hak Akses',
        'route' => 'admin.manajemen-hak-akses',
        'url'   => '/admin/manajemen-hak-akses',
        'icon'  => 'bi-people-fill',
        'roles' => ['Admin Aplikasi'],
        'active' => ['admin.manajemen-hak-akses', 'admin/manajemen-hak-akses*'],
    ],
    [
        'title' => 'Log Aktivitas',
        'route' => 'admin.log-aktivitas',
        'url'   => '/admin/log-aktivitas',
        'icon'  => 'bi-list-task',
        'roles' => ['Admin Aplikasi'],
        'active' => ['admin.log-aktivitas', 'admin/log-aktivitas*'],
    ],
    [
        'title' => 'Arsip Data Warga',
        'route' => 'admin.arsip-data-warga',
        'url'   => '/admin/arsip-data-warga',
        'icon'  => 'bi-archive-fill',
        'roles' => ['Admin Aplikasi'],
        'active' => ['admin.arsip-data-warga', 'admin/arsip-data-warga*'],
    ],
    [
        'title' => 'Manajemen Data',
        'route' => 'admin.manajemen-data',
        'url'   => '/admin/manajemen-data',
        'icon'  => 'bi-database-fill-gear',
        'roles' => ['Admin Aplikasi'],
        'active' => ['admin.manajemen-data', 'admin/manajemen-data*'],
    ],

    // --- ADMIN RW / PIMPINAN RW ---
    [
        'title' => 'Dashboard',
        'route' => 'rw.dashboard',
        'url'   => '/rw/dashboard',
        'icon'  => 'bi-grid-1x2-fill',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw.dashboard', 'rw', 'rw/dashboard'],
    ],
    [
        'title' => 'Manajemen Penduduk',
        'url'   => '#',
        'icon'  => 'bi-people-fill',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw/penduduk*'],
    ],
    [
        'title' => 'Manajemen RT',
        'url'   => '#',
        'icon'  => 'bi-building',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw/rt*'],
    ],
    [
        'title' => 'Persetujuan Dokumen',
        'url'   => '#',
        'icon'  => 'bi-file-earmark-text-fill',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw/dokumen*'],
    ],
    [
        'title' => 'Persetujuan Keuangan',
        'url'   => '#',
        'icon'  => 'bi-cash-stack',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw/keuangan*'],
    ],
    [
        'title' => 'Laporan Keuangan',
        'url'   => '#',
        'icon'  => 'bi-wallet2',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw/laporan-keuangan*'],
    ],
    [
        'title' => 'UMKM',
        'url'   => '#',
        'icon'  => 'bi-shop',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw/umkm*'],
    ],
    [
        'title' => 'Pusat Informasi',
        'route' => 'rw.pusat-informasi.index',
        'url'   => '/rw/pusat-informasi',
        'icon'  => 'bi-chat-dots-fill',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw/pusat-informasi*'],
    ],
    [
        'title' => 'Koperasi',
        'url'   => '#',
        'icon'  => 'bi-storefront',
        'roles' => ['Admin RW', 'Pimpinan RW'],
        'active' => ['rw/koperasi*'],
    ],

    // --- KETUA RT ---
    [
        'title' => 'Dashboard',
        'route' => 'rt.dashboard',
        'url'   => '/rt/dashboard',
        'icon'  => 'bi-grid-1x2-fill',
        'roles' => ['Ketua RT'],
        'active' => ['rt.dashboard', 'rt', 'rt/dashboard'],
    ],
    [
        'title' => 'Persetujuan Dokumen',
        'url'   => '#',
        'icon'  => 'bi-file-earmark-text-fill',
        'roles' => ['Ketua RT'],
        'active' => ['rt/dokumen*'],
    ],
    [
        'title' => 'Persetujuan Keuangan',
        'url'   => '#',
        'icon'  => 'bi-cash-stack',
        'roles' => ['Ketua RT'],
        'active' => ['rt/keuangan*'],
    ],
    [
        'title' => 'Keuangan Kas RT',
        'url'   => '#',
        'icon'  => 'bi-wallet2',
        'roles' => ['Ketua RT'],
        'active' => ['rt/kas*'],
    ],
    [
        'title' => 'Informasi Keluarga',
        'url'   => '#',
        'icon'  => 'bi-people-fill',
        'roles' => ['Ketua RT'],
        'active' => ['rt/keluarga*'],
    ],

    // --- OPERATOR KONTEN ---
    [
        'title' => 'Dashboard',
        'route' => 'opkonten.dashboard',
        'url'   => '/op-konten/dashboard',
        'icon'  => 'bi-grid-1x2-fill',
        'roles' => ['Op Konten RW', 'Op Konten RT'],
        'active' => ['opkonten.dashboard', 'op-konten/dashboard'],
    ],
    [
        'title' => 'Berita',
        'route' => 'opkonten.berita.index',
        'url'   => '/op-konten/berita',
        'icon'  => 'bi-newspaper',
        'roles' => ['Op Konten RW', 'Op Konten RT'],
        'active' => ['opkonten.berita*', 'op-konten/berita*'],
    ],
    [
        'title' => 'Agenda',
        'route' => 'opkonten.agenda.index',
        'url'   => '/op-konten/agenda',
        'icon'  => 'bi-calendar-event',
        'roles' => ['Op Konten RW', 'Op Konten RT'],
        'active' => ['opkonten.agenda*', 'op-konten/agenda*'],
    ],
    [
        'title' => 'Pengumuman',
        'route' => 'opkonten.pengumuman.index',
        'url'   => '/op-konten/pengumuman',
        'icon'  => 'bi-megaphone',
        'roles' => ['Op Konten RW', 'Op Konten RT'],
        'active' => ['opkonten.pengumuman*', 'op-konten/pengumuman*'],
    ],
    [
        'title' => 'Galeri Dokumentasi',
        'route' => 'opkonten.galeri.index',
        'url'   => '/op-konten/galeri',
        'icon'  => 'bi-images',
        'roles' => ['Op Konten RW', 'Op Konten RT'],
        'active' => ['opkonten.galeri*', 'op-konten/galeri*'],
    ],

    // --- OPERATOR KEUANGAN ---
    [
        'title' => 'Dashboard',
        'route' => 'opkeuangan.dashboard',
        'url'   => '/op-keuangan/dashboard',
        'icon'  => 'bi-grid-1x2-fill',
        'roles' => ['Op Keuangan RW', 'Op Keuangan RT', 'DKM'],
        'active' => ['opkeuangan.dashboard', 'op-keuangan*', 'opkeuangan*'],
    ],
    [
        'title' => 'Catat Transaksi',
        'url'   => '#',
        'icon'  => 'bi-receipt',
        'roles' => ['Op Keuangan RW', 'Op Keuangan RT', 'DKM'],
        'active' => ['opkeuangan/transaksi*'],
    ],
    [
        'title' => 'Laporan Keuangan',
        'url'   => '#',
        'icon'  => 'bi-clipboard-data',
        'roles' => ['Op Keuangan RW', 'Op Keuangan RT', 'DKM'],
        'active' => ['opkeuangan/laporan*'],
    ],

    // --- WARGA ---
    [
        'title' => 'Dashboard',
        'route' => 'warga.dashboard',
        'url'   => '/warga/dashboard',
        'icon'  => 'bi-grid-1x2-fill',
        'roles' => ['Warga'],
        'active' => ['warga.dashboard'],
    ],
    [
        'title' => 'Berita',
        'route' => 'berita',
        'url'   => '/berita',
        'icon'  => 'bi-newspaper',
        'roles' => ['Warga'],
        'active' => ['berita*'],
    ],
    [
        'title' => 'Permohonan Surat',
        'route' => 'warga.surat.index',
        'url'   => '/warga/surat',
        'icon'  => 'bi-file-earmark-text-fill',
        'roles' => ['Warga'],
        'active' => ['warga.surat.*'],
    ],
    [
        'title' => 'Galeri UMKM',
        'route' => 'warga.umkm.produk.index',
        'url'   => '/warga/umkm/produk',
        'icon'  => 'bi-shop',
        'roles' => ['Warga'],
        'active' => ['warga.umkm.*'],
    ],
    [
        'title' => 'Agenda Warga',
        'route' => 'informasi',
        'url'   => '/informasi',
        'icon'  => 'bi-calendar-event-fill',
        'roles' => ['Warga'],
        'active' => ['informasi*'],
    ],
    [
        'title' => 'Pengaturan Keluarga',
        'route' => 'warga.keluarga.index',
        'url'   => '/warga/keluarga',
        'icon'  => 'bi-people-fill',
        'roles' => ['Warga'],
        'active' => ['warga.keluarga.*'],
    ],
    [
        'title' => 'Laporan Keuangan',
        'route' => 'transparansi',
        'url'   => '/transparansi',
        'icon'  => 'bi-wallet2',
        'roles' => ['Warga'],
        'active' => ['transparansi*'],
    ],

    // --- BOTTOM MENU ITEMS ---
    [
        'title'    => 'Pengaturan',
        'url'      => '#',
        'icon'     => 'bi-gear',
        'roles'    => ['Ketua RT'],
        'position' => 'bottom',
        'active'   => ['rt/pengaturan*'],
    ],
    [
        'title'    => 'Pusat Bantuan',
        'url'      => '#',
        'icon'     => 'bi-question-circle',
        'roles'    => ['Admin RW', 'Pimpinan RW', 'Warga'],
        'position' => 'bottom',
    ],
    [
        'title'    => 'Bantuan',
        'url'      => '#',
        'icon'     => 'bi-question-circle',
        'roles'    => ['Admin Aplikasi', 'Op Konten RW', 'Op Konten RT', 'Op Keuangan RW', 'Op Keuangan RT', 'DKM'],
        'position' => 'bottom',
    ],
    [
        'title'     => 'Keluar',
        'url'       => '#',
        'icon'      => 'bi-box-arrow-left',
        'roles'     => ['Admin Aplikasi', 'Admin RW', 'Pimpinan RW', 'Ketua RT', 'Op Konten RW', 'Op Konten RT', 'Op Keuangan RW', 'Op Keuangan RT', 'DKM', 'Warga'],
        'position'  => 'bottom',
        'is_logout' => true,
    ],
];