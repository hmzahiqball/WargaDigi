<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpKontenController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'berita_aktif' => [
                'total' => '142',
                'change' => '+12%',
                'label' => 'Artikel Berita Aktif',
            ],
            'agenda_akan_datang' => [
                'total' => '8',
                'badge' => 'This Week',
                'label' => 'Agenda Akan Datang',
            ],
            'tinjauan_proses' => [
                'total' => '24',
                'badge' => 'Action Needed',
                'label' => 'Tinjauan Sedang Diprosese',
            ],
            'galeri_foto' => [
                'total' => '1,204',
                'label' => 'Galeri Foto Lengkap',
            ],
        ];

        $statusBerita = [
            [
                'title' => 'Program Vaksinasi Booster M',
                'kategori' => 'Kesehatan',
                'status' => 'Published',
                'status_class' => 'bg-success bg-opacity-10 text-success',
                'update' => '2 hrs ago',
            ],
            [
                'title' => 'Jadwal Ronda Malam Siskam',
                'kategori' => 'Keamanan',
                'status' => 'Review',
                'status_class' => 'bg-danger bg-opacity-10 text-danger',
                'update' => '5 hrs ago',
            ],
            [
                'title' => 'Penyaluran Bantuan Sembak',
                'kategori' => 'Sosial',
                'status' => 'Draft',
                'status_class' => 'bg-secondary bg-opacity-10 text-secondary',
                'update' => '1 day ago',
            ],
            [
                'title' => 'Laporan Perbaikan Saluran A',
                'kategori' => 'Infrastruktur',
                'status' => 'Approve',
                'status_class' => 'bg-primary bg-opacity-10 text-primary',
                'update' => '2 days ago',
            ],
        ];

        $upcomingAgenda = [
            [
                'month' => 'OCT',
                'day' => '12',
                'title' => 'Beres-Beres Kampung',
                'desc' => 'Kerja bakti membersihkan...',
                'status' => 'Approved',
                'status_class' => 'bg-success bg-opacity-10 text-success',
            ],
            [
                'month' => 'OCT',
                'day' => '15',
                'title' => 'Gotong Royong Perbaikan Balai',
                'desc' => 'Renovasi atap balai warga yang',
                'status' => 'Pending Approval',
                'status_class' => 'bg-warning bg-opacity-20 text-dark',
            ],
            [
                'month' => 'OCT',
                'day' => '20',
                'title' => 'Posyandu Balita & Lansia',
                'desc' => 'Pemeriksaan kesehatan rutin...',
                'status' => 'Approved',
                'status_class' => 'bg-success bg-opacity-10 text-success',
            ],
        ];

        return view('opKonten.dashboard', compact('stats', 'statusBerita', 'upcomingAgenda'));
    }
}
