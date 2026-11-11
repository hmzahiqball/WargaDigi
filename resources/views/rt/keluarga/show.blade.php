@extends('layouts.global')

@section('title', 'Detail Keluarga')

@php
    // Susun alamat KK lengkap.
    $bagian = [];
    if (! empty($keluarga->alamat)) { $bagian[] = $keluarga->alamat; }
    $rtKode = $keluarga->rt?->kode_rt;
    if ($rtKode || $keluarga->rw) {
        $bagian[] = 'RT ' . str_pad((string) ($rtKode ?? '000'), 3, '0', STR_PAD_LEFT)
            . '/RW ' . str_pad((string) ($keluarga->rw ?? '000'), 3, '0', STR_PAD_LEFT);
    }
    if (! empty($keluarga->kelurahan)) { $bagian[] = 'Kel. ' . $keluarga->kelurahan; }
    if (! empty($keluarga->kecamatan)) { $bagian[] = 'Kec. ' . $keluarga->kecamatan; }
    if (! empty($keluarga->kabupaten)) { $bagian[] = $keluarga->kabupaten; }
    $prov = $keluarga->provinsi;
    if (! empty($keluarga->kode_pos)) { $prov = ($prov ? $prov . ' ' : '') . $keluarga->kode_pos; }
    if (! empty($prov)) { $bagian[] = $prov; }
    $alamatLengkap = implode(', ', $bagian);
@endphp

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Detail Keluarga</h3>
            <p class="text-muted mb-0">Data Kartu Keluarga dan anggota penduduk.</p>
        </div>
        <a href="{{ route('rt.penduduk.index') }}" class="btn btn-light border" style="border-radius:0.5rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Profil KK --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-house-door me-2 text-success"></i>Profil Kartu Keluarga</h6>
            <dl class="row mb-0">
                <dt class="col-sm-3">Nomor KK</dt>
                <dd class="col-sm-9" style="font-family: monospace;">{{ $keluarga->no_kk }}</dd>
                <dt class="col-sm-3">Kepala Keluarga</dt>
                <dd class="col-sm-9">{{ $keluarga->kepalaKeluarga?->nama_lengkap ?? '-' }}</dd>
                <dt class="col-sm-3">Alamat (sesuai KK)</dt>
                <dd class="col-sm-9">{{ $alamatLengkap ?: '-' }}</dd>
                <dt class="col-sm-3">Alamat Domisili</dt>
                <dd class="col-sm-9">{{ $keluarga->alamat_domisili ?: '-' }}</dd>
                <dt class="col-sm-3">Status KK</dt>
                <dd class="col-sm-9">
                    @if($keluarga->status_warga === 'tetap')
                        <span class="badge bg-success bg-opacity-10 text-success">Warga Tetap</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning">Bukan Warga Tetap</span>
                    @endif
                </dd>
                <dt class="col-sm-3">Pemilik Rumah</dt>
                <dd class="col-sm-9">{{ $keluarga->pemilik_rumah ? 'Ya' : 'Bukan' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Status akun & aktivasi --}}
    @if($akun)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-person-lock me-2 text-success"></i>Akun Keluarga</h6>
            <dl class="row mb-0">
                <dt class="col-sm-3">Username</dt>
                <dd class="col-sm-9">{{ $akun->username }}</dd>
                <dt class="col-sm-3">Status Akun</dt>
                <dd class="col-sm-9">
                    @if($akun->status_akun === 'Active')
                        <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning">Belum Diaktivasi</span>
                    @endif
                </dd>
            </dl>

            @if($akun->status_akun !== 'Active')
                <hr>
                <p class="small text-muted mb-2">
                    Akun belum diaktivasi. Bagikan link berikut kepada pemegang akun agar dapat membuat password sendiri.
                </p>
                @if($linkAktivasi)
                <div class="input-group input-group-sm mb-2">
                    <input type="text" id="linkAktivasiDetail" class="form-control bg-white" value="{{ $linkAktivasi }}" readonly>
                    <button type="button" class="btn btn-success" id="btnSalinLinkDetail"><i class="bi bi-clipboard me-1"></i> Salin</button>
                </div>
                @endif
                <form method="POST" action="{{ route('rt.keluarga.aktivasi-ulang', $keluarga->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-repeat me-1"></i> Buat Ulang Link Aktivasi
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endif

    {{-- Anggota penduduk --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-people me-2 text-success"></i>Anggota Keluarga ({{ $keluarga->anggota->count() }} jiwa)</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted small fw-semibold py-3">NAMA</th>
                            <th class="text-muted small fw-semibold py-3">NIK</th>
                            <th class="text-muted small fw-semibold py-3">L/P</th>
                            <th class="text-muted small fw-semibold py-3">HUBUNGAN</th>
                            <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keluarga->anggota as $a)
                        <tr>
                            <td class="py-3"><div class="fw-semibold text-dark">{{ $a->nama_lengkap }}</div></td>
                            <td class="py-3"><span style="font-family: monospace;">{{ $a->nik }}</span></td>
                            <td class="py-3">{{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td>
                            <td class="py-3">{{ $a->status_hubungan_keluarga }}</td>
                            <td class="py-3 text-end">
                                <a href="{{ route('rt.penduduk.show', $a->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada anggota.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var btn = document.getElementById('btnSalinLinkDetail');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var input = document.getElementById('linkAktivasiDetail');
        input.select(); input.setSelectionRange(0, 99999);
        var ok = false;
        try {
            if (navigator.clipboard) { navigator.clipboard.writeText(input.value); ok = true; }
            else { ok = document.execCommand('copy'); }
        } catch (e) { ok = false; }
        btn.innerHTML = ok ? '<i class="bi bi-check2 me-1"></i> Tersalin' : '<i class="bi bi-clipboard me-1"></i> Salin';
        setTimeout(function () { btn.innerHTML = '<i class="bi bi-clipboard me-1"></i> Salin'; }, 2000);
    });
})();
</script>
@endpush
@endsection
