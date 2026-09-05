@extends('layouts.warga')

@section('title', 'Layanan Permohonan Surat')

@section('content')
{{-- Header --}}
<div class="mb-4">
    <h2 class="fw-bold" style="color: #198754;">Layanan Permohonan Surat</h2>
    <p class="text-muted mb-0">Ajukan permohonan surat administrasi (Domisili, Keterangan Tidak Mampu, dll) secara mandiri.</p>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <ul class="mb-0 list-unstyled">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- Kolom Utama (Kiri) --}}
    <div class="col-lg-8">
        {{-- Section 1: Status Pengajuan Saya --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Status Pengajuan Saya</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white bg-white border rounded-pill px-3 dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-funnel me-1"></i>Semua Status
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="#">Semua Status</a></li>
                            <li><a class="dropdown-item" href="#">Menunggu Verifikasi</a></li>
                            <li><a class="dropdown-item" href="#">Selesai / Siap Diambil</a></li>
                            <li><a class="dropdown-item" href="#">Ditolak</a></li>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr class="text-muted small" style="font-size: 12px;">
                                <th class="border-0 py-2 fw-bold">Jenis Dokumen</th>
                                <th class="border-0 py-2 fw-bold text-center">Tanggal Pengajuan</th>
                                <th class="border-0 py-2 fw-bold text-center">Status</th>
                                <th class="border-0 py-2 fw-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan as $item)
                            <tr class="bg-white">
                                <td class="bg-white py-3">
                                    <span class="fw-bold small">{{ $item->tipe_surat }}</span><br>
                                    <span class="text-muted" style="font-size: 11px;">ID: DOC-{{ now()->format('Y') }}-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="bg-white py-3 text-center">
                                    <span class="text-muted small">{{ $item->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="bg-white py-3 text-center">
                                    @php
                                        $statusConfig = match($item->status) {
                                            'Diajukan' => ['bg' => '#FFF3CD', 'color' => '#B8860B', 'label' => 'Menunggu Verifikasi'],
                                            'Disetujui RT' => ['bg' => '#D4EDDA', 'color' => '#155724', 'label' => 'Disetujui RT'],
                                            'Selesai' => ['bg' => '#D1ECF1', 'color' => '#0C5460', 'label' => 'Selesai / Siap Diambil'],
                                            'Ditolak RT' => ['bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak'],
                                            'Ditolak RW' => ['bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak'],
                                            default => ['bg' => '#E2E3E5', 'color' => '#383D41', 'label' => $item->status],
                                        };
                                    @endphp
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }}; font-size: 11px;">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="bg-white py-3 text-center">
                                    @if($item->status === 'Selesai')
                                        <a href="#" class="text-success fw-bold small text-decoration-none"><i class="bi bi-download me-1"></i>Detail</a>
                                    @else
                                        <a href="#" class="text-muted fw-bold small text-decoration-none"><i class="bi bi-clock-history me-1"></i>Detail</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4 bg-white">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                    Belum ada riwayat pengajuan surat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Section 2: Pilih Kategori Permohonan --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h5 class="fw-bold mb-1">Pilih Kategori Permohonan</h5>
                <p class="text-muted small mb-3">Pilih jenis surat untuk melihat persyaratan yang dibutuhkan</p>

                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">JENIS SURAT</span>
                <p class="text-muted mb-2" style="font-size: 12px;"><i class="bi bi-lightbulb me-1"></i>Tips: Pastikan data profil Anda sudah lengkap sebelum mengajukan.</p>

                <select class="form-select border rounded-3 py-2 bg-white shadow-sm" id="jenisSuratSelect" style="font-size: 14px;">
                    <option value="" selected>Pilih jenis surat yang ingin diajukan...</option>
                    @foreach($tipeSurat as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Section 3: Formulir Permohonan (Tersembunyi, tampil saat pilih jenis surat) --}}
        <div class="card border-0 shadow-sm mb-4 d-none" id="formPengajuan" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h5 class="fw-bold mb-1" id="formTitle">Formulir Permohonan Surat Pengantar Domisili</h5>
                <p class="text-muted small mb-4">Lengkapi form di bawah ini untuk mengajukan surat pengantar di RT/RW.</p>

                <form action="{{ route('warga.surat.store') }}" method="POST" enctype="multipart/form-data" id="suratForm">
                    @csrf
                    <input type="hidden" name="tipe_surat" id="tipeSuratHidden" value="">

                    {{-- Data Diri Pemohon --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-person-fill me-2 text-success"></i>Data Diri Pemohon</h6>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->nama_lengkap ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">NIK <i class="bi bi-shield-check text-success"></i></label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->nik ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tempat Lahir</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->tempat_lahir ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tanggal Lahir</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk ? $penduduk->tanggal_lahir->format('d/m/Y') : '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Jenis Kelamin</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk ? ($penduduk->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Pekerjaan</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->pekerjaan ?? '-' }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Alamat Domisili Saat Ini</label>
                                <textarea class="form-control bg-light border" rows="2" readonly>{{ $penduduk && $penduduk->keluarga ? $penduduk->keluarga->alamat : '-' }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Keperluan Pengajuan --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-send-fill me-2 text-success"></i>Keperluan Pengajuan</h6>
                        <label class="form-label small fw-bold text-muted mt-1">Jelaskan Tujuan Pengajuan Surat</label>
                        <textarea class="form-control bg-white border" name="keperluan" rows="4" placeholder="Contoh: Persyaratan pembuatan rekening bank baru..." required></textarea>
                    </div>

                    {{-- Unggah Dokumen Pendukung --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-paperclip me-2 text-success"></i>Unggah Dokumen Pendukung</h6>
                        <div class="row g-3 mt-1">
                            {{-- Upload KTP --}}
                            <div class="col-md-6">
                                <div class="border border-2 rounded-3 p-4 text-center position-relative upload-zone" style="border-style: dashed !important; cursor: pointer; background: rgba(25, 135, 84, 0.02);" onclick="document.getElementById('fileKtp').click()">
                                    <input type="file" id="fileKtp" name="file_ktp" class="d-none" accept=".jpg,.jpeg,.png,.pdf" onchange="showFileName(this, 'ktpPreview')">
                                    <div id="ktpPreview">
                                        <i class="bi bi-cloud-arrow-up text-success fs-2 d-block mb-2"></i>
                                        <span class="fw-bold small d-block">Unggah Foto KTP</span>
                                        <span class="text-muted" style="font-size: 11px;">Format: JPG, PNG, PDF (Max 5MB)</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">Pilih File</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Upload KK --}}
                            <div class="col-md-6">
                                <div class="border border-2 rounded-3 p-4 text-center position-relative upload-zone" style="border-style: dashed !important; cursor: pointer; background: rgba(25, 135, 84, 0.02);" onclick="document.getElementById('fileKk').click()">
                                    <input type="file" id="fileKk" name="file_kk" class="d-none" accept=".jpg,.jpeg,.png,.pdf" onchange="showFileName(this, 'kkPreview')">
                                    <div id="kkPreview">
                                        <i class="bi bi-cloud-arrow-up text-success fs-2 d-block mb-2"></i>
                                        <span class="fw-bold small d-block">Unggah Kartu Keluarga</span>
                                        <span class="text-muted" style="font-size: 11px;">Format: JPG, PNG, PDF (Max 5MB)</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">Pilih File</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pernyataan & Submit --}}
                    <div class="border-top pt-4">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input border-success" id="pernyataanCheck" name="pernyataan" value="1" required>
                            <label class="form-check-label small text-muted" for="pernyataanCheck">
                                Saya menyatakan bahwa data yang saya isikan di atas adalah benar dan dapat dipertanggungjawabkan sesuai dengan peraturan perundang-undangan yang berlaku.
                            </label>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" onclick="resetForm()">Batal</button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-send me-1"></i>Kirim Pengajuan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Sidebar (Kanan) --}}
    <div class="col-lg-4">
        {{-- Butuh Bantuan? --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h6 class="fw-bold mb-2">Butuh Bantuan?</h6>
                <p class="text-muted small mb-3">Jika ada kendala dalam pengajuan dokumen, silakan hubungi pengurus RT/RW.</p>
                <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-success rounded-pill px-4 w-100 fw-bold">
                    <i class="bi bi-whatsapp me-1"></i>Hubungi Pengurus
                </a>
            </div>
        </div>

        {{-- Informasi Proses --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb-fill text-warning me-1"></i>Informasi Proses</h6>
                <p class="text-muted small mb-0">
                    Proses verifikasi biasanya memakan waktu <strong>1-2 hari kerja</strong> oleh Admin RW.
                    Pastikan data pendukung yang Anda unggah terlihat jelas untuk mempercepat proses persetujuan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectEl = document.getElementById('jenisSuratSelect');
    const formCard = document.getElementById('formPengajuan');
    const formTitle = document.getElementById('formTitle');
    const hiddenInput = document.getElementById('tipeSuratHidden');

    selectEl.addEventListener('change', function() {
        if (this.value) {
            formCard.classList.remove('d-none');
            formTitle.textContent = 'Formulir Permohonan ' + this.value;
            hiddenInput.value = this.value;
            // Smooth scroll ke formulir
            formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            formCard.classList.add('d-none');
        }
    });
});

function showFileName(input, previewId) {
    const previewEl = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeMB = (file.size / (1024 * 1024)).toFixed(1);

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB.');
            input.value = '';
            return;
        }

        previewEl.innerHTML = `
            <i class="bi bi-file-earmark-check-fill text-success fs-2 d-block mb-2"></i>
            <span class="fw-bold small d-block text-success">${file.name}</span>
            <span class="text-muted" style="font-size: 11px;">${sizeMB} MB — Siap diunggah</span>
        `;
    }
}

function resetForm() {
    document.getElementById('formPengajuan').classList.add('d-none');
    document.getElementById('jenisSuratSelect').value = '';
    document.getElementById('suratForm').reset();
    // Reset preview
    document.getElementById('ktpPreview').innerHTML = `
        <i class="bi bi-cloud-arrow-up text-success fs-2 d-block mb-2"></i>
        <span class="fw-bold small d-block">Unggah Foto KTP</span>
        <span class="text-muted" style="font-size: 11px;">Format: JPG, PNG, PDF (Max 5MB)</span>
    `;
    document.getElementById('kkPreview').innerHTML = `
        <i class="bi bi-cloud-arrow-up text-success fs-2 d-block mb-2"></i>
        <span class="fw-bold small d-block">Unggah Kartu Keluarga</span>
        <span class="text-muted" style="font-size: 11px;">Format: JPG, PNG, PDF (Max 5MB)</span>
    `;
}
</script>
@endpush
