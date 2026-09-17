@extends('layouts.global')

@section('title', 'Persetujuan Mutasi Warga')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;">
                <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">Verifikasi Mutasi Warga</h3>
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

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-semibold" style="font-size: 0.85rem;">WAKTU PENGAJUAN</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 0.85rem;">KEPALA KELUARGA</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size: 0.85rem;">JENIS MUTASI</th>
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
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 rounded-pill">Edit Data Anggota</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="d-block text-dark fw-medium">{{ $item->data_pengajuan['nama_lengkap'] ?? 'N/A' }}</span>
                                <span class="text-muted" style="font-size: 0.8rem;">NIK: {{ $item->data_pengajuan['nik'] ?? 'N/A' }}</span>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalDetail-{{ $item->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="modalDetail-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Detail Pengajuan Mutasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            @foreach($item->data_pengajuan as $key => $value)
                                                <div class="col-md-6">
                                                    <p class="text-muted mb-1" style="font-size: 0.8rem;">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                                    <p class="fw-medium text-dark mb-0">{{ $value ?: '-' }}</p>
                                                </div>
                                            @endforeach
                                            @if($item->file_bukti)
                                                <div class="col-12 mt-3">
                                                    <p class="text-muted mb-1" style="font-size: 0.8rem;">File Bukti (Scan KK)</p>
                                                    <a href="{{ asset('storage/'.$item->file_bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-text"></i> Lihat Dokumen</a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                            <form action="{{ route('rt.mutasi.reject', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="alasan_penolakan" value="Ditolak oleh RT">
                                                <button type="submit" class="btn btn-outline-danger px-4 py-2 rounded-3">Tolak</button>
                                            </form>
                                            <form action="{{ route('rt.mutasi.approve', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success px-4 py-2 rounded-3" style="background-color: #559e66; border: none;">Setujui (Teruskan ke RW)</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
@endsection
