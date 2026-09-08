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
use App\Http\Controllers\Warga\BeritaController as WargaBeritaController;
use App\Http\Controllers\Warga\KeluargaController;
use App\Http\Controllers\Warga\SuratController;
use App\Http\Controllers\Warga\GaleriUmkmController;
use App\Http\Controllers\Warga\KelolaUmkmController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RwController;
use App\Http\Controllers\RtController;
use App\Http\Controllers\OpKontenController;
use App\Http\Controllers\OpKeuanganController;
use App\Http\Controllers\OpKonten\AgendaController as OpKontenAgendaController;
use App\Http\Controllers\OpKonten\GaleriController as OpKontenGaleriController;
use App\Http\Controllers\OpKonten\BeritaController as OpKontenBeritaController;
use App\Http\Controllers\OpKonten\PengumumanController as OpKontenPengumumanController;

// LandingPage & Shared Domain: UMKM
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
Route::get('/transparansi', [TransparansiController::class, 'index'])->name('transparansi');
Route::get('/transparansi/{id}/pdf', [TransparansiController::class, 'downloadPdf'])->name('transparansi.download-pdf');
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
Route::get('/berita/{id}', [BeritaController::class, 'show'])->name('berita.show');
Route::get('/layanan-mandiri', [LayananController::class, 'index'])->name('layanan-mandiri');

// Shared Domain: UMKM (Galeri, Koleksi, Usaha, Produk)
Route::get('/galeri', [GaleriUmkmController::class, 'index'])->name('umkm.galeri');
Route::get('/galeri/koleksi/{tipe?}', [GaleriUmkmController::class, 'koleksiProduk'])->name('umkm.koleksi');
Route::get('/usaha/{id?}', [UmkmController::class, 'detailUsaha'])->name('umkm.usaha.show');
Route::get('/produk/{id}', [UmkmController::class, 'detailProduk'])->name('produk.show');

Route::get('/pojok-umkm', [UmkmController::class, 'index'])->name('pojok-umkm');
Route::get('/pojok-umkm/usaha/{id?}', [UmkmController::class, 'detailUsaha'])->name('pojok-umkm.detail_usaha');
Route::get('/public/usaha/{id?}', [UmkmController::class, 'detailUsaha'])->name('public.umkm.usaha.show');
Route::get('/pojok-umkm/produk/{id}', [UmkmController::class, 'detailProduk'])->name('public.umkm.produk.show');
Route::get('/galeri/detail-usaha/{id?}', [UmkmController::class, 'detailUsaha'])->name('umkm.detail_usaha');

// Redirect /warga/galeri routes
Route::redirect('/warga/galeri', '/galeri')->name('warga.umkm.galeri');
Route::get('/warga/galeri/koleksi/{tipe?}', function($tipe = null) {
    return redirect()->route('umkm.koleksi', $tipe ? ['tipe' => $tipe] : []);
})->name('warga.umkm.koleksi');
Route::get('/warga/galeri/usaha/{id?}', function($id = null) {
    return redirect()->route('umkm.usaha.show', $id ? ['id' => $id] : []);
})->name('warga.umkm.usaha.show');
Route::get('/warga/galeri/detail-usaha/{id?}', function($id = null) {
    return redirect()->route('umkm.usaha.show', $id ? ['id' => $id] : []);
})->name('warga.umkm.detail_usaha');
Route::get('/warga/galeri/produk/{id}', function($id) {
    return redirect()->route('produk.show', ['id' => $id]);
})->name('warga.umkm.produk.detail');
Route::redirect('/galeri/kelola', '/warga/galeri/kelola');

//Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::get('/aktivasi', [RegisterController::class, 'aktivasi'])->name('aktivasi');

// Temporary Magic Login for Testing
Route::get('/login-warga', function () {
    $user = \App\Models\User::where('nik', '3204xxxxxxxx0001')->first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect()->route('warga.surat.index');
    }
    return "User warga tidak ditemukan. Pastikan seeder sudah dijalankan.";
});

Route::get('/debug-php', function () {
    return [
        'upload_tmp_dir' => ini_get('upload_tmp_dir'),
        'sys_temp_dir' => sys_get_temp_dir(),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'user' => get_current_user(),
    ];
});

Route::get('/login-rt', function () {
    $user = \App\Models\User::where('nik', '3217010101010008')->first(); // NIK RT
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect()->route('rt.persetujuan-dokumen');
    }
    return "User RT tidak ditemukan.";
});

Route::get('/login-rw', function () {
    $user = \App\Models\User::where('nik', '3217010101010002')->first(); // NIK RW
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect()->route('rw.persetujuan-dokumen');
    }
    return "User RW tidak ditemukan.";
});

// Admin Routes (Role: Admin Aplikasi)
Route::middleware(['auth', 'role:Admin Aplikasi'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/pengaturan-sistem', [AdminController::class, 'pengaturanSistem'])->name('admin.pengaturan-sistem');
    Route::get('/manajemen-hak-akses', [AdminController::class, 'manajemenHakAkses'])->name('admin.manajemen-hak-akses');
    Route::get('/log-aktivitas', [AdminController::class, 'logAktivitas'])->name('admin.log-aktivitas');
    Route::get('/arsip-data-warga', [AdminController::class, 'arsipDataWarga'])->name('admin.arsip-data-warga');
    Route::get('/manajemen-data', [AdminController::class, 'manajemenData'])->name('admin.manajemen-data');
});

// RW (Role: Admin RW, Pimpinan RW)
Route::middleware(['auth', 'role:Admin RW,Pimpinan RW,Pimpinan'])->prefix('rw')->name('rw.')->group(function () {
    Route::get('/dashboard', [RwController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [RwController::class, 'dashboard']);
    
    // Pusat Informasi (Berita & Agenda Approval)
    Route::get('/pusat-informasi', [\App\Http\Controllers\Rw\PusatInformasiController::class, 'index'])->name('pusat-informasi.index');
    Route::put('/pusat-informasi/berita/{id}/approve', [\App\Http\Controllers\Rw\PusatInformasiController::class, 'approveBerita'])->name('pusat-informasi.berita.approve');
    Route::put('/pusat-informasi/berita/{id}/reject', [\App\Http\Controllers\Rw\PusatInformasiController::class, 'rejectBerita'])->name('pusat-informasi.berita.reject');
    Route::put('/pusat-informasi/agenda/{id}/approve', [\App\Http\Controllers\Rw\PusatInformasiController::class, 'approveAgenda'])->name('pusat-informasi.agenda.approve');
    Route::put('/pusat-informasi/agenda/{id}/reject', [\App\Http\Controllers\Rw\PusatInformasiController::class, 'rejectAgenda'])->name('pusat-informasi.agenda.reject');
    Route::put('/pusat-informasi/pengumuman/{id}/approve', [\App\Http\Controllers\Rw\PusatInformasiController::class, 'approvePengumuman'])->name('pusat-informasi.pengumuman.approve');
    Route::put('/pusat-informasi/pengumuman/{id}/reject', [\App\Http\Controllers\Rw\PusatInformasiController::class, 'rejectPengumuman'])->name('pusat-informasi.pengumuman.reject');

    // Persetujuan Dokumen
    Route::get('/persetujuan-dokumen', [RwController::class, 'persetujuanDokumen'])->name('persetujuan-dokumen');
    Route::post('/surat/{id}/approve', [RwController::class, 'approveDokumen'])->name('surat.approve');
    Route::post('/surat/{id}/reject', [RwController::class, 'rejectDokumen'])->name('surat.reject');
    Route::get('/surat/{id}/preview', [RwController::class, 'previewSurat'])->name('surat.preview');

    // UMKM
    Route::get('/umkm', [RwController::class, 'umkm'])->name('umkm.index');
    Route::get('/umkm/usaha/{id}', [RwController::class, 'detailUsahaUmkm'])->name('umkm.usaha.detail');
    Route::post('/umkm/{id}/approve', [RwController::class, 'approveUmkm'])->name('umkm.approve');
    Route::post('/umkm/{id}/reject', [RwController::class, 'rejectUmkm'])->name('umkm.reject');
});

//RT (Role: Ketua RT)
Route::middleware(['auth', 'role:Ketua RT'])->prefix('rt')->group(function () {
    Route::get('/dashboard', [RtController::class, 'dashboard'])->name('rt.dashboard');
    Route::get('/', [RtController::class, 'dashboard']);
    Route::get('/persetujuan-dokumen', [RtController::class, 'persetujuanDokumen'])->name('rt.persetujuan-dokumen');
    Route::post('/surat/{id}/approve', [RtController::class, 'approveDokumen'])->name('rt.surat.approve');
    Route::post('/surat/{id}/reject', [RtController::class, 'rejectDokumen'])->name('rt.surat.reject');
});

// Operator Konten Routes (Role: Op Konten RW, Op Konten RT)
Route::middleware(['auth', 'role:Op Konten RW,Op Konten RT,Op. Konten RW,Op. Konten RT,Op Konten RW,Op Konten RT'])->group(function () {
    Route::prefix('op-konten')->name('opkonten.')->group(function () {
        Route::get('/dashboard', [OpKontenController::class, 'dashboard'])->name('dashboard');
        Route::get('/berita', [OpKontenBeritaController::class, 'index'])->name('berita.index');
        Route::post('/berita', [OpKontenBeritaController::class, 'store'])->name('berita.store');
        Route::put('/berita/{berita}', [OpKontenBeritaController::class, 'update'])->name('berita.update');
        Route::delete('/berita/{berita}', [OpKontenBeritaController::class, 'destroy'])->name('berita.destroy');
        Route::put('/berita/{berita}/submit', [OpKontenBeritaController::class, 'submit'])->name('berita.submit');
        Route::put('/berita/{berita}/archive', [OpKontenBeritaController::class, 'archive'])->name('berita.archive');
        Route::put('/berita/{berita}/unarchive', [OpKontenBeritaController::class, 'unarchive'])->name('berita.unarchive');
        Route::put('/berita/{berita}/revoke', [OpKontenBeritaController::class, 'revoke'])->name('berita.revoke');
        Route::get('/agenda', [OpKontenAgendaController::class, 'index'])->name('agenda.index');
        Route::post('/agenda', [OpKontenAgendaController::class, 'store'])->name('agenda.store');
        Route::put('/agenda/{agenda}', [OpKontenAgendaController::class, 'update'])->name('agenda.update');
        Route::delete('/agenda/{agenda}', [OpKontenAgendaController::class, 'destroy'])->name('agenda.destroy');
        Route::post('/agenda/{agenda}/submit', [OpKontenAgendaController::class, 'submitToReview'])->name('agenda.submitToReview');
        Route::post('/agenda/{agenda}/revoke', [OpKontenAgendaController::class, 'revokeToDraft'])->name('agenda.revokeToDraft');
        Route::get('/galeri', [OpKontenGaleriController::class, 'index'])->name('galeri.index');
        Route::get('/galeri/{id}', [OpKontenGaleriController::class, 'show'])->name('galeri.show');
        Route::post('/galeri', [OpKontenGaleriController::class, 'store'])->name('galeri.store');
        Route::put('/galeri/{id}', [OpKontenGaleriController::class, 'update'])->name('galeri.update');
        Route::delete('/galeri/{id}', [OpKontenGaleriController::class, 'destroy'])->name('galeri.destroy');
        Route::get('/pengumuman', [OpKontenPengumumanController::class, 'index'])->name('pengumuman.index');
        Route::post('/pengumuman', [OpKontenPengumumanController::class, 'store'])->name('pengumuman.store');
        Route::put('/pengumuman/{id}', [OpKontenPengumumanController::class, 'update'])->name('pengumuman.update');
        Route::delete('/pengumuman/{id}', [OpKontenPengumumanController::class, 'destroy'])->name('pengumuman.destroy');
        Route::post('/pengumuman/{id}/submit', [OpKontenPengumumanController::class, 'submit'])->name('pengumuman.submit');
        Route::post('/pengumuman/{id}/cancel-submit', [OpKontenPengumumanController::class, 'cancelSubmit'])->name('pengumuman.cancel-submit');
        Route::get('/', [OpKontenController::class, 'dashboard']);
    });
    Route::get('/opkonten/dashboard', [OpKontenController::class, 'dashboard']);
});

// Operator Keuangan Routes (Role: Op. Keuangan RW, Op. Keuangan RT, DKM)
Route::middleware(['auth', 'role:Op Keuangan RW,Op Keuangan RT,DKM,Op. Keuangan RW,Op. Keuangan RT,Op Keuangan RW,Op Keuangan RT'])->group(function () {
    Route::prefix('op-keuangan')->group(function () {
        Route::get('/dashboard', [OpKeuanganController::class, 'dashboard'])->name('opkeuangan.dashboard');
        Route::get('/', [OpKeuanganController::class, 'dashboard']);
    });
    Route::get('/opkeuangan/dashboard', [OpKeuanganController::class, 'dashboard']);
});

// Warga
Route::middleware(['auth', 'role:Warga'])->prefix('warga')->name('warga.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/berita', [WargaBeritaController::class, 'index'])->name('berita.index');
    Route::get('/keluarga', [KeluargaController::class, 'index'])->name('keluarga.index');
    Route::get('/keluarga/edit', [KeluargaController::class, 'edit'])->name('keluarga.edit');
    Route::put('/keluarga/update', [KeluargaController::class, 'update'])->name('keluarga.update');
    Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
    Route::post('/surat', [SuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/{id}/pdf', [SuratController::class, 'downloadPdf'])->name('surat.download-pdf');
    Route::get('/surat/{id}/download', [SuratController::class, 'downloadPdf'])->name('surat.download');
    
    // UMKM Galeri Baru
    Route::get('/galeri/produk/{id}', [GaleriUmkmController::class, 'detailProduk'])->name('umkm.produk.detail');
    Route::get('/galeri/daftar', [GaleriUmkmController::class, 'createUsaha'])->name('umkm.daftar');
    Route::post('/galeri/daftar', [GaleriUmkmController::class, 'storeUsaha'])->name('umkm.store-usaha');
    
    Route::get('/galeri/kelola', [KelolaUmkmController::class, 'index'])->name('umkm.kelola');
    Route::put('/galeri/usaha/{id}', [KelolaUmkmController::class, 'updateUsaha'])->name('umkm.update-usaha');
    Route::post('/galeri/usaha/{id}/sampul', [KelolaUmkmController::class, 'updateSampulUsaha'])->name('umkm.update-sampul');
    Route::get('/galeri/kelola/produk/create', [KelolaUmkmController::class, 'createProduk'])->name('umkm.produk.create');
    Route::post('/galeri/kelola/produk', [KelolaUmkmController::class, 'storeProduk'])->name('umkm.produk.store');
    Route::put('/galeri/kelola/produk/{id}', [KelolaUmkmController::class, 'updateProduk'])->name('umkm.produk.update');
    Route::delete('/galeri/kelola/produk/{id}', [KelolaUmkmController::class, 'destroyProduk'])->name('umkm.produk.destroy');
});

