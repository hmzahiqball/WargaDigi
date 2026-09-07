@extends('layouts.opKeuangan')

@section('title', 'Catat Transaksi')

@section('content')
@php
    $role = Auth::user()?->role ?? '';
    $isOpKeuanganRT = $role === 'Op. Keuangan RT';
    $isOpKeuanganRW = $role === 'Op. Keuangan RW';
    $isKetuaRT = $role === 'Ketua RT';
    $isPimpinanRW = $role === 'Pimpinan RW';
    $isDKM = $role === 'DKM';

    // Op. Keuangan RT bisa menambah transaksi iuran
    $canAddTransaksi = $isOpKeuanganRT;
    // Op. Keuangan RW MUTLAK read-only untuk transaksi iuran RT
    $isReadOnly = $isOpKeuanganRW || $isKetuaRT || $isPimpinanRW || $isDKM;
@endphp

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Validasi gagal:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Catat Transaksi</h2>
        <p class="text-muted mb-0">Menghasilkan dan mengelola laporan keuangan untuk tinjauan administratif.</p>
    </div>
    {{-- Tombol Aksi: hanya untuk Op. Keuangan RT --}}
    @if($canAddTransaksi)
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary px-3 rounded-3 fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#modalBuatTagihan">
                <i class="bi bi-receipt me-1"></i> Buat Tagihan Baru
            </button>
            <button type="button" class="btn btn-success px-3 rounded-3 fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#modalCatatTransaksi">
                <i class="bi bi-plus-lg me-1"></i> Catat Transaksi Baru
            </button>
        </div>
    @endif

    {{-- Op. Keuangan RW: info read-only --}}
    @if($isOpKeuanganRW)
        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fw-semibold">
            <i class="bi bi-eye me-1"></i> Mode Baca — Riwayat Transaksi RT
        </span>
    @endif
</div>

{{-- Filter Bar --}}
<div class="card card-custom p-3 shadow-sm border-0 mb-4">
    <div class="row align-items-end g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold small mb-1">Range Tanggal:</label>
            <div class="d-flex align-items-center gap-2">
                <input type="date" class="form-control form-control-sm rounded-3" id="filterDateFrom">
                <span class="text-muted small">—</span>
                <input type="date" class="form-control form-control-sm rounded-3" id="filterDateTo">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold small mb-1">Status</label>
            <select class="form-select form-select-sm rounded-3" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="Pending">Pending</option>
                <option value="Terverifikasi">Terverifikasi</option>
                <option value="Ditolak">Ditolak</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold small mb-1">Metode</label>
            <select class="form-select form-select-sm rounded-3" id="filterMetode">
                <option value="">Semua Metode</option>
                <option value="Tunai">Tunai</option>
                <option value="Transfer">Transfer</option>
            </select>
        </div>
        <div class="col-md-2 text-end">
            <button class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-funnel me-1"></i> Filters
            </button>
        </div>
    </div>
</div>

{{-- Transaction Table --}}
<div class="card card-custom shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <th class="py-3 px-4 fw-bold border-0">Tanggal</th>
                    <th class="py-3 px-4 fw-bold border-0">Keluarga (KK)</th>
                    <th class="py-3 px-4 fw-bold border-0">RT</th>
                    <th class="py-3 px-4 fw-bold border-0">Periode</th>
                    <th class="py-3 px-4 fw-bold border-0 text-end">Jumlah (IDR)</th>
                    <th class="py-3 px-4 fw-bold border-0 text-center">Metode</th>
                    <th class="py-3 px-4 fw-bold border-0 text-center">Status</th>
                    <th class="py-3 px-4 fw-bold border-0 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $tx)
                    @php
                        $namaBulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                    @endphp
                    <tr class="border-bottom">
                        <td class="py-3 px-4 text-muted small">{{ $tx->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-arrow-down-short text-success fs-5"></i>
                                <div>
                                    <span class="fw-bold small d-block">{{ $tx->keluarga->no_kk ?? '-' }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $tx->keluarga->nik_kepala_keluarga ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 small fw-semibold">{{ $tx->rt->nama_rt ?? '-' }}</td>
                        <td class="py-3 px-4 text-muted small">
                            {{ $namaBulan[$tx->periode_bulan] ?? '' }} {{ $tx->periode_tahun }}
                        </td>
                        <td class="py-3 px-4 text-end fw-semibold small">
                            {{ number_format($tx->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="badge rounded-pill {{ $tx->metode_pembayaran === 'Transfer' ? 'bg-primary bg-opacity-10 text-primary' : 'bg-secondary bg-opacity-10 text-secondary' }} px-2 py-1" style="font-size: 0.7rem;">
                                {{ $tx->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @php
                                $statusClasses = [
                                    'Pending'       => 'bg-warning bg-opacity-10 text-warning',
                                    'Terverifikasi' => 'bg-success bg-opacity-10 text-success',
                                    'Ditolak'       => 'bg-danger bg-opacity-10 text-danger',
                                ];
                                $badgeClass = $statusClasses[$tx->status] ?? 'bg-secondary bg-opacity-10 text-secondary';
                            @endphp
                            <span class="badge rounded-pill px-2 py-1 {{ $badgeClass }} fw-semibold" style="font-size: 0.7rem;">
                                {{ $tx->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($tx->status === 'Terverifikasi')
                                <button type="button" class="btn btn-outline-success btn-sm rounded-3 px-3"
                                        onclick="showDetailTransaksi({
                                            tanggal: '{{ $tx->created_at->format('d M Y') }}',
                                            perihal: 'Iuran {{ $tx->keluarga->no_kk ?? '' }}',
                                            kategori: 'Iuran Warga',
                                            jumlah_formatted: 'Rp {{ number_format($tx->jumlah, 0, ',', '.') }}',
                                            metode: '{{ $tx->metode_pembayaran }}',
                                            operator: '{{ $tx->operator->username ?? '-' }}',
                                            verified_at: '{{ $tx->verified_at ? $tx->verified_at->format('d M Y') : '-' }}',
                                            keterangan: '{{ addslashes($tx->keterangan ?? 'Tidak ada catatan.') }}',
                                            status: '{{ $tx->status }}',
                                            bukti: '{{ $tx->bukti_pembayaran ? asset('storage/' . $tx->bukti_pembayaran) : '' }}'
                                        })">
                                    Cetak Kuitansi
                                </button>
                            @elseif($tx->status === 'Pending' && $isKetuaRT)
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-3 px-3"
                                        onclick="showDetailTransaksi({
                                            tanggal: '{{ $tx->created_at->format('d M Y') }}',
                                            perihal: 'Iuran {{ $tx->keluarga->no_kk ?? '' }}',
                                            kategori: 'Iuran Warga',
                                            jumlah_formatted: 'Rp {{ number_format($tx->jumlah, 0, ',', '.') }}',
                                            metode: '{{ $tx->metode_pembayaran }}',
                                            operator: '{{ $tx->operator->username ?? '-' }}',
                                            verified_at: '-',
                                            keterangan: '{{ addslashes($tx->keterangan ?? 'Tidak ada catatan.') }}',
                                            status: '{{ $tx->status }}',
                                            bukti: ''
                                        })">
                                    Review
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3"
                                        onclick="showDetailTransaksi({
                                            tanggal: '{{ $tx->created_at->format('d M Y') }}',
                                            perihal: 'Iuran {{ $tx->keluarga->no_kk ?? '' }}',
                                            kategori: 'Iuran Warga',
                                            jumlah_formatted: 'Rp {{ number_format($tx->jumlah, 0, ',', '.') }}',
                                            metode: '{{ $tx->metode_pembayaran }}',
                                            operator: '{{ $tx->operator->username ?? '-' }}',
                                            verified_at: '{{ $tx->verified_at ? $tx->verified_at->format('d M Y') : '-' }}',
                                            keterangan: '{{ addslashes($tx->keterangan ?? 'Tidak ada catatan.') }}',
                                            status: '{{ $tx->status }}',
                                            bukti: ''
                                        })">
                                    Lihat Detail
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted opacity-50"></i>
                            <p class="mb-1 fw-semibold">Belum ada transaksi iuran</p>
                            <small>Data transaksi akan muncul di sini setelah dicatat.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (Laravel native) --}}
    @if($transaksi->hasPages())
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
            <span class="text-muted small">
                Showing {{ $transaksi->firstItem() }} to {{ $transaksi->lastItem() }} of {{ $transaksi->total() }} entries
            </span>
            <div>
                {{ $transaksi->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @elseif($transaksi->count() > 0)
        <div class="px-4 py-3 border-top">
            <span class="text-muted small">Menampilkan {{ $transaksi->count() }} data</span>
        </div>
    @endif
</div>

{{-- Include Modals --}}
@if($canAddTransaksi)
    @include('components.modals.catat-transaksi')
    @include('components.modals.buat-tagihan')
@endif
@include('components.modals.detail-transaksi')
@include('components.modals.success-dialog')
@endsection
