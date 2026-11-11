@extends('layouts.global')

@section('title', 'Setoran ke RW')

@php
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $bulanNama = \App\Services\IuranService::BULAN;
    $total = $rekap['total'];
    $badge = [
        'menunggu'     => ['Menunggu konfirmasi', 'warning'],
        'dikonfirmasi' => ['Dikonfirmasi', 'success'],
        'ditolak'      => ['Ditolak', 'danger'],
        'dibatalkan'   => ['Dibatalkan', 'secondary'],
    ];
    $bulananBisa = collect($bisaBulanan)->filter(fn ($a) => $a['bisa'] > 0);
    $lainnyaBisa = $tagihanRt->filter(fn ($it) => $it->bisa_disetor > 0);
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h2 class="fw-bold mb-1 fs-4" style="color:#198754;">Setoran ke RW{{ $rt ? ' - RT ' . $rt->kode_rt : '' }}</h2>
        <p class="text-muted mb-0 small">Warga membayar ke RT, lalu yang terkumpul disetor ke RW. Setelah dikonfirmasi RW, kas RT dan RW tercatat otomatis.</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="GET" class="d-flex">
            <select name="tahun" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()" aria-label="Tahun">
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" @selected($tahun == $th)>{{ $th }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@foreach(['success' => 'success', 'error' => 'danger', 'warning' => 'warning'] as $kunci => $warna)
    @if(session($kunci))
        <div class="alert alert-{{ $warna }} alert-dismissible fade show border-0 shadow-sm small" style="border-radius:12px;">{{ session($kunci) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
@endforeach
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm small" style="border-radius:12px;"><ul class="mb-0 list-unstyled">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($rekap['nominal_per_kk'] <= 0)
    <div class="alert alert-warning border-0 small"><i class="bi bi-exclamation-triangle-fill me-1"></i>RW belum menetapkan nominal setoran per KK untuk tahun {{ $tahun }}, sehingga kewajiban setoran iuran bulanan belum dapat dihitung.</div>
@else
    <div class="small text-muted mb-2">Nominal setoran iuran bulanan: <strong class="text-dark">{{ $rp($rekap['nominal_per_kk']) }}</strong> per KK yang lunas, per bulan (ditetapkan RW).</div>
@endif

{{-- Ringkasan --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
        <div class="small text-muted">Kewajiban {{ $tahun }}</div><div class="fw-bold text-nowrap">{{ $rp($total['kewajiban']) }}</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
        <div class="small text-muted">Sudah dikonfirmasi</div><div class="fw-bold text-success text-nowrap">{{ $rp($total['disetor']) }}</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
        <div class="small text-muted">Menunggu konfirmasi</div><div class="fw-bold text-warning text-nowrap">{{ $rp($total['menunggu']) }}</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
        <div class="small text-muted">Sisa kewajiban</div><div class="fw-bold text-nowrap {{ $total['sisa'] > 0 ? 'text-danger' : 'text-dark' }}" data-testid="sisa-setoran">{{ $rp(max(0, $total['sisa'])) }}</div>
    </div></div></div>
</div>

{{-- Rekap per bulan (centang bulan lalu "Setor Seluruhnya") --}}
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2">
            <div class="fw-bold small text-uppercase text-muted" style="letter-spacing:.5px;">Kewajiban Iuran Bulanan per Bulan</div>
            @if($bulananBisa->isNotEmpty())
                <div class="d-flex justify-content-between justify-content-md-end align-items-center gap-3">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="cekSemuaBulan" aria-label="Pilih semua bulan yang masih ada sisa">
                        <label class="form-check-label small" for="cekSemuaBulan">Pilih semua</label>
                    </div>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 text-nowrap" onclick="bukaSetorBulanan(bulanTerpilih().length ? bulanTerpilih() : null)" data-testid="btn-setor-bulanan">
                        <i class="bi bi-box-arrow-up me-1"></i>Setor Iuran Bulanan
                    </button>
                </div>
            @endif
        </div>

        {{-- Mobile --}}
        <div class="d-md-none">
            @foreach($rekap['per_bulan'] as $b => $a)
                @php $bisa = $bisaBulanan[$b]['bisa'] ?? 0; $sisa = max(0, $a['sisa']); @endphp
                <div class="py-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <span class="d-flex align-items-center gap-2">
                            @if($bisa > 0)
                                <input class="form-check-input cek-bulan mt-0" type="checkbox" data-bulan="{{ $b }}" data-bisa="{{ $bisa }}" aria-label="Pilih {{ $a['nama'] }}">
                            @else
                                <span style="width:1em;display:inline-block"></span>
                            @endif
                            <span class="fw-semibold">{{ $a['nama'] }}</span>
                        </span>
                        @if($bisa > 0)
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 py-0" onclick="bukaSetorBulanan([{{ $b }}])">Setor</button>
                        @elseif($a['kewajiban'] > 0 && $sisa <= 0)
                            <span class="small text-success"><i class="bi bi-check-circle-fill"></i> Lunas</span>
                        @elseif($a['menunggu'] > 0)
                            <span class="small text-warning">Menunggu konfirmasi</span>
                        @else
                            <span class="small text-muted">Tidak ada kewajiban</span>
                        @endif
                    </div>
                    @if($a['kewajiban'] > 0 || $a['disetor'] > 0 || $a['menunggu'] > 0)
                        <div class="small text-muted ms-4">{{ $a['lunas'] }} KK lunas &bull; Kewajiban {{ $rp($a['kewajiban']) }}</div>
                        <div class="small ms-4">Disetor {{ $rp($a['disetor']) }}@if($a['menunggu'] > 0) <span class="text-warning">(+{{ $rp($a['menunggu']) }} menunggu)</span>@endif @if($sisa > 0) &bull; <span class="text-danger">Sisa {{ $rp($sisa) }}</span>@endif</div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Desktop --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle mb-0 small">
                <thead><tr class="text-muted">
                    <th style="width:36px;"></th><th>Bulan</th><th class="text-end">KK lunas</th><th class="text-end">Iuran terkumpul</th>
                    <th class="text-end">Kewajiban</th><th class="text-end">Disetor</th><th class="text-end">Menunggu</th>
                    <th class="text-end">Sisa</th><th></th>
                </tr></thead>
                <tbody>
                @foreach($rekap['per_bulan'] as $b => $a)
                    @php $bisa = $bisaBulanan[$b]['bisa'] ?? 0; $sisa = max(0, $a['sisa']); @endphp
                    <tr>
                        <td>
                            @if($bisa > 0)
                                <input class="form-check-input cek-bulan" type="checkbox" data-bulan="{{ $b }}" data-bisa="{{ $bisa }}" aria-label="Pilih {{ $a['nama'] }}">
                            @endif
                        </td>
                        <td>{{ $a['nama'] }}</td>
                        <td class="text-end">{{ $a['lunas'] }}</td>
                        <td class="text-end">{{ $rp($a['terkumpul']) }}</td>
                        <td class="text-end">{{ $rp($a['kewajiban']) }}</td>
                        <td class="text-end text-success">{{ $rp($a['disetor']) }}</td>
                        <td class="text-end text-warning">{{ $a['menunggu'] > 0 ? $rp($a['menunggu']) : '-' }}</td>
                        <td class="text-end {{ $sisa > 0 ? 'text-danger fw-semibold' : '' }}">{{ $rp($sisa) }}</td>
                        <td class="text-end">
                            @if($bisa > 0)
                                <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 py-0" onclick="bukaSetorBulanan([{{ $b }}])">Setor</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Iuran Lainnya dari RW yang dikumpulkan lewat RT --}}
@if($tagihanRt->isNotEmpty())
<div class="card border-0 shadow-sm rounded-4 mb-3" data-testid="setoran-iuran-lainnya">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2 mb-2">
            <div>
                <div class="fw-bold small text-uppercase text-muted mb-1" style="letter-spacing:.5px;">Iuran Lainnya dari RW</div>
                <div class="small text-muted">Yang sudah diterima RT dan belum disetor dapat disetor ke RW.</div>
            </div>
            <button type="button" class="btn btn-success btn-sm rounded-pill px-3 text-nowrap flex-shrink-0 align-self-start" data-bs-toggle="modal" data-bs-target="#modalSetorLainnya"
                    data-testid="btn-setor-lainnya" @disabled($lainnyaBisa->isEmpty())>
                <i class="bi bi-box-arrow-up me-1"></i>Setor Iuran Lainnya
            </button>
        </div>
        @foreach($tagihanRt as $it)
            <div class="py-2 border-bottom">
                <div class="fw-semibold small">{{ $it->tagihan->judul }}</div>
                <div class="text-muted" style="font-size:.75rem;">{{ $it->lunas }} dari {{ $it->total_kk }} KK lunas &bull; Terkumpul {{ $rp($it->terkumpul) }}</div>
                <div class="small">Disetor {{ $rp($it->disetor) }}@if($it->menunggu > 0) <span class="text-warning">(+{{ $rp($it->menunggu) }} menunggu)</span>@endif @if($it->sisa > 0) &bull; <span class="text-danger">Sisa {{ $rp($it->sisa) }}</span>@elseif($it->terkumpul > 0) &bull; <span class="text-success">Lunas</span>@endif</div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Riwayat (satu kartu per setoran) --}}
<div class="card border-0 shadow-sm rounded-4 mb-5" id="riwayat-setoran">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold small text-uppercase text-muted" style="letter-spacing:.5px;">Riwayat Setoran {{ $tahun }}</div>
            @if($riwayat->total() > 0)<span class="small text-muted">{{ $riwayat->total() }} setoran</span>@endif
        </div>
        @forelse($riwayat as $s)
            @php [$labelStatus, $warna] = $badge[$s->status] ?? [$s->status, 'secondary']; @endphp
            <div class="py-2 border-bottom">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-semibold small">{{ $s->periode_label }} &bull; {{ $rp($s->nominal) }}</div>
                        @if($s->rows->count() > 1)
                            <div class="text-muted" style="font-size:.75rem;">
                                {{ $s->rows->map(fn ($r) => ($r->tagihan_id ? ($r->tagihan->judul ?? '-') : $bulanNama[$r->periode_bulan]) . ' ' . $rp($r->nominal))->implode(' | ') }}
                            </div>
                        @endif
                        <div class="text-muted" style="font-size:.75rem;">{{ $s->tanggal_setor->format('d M Y') }} &bull; {{ ucfirst($s->metode) }}
                            @if($s->bukti_url) &bull; <a href="{{ $s->bukti_url }}" target="_blank" rel="noopener">Lihat bukti</a>@endif
                        </div>
                        @if(in_array($s->status, ['ditolak', 'dibatalkan']) && $s->alasan)
                            <div class="small text-danger mt-1">Alasan: {{ $s->alasan }}</div>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $warna }} bg-opacity-10 text-{{ $warna }}">{{ $labelStatus }}</span>
                        @if(in_array($s->status, ['menunggu', 'ditolak']))
                            <form action="{{ route('opkeuangan.setoran.destroy', $s->id) }}" method="POST" class="mt-1" data-konfirmasi="Hapus catatan setoran ini?" data-konfirmasi-judul="Hapus Catatan Setoran" data-konfirmasi-tombol="Hapus" data-konfirmasi-jenis="bahaya">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light text-danger py-0 px-2" aria-label="Hapus catatan setoran"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted small mb-0">Belum ada setoran pada tahun {{ $tahun }}.</p>
        @endforelse
        @if($riwayat->hasPages())<div class="mt-3">{{ $riwayat->links() }}</div>@endif
    </div>
</div>

{{-- Bar aksi: muncul setelah ada bulan yang dicentang --}}
<div id="barSetor" class="d-none position-fixed bottom-0 start-0 end-0 bg-white border-top shadow-lg py-2 px-3" style="z-index:1030;" data-testid="bar-setor">
    <div class="d-flex justify-content-between align-items-center gap-2 mx-auto" style="max-width:960px;">
        <div class="small lh-sm" style="min-width:0;"><strong id="barJumlah">0</strong> bulan dipilih<br class="d-sm-none"><span class="d-none d-sm-inline"> &bull; </span><strong id="barTotal">Rp 0</strong></div>
        <button type="button" class="btn btn-success btn-sm rounded-pill px-3 flex-shrink-0 text-nowrap" onclick="bukaSetorBulanan(bulanTerpilih())">Setor Seluruhnya</button>
    </div>
</div>

{{-- Modal setor iuran bulanan --}}
<div class="modal fade" id="modalSetorBulanan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form class="modal-content border-0 shadow" style="border-radius:16px;" action="{{ route('opkeuangan.setoran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jenis" value="bulanan">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">Setor Iuran Bulanan ke RW</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="small text-muted">Periksa rincian. Nominal tiap bulan dapat dikurangi untuk setoran sebagian, tetapi tidak boleh melebihi sisa.</div>
                        <div class="small"><span class="fw-semibold" data-info-dipilih></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-2 align-baseline" data-aksi-semua="1">Pilih semua</button>
                            <span class="text-muted">|</span>
                            <button type="button" class="btn btn-link btn-sm p-0 align-baseline" data-aksi-semua="0">Kosongkan</button>
                        </div>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle mb-0 small" data-testid="rincian-setor-bulanan">
                            <thead><tr class="text-muted">
                                <th style="width:32px;"></th><th>Bulan</th><th class="text-end d-none d-sm-table-cell">KK lunas</th>
                                <th class="text-end d-none d-sm-table-cell">Kewajiban</th><th class="text-end d-none d-sm-table-cell">Sudah/menunggu</th><th class="text-end" style="min-width:120px;">Disetor (Rp)</th>
                            </tr></thead>
                            <tbody>
                            @foreach($bulananBisa as $b => $a)
                                <tr class="baris-setor" data-bulan="{{ $b }}" data-max="{{ $a['bisa'] }}">
                                    <td><input type="checkbox" class="form-check-input cek-modal" checked aria-label="Sertakan {{ $a['nama'] }}"></td>
                                    <td>
                                        {{ $a['nama'] }}
                                        <div class="d-sm-none text-muted" style="font-size:.7rem;line-height:1.2;">{{ $a['lunas'] }} KK &bull; Kewajiban {{ $rp($a['kewajiban']) }}@if($a['disetor'] + $a['menunggu'] > 0)<br>Sudah/menunggu {{ $rp($a['disetor'] + $a['menunggu']) }}@endif</div>
                                    </td>
                                    <td class="text-end d-none d-sm-table-cell">{{ $a['lunas'] }}</td>
                                    <td class="text-end d-none d-sm-table-cell">{{ $rp($a['kewajiban']) }}</td>
                                    <td class="text-end d-none d-sm-table-cell">{{ $rp($a['disetor'] + $a['menunggu']) }}</td>
                                    <td><input type="text" inputmode="numeric" name="items[{{ $b }}]" class="form-control form-control-sm text-end nom-setor" value="{{ number_format($a['bisa'], 0, ',', '.') }}" aria-label="Nominal setoran {{ $a['nama'] }}"></td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr class="fw-bold"><td colspan="2" class="text-end d-sm-none">Total disetor</td><td colspan="5" class="text-end d-none d-sm-table-cell">Total disetor</td><td class="text-end total-setor">Rp 0</td></tr></tfoot>
                        </table>
                    </div>
                    @include('opKeuangan._form-setoran-umum', ['prefix' => 'bln'])
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold btn-kirim-setor">Kirim ke RW</button>
                </div>
            </form>
    </div>
</div>

{{-- Modal setor Iuran Lainnya dari RW --}}
<div class="modal fade" id="modalSetorLainnya" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <form class="modal-content border-0 shadow" style="border-radius:16px;" action="{{ route('opkeuangan.setoran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jenis" value="lainnya">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">Setor Iuran Lainnya ke RW</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="small text-muted">Nominal terisi sebesar yang sudah diterima RT dan belum disetor. Boleh dikurangi untuk setoran sebagian.</div>
                        <div class="small"><span class="fw-semibold" data-info-dipilih></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-2 align-baseline" data-aksi-semua="1">Pilih semua</button>
                            <span class="text-muted">|</span>
                            <button type="button" class="btn btn-link btn-sm p-0 align-baseline" data-aksi-semua="0">Kosongkan</button>
                        </div>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle mb-0 small" data-testid="rincian-setor-lainnya">
                            <thead><tr class="text-muted">
                                <th style="width:32px;"></th><th>Iuran</th><th class="text-end d-none d-sm-table-cell">Diterima</th>
                                <th class="text-end d-none d-sm-table-cell">Sudah/menunggu</th><th class="text-end" style="min-width:120px;">Disetor (Rp)</th>
                            </tr></thead>
                            <tbody>
                            @foreach($lainnyaBisa as $it)
                                <tr class="baris-setor" data-max="{{ $it->bisa_disetor }}">
                                    <td><input type="checkbox" class="form-check-input cek-modal" checked aria-label="Sertakan {{ $it->tagihan->judul }}"></td>
                                    <td class="text-break">
                                        {{ $it->tagihan->judul }}
                                        <div class="d-sm-none text-muted" style="font-size:.7rem;line-height:1.2;">Diterima {{ $rp($it->terkumpul) }}@if($it->disetor + $it->menunggu > 0)<br>Sudah/menunggu {{ $rp($it->disetor + $it->menunggu) }}@endif</div>
                                    </td>
                                    <td class="text-end d-none d-sm-table-cell">{{ $rp($it->terkumpul) }}</td>
                                    <td class="text-end d-none d-sm-table-cell">{{ $rp($it->disetor + $it->menunggu) }}</td>
                                    <td><input type="text" inputmode="numeric" name="items[{{ $it->tagihan->id }}]" class="form-control form-control-sm text-end nom-setor" value="{{ number_format($it->bisa_disetor, 0, ',', '.') }}" aria-label="Nominal setoran {{ $it->tagihan->judul }}"></td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot><tr class="fw-bold"><td colspan="2" class="text-end d-sm-none">Total disetor</td><td colspan="4" class="text-end d-none d-sm-table-cell">Total disetor</td><td class="text-end total-setor">Rp 0</td></tr></tfoot>
                        </table>
                    </div>
                    @include('opKeuangan._form-setoran-umum', ['prefix' => 'lain'])
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold btn-kirim-setor">Kirim ke RW</button>
                </div>
            </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const fmt = n => Number(n).toLocaleString('id-ID');
    const angka = v => parseInt((v || '').replace(/\D/g, '') || '0', 10);

    // ── Modal: hitung total, batasi nominal ≤ sisa, nonaktifkan baris yang tidak dicentang ──
    function hitung(modal) {
        let total = 0;
        modal.querySelectorAll('.baris-setor').forEach(function (tr) {
            const cek = tr.querySelector('.cek-modal');
            const inp = tr.querySelector('.nom-setor');
            inp.disabled = !cek.checked;
            if (!cek.checked) return;
            const max = parseInt(tr.dataset.max, 10);
            let v = angka(inp.value);
            if (v > max) { v = max; inp.value = fmt(v); }
            total += v;
        });
        const baris = modal.querySelectorAll('.baris-setor');
        const dipilih = Array.from(baris).filter(tr => tr.querySelector('.cek-modal').checked).length;
        const info = modal.querySelector('[data-info-dipilih]');
        if (info) info.textContent = dipilih + ' dari ' + baris.length + ' disertakan';
        modal.querySelector('.total-setor').textContent = 'Rp ' + fmt(total);
        modal.querySelector('.btn-kirim-setor').disabled = total <= 0;
    }

    ['modalSetorBulanan', 'modalSetorLainnya'].forEach(function (id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.querySelectorAll('.cek-modal').forEach(el => el.addEventListener('change', () => hitung(modal)));
        modal.querySelectorAll('[data-aksi-semua]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const centang = btn.dataset.aksiSemua === '1';
                modal.querySelectorAll('.baris-setor').forEach(function (tr) {
                    tr.querySelector('.cek-modal').checked = centang;
                    tr.querySelector('.nom-setor').value = fmt(tr.dataset.max);
                });
                hitung(modal);
            });
        });
        modal.querySelectorAll('.nom-setor').forEach(function (inp) {
            inp.addEventListener('input', function () {
                const v = angka(inp.value);
                inp.value = v ? fmt(v) : '';
                hitung(modal);
            });
        });
        // Setiap dibuka: kembali ke nominal penuh (sisa) dan centang semua.
        modal.addEventListener('show.bs.modal', function (e) {
            if (id === 'modalSetorLainnya') {
                modal.querySelectorAll('.baris-setor').forEach(function (tr) {
                    tr.querySelector('.cek-modal').checked = true;
                    tr.querySelector('.nom-setor').value = fmt(tr.dataset.max);
                });
            }
            hitung(modal);
        });
        hitung(modal);
    });

    // ── Iuran bulanan: pilih bulan (dari tabel/baris) lalu buka modal rincian ──
    window.bukaSetorBulanan = function (daftar) {
        const modalEl = document.getElementById('modalSetorBulanan');
        modalEl.querySelectorAll('.baris-setor').forEach(function (tr) {
            const bulan = parseInt(tr.dataset.bulan, 10);
            tr.querySelector('.cek-modal').checked = daftar === null || daftar.includes(bulan);
            tr.querySelector('.nom-setor').value = fmt(tr.dataset.max);
        });
        hitung(modalEl);
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    };

    const semuaCek = () => Array.from(document.querySelectorAll('.cek-bulan'));
    window.bulanTerpilih = function () {
        return Array.from(new Set(semuaCek().filter(el => el.checked).map(el => parseInt(el.dataset.bulan, 10))));
    };

    function sinkron(sumber) {
        // Tabel desktop dan daftar mobile berbagi pilihan yang sama.
        semuaCek().forEach(function (el) {
            if (el !== sumber && el.dataset.bulan === sumber.dataset.bulan) el.checked = sumber.checked;
        });
        const bar = document.getElementById('barSetor');
        const dipilih = bulanTerpilih();
        let total = 0;
        dipilih.forEach(function (b) {
            const el = semuaCek().find(x => parseInt(x.dataset.bulan, 10) === b);
            total += parseInt(el.dataset.bisa, 10);
        });
        document.getElementById('barJumlah').textContent = dipilih.length;
        document.getElementById('barTotal').textContent = 'Rp ' + fmt(total);
        bar.classList.toggle('d-none', dipilih.length === 0);
    }

    semuaCek().forEach(el => el.addEventListener('change', () => sinkron(el)));

    const semua = document.getElementById('cekSemuaBulan');
    if (semua) {
        semua.addEventListener('change', function () {
            semuaCek().forEach(el => { el.checked = semua.checked; });
            const pertama = semuaCek()[0];
            if (pertama) sinkron(pertama);
        });
    }
})();
</script>
@endpush
