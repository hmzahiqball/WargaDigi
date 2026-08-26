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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
Route::get('/transparansi', [TransparansiController::class, 'index'])->name('transparansi');
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
Route::get('/pojok-umkm', [UmkmController::class, 'index'])->name('pojok-umkm');
Route::get('/layanan-mandiri', [LayananController::class, 'index'])->name('layanan-mandiri');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/aktivasi', [AuthController::class, 'aktivasi'])->name('aktivasi');
Route::get('/login', [AuthController::class, 'login'])->name('login');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/pengaturan-sistem', [AdminController::class, 'pengaturanSistem'])->name('admin.pengaturan-sistem');
    Route::get('/manajemen-hak-akses', [AdminController::class, 'manajemenHakAkses'])->name('admin.manajemen-hak-akses');
    Route::get('/log-aktivitas', [AdminController::class, 'logAktivitas'])->name('admin.log-aktivitas');
    Route::get('/arsip-data-warga', [AdminController::class, 'arsipDataWarga'])->name('admin.arsip-data-warga');
    Route::get('/manajemen-data', [AdminController::class, 'manajemenData'])->name('admin.manajemen-data');
});
