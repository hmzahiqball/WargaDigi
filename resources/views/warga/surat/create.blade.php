@extends('layouts.global')

@section('title', 'Buat Surat Pengajuan')

@section('content')
<div class="mb-4">
    <a href="{{ route('warga.surat.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
    </a>
    <h2 class="fw-bold text-success">Buat Surat Baru</h2>
</div>

<div class="card shadow-sm border-0 max-w-lg mx-auto">
    <div class="card-body p-4">
        <form action="{{ route('warga.surat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-bold">Pemohon</label>
                <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }} (NIK: {{ auth()->user()->nik }})" readonly>
                <small class="text-muted">Data diri Anda otomatis terisi.</small>
            </div>

            <div class="mb-3">
                <label for="jenis_surat" class="form-label fw-bold">Kategori / Jenis Surat <span class="text-danger">*</span></label>
                <select class="form-select @error('jenis_surat') is-invalid @enderror" id="jenis_surat" name="jenis_surat" required>
                    <option value="" disabled selected>Pilih Kategori Surat</option>
                    <option value="Surat Pengantar RT/RW">Surat Pengantar RT/RW</option>
                    <option value="Surat Keterangan Domisili">Surat Keterangan Domisili</option>
                    <option value="Surat Keterangan Tidak Mampu (SKTM)">Surat Keterangan Tidak Mampu (SKTM)</option>
                    <option value="Surat Keterangan Usaha">Surat Keterangan Usaha</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                @error('jenis_surat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="keperluan" class="form-label fw-bold">Keperluan <span class="text-danger">*</span></label>
                <textarea class="form-control @error('keperluan') is-invalid @enderror" id="keperluan" name="keperluan" rows="3" placeholder="Jelaskan keperluan pembuatan surat secara rinci" required>{{ old('keperluan') }}</textarea>
                @error('keperluan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="file_lampiran" class="form-label fw-bold">Unggah Syarat/Lampiran (Opsional)</label>
                <input class="form-control @error('file_lampiran') is-invalid @enderror" type="file" id="file_lampiran" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Batas maksimal 2MB. Format: PDF, JPG, PNG. (Misalnya: Foto KK/KTP, Pengantar RT lama)</small>
                @error('file_lampiran')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success py-2 fw-semibold">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
