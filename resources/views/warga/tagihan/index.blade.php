@extends('layouts.global')

@section('title', 'Iuran Lainnya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Iuran Lainnya</h2>
        <p class="text-muted mb-0">Iuran di luar iuran bulanan wajib. Bayar dan unggah bukti di sini.</p>
    </div>
</div>



@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@php
    $labelStatus = [
        'Unpaid'  => ['Belum Lunas', 'danger'],
        'Pending' => ['Menunggu Konfirmasi', 'info'],
        'Paid'    => ['Lunas', 'success'],
    ];
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
@endphp



@if($tagihanList->isEmpty())
    <div class="text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
            <i class="bi bi-check2-circle text-success fs-1"></i>
        </div>
        <h5 class="fw-bold">Tidak Ada Tagihan</h5>
        <p class="text-muted">Keluarga Anda tidak memiliki tagihan aktif saat ini.</p>
    </div>
@else
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">

        {{-- Tabel (tablet dan desktop) --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0" data-testid="tabel-iuran-lainnya">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Iuran</th>
                        <th>Tenggat</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tagihanList as $bayar)
                    @php [$lbl, $wrn] = $labelStatus[$bayar->status] ?? [$bayar->status, 'secondary']; @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block">{{ $bayar->tagihan->judul }}</span>
                            @if($bayar->tagihan->unit === 'RW' && $bayar->tagihan->via_rt)
                                <span class="badge bg-primary bg-opacity-10 text-primary" data-testid="badge-dibayar-lewat-rt">Dari RW, dibayar lewat RT</span>
                            @endif
                            @if($bayar->status === 'Unpaid' && $bayar->catatan)
                                <div class="small text-danger mt-1"><i class="bi bi-x-circle-fill me-1"></i>Ditolak: {{ \Illuminate\Support\Str::after($bayar->catatan, 'Ditolak: ') }}. Silakan bayar ulang.</div>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $bayar->tagihan->tenggat ? \Carbon\Carbon::parse($bayar->tagihan->tenggat)->translatedFormat('d M Y') : '-' }}</td>
                        <td class="text-nowrap">
                            @if($bayar->status !== 'Unpaid' && $bayar->nominal)
                                <span class="fw-semibold">{{ $rp($bayar->nominal) }}</span>
                            @else
                                <span class="fw-semibold">{{ $bayar->tagihan->formatted_nominal }}</span>
                                <span class="d-block text-muted small">{{ $bayar->tagihan->nominal_fix ? 'Nominal pasti' : 'Minimal, boleh lebih' }}</span>
                            @endif
                        </td>
                        <td><span class="badge bg-{{ $wrn }} bg-opacity-10 text-{{ $wrn }} rounded-pill fw-semibold px-3 py-2">{{ $lbl }}</span></td>
                        <td class="text-end pe-4">
                            @if($bayar->status === 'Unpaid')
                                <button class="btn btn-success btn-sm fw-semibold px-3 rounded-3" data-bs-toggle="modal" data-bs-target="#modalBayar{{ $bayar->id }}">Bayar</button>
                            @elseif($bayar->status === 'Pending')
                                <span class="small text-muted">Sedang diverifikasi</span>
                            @else
                                {{-- Tombol Download Resi disembunyikan sementara. --}}
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Daftar (mobile) --}}
        <div class="d-md-none" data-testid="daftar-iuran-lainnya">
            @foreach($tagihanList as $bayar)
                @php [$lbl, $wrn] = $labelStatus[$bayar->status] ?? [$bayar->status, 'secondary']; @endphp
                <div class="p-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="min-w-0">
                            <div class="fw-bold text-dark text-break">{{ $bayar->tagihan->judul }}</div>
                            @if($bayar->tagihan->unit === 'RW' && $bayar->tagihan->via_rt)
                                <span class="badge bg-primary bg-opacity-10 text-primary">Dari RW, dibayar lewat RT</span>
                            @endif
                        </div>
                        <span class="badge bg-{{ $wrn }} bg-opacity-10 text-{{ $wrn }} rounded-pill fw-semibold px-2 py-1 flex-shrink-0">{{ $lbl }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end gap-2 mt-2">
                        <div class="small">
                            <div class="fw-semibold">
                                @if($bayar->status !== 'Unpaid' && $bayar->nominal)
                                    {{ $rp($bayar->nominal) }}
                                @else
                                    {{ $bayar->tagihan->formatted_nominal }} <span class="text-muted fw-normal">{{ $bayar->tagihan->nominal_fix ? '(pasti)' : '(minimal)' }}</span>
                                @endif
                            </div>
                            @if($bayar->tagihan->tenggat)
                                <div class="text-muted"><i class="bi bi-calendar3 me-1"></i>Tenggat {{ \Carbon\Carbon::parse($bayar->tagihan->tenggat)->translatedFormat('d M Y') }}</div>
                            @endif
                        </div>
                        @if($bayar->status === 'Unpaid')
                            <button class="btn btn-success btn-sm fw-semibold px-3 rounded-3 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalBayar{{ $bayar->id }}">Bayar</button>
                        @elseif($bayar->status === 'Pending')
                            <span class="small text-muted flex-shrink-0">Sedang diverifikasi</span>
                        @endif
                    </div>
                    @if($bayar->status === 'Unpaid' && $bayar->catatan)
                        <div class="small text-danger mt-2"><i class="bi bi-x-circle-fill me-1"></i>Ditolak: {{ \Illuminate\Support\Str::after($bayar->catatan, 'Ditolak: ') }}. Silakan bayar ulang.</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if($tagihanList->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $tagihanList->links() }}</div>
    @endif
</div>

{{-- Modal bayar untuk tagihan yang belum dibayar pada halaman ini --}}
@foreach($tagihanList as $bayar)
    @if($bayar->status === 'Unpaid')
        @include('warga.tagihan.modal-bayar', ['bayar' => $bayar])
    @endif
@endforeach
@endif
@endsection
