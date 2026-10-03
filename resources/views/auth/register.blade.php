@extends('layouts.app')

@section('title', 'Pendaftaran Akun')

@section('content')
<section class="d-flex align-items-center justify-content-center py-5" style="min-height: 80vh; background-color: #f4f7f6;">
    <div class="card border-0 shadow-sm" style="max-width: 500px; width: 100%; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background-color: #e8f5e9; color: #2E7D32; border-radius: 0.5rem;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <h3 class="fw-bold text-dark">WargaDigi 21</h3>
                <p class="text-muted small">Bergabunglah dan nikmati kemudahan layanan digital RW 21.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('aktivasi.verifikasi') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" inputmode="numeric" maxlength="16" required value="{{ old('nik') }}" style="border-radius: 0.5rem;">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">No. WhatsApp</label>
                    <input type="tel" name="no_wa" class="form-control" placeholder="Mulai dengan 08..." required value="{{ old('no_wa') }}" style="border-radius: 0.5rem;">
                </div>
                <button type="submit" class="btn w-100 fw-bold py-2" style="background-color: #2E7D32; color: white; border-radius: 0.5rem; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#1B5E20'" onmouseout="this.style.backgroundColor='#2E7D32'">Aktivasi Akun</button>
            </form>

            <div class="text-center mt-4">
                <a href="login" class="text-decoration-none small" style="color: #3949ab; font-weight: 600;">Sudah punya akun? Masuk di sini</a>
            </div>
        </div>
    </div>
</section>
@endsection
