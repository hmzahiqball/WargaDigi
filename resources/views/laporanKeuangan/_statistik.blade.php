@php
    $s = $statistik;
    $r = $s['ringkasan'];
    $p = $s['penilaian'];
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $adaData = $s['n_bulan'] > 0;
    $warnaLevel = ['baik' => 'success', 'waspada' => 'warning', 'kritis' => 'danger', 'kosong' => 'secondary'];
    $labelLevel = ['baik' => 'Kondisi baik', 'waspada' => 'Perlu diperhatikan', 'kritis' => 'Perlu tindakan', 'kosong' => 'Belum ada data'];
    $ikonLevel = ['baik' => 'bi-check-circle-fill', 'waspada' => 'bi-exclamation-triangle-fill', 'kritis' => 'bi-x-octagon-fill', 'kosong' => 'bi-info-circle-fill'];
    $warna = $warnaLevel[$p['level']];
    $unitLabel = ['RT' => 'RT', 'RW' => 'RW', 'DKM' => 'DKM'];
    $iuran = $s['iuran'];
    $tata = $s['tata_kelola'];
    $urlStat = fn (array $ubah = []) => route('laporan-keuangan.publik', array_filter(array_merge([
        'tab' => 'statistik', 'unit' => $unit, 'tahun' => $tahun, 'rt' => $rtTerpilih,
    ], $ubah), fn ($v) => $v !== null && $v !== ''));
@endphp

{{-- Pemilih unit, RT, dan tahun --}}
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3 d-flex flex-column flex-lg-row gap-3 justify-content-between">
        <ul class="nav nav-pills gap-1 flex-nowrap" data-testid="pemilih-unit-statistik">
            @foreach($unitLabel as $kode => $label)
                <li class="nav-item text-center">
                    <a class="nav-link text-nowrap px-3 {{ $unit === $kode ? 'active' : '' }}" href="{{ $urlStat(['unit' => $kode, 'tahun' => null, 'rt' => $kode === 'RT' ? $rtTerpilih : null]) }}">{{ $label }}</a>
                </li>
            @endforeach
        </ul>

        <form method="GET" action="{{ route('laporan-keuangan.publik') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="statistik">
            <input type="hidden" name="unit" value="{{ $unit }}">
            @if($pimpinanRw && $unit === 'RT')
                <label for="statRt" class="small text-muted mb-0">RT</label>
                <select id="statRt" name="rt" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="">Semua RT</option>
                    @foreach($daftarRt as $rt)
                        <option value="{{ $rt->id }}" @selected($rtTerpilih === $rt->id)>RT {{ $rt->kode_rt }}</option>
                    @endforeach
                </select>
            @endif
            <label for="statTahun" class="small text-muted mb-0">Tahun</label>
            <select id="statTahun" name="tahun" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" @selected($t == $tahun)>{{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Penilaian ringkas --}}
<div class="alert alert-{{ $warna }} border-0 shadow-sm rounded-4 d-flex gap-3 align-items-start mb-3" role="status" data-testid="penilaian-statistik">
    <i class="bi {{ $ikonLevel[$p['level']] }} fs-4 flex-shrink-0"></i>
    <div>
        <div class="fw-bold mb-1">{{ $labelLevel[$p['level']] }}</div>
        <div class="small">{{ implode(' ', $p['kalimat']) }}</div>
        @if($hanyaTerbit)
            <div class="small opacity-75 mt-1">Angka unit ini dihitung dari laporan yang sudah dipublikasikan.</div>
        @elseif($unit === 'RT' && $pimpinanRw && ! $rtTerpilih)
            <div class="small opacity-75 mt-1">Gabungan laporan seluruh RT yang sudah disetujui.</div>
        @endif
    </div>
</div>

@if($adaData)
    {{-- Kartu kunci --}}
    <div class="row g-2 g-lg-3 mb-3" data-testid="kartu-statistik">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
                <div class="small text-muted">Saldo kas</div>
                <div class="fs-5 fw-bold text-break {{ $r['saldo'] < 0 ? 'text-danger' : 'text-dark' }}">{{ $rp($r['saldo']) }}</div>
                @if($r['perubahan_saldo'] !== null)
                    <div class="small {{ $r['perubahan_saldo'] < 0 ? 'text-danger' : 'text-success' }}">
                        <i class="bi {{ $r['perubahan_saldo'] < 0 ? 'bi-arrow-down-right' : 'bi-arrow-up-right' }}"></i>
                        {{ $rp(abs($r['perubahan_saldo'])) }} arus bersih sejak awal tahun
                    </div>
                @endif
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
                <div class="small text-muted">Cadangan kas</div>
                @if($r['cadangan'] !== null)
                    <div class="fs-5 fw-bold {{ $r['cadangan'] < 1 ? 'text-danger' : ($r['cadangan'] < 3 ? 'text-warning' : 'text-dark') }}">{{ number_format($r['cadangan'], 1, ',', '.') }} bulan</div>
                    <div class="small text-muted">rata-rata keluar {{ $rp($r['rata_keluar']) }}/bulan</div>
                @else
                    <div class="fs-5 fw-bold text-muted">-</div>
                    <div class="small text-muted">belum ada pengeluaran</div>
                @endif
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
                <div class="small text-muted">Pengeluaran vs pemasukan</div>
                @if($r['rasio'] !== null)
                    <div class="fs-5 fw-bold {{ $r['rasio'] > 100 ? 'text-danger' : 'text-dark' }}">{{ $r['rasio'] }}%</div>
                    <div class="small {{ $r['selisih'] < 0 ? 'text-danger' : 'text-success' }}">{{ $r['selisih'] < 0 ? 'defisit' : 'surplus' }} {{ $rp(abs($r['selisih'])) }}</div>
                @else
                    <div class="fs-5 fw-bold text-muted">-</div>
                    <div class="small text-muted">belum ada pemasukan</div>
                @endif
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
                <div class="small text-muted">Pengumpulan iuran wajib</div>
                @if($iuran && $iuran['total']['tingkat'] !== null)
                    <div class="fs-5 fw-bold {{ $iuran['total']['tingkat'] < 60 ? 'text-danger' : 'text-dark' }}">{{ $iuran['total']['tingkat'] }}%</div>
                    <div class="small text-muted">KK lunas s.d. {{ $iuran['sampai_nama'] }}</div>
                @else
                    <div class="fs-5 fw-bold text-muted">-</div>
                    <div class="small text-muted">{{ $unit === 'DKM' ? 'tidak berlaku untuk DKM' : 'tidak tersedia' }}</div>
                @endif
            </div></div>
        </div>
    </div>

    {{-- Perlu perhatian --}}
    @if(count($s['perhatian']) > 0)
        <div class="card border-0 shadow-sm rounded-4 mb-3" data-testid="perlu-perhatian">
            <div class="card-body p-3">
                <div class="fw-bold small text-uppercase text-muted mb-2" style="letter-spacing:.5px;">Perlu perhatian</div>
                <ul class="list-unstyled mb-0">
                    @foreach($s['perhatian'] as $it)
                        @php [$ikon, $wrn] = ['danger' => ['bi-x-octagon-fill', 'danger'], 'warning' => ['bi-exclamation-triangle-fill', 'warning'], 'info' => ['bi-info-circle-fill', 'primary']][$it['level']]; @endphp
                        <li class="d-flex gap-2 py-1 small"><i class="bi {{ $ikon }} text-{{ $wrn }} flex-shrink-0 mt-1"></i><span>{{ $it['teks'] }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Tren --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-1">
                <h6 class="fw-bold mb-0">Tren Kas {{ $tahun }}</h6>
                <span class="small text-muted">batang: pemasukan dan pengeluaran, garis: saldo akhir</span>
            </div>
            <div style="position:relative;height:260px;">
                <canvas id="chartStatTren" role="img" aria-label="Grafik pemasukan, pengeluaran, dan saldo akhir per bulan"></canvas>
            </div>
            @if($s['berjalan'])
                <div class="small text-muted mt-2" data-testid="bulan-berjalan">
                    <i class="bi bi-hourglass-split me-1"></i>{{ $s['berjalan']['nama'] }} belum final (laporan belum disetujui):
                    masuk {{ $rp($s['berjalan']['pemasukan']) }}, keluar {{ $rp($s['berjalan']['pengeluaran']) }}. Belum dihitung pada angka di atas.
                </div>
            @endif
        </div>
    </div>

    {{-- Komposisi --}}
    <div class="row g-3 mb-3">
        @foreach([['masuk', 'Sumber pemasukan', 'success'], ['keluar', 'Penggunaan dana', 'danger']] as [$kunci, $judul, $wrn])
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3">{{ $judul }}</h6>
                        @forelse($s['kategori'][$kunci] as $k)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small gap-2">
                                    <span class="text-break">{{ $k['nama'] }} <span class="text-muted">({{ $k['persen'] }}%)</span></span>
                                    <span class="fw-semibold text-{{ $wrn }} text-nowrap">{{ $rp($k['total']) }}</span>
                                </div>
                                <div class="progress" style="height:6px;" role="progressbar" aria-valuenow="{{ $k['persen'] }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-{{ $wrn }}" style="width: {{ $k['persen'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">Belum ada data.</p>
                        @endforelse
                        @if($kunci === 'keluar' && $s['kategori']['perubahan'])
                            @php $pb = $s['kategori']['perubahan']; @endphp
                            <div class="small text-muted mt-3 pt-2 border-top" data-testid="perubahan-kategori">
                                Perubahan terbesar: <span class="fw-semibold text-dark">{{ $pb['nama'] }}</span>
                                {{ $pb['selisih'] > 0 ? 'naik' : 'turun' }} {{ $rp(abs($pb['selisih'])) }}
                                ({{ $pb['pembanding'] }}: {{ $rp($pb['sebelumnya']) }}, {{ $pb['bulan'] }}: {{ $rp($pb['sekarang']) }}).
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Kesehatan iuran dan setoran --}}
@if($iuran)
    <div class="card border-0 shadow-sm rounded-4 mb-3" data-testid="kesehatan-iuran">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-1">Iuran wajib dan setoran ke RW</h6>
            <p class="small text-muted mb-3">Kepatuhan KK sampai {{ $iuran['sampai_nama'] ?? 'awal tahun' }}. Tunggakan dihitung sampai bulan lalu.</p>

            @php $t = $iuran['total']; $totalKk = $t['tepat'] + $t['tunggak1'] + $t['tunggak2']; @endphp
            @if($totalKk > 0)
                <div class="d-flex justify-content-between small mb-1">
                    <span>Tepat waktu <strong>{{ $t['tepat'] }}</strong> KK</span>
                    <span>Menunggak 1 bulan <strong>{{ $t['tunggak1'] }}</strong></span>
                    <span>Menunggak 2 bulan atau lebih <strong class="{{ $t['tunggak2'] > 0 ? 'text-danger' : '' }}">{{ $t['tunggak2'] }}</strong></span>
                </div>
                <div class="progress mb-3" style="height:10px;" role="img" aria-label="Komposisi kepatuhan iuran KK">
                    <div class="progress-bar bg-success" style="width: {{ $t['tepat'] / $totalKk * 100 }}%"></div>
                    <div class="progress-bar bg-warning" style="width: {{ $t['tunggak1'] / $totalKk * 100 }}%"></div>
                    <div class="progress-bar bg-danger" style="width: {{ $t['tunggak2'] / $totalKk * 100 }}%"></div>
                </div>
            @else
                <p class="text-muted small">Iuran wajib belum ditetapkan untuk tahun {{ $tahun }}.</p>
            @endif

            <div class="row g-2 mb-3 small">
                <div class="col-6 col-md-3"><div class="text-muted">Terkumpul</div><div class="fw-bold">{{ $rp($t['terkumpul']) }}</div></div>
                <div class="col-6 col-md-3"><div class="text-muted">Kewajiban setoran</div><div class="fw-bold">{{ $rp($t['kewajiban']) }}</div></div>
                <div class="col-6 col-md-3"><div class="text-muted">Sudah disetor</div><div class="fw-bold text-success">{{ $rp($t['disetor']) }}@if($t['menunggu'] > 0) <span class="text-warning fw-normal">(+{{ $rp($t['menunggu']) }} menunggu)</span>@endif</div></div>
                <div class="col-6 col-md-3"><div class="text-muted">Belum disetor</div><div class="fw-bold {{ $t['sisa'] > 0 ? 'text-danger' : '' }}">{{ $rp($t['sisa']) }}</div></div>
            </div>

            @if($iuran['bandingkan'])
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 small" data-testid="tabel-per-rt">
                        <thead><tr class="text-muted">
                            <th>RT</th><th class="text-end">KK</th><th class="text-end">Iuran lunas</th><th class="text-end">Tunggak 2+ bln</th>
                            <th class="text-end">Kewajiban setoran</th><th class="text-end">Disetor</th><th class="text-end">Belum disetor</th>
                        </tr></thead>
                        <tbody>
                        @foreach($iuran['baris'] as $b)
                            <tr>
                                <td class="fw-semibold">RT {{ $b['rt'] }}</td>
                                <td class="text-end">{{ $b['kk'] }}</td>
                                @if($b['ada_iuran'])
                                    <td class="text-end">{{ $b['tingkat'] !== null ? $b['tingkat'] . '%' : '-' }}</td>
                                    <td class="text-end {{ $b['tunggak2'] > 0 ? 'text-danger fw-semibold' : '' }}">{{ $b['tunggak2'] }}</td>
                                @else
                                    <td class="text-end text-muted" colspan="2">iuran belum ditetapkan</td>
                                @endif
                                <td class="text-end">{{ $rp($b['kewajiban']) }}</td>
                                <td class="text-end">{{ $rp($b['disetor']) }}</td>
                                <td class="text-end {{ $b['sisa'] > 0 ? 'text-danger fw-semibold' : '' }}">{{ $rp($b['sisa']) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="small text-muted mt-2">Urutan berdasarkan kode RT, bukan peringkat.</div>
            @endif
        </div>
    </div>
@endif

{{-- Tata kelola --}}
@if($tata && $adaData)
    <div class="card border-0 shadow-sm rounded-4 mb-3" data-testid="tata-kelola">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-3">Ketertiban pengelolaan</h6>
            <div class="row g-3 small">
                <div class="col-6 col-md-3">
                    <div class="text-muted">Pengeluaran berbukti</div>
                    <div class="fs-5 fw-bold {{ ($tata['bukti_persen'] ?? 100) < 70 ? 'text-danger' : '' }}">{{ $tata['bukti_persen'] !== null ? $tata['bukti_persen'] . '%' : '-' }}</div>
                    <div class="text-muted">dari {{ $tata['pengeluaran'] }} pengeluaran</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted">Pengeluaran belum diputuskan</div>
                    <div class="fs-5 fw-bold {{ $tata['pending_jumlah'] > 0 ? 'text-warning' : '' }}">{{ $tata['pending_jumlah'] }}</div>
                    <div class="text-muted">{{ $rp($tata['pending_nominal']) }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted">Laporan terlambat disetujui</div>
                    <div class="fs-5 fw-bold {{ $tata['laporan_terlambat'] > 0 ? 'text-warning' : '' }}">{{ $tata['laporan_terlambat'] }}</div>
                    <div class="text-muted">dari {{ $tata['laporan_total'] }} laporan (batas tgl {{ \App\Services\StatistikKeuanganService::BATAS_TERLAMBAT_HARI }} bulan berikutnya)</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted">Laporan pernah ditolak</div>
                    <div class="fs-5 fw-bold">{{ $tata['laporan_ditolak'] }}</div>
                    <div class="text-muted">perlu revisi</div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(! $adaData)
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-graph-up fs-1 text-muted"></i>
            <h5 class="fw-bold mt-3">Belum ada data untuk dianalisis</h5>
            <p class="text-muted mb-0">Statistik muncul setelah ada laporan yang disetujui pada tahun {{ $tahun }}.</p>
        </div>
    </div>
@endif

<p class="small text-muted mb-4">
    Dasar angka: laporan bulanan yang sudah disetujui. Cadangan kas = saldo terakhir dibagi rata-rata pengeluaran bulanan.
    Penilaian dibuat otomatis dari aturan sederhana dan bukan pengganti pertimbangan pengurus.
</p>

@if($adaData)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('chartStatTren');
    if (!el || !window.Chart) return;
    const data = @json($s['per_bulan']);
    const fmt = v => 'Rp ' + Number(v).toLocaleString('id-ID');
    new Chart(el, {
        data: {
            labels: data.map(b => b.nama.substring(0, 3)),
            datasets: [
                { type: 'bar', label: 'Pemasukan', data: data.map(b => b.ada ? b.pemasukan : null), backgroundColor: '#198754', borderRadius: 4, order: 2 },
                { type: 'bar', label: 'Pengeluaran', data: data.map(b => b.ada ? b.pengeluaran : null), backgroundColor: '#dc3545', borderRadius: 4, order: 2 },
                { type: 'line', label: 'Saldo akhir', data: data.map(b => b.ada ? b.saldo_akhir : null), borderColor: '#0d6efd', backgroundColor: '#0d6efd', tension: 0.25, spanGaps: true, order: 1 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { ticks: { callback: fmt } } },
            plugins: { tooltip: { callbacks: { label: c => c.dataset.label + ': ' + fmt(c.parsed.y) } } }
        }
    });
});
</script>
@endpush
@endif
