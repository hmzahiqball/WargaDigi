@extends('layouts.global')

@section('title', 'Riwayat Surat Pengajuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-success mb-0">Riwayat Pengajuan Surat</h2>
    <a href="{{ route('warga.surat.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i> Buat Surat Baru
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Jenis Surat</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $index => $surat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $surat->jenis_surat }}</td>
                        <td>{{ $surat->created_at->format('d M Y') }}</td>
                        <td>
                            @if($surat->status === 'Selesai' || $surat->status === 'Disetujui')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($surat->status === 'Ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ $surat->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($surat->status === 'Selesai' || $surat->status === 'Disetujui')
                                <a href="{{ route('warga.surat.download-pdf', $surat->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-download"></i> Unduh PDF
                                </a>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" disabled>Belum Selesai</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada riwayat pengajuan surat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
