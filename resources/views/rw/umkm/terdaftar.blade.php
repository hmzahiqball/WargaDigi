@extends('layouts.global')

@section('title', 'Pojok UMKM - RW')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1 fs-2">Pojok UMKM</h2>
            <p class="text-muted mb-0 small fs-6">
                Daftar usaha warga yang sudah disetujui RW{{ $isPimpinan ? '.' : ', beserta pengelolaan status tampilnya.' }}
            </p>
        </div>
        <a href="{{ route('rw.persetujuan-umkm') }}" class="btn btn-outline-success rounded-3 px-3 py-2 small fw-semibold text-nowrap">
            <i class="bi bi-shield-check me-1"></i> Persetujuan UMKM
        </a>
    </div>

    @include('rw.umkm.partials.daftar-terdaftar', [
        'daftarUsahaTerdaftar' => $daftarUsahaTerdaftar,
        'isPimpinan' => $isPimpinan,
        'daftarKategoriUmkm' => $daftarKategoriUmkm,
    ])
</div>
@endsection
