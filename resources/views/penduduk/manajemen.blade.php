@extends('layouts.global')

@section('title', 'Manajemen Penduduk')

{{--
    View bersama Manajemen Penduduk untuk RT dan RW.
    $konfigurasi: lingkup, daftarRt (null = tanpa filter RT), rtTerpilih, routeIndex, routeExport,
    routeKeluarga, routePenduduk, routeTambah (null = tidak bisa menambah data), catatanExport.
--}}
@php
    $k_ = $konfigurasi; // alias pendek; $k dipakai untuk baris keluarga
    $filterRt = $k_['daftarRt'] !== null;
    $rtParam = $filterRt && $k_['rtTerpilih'] ? ['rt_id' => $k_['rtTerpilih']] : [];
@endphp

@section('content')
@if(session('activation_link'))
<div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
    <div class="d-flex align-items-start gap-2">
        <i class="bi bi-link-45deg fs-4 text-success"></i>
        <div class="flex-grow-1">
            <div class="fw-semibold mb-1">Link Aktivasi Akun Warga</div>
            <p class="small text-muted mb-2">
                Bagikan link berikut kepada pemegang akun (<strong>{{ session('activation_username') }}</strong>)
                agar dapat membuat password sendiri. Link berlaku sampai akun diaktifkan.
            </p>
            <div class="input-group input-group-sm">
                <input type="text" id="linkAktivasi" class="form-control bg-white" value="{{ session('activation_link') }}" readonly aria-label="Link aktivasi">
                <button type="button" class="btn btn-success" id="btnSalinLink"><i class="bi bi-clipboard me-1"></i> Salin</button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">Manajemen Penduduk</h2>
        <p class="text-muted mb-0">Data penduduk di {{ $k_['lingkup'] }}.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill" data-testid="total-penduduk">
            <i class="bi bi-people-fill me-1"></i> Total: {{ number_format($totalPenduduk, 0, ',', '.') }} jiwa
        </span>
        <button type="button" class="btn btn-outline-success" style="border-radius: 0.5rem;" title="Export ke Excel" data-bs-toggle="modal" data-bs-target="#modalExportPenduduk">
            <i class="bi bi-file-earmark-excel me-1"></i> Export
        </button>
        @if($k_['routeTambah'])
            <a href="{{ route($k_['routeTambah']) }}" class="btn btn-success" style="border-radius: 0.5rem;" data-testid="btn-tambah-keluarga">
                <i class="bi bi-plus-lg me-1"></i> Tambah Keluarga
            </a>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3 p-md-4">
        <ul class="nav nav-tabs mb-3" id="tabPenduduk" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-kk-btn" data-bs-toggle="tab" data-bs-target="#tab-kk" type="button" role="tab" aria-controls="tab-kk" aria-selected="true">
                    <i class="bi bi-house-door me-1"></i> Profil Keluarga
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-penduduk-btn" data-bs-toggle="tab" data-bs-target="#tab-penduduk" type="button" role="tab" aria-controls="tab-penduduk" aria-selected="false">
                    <i class="bi bi-people me-1"></i> Daftar Semua Penduduk
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- ===================== TAB 1: PROFIL KELUARGA ===================== --}}
            <div class="tab-pane fade show active" id="tab-kk" role="tabpanel" aria-labelledby="tab-kk-btn">
                <form method="GET" action="{{ route($k_['routeIndex']) }}" class="row g-3 mb-4">
                    <input type="hidden" name="tab" value="kk">
                    <input type="hidden" name="q" value="{{ $search }}">
                    <div class="{{ $filterRt ? 'col-md-6' : 'col-md-9' }}">
                        <label for="q_kk" class="form-label text-muted small">Cari Keluarga (No. KK / Nama Kepala / Alamat)</label>
                        <input type="text" id="q_kk" name="q_kk" value="{{ $searchKk }}" class="form-control bg-light border-0" placeholder="Nomor KK, nama kepala keluarga, atau alamat...">
                    </div>
                    @if($filterRt)
                        @include('penduduk.partials.filter-rt', ['idFilter' => 'rt_id_kk'])
                    @endif
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-secondary flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
                        @if($searchKk !== '' || $rtParam)
                            <a href="{{ route($k_['routeIndex'], array_filter(['q' => $search])) }}#kk" class="btn btn-light border text-danger" title="Reset Filter" aria-label="Reset filter"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-muted small fw-semibold py-3">NO. KK</th>
                                <th class="text-muted small fw-semibold py-3">KEPALA KELUARGA</th>
                                @if($filterRt)<th class="text-muted small fw-semibold py-3">RT</th>@endif
                                <th class="text-muted small fw-semibold py-3">ALAMAT</th>
                                <th class="text-muted small fw-semibold py-3">STATUS WARGA</th>
                                <th class="text-muted small fw-semibold py-3">PEMILIK RUMAH</th>
                                <th class="text-muted small fw-semibold py-3 text-center">ANGGOTA</th>
                                <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keluarga as $k)
                            <tr>
                                <td class="py-3"><span style="font-family: monospace;">{{ $k->no_kk }}</span></td>
                                <td class="py-3"><div class="fw-semibold text-dark">{{ $k->kepalaKeluarga?->nama_lengkap ?? '-' }}</div></td>
                                @if($filterRt)<td class="py-3">{{ $k->rt?->kode_rt ? 'RT ' . $k->rt->kode_rt : '-' }}</td>@endif
                                <td class="py-3 text-muted small">{{ $k->alamat_domisili ?: ($k->alamat ?: '-') }}</td>
                                <td class="py-3">
                                    @if($k->status_warga === 'tetap')
                                        <span class="badge bg-success bg-opacity-10 text-success">Warga Tetap</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning">Bukan Warga Tetap</span>
                                    @endif
                                </td>
                                <td class="py-3">{{ $k->pemilik_rumah ? 'Ya' : 'Bukan' }}</td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $k->anggota_count }} jiwa</span>
                                </td>
                                <td class="py-3 text-end">
                                    <a href="{{ route($k_['routeKeluarga'], $k->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $filterRt ? 8 : 7 }}" class="text-center py-5 text-muted">
                                    {{ $searchKk !== '' ? 'Tidak ada keluarga yang cocok dengan pencarian.' : 'Belum ada data keluarga di ' . $k_['lingkup'] . '.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <span class="text-muted small">Menampilkan {{ $keluarga->firstItem() ?? 0 }} - {{ $keluarga->lastItem() ?? 0 }} dari {{ $keluarga->total() }} keluarga</span>
                    {{ $keluarga->fragment('kk')->links('pagination::bootstrap-5') }}
                </div>
            </div>

            {{-- ===================== TAB 2: DAFTAR SEMUA PENDUDUK ===================== --}}
            <div class="tab-pane fade" id="tab-penduduk" role="tabpanel" aria-labelledby="tab-penduduk-btn">
                <form method="GET" action="{{ route($k_['routeIndex']) }}" class="row g-3 mb-4">
                    <input type="hidden" name="tab" value="penduduk">
                    <input type="hidden" name="q_kk" value="{{ $searchKk }}">
                    <div class="{{ $filterRt ? 'col-md-6' : 'col-md-9' }}">
                        <label for="q_penduduk" class="form-label text-muted small">Cari Penduduk (Nama / NIK)</label>
                        <input type="text" id="q_penduduk" name="q" value="{{ $search }}" class="form-control bg-light border-0" placeholder="Nama lengkap atau NIK...">
                    </div>
                    @if($filterRt)
                        @include('penduduk.partials.filter-rt', ['idFilter' => 'rt_id_penduduk'])
                    @endif
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-secondary flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
                        @if($search !== '' || $rtParam)
                            <a href="{{ route($k_['routeIndex'], array_filter(['q_kk' => $searchKk, 'tab' => 'penduduk'])) }}#penduduk" class="btn btn-light border text-danger" title="Reset Filter" aria-label="Reset filter"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-muted small fw-semibold py-3">NAMA</th>
                                <th class="text-muted small fw-semibold py-3">NIK</th>
                                <th class="text-muted small fw-semibold py-3">L/P</th>
                                <th class="text-muted small fw-semibold py-3">HUBUNGAN KELUARGA</th>
                                <th class="text-muted small fw-semibold py-3">NO. KK</th>
                                @if($filterRt)<th class="text-muted small fw-semibold py-3">RT</th>@endif
                                <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penduduk as $p)
                            <tr>
                                <td class="py-3"><div class="fw-semibold text-dark">{{ $p->nama_lengkap }}</div></td>
                                <td class="py-3"><span style="font-family: monospace;">{{ $p->nik }}</span></td>
                                <td class="py-3">{{ $p->jenis_kelamin }}</td>
                                <td class="py-3">{{ $p->status_hubungan_keluarga }}</td>
                                <td class="py-3">{{ $p->keluarga?->no_kk ?? '-' }}</td>
                                @if($filterRt)<td class="py-3">{{ $p->keluarga?->rt?->kode_rt ? 'RT ' . $p->keluarga->rt->kode_rt : '-' }}</td>@endif
                                <td class="py-3 text-end">
                                    <a href="{{ route($k_['routePenduduk'], $p->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $filterRt ? 7 : 6 }}" class="text-center py-5 text-muted">
                                    {{ $search !== '' ? 'Tidak ada penduduk yang cocok dengan pencarian.' : 'Belum ada data penduduk di ' . $k_['lingkup'] . '.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <span class="text-muted small">Menampilkan {{ $penduduk->firstItem() ?? 0 }} - {{ $penduduk->lastItem() ?? 0 }} dari {{ $penduduk->total() }} penduduk</span>
                    {{ $penduduk->fragment('penduduk')->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL: Export Penduduk ke Excel ===================== --}}
<div class="modal fade" id="modalExportPenduduk" tabindex="-1" aria-labelledby="modalExportPendudukLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0.75rem;">
            <form method="GET" action="{{ route($k_['routeExport']) }}" id="formExportPenduduk">
                {{-- Ikut filter pencarian penduduk (dan RT untuk RW) yang sedang aktif. --}}
                <input type="hidden" name="q" value="{{ $search }}">
                @if($rtParam)
                    <input type="hidden" name="rt_id" value="{{ $rtParam['rt_id'] }}">
                @endif
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExportPendudukLabel"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Export Data Penduduk ({{ $k_['lingkup'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Pilih kolom data yang ingin diekspor ke Excel. {{ $k_['catatanExport'] }}</p>
                    <div class="mb-2 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="exportSelectAll">Pilih Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="exportSelectNone">Kosongkan</button>
                    </div>
                    <div class="row">
                        @foreach(\App\Exports\PendudukExport::KOLOM as $key => $label)
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input export-kolom" type="checkbox" name="kolom[]" value="{{ $key }}" id="kolom_{{ $key }}" checked>
                                <label class="form-check-label small" for="kolom_{{ $key }}">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="invalid-feedback d-block mt-2" id="exportKolomError" style="display:none !important;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-download me-1"></i>Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // Tombol salin link aktivasi.
    var btnSalin = document.getElementById('btnSalinLink');
    if (btnSalin) {
        btnSalin.addEventListener('click', function () {
            var input = document.getElementById('linkAktivasi');
            input.select();
            input.setSelectionRange(0, 99999);
            var ok = false;
            try {
                if (navigator.clipboard) { navigator.clipboard.writeText(input.value); ok = true; }
                else { ok = document.execCommand('copy'); }
            } catch (e) { ok = false; }
            btnSalin.innerHTML = ok ? '<i class="bi bi-check2 me-1"></i> Tersalin' : '<i class="bi bi-clipboard me-1"></i> Salin';
            setTimeout(function () { btnSalin.innerHTML = '<i class="bi bi-clipboard me-1"></i> Salin'; }, 2000);
        });
    }

    // Validasi minimal 1 kolom + tombol pilih semua/kosongkan pada modal export.
    var formExport = document.getElementById('formExportPenduduk');
    if (formExport) {
        var checks = formExport.querySelectorAll('.export-kolom');
        var err = document.getElementById('exportKolomError');
        document.getElementById('exportSelectAll')?.addEventListener('click', function () {
            checks.forEach(function (c) { c.checked = true; });
        });
        document.getElementById('exportSelectNone')?.addEventListener('click', function () {
            checks.forEach(function (c) { c.checked = false; });
        });
        formExport.addEventListener('submit', function (e) {
            var ada = Array.prototype.some.call(checks, function (c) { return c.checked; });
            if (!ada) {
                e.preventDefault();
                if (err) { err.textContent = 'Pilih minimal satu kolom untuk diekspor.'; err.style.display = 'block'; }
                return;
            }
            if (err) err.style.display = 'none';
            var modalEl = document.getElementById('modalExportPenduduk');
            if (typeof bootstrap !== 'undefined') {
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) setTimeout(function () { modal.hide(); }, 300);
            }
        });
    }

    // Tab aktif dari query ?tab= atau hash URL (setelah submit filter / pagination).
    var params = new URLSearchParams(window.location.search);
    var aktifPenduduk = params.get('tab') === 'penduduk' || window.location.hash === '#penduduk';
    if (aktifPenduduk) {
        var btn = document.getElementById('tab-penduduk-btn');
        if (btn && typeof bootstrap !== 'undefined') new bootstrap.Tab(btn).show();
    }
    document.querySelectorAll('#tabPenduduk button[data-bs-toggle="tab"]').forEach(function (b) {
        b.addEventListener('shown.bs.tab', function (e) {
            var id = e.target.getAttribute('data-bs-target');
            history.replaceState(null, '', id === '#tab-penduduk' ? '#penduduk' : '#kk');
        });
    });
})();
</script>
@endpush
@endsection
