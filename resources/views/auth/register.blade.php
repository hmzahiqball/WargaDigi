@extends('layouts.app')

@section('title', 'Aktivasi Akun')

@section('content')
<section class="d-flex align-items-center justify-content-center py-5" style="min-height: 80vh; background-color: #f4f7f6;">
    <div class="card border-0 shadow-sm" style="max-width: 520px; width: 100%; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background-color: #e8f5e9; color: #2E7D32; border-radius: 0.5rem;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <h3 class="fw-bold text-dark">WargaDigi 21</h3>
                <p class="text-muted small">Aktivasi akun warga RW 21.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif

            <div class="alert alert-info small border-0" style="background-color:#e8f5e9;color:#1B5E20;">
                <i class="bi bi-info-circle me-1"></i>
                Akun warga didaftarkan oleh <strong>Ketua RT</strong>. Untuk mengaktifkan akun,
                silakan minta <strong>link aktivasi</strong> kepada Ketua RT Anda, lalu buka link
                tersebut untuk membuat password sendiri.
            </div>

            <ol class="small text-muted ps-3 mb-4">
                <li class="mb-1">Hubungi Ketua RT untuk mendapatkan link aktivasi.</li>
                <li class="mb-1">Buka link aktivasi di perangkat Anda.</li>
                <li class="mb-1">Buat password, lalu masuk dengan username Anda.</li>
            </ol>

            <div class="text-center">
                <a href="{{ route('login') }}" class="btn w-100 fw-bold py-2 text-white" style="background-color: #2E7D32; border-radius: 0.5rem;">
                    Kembali ke Halaman Masuk
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
