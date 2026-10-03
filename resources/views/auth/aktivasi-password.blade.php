@extends('layouts.app')

@section('title', 'Buat Password')

@section('content')
<section class="d-flex align-items-center justify-content-center py-5" style="min-height: 80vh; background-color: #f4f7f6;">
    <div class="card border-0 shadow-sm" style="max-width: 500px; width: 100%; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <h3 class="fw-bold text-dark mb-2">Buat Password Akun</h3>
            <p class="text-muted small mb-4">Password baru minimal 8 karakter.</p>
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('aktivasi.password.complete') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru</label>
                    <input type="password" name="password" class="form-control" minlength="8" required autocomplete="new-password">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" minlength="8" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn w-100 fw-bold py-2 text-white" style="background-color: #2E7D32;">Aktivasi Akun</button>
            </form>
        </div>
    </div>
</section>
@endsection
