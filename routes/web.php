<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\TransparansiController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Warga\DashboardController;
use App\Http\Controllers\Warga\KeluargaController;
use App\Http\Controllers\Warga\SuratController;
use App\Http\Controllers\Warga\UmkmController as WargaUmkmController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RwController;
use App\Http\Controllers\RtController;
use App\Http\Controllers\OpKontenController;
use App\Http\Controllers\OpKeuanganController;
use App\Http\Controllers\OpKonten\AgendaController as OpKontenAgendaController;
use App\Http\Controllers\OpKonten\GaleriController as OpKontenGaleriController;
use App\Http\Controllers\OpKonten\BeritaController as OpKontenBeritaController;
use App\Http\Controllers\OpKonten\PengumumanController as OpKontenPengumumanController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
Route::get('/transparansi', [TransparansiController::class, 'index'])->name('transparansi');
Route::get('/transparansi/{id}/pdf', [TransparansiController::class, 'downloadPdf'])->name('transparansi.download-pdf');
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
Route::get('/berita/{id}', [BeritaController::class, 'show'])->name('berita.show');
Route::get('/pojok-umkm', [UmkmController::class, 'index'])->name('pojok-umkm');
Route::get('/layanan-mandiri', [LayananController::class, 'index'])->name('layanan-mandiri');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::get('/aktivasi', [RegisterController::class, 'aktivasi'])->name('aktivasi');

// Admin Routes (Role: Admin Aplikasi)
Route::middleware(['auth', 'role:Admin Aplikasi'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/pengaturan-sistem', [AdminController::class, 'pengaturanSistem'])->name('admin.pengaturan-sistem');
    Route::get('/manajemen-hak-akses', [AdminController::class, 'manajemenHakAkses'])->name('admin.manajemen-hak-akses');
    Route::get('/log-aktivitas', [AdminController::class, 'logAktivitas'])->name('admin.log-aktivitas');
    Route::get('/arsip-data-warga', [AdminController::class, 'arsipDataWarga'])->name('admin.arsip-data-warga');
    Route::get('/manajemen-data', [AdminController::class, 'manajemenData'])->name('admin.manajemen-data');
});

// RW Routes (Role: Admin RW, Pimpinan RW)
Route::middleware(['auth', 'role:Admin RW,Pimpinan RW'])->prefix('rw')->group(function () {
    Route::get('/dashboard', [RwController::class, 'dashboard'])->name('rw.dashboard');
    Route::get('/', [RwController::class, 'dashboard']);
});

// RT Routes (Role: Ketua RT)
Route::middleware(['auth', 'role:Ketua RT'])->prefix('rt')->group(function () {
    Route::get('/dashboard', [RtController::class, 'dashboard'])->name('rt.dashboard');
    Route::get('/', [RtController::class, 'dashboard']);
});

// Operator Konten Routes (Role: Op Konten RW, Op Konten RT)
Route::middleware(['auth', 'role:Op Konten RW,Op Konten RT'])->group(function () {
    Route::prefix('op-konten')->name('opkonten.')->group(function () {
        Route::get('/dashboard', [OpKontenController::class, 'dashboard'])->name('dashboard');
        Route::get('/berita', [OpKontenBeritaController::class, 'index'])->name('berita.index');
        Route::get('/agenda', [OpKontenAgendaController::class, 'index'])->name('agenda.index');
        Route::get('/galeri', [OpKontenGaleriController::class, 'index'])->name('galeri.index');
        Route::get('/pengumuman', [OpKontenPengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/', [OpKontenController::class, 'dashboard']);
    });
    Route::get('/opkonten/dashboard', [OpKontenController::class, 'dashboard']);
});

// Operator Keuangan Routes (Role: Op. Keuangan RW, Op. Keuangan RT, DKM)
Route::middleware(['auth', 'role:Op Keuangan RW,Op Keuangan RT,DKM'])->group(function () {
    Route::prefix('op-keuangan')->group(function () {
        Route::get('/dashboard', [OpKeuanganController::class, 'dashboard'])->name('opkeuangan.dashboard');
        Route::get('/', [OpKeuanganController::class, 'dashboard']);
    });
    Route::get('/opkeuangan/dashboard', [OpKeuanganController::class, 'dashboard']);
});

// Warga Routes (Role: Warga)
Route::middleware(['auth', 'role:Warga'])->prefix('warga')->name('warga.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/keluarga', [KeluargaController::class, 'index'])->name('keluarga.index');
    Route::get('/keluarga/edit', [KeluargaController::class, 'edit'])->name('keluarga.edit');
    Route::put('/keluarga/update', [KeluargaController::class, 'update'])->name('keluarga.update');
    Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/create', [SuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [SuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/{id}/pdf', [SuratController::class, 'downloadPdf'])->name('surat.download-pdf');
    Route::get('/umkm/galeri', [WargaUmkmController::class, 'indexGaleri'])->name('umkm.galeri');
    Route::get('/umkm/kelola', [WargaUmkmController::class, 'kelolaProduk'])->name('umkm.kelola');
    Route::get('/umkm/daftar', [WargaUmkmController::class, 'createUsaha'])->name('umkm.daftar');
    Route::post('/umkm/daftar', [WargaUmkmController::class, 'storeUsaha'])->name('umkm.store-usaha');
    Route::get('/umkm/produk', [WargaUmkmController::class, 'kelolaProduk'])->name('umkm.produk.index');
    Route::get('/umkm/produk/create', [WargaUmkmController::class, 'createProduk'])->name('umkm.produk.create');
    Route::post('/umkm/produk', [WargaUmkmController::class, 'storeProduk'])->name('umkm.produk.store');
    Route::get('/umkm/produk/{id}/edit', [WargaUmkmController::class, 'editProduk'])->name('umkm.produk.edit');
    Route::put('/umkm/produk/{id}', [WargaUmkmController::class, 'updateProduk'])->name('umkm.produk.update');
    Route::delete('/umkm/produk/{id}', [WargaUmkmController::class, 'destroyProduk'])->name('umkm.produk.destroy');
    Route::get('/umkm/produk/{id}', [WargaUmkmController::class, 'showProduk'])->name('umkm.produk.show');
});