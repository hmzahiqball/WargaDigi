@extends('layouts.app')

@section('title', 'Verifikasi OTP')

@section('content')
<section class="d-flex align-items-center justify-content-center py-5" style="min-height: 80vh; background-color: #f4f7f6;">
    <div class="card border-0 shadow-sm" style="max-width: 500px; width: 100%; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; background-color: #e8f5e9; color: #2E7D32; border-radius: 50%;">
                    <i class="bi bi-shield-check fs-3"></i>
                </div>
                <h3 class="fw-bold text-dark">Verifikasi Kode OTP</h3>
                <p class="text-muted small">Masukkan kode OTP mock untuk pengujian UAT.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-info small">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('aktivasi.otp.verify') }}" id="otpForm">
                @csrf
                <div class="d-flex justify-content-between mb-3 gap-2">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" name="otp[]" class="form-control text-center fw-bold text-success otp-digit" maxlength="1" inputmode="numeric" style="width: 48px; height: 54px; font-size: 1.25rem; border-color: #2E7D32; border-radius: 0.5rem;" autocomplete="off">
                    @endfor
                </div>
                <input type="hidden" name="otp" id="otp">
                <button type="submit" class="btn w-100 fw-bold py-2 text-white" style="background-color: #2E7D32;">Verifikasi OTP</button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const otpDigits = Array.from(document.querySelectorAll('.otp-digit'));
    otpDigits.forEach(function (input, index) {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 1);
            if (this.value && otpDigits[index + 1]) otpDigits[index + 1].focus();
        });
    });
    document.getElementById('otpForm').addEventListener('submit', function () {
        document.getElementById('otp').value = otpDigits.map(input => input.value).join('');
    });
</script>
@endpush
