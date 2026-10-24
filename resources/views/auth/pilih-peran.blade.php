@extends('layouts.app')

@section('title', 'Pilih Peran')

@section('content')
<section class="d-flex align-items-center justify-content-center py-5" style="min-height: 80vh; background-color: #f4f7f6;">
    <div class="card border-0 shadow-sm" style="max-width: 460px; width: 100%; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; background-color: #e8f5e9; color: #2E7D32; border-radius: 50%;">
                    <i class="bi bi-person-rolodex fs-3"></i>
                </div>
                <h3 class="fw-bold text-dark">Masuk Sebagai</h3>
                <p class="text-muted small mb-0">Akun Anda memiliki lebih dari satu peran. Pilih peran untuk melanjutkan.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('pilih-peran.store') }}">
                @csrf
                <div class="d-grid gap-3">
                    @foreach ($options as $opt)
                        <button type="submit" name="role" value="{{ $opt['value'] }}"
                                class="btn text-start d-flex align-items-center gap-3 p-3 border rounded-3 w-100 role-choice"
                                style="background: white; border-color: #D1D5DB !important;">
                            <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                  style="width: 42px; height: 42px; border-radius: 10px; background: #e8f5e9; color: #2E7D32;">
                                <i class="bi {{ $opt['value'] === 'Warga' ? 'bi-house-heart-fill' : 'bi-shield-lock-fill' }} fs-5"></i>
                            </span>
                            <span class="flex-grow-1">
                                <span class="d-block fw-semibold text-dark">{{ $opt['label'] }}</span>
                                <span class="d-block text-muted small">
                                    {{ $opt['value'] === 'Warga' ? 'Akses layanan warga & keluarga' : 'Akses dashboard & tugas kepengurusan' }}
                                </span>
                            </span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </button>
                    @endforeach
                </div>
            </form>

            <div class="text-center mt-4">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none small text-muted p-0">
                        <i class="bi bi-box-arrow-left me-1"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .role-choice { transition: all 0.15s ease; }
    .role-choice:hover { border-color: #2E7D32 !important; background: #f6fbf6 !important; transform: translateY(-1px); }
</style>
@endpush
