@extends('layouts.global')

@section('title', 'Riwayat Pengajuan')

@php
    $badgeStatus = function ($status) {
        return match ($status) {
            'disetujui'   => ['bg-success bg-opacity-10 text-success', 'Disetujui'],
            'ditolak'     => ['bg-danger bg-opacity-10 text-danger', 'Ditolak'],
            'menunggu_rw' => ['bg-info bg-opacity-10 text-info', 'Menunggu RW'],
            default       => ['bg-warning bg-opacity-10 text-warning', 'Menunggu RT'],
        };
    };
@endphp

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Riwayat Pengajuan</h3>
            <p class="text-muted mb-0">Seluruh pengajuan perubahan data keluarga Anda.</p>
        </div>
        <a href="{{ route('warga.keluarga.index') }}" class="btn btn-light border" style="border-radius:0.5rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted small fw-semibold py-3">TANGGAL</th>
                            <th class="text-muted small fw-semibold py-3">JENIS</th>
                            <th class="text-muted small fw-semibold py-3">NAMA TERKAIT</th>
                            <th class="text-muted small fw-semibold py-3">STATUS</th>
                            <th class="text-muted small fw-semibold py-3">KETERANGAN</th>
                            <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        @php [$badgeClass, $badgeLabel] = $badgeStatus($r->status); @endphp
                        <tr>
                            <td class="py-3">{{ $r->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3">{{ $r->labelJenis() }}</td>
                            <td class="py-3">{{ $r->penduduk?->nama_lengkap ?? ($r->data_pengajuan['nama_lengkap'] ?? '-') }}</td>
                            <td class="py-3"><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                            <td class="py-3 text-muted small">{{ $r->keterangan_tolak ?: '-' }}</td>
                            <td class="py-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalRiwayat-{{ $r->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $riwayat->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal detail riwayat (di luar tabel agar HTML valid & modal berfungsi) --}}
@foreach($riwayat as $r)
<div class="modal fade" id="modalRiwayat-{{ $r->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">{{ $r->labelJenis() }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-table me-2 text-success"></i>Perubahan Data</h6>
                @include('partials._mutasi-diff', ['mutasi' => $r])

                <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-clock-history me-2 text-success"></i>Riwayat Proses</h6>
                @include('partials._mutasi-timeline', ['mutasi' => $r])
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
