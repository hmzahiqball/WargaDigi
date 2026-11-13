@extends('layouts.global')

@section('title', 'Dashboard RW')

@section('content')
<div class="dash-header d-flex justify-content-between align-items-end mb-4">
    <div class="dash-header-title">
        <h2 class="fw-bold text-success mb-1">Ringkasan</h2>
        <p class="text-muted mb-0">Ringkasan Data Aplikasi - {{ \App\Models\SystemSetting::get('instance_name', 'RW') }}</p>
    </div>
    <span class="dash-header-status badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold">
        <i class="bi bi-check-circle me-1"></i> STATUS: AKTIF
    </span>
</div>

@if($pendingUmkmCount > 0)
    <div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 mb-4 rounded-3 gap-3" style="background-color: #ffffff;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(245, 158, 11, 0.2); width: 44px; height: 44px;">
                <i class="bi bi-shop text-warning fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Pemberitahuan UMKM Baru Menunggu Verifikasi</h6>
                <p class="text-muted small mb-0">Terdapat <strong>{{ $pendingUmkmCount }}</strong> usaha warga baru yang telah didaftarkan dan menunggu peninjauan Anda.</p>
            </div>
        </div>
        <a href="{{ route('rw.persetujuan-umkm') }}" class="btn btn-warning btn-sm text-light fw-bold rounded-pill px-3 py-2 text-nowrap shadow-sm">
            <i class="bi bi-clipboard-check me-1"></i> Tinjau UMKM Sekarang
        </a>
    </div>
@endif

@if($stats['dokumen_pending']['need_review'] > 0)
<div class="alert border-0 shadow-sm d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 mb-4 rounded-3 gap-3" style="background-color: #ffffff; border-left: 4px solid #3b82f6 !important;">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(59, 130, 246, 0.2); width: 42px; height: 42px;">
            <i class="bi bi-file-earmark-text text-primary fs-5"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-dark">Terdapat Dokumen Surat yang Perlu Disahkan</h6>
            <p class="text-muted small mb-0">Terdapat <strong>{{ $stats['dokumen_pending']['need_review'] }}</strong> surat keterangan yang sudah disetujui RT dan menunggu pengesahan tanda tangan Anda.</p>
        </div>
    </div>
    <a href="{{ route('rw.persetujuan-dokumen') }}" class="btn btn-primary btn-sm fw-bold rounded-pill px-3 py-2 text-nowrap shadow-sm">
        <i class="bi bi-clipboard-check me-1"></i> Tinjau Dokumen Sekarang
    </a>
</div>
@endif

<div class="row g-3 mb-4 dash-stats">
    @include('rw.components.cardStatDashboard', [
        'title' => 'JUMLAH PENDUDUK',
        'titleClass' => 'text-secondary',
        'icon' => 'bi-people',
        'iconColor' => 'text-info',
        'value' => $stats['penduduk']['total'],
        'subtitle' => $stats['penduduk']['change'],
        'subtitleClass' => $stats['penduduk']['trend'] === 'up' ? 'text-success fw-semibold' : 'text-muted',
        'subtitleIcon' => $stats['penduduk']['trend'] === 'up' ? 'bi-arrow-up-short' : null,
        'link' => route('rw.penduduk.index'),
    ])

    @include('rw.components.cardStatDashboard', [
        'title' => 'DOKUMEN PENDING',
        'titleClass' => 'text-danger',
        'icon' => 'bi-file-earmark-medical',
        'iconColor' => 'text-danger',
        'value' => $stats['dokumen_pending']['total'],
        'subtitle' => $stats['dokumen_pending']['need_review'] > 0
            ? $stats['dokumen_pending']['need_review'] . ' menunggu pengesahan RW'
            : 'Tidak ada yang menunggu RW',
        'subtitleClass' => $stats['dokumen_pending']['need_review'] > 0 ? 'text-danger fw-semibold' : 'text-muted',
        'subtitleIcon' => $stats['dokumen_pending']['need_review'] > 0 ? 'bi-exclamation-triangle' : null,
        'link' => route('rw.persetujuan-dokumen'),
    ])

    @include('rw.components.cardStatDashboard', [
        'title' => 'UMKM BARU',
        'icon' => 'bi-shop',
        'iconColor' => 'text-success',
        'value' => $stats['umkm_baru']['total'],
        'subtitle' => $stats['umkm_baru']['status'],
        'subtitleClass' => ($stats['umkm_baru']['total'] > 0) ? 'text-warning fw-bold' : 'text-muted',
        'subtitleIcon' => ($stats['umkm_baru']['total'] > 0) ? 'bi-exclamation-circle-fill' : null,
        'link' => route('rw.persetujuan-umkm'),
    ])

    @include('rw.components.cardStatDashboard', [
        'title' => 'KONTEN DITINJAU',
        'icon' => 'bi-pencil-square',
        'iconColor' => 'text-success',
        'value' => $stats['konten_ditinjau']['total'],
        'subtitle' => $stats['konten_ditinjau']['status'],
        'subtitleClass' => $stats['konten_ditinjau']['total'] > 0 ? 'text-warning fw-bold' : 'text-muted',
        'link' => $stats['konten_ditinjau']['link'],
    ])
</div>

{{-- Ringkasan kas RW (hanya baca). --}}
<div class="card card-custom p-3 p-md-4 shadow-sm border-0 mb-4" data-testid="ringkasan-kas-rw">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2 text-success"></i>Kas RW</h5>
        <a href="{{ route('laporan-keuangan.publik') }}" class="text-success text-decoration-none small fw-semibold">
            Lihat Laporan Keuangan <i class="bi bi-arrow-right-short"></i>
        </a>
    </div>
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <small class="text-muted d-block">Saldo kas</small>
            <span class="fs-5 fw-bold {{ $keuangan['saldo'] < 0 ? 'text-danger' : 'text-dark' }}">Rp {{ number_format($keuangan['saldo'], 0, ',', '.') }}</span>
        </div>
        <div class="col-6 col-md-4">
            <small class="text-muted d-block">Pemasukan {{ $keuangan['nama_bulan'] }}</small>
            <span class="fw-semibold text-success">Rp {{ number_format($keuangan['pemasukan_bulan'], 0, ',', '.') }}</span>
        </div>
        <div class="col-6 col-md-4">
            <small class="text-muted d-block">Pengeluaran {{ $keuangan['nama_bulan'] }}</small>
            <span class="fw-semibold text-danger">Rp {{ number_format($keuangan['pengeluaran_bulan'], 0, ',', '.') }}</span>
        </div>
    </div>
    @php
        $statusLaporan = [
            'Draft' => ['Draft', 'text-secondary'],
            'Submitted' => ['Diajukan', 'text-warning'],
            'Approved' => ['Disetujui', 'text-success'],
            'Rejected' => ['Ditolak', 'text-danger'],
        ];
        $lap = $keuangan['laporan'];
    @endphp
    <div class="small text-muted mt-3 pt-3 border-top">
        @if($lap)
            Laporan RW terakhir: <span class="fw-semibold text-dark">{{ $lap->judul }}</span>
            <span class="{{ $statusLaporan[$lap->status][1] ?? 'text-secondary' }} fw-semibold">({{ $statusLaporan[$lap->status][0] ?? $lap->status }})</span>
        @else
            Belum ada laporan keuangan RW.
        @endif
        <span class="mx-1">&middot;</span>
        <a href="{{ route('rw.rekap-iuran') }}" class="text-success text-decoration-none fw-semibold">Rekap iuran per RT</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">AKTIVITAS MENUNGGU TINDAKAN</h5>
            </div>

            @if(count($activities) > 0)
                <div class="rw-timeline">
                    @foreach($activities as $activity)
                        @include('rw.components.timelineActivity', ['activity' => $activity])
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-5" data-testid="aktivitas-kosong">
                    <i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>
                    Tidak ada aktivitas yang menunggu tindakan.
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-5 d-flex flex-column gap-4">
        <div class="card card-custom p-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Aksi Cepat</h5>
            </div>

            <div class="row g-3">
                @foreach($quickActions as $action)
                    @include('rw.components.cardQuickAction', ['action' => $action])
                @endforeach
                @include('rw.components.cardQuickAction', ['action' => [
                    'title' => 'Manajemen Penduduk',
                    'icon' => 'bi-people',
                    'link' => route('rw.penduduk.index'),
                ]])
            </div>
        </div>

        <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Konten Terbaru</h5>
                <a href="{{ route('rw.pusat-informasi.index') }}" class="text-success text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>
            <div class="row g-3">
                @forelse($recentDocs as $doc)
                    @include('rw.components.cardRecentDoc', ['doc' => $doc])
                @empty
                    <div class="col-12 text-muted small">Belum ada konten yang terbit.</div>
                @endforelse
            </div>
        </div>
    </div>

    @include('partials._demografi', ['demografiScope' => 'seluruh RW'])
</div>
@endsection
