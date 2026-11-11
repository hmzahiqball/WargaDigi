@extends('layouts.global')

@section('title', 'Persetujuan Pengajuan Data')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="header-icon-box bg-success bg-opacity-10 text-success rounded-3 d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;">
                <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">Verifikasi Pengajuan Data</h3>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Daftar pengajuan penambahan/perubahan data anggota keluarga.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('activation_link'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-link-45deg fs-4 text-success"></i>
            <div class="flex-grow-1">
                <div class="fw-semibold mb-1">Link Aktivasi Akun Warga</div>
                <p class="small text-muted mb-2">
                    Bagikan link berikut kepada pemegang akun baru (<strong>{{ session('activation_username') }}</strong>)
                    agar dapat membuat username &amp; password sendiri. Link berlaku sampai akun diaktifkan.
                </p>
                <div class="input-group input-group-sm">
                    <input type="text" id="linkAktivasi" class="form-control bg-white" value="{{ session('activation_link') }}" readonly>
                    <button type="button" class="btn btn-success" id="btnSalinLink"><i class="bi bi-clipboard me-1"></i> Salin</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-semibold" style="font-size: 0.85rem;">WAKTU PENGAJUAN</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 0.85rem;">KEPALA KELUARGA</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 0.85rem;">JENIS PENGAJUAN</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 0.85rem;">DETAIL AJUAN</th>
                            <th class="pe-4 py-3 text-muted fw-semibold text-end" style="font-size: 0.85rem;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($mutasi as $item)
                        <tr>
                            <td class="ps-4 py-3">
                                <span class="d-block text-dark fw-medium">{{ $item->created_at->format('d M Y') }}</span>
                                <span class="text-muted" style="font-size: 0.8rem;">{{ $item->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3">
                                <span class="d-block text-dark fw-medium">{{ $item->keluarga->kepalaKeluarga->nama_lengkap ?? 'Unknown' }}</span>
                                <span class="text-muted" style="font-size: 0.8rem;">KK: {{ $item->keluarga->no_kk }}</span>
                            </td>
                            <td class="py-3">
                                @if($item->jenis_mutasi == 'tambah')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill">Tambah Anggota Baru</span>
                                @elseif($item->jenis_mutasi == 'edit_kk')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill">Perubahan Data KK</span>
                                @elseif($item->jenis_mutasi == 'pindah_akun')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">Pindah Pemegang Akun</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 rounded-pill">Edit Data Anggota</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="d-block text-dark fw-medium">{{ $item->data_pengajuan['nama_tujuan'] ?? $item->data_pengajuan['nama_lengkap'] ?? 'N/A' }}</span>
                                <span class="text-muted" style="font-size: 0.8rem;">NIK: {{ $item->data_pengajuan['nik_tujuan'] ?? $item->data_pengajuan['nik'] ?? 'N/A' }}</span>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalDetail-{{ $item->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Belum ada pengajuan mutasi baru
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal dipindah keluar dari tabel agar HTML valid (browser tidak mengeluarkan modal dari dalam <tbody>). --}}
@foreach($mutasi as $item)
    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Detail Pengajuan Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Perbandingan data lama vs baru --}}
                    <h6 class="fw-bold mb-2"><i class="bi bi-table me-2 text-success"></i>Perubahan Data</h6>
                    @include('partials._mutasi-diff', ['mutasi' => $item])

                    {{-- Timeline tahap pengajuan --}}
                    <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-clock-history me-2 text-success"></i>Riwayat Proses</h6>
                    @include('partials._mutasi-timeline', ['mutasi' => $item])

                    @if($item->file_bukti)
                        <div class="mt-3 pt-3 border-top">
                            <p class="text-muted mb-1" style="font-size: 0.8rem;">File Bukti (Scan KK)</p>
                            <a href="{{ route('rt.mutasi.dokumen', $item->id) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-text"></i> Lihat Dokumen</a>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-danger px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalTolak-{{ $item->id }}">Tolak</button>
                        <form action="{{ route('rt.mutasi.approve', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success px-4 py-2 rounded-3" style="background-color: #559e66; border: none;">
                                {{ $item->jenis_mutasi === 'pindah_akun' ? 'Setujui Pemindahan Akun' : 'Setujui (Teruskan ke RW)' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div class="modal fade" id="modalTolak-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form action="{{ route('rt.mutasi.reject', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Tolak Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label for="alasanTolakRt-{{ $item->id }}" class="form-label fw-semibold">Alasan penolakan</label>
                        <textarea id="alasanTolakRt-{{ $item->id }}" name="alasan_penolakan" class="form-control" rows="3" required placeholder="Tuliskan alasan penolakan agar warga dapat memperbaiki pengajuan."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2 p-4 pt-0">
                        <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4 rounded-3">Tolak Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')
<script>
(function () {
    // Tombol salin link aktivasi (muncul setelah approval pindah akun).
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
})();
</script>
@endpush
