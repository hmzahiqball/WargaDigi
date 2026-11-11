@extends('layouts.global')

@section('title', 'Detail Penduduk')

@php
    $jkLabel = match ($penduduk->jenis_kelamin) {
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
        default => $penduduk->jenis_kelamin ?: '-',
    };

    $tglLahir = $penduduk->tanggal_lahir
        ? $penduduk->tanggal_lahir->locale('id')->translatedFormat('d M Y')
        : '-';

    $ktpUrl = $penduduk->file_ktp ? asset('storage/' . $penduduk->file_ktp) : null;
    $ktpPdf = $ktpUrl && \Illuminate\Support\Str::endsWith(strtolower($penduduk->file_ktp), '.pdf');
@endphp

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Detail Penduduk</h3>
            <p class="text-muted mb-0">Informasi identitas dan hubungan keluarga penduduk.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('warga.keluarga.anggota.edit', $penduduk->id) }}" class="btn btn-success" style="background-color: #559e66; border: none; border-radius:0.5rem;">
                <i class="bi bi-pencil-square me-1"></i> Ajukan Perubahan Data
            </a>
            <a href="{{ route('warga.keluarga.index') }}" class="btn btn-light border" style="border-radius:0.5rem;">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nama Lengkap</dt>
                        <dd class="col-sm-8">{{ $penduduk->nama_lengkap }}</dd>
                        <dt class="col-sm-4">NIK</dt>
                        <dd class="col-sm-8">{{ $penduduk->nik }}</dd>
                        <dt class="col-sm-4">Nomor WA</dt>
                        <dd class="col-sm-8">{{ $penduduk->no_wa ?: '-' }}</dd>
                        <dt class="col-sm-4">Jenis Kelamin</dt>
                        <dd class="col-sm-8">{{ $jkLabel }}</dd>
                        <dt class="col-sm-4">Tempat, Tanggal Lahir</dt>
                        <dd class="col-sm-8">{{ $penduduk->tempat_lahir }}, {{ $tglLahir }}</dd>
                        <dt class="col-sm-4">Agama</dt>
                        <dd class="col-sm-8">{{ $penduduk->agama }}</dd>
                        <dt class="col-sm-4">Pekerjaan</dt>
                        <dd class="col-sm-8">{{ $penduduk->pekerjaan ?: '-' }}</dd>
                        <dt class="col-sm-4">Pendidikan Terakhir</dt>
                        <dd class="col-sm-8">{{ $penduduk->pendidikan_terakhir ?: '-' }}</dd>
                        <dt class="col-sm-4">Status Perkawinan</dt>
                        <dd class="col-sm-8">{{ $penduduk->status_perkawinan ?: '-' }}</dd>
                        <dt class="col-sm-4">Kewarganegaraan</dt>
                        <dd class="col-sm-8">{{ $penduduk->kewarganegaraan ?: '-' }}</dd>
                        <dt class="col-sm-4">Hubungan Keluarga</dt>
                        <dd class="col-sm-8">{{ $penduduk->status_hubungan_keluarga }}</dd>
                        <dt class="col-sm-4">Status Warga</dt>
                        <dd class="col-sm-8">
                            @if($penduduk->status_warga === 'tetap')
                                <span class="badge bg-success bg-opacity-10 text-success">Warga Tetap</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning">Bukan Warga Tetap</span>
                            @endif
                        </dd>
                        <dt class="col-sm-4">Nomor KK</dt>
                        <dd class="col-sm-8">{{ $penduduk->keluarga?->no_kk }}</dd>
                        <dt class="col-sm-4">Alamat (sesuai KTP)</dt>
                        <dd class="col-sm-8">{{ $penduduk->alamat ?: '-' }}</dd>
                        <dt class="col-sm-4">Alamat Domisili</dt>
                        <dd class="col-sm-8">{{ $penduduk->alamat_domisili ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-vcard me-2 text-success"></i>Kartu Tanda Penduduk</h6>
                    @if($ktpUrl)
                        @if($ktpPdf)
                            <a href="{{ $ktpUrl }}" target="_blank" class="btn btn-outline-success btn-sm w-100">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Lihat KTP (PDF)
                            </a>
                        @else
                            <a href="{{ $ktpUrl }}" target="_blank" class="d-block">
                                <img src="{{ $ktpUrl }}" alt="KTP {{ $penduduk->nama_lengkap }}"
                                     class="img-fluid rounded-3 border" style="max-height:260px;object-fit:contain;">
                            </a>
                            <p class="small text-muted mt-2 mb-0">Klik gambar untuk memperbesar.</p>
                        @endif
                    @else
                        <p class="text-muted small mb-0"><i class="bi bi-image me-1"></i> Belum ada file KTP yang diunggah.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
