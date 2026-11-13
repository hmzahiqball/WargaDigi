@extends('layouts.global')

@section('title', 'Kepengurusan / Pejabat')

@section('content')
@php
    $fmt = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->translatedFormat('d M Y') : '-';

    // Baris tabel: RW, setiap RT, lalu DKM. Jabatan null = tidak ada untuk unit itu.
    $baris = array_merge(
        [['unit' => 'RW', 'rt' => null, 'ketua' => 'ketua_rw', 'sekretaris' => 'sekretaris_rw', 'bendahara' => 'bendahara_rw']],
        $rtList->map(fn ($rt) => ['unit' => 'RT ' . $rt->kode_rt, 'rt' => $rt, 'ketua' => 'ketua_rt', 'sekretaris' => 'sekretaris_rt', 'bendahara' => 'bendahara_rt'])->all(),
        [['unit' => 'DKM', 'rt' => null, 'ketua' => null, 'sekretaris' => null, 'bendahara' => 'bendahara_dkm']]
    );
    $kolom = ['ketua' => 'Ketua', 'sekretaris' => 'Sekretaris', 'bendahara' => 'Bendahara'];
@endphp

<div class="admin-page-header">
    <h1>Kepengurusan (Pejabat)</h1>
    <p class="text-muted">Tetapkan Ketua, Sekretaris, dan Bendahara dari data penduduk. Setiap pergantian tersimpan sebagai riwayat untuk pelacakan. Nama Bendahara dan Ketua dipakai pada laporan keuangan dan resi pembayaran.</p>
</div>

@if($errors->any() && ! old('jabatan'))
    <div class="alert alert-danger mb-4" style="border-radius: 0.5rem;">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="admin-card mb-4">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0" data-testid="tabel-kepengurusan">
                <thead>
                    <tr>
                        <th>Unit</th>
                        @foreach($kolom as $nama)<th>{{ $nama }}</th>@endforeach
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($baris as $b)
                    <tr>
                        <td class="fw-semibold text-nowrap">{{ $b['unit'] }}</td>
                        @foreach($kolom as $kunci => $nama)
                            @php
                                $jabatan = $b[$kunci];
                                $aktif = $jabatan ? ($aktifMap[$jabatan . '|' . ($b['rt']->id ?? '')] ?? null) : null;
                            @endphp
                            <td>
                                @if(! $jabatan)
                                    <span class="text-muted">-</span>
                                @elseif($aktif && $aktif->penduduk)
                                    <span class="fw-semibold">{{ $aktif->penduduk->nama_lengkap }}</span>
                                    <small class="d-block text-muted">sejak {{ $fmt($aktif->mulai_menjabat) }}</small>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Belum ditetapkan</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end text-nowrap">
                            @foreach($kolom as $kunci => $nama)
                                @if($b[$kunci])
                                    <button type="button" class="btn btn-sm btn-success btn-set-pejabat" style="border-radius: 0.5rem;"
                                        data-jabatan="{{ $b[$kunci] }}" data-rt-id="{{ $b['rt']->id ?? '' }}"
                                        data-judul="Set {{ $nama }} {{ $b['unit'] }}">
                                        <i class="bi bi-person-check"></i> Set {{ $nama }}
                                    </button>
                                @endif
                            @endforeach
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" style="border-radius: 0.5rem;" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-clock-history"></i> Riwayat
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @foreach($kolom as $kunci => $nama)
                                        @if($b[$kunci])
                                            <li><button type="button" class="dropdown-item btn-riwayat-pejabat"
                                                data-jabatan="{{ $b[$kunci] }}" data-rt-id="{{ $b['rt']->id ?? '' }}"
                                                data-judul="Riwayat {{ $nama }} {{ $b['unit'] }}">{{ $nama }}</button></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal bersama: Set pejabat --}}
<div class="modal fade" id="modalSetPejabat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" style="border-radius: 0.75rem;" method="POST" action="{{ route('admin.pejabat.store') }}">
            @csrf
            <input type="hidden" name="jabatan" id="setJabatan" value="{{ old('jabatan') }}">
            <input type="hidden" name="rt_id" id="setRtId" value="{{ old('rt_id') }}">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2 text-success"></i><span id="setJudul">Set Pejabat</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                @if($errors->any() && old('jabatan'))
                    <div class="alert alert-danger small" style="border-radius: 0.5rem;">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label small fw-semibold" for="setPenduduk">Pilih Penduduk <span class="text-danger">*</span></label>
                    <select name="penduduk_nik" id="setPenduduk" class="form-select admin-input" required></select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Mulai Menjabat <span class="text-danger">*</span></label>
                        <input type="date" name="mulai_menjabat" class="form-control admin-input" value="{{ old('mulai_menjabat', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tanggal Selesai Menjabat</label>
                        <input type="date" name="selesai_menjabat" class="form-control admin-input" value="{{ old('selesai_menjabat') }}">
                    </div>
                </div>
                <div class="alert alert-light border small mt-3 mb-0" style="border-radius: 0.5rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    Menyimpan akan membuat data pejabat baru dan menjadikannya aktif. Pejabat sebelumnya dipindah ke riwayat tanpa mengubah tanggalnya.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal bersama: Riwayat (dimuat saat dibuka) --}}
<div class="modal fade" id="modalRiwayatPejabat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0.75rem;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2 text-success"></i><span id="riwayatJudul">Riwayat</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="riwayatIsi"><p class="text-muted small mb-0">Memuat...</p></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const PENDUDUK = @json($penduduk);            // [nik, nama, rt_id]
    const URL_RIWAYAT = @json(route('admin.pejabat.riwayat'));
    const modalSet = document.getElementById('modalSetPejabat');
    const selPenduduk = document.getElementById('setPenduduk');
    const oldNik = @json(old('penduduk_nik'));

    // Isi pilihan penduduk: untuk jabatan tingkat RT dibatasi pada warga RT itu.
    function isiPilihan(rtId, pilih) {
        if (selPenduduk.tomselect) selPenduduk.tomselect.destroy();
        selPenduduk.innerHTML = '<option value="">Cari nama atau NIK...</option>';
        PENDUDUK.filter(p => !rtId || p[2] === rtId).forEach(function (p) {
            const opt = document.createElement('option');
            opt.value = p[0];
            opt.textContent = p[1] + ' - ' + p[0];
            if (pilih && pilih === p[0]) opt.selected = true;
            selPenduduk.appendChild(opt);
        });
        new TomSelect(selPenduduk, { allowEmptyOption: false, placeholder: 'Cari nama atau NIK...', controlInput: '<input class="dropdown-input">' });
    }

    function bukaSet(jabatan, rtId, judul, pilih) {
        document.getElementById('setJabatan').value = jabatan;
        document.getElementById('setRtId').value = rtId || '';
        document.getElementById('setJudul').textContent = judul;
        isiPilihan(rtId || '', pilih || '');
        bootstrap.Modal.getOrCreateInstance(modalSet).show();
    }

    // Menu Riwayat memakai posisi fixed agar tidak terpotong oleh kontainer tabel yang dapat digulir.
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (el) {
        new bootstrap.Dropdown(el, { popperConfig: { strategy: 'fixed' } });
    });

    document.querySelectorAll('.btn-set-pejabat').forEach(function (btn) {
        btn.addEventListener('click', () => bukaSet(btn.dataset.jabatan, btn.dataset.rtId, btn.dataset.judul, ''));
    });

    document.querySelectorAll('.btn-riwayat-pejabat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const isi = document.getElementById('riwayatIsi');
            document.getElementById('riwayatJudul').textContent = btn.dataset.judul;
            isi.innerHTML = '<p class="text-muted small mb-0">Memuat...</p>';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalRiwayatPejabat')).show();
            const query = new URLSearchParams({ jabatan: btn.dataset.jabatan, rt_id: btn.dataset.rtId || '' });
            fetch(URL_RIWAYAT + '?' + query.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.text() : Promise.reject())
                .then(html => { isi.innerHTML = html; })
                .catch(() => { isi.innerHTML = '<p class="text-danger small mb-0">Riwayat gagal dimuat. Coba lagi.</p>'; });
        });
    });

    @if($errors->any() && old('jabatan'))
        // Validasi gagal: buka kembali modal dengan isian sebelumnya.
        bukaSet(@json(old('jabatan')), @json(old('rt_id')), @json('Set ' . \App\Models\Pejabat::labelJabatan((string) old('jabatan'))), oldNik);
    @endif
});
</script>
@endpush