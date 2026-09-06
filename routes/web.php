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
use App\Http\Controllers\Warga\GaleriUmkmController;
use App\Http\Controllers\Warga\KelolaUmkmController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RwController;
use App\Http\Controllers\RtController;
use App\Http\Controllers\OpKontenController;
use App\Http\Controllers\OpKeuanganController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
Route::get('/transparansi', [TransparansiController::class, 'index'])->name('transparansi');
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
Route::get('/pojok-umkm', [UmkmController::class, 'index'])->name('pojok-umkm');
Route::get('/pojok-umkm/usaha/{id?}', [UmkmController::class, 'detailUsaha'])->name('pojok-umkm.detail_usaha');
Route::get('/usaha/{id?}', [UmkmController::class, 'detailUsaha'])->name('public.umkm.usaha.show');
Route::get('/produk/{id}', [UmkmController::class, 'detailProduk'])->name('produk.show');
Route::get('/pojok-umkm/produk/{id}', [UmkmController::class, 'detailProduk'])->name('public.umkm.produk.show');
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
Route::middleware(['auth', 'role:Admin RW,Pimpinan RW'])->prefix('rw')->name('rw.')->group(function () {
    Route::get('/dashboard', [RwController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [RwController::class, 'dashboard']);
    Route::get('/umkm', [RwController::class, 'umkm'])->name('umkm.index');
    Route::post('/umkm/{id}/approve', [RwController::class, 'approveUmkm'])->name('umkm.approve');
    Route::post('/umkm/{id}/reject', [RwController::class, 'rejectUmkm'])->name('umkm.reject');
});

// RT Routes (Role: Ketua RT)
Route::middleware(['auth', 'role:Ketua RT'])->prefix('rt')->group(function () {
    Route::get('/dashboard', [RtController::class, 'dashboard'])->name('rt.dashboard');
    Route::get('/', [RtController::class, 'dashboard']);
});

// Operator Konten Routes (Role: Op. Konten RW, Op. Konten RT)
Route::middleware(['auth', 'role:Op. Konten RW,Op. Konten RT'])->group(function () {
    Route::prefix('op-konten')->group(function () {
        Route::get('/dashboard', [OpKontenController::class, 'dashboard'])->name('opkonten.dashboard');
        Route::get('/', [OpKontenController::class, 'dashboard']);
    });
    Route::get('/opkonten/dashboard', [OpKontenController::class, 'dashboard']);
});

// Operator Keuangan Routes (Role: Op. Keuangan RW, Op. Keuangan RT, DKM)
Route::middleware(['auth', 'role:Op. Keuangan RW,Op. Keuangan RT,DKM'])->group(function () {
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
    // Galeri UMKM (Eksplorasi & Publik Warga)
    Route::get('/galeri', [GaleriUmkmController::class, 'index'])->name('umkm.galeri');
    Route::get('/galeri/koleksi/{tipe?}', [GaleriUmkmController::class, 'koleksiProduk'])->name('umkm.koleksi');
    Route::get('/galeri/usaha/{id?}', [GaleriUmkmController::class, 'detailUsaha'])->name('umkm.usaha.show');
    Route::get('/galeri/detail-usaha/{id?}', [GaleriUmkmController::class, 'detailUsaha'])->name('umkm.detail_usaha');
    Route::get('/galeri/produk/{id}', [GaleriUmkmController::class, 'detailProduk'])->name('umkm.produk.detail');
    Route::get('/galeri/daftar', [GaleriUmkmController::class, 'createUsaha'])->name('umkm.daftar');
    Route::post('/galeri/daftar', [GaleriUmkmController::class, 'storeUsaha'])->name('umkm.store-usaha');

    // Kelola UMKM (Manajemen Toko & Produk Pemilik UMKM)
    Route::get('/galeri/kelola', [KelolaUmkmController::class, 'index'])->name('umkm.kelola');
    Route::put('/galeri/usaha/{id}', [KelolaUmkmController::class, 'updateUsaha'])->name('umkm.update-usaha');
    Route::post('/galeri/usaha/{id}/sampul', [KelolaUmkmController::class, 'updateSampulUsaha'])->name('umkm.update-sampul');
    Route::get('/galeri/kelola/produk', [KelolaUmkmController::class, 'kelolaProduk'])->name('umkm.produk.index');
    Route::get('/galeri/kelola/produk/create', [KelolaUmkmController::class, 'createProduk'])->name('umkm.produk.create');
    Route::post('/galeri/kelola/produk', [KelolaUmkmController::class, 'storeProduk'])->name('umkm.produk.store');
    Route::put('/galeri/kelola/produk/{id}', [KelolaUmkmController::class, 'updateProduk'])->name('umkm.produk.update');
    Route::delete('/galeri/kelola/produk/{id}', [KelolaUmkmController::class, 'destroyProduk'])->name('umkm.produk.destroy');
});