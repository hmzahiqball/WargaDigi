@extends('layouts.global')

@section('title', 'Tambah Keluarga & Kepala Keluarga')

@section('content')
<div class="admin-page-header">
    <h1>Tambah Keluarga & Kepala Keluarga</h1>
    <p class="text-muted">Daftarkan satu Kartu Keluarga beserta data Kepala Keluarga. Kepala Keluarga memperoleh akun warga (login via username) yang diaktivasi kemudian.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius: 0.5rem;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('rt.keluarga.store') }}">
    @csrf

    {{-- Data Kartu Keluarga --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-house-door me-2 text-success"></i>Data Kartu Keluarga</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Nomor KK</label>
                    <input type="text" name="no_kk" inputmode="numeric" maxlength="16" class="form-control admin-input" value="{{ old('no_kk') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">No. WhatsApp Keluarga</label>
                    <input type="text" name="no_wa" inputmode="tel" class="form-control admin-input" placeholder="08..." value="{{ old('no_wa') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Alamat</label>
                    <input type="text" name="alamat" class="form-control admin-input" value="{{ old('alamat') }}" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Kepala Keluarga --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-vcard me-2 text-success"></i>Data Kepala Keluarga</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">NIK</label>
                    <input type="text" name="nik" inputmode="numeric" maxlength="16" class="form-control admin-input" value="{{ old('nik') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control admin-input" value="{{ old('nama_lengkap') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select admin-input" required>
                        <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control admin-input" value="{{ old('tempat_lahir') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control admin-input" value="{{ old('tanggal_lahir') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Agama</label>
                    <select name="agama" class="form-select admin-input" required>
                        @foreach($agamaOpts as $a)
                            <option value="{{ $a }}" {{ old('agama') === $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control admin-input" value="{{ old('pekerjaan') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Pendidikan Terakhir</label>
                    <select name="pendidikan_terakhir" class="form-select admin-input" required>
                        @foreach($pendidikanOpts as $p)
                            <option value="{{ $p }}" {{ old('pendidikan_terakhir') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Status Perkawinan</label>
                    <select name="status_perkawinan" class="form-select admin-input" required>
                        @foreach($statusKawinOpts as $s)
                            <option value="{{ $s }}" {{ old('status_perkawinan') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Kewarganegaraan</label>
                    <select name="kewarganegaraan" class="form-select admin-input" required>
                        <option value="WNI" {{ old('kewarganegaraan', 'WNI') === 'WNI' ? 'selected' : '' }}>WNI</option>
                        <option value="WNA" {{ old('kewarganegaraan') === 'WNA' ? 'selected' : '' }}>WNA</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Nama Ayah <span class="text-muted">(opsional)</span></label>
                    <input type="text" name="nama_ayah" class="form-control admin-input" value="{{ old('nama_ayah') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Nama Ibu <span class="text-muted">(opsional)</span></label>
                    <input type="text" name="nama_ibu" class="form-control admin-input" value="{{ old('nama_ibu') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Akun Login Kepala Keluarga --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <h5 class="fw-bold mb-1"><i class="bi bi-key me-2 text-success"></i>Akun Login Kepala Keluarga</h5>
            <p class="text-muted small mb-3">Username wajib unik. Digunakan Kepala Keluarga untuk masuk ke aplikasi (selain nomor WhatsApp).</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control admin-input" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Password Awal</label>
                    <input type="password" name="password" class="form-control admin-input" placeholder="Min. 8 karakter" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control admin-input" required>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('rt.dashboard') }}" class="btn btn-outline-secondary" style="border-radius: 0.5rem;">Batal</a>
        <button type="submit" class="btn btn-success px-4" style="border-radius: 0.5rem;">
            <i class="bi bi-save me-1"></i> Simpan Keluarga
        </button>
    </div>
</form>
@endsection
