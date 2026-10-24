@extends('layouts.global')

@section('title', 'Detail Penduduk')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Detail Penduduk</h3>
            <p class="text-muted mb-0">Informasi identitas dan hubungan keluarga penduduk.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama Lengkap</dt>
                <dd class="col-sm-8">{{ $penduduk->nama_lengkap }}</dd>
                <dt class="col-sm-4">NIK</dt>
                <dd class="col-sm-8">{{ $penduduk->nik }}</dd>
                <dt class="col-sm-4">Jenis Kelamin</dt>
                <dd class="col-sm-8">{{ $penduduk->jenis_kelamin }}</dd>
                <dt class="col-sm-4">Tempat, Tanggal Lahir</dt>
                <dd class="col-sm-8">{{ $penduduk->tempat_lahir }}, {{ optional($penduduk->tanggal_lahir)->format('d-m-Y') }}</dd>
                <dt class="col-sm-4">Agama</dt>
                <dd class="col-sm-8">{{ $penduduk->agama }}</dd>
                <dt class="col-sm-4">Pekerjaan</dt>
                <dd class="col-sm-8">{{ $penduduk->pekerjaan }}</dd>
                <dt class="col-sm-4">Hubungan Keluarga</dt>
                <dd class="col-sm-8">{{ $penduduk->status_hubungan_keluarga }}</dd>
                <dt class="col-sm-4">Nomor KK</dt>
                <dd class="col-sm-8">{{ $penduduk->keluarga?->no_kk }}</dd>
                <dt class="col-sm-4">Alamat</dt>
                <dd class="col-sm-8">{{ $penduduk->keluarga?->alamat }}</dd>
                <dt class="col-sm-4">RT</dt>
                <dd class="col-sm-8">{{ $penduduk->keluarga?->rt?->kode_rt }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
