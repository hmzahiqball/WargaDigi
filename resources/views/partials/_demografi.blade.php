{{--
    Partial grafik demografi (reusable RT & RW).
    Membutuhkan: $demografi (array dari DemografiService::semua).
    Opsional: $demografiScope (string label lingkup, mis. "RT 03" / "RW 21").
--}}
@php
    $scopeLabel = $demografiScope ?? null;
    $adaSebaran = isset($demografi['sebaran_rt']);
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1"><i class="bi bi-bar-chart-line me-2 text-success"></i>Demografi Penduduk</h5>
                <p class="text-muted small mb-0">
                    Berdasarkan {{ number_format($demografi['total'] ?? 0, 0, ',', '.') }} data penduduk
                    @if($scopeLabel) di {{ $scopeLabel }} @endif
                    .
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <p class="small fw-semibold text-muted mb-2">Kelompok Usia</p>
                <div style="position:relative;height:260px;"><canvas id="chartUsia"></canvas></div>
            </div>
            <div class="col-lg-6">
                <p class="small fw-semibold text-muted mb-2">Pendidikan Terakhir</p>
                <div style="position:relative;height:260px;"><canvas id="chartPendidikan"></canvas></div>
            </div>
            <div class="col-lg-12">
                <p class="small fw-semibold text-muted mb-2">Pekerjaan</p>
                <div style="position:relative;height:320px;"><canvas id="chartPekerjaan"></canvas></div>
            </div>
            <div class="col-md-4">
                <p class="small fw-semibold text-muted mb-2">Jenis Kelamin</p>
                <div style="position:relative;height:240px;"><canvas id="chartJk"></canvas></div>
            </div>
            <div class="col-md-4">
                <p class="small fw-semibold text-muted mb-2">Status Perkawinan</p>
                <div style="position:relative;height:240px;"><canvas id="chartKawin"></canvas></div>
            </div>
            <div class="col-md-4">
                <p class="small fw-semibold text-muted mb-2">Status Warga</p>
                <div style="position:relative;height:240px;"><canvas id="chartStatusWarga"></canvas></div>
            </div>
            @if($adaSebaran)
            <div class="col-12">
                <p class="small fw-semibold text-muted mb-2">Sebaran Penduduk per RT</p>
                <div style="position:relative;height:300px;"><canvas id="chartSebaranRt"></canvas></div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const demografi = @json($demografi);
    if (typeof Chart === 'undefined') return;

    // Palet warna kontras (kategorikal) untuk membedakan antar segmen.
    const palet = [
        '#2E7D32', // hijau
        '#1E88E5', // biru
        '#F9A825', // kuning/amber
        '#E53935', // merah
        '#8E24AA', // ungu
        '#00ACC1', // cyan
        '#FB8C00', // oranye
        '#6D4C41', // coklat
        '#546E7A', // abu biru
    ];
    const warna = (n) => Array.from({length: n}, (_, i) => palet[i % palet.length]);

    function buat(id, type, data, opsiTambahan, warnaBar) {
        const el = document.getElementById(id);
        if (!el || !data) return;
        const isBatang = (type === 'bar');
        new Chart(el, {
            type: type,
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah',
                    data: data.values,
                    // Bar: tiap batang beda warna agar kontras. Doughnut: palet. Line: satu warna.
                    backgroundColor: isBatang ? warna(data.labels.length)
                        : (type === 'line' ? (warnaBar || '#1E88E5') : warna(data.labels.length)),
                    borderColor: isBatang ? '#ffffff' : (warnaBar || '#1565C0'),
                    borderWidth: isBatang ? 1 : (type === 'line' ? 2 : 0),
                    fill: false,
                }]
            },
            options: Object.assign({
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: (type === 'doughnut' || type === 'pie') } },
            }, opsiTambahan || {})
        });
    }

    buat('chartUsia', 'bar', demografi.usia, { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } });
    buat('chartPendidikan', 'doughnut', demografi.pendidikan);
    buat('chartPekerjaan', 'bar', demografi.pekerjaan, { indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } });
    buat('chartJk', 'doughnut', demografi.jenis_kelamin);
    buat('chartKawin', 'doughnut', demografi.perkawinan);
    buat('chartStatusWarga', 'doughnut', demografi.status_warga);
    if (demografi.sebaran_rt) {
        buat('chartSebaranRt', 'bar', demografi.sebaran_rt, { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } });
    }
})();
</script>
@endpush
