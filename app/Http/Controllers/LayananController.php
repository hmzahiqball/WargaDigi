<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        $services = [
            [
                'icon' => 'bi-house-door',
                'title' => 'Pengantar Domisili',
                'desc' => 'Untuk keperluan perbankan, administrasi kependudukan, atau domisili usaha.',
            ],
            [
                'icon' => 'bi-person-vcard',
                'title' => 'Pengantar KTP/KK',
                'desc' => 'Pembuatan baru, perpanjangan, atau perubahan data KTP dan Kartu Keluarga.',
            ],
            [
                'icon' => 'bi-shield-check',
                'title' => 'Pengantar SKCK',
                'desc' => 'Surat keterangan catatan kepolisian untuk melamar kerja atau keperluan lain.',
            ],
            [
                'icon' => 'bi-people',
                'title' => 'Izin Keramaian',
                'desc' => 'Pemberitahuan acara pernikahan, khitanan, atau kegiatan yang mengundang massa.',
            ],
        ];

        return view('pages.layanan-mandiri', compact('services'));
    }
}
