@extends('layouts.app')

@section('title', 'Aktivasi Akun')

@section('content')
<section class="d-flex align-items-center justify-content-center py-5" style="min-height: 80vh; background-color: #f4f7f6;">
    <div class="card border-0 shadow-sm" style="max-width: 500px; width: 100%; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-3">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width:64px;height:64px;">
                    <i class="bi bi-person-check text-success" style="font-size:1.75rem;"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-1 text-center">Aktivasi Akun</h3>
            <p class="text-muted small mb-4 text-center">
                Halo <strong>{{ $nama }}</strong>. Tentukan username dan password untuk mengaktifkan akun Anda.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('aktivasi.token.proses', ['token' => $token]) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $username) }}" required autocomplete="username" placeholder="Huruf, angka, titik, _ atau -">
                    <small class="text-muted">Username unik untuk masuk ke aplikasi.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" minlength="8" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" minlength="8" required autocomplete="new-password" placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn w-100 fw-bold py-2 text-white" style="background-color: #2E7D32;">
                    <i class="bi bi-check-circle me-1"></i> Aktifkan &amp; Simpan Password
                </button>
            </form>

            <p class="text-center small text-muted mt-3 mb-0">
                Sudah aktif? <a href="{{ route('login') }}" class="text-success fw-semibold">Masuk di sini</a>
            </p>
        </div>
    </div>
</section>
@endsection
