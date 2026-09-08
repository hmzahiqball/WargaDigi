@extends('layouts.global')

@section('title', 'Layanan Permohonan Surat')

@section('content')
{{-- Header --}}
<div class="mb-4">
    <h2 class="fw-bold" style="color: #198754;">Layanan Permohonan Surat</h2>
    <p class="text-muted mb-0">Ajukan permohonan surat pengantar RT/RW secara mandiri.</p>
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
                    <h5 class="fw-bold mb-0">Riwayat Pengajuan Surat</h5>
                </div>
                @forelse($pengajuan as $item)
                <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center" style="background: #fafafa;">
                    <div>
                        <span class="fw-bold small">{{ $item->tipe_surat }}</span>
                        <br><span class="text-muted" style="font-size: 11px;">{{ $item->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $sc = match($item->status) {
                                'Diajukan' => ['bg' => '#FFF3CD', 'color' => '#856404', 'label' => 'Menunggu RT'],
                                'Disetujui RT' => ['bg' => '#D1ECF1', 'color' => '#0C5460', 'label' => 'Menunggu RW'],
                                'Selesai' => ['bg' => '#D4EDDA', 'color' => '#155724', 'label' => 'Selesai'],
                                'Ditolak RT' => ['bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak RT'],
                                'Ditolak RW' => ['bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak RW'],
                                default => ['bg' => '#E2E3E5', 'color' => '#383D41', 'label' => $item->status],
                            };
                        @endphp
                        <span class="badge rounded-pill px-3 py-1" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:11px;">{{ $sc['label'] }}</span>
                        @if($item->status === 'Selesai')
                            <a href="{{ route('warga.surat.download-pdf', $item->id) }}" class="btn btn-sm btn-success rounded-pill px-3"><i class="bi bi-download me-1"></i>PDF</a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted small mb-0">Belum ada pengajuan surat.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Section 2: Pilih Kategori Permohonan --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h5 class="fw-bold mb-1">Pilih Kategori Permohonan</h5>
                <p class="text-muted small mb-3">Pilih jenis surat untuk persyaratan yang dibutuhkan.</p>
                <label class="form-label fw-bold small text-muted">JENIS SURAT</label>
                <select class="form-select bg-white border" id="jenisSuratSelect">
                    <option value="">Pilih jenis surat yang ingin diajukan...</option>
                    @foreach($tipeSurat as $i => $surat)
                        <option value="{{ $surat }}">{{ ($i+1) }}. {{ $surat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Section 3: Formulir (hidden by default) --}}
        <div class="card border-0 shadow-sm mb-4 d-none" id="formPengajuan" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h5 class="fw-bold mb-3" id="formTitle">Formulir Permohonan</h5>
                <form action="{{ route('warga.surat.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipe_surat" id="tipeSuratHidden">

                    {{-- Data Diri Pemohon (readonly dari database) --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-person-fill me-2 text-success"></i>Data Diri Pemohon</h6>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->nama_lengkap ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">NIK</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->nik ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tempat Lahir</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->tempat_lahir ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tanggal Lahir</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk && $penduduk->tanggal_lahir ? $penduduk->tanggal_lahir->format('d-m-Y') : '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Jenis Kelamin</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk ? ($penduduk->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Pekerjaan</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->pekerjaan ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Agama</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->agama ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Status Perkawinan</label>
                                <input type="text" class="form-control bg-light border" value="{{ $penduduk->status_perkawinan ?? '-' }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Alamat Domisili</label>
                                <textarea class="form-control bg-light border" rows="2" readonly>{{ $penduduk && $penduduk->keluarga ? $penduduk->keluarga->alamat : '-' }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Keperluan Pengajuan --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-send-fill me-2 text-success"></i>Keperluan Pengajuan</h6>
                        <label class="form-label small fw-bold text-muted mt-1">Jelaskan Tujuan Pengajuan Surat (Opsional)</label>
                        <textarea class="form-control bg-white border" name="keperluan" rows="3" placeholder="Contoh: Persyaratan pembuatan rekening bank baru..."></textarea>
                    </div>

                    {{-- Dokumen Pendukung (Preview Otomatis) --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-file-earmark-image me-2 text-success"></i>Dokumen Pendukung (KTP & KK)</h6>
                        <p class="text-muted small mb-3">Dokumen KTP dan KK Anda otomatis ditampilkan dari data yang sudah terdaftar.</p>
                        <div class="row g-3">
                            {{-- Preview KTP --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Foto KTP</label>
                                <div class="border rounded-3 p-3 text-center" style="background: rgba(25,135,84,0.02); min-height: 180px;">
                                    @if($ktpUrl)
                                        <img src="{{ $ktpUrl }}" alt="KTP" class="img-fluid rounded shadow-sm mb-2" style="max-height: 140px; cursor:pointer;" onclick="showImageModal('{{ $ktpUrl }}', 'Preview KTP - {{ $penduduk->nama_lengkap }}')">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="showImageModal('{{ $ktpUrl }}', 'Preview KTP')"><i class="bi bi-zoom-in me-1"></i>Perbesar</button>
                                        </div>
                                    @else
                                        <div class="py-3">
                                            <i class="bi bi-camera text-muted fs-1 d-block mb-2"></i>
                                            <span class="text-muted small d-block mb-2">Belum ada foto KTP tersimpan</span>
                                            <label class="btn btn-sm btn-outline-success rounded-pill px-3" for="fileKtp">
                                                <i class="bi bi-upload me-1"></i>Unggah KTP
                                            </label>
                                            <input type="file" id="fileKtp" name="file_ktp" class="d-none" accept=".jpg,.jpeg,.png">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- Preview KK --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Kartu Keluarga</label>
                                <div class="border rounded-3 p-3 text-center" style="background: rgba(25,135,84,0.02); min-height: 180px;">
                                    @if($kkUrl)
                                        <img src="{{ $kkUrl }}" alt="KK" class="img-fluid rounded shadow-sm mb-2" style="max-height: 140px; cursor:pointer;" onclick="showImageModal('{{ $kkUrl }}', 'Preview KK - {{ $penduduk->nama_lengkap }}')">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="showImageModal('{{ $kkUrl }}', 'Preview KK')"><i class="bi bi-zoom-in me-1"></i>Perbesar</button>
                                        </div>
                                    @else
                                        <div class="py-3">
                                            <i class="bi bi-camera text-muted fs-1 d-block mb-2"></i>
                                            <span class="text-muted small d-block mb-2">Belum ada foto KK tersimpan</span>
                                            <label class="btn btn-sm btn-outline-info rounded-pill px-3" for="fileKk">
                                                <i class="bi bi-upload me-1"></i>Unggah KK
                                            </label>
                                            <input type="file" id="fileKk" name="file_kk" class="d-none" accept=".jpg,.jpeg,.png">
                                        </div>
                                    @endif
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
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h6 class="fw-bold mb-2">Butuh Bantuan?</h6>
                <p class="text-muted small mb-3">Jika ada kendala dalam pengajuan dokumen, silakan hubungi pengurus RT/RW.</p>
                <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-success rounded-pill px-4 w-100 fw-bold">
                    <i class="bi bi-whatsapp me-1"></i>Hubungi Pengurus
                </a>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb-fill text-warning me-1"></i>Informasi Proses</h6>
                <p class="text-muted small mb-0">
                    Proses verifikasi: <strong>Warga ? RT ? RW</strong>. Setelah disetujui kedua pihak, surat bisa langsung diunduh dalam format PDF.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview Gambar --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold" id="imagePreviewTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4 text-center">
                <img src="" id="imagePreviewSrc" class="img-fluid rounded shadow" style="max-height: 70vh;" alt="Preview">
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
            formTitle.textContent = 'Formulir Permohonan: ' + this.value;
            hiddenInput.value = this.value;
            formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            formCard.classList.add('d-none');
        }
    });
});

function showImageModal(src, title) {
    document.getElementById('imagePreviewSrc').src = src;
    document.getElementById('imagePreviewTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
}

function resetForm() {
    document.getElementById('formPengajuan').classList.add('d-none');
    document.getElementById('jenisSuratSelect').value = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endpush
