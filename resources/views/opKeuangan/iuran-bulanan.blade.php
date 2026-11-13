@extends('layouts.global')

@section('title', 'Iuran Bulanan Warga')

@php
    $namaBulan = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h2 class="fw-bold mb-1 fs-4" style="color:#198754;">Iuran Bulanan Warga</h2>
        <p class="text-muted mb-0 small">Kelola iuran wajib bulanan per KK. Centang pembayaran, otomatis masuk ke kas RT.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form method="GET" class="d-flex">
            <select name="tahun" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()" aria-label="Tahun">
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>{{ $th }}</option>
                @endforeach
            </select>
        </form>
        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalMasterIuran">
            <i class="bi bi-gear me-1"></i>Pengaturan
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;"><ul class="mb-0 list-unstyled">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if(! $master)
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-5 text-center">
            <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
            <h5 class="fw-bold">Belum ada iuran untuk tahun {{ $tahun }}</h5>
            <p class="text-muted">Atur nominal dan nama iuran terlebih dahulu untuk mulai mencatat pembayaran.</p>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalMasterIuran"><i class="bi bi-plus-lg me-1"></i>Atur Iuran {{ $tahun }}</button>
        </div>
    </div>
@else
    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap small">
        <span class="badge bg-success bg-opacity-10 text-success border px-2 py-1 fw-semibold">{{ $master->nama }}</span>
        <span class="badge bg-primary bg-opacity-10 text-primary border px-2 py-1 fw-semibold">Rp {{ number_format($master->nominal, 0, ',', '.') }} / bulan</span>
        @if(! $master->aktif)<span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">Nonaktif</span>@endif
    </div>

    <ul class="nav nav-pills gap-2 mb-3 flex-nowrap" role="tablist">
        <li class="nav-item"><button class="nav-link active rounded-pill px-3 py-1 small text-nowrap" data-bs-toggle="pill" data-bs-target="#tabInput" type="button"><i class="bi bi-grid-3x3-gap me-1"></i>Rekap &amp; Input</button></li>
        <li class="nav-item"><button class="nav-link rounded-pill px-3 py-1 small text-nowrap" data-bs-toggle="pill" data-bs-target="#tabVerifikasi" type="button"><i class="bi bi-hourglass-split me-1"></i>Verifikasi @if($pengajuanWarga->count())<span class="badge rounded-pill bg-warning text-dark ms-1">{{ $pengajuanWarga->count() }}</span>@endif</button></li>
    </ul>

    <div class="tab-content">
        {{-- Tab Input/Rekap --}}
        <div class="tab-pane fade show active" id="tabInput" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <form method="GET" class="mb-3">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <div class="input-group" style="max-width:360px;">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="Cari warga / No. KK...">
                            @if(request('cari'))<a href="{{ route('opkeuangan.iuran-bulanan.index', ['tahun'=>$tahun]) }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>@endif
                        </div>
                    </form>

                    <div class="d-flex align-items-center gap-3 mb-2 small text-muted flex-wrap">
                        <span><span class="d-inline-block rounded me-1" style="width:14px;height:14px;background:#D4EDDA;border:1px solid #bbb;"></span>Lunas</span>
                        <span><span class="d-inline-block rounded me-1" style="width:14px;height:14px;background:#FFE5B4;border:1px solid #bbb;"></span>Menunggu verifikasi</span>
                        <span><span class="d-inline-block rounded me-1" style="width:14px;height:14px;background:#f1f3f5;border:1px solid #bbb;"></span>Belum bayar</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center mb-0" style="font-size:0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start" style="min-width:180px;position:sticky;left:0;background:#f8f9fa;z-index:2;">WARGA</th>
                                    @foreach($namaBulan as $i => $b)<th>{{ $b }}</th>@endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($keluarga as $k)
                                    @php
                                        $kepala = optional($k->kepalaKeluarga)->nama_lengkap ?? 'KK ' . $k->no_kk;
                                        $totalKk = 0;
                                    @endphp
                                    <tr>
                                        <td class="text-start" style="position:sticky;left:0;background:#fff;z-index:1;">
                                            <div class="fw-semibold text-truncate" style="max-width:200px;">{{ $kepala }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $k->no_kk }}</div>
                                        </td>
                                        @for($bln = 1; $bln <= 12; $bln++)
                                            @php $sel = $peta[$k->id][$bln] ?? null; @endphp
                                            @if($sel && $sel->status === 'lunas')
                                                @php $totalKk += $master->nominalUntukBulan($bln); @endphp
                                                <td style="background:#D4EDDA;cursor:pointer;" title="Lunas - klik untuk batalkan"
                                                    onclick="batalkanSel('{{ $sel->id }}', '{{ $namaBulan[$bln] }} {{ $tahun }}', '{{ addslashes($kepala) }}')">
                                                    <i class="bi bi-check-lg text-success"></i>
                                                </td>
                                            @elseif($sel && $sel->status === 'menunggu')
                                                <td style="background:#FFE5B4;" title="Menunggu verifikasi"><i class="bi bi-hourglass-split text-warning"></i></td>
                                            @else
                                                <td style="cursor:pointer;background:#f8f9fa;"
                                                    onclick="bukaBayar('{{ $k->id }}', '{{ addslashes($kepala) }}', {{ $bln }})" title="Klik untuk catat bayar"></td>
                                            @endif
                                        @endfor
                                        <td class="fw-bold">Rp {{ number_format($totalKk, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="14" class="py-4 text-muted">Tidak ada data warga.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Verifikasi --}}
        <div class="tab-pane fade" id="tabVerifikasi" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    @forelse($pengajuanWarga as $grup)
                        @php
                            $p = $grup->wakil;
                            $kepala = optional($p->keluarga->kepalaKeluarga)->nama_lengkap ?? 'KK ' . optional($p->keluarga)->no_kk;
                            $labelBulan = $grup->bulan->map(fn ($b) => \App\Services\IuranService::BULAN[$b] ?? $b)->implode(', ');
                            $total = $grup->bulan->sum(fn ($b) => $master->nominalUntukBulan((int) $b));
                        @endphp
                        <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:#fff8ef;">
                            <div>
                                <div class="fw-semibold small">{{ $kepala }}</div>
                                <div class="text-muted" style="font-size:.75rem;">KK {{ optional($p->keluarga)->no_kk }}</div>
                                <div class="small text-muted">{{ $grup->jumlah }} bulan: {{ $labelBulan }} {{ $tahun }} &bull; Total Rp {{ number_format($total, 0, ',', '.') }} &bull; {{ ucfirst($p->metode ?? '-') }} &bull; {{ optional($p->tanggal_bayar)->format('d M Y') }}</div>
                                @if($p->bukti_file)<a href="{{ asset('storage/' . $p->bukti_file) }}" target="_blank" class="small text-decoration-none"><i class="bi bi-paperclip"></i> Lihat bukti</a>@endif
                            </div>
                            <button type="button" class="btn btn-primary rounded-pill px-3 py-1 small" data-bs-toggle="modal" data-bs-target="#modalVerif{{ $p->id }}" data-testid="periksa-pengajuan">
                                <i class="bi bi-search me-1"></i>Periksa
                            </button>
                        </div>

                        {{-- Dialog detail pembayaran: setujui atau tolak (dengan alasan) --}}
                        <div class="modal fade" id="modalVerif{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content border-0 shadow" style="border-radius:16px;">
                                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                                        <h5 class="modal-title fw-bold">Detail Pembayaran Iuran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body px-4">
                                        <div class="bg-light rounded-3 p-3 small mb-3">
                                            <div class="d-flex justify-content-between mb-1"><span class="text-muted">Warga</span><strong class="text-end">{{ $kepala }}</strong></div>
                                            <div class="d-flex justify-content-between mb-1"><span class="text-muted">No. KK</span><span>{{ optional($p->keluarga)->no_kk }}</span></div>
                                            <div class="d-flex justify-content-between mb-1"><span class="text-muted">Periode</span><span class="text-end">{{ $labelBulan }} {{ $tahun }}</span></div>
                                            <div class="d-flex justify-content-between mb-1"><span class="text-muted">Jumlah bulan</span><span>{{ $grup->jumlah }}</span></div>
                                            <div class="d-flex justify-content-between mb-1"><span class="text-muted">Total</span><strong class="text-success">Rp {{ number_format($total, 0, ',', '.') }}</strong></div>
                                            <div class="d-flex justify-content-between mb-1"><span class="text-muted">Metode</span><span>{{ ucfirst($p->metode ?? '-') }}</span></div>
                                            <div class="d-flex justify-content-between"><span class="text-muted">Tanggal bayar</span><span>{{ optional($p->tanggal_bayar)->format('d M Y') }}</span></div>
                                            @if($p->catatan)<div class="mt-2 pt-2 border-top"><span class="text-muted">Catatan warga:</span> {{ $p->catatan }}</div>@endif
                                        </div>
                                        @if($p->bukti_file)
                                            <div class="text-center mb-3">
                                                <a href="{{ asset('storage/' . $p->bukti_file) }}" target="_blank" rel="noopener">
                                                    <img src="{{ asset('storage/' . $p->bukti_file) }}" alt="Bukti pembayaran" class="img-fluid rounded border" style="max-height:320px;object-fit:contain;">
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-muted small text-center mb-3">Tidak ada bukti pembayaran dilampirkan.</div>
                                        @endif

                                        <form action="{{ route('opkeuangan.iuran-bulanan.tolak', $p->id) }}" method="POST" id="formTolak{{ $p->id }}" class="d-none">
                                            @csrf @method('PUT')
                                            <label class="form-label fw-semibold" for="alasan{{ $p->id }}">Alasan penolakan</label>
                                            <textarea id="alasan{{ $p->id }}" name="alasan" rows="3" maxlength="500" class="form-control" placeholder="Contoh: Bukti transfer buram atau nominal tidak sesuai" required></textarea>
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button type="button" class="btn btn-light" onclick="toggleTolak('{{ $p->id }}', false)">Batal</button>
                                                <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer border-0 px-4 pb-4 pt-0" id="aksiVerif{{ $p->id }}">
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-4" onclick="toggleTolak('{{ $p->id }}', true)"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                                        <form action="{{ route('opkeuangan.iuran-bulanan.setujui', $p->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <button class="btn btn-success rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Setujui</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada pengajuan pembayaran dari warga.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Bayar (ceklis bendahara) --}}
    <div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('opkeuangan.iuran-bulanan.lunas') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="master_iuran_id" value="{{ $master->id }}">
                    <input type="hidden" name="keluarga_id" id="bayarKeluargaId">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <h5 class="modal-title fw-bold">Catat Pembayaran Iuran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pt-3">
                        <p class="mb-2">Warga: <strong id="bayarNamaWarga"></strong></p>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Bulan yang dibayar</label>
                            <div class="d-flex flex-wrap gap-2" id="bayarBulanWrap">
                                @foreach($namaBulan as $i => $b)
                                    <div class="form-check">
                                        <input class="form-check-input bayar-bulan" type="checkbox" name="bulan[]" value="{{ $i }}" id="bayarBulan{{ $i }}">
                                        <label class="form-check-label small" for="bayarBulan{{ $i }}">{{ $b }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text">Centang lebih dari satu bulan untuk pembayaran di muka.</div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Tanggal Bayar</label>
                                <input type="date" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Metode</label>
                                <select name="metode" class="form-select" required>
                                    <option value="tunai">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Bukti (opsional)</label>
                                <input type="file" name="bukti_file" class="form-control" accept=".jpg,.jpeg,.png">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Catatan (opsional)</label>
                                <input type="text" name="catatan" class="form-control" maxlength="500">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan &amp; Masuk Kas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Form batal (tersembunyi) --}}
    <form id="formBatal" method="POST" class="d-none">@csrf @method('DELETE')</form>
@endif

{{-- Modal Pengaturan Master --}}
<div class="modal fade" id="modalMasterIuran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:16px;">
            <form action="{{ route('opkeuangan.iuran-bulanan.master') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">Pengaturan Iuran {{ $tahun }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Iuran</label>
                        <input type="text" name="nama" class="form-control" maxlength="100" value="{{ $master->nama ?? 'Iuran Bulanan Warga' }}" required>
                    </div>
                    @php $nominalSaatIni = $master ? $master->nominal : 0; $bolehUbahNominal = ! $master || $bulanMulaiTerawal <= 12; @endphp
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted" for="inputNominalMaster">Nominal per Bulan (per KK)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="nominal" id="inputNominalMaster" class="form-control" data-money inputmode="numeric"
                                   value="{{ $master ? number_format($nominalSaatIni, 0, ',', '.') : '' }}" placeholder="Contoh: 90.000"
                                   data-awal="{{ $nominalSaatIni }}" {{ $bolehUbahNominal ? '' : 'readonly' }} required>
                        </div>
                        @if($master)
                            <div class="form-text">Nominal yang berlaku saat ini. Perubahan hanya berlaku untuk bulan berikutnya, tidak mengubah bulan yang sudah lewat.</div>
                        @endif
                    </div>

                    @if($master)
                        @if(! $bolehUbahNominal)
                            <div class="alert alert-secondary border-0 small">Nominal iuran {{ $tahun }} tidak dapat diubah lagi karena seluruh bulan tahun ini sudah memiliki pembayaran atau laporan. Atur nominal baru pada iuran tahun berikutnya.</div>
                        @endif

                        @if($riwayatNominal->count() > 0)
                            <div class="mb-3">
                                <div class="small fw-bold text-muted mb-1">Riwayat nominal {{ $tahun }}</div>
                                @foreach($riwayatNominal as $i => $rw)
                                    @php
                                        $selesai = optional($riwayatNominal->get($i + 1))->berlaku_mulai_bulan;
                                        $namaPenuh = \App\Services\IuranService::BULAN;
                                    @endphp
                                    <div class="d-flex justify-content-between small py-1 border-bottom">
                                        <span class="text-muted">
                                            {{ $namaPenuh[$rw->berlaku_mulai_bulan] }}
                                            @if($selesai) s.d. {{ $namaPenuh[$selesai - 1] }} @else dan seterusnya @endif
                                        </span>
                                        <span class="fw-semibold">Rp {{ number_format($rw->nominal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($bolehUbahNominal)
                        {{-- Muncul hanya saat nominal diubah: pilih bulan mulai berlaku + konfirmasi cut off --}}
                        <div id="panelUbahNominal" class="d-none border border-warning rounded-3 p-3 mb-3 bg-warning bg-opacity-10" data-testid="panel-ubah-nominal">
                            <div class="fw-bold small mb-2"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>Konfirmasi perubahan nominal</div>
                            <label class="form-label small fw-bold text-muted" for="selectBerlakuMulai">Nominal baru berlaku mulai bulan</label>
                            <select name="berlaku_mulai" id="selectBerlakuMulai" class="form-select form-select-sm mb-2">
                                @for($b = $bulanMulaiTerawal; $b <= 12; $b++)
                                    <option value="{{ $b }}">{{ \App\Services\IuranService::BULAN[$b] }} {{ $tahun }}</option>
                                @endfor
                            </select>
                            @if($bulanTerkunci > 0)
                                <div class="form-text mb-2">Bulan sampai {{ \App\Services\IuranService::BULAN[$bulanTerkunci] }} tidak dapat dipilih karena sudah ada pembayaran atau laporan.</div>
                            @endif
                            <div id="ringkasanCutoff" class="small mb-2"></div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="konfirmasi_cutoff" value="1" id="cekKonfirmasiCutoff">
                                <label class="form-check-label small" for="cekKonfirmasiCutoff">Saya mengerti dan mengonfirmasi perubahan nominal ini berlaku mulai bulan yang dipilih (cut off), tanpa mengubah bulan sebelumnya.</label>
                            </div>
                        </div>
                        @endif
                    @endif
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Kategori Kas (pemasukan)</label>
                        <select name="kategori_id" class="form-select">
                            <option value="">-- Pilih kategori --</option>
                            @foreach($kategoriPemasukan as $kat)
                                <option value="{{ $kat->id }}" {{ optional($master)->kategori_id === $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Dipakai saat pembayaran dicatat ke kas RT.</div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="masterAktif" {{ !$master || $master->aktif ? 'checked' : '' }}>
                        <label class="form-check-label small" for="masterAktif">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bukaBayar(keluargaId, nama, bulan) {
    document.getElementById('bayarKeluargaId').value = keluargaId;
    document.getElementById('bayarNamaWarga').textContent = nama;
    document.querySelectorAll('.bayar-bulan').forEach(c => c.checked = false);
    const cb = document.getElementById('bayarBulan' + bulan);
    if (cb) cb.checked = true;
    new bootstrap.Modal(document.getElementById('modalBayar')).show();
}

// Konfirmasi cut off: tampil otomatis saat nominal diubah dari nilai yang berlaku sekarang.
(function () {
    const input = document.getElementById('inputNominalMaster');
    const panel = document.getElementById('panelUbahNominal');
    if (!input || !panel) return;

    const bulan = @json(\App\Services\IuranService::BULAN);
    const tahun = {{ (int) $tahun }};
    const awal = parseInt(input.dataset.awal || '0', 10);
    const pilih = document.getElementById('selectBerlakuMulai');
    const cek = document.getElementById('cekKonfirmasiCutoff');
    const ringkasan = document.getElementById('ringkasanCutoff');
    const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
    const esc = s => String(s).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));

    function nominalBaru() { return parseInt((input.value || '').replace(/\D/g, '') || '0', 10); }

    function render() {
        const baru = nominalBaru();
        const berubah = awal > 0 && baru > 0 && baru !== awal;
        panel.classList.toggle('d-none', !berubah);
        cek.required = berubah;
        if (!berubah) { cek.checked = false; return; }

        const mulai = parseInt(pilih.value, 10);
        const sebelum = mulai > 1 ? (mulai === 2 ? bulan[1] : bulan[1] + ' s.d. ' + bulan[mulai - 1]) : null;
        const naik = baru > awal ? 'naik' : 'turun';
        ringkasan.innerHTML =
            '<p class="mb-1">Nominal iuran akan ' + naik + ' dari <strong>' + rp(awal) + '</strong> menjadi <strong>' + rp(baru) +
            '</strong> per KK per bulan, <strong>berlaku mulai ' + esc(bulan[mulai]) + ' ' + tahun + '</strong> (cut off).</p>' +
            '<ul class="mb-1 ps-3">' +
            (sebelum ? '<li>Iuran <strong>' + esc(sebelum) + '</strong> tetap ' + rp(awal) + '. Pembayaran, catatan kas, rekap, dan perhitungan setoran ke RW pada bulan-bulan itu tidak berubah.</li>' : '') +
            '<li>Iuran <strong>' + esc(bulan[mulai]) + ' ' + tahun + ' dan seterusnya</strong> memakai ' + rp(baru) + '.</li>' +
            '<li>Perubahan tidak dapat berlaku surut. Pengajuan warga yang masih menunggu verifikasi mengikuti nominal bulan yang diajukan.</li>' +
            '</ul>';
    }

    input.addEventListener('input', render);
    pilih.addEventListener('change', render);
    document.getElementById('modalMasterIuran').addEventListener('show.bs.modal', render);
    render();
})();

function toggleTolak(id, tampil) {
    document.getElementById('formTolak' + id).classList.toggle('d-none', !tampil);
    document.getElementById('aksiVerif' + id).classList.toggle('d-none', tampil);
    if (tampil) document.getElementById('alasan' + id).focus();
}

function batalkanSel(id, periode, nama) {
    const form = document.getElementById('formBatal');
    form.action = '{{ $master ? route('opkeuangan.iuran-bulanan.batal', '__ID__') : '' }}'.replace('__ID__', id);
    window.showDeleteModal({
        prefix: 'Batalkan Pembayaran',
        itemName: periode + ' - ' + nama,
        subtitle: 'Seluruh pembayaran dalam batch ini akan dibatalkan dan transaksi kas terkait dihapus. Lanjutkan?',
        confirmButtonText: 'Ya, Batalkan',
        formId: 'formBatal',
    });
}
</script>
@endpush
