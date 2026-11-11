@extends('layouts.global')

@section('title', 'Ajukan Perubahan KK')

@php
    $v = fn ($field, $default = '') => old($field, $keluarga->{$field} ?? $default);
@endphp

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Ajukan Perubahan Data KK</h3>
            <p class="text-muted mb-0">Perubahan identitas Kartu Keluarga diajukan dan diproses oleh Admin RT lalu RW. Data KK berubah setelah RW menyetujui.</p>
        </div>
        <a href="{{ route('warga.keluarga.index') }}" class="btn btn-light border" style="border-radius:0.5rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('warga.keluarga.update') }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nomor KK</label>
                        <input type="text" class="form-control bg-light" value="{{ $keluarga->no_kk }}" readonly disabled>
                        <small class="text-muted">Nomor KK tidak dapat diubah.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Kepala Keluarga</label>
                        <input type="text" class="form-control bg-light" value="{{ $keluarga->kepalaKeluarga?->nama_lengkap ?? '-' }}" readonly disabled>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Alamat (sesuai KK) <span class="text-danger">*</span></label>
                        <input type="text" name="alamat" class="form-control" value="{{ $v('alamat') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Alamat Domisili</label>
                        <input type="text" name="alamat_domisili" class="form-control" value="{{ $v('alamat_domisili') }}" placeholder="Isi bila berbeda dari alamat KK">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">No. WhatsApp Keluarga</label>
                        <input type="text" name="no_wa" inputmode="tel" class="form-control" value="{{ $v('no_wa') }}" placeholder="08...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Status KK</label>
                        <select name="status_warga" class="form-select">
                            <option value="tidak_tetap" {{ $v('status_warga', 'tidak_tetap') === 'tetap' ? '' : 'selected' }}>Bukan Warga Tetap</option>
                            <option value="tetap" {{ $v('status_warga') === 'tetap' ? 'selected' : '' }}>Warga Tetap</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="pemilik_rumah" value="1" id="pemilikRumah" {{ old('pemilik_rumah', $keluarga->pemilik_rumah) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="pemilikRumah">Pemilik Rumah</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kelurahan</label>
                        <input type="text" name="kelurahan" class="form-control" value="{{ $v('kelurahan') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-control" value="{{ $v('kecamatan') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kabupaten</label>
                        <input type="text" name="kabupaten" class="form-control" value="{{ $v('kabupaten') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Provinsi</label>
                        <input type="text" name="provinsi" class="form-control" value="{{ $v('provinsi') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kode Pos</label>
                        <input type="text" name="kode_pos" class="form-control" value="{{ $v('kode_pos') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RW</label>
                        <input type="text" name="rw" class="form-control" value="{{ $v('rw') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('warga.keluarga.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Batal</a>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3" style="background-color: #559e66; border: none;">
                        <i class="bi bi-send me-1"></i> Ajukan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
