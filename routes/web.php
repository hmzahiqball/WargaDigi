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


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
Route::get('/transparansi', [TransparansiController::class, 'index'])->name('transparansi');
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
Route::get('/pojok-umkm', [UmkmController::class, 'index'])->name('pojok-umkm');
Route::get('/layanan-mandiri', [LayananController::class, 'index'])->name('layanan-mandiri');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::get('/aktivasi', [RegisterController::class, 'aktivasi'])->name('aktivasi');
Route::get('/login',[RegisterController::class,'login'])->name('login');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/pengaturan-sistem', [AdminController::class, 'pengaturanSistem'])->name('admin.pengaturan-sistem');
    Route::get('/manajemen-hak-akses', [AdminController::class, 'manajemenHakAkses'])->name('admin.manajemen-hak-akses');
    Route::get('/log-aktivitas', [AdminController::class, 'logAktivitas'])->name('admin.log-aktivitas');
    Route::get('/arsip-data-warga', [AdminController::class, 'arsipDataWarga'])->name('admin.arsip-data-warga');
    Route::get('/manajemen-data', [AdminController::class, 'manajemenData'])->name('admin.manajemen-data');
});

// RW Routes
Route::prefix('rw')->group(function () {
    Route::get('/dashboard', [RwController::class, 'dashboard'])->name('rw.dashboard');
    Route::get('/', [RwController::class, 'dashboard']);
    Route::get('/persetujuan-dokumen', [RwController::class, 'persetujuanDokumen'])->name('rw.persetujuan-dokumen');
    Route::post('/surat/{id}/approve', [RwController::class, 'approveDokumen'])->name('rw.surat.approve');
    Route::post('/surat/{id}/reject', [RwController::class, 'rejectDokumen'])->name('rw.surat.reject');
    Route::get('/surat/{id}/preview', [RwController::class, 'previewSurat'])->name('rw.surat.preview');
});

// RT Routes
Route::prefix('rt')->group(function () {
    Route::get('/dashboard', [RtController::class, 'dashboard'])->name('rt.dashboard');
    Route::get('/', [RtController::class, 'dashboard']);
    Route::get('/persetujuan-dokumen', [RtController::class, 'persetujuanDokumen'])->name('rt.persetujuan-dokumen');
    Route::post('/surat/{id}/approve', [RtController::class, 'approveDokumen'])->name('rt.surat.approve');
    Route::post('/surat/{id}/reject', [RtController::class, 'rejectDokumen'])->name('rt.surat.reject');
});

// Operator Konten Routes
Route::prefix('op-konten')->group(function () {
    Route::get('/dashboard', [OpKontenController::class, 'dashboard'])->name('opkonten.dashboard');
    Route::get('/', [OpKontenController::class, 'dashboard']);
});
Route::get('/opkonten/dashboard', [OpKontenController::class, 'dashboard']);

// Operator Keuangan Routes
Route::prefix('op-keuangan')->group(function () {
    Route::get('/dashboard', [OpKeuanganController::class, 'dashboard'])->name('opkeuangan.dashboard');
    Route::get('/', [OpKeuanganController::class, 'dashboard']);
});
Route::get('/opkeuangan/dashboard', [OpKeuanganController::class, 'dashboard']);

// Warga Routes
Route::middleware(['auth'])->prefix('warga')->name('warga.')->group(function () {
      Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/keluarga', [KeluargaController::class, 'index'])->name('keluarga.index');
    Route::get('/keluarga/edit', [KeluargaController::class, 'edit'])->name('keluarga.edit');
    Route::put('/keluarga/update', [KeluargaController::class, 'update'])->name('keluarga.update');
    Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/create', [SuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [SuratController::class, 'store'])->name('surat.store');
    Route::get('/umkm/daftar', [WargaUmkmController::class, 'createUsaha'])->name('umkm.daftar');
    Route::post('/umkm/daftar', [WargaUmkmController::class, 'storeUsaha'])->name('umkm.store-usaha');
    Route::get('/umkm/produk', [WargaUmkmController::class, 'indexProduk'])->name('umkm.produk.index');
    Route::get('/umkm/produk/create', [WargaUmkmController::class, 'createProduk'])->name('umkm.produk.create');
    Route::post('/umkm/produk', [WargaUmkmController::class, 'storeProduk'])->name('umkm.produk.store');
});