<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\TransparansiController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
Route::get('/transparansi', [TransparansiController::class, 'index'])->name('transparansi');
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
Route::get('/pojok-umkm', [UmkmController::class, 'index'])->name('pojok-umkm');
Route::get('/layanan-mandiri', [LayananController::class, 'index'])->name('layanan-mandiri');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/aktivasi', [AuthController::class, 'aktivasi'])->name('aktivasi');
