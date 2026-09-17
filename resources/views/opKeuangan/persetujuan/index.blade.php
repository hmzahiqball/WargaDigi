@extends('layouts.global')

@section('title', 'Persetujuan Keuangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Persetujuan Keuangan</h2>
        <p class="text-muted mb-0">Tinjau dan setujui laporan keuangan bulanan yang diajukan oleh Bendahara.</p>
    </div>
    <div>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fs-6">
            <i class="bi bi-person-badge me-1"></i> {{ $scope['label'] }}
        </span>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Pending Reports --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-hourglass-split text-warning fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">Menunggu Persetujuan</h5>
                <small class="text-muted">{{ $pending->total() }} laporan menunggu ditinjau</small>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-muted small fw-semibold py-3">PERIODE</th>
                        <th class="text-muted small fw-semibold py-3">UNIT</th>
                        <th class="text-muted small fw-semibold py-3">DIBUAT OLEH</th>
                        <th class="text-muted small fw-semibold py-3 text-success">PEMASUKAN</th>
                        <th class="text-muted small fw-semibold py-3 text-danger">PENGELUARAN</th>
                        <th class="text-muted small fw-semibold py-3 text-primary">SALDO AKHIR</th>
                        <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($pending as $lap)
                    <tr>
                        <td class="py-3">
                            <div class="fw-semibold text-dark">{{ date('F', mktime(0,0,0,$lap->periode_bulan,10)) }} {{ $lap->periode_tahun }}</div>
                            <div class="text-muted small">{{ $lap->judul }}</div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ $lap->unit }}{{ $lap->unit === 'RT' && $lap->rt ? ' ' . ($lap->rt->kode_rt ?? '') : '' }}</span>
                        </td>
                        <td class="py-3 text-dark">{{ $lap->pembuat->name ?? '-' }}</td>
                        <td class="py-3 text-success fw-medium">{{ $lap->formatted_total_pemasukan }}</td>
                        <td class="py-3 text-danger fw-medium">- {{ $lap->formatted_total_pengeluaran }}</td>
                        <td class="py-3 text-primary fw-bold">{{ $lap->formatted_saldo_akhir }}</td>
                        <td class="py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('laporan-keuangan.pdf', $lap->id) }}" class="btn btn-sm btn-light border" title="Lihat PDF" target="_blank">
                                    <i class="bi bi-file-pdf text-danger"></i>
                                </a>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $lap->id }}" title="Setujui">
                                    <i class="bi bi-check-lg me-1"></i> Setujui
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalReject{{ $lap->id }}" title="Tolak">
                                    <i class="bi bi-x-lg me-1"></i> Tolak
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Approve --}}
                    <div class="modal fade" id="modalApprove{{ $lap->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow">
                                <div class="modal-header border-bottom-0 pb-0 mt-2 mx-2">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-check-circle text-success me-2"></i>Setujui Laporan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="alert alert-success border-0 bg-success bg-opacity-10 rounded-3 mb-3">
                                        <strong>{{ $lap->judul }}</strong><br>
                                        <small>Periode: {{ date('F', mktime(0,0,0,$lap->periode_bulan,10)) }} {{ $lap->periode_tahun }}</small>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-4 text-center">
                                            <div class="text-muted small">Pemasukan</div>
                                            <div class="fw-bold text-success">{{ $lap->formatted_total_pemasukan }}</div>
                                        </div>
                                        <div class="col-4 text-center">
                                            <div class="text-muted small">Pengeluaran</div>
                                            <div class="fw-bold text-danger">{{ $lap->formatted_total_pengeluaran }}</div>
                                        </div>
                                        <div class="col-4 text-center">
                                            <div class="text-muted small">Saldo Akhir</div>
                                            <div class="fw-bold text-primary">{{ $lap->formatted_saldo_akhir }}</div>
                                        </div>
                                    </div>
                                    <form action="{{ route($routePrefix . '.approve', $lap->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Catatan (opsional)</label>
                                            <textarea name="catatan" class="form-control bg-light border-0" rows="2" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-success py-2 fw-semibold rounded-3">
                                                <i class="bi bi-check-circle me-2"></i>Ya, Setujui Laporan
                                            </button>
                                            <button type="button" class="btn btn-light py-2 fw-semibold rounded-3 text-muted" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Reject --}}
                    <div class="modal fade" id="modalReject{{ $lap->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow">
                                <div class="modal-header border-bottom-0 pb-0 mt-2 mx-2">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle text-danger me-2"></i>Tolak Laporan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 rounded-3 mb-3">
                                        <strong>{{ $lap->judul }}</strong><br>
                                        <small>Periode: {{ date('F', mktime(0,0,0,$lap->periode_bulan,10)) }} {{ $lap->periode_tahun }}</small>
                                    </div>
                                    <form action="{{ route($routePrefix . '.reject', $lap->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                            <textarea name="catatan" class="form-control bg-light border-0" rows="3" placeholder="Jelaskan alasan penolakan laporan ini..." required></textarea>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-danger py-2 fw-semibold rounded-3">
                                                <i class="bi bi-x-circle me-2"></i>Ya, Tolak Laporan
                                            </button>
                                            <button type="button" class="btn btn-light py-2 fw-semibold rounded-3 text-muted" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-50"></i>
                                Semua laporan sudah ditinjau. Tidak ada yang menunggu persetujuan.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pending->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Menampilkan {{ $pending->firstItem() ?? 0 }} - {{ $pending->lastItem() ?? 0 }} dari {{ $pending->total() }} laporan</span>
            {{ $pending->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

{{-- History --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-clock-history text-secondary fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">Riwayat Persetujuan</h5>
                <small class="text-muted">Laporan yang sudah diproses</small>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-muted small fw-semibold py-3">PERIODE</th>
                        <th class="text-muted small fw-semibold py-3">UNIT</th>
                        <th class="text-muted small fw-semibold py-3">DIBUAT OLEH</th>
                        <th class="text-muted small fw-semibold py-3">STATUS</th>
                        <th class="text-muted small fw-semibold py-3">DIPROSES PADA</th>
                        <th class="text-muted small fw-semibold py-3">CATATAN</th>
                        <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($history as $lap)
                    <tr>
                        <td class="py-3">
                            <div class="fw-semibold text-dark">{{ date('F', mktime(0,0,0,$lap->periode_bulan,10)) }} {{ $lap->periode_tahun }}</div>
                            <div class="text-muted small">{{ $lap->judul }}</div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ $lap->unit }}{{ $lap->unit === 'RT' && $lap->rt ? ' ' . ($lap->rt->kode_rt ?? '') : '' }}</span>
                        </td>
                        <td class="py-3 text-dark">{{ $lap->pembuat->name ?? '-' }}</td>
                        <td class="py-3 text-center">
                            @if($lap->status == 'Approved')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                            @endif
                        </td>
                        <td class="py-3 text-muted">{{ $lap->tanggal_disetujui ? $lap->tanggal_disetujui->format('d M Y H:i') : '-' }}</td>
                        <td class="py-3">
                            <span class="text-muted small text-truncate d-inline-block" style="max-width: 200px;" title="{{ $lap->catatan }}">{{ $lap->catatan ?? '-' }}</span>
                        </td>
                        <td class="py-3 text-end">
                            <a href="{{ route('laporan-keuangan.pdf', $lap->id) }}" class="btn btn-sm btn-light border" title="Lihat PDF" target="_blank">
                                <i class="bi bi-file-pdf text-danger"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat persetujuan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Menampilkan {{ $history->firstItem() ?? 0 }} - {{ $history->lastItem() ?? 0 }} dari {{ $history->total() }} laporan</span>
            {{ $history->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
