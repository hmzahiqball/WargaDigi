@extends('layouts.global')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Laporan Keuangan</h2>
        <p class="text-muted mb-0">Lihat rekapitulasi arus kas bulanan dan ekspor data ke PDF.</p>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGenerateLaporan">
            <i class="bi bi-file-earmark-plus me-1"></i> Generate Laporan Baru
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <!-- Filter Bar -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label text-muted small">Tahun</label>
                <select class="form-select bg-light border-0">
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small">Status</label>
                <select class="form-select bg-light border-0">
                    <option value="">Semua Status</option>
                    <option value="Approved">Approved</option>
                    <option value="Pending">Pending</option>
                    <option value="Draft">Draft</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-secondary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-muted small fw-semibold py-3">PERIODE</th>
                        <th class="text-muted small fw-semibold py-3">JUDUL LAPORAN</th>
                        <th class="text-muted small fw-semibold py-3">TOTAL PEMASUKAN</th>
                        <th class="text-muted small fw-semibold py-3">TOTAL PENGELUARAN</th>
                        <th class="text-muted small fw-semibold py-3">SALDO AKHIR</th>
                        <th class="text-muted small fw-semibold py-3 text-center">STATUS</th>
                        <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($laporan as $lap)
                    <tr>
                        <td class="py-3 text-dark fw-medium">
                            {{ date('F', mktime(0, 0, 0, $lap->periode_bulan, 10)) }} {{ $lap->periode_tahun }}
                        </td>
                        <td class="py-3">
                            <div class="text-dark">{{ $lap->judul }}</div>
                            <div class="text-muted small">Dibuat: {{ $lap->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="py-3 text-success fw-medium">+ {{ $lap->formatted_total_pemasukan }}</td>
                        <td class="py-3 text-danger fw-medium">- {{ $lap->formatted_total_pengeluaran }}</td>
                        <td class="py-3 text-primary fw-bold">{{ $lap->formatted_saldo_akhir }}</td>
                        <td class="py-3 text-center">
                            @if($lap->status == 'Approved')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Approved</span>
                            @elseif($lap->status == 'Submitted')
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill"><i class="bi bi-hourglass me-1"></i> Pending Review</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">{{ $lap->status }}</span>
                            @endif
                        </td>
                        <td class="py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-light text-primary" title="Detail"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-light text-danger" title="Download PDF"><i class="bi bi-file-pdf"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada laporan yang di-generate.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Menampilkan {{ $laporan->firstItem() ?? 0 }} - {{ $laporan->lastItem() ?? 0 }} dari {{ $laporan->total() }} laporan</span>
            {{ $laporan->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Generate Laporan -->
<div class="modal fade" id="modalGenerateLaporan" tabindex="-1" aria-labelledby="modalGenerateLaporanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0 mt-2 mx-2">
                <h5 class="modal-title fw-bold" id="modalGenerateLaporanLabel">Generate Laporan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('opkeuangan.laporan.store') }}" method="POST">
                    @csrf
                    
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 d-flex gap-3 align-items-start rounded-3 mb-4">
                        <i class="bi bi-info-circle-fill text-info mt-1"></i>
                        <div>
                            Sistem akan secara otomatis menghitung total pemasukan, pengeluaran, dan saldo akhir berdasarkan transaksi yang telah diverifikasi pada periode yang dipilih.
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bulan</label>
                            <select class="form-select bg-light border-0 py-2" name="bulan" required>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10" selected>Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun</label>
                            <input type="number" class="form-control bg-light border-0 py-2" name="tahun" value="2026" required>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold rounded-3"><i class="bi bi-magic me-2"></i> Generate Laporan</button>
                        <button type="button" class="btn btn-light py-2 fw-semibold rounded-3 text-muted" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
