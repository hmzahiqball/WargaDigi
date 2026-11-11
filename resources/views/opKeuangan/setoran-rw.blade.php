@extends('layouts.global')

@section('title', $bisaKelola ? 'Setoran dari RT' : 'Rekap Iuran RT')

@php
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $tb = $rekap['total_bulan'];
    $tk = $rekap['total_kumulatif'];
    $rute = $bisaKelola ? route('opkeuangan.setoran.index') : route('rw.rekap-iuran');
    $badgeStatus = [
        'lunas'     => ['Lunas', 'success'],
        'sebagian'  => ['Sebagian', 'info'],
        'menunggu'  => ['Menunggu konfirmasi', 'warning'],
        'belum'     => ['Belum setor', 'danger'],
        'tidak_ada' => ['Tidak ada kewajiban', 'secondary'],
    ];
    $badgeRiwayat = [
        'dikonfirmasi' => ['Dikonfirmasi', 'success'],
        'ditolak'      => ['Ditolak', 'danger'],
        'dibatalkan'   => ['Dibatalkan', 'secondary'],
    ];
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h2 class="fw-bold mb-1 fs-4" style="color:#198754;">{{ $bisaKelola ? 'Setoran dari RT' : 'Rekap Iuran RT' }}</h2>
        <p class="text-muted mb-0 small">Iuran warga tercatat di kas RT. Kas RW bertambah saat setoran RT dikonfirmasi.</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="GET" action="{{ $rute }}" class="d-flex gap-2">
            <select name="bulan" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()" aria-label="Bulan">
                @foreach($namaBulan as $i => $nm)
                    <option value="{{ $i }}" @selected($bulan == $i)>{{ $nm }}</option>
                @endforeach
            </select>
            <select name="tahun" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()" aria-label="Tahun">
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" @selected($tahun == $th)>{{ $th }}</option>
                @endforeach
            </select>
        </form>
        @if($bisaKelola)
            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalPengaturanSetoran">
                <i class="bi bi-gear me-1"></i>Nominal Setoran
            </button>
        @endif
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

@if(($pengaturan['default'] ?? 0) <= 0 && empty($pengaturan['per_rt']))
    <div class="alert alert-warning border-0 small"><i class="bi bi-exclamation-triangle-fill me-1"></i>Nominal setoran per KK untuk {{ $tahun }} belum ditetapkan, sehingga kewajiban setoran bernilai nol.@if($bisaKelola) Atur lewat tombol "Nominal Setoran".@endif</div>
@endif

{{-- Ringkasan --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
        <div class="small text-muted">Iuran terkumpul (semua RT)</div><div class="fw-bold text-nowrap">{{ $rp($tb['terkumpul']) }}</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
        <div class="small text-muted">Kewajiban setoran</div><div class="fw-bold text-nowrap">{{ $rp($tb['kewajiban']) }}</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
        <div class="small text-muted">Setoran diterima</div><div class="fw-bold text-success text-nowrap">{{ $rp($tb['disetor']) }}</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-3">
        <div class="small text-muted">Sisa s.d. {{ $namaBulan[$bulan] }}</div><div class="fw-bold text-nowrap {{ $tk['sisa'] > 0 ? 'text-danger' : 'text-dark' }}">{{ $rp($tk['sisa']) }}</div>
    </div></div></div>
</div>

<ul class="nav nav-pills gap-2 mb-3 flex-nowrap" role="tablist">
    <li class="nav-item"><button class="nav-link active rounded-pill px-3 py-1 small text-nowrap" data-bs-toggle="pill" data-bs-target="#tabRekap" type="button">Rekap per RT</button></li>
    @if($tagihanRw->isNotEmpty())
        <li class="nav-item"><button class="nav-link rounded-pill px-3 py-1 small text-nowrap" data-bs-toggle="pill" data-bs-target="#tabLainnya" type="button" data-testid="tab-iuran-lainnya">Iuran Lainnya</button></li>
    @endif
    @if($bisaKelola)
        <li class="nav-item"><button class="nav-link rounded-pill px-3 py-1 small text-nowrap" data-bs-toggle="pill" data-bs-target="#tabKonfirmasi" type="button" data-testid="tab-konfirmasi">Konfirmasi @if($menunggu->count())<span class="badge rounded-pill bg-warning text-dark ms-1">{{ $menunggu->count() }}</span>@endif</button></li>
    @endif
    <li class="nav-item"><button class="nav-link rounded-pill px-3 py-1 small text-nowrap" data-bs-toggle="pill" data-bs-target="#tabRiwayat" type="button">Riwayat</button></li>
</ul>

<div class="tab-content">
    {{-- Rekap per RT --}}
    <div class="tab-pane fade show active" id="tabRekap" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3">
                {{-- Mobile --}}
                <div class="d-md-none">
                    @foreach($rekap['baris'] as $r)
                        @php [$lbl, $wrn] = $badgeStatus[$r->status]; @endphp
                        <div class="py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">RT {{ $r->rt->kode_rt }}</span>
                                <span class="badge bg-{{ $wrn }} bg-opacity-10 text-{{ $wrn }}">{{ $lbl }}</span>
                            </div>
                            <div class="small text-muted">{{ $r->bulan['lunas'] }} dari {{ $r->kk_total }} KK lunas &bull; Terkumpul {{ $rp($r->bulan['terkumpul']) }}</div>
                            <div class="small">Kewajiban {{ $rp($r->bulan['kewajiban']) }} &bull; Disetor {{ $rp($r->bulan['disetor']) }}@if($r->bulan['menunggu'] > 0) <span class="text-warning">(+{{ $rp($r->bulan['menunggu']) }} menunggu)</span>@endif</div>
                            @if($r->kumulatif['sisa'] > 0)<div class="small text-danger">Sisa s.d. {{ $namaBulan[$bulan] }}: {{ $rp($r->kumulatif['sisa']) }}</div>@endif
                        </div>
                    @endforeach
                </div>
                {{-- Desktop --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle mb-0 small" data-testid="tabel-rekap-rt">
                        <thead><tr class="text-muted">
                            <th>RT</th><th class="text-end">KK lunas</th><th class="text-end">Terkumpul</th>
                            <th class="text-end">Setoran/KK</th><th class="text-end">Kewajiban</th><th class="text-end">Disetor</th>
                            <th class="text-end">Menunggu</th><th>Status</th><th class="text-end">Sisa s.d. {{ $namaBulan[$bulan] }}</th>
                        </tr></thead>
                        <tbody>
                        @foreach($rekap['baris'] as $r)
                            @php [$lbl, $wrn] = $badgeStatus[$r->status]; @endphp
                            <tr>
                                <td class="fw-semibold">RT {{ $r->rt->kode_rt }}</td>
                                <td class="text-end">{{ $r->bulan['lunas'] }} / {{ $r->kk_total }}</td>
                                <td class="text-end">{{ $rp($r->bulan['terkumpul']) }}</td>
                                <td class="text-end">{{ $rp($r->nominal_per_kk) }}</td>
                                <td class="text-end">{{ $rp($r->bulan['kewajiban']) }}</td>
                                <td class="text-end text-success">{{ $rp($r->bulan['disetor']) }}</td>
                                <td class="text-end text-warning">{{ $r->bulan['menunggu'] > 0 ? $rp($r->bulan['menunggu']) : '-' }}</td>
                                <td><span class="badge bg-{{ $wrn }} bg-opacity-10 text-{{ $wrn }}">{{ $lbl }}</span></td>
                                <td class="text-end {{ $r->kumulatif['sisa'] > 0 ? 'text-danger fw-semibold' : '' }}">{{ $rp($r->kumulatif['sisa']) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="small text-muted mt-2">Kewajiban = nominal setoran per KK x jumlah KK yang lunas pada bulan itu. Hanya angka agregat per RT yang ditampilkan.</div>
            </div>
        </div>
    </div>

    {{-- Iuran Lainnya yang dikumpulkan lewat RT --}}
    @if($tagihanRw->isNotEmpty())
    <div class="tab-pane fade" id="tabLainnya" role="tabpanel">
        @foreach($tagihanRw as $it)
            @php $t = $it->tagihan; $tot = $it->rekap['total']; @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        <div>
                            <div class="fw-bold">{{ $t->judul }}</div>
                            <div class="small text-muted">{{ $t->formatted_nominal }} &bull; dikumpulkan lewat RT &bull; {{ $t->status === 'Active' ? 'Aktif' : 'Ditutup' }}</div>
                        </div>
                        @if($bisaKelola)<a href="{{ route('opkeuangan.iuran.detail', $t->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-0">Detail</a>@endif
                    </div>
                    <div class="small mb-2">Terkumpul <strong>{{ $rp($tot['terkumpul']) }}</strong> &bull; Disetor <strong class="text-success">{{ $rp($tot['disetor']) }}</strong>@if($tot['menunggu'] > 0) <span class="text-warning">(+{{ $rp($tot['menunggu']) }} menunggu)</span>@endif &bull; Sisa <strong class="{{ $tot['sisa'] > 0 ? 'text-danger' : '' }}">{{ $rp($tot['sisa']) }}</strong></div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 small">
                            <thead><tr class="text-muted"><th>RT</th><th class="text-end">KK lunas</th><th class="text-end">Terkumpul</th><th class="text-end">Disetor</th><th class="text-end">Sisa</th></tr></thead>
                            <tbody>
                            @foreach($it->rekap['baris'] as $r)
                                <tr>
                                    <td>RT {{ $r->rt->kode_rt }}</td>
                                    <td class="text-end">{{ $r->lunas }} / {{ $r->total_kk }}</td>
                                    <td class="text-end">{{ $rp($r->terkumpul) }}</td>
                                    <td class="text-end text-success">{{ $rp($r->disetor) }}@if($r->menunggu > 0) <span class="text-warning">(+{{ $rp($r->menunggu) }})</span>@endif</td>
                                    <td class="text-end {{ $r->sisa > 0 ? 'text-danger fw-semibold' : '' }}">{{ $rp($r->sisa) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Konfirmasi --}}
    @if($bisaKelola)
    <div class="tab-pane fade" id="tabKonfirmasi" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3">
                @forelse($menunggu as $s)
                    <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:#fff8ef;">
                        <div>
                            <div class="fw-semibold small">RT {{ $s->rt->kode_rt }} &bull; {{ $s->periode_label }}</div>
                            <div class="small text-muted">{{ $rp($s->nominal) }} &bull; {{ ucfirst($s->metode) }} &bull; {{ $s->tanggal_setor->format('d M Y') }}</div>
                        </div>
                        <button type="button" class="btn btn-primary rounded-pill px-3 py-1 small" data-bs-toggle="modal" data-bs-target="#modalSetoran{{ $s->id }}" data-testid="periksa-setoran"><i class="bi bi-search me-1"></i>Periksa</button>
                    </div>

                    <div class="modal fade" id="modalSetoran{{ $s->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow" style="border-radius:16px;">
                                <div class="modal-header border-0 px-4 pt-4 pb-0">
                                    <h5 class="modal-title fw-bold">Detail Setoran RT {{ $s->rt->kode_rt }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body px-4">
                                    <div class="bg-light rounded-3 p-3 small mb-3">
                                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">Setoran untuk</span><span class="text-end">{{ $s->periode_label }}</span></div>
                                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">Total</span><strong class="text-success">{{ $rp($s->nominal) }}</strong></div>
                                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">Tanggal setor</span><span>{{ $s->tanggal_setor->format('d M Y') }}</span></div>
                                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">Metode</span><span>{{ ucfirst($s->metode) }}</span></div>
                                        <div class="d-flex justify-content-between"><span class="text-muted">Dicatat oleh</span><span>{{ $s->pembuat->username ?? '-' }}</span></div>
                                        @if($s->catatan)<div class="mt-2 pt-2 border-top"><span class="text-muted">Catatan:</span> {{ $s->catatan }}</div>@endif
                                    </div>
                                    @if($s->rows->count() > 0)
                                        <div class="table-responsive mb-3">
                                            <table class="table table-sm small mb-0" data-testid="rincian-setoran">
                                                <thead><tr class="text-muted"><th>{{ $s->jenis === 'lainnya' ? 'Iuran' : 'Bulan' }}</th><th class="text-end">Nominal</th></tr></thead>
                                                <tbody>
                                                @foreach($s->rows as $r)
                                                    <tr><td>{{ $r->tagihan_id ? ($r->tagihan->judul ?? '-') : $namaBulan[$r->periode_bulan] . ' ' . $r->periode_tahun }}</td><td class="text-end">{{ $rp($r->nominal) }}</td></tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                    @if($s->bukti_url)
                                        <div class="text-center mb-3"><a href="{{ $s->bukti_url }}" target="_blank" rel="noopener"><img src="{{ $s->bukti_url }}" alt="Bukti setoran" class="img-fluid rounded border" style="max-height:300px;object-fit:contain;"></a></div>
                                    @else
                                        <div class="text-muted small text-center mb-3">Tidak ada bukti dilampirkan.</div>
                                    @endif

                                    <form action="{{ route('opkeuangan.setoran.tolak', $s->id) }}" method="POST" id="formTolakSetoran{{ $s->id }}" class="d-none">
                                        @csrf @method('PUT')
                                        <label class="form-label fw-semibold" for="alasanSetoran{{ $s->id }}">Alasan penolakan</label>
                                        <textarea id="alasanSetoran{{ $s->id }}" name="alasan" rows="3" maxlength="500" class="form-control" placeholder="Contoh: Nominal di bukti tidak sesuai" required></textarea>
                                        <div class="d-flex justify-content-end gap-2 mt-3">
                                            <button type="button" class="btn btn-light" onclick="toggleTolakSetoran('{{ $s->id }}', false)">Batal</button>
                                            <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer border-0 px-4 pb-4 pt-0" id="aksiSetoran{{ $s->id }}">
                                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" onclick="toggleTolakSetoran('{{ $s->id }}', true)"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                                    <form action="{{ route('opkeuangan.setoran.konfirmasi', $s->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button class="btn btn-success rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Konfirmasi</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada setoran yang menunggu konfirmasi.</div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- Riwayat --}}
    <div class="tab-pane fade" id="tabRiwayat" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3">
                @forelse($riwayat as $s)
                    @php [$lbl, $wrn] = $badgeRiwayat[$s->status] ?? [$s->status, 'secondary']; @endphp
                    <div class="py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-semibold small">RT {{ $s->rt->kode_rt }} &bull; {{ $s->periode_label }} &bull; {{ $rp($s->nominal) }}</div>
                                @if($s->rows->count() > 1)<div class="text-muted" style="font-size:.75rem;">{{ $s->rows->map(fn ($r) => ($r->tagihan_id ? ($r->tagihan->judul ?? '-') : $namaBulan[$r->periode_bulan]) . ' ' . $rp($r->nominal))->implode(' | ') }}</div>@endif
                                <div class="text-muted" style="font-size:.75rem;">{{ $s->tanggal_setor->format('d M Y') }} &bull; {{ ucfirst($s->metode) }}@if($s->bukti_url) &bull; <a href="{{ $s->bukti_url }}" target="_blank" rel="noopener">Lihat bukti</a>@endif</div>
                                @if(in_array($s->status, ['ditolak', 'dibatalkan']) && $s->alasan)<div class="small text-danger mt-1">Alasan: {{ $s->alasan }}</div>@endif
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $wrn }} bg-opacity-10 text-{{ $wrn }}">{{ $lbl }}</span>
                                @if($bisaKelola && $s->status === 'dikonfirmasi')
                                    <div class="mt-1"><button type="button" class="btn btn-sm btn-light text-danger py-0 px-2" data-bs-toggle="modal" data-bs-target="#modalBatalSetoran{{ $s->id }}" aria-label="Batalkan setoran" title="Batalkan setoran"><i class="bi bi-arrow-counterclockwise"></i></button></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($bisaKelola && $s->status === 'dikonfirmasi')
                        <div class="modal fade" id="modalBatalSetoran{{ $s->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form class="modal-content border-0 shadow" style="border-radius:16px;" method="POST" action="{{ route('opkeuangan.setoran.batalkan', $s->id) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                                        <h5 class="modal-title fw-bold">Batalkan Setoran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body px-4">
                                        <p class="small">Setoran RT {{ $s->rt->kode_rt }} {{ $s->periode_label }} ({{ $rp($s->nominal) }}) akan dibatalkan seluruhnya. Transaksi di kas RT dan kas RW ikut dihapus.</p>
                                        <label class="form-label fw-semibold" for="alasanBatal{{ $s->id }}">Alasan</label>
                                        <textarea id="alasanBatal{{ $s->id }}" name="alasan" rows="2" maxlength="500" class="form-control" required></textarea>
                                    </div>
                                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-danger">Batalkan Setoran</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-muted small mb-0">Belum ada riwayat setoran pada tahun {{ $tahun }}.</p>
                @endforelse
                @if($riwayat->hasPages())<div class="mt-3">{{ $riwayat->links() }}</div>@endif
            </div>
        </div>
    </div>
</div>

@if($bisaKelola)
{{-- Pengaturan nominal setoran per KK --}}
<div class="modal fade" id="modalPengaturanSetoran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content border-0 shadow" style="border-radius:16px;" method="POST" action="{{ route('opkeuangan.setoran.pengaturan') }}">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Nominal Setoran per KK {{ $tahun }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body px-4">
                @php $bolehUbah = $bulanMulaiTerawal <= 12; $namaPenuh = \App\Services\IuranService::BULAN; @endphp
                @if(! $bolehUbah)
                    <div class="alert alert-secondary border-0 small">Nominal setoran {{ $tahun }} tidak dapat diubah lagi karena seluruh bulan tahun ini sudah memiliki setoran. Atur nominal pada tahun berikutnya.</div>
                @endif
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted" for="setoranDefault">Nominal untuk semua RT (Rp per KK lunas per bulan)</label>
                    <input type="text" inputmode="numeric" data-money id="setoranDefault" name="default" class="form-control setoran-input"
                           value="{{ number_format($pengaturan['default'] ?? 0, 0, ',', '.') }}" data-awal="{{ (int) ($pengaturan['default'] ?? 0) }}" {{ $bolehUbah ? '' : 'readonly' }} required>
                    <div class="form-text">Nominal yang berlaku saat ini. Perubahan hanya berlaku mulai bulan yang Anda pilih dan tidak mengubah bulan sebelumnya.</div>
                </div>
                <div class="small fw-bold text-muted mb-1">Khusus RT tertentu (opsional)</div>
                <div class="form-text mb-2">Kosongkan agar RT mengikuti nominal untuk semua RT.</div>
                @foreach($rekap['baris'] as $r)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <label class="small flex-shrink-0" style="width:60px;" for="setoranRt{{ $r->rt->id }}">RT {{ $r->rt->kode_rt }}</label>
                        <input type="text" inputmode="numeric" data-money id="setoranRt{{ $r->rt->id }}" name="per_rt[{{ $r->rt->id }}]" class="form-control form-control-sm setoran-input"
                               placeholder="ikut default" data-awal="{{ isset($pengaturan['per_rt'][$r->rt->id]) ? (int) $pengaturan['per_rt'][$r->rt->id] : '' }}" {{ $bolehUbah ? '' : 'readonly' }}
                               value="{{ isset($pengaturan['per_rt'][$r->rt->id]) ? number_format($pengaturan['per_rt'][$r->rt->id], 0, ',', '.') : '' }}">
                    </div>
                @endforeach

                @if(!empty($riwayatPengaturan['default']) || !empty($riwayatPengaturan['per_rt']))
                    <div class="mt-3">
                        <div class="small fw-bold text-muted mb-1">Riwayat nominal {{ $tahun }}</div>
                        @foreach($riwayatPengaturan['default'] as $i => $rw)
                            @php $selesai = $riwayatPengaturan['default'][$i + 1]['bulan'] ?? null; @endphp
                            <div class="d-flex justify-content-between small py-1 border-bottom">
                                <span class="text-muted">Semua RT: {{ $namaPenuh[$rw['bulan']] }} @if($selesai) s.d. {{ $namaPenuh[$selesai - 1] }} @else dan seterusnya @endif</span>
                                <span class="fw-semibold">Rp {{ number_format($rw['nominal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        @foreach($riwayatPengaturan['per_rt'] as $rtId => $daftar)
                            @php $kodeRt = collect($rekap['baris'])->first(fn ($x) => $x->rt->id === $rtId)?->rt->kode_rt ?? '?'; @endphp
                            @foreach($daftar as $i => $rw)
                                @php $selesai = $daftar[$i + 1]['bulan'] ?? null; @endphp
                                <div class="d-flex justify-content-between small py-1 border-bottom">
                                    <span class="text-muted">RT {{ $kodeRt }}: {{ $namaPenuh[$rw['bulan']] }} @if($selesai) s.d. {{ $namaPenuh[$selesai - 1] }} @else dan seterusnya @endif</span>
                                    <span class="fw-semibold">{{ $rw['nominal'] === null ? 'ikut semua RT' : 'Rp ' . number_format($rw['nominal'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endif

                @if($bolehUbah)
                    <div id="panelUbahSetoran" class="d-none border border-warning rounded-3 p-3 mt-3 bg-warning bg-opacity-10" data-testid="panel-ubah-setoran">
                        <div class="fw-bold small mb-2"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>{{ $pengaturanAwal ? 'Mulai berlaku nominal setoran' : 'Konfirmasi perubahan nominal' }}</div>
                        <label class="form-label small fw-bold text-muted" for="selectBerlakuSetoran">Nominal berlaku mulai bulan</label>
                        <select name="berlaku_mulai" id="selectBerlakuSetoran" class="form-select form-select-sm mb-2">
                            @for($b = $bulanMulaiTerawal; $b <= 12; $b++)
                                <option value="{{ $b }}">{{ $namaPenuh[$b] }} {{ $tahun }}</option>
                            @endfor
                        </select>
                        <div id="ringkasanSetoran" class="small mb-2"></div>
                        @unless($pengaturanAwal)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="konfirmasi_cutoff" value="1" id="cekKonfirmasiSetoran">
                                <label class="form-check-label small" for="cekKonfirmasiSetoran">Saya mengerti dan mengonfirmasi perubahan ini berlaku mulai bulan yang dipilih (cut off), tanpa mengubah kewajiban setoran bulan sebelumnya.</label>
                            </div>
                        @endunless
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (location.hash === '#tabRiwayat') {
        const tombol = document.querySelector('[data-bs-target="#tabRiwayat"]');
        if (tombol) bootstrap.Tab.getOrCreateInstance(tombol).show();
    }
});

function toggleTolakSetoran(id, tampil) {
    document.getElementById('formTolakSetoran' + id).classList.toggle('d-none', !tampil);
    document.getElementById('aksiSetoran' + id).classList.toggle('d-none', tampil);
    if (tampil) document.getElementById('alasanSetoran' + id).focus();
}

// Konfirmasi cut off nominal setoran: panel muncul saat ada nominal yang diubah.
(function () {
    const modal = document.getElementById('modalPengaturanSetoran');
    const panel = document.getElementById('panelUbahSetoran');
    if (!modal || !panel) return;

    const bulan = @json(\App\Services\IuranService::BULAN);
    const tahun = {{ (int) $tahun }};
    const awalan = {{ $pengaturanAwal ? 'true' : 'false' }};
    const pilih = document.getElementById('selectBerlakuSetoran');
    const cek = document.getElementById('cekKonfirmasiSetoran');
    const ringkasan = document.getElementById('ringkasanSetoran');
    const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
    const esc = s => String(s).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
    const angka = v => { const d = (v || '').replace(/\D/g, ''); return d === '' ? null : parseInt(d, 10); };

    function perubahan() {
        const hasil = [];
        modal.querySelectorAll('.setoran-input').forEach(function (el) {
            const baru = angka(el.value);
            const lama = el.dataset.awal === '' ? null : parseInt(el.dataset.awal, 10);
            if (baru !== lama) {
                const label = el.name === 'default' ? 'Semua RT' : el.closest('div').querySelector('label').textContent.trim();
                hasil.push({ label: label, lama: lama, baru: baru });
            }
        });
        return hasil;
    }

    function render() {
        const daftar = perubahan();
        panel.classList.toggle('d-none', daftar.length === 0);
        if (cek) { cek.required = daftar.length > 0; if (!daftar.length) cek.checked = false; }
        if (!daftar.length) return;

        const mulai = parseInt(pilih.value, 10);
        const sebelum = mulai > 1 ? (mulai === 2 ? bulan[1] : bulan[1] + ' s.d. ' + bulan[mulai - 1]) : null;
        const baris = daftar.map(function (d) {
            const dari = d.lama === null ? 'ikut nominal semua RT' : rp(d.lama);
            const ke = d.baru === null ? 'ikut nominal semua RT' : rp(d.baru);
            return '<li><strong>' + esc(d.label) + '</strong>: ' + dari + ' menjadi <strong>' + ke + '</strong> per KK lunas per bulan</li>';
        }).join('');
        ringkasan.innerHTML =
            '<p class="mb-1">Perubahan nominal setoran <strong>berlaku mulai ' + esc(bulan[mulai]) + ' ' + tahun + '</strong>' + (awalan ? '.' : ' (cut off):') + '</p>' +
            '<ul class="mb-1 ps-3">' + baris + '</ul>' +
            (awalan
                ? '<p class="mb-0">Bulan sebelum ' + esc(bulan[mulai]) + ' tidak memiliki kewajiban setoran.</p>'
                : '<p class="mb-0">' + (sebelum ? 'Kewajiban setoran <strong>' + esc(sebelum) + '</strong> tetap memakai nominal lama dan tidak berubah. ' : '') +
                  'Perubahan tidak dapat berlaku surut.</p>');
    }

    modal.querySelectorAll('.setoran-input').forEach(el => el.addEventListener('input', render));
    pilih.addEventListener('change', render);
    modal.addEventListener('show.bs.modal', render);
    render();
})();</script>
@endpush
