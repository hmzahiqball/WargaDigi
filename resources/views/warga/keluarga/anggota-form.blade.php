@extends('layouts.global')

@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $judul = $isEdit ? 'Ajukan Perubahan Data Penduduk' : 'Tambah Anggota Keluarga';
    $deskripsi = $isEdit
        ? 'Perubahan data anggota diajukan dan diproses oleh Admin RT lalu RW. Data penduduk berubah setelah RW menyetujui.'
        : 'Pengajuan penambahan anggota diproses oleh Admin RT lalu RW. Data penduduk dibuat setelah RW menyetujui.';
@endphp

@section('title', $judul)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">{{ $judul }}</h3>
            <p class="text-muted mb-0">{{ $deskripsi }}</p>
        </div>
        <a href="{{ route('warga.keluarga.index') }}" class="btn btn-light border" style="border-radius:0.5rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <p class="text-muted small mb-3">
                Unggah KTP untuk mengisi otomatis — <strong>periksa kembali NIK</strong> karena angka rawan salah baca.
            </p>
            @include('partials._form-anggota-warga', [
                'formId' => $isEdit ? ('formEditAnggota-' . $member->id) : 'formTambahAnggota',
                'action' => $action,
                'method' => $method,
                'submitLabel' => $isEdit ? 'Ajukan Perubahan' : 'Ajukan Tambah Anggota',
                'submitClass' => $isEdit ? 'btn-success' : 'btn-success',
                'member' => $member,
                'requireKk' => $requireKk,
                'kkAlamatLengkap' => $kkAlamatLengkap,
                'kkDomisili' => $kkDomisili,
                'cancelUrl' => route('warga.keluarga.index'),
            ])
        </div>
    </div>
</div>

@include('partials._modal-kamera-ktp')
@endsection

@push('scripts')
    @include('partials._form-anggota-warga-scripts')
@endpush
