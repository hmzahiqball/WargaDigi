@extends('layouts.app')

@section('title', 'Verifikasi OTP & Aktivasi')

@section('content')
<section class="py-0 py-md-5" style="background-color: #f4f7f6; min-height: calc(100vh - 76px - 280px);">
    <div class="container h-100">
        <div class="row h-100 bg-white shadow-sm overflow-hidden" style="border-radius: 1rem; min-height: 600px;">
            {{-- Left Side: Hero Image --}}
            <div class="col-lg-6 d-none d-lg-flex p-0 position-relative">
                <img src="{{ asset('images/auth-hero.jpg') }}" alt="WargaDigi Community" class="w-100 h-100" style="object-fit: cover;">
                <div class="position-absolute bottom-0 start-0 w-100 p-5" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                    <h2 class="text-white fw-bold mb-2">WargaDigi 21</h2>
                    <p class="text-white-50 mb-0">Digital Gotong Royong. Membangun lingkungan yang lebih baik, terhubung, dan sejahtera bersama-sama.</p>
                </div>
            </div>
            
            {{-- Right Side: OTP & Password Form --}}
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5">
                <div class="w-100" style="max-width: 400px;">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; background-color: #e8f5e9; color: #2E7D32; border-radius: 50%;">
                            <i class="bi bi-shield-check fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark">Verifikasi Kode OTP</h3>
                        <p class="text-muted small">Masukkan 6 digit kode yang telah dikirimkan ke nomor WhatsApp Anda.</p>
                    </div>

                    <form>
                        {{-- OTP Inputs --}}
                        <div class="d-flex justify-content-between mb-3 gap-2">
                            @for ($i = 0; $i < 6; $i++)
                            <input type="text" class="form-control text-center fw-bold text-success" maxlength="1" style="width: 48px; height: 54px; font-size: 1.25rem; border-color: #2E7D32; border-radius: 0.5rem;" autocomplete="off">
                            @endfor
                        </div>
                        <div class="text-center mb-4">
                            <small class="text-muted">Kode berlaku selama <span class="text-success fw-bold">04:59</span></small>
                        </div>

                        <div class="alert p-2 d-flex align-items-center justify-content-center gap-2 mb-4" style="background-color: #e1f5fe; color: #37474f; border-radius: 0.5rem; border: none; font-size: 0.85rem;">
                            <i class="bi bi-check-circle text-success"></i>
                            Data terverifikasi oleh pengurus RW
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Password Baru (Minimal 8 karakter)</label>
                            <div class="input-group">
                                <input type="password" class="form-control border-end-0" placeholder="Masukkan Password" style="border-radius: 0.5rem 0 0 0.5rem;">
                                <span class="input-group-text bg-white border-start-0 text-success" style="border-radius: 0 0.5rem 0.5rem 0; cursor: pointer;">
                                    <i class="bi bi-eye-fill"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-dark">Konfirmasi Password</label>
                            <input type="password" class="form-control" placeholder="Masukkan Password" style="border-radius: 0.5rem;">
                        </div>

                        <button type="submit" class="btn w-100 fw-bold py-2" style="background-color: #2E7D32; color: white; border-radius: 0.5rem; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#1B5E20'" onmouseout="this.style.backgroundColor='#2E7D32'">Aktivasi Akun</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
