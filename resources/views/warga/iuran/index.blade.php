@extends('layouts.global')

@section('title', 'Iuran Bulanan')

@php
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h2 class="fw-bold mb-1 fs-4" style="color:#198754;">Iuran Bulanan</h2>
        <p class="text-muted mb-0 small">Status pembayaran iuran bulanan keluarga Anda.</p>
    </div>
    <form method="GET">
        <select name="tahun" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()" aria-label="Tahun">
            @foreach($daftarTahun as $th)
                <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>Tahun {{ $th }}</option>
            @endforeach
        </select>
    </form>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;"><ul class="mb-0 list-unstyled">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if(! $master)
    <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-5 text-center">
        <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
        <p class="text-muted mb-0">Belum ada iuran yang ditetapkan RT untuk tahun {{ $tahun }}.</p>
    </div></div>
@else
    @php $adaBelumBayar = collect(range(1,12))->contains(fn ($b) => ! isset($peta[$b]) || $peta[$b]->status === 'ditolak'); @endphp
    <div class="d-flex align-items-center justify-content-between gap-2 mb-3 flex-wrap">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-success bg-opacity-10 text-success border px-3 py-2">{{ $master->nama }}</span>
            <span class="badge bg-primary bg-opacity-10 text-primary border px-3 py-2">Rp {{ number_format($master->nominal, 0, ',', '.') }} / bulan</span>
        </div>
        @if($adaBelumBayar)
            <button type="button" class="btn btn-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalBayarIuran">
                <i class="bi bi-cash-coin me-1"></i>Bayar Iuran
            </button>
        @endif
    </div>

    <div class="row g-2">
        @for($bln = 1; $bln <= 12; $bln++)
            @php $sel = $peta[$bln] ?? null; @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="border rounded-3 p-3 h-100 text-center"
                     style="background: {{ $sel && $sel->status==='lunas' ? '#D4EDDA' : ($sel && $sel->status==='menunggu' ? '#FFE5B4' : ($sel && $sel->status==='ditolak' ? '#F8D7DA' : '#fff')) }};">
                    <div class="fw-semibold">{{ $namaBulan[$bln] }}</div>
                    @if($sel && $sel->status === 'lunas')
                        <div class="small text-success mt-1"><i class="bi bi-check-circle-fill"></i> Lunas</div>
                    @elseif($sel && $sel->status === 'menunggu')
                        <div class="small text-warning mt-1"><i class="bi bi-hourglass-split"></i> Menunggu verifikasi</div>
                    @elseif($sel && $sel->status === 'ditolak')
                        <div class="small text-danger mt-1 fw-semibold"><i class="bi bi-x-circle-fill"></i> Ditolak</div>
                        @if($sel->catatan)<div class="small text-danger mt-1" style="line-height:1.2">Alasan: {{ $sel->catatan }}</div>@endif
                        <div class="small text-muted mt-1">Silakan ajukan ulang.</div>
                    @else
                        <div class="small text-muted mt-1">Belum bayar</div>
                    @endif
                </div>
            </div>
        @endfor
    </div>

    {{-- Modal Ajukan Bayar (multi-bulan) --}}
    <div class="modal fade" id="modalBayarIuran" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('warga.iuran.ajukan') }}" method="POST" enctype="multipart/form-data" id="formBayarIuran">
                    @csrf
                    <input type="hidden" name="master_iuran_id" value="{{ $master->id }}">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <h5 class="modal-title fw-bold">Bayar Iuran {{ $tahun }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pt-3">
                        <label class="form-label small fw-bold text-muted">Pilih Bulan yang Dibayar</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @for($bln = 1; $bln <= 12; $bln++)
                                @php $sel = $peta[$bln] ?? null; @endphp
                                @if(! $sel || $sel->status === 'ditolak')
                                    <div class="form-check">
                                        <input class="form-check-input bln-iuran" type="checkbox" name="bulan[]" value="{{ $bln }}" id="blnIuran{{ $bln }}" onchange="hitungTotal()">
                                        <label class="form-check-label small" for="blnIuran{{ $bln }}">{{ $namaBulan[$bln] }} <span class="text-muted">(Rp {{ number_format($nominalPerBulan[$bln] ?? 0, 0, ',', '.') }})</span></label>
                                    </div>
                                @endif
                            @endfor
                        </div>
                        <div class="alert alert-success bg-success bg-opacity-10 border-0 small mb-3">
                            Total: <strong id="totalBayar">Rp 0</strong> <span class="text-muted" id="infoNominalBulan"></span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tanggal Bayar</label>
                                <input type="date" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Metode</label>
                                <select name="metode" class="form-select" id="metodeIuran" required>
                                    <option value="transfer">Transfer</option>
                                    <option value="tunai">Tunai</option>
                                </select>
                            </div>
                        </div>

                        {{-- Info rekening / QRIS RT (untuk metode transfer) --}}
                        <div id="infoRekening" class="mb-3">
                            @if($rekeningRt)
                                <div class="border rounded-3 p-3 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted small">Rekening RT</span>
                                        <span class="badge bg-success">{{ $rekeningRt->bank }}</span>
                                    </div>
                                    <div class="fw-bold font-monospace">{{ $rekeningRt->no_rek }}</div>
                                    <div class="small text-muted">a.n. {{ $rekeningRt->nama_rek }}</div>
                                    @if($rekeningRt->qris_file)
                                        @php $cleanQris = str_replace('\\','/',$rekeningRt->qris_file); $qrisUrl = str_starts_with($cleanQris,'storage/') ? asset($cleanQris) : asset('storage/'.$cleanQris); @endphp
                                        <div class="text-center mt-2"><img src="{{ $qrisUrl }}" alt="QRIS RT" class="img-fluid" style="max-height:180px;"></div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning border-0 small mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i>Rekening RT belum diatur bendahara. Anda tetap dapat membayar tunai.</div>
                            @endif
                        </div>

                        <div class="mb-1">
                            <label class="form-label small fw-bold text-muted">Bukti Pembayaran</label>
                            <input type="file" name="bukti_file" class="form-control" accept=".jpg,.jpeg,.png">
                            <div class="form-text">Unggah bukti transfer bila membayar via transfer.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold" id="btnKirimIuran" disabled>Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
const NOMINAL_BULAN = @json($nominalPerBulan ?? []);
function hitungTotal() {
    const terpilih = Array.from(document.querySelectorAll('.bln-iuran:checked'));
    const dipilih = terpilih.length;
    const total = terpilih.reduce((jml, el) => jml + (NOMINAL_BULAN[el.value] || 0), 0);
    const info = document.getElementById('infoNominalBulan');
    if (info) info.textContent = dipilih ? '(' + dipilih + ' bulan)' : '';
    document.getElementById('totalBayar').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('btnKirimIuran').disabled = dipilih === 0;
}
document.addEventListener('DOMContentLoaded', function () {
    const metode = document.getElementById('metodeIuran');
    const info = document.getElementById('infoRekening');
    if (metode && info) {
        const toggle = () => { info.style.display = metode.value === 'transfer' ? 'block' : 'none'; };
        metode.addEventListener('change', toggle);
        toggle();
    }
});
</script>
@endpush
