@extends('layouts.global')

@section('title', 'Dashboard Keuangan DKM')

@php
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $r = $d['ringkasan'];
    $t = $d['tindakan'];
    $adaTindakan = $t['pembayaran_menunggu'] > 0 || $t['pengeluaran_menunggu'] > 0
        || $t['laporan_lalu_belum'] || $t['laporan_ditolak'] || $t['laporan_draft'] > 0;
@endphp

@section('content')
<div class="mb-3">
    <h2 class="fw-bold text-dark mb-1 fs-4">Dashboard Keuangan DKM</h2>
    <p class="text-muted mb-0 small">Kas DKM, iuran DKM, dan pekerjaan yang menunggu Anda. Angka bersumber dari data keuangan DKM.</p>
</div>

{{-- 1. Perlu tindakan + laporan terakhir --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100" data-testid="perlu-tindakan">
            <div class="card-body p-3">
                <div class="fw-bold small text-uppercase text-muted mb-2" style="letter-spacing:.5px;">Perlu Tindakan</div>
                @if(! $adaTindakan)
                    <div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Tidak ada pekerjaan yang tertunda.</div>
                @else
                    <div class="list-group list-group-flush">
                        @if($t['pembayaran_menunggu'] > 0)
                            <a href="{{ route('opkeuangan.iuran.index') }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center gap-2" data-testid="tindakan-pembayaran-menunggu">
                                <span class="small"><i class="bi bi-hourglass-split text-warning me-2"></i>Pembayaran iuran DKM menunggu verifikasi</span>
                                <span class="badge rounded-pill bg-warning text-dark">{{ $t['pembayaran_menunggu'] }}</span>
                            </a>
                        @endif
                        @if($t['pengeluaran_menunggu'] > 0)
                            <a href="{{ route('opkeuangan.transaksi.index') }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center gap-2">
                                <span class="small"><i class="bi bi-receipt text-warning me-2"></i>Pengeluaran menunggu persetujuan</span>
                                <span class="small fw-semibold text-nowrap">{{ $rp($t['pengeluaran_menunggu']) }}</span>
                            </a>
                        @endif
                        @if($t['laporan_lalu_belum'])
                            <a href="{{ route('opkeuangan.laporan.index') }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center gap-2">
                                <span class="small"><i class="bi bi-file-earmark-text text-primary me-2"></i>Laporan {{ $t['laporan_lalu_label'] }} belum dibuat</span>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        @endif
                        @if($t['laporan_ditolak'])
                            <a href="{{ route('opkeuangan.laporan.index') }}" class="list-group-item list-group-item-action px-0">
                                <div class="small"><i class="bi bi-x-circle-fill text-danger me-2"></i>Laporan ditolak, perlu revisi</div>
                                @if($t['laporan_ditolak']->catatan)<div class="text-muted ms-4" style="font-size:.75rem;">{{ $t['laporan_ditolak']->catatan }}</div>@endif
                            </a>
                        @endif
                        @if($t['laporan_draft'] > 0)
                            <a href="{{ route('opkeuangan.laporan.index') }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center gap-2">
                                <span class="small"><i class="bi bi-send text-secondary me-2"></i>Laporan draft belum diajukan</span>
                                <span class="badge rounded-pill bg-secondary">{{ $t['laporan_draft'] }}</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" data-testid="status-laporan">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small text-uppercase text-muted" style="letter-spacing:.5px;">Laporan Terakhir</span>
                    <a href="{{ route('opkeuangan.laporan.index') }}" class="small text-success text-decoration-none fw-semibold">Kelola</a>
                </div>
                @if($d['laporan'])
                    @php
                        $lap = $d['laporan'];
                        [$lblLap, $wrnLap] = ['Draft' => ['Draft, belum diajukan', 'secondary'], 'Submitted' => ['Menunggu persetujuan', 'warning'], 'Approved' => ['Disetujui', 'success'], 'Rejected' => ['Ditolak', 'danger']][$lap->status] ?? [$lap->status, 'secondary'];
                    @endphp
                    <div class="fw-semibold small">{{ \App\Services\IuranService::BULAN[$lap->periode_bulan] ?? $lap->periode_bulan }} {{ $lap->periode_tahun }}</div>
                    <span class="badge bg-{{ $wrnLap }} bg-opacity-10 text-{{ $wrnLap }} mt-1">{{ $lblLap }}</span>
                    @if($lap->status === 'Approved')
                        <div class="small text-muted mt-2">{{ $lap->is_published ? 'Sudah dipublikasikan ke warga.' : 'Belum dipublikasikan ke warga.' }}</div>
                    @endif
                    @if($lap->status === 'Rejected' && $lap->catatan)
                        <div class="small text-danger mt-2">Catatan: {{ $lap->catatan }}</div>
                    @endif
                @else
                    <p class="text-muted small mb-0">Belum ada laporan. Buat laporan bulanan dari menu Laporan.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- 2. Ringkasan kas DKM --}}
<div class="row g-2 g-lg-3 mb-3">
    <div class="col-12 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
            <div class="small text-muted">Saldo Kas DKM</div>
            <div class="fs-3 fw-bold text-break {{ $r['saldo'] < 0 ? 'text-danger' : 'text-dark' }}" data-testid="saldo-kas-dkm">{{ $rp($r['saldo']) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
            <div class="small text-muted">Pemasukan {{ $d['nama_bulan'] }}</div>
            <div class="fw-bold text-success text-nowrap">{{ $rp($r['pemasukan_bulan']) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
            <div class="small text-muted">Pengeluaran {{ $d['nama_bulan'] }}</div>
            <div class="fw-bold text-danger text-nowrap">{{ $rp($r['pengeluaran_bulan']) }}</div>
        </div></div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
            <div class="small text-muted">Selisih bulan ini</div>
            <div class="fw-bold text-nowrap {{ $r['selisih_bulan'] < 0 ? 'text-danger' : 'text-dark' }}">{{ ($r['selisih_bulan'] > 0 ? '+' : '') . $rp($r['selisih_bulan']) }}</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    {{-- 3. Grafik --}}
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3">
                <div class="fw-bold small text-uppercase text-muted mb-2" style="letter-spacing:.5px;">Pemasukan vs Pengeluaran Kas DKM (6 bulan)</div>
                <div style="position:relative;height:220px;">
                    <canvas id="chartKasDkm" role="img" aria-label="Grafik pemasukan dan pengeluaran kas DKM enam bulan terakhir"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Iuran DKM aktif --}}
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100" data-testid="tagihan-aktif">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small text-uppercase text-muted" style="letter-spacing:.5px;">Iuran DKM Aktif</span>
                    <a href="{{ route('opkeuangan.iuran.index') }}" class="small text-success text-decoration-none fw-semibold">Kelola</a>
                </div>
                @forelse($d['tagihan'] as $tg)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small gap-2">
                            <span class="fw-semibold text-break">{{ $tg->judul }}</span>
                            <span class="text-muted text-nowrap">{{ $tg->lunas }}/{{ $tg->total }} KK</span>
                        </div>
                        <div class="progress my-1" style="height:6px;" role="progressbar" aria-valuenow="{{ $tg->persen }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-primary" style="width: {{ $tg->persen }}%"></div>
                        </div>
                        <div class="text-muted" style="font-size:.75rem;">
                            Terkumpul {{ $rp($tg->terkumpul) }}@if($tg->tenggat) &bull; tenggat {{ $tg->tenggat }}@endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Tidak ada iuran DKM yang aktif.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 5. Rincian per kategori --}}
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" data-testid="rincian-kategori">
            <div class="card-body p-3">
                <div class="fw-bold small text-uppercase text-muted mb-2" style="letter-spacing:.5px;">Pemasukan per Kategori ({{ $d['nama_bulan'] }})</div>
                @forelse($d['masuk'] as $k)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small gap-2"><span class="text-break">{{ $k->nama }}</span><span class="text-nowrap fw-semibold text-success">{{ $rp($k->total) }}</span></div>
                        <div class="progress" style="height:5px;"><div class="progress-bar bg-success" style="width: {{ $k->persen }}%"></div></div>
                    </div>
                @empty
                    <p class="text-muted small">Belum ada pemasukan bulan ini.</p>
                @endforelse

                <div class="fw-bold small text-uppercase text-muted mt-3 mb-2" style="letter-spacing:.5px;">Pengeluaran per Kategori ({{ $d['nama_bulan'] }})</div>
                @forelse($d['keluar'] as $k)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small gap-2"><span class="text-break">{{ $k->nama }}</span><span class="text-nowrap fw-semibold text-danger">{{ $rp($k->total) }}</span></div>
                        <div class="progress" style="height:5px;"><div class="progress-bar bg-danger" style="width: {{ $k->persen }}%"></div></div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Belum ada pengeluaran bulan ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 6. Transaksi terbaru --}}
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small text-uppercase text-muted" style="letter-spacing:.5px;">Transaksi Terbaru</span>
                    <a href="{{ route('opkeuangan.transaksi.index') }}" class="small text-success text-decoration-none fw-semibold">Lihat semua</a>
                </div>
                @forelse($d['terbaru'] as $tx)
                    <div class="d-flex justify-content-between align-items-center gap-2 py-2 border-bottom">
                        <div>
                            <div class="small fw-semibold text-break">{{ $tx->judul }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $tx->tanggal }}@if($tx->label) &bull; <span class="badge bg-success bg-opacity-10 text-success">{{ $tx->label }}</span>@endif</div>
                        </div>
                        <div class="small fw-bold text-nowrap {{ $tx->pemasukan ? 'text-success' : 'text-danger' }}">{{ $tx->pemasukan ? '+' : '-' }} {{ $rp($tx->jumlah) }}</div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('chartKasDkm');
    if (!el || !window.Chart) return;
    const fmt = v => 'Rp ' + Number(v).toLocaleString('id-ID');
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json($d['grafik']['labels']),
            datasets: [
                { label: 'Pemasukan', data: @json($d['grafik']['masuk']), backgroundColor: '#198754', borderRadius: 4 },
                { label: 'Pengeluaran', data: @json($d['grafik']['keluar']), backgroundColor: '#dc3545', borderRadius: 4 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { callback: fmt } } },
            plugins: { tooltip: { callbacks: { label: c => c.dataset.label + ': ' + fmt(c.parsed.y) } } }
        }
    });
});
</script>
@endpush
