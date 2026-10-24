@extends('layouts.global')

@section('title', 'Kepengurusan / Pejabat')

@section('content')
<div class="admin-page-header">
    <h1>Kepengurusan (Pejabat)</h1>
    <p class="text-muted">Tetapkan Ketua RW dan Ketua RT dari data penduduk. Setiap pergantian tersimpan sebagai riwayat untuk pelacakan.</p>
</div>

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="border-radius: 0.5rem;">
        <i class="bi bi-check-circle-fill"></i> <span>{{ session('success') }}</span>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius: 0.5rem;">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

@php
    $fmt = fn($d) => $d ? \Illuminate\Support\Carbon::parse($d)->translatedFormat('d M Y') : '-';
@endphp

{{-- ===================== KETUA RW ===================== --}}
<div class="admin-card mb-4">
    <div class="admin-card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-success"></i>Ketua RW</h5>
            @if($ketuaRwAktif && $ketuaRwAktif->penduduk)
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i> Aktif: {{ $ketuaRwAktif->penduduk->nama_lengkap }}
                </span>
            @else
                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">Belum ditetapkan</span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.pejabat.store') }}" class="row g-2 align-items-end mb-3">
            @csrf
            <input type="hidden" name="jabatan" value="ketua_rw">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Pilih Penduduk</label>
                <select name="penduduk_nik" class="form-select admin-input" required>
                    <option value="">— Pilih penduduk —</option>
                    @foreach($semuaPenduduk as $p)
                        <option value="{{ $p->nik }}" {{ optional($ketuaRwAktif)->penduduk_nik === $p->nik ? 'selected' : '' }}>
                            {{ $p->nama_lengkap }} — {{ $p->nik }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Mulai Menjabat</label>
                <input type="date" name="mulai_menjabat" class="form-control admin-input">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success w-100" style="border-radius: 0.5rem;">
                    <i class="bi bi-person-check me-1"></i> Tetapkan
                </button>
            </div>
        </form>

        @include('admin.partials.pejabat-riwayat', ['riwayat' => $riwayatRw, 'jabatan' => 'ketua_rw', 'rtId' => null, 'fmt' => $fmt])
    </div>
</div>

{{-- ===================== KETUA RT (per RT) ===================== --}}
@foreach($rtList as $rt)
    @php
        $aktif = $ketuaRtAktif[$rt->id] ?? null;
        $riwayat = $riwayatRt[$rt->id] ?? collect();
        $pendudukRt = $pendudukPerRt[$rt->id] ?? $semuaPenduduk;
    @endphp
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-house-door me-2 text-success"></i>Ketua RT {{ $rt->kode_rt }}</h5>
                @if($aktif && $aktif->penduduk)
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i> Aktif: {{ $aktif->penduduk->nama_lengkap }}
                    </span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">Belum ditetapkan</span>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.pejabat.store') }}" class="row g-2 align-items-end mb-3">
                @csrf
                <input type="hidden" name="jabatan" value="ketua_rt">
                <input type="hidden" name="rt_id" value="{{ $rt->id }}">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Pilih Penduduk (warga RT {{ $rt->kode_rt }})</label>
                    <select name="penduduk_nik" class="form-select admin-input" required>
                        <option value="">— Pilih penduduk —</option>
                        @foreach($pendudukRt as $p)
                            <option value="{{ $p->nik }}" {{ optional($aktif)->penduduk_nik === $p->nik ? 'selected' : '' }}>
                                {{ $p->nama_lengkap }} — {{ $p->nik }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Mulai Menjabat</label>
                    <input type="date" name="mulai_menjabat" class="form-control admin-input">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100" style="border-radius: 0.5rem;">
                        <i class="bi bi-person-check me-1"></i> Tetapkan
                    </button>
                </div>
            </form>

            @include('admin.partials.pejabat-riwayat', ['riwayat' => $riwayat, 'jabatan' => 'ketua_rt', 'rtId' => $rt->id, 'fmt' => $fmt])
        </div>
    </div>
@endforeach
@endsection
