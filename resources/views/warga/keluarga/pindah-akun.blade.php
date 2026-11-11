@extends('layouts.global')

@section('title', 'Pindahkan Pemegang Akun')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Pindahkan Pemegang Akun</h3>
            <p class="text-muted mb-0">Alihkan hak pemegang akun keluarga kepada anggota keluarga lain.</p>
        </div>
        <a href="{{ route('warga.keluarga.index') }}" class="btn btn-light border" style="border-radius:0.5rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="alert alert-warning bg-warning bg-opacity-10 border-0 border-start border-warning border-4 d-flex align-items-start p-3 mb-4 rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-3 mt-1"></i>
        <div>
            <h6 class="fw-bold text-warning mb-1" style="font-size: 0.95rem;">Perhatian</h6>
            @if(!empty($perananLain))
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    Setelah pengajuan ini disetujui Ketua RT, <strong>hanya akses Warga</strong> pada akun Anda yang
                    dialihkan. Akses <strong>{{ implode(', ', $perananLain) }}</strong> tetap aktif dan Anda masih
                    bisa masuk dengan username dan password yang sama. Anggota keluarga yang dipilih akan menerima
                    akses Warga; bila belum punya akun, ia memperoleh <strong>link aktivasi melalui Ketua RT</strong>
                    untuk menentukan username dan password sendiri.
                </p>
            @else
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    Setelah pengajuan ini disetujui Ketua RT, <strong>akun Anda akan dinonaktifkan</strong>
                    dan anggota keluarga yang dipilih akan menerima <strong>link aktivasi melalui Ketua RT</strong>
                    untuk menentukan username dan password sendiri.
                </p>
            @endif
        </div>
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

            @if($anggotaLain->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-3"></i>
                    Belum ada anggota keluarga lain yang dapat menerima pemindahan akun.
                </div>
            @else
                <form method="POST" action="{{ route('warga.keluarga.pindah-akun.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="nik_tujuan" class="form-label small fw-semibold">Pilih Anggota Keluarga Tujuan</label>
                        <select name="nik_tujuan" id="nik_tujuan" class="form-select @error('nik_tujuan') is-invalid @enderror" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($anggotaLain as $anggota)
                                <option value="{{ $anggota->nik }}" @selected(old('nik_tujuan') === $anggota->nik)>
                                    {{ $anggota->nama_lengkap }} ({{ $anggota->status_hubungan_keluarga }})
                                </option>
                            @endforeach
                        </select>
                        @error('nik_tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-success" style="background-color: #559e66; border: none; border-radius:0.5rem;">
                            <i class="bi bi-send me-1"></i> Ajukan Pemindahan Akun
                        </button>
                        <a href="{{ route('warga.keluarga.index') }}" class="btn btn-light border" style="border-radius:0.5rem;">
                            Kembali
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
