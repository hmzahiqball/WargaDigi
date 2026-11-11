@extends('layouts.global')

@section('title', 'Detail Tagihan: ' . $tagihan->judul)

@section('content')
<div class="mb-4">
    <a href="{{ route('opkeuangan.iuran.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Iuran
    </a>
    <h2 class="fw-bold text-dark mb-1">{{ $tagihan->judul }}</h2>
    <p class="text-muted mb-0">Total Tagihan: <span class="fw-bold text-primary">{{ $tagihan->formatted_nominal }}</span> | Tenggat: {{ $tagihan->tenggat ? \Carbon\Carbon::parse($tagihan->tenggat)->translatedFormat('d M Y') : '-' }}</p>
</div>

@php
    $pengajuan = $tagihan->pembayaran->where('status', 'Pending');
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
@endphp



@if($tagihan->unit === 'RW' && $tagihan->via_rt)
    <div class="alert alert-info border-0 small" data-testid="info-via-rt">
        <i class="bi bi-info-circle-fill me-1"></i>
        @if($unit === 'RT')
            Iuran ini dibuat RW dan dikumpulkan lewat RT. Verifikasi pembayaran warga RT Anda di sini, lalu setor seluruh yang terkumpul ke RW lewat menu Setoran ke RW.
        @else
            Iuran ini dikumpulkan lewat RT. Verifikasi pembayaran warga dilakukan oleh Operator Keuangan RT; RW mengonfirmasi setoran dari tiap RT.
        @endif
    </div>
@endif

@if($ringkasanRt)
    @php $tot = $ringkasanRt['total']; @endphp
    <div class="row g-2 mb-3">
        <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3"><div class="small text-muted">KK lunas</div><div class="fw-bold">{{ $tot['lunas'] }} / {{ $tot['total_kk'] }}</div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3"><div class="small text-muted">Terkumpul di RT</div><div class="fw-bold text-nowrap">{{ $rp($tot['terkumpul']) }}</div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3"><div class="small text-muted">Sudah disetor ke RW</div><div class="fw-bold text-success text-nowrap">{{ $rp($tot['disetor']) }}</div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3"><div class="small text-muted">Belum disetor</div><div class="fw-bold text-nowrap {{ $tot['sisa'] > 0 ? 'text-danger' : '' }}">{{ $rp($tot['sisa']) }}</div></div></div></div>
    </div>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="fw-bold small text-uppercase text-muted mb-2" style="letter-spacing:.5px;">Rekap per RT</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 small" data-testid="rekap-tagihan-rt">
                    <thead><tr class="text-muted"><th>RT</th><th class="text-end">KK lunas</th><th class="text-end">Terkumpul</th><th class="text-end">Disetor</th><th class="text-end">Menunggu</th><th class="text-end">Sisa</th></tr></thead>
                    <tbody>
                    @foreach($ringkasanRt['baris'] as $r)
                        <tr>
                            <td class="fw-semibold">RT {{ $r->rt->kode_rt }}</td>
                            <td class="text-end">{{ $r->lunas }} / {{ $r->total_kk }}</td>
                            <td class="text-end">{{ $rp($r->terkumpul) }}</td>
                            <td class="text-end text-success">{{ $rp($r->disetor) }}</td>
                            <td class="text-end text-warning">{{ $r->menunggu > 0 ? $rp($r->menunggu) : '-' }}</td>
                            <td class="text-end {{ $r->sisa > 0 ? 'text-danger fw-semibold' : '' }}">{{ $rp($r->sisa) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="small text-muted mt-2">Hanya angka agregat per RT. Kas RW bertambah saat setoran RT dikonfirmasi di menu Iuran Bulanan.</div>
        </div>
    </div>
@else
<ul class="nav nav-pills gap-2 mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active rounded-pill px-3" data-bs-toggle="pill" data-bs-target="#tabDaftar" type="button"><i class="bi bi-list-check me-1"></i>Daftar &amp; Catat</button></li>
    <li class="nav-item"><button class="nav-link rounded-pill px-3" data-bs-toggle="pill" data-bs-target="#tabVerif" type="button"><i class="bi bi-hourglass-split me-1"></i>Pembayaran Warga @if($pengajuan->count())<span class="badge rounded-pill bg-warning text-dark ms-1">{{ $pengajuan->count() }}</span>@endif</button></li>
</ul>

<div class="tab-content">
    {{-- Tab Daftar & Catat: seluruh KK --}}
    <div class="tab-pane fade show active" id="tabDaftar" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Kepala Keluarga</th>
                                <th>Status</th>
                                <th>Nominal</th>
                                <th>Metode</th>
                                <th>Waktu Bayar</th>
                                <th>Bukti</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tagihan->pembayaran->where('status', '!=', 'Pending') as $bayar)
                                @include('opKeuangan.iuran._baris-pembayaran', ['bayar' => $bayar, 'tagihan' => $tagihan])
                            @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Pembayaran Warga: hanya pengajuan menunggu verifikasi --}}
    <div class="tab-pane fade" id="tabVerif" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Kepala Keluarga</th>
                                <th>Status</th>
                                <th>Nominal</th>
                                <th>Metode</th>
                                <th>Waktu Bayar</th>
                                <th>Bukti</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan as $bayar)
                                @include('opKeuangan.iuran._baris-pembayaran', ['bayar' => $bayar, 'tagihan' => $tagihan])
                            @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada pembayaran dari warga yang menunggu verifikasi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
