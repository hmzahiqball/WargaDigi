@extends('layouts.rw')

@section('title', 'Persetujuan Dokumen')

@section('content')
{{-- Header --}}
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">Portal Persetujuan Dokumen</h2>
    <p class="text-muted mb-0">Permohonan dokumen tahap akhir sedang menunggu persetujuan RW.</p>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Two Column Layout --}}
<div class="row g-4">
    {{-- LEFT: Daftar Pengajuan (Cards) --}}
    <div class="col-lg-8">
        @foreach($pengajuan as $item)
        <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 16px;">
            <div class="card-body p-4">
                {{-- Card Header: Badge + ID + Menu --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge fw-bold px-3 py-2 small" style="background-color: #dbeafe; color: #1e3a8a; border-radius: 4px; letter-spacing: 0.5px;">DISETUJUI RT</span>
                        <span class="text-muted small">REQ-{{ str_replace(' ', '-', $item->tanggal_pengajuan) }}-{{ strtoupper(substr(md5($item->id), 0, 3)) }}</span>
                    </div>
                    <button class="btn btn-sm text-muted p-0 border-0" type="button"><i class="bi bi-three-dots-vertical"></i></button>
                </div>

                {{-- Judul Surat + Nama Pemohon --}}
                <h5 class="fw-bold text-dark mb-1">{{ $item->tipe_surat }}</h5>
                <p class="text-muted small mb-3">{{ $item->nama_pemohon }} • RT {{ substr($item->alamat, -2) ?? '03' }}</p>

                {{-- Stepper Progress --}}
                <div class="d-flex align-items-center mb-4" style="max-width: 500px;">
                    {{-- Step 1: Diajukan --}}
                    <div class="text-center flex-shrink-0">
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-check text-white" style="font-size: 16px;"></i>
                        </div>
                        <div class="small fw-semibold text-success mt-1" style="font-size: 11px;">Diajukan</div>
                    </div>
                    {{-- Line --}}
                    <div class="flex-grow-1 mx-2" style="height: 2px; background-color: #43A047;"></div>
                    {{-- Step 2: Disetujui RT --}}
                    <div class="text-center flex-shrink-0">
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-check text-white" style="font-size: 16px;"></i>
                        </div>
                        <div class="small fw-semibold text-success mt-1" style="font-size: 11px;">Disetujui RT</div>
                    </div>
                    {{-- Line --}}
                    <div class="flex-grow-1 mx-2" style="height: 2px; background-color: #dee2e6;"></div>
                    {{-- Step 3: Disetujui RW --}}
                    <div class="text-center flex-shrink-0">
                        <div class="border border-2 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-color: #dee2e6 !important;">
                            <i class="bi bi-person text-muted" style="font-size: 14px;"></i>
                        </div>
                        <div class="small fw-semibold text-muted mt-1" style="font-size: 11px;">Disetujui RW</div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-outline-secondary rounded-pill px-4 py-2 small fw-semibold"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDetailRw{{ $item->id }}">
                        <i class="bi bi-file-earmark-text me-1"></i>Lihat Detail Pemohon
                    </button>
                    <button class="btn btn-outline-success rounded-pill px-4 py-2 fw-semibold"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDetailRw{{ $item->id }}">
                        Selesaikan <i class="bi bi-check-circle ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- RIGHT: Info Panel --}}
    <div class="col-lg-4">
        {{-- Aturan Alur Kerja --}}
        <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bi bi-info-circle-fill text-primary" style="font-size: 14px;"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Aturan Alur Kerja</h6>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.6;">
                    Semua dokumen dalam antrean ini telah diverifikasi oleh ketua RT masing-masing. Persetujuan akhir memerlukan tanda tangan basah dan stempel organisasi.
                </p>
                <ul class="list-unstyled text-muted small mb-0" style="line-height: 2;">
                    <li><i class="bi bi-check2 text-success me-2"></i>Tinjau detail dalam draf yang dihasilkan.</li>
                    <li><i class="bi bi-check2 text-success me-2"></i>Cetak draf, tanda tangani, dan bubuhkan stempel.</li>
                    <li><i class="bi bi-check2 text-success me-2"></i>Pindai dan unggah PDF yang telah ditandatangani.</li>
                    <li><i class="bi bi-check2 text-success me-2"></i>Klik 'Selesaikan' untuk memberi tahu penghuni.</li>
                </ul>
            </div>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Aktivitas Terbaru</h6>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                        <i class="bi bi-check-lg text-success" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <p class="mb-0 small fw-semibold text-dark">SKTM selesai untuk Anton</p>
                        <span class="text-muted" style="font-size: 11px;">2 jam lalu</span>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                        <i class="bi bi-check-lg text-success" style="font-size: 14px;"></i>
                    </div>
                    <div>
                        <p class="mb-0 small fw-semibold text-dark">Surat Pengantar selesai untuk Lina</p>
                        <span class="text-muted" style="font-size: 11px;">kemarin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ==================== MODALS ==================== --}}
@foreach($pengajuan as $item)

{{-- Modal 1: Detail Permintaan Dokumen --}}
<div class="modal fade" id="modalDetailRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Detail Permintaan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                {{-- Top Section: Pemohon + Data Lengkap --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <div class="border rounded-3 p-3 h-100 bg-white">
                            <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Pemohon</span>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                    <span class="text-primary fw-bold fs-5">{{ strtoupper(substr($item->nama_pemohon, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">{{ $item->nama_pemohon }}</h6>
                                    <span class="text-muted small">NIK: {{ $item->nik }}</span><br>
                                    <span class="text-muted small">{{ $item->alamat }}</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-muted small">Tipe Surat:</span>
                                <h6 class="fw-bold text-dark mt-1 mb-0">{{ $item->tipe_surat }}</h6>
                            </div>
                            <div class="mt-2 d-flex align-items-center gap-1 text-muted small">
                                <i class="bi bi-calendar3"></i>
                                <span>Diajukan: {{ $item->tanggal_pengajuan }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="border rounded-3 p-3 h-100 position-relative bg-white" style="border-color: #198754 !important;">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Data Lengkap Pemohon</span>
                                <span class="badge bg-warning bg-opacity-20 text-dark rounded-pill px-3 py-1 fw-semibold small">{{ $item->status }}</span>
                            </div>
                            <div class="row mt-3 g-2">
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Jenis Kelamin</span>
                                    <p class="fw-bold small mb-2">{{ $item->jenis_kelamin }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Tempat, Tgl Lahir</span>
                                    <p class="fw-bold small mb-2">{{ $item->tempat_tgl_lahir }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Agama</span>
                                    <p class="fw-bold small mb-2">{{ $item->agama }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Pendidikan Terakhir</span>
                                    <p class="fw-bold small mb-2">{{ $item->pendidikan_terakhir }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Jenis Pekerjaan</span>
                                    <p class="fw-bold small mb-2">{{ $item->pekerjaan }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Status Perkawinan</span>
                                    <p class="fw-bold small mb-2">{{ $item->status_perkawinan }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Kewarganegaraan</span>
                                    <p class="fw-bold small mb-2">{{ $item->kewarganegaraan }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Nama Orang Tua</span>
                                    <p class="fw-bold small mb-2">{{ $item->nama_orang_tua }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lampiran Dokumen --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;" id="rwLampiranLabel{{ $item->id }}">LAMPIRAN DOKUMEN (KTP)</span>
                        <a href="#" class="text-success small fw-bold text-decoration-none" onclick="toggleRwLampiran({{ $item->id }})">
                            <span id="rwLampiranToggle{{ $item->id }}">LIHAT KARTU KELUARGA →</span>
                        </a>
                    </div>
                    <div class="bg-light rounded-3 p-4 text-center border" id="rwLampiranKTP{{ $item->id }}">
                        <div class="bg-white rounded-3 p-3 d-inline-block shadow-sm">
                            <i class="bi bi-file-earmark-text text-success fs-2 d-block mb-1"></i>
                            <span class="fw-bold small d-block">{{ $item->file_ktp }}</span>
                            <span class="text-muted" style="font-size: 11px;">{{ $item->file_ktp_size }}</span>
                            <div class="mt-2">
                                <a href="#" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i>Lihat Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-light rounded-3 p-4 text-center border d-none" id="rwLampiranKK{{ $item->id }}">
                        <div class="bg-white rounded-3 p-3 d-inline-block shadow-sm">
                            <i class="bi bi-file-earmark-text text-success fs-2 d-block mb-1"></i>
                            <span class="fw-bold small d-block">{{ $item->file_kk }}</span>
                            <span class="text-muted" style="font-size: 11px;">{{ $item->file_kk_size }}</span>
                            <div class="mt-2">
                                <a href="#" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i>Lihat Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mb-2">
                    <label class="form-label fw-bold small">Catatan Persetujuan (Opsional)</label>
                    <textarea class="form-control bg-white border" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#modalRejectRw{{ $item->id }}"
                        data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Tolak & Beri Catatan
                </button>
                <button type="button" class="btn btn-outline-success rounded-pill px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#modalPreviewRw{{ $item->id }}"
                        data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-1"></i>Setujui Dokumen
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 2: Tolak Pengajuan Dokumen --}}
<div class="modal fade" id="modalRejectRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Tolak Pengajuan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rw.surat.reject', $item->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-3 bg-white">
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 d-flex gap-3 align-items-start mb-4" style="border-radius: 12px;">
                        <i class="bi bi-info-circle-fill text-danger mt-1"></i>
                        <span class="small text-danger">Mohon berikan alasan penolakan atau catatan koreksi agar pemohon dapat melakukan perbaikan pada pengajuan ini.</span>
                    </div>
                    <label class="form-label fw-bold small">Alasan Penolakan / Catatan Koreksi <span class="text-danger">*</span></label>
                    <textarea class="form-control bg-white border" name="catatan_penolakan" rows="4" required placeholder="Contoh: Ada kesalahan input pada data NIK..."></textarea>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDetailRw{{ $item->id }}">Batal</button>
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                        Tolak & Kirim Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 3: Pratinjau Dokumen (SKD) --}}
<div class="modal fade" id="modalPreviewRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Pratinjau Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                <div class="bg-white border rounded-3 p-4 shadow-sm" style="font-family: 'Times New Roman', serif;">
                    <div class="text-center mb-3">
                        <h6 class="fw-bold mb-0" style="font-size: 14px;">PEMERINTAH KABUPATEN BANDUNG BARAT</h6>
                        <h6 class="fw-bold mb-0" style="font-size: 14px;">KECAMATAN NGAMPRAH</h6>
                        <h6 class="fw-bold mb-0" style="font-size: 14px;">DESA TANIMULYA</h6>
                        <p class="small mb-1">Jl. Pasirhalang Raya No. 1, Tanimulya, Kec. Ngamprah, Kab. Bandung Barat,</p>
                        <p class="small mb-2">Kode Pos 40552, Telp. (022) 1234567</p>
                        <hr style="border-top: 3px double #000;" class="my-2">
                    </div>
                    <div class="text-center mb-3">
                        <h6 class="fw-bold text-decoration-underline mb-1">SURAT KETERANGAN DOMISILI</h6>
                        <p class="small">Nomor: {{ $item->nomor_surat ?? '---/SKD/VIII/2024' }}</p>
                    </div>
                    <p class="small mb-2">Yang bertanda tangan dibawah ini :</p>
                    <div class="ms-4 small mb-3">
                        <table>
                            <tr><td style="width:140px">Nama</td><td>: {{ $item->nama_kepala_desa ?? 'Budi Santoso, S.Sos.' }}</td></tr>
                            <tr><td>Jabatan</td><td>: Kepala Desa Tanimulya</td></tr>
                            <tr><td>Alamat</td><td>: {{ $item->alamat_kepala_desa ?? 'RT 03 RW 10, Kp. Pasirhalang, Desa Tanimulya, Ngamprah.' }}</td></tr>
                        </table>
                    </div>
                    <p class="small mb-2">Menerangkan bahwa :</p>
                    <div class="ms-4 small mb-3">
                        <table>
                            <tr><td style="width:180px">Nama</td><td>: {{ $item->nama_pemohon_surat ?? $item->nama_pemohon }}</td></tr>
                            <tr><td>Tempat, Tanggal Lahir</td><td>: {{ $item->tempat_tgl_lahir_surat ?? $item->tempat_tgl_lahir }}</td></tr>
                            <tr><td>Jenis Kelamin</td><td>: {{ $item->jenis_kelamin_surat ?? $item->jenis_kelamin }}</td></tr>
                            <tr><td>Pekerjaan</td><td>: {{ $item->pekerjaan_surat ?? $item->pekerjaan }}</td></tr>
                            <tr><td>Agama</td><td>: {{ $item->agama_surat ?? $item->agama }}</td></tr>
                            <tr><td>Status Perkawinan</td><td>: {{ $item->status_perkawinan_surat ?? $item->status_perkawinan }}</td></tr>
                            <tr><td>Kewarganegaraan</td><td>: {{ $item->kewarganegaraan_surat ?? 'Indonesia' }}</td></tr>
                            <tr><td>Alamat</td><td>: {{ $item->alamat_surat ?? $item->alamat }}</td></tr>
                        </table>
                    </div>
                    <p class="small">Dengan ini menerangkan bahwa orang yang bersangkutan benar tingal berdomisili di Desa Tanimulya Kecamatan Ngamprah Kabupaten Bandung Barat.</p>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-3 d-flex justify-content-between align-items-center">
                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>Diajukan: {{ $item->tanggal_pengajuan }}</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </button>
                    <button type="button" class="btn btn-outline-success rounded-pill px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#modalTtdRw{{ $item->id }}"
                            data-bs-dismiss="modal">
                        <i class="bi bi-check-circle me-1"></i>Setujui Dokumen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal 6: Tanda Tangan & Stempel Digital --}}
<div class="modal fade" id="modalTtdRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Tanda Tangan & Stempel Digital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 bg-white">
                {{-- Tanda Tangan Digital --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Tanda Tangan Digital</span>
                        <a href="javascript:void(0)" onclick="clearSignatureCanvas({{ $item->id }})" class="text-danger small fw-bold text-decoration-none"><i class="bi bi-trash me-1"></i>Bersihkan</a>
                    </div>
                    <div class="border rounded-3 text-center bg-white overflow-hidden position-relative" style="height: 180px;">
                        <canvas id="signatureCanvas{{ $item->id }}" class="signature-canvas w-100 h-100" style="touch-action: none; cursor: crosshair;"></canvas>
                        <div id="signaturePlaceholder{{ $item->id }}" class="position-absolute top-50 start-50 translate-middle text-muted small pointer-events-none" style="pointer-events: none;">
                            Gambar tanda tangan Anda di sini
                        </div>
                    </div>
                </div>

                {{-- Stempel Digital --}}
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Stempel Digital</span>
                    <div id="dropZone{{ $item->id }}" class="border border-success border-2 rounded-3 p-4 mt-2 text-center position-relative drop-zone" style="border-style: dashed !important; background: rgba(25, 135, 84, 0.03); cursor: pointer;" onclick="document.getElementById('stempelInput{{ $item->id }}').click()">
                        <input type="file" id="stempelInput{{ $item->id }}" class="d-none" accept="image/png" onchange="handleStempelUpload(this, {{ $item->id }})">
                        <div id="stempelPreviewContainer{{ $item->id }}" class="d-none">
                            <img id="stempelPreview{{ $item->id }}" src="" alt="Preview Stempel" style="max-height: 120px; object-fit: contain;">
                            <div class="mt-2 text-success small fw-bold">Klik untuk mengganti gambar</div>
                        </div>
                        <div id="stempelPlaceholder{{ $item->id }}">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                <i class="bi bi-cloud-arrow-up text-success fs-5"></i>
                            </div>
                            <p class="small mb-1 fw-semibold">Tarik & lepas file stempel di sini, atau <span class="text-success fw-bold text-decoration-underline">Pilih File</span></p>
                            <p class="text-muted" style="font-size: 11px;">Format didukung: PNG (Transparan). Maks ukuran: 2MB.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-3 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-outline-success rounded-pill px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#modalConfirmRw{{ $item->id }}"
                        data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-1"></i>Simpan & Terapkan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 4: Konfirmasi Sukses (Surat Siap Dikirim) --}}
<div class="modal fade" id="modalConfirmRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow text-center bg-white" style="border-radius: 16px;">
            <div class="modal-body p-5 bg-white">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-check-lg text-success fs-2"></i>
                </div>
                <h5 class="fw-bold mb-2">Surat Siap Dikirim</h5>
                <p class="text-muted small mb-4">Surat sudah berhasil dibuat dan siap untuk dikirimkan ke pengaju.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 text-nowrap" data-bs-dismiss="modal">Kembali</button>
                    <form action="{{ route('rw.surat.approve', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4 text-nowrap">Setujui & Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal 5: Catatan Telah Dikirim --}}
<div class="modal fade" id="modalRejectSuccessRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow text-center bg-white" style="border-radius: 16px;">
            <div class="modal-body p-5 bg-white">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-check-circle-fill text-success fs-2"></i>
                </div>
                <h5 class="fw-bold mb-2">Catatan Telah Dikirim</h5>
                <p class="text-muted small mb-4">Pengajuan yang telah ditolak berhasil dikirim kembali ke pengaju beserta catatan.</p>
                <a href="{{ route('rw.persetujuan-dokumen') }}" class="btn btn-success rounded-pill px-4 w-100">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</div>

@endforeach
@endsection

@push('scripts')
<script>
function toggleRwLampiran(id) {
    const ktpEl = document.getElementById('rwLampiranKTP' + id);
    const kkEl = document.getElementById('rwLampiranKK' + id);
    const labelEl = document.getElementById('rwLampiranLabel' + id);
    const toggleEl = document.getElementById('rwLampiranToggle' + id);

    if (ktpEl.classList.contains('d-none')) {
        ktpEl.classList.remove('d-none');
        kkEl.classList.add('d-none');
        labelEl.textContent = 'LAMPIRAN DOKUMEN (KTP)';
        toggleEl.textContent = 'LIHAT KARTU KELUARGA →';
    } else {
        ktpEl.classList.add('d-none');
        kkEl.classList.remove('d-none');
        labelEl.textContent = 'LAMPIRAN DOKUMEN (KK)';
        toggleEl.textContent = 'LIHAT KTP →';
    }
}

// Logic untuk Tanda Tangan Canvas & Stempel Drag Drop
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('[id^="modalTtdRw"]');
    
    modals.forEach(modal => {
        const id = modal.id.replace('modalTtdRw', '');
        const canvas = document.getElementById('signatureCanvas' + id);
        const placeholder = document.getElementById('signaturePlaceholder' + id);
        const ctx = canvas.getContext('2d');
        
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Resize canvas ketika modal terbuka
        modal.addEventListener('shown.bs.modal', function () {
            const rect = canvas.parentElement.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
            // Background putih agar tidak transparan saat disave ke base64
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        });

        function getPointerPos(canvas, evt) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: evt.clientX - rect.left,
                y: evt.clientY - rect.top
            };
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault(); 
            
            if(placeholder) placeholder.classList.add('d-none');

            const pos = getPointerPos(canvas, e);

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.strokeStyle = '#000000'; // Warna tinta hitam
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();

            lastX = pos.x;
            lastY = pos.y;
        }

        function startDrawing(e) {
            isDrawing = true;
            const pos = getPointerPos(canvas, e);
            lastX = pos.x;
            lastY = pos.y;
            // Menangkap pointer agar tetap melacak secara presisi meskipun jari/kursor keluar batas
            canvas.setPointerCapture(e.pointerId);
            e.preventDefault();
        }

        function stopDrawing(e) {
            if (isDrawing) {
                isDrawing = false;
                canvas.releasePointerCapture(e.pointerId);
            }
        }

        // Menggunakan Pointer Events untuk mencakup Mouse, Touch (Layar Sentuh), dan Stylus secara universal
        canvas.addEventListener('pointerdown', startDrawing);
        canvas.addEventListener('pointermove', draw);
        canvas.addEventListener('pointerup', stopDrawing);
        canvas.addEventListener('pointercancel', stopDrawing);
        
        // Drag and drop zone logic untuk Stempel
        const dropZone = document.getElementById('dropZone' + id);
        const fileInput = document.getElementById('stempelInput' + id);

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-success');
                dropZone.classList.add('border-primary'); // Highlight
                dropZone.style.background = 'rgba(13, 110, 253, 0.05)';
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-primary');
                dropZone.classList.add('border-success');
                dropZone.style.background = 'rgba(25, 135, 84, 0.03)';
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if(files.length > 0) {
                fileInput.files = files; // Attach to input
                window.handleStempelUpload(fileInput, id); // Trigger preview
            }
        });
    });
});

window.clearSignatureCanvas = function(id) {
    const canvas = document.getElementById('signatureCanvas' + id);
    const placeholder = document.getElementById('signaturePlaceholder' + id);
    const ctx = canvas.getContext('2d');
    
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    if(placeholder) placeholder.classList.remove('d-none');
};

window.handleStempelUpload = function(input, id) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validasi PNG
        if (file.type !== 'image/png') {
            alert('Format tidak didukung! Harap unggah file PNG transparan.');
            input.value = '';
            return;
        }

        // Validasi ukuran < 2MB
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('stempelPreview' + id).src = e.target.result;
            document.getElementById('stempelPreviewContainer' + id).classList.remove('d-none');
            document.getElementById('stempelPlaceholder' + id).classList.add('d-none');
        }
        reader.readAsDataURL(file);
    }
};
</script>
@endpush
