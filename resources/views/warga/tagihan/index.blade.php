@extends('layouts.global')

@section('title', 'Tagihan & Iuran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Daftar Tagihan Iuran</h2>
        <p class="text-muted mb-0">Kelola dan bayar tagihan iuran keluarga Anda.</p>
    </div>
</div>

<div class="row">
    @forelse($tagihanList as $bayar)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border border-light-subtle shadow-sm rounded-4 overflow-hidden position-relative">
                @if($bayar->status === 'Unpaid')
                    <div class="position-absolute top-0 start-0 w-100 bg-warning" style="height: 4px;"></div>
                @elseif($bayar->status === 'Pending')
                    <div class="position-absolute top-0 start-0 w-100 bg-info" style="height: 4px;"></div>
                @else
                    <div class="position-absolute top-0 start-0 w-100 bg-success" style="height: 4px;"></div>
                @endif
                
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold tracking-wide d-block mb-1">
                                {{ $bayar->tagihan->jenis === 'rutin' ? 'Iuran Bulanan' : 'Tagihan Insidental' }}
                            </span>
                            <h5 class="fw-bold text-dark mb-0 lh-base" style="font-size: 1.1rem;">
                                {{ $bayar->tagihan->judul }}
                            </h5>
                        </div>
                        @if($bayar->status === 'Unpaid')
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill fw-semibold px-3 py-2">Belum Lunas</span>
                        @elseif($bayar->status === 'Pending')
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill fw-semibold px-3 py-2">Menunggu Konfirmasi</span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-semibold px-3 py-2">Lunas</span>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <span class="text-muted small d-block mb-1">Total Tagihan</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $bayar->tagihan->formatted_nominal }}</h3>
                    </div>

                    @if($bayar->status === 'Unpaid')
                        <button class="btn btn-success w-100 fw-semibold py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalBayar{{ $bayar->id }}">
                            Bayar
                        </button>
                    @elseif($bayar->status === 'Pending')
                        <button class="btn btn-light w-100 fw-semibold py-2 rounded-3 text-muted border" disabled>
                            Sedang Diverifikasi
                        </button>
                    @else
                        <a href="{{ route('warga.tagihan.resi', $bayar->id) }}" class="btn btn-outline-success w-100 fw-semibold py-2 rounded-3">
                            <i class="bi bi-download me-1"></i> Download Resi
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Render Modal Bayar -->
        @if($bayar->status === 'Unpaid')
            @include('warga.tagihan.modal-bayar', ['bayar' => $bayar])
        @endif

    @empty
        <div class="col-12 text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-check2-circle text-success fs-1"></i>
            </div>
            <h5 class="fw-bold">Tidak Ada Tagihan</h5>
            <p class="text-muted">Keluarga Anda tidak memiliki tagihan aktif saat ini.</p>
        </div>
    @endforelse
</div>
@endsection
