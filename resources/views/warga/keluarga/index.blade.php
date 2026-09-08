@extends('layouts.global')

@section('title', 'Profil Keluarga')

@section('content')
<div class="container-fluid px-0">
    <!-- Alert Akses Terbatas -->
    <div class="alert alert-success bg-success bg-opacity-10 border-0 border-start border-success border-4 d-flex align-items-start p-3 mb-4 rounded-3" role="alert">
        <i class="bi bi-info-circle-fill text-success fs-5 me-3 mt-1"></i>
        <div>
            <h6 class="fw-bold text-success mb-1" style="font-size: 0.95rem;">Akses Terbatas</h6>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Data ini merupakan rekaman resmi administrasi warga. Pembaruan data dasar hanya dapat dilakukan oleh <strong>Kepala Keluarga</strong> dan disetujui oleh <strong>Staff RT dan RW</strong>. Pastikan informasi kontak akurat dan up-to-date.</p>
        </div>
    </div>

    <!-- Header Profil Keluarga -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;">
                <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">Profil Keluarga</h3>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola data domisili dan informasi kontak utama keluarga Anda.</p>
            </div>
        </div>
        <div>
            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2 fw-medium">
                <i class="bi bi-check-circle me-1"></i> Kepala Keluarga
            </span>
        </div>
    </div>

    <div class="mb-4">
        <button class="btn btn-success fw-medium px-4 py-2" style="background-color: #559e66; border: none; border-radius: 8px;">
            <i class="bi bi-file-earmark-text me-1"></i> Ajukan Perubahan Data/KK Baru
        </button>
    </div>

    <!-- Identitas Keluarga -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="border: 1px solid #e2e8f0 !important;">
        <div class="card-header bg-white border-bottom pt-3 pb-2 px-4 d-flex align-items-center gap-2">
            <i class="bi bi-card-heading text-success fs-5"></i>
            <h5 class="fw-bold mb-0 text-dark">Identitas Keluarga</h5>
        </div>
        <div class="card-body p-4">
            <div class="mb-4">
                <p class="text-muted mb-1" style="font-size: 0.85rem;">Nomor Kartu Keluarga (KK)</p>
                <h6 class="fw-semibold mb-0" style="font-size: 1rem;">{{ $keluarga->no_kk }}</h6>
            </div>
            <div class="mb-4">
                <p class="text-muted mb-1" style="font-size: 0.85rem;">Nama Kepala Keluarga</p>
                <h6 class="fw-semibold mb-0" style="font-size: 1rem;">{{ $keluarga->kepalaKeluarga->nama_lengkap ?? '-' }}</h6>
            </div>
            <div>
                <p class="text-muted mb-1" style="font-size: 0.85rem;">Alamat Sesuai KK</p>
                <p class="fw-medium text-dark mb-0" style="font-size: 0.95rem; line-height: 1.5;">
                    {{ $keluarga->alamat ?? '-' }}, <br>
                    RT {{ $keluarga->rt->nomor ?? '-' }} / RW 21,<br>
                    Kelurahan Tanimulya, Kecamatan Ngamprah,<br>
                    Kabupaten Bandung Barat, Jawa Barat 40552
                </p>
            </div>
        </div>
    </div>

    <!-- Tabel Anggota Keluarga -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="border: 1px solid #e2e8f0 !important;">
        <div class="card-header bg-white border-bottom pt-3 pb-2 px-4 d-flex align-items-center gap-2">
            <i class="bi bi-people text-success fs-5"></i>
            <h5 class="fw-bold mb-0 text-dark">Tabel Anggota Keluarga</h5>
        </div>
        <div class="card-body p-4">
            <div class="d-flex flex-column gap-4">
                @forelse($anggota as $member)
                <div class="border rounded-3 p-4" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 1.1rem;">{{ $member->nama_lengkap }}</h6>
                            <p class="text-muted mb-0" style="font-size: 0.85rem; font-family: monospace;">NIK: {{ $member->nik }}</p>
                        </div>
                        <div>
                            @php
                                $badgeColor = 'bg-secondary';
                                if($member->status_hubungan_keluarga == 'Kepala Keluarga') $badgeColor = 'bg-success text-white';
                                else if($member->status_hubungan_keluarga == 'Istri') $badgeColor = 'bg-info bg-opacity-10 text-info border border-info';
                                else if($member->status_hubungan_keluarga == 'Anak') $badgeColor = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                            @endphp
                            <span class="badge {{ $badgeColor }} px-3 py-1 rounded-pill" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                {{ strtoupper($member->status_hubungan_keluarga) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Jenis Kelamin</p>
                                <p class="fw-medium text-dark mb-0" style="font-size: 0.9rem;">{{ $member->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Agama</p>
                                <p class="fw-medium text-dark mb-0" style="font-size: 0.9rem;">{{ $member->agama ?? '-' }}</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Jenis Pekerjaan</p>
                                <p class="fw-medium text-dark mb-0" style="font-size: 0.9rem;">{{ $member->pekerjaan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Kewarganegaraan</p>
                                <p class="fw-medium text-dark mb-0" style="font-size: 0.9rem;">WNI</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Tempat, Tgl Lahir</p>
                                <p class="fw-medium text-dark mb-0" style="font-size: 0.9rem;">{{ $member->tempat_lahir ?? '-' }}, {{ $member->tanggal_lahir ? $member->tanggal_lahir->format('d-m-Y') : '-' }}</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Pendidikan Terakhir</p>
                                <p class="fw-medium text-dark mb-0" style="font-size: 0.9rem;">-</p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Status Perkawinan</p>
                                <p class="fw-medium text-dark mb-0" style="font-size: 0.9rem;">{{ $member->status_perkawinan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Nama Orang Tua</p>
                                <p class="fw-medium text-dark mb-0" style="font-size: 0.9rem;">- / -</p>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <p>Belum ada data anggota keluarga</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Detail Kontak -->
    <div class="card border-0 shadow-sm rounded-4 mb-5" style="border: 1px solid #e2e8f0 !important;">
        <div class="card-header bg-white border-bottom pt-3 pb-2 px-4 d-flex align-items-center gap-2">
            <i class="bi bi-telephone text-success fs-5"></i>
            <h5 class="fw-bold mb-0 text-dark">Detail Kontak & Domisili</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('warga.keluarga.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="no_wa" class="form-label text-muted mb-1" style="font-size: 0.85rem;">Kontak Darurat (HP) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-phone"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 @error('no_wa') is-invalid @enderror" id="no_wa" name="no_wa" value="{{ old('no_wa', $keluarga->no_wa) }}" placeholder="Contoh: 081234567890" style="box-shadow: none;">
                        @error('no_wa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text" style="font-size: 0.75rem;">Nomor yang dapat dihubungi saat darurat.</div>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label text-muted mb-1" style="font-size: 0.85rem;">Alamat Domisili <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 @error('alamat') is-invalid @enderror" id="alamat" name="alamat" value="{{ old('alamat', $keluarga->alamat) }}" placeholder="Contoh: Jl. Merdeka Barat No. 45" required style="box-shadow: none;">
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text" style="font-size: 0.75rem;">Alamat tempat tinggal saat ini (bisa berbeda dengan KK).</div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <button type="reset" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success px-4 py-2" style="background-color: #559e66; border: none; border-radius: 8px;">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
