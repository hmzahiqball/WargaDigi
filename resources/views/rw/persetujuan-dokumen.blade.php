@extends('layouts.global')

@section('title', 'Persetujuan Dokumen')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">Portal Persetujuan Dokumen</h2>
    <p class="text-muted mb-0">Permohonan dokumen tahap akhir menunggu pengesahan RW.</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@forelse($pengajuan as $item)
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4 bg-white" style="border-radius: 16px;">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                    <i class="bi bi-person-fill text-primary fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">{{ $item->nama_pemohon }}</h6>
                    <span class="text-muted small">NIK: {{ $item->nik }} &bull; {{ $item->tanggal_pengajuan }}</span>
                </div>
            </div>
            @php
                $sc = match($item->status) {
                    'Disetujui RT' => ['bg' => '#FFF3CD', 'color' => '#B8860B', 'label' => 'Menunggu Review RW'],
                    'Selesai' => ['bg' => '#D4EDDA', 'color' => '#155724', 'label' => 'Selesai'],
                    'Ditolak RW' => ['bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak RW'],
                    default => ['bg' => '#E2E3E5', 'color' => '#383D41', 'label' => $item->status],
                };
            @endphp
            <span class="badge rounded-pill px-3 py-1" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:11px;">{{ $sc['label'] }}</span>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-md-4"><span class="text-muted small">Jenis Surat</span><br><span class="fw-semibold small">{{ $item->tipe_surat }}</span></div>
            <div class="col-md-4"><span class="text-muted small">Alamat</span><br><span class="fw-semibold small">{{ $item->alamat }}</span></div>
            <div class="col-md-4"><span class="text-muted small">Pekerjaan</span><br><span class="fw-semibold small">{{ $item->pekerjaan }}</span></div>
        </div>
        @if($item->status === 'Disetujui RT')
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetailRw{{ $item->id }}"><i class="bi bi-eye me-1"></i>Lihat Detail & Preview Surat</button>
            <button class="btn btn-outline-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTtdRw{{ $item->id }}"><i class="bi bi-pen me-1"></i>Tanda Tangan & Sahkan</button>
            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalRejectRw{{ $item->id }}"><i class="bi bi-x-circle me-1"></i>Tolak</button>
        </div>
        @endif
    </div>
</div>

{{-- Modal Detail RW --}}
<div class="modal fade" id="modalDetailRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Detail & Preview Surat - {{ $item->nama_pemohon }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="bi bi-person-vcard me-2 text-primary"></i>Data Diri</h6>
                        <table class="table table-sm table-borderless small">
                            <tr><td class="text-muted" style="width:150px">Nama</td><td class="fw-semibold">{{ $item->nama_pemohon }}</td></tr>
                            <tr><td class="text-muted">NIK</td><td>{{ $item->nik }}</td></tr>
                            <tr><td class="text-muted">TTL</td><td>{{ $item->tempat_tgl_lahir }}</td></tr>
                            <tr><td class="text-muted">Jenis Kelamin</td><td>{{ $item->jenis_kelamin }}</td></tr>
                            <tr><td class="text-muted">Agama</td><td>{{ $item->agama }}</td></tr>
                            <tr><td class="text-muted">Pekerjaan</td><td>{{ $item->pekerjaan }}</td></tr>
                            <tr><td class="text-muted">Status</td><td>{{ $item->status_perkawinan }}</td></tr>
                            <tr><td class="text-muted">Alamat</td><td>{{ $item->alamat }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-image me-2 text-primary"></i>Dokumen Pendukung</h6>
                        <div class="border rounded-3 p-3 mb-3 text-center" style="background: rgba(13,110,253,0.02);">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 mb-2">KTP</span>
                            @if($item->file_ktp_url)
                                <img src="{{ $item->file_ktp_url }}" alt="KTP" class="img-fluid rounded shadow-sm d-block mx-auto" style="max-height: 130px; cursor:pointer;" onclick="showRwImg('{{ $item->file_ktp_url }}', 'Preview KTP')">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill mt-2" onclick="showRwImg('{{ $item->file_ktp_url }}', 'Preview KTP')"><i class="bi bi-zoom-in me-1"></i>Perbesar</button>
                            @else
                                <p class="text-muted small mb-0">Belum diunggah</p>
                            @endif
                        </div>
                        <div class="border rounded-3 p-3 text-center" style="background: rgba(13,110,253,0.02);">
                            <span class="badge bg-info-subtle text-info rounded-pill px-3 mb-2">Kartu Keluarga</span>
                            @if($item->file_kk_url)
                                <img src="{{ $item->file_kk_url }}" alt="KK" class="img-fluid rounded shadow-sm d-block mx-auto" style="max-height: 130px; cursor:pointer;" onclick="showRwImg('{{ $item->file_kk_url }}', 'Preview KK')">
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill mt-2" onclick="showRwImg('{{ $item->file_kk_url }}', 'Preview KK')"><i class="bi bi-zoom-in me-1"></i>Perbesar</button>
                            @else
                                <p class="text-muted small mb-0">Belum diunggah</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Preview Surat dengan TTD RT --}}
                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Preview Surat (Sudah Ditandatangani RT)</h6>
                <div class="bg-white border rounded-3 p-4 shadow-sm" style="font-family: 'Times New Roman', serif; font-size: 13px;">
                    <div class="text-center mb-3">
                        <strong style="font-size: 14px;">RUKUN WARGA 21</strong><br>
                        <strong style="font-size: 14px;">KOMPLEK PURI CIPAGERAN INDAH II</strong><br>
                        <strong style="font-size: 13px;">DESA TANIMULYA, KECAMATAN NGAMPRAH, KABUPATEN BANDUNG BARAT</strong>
                        <hr style="border-top: 2px solid #000;" class="my-2">
                    </div>
                    <p class="text-center"><strong><u>SURAT PENGANTAR</u></strong></p>
                    <p>Yang bertandatangan dibawah ini, Pengurus RT/RW21, menerangkan bahwa:</p>
                    <table class="small" style="margin-left: 20px;">
                        <tr><td style="width:160px">Nama Lengkap</td><td>: {{ $item->nama_pemohon_surat }}</td></tr>
                        <tr><td>Tempat & Tanggal lahir</td><td>: {{ $item->tempat_tgl_lahir_surat }}</td></tr>
                        <tr><td>Alamat</td><td>: {{ $item->alamat_surat }}</td></tr>
                        <tr><td>Nomor KK / NIK</td><td>: {{ $item->no_kk }} / {{ $item->nik_surat }}</td></tr>
                        <tr><td>Jenis Kelamin</td><td>: {{ $item->jenis_kelamin_surat }}</td></tr>
                        <tr><td>Agama</td><td>: {{ $item->agama_surat }}</td></tr>
                        <tr><td>Status Perkawinan</td><td>: {{ $item->status_perkawinan_surat }}</td></tr>
                        <tr><td>Pekerjaan</td><td>: {{ $item->pekerjaan_surat }}</td></tr>
                    </table>
                    <p class="mt-3"><strong>Keperluan:</strong> {{ $item->tipe_surat }}</p>
                    <div class="d-flex justify-content-between mt-4">
                        <div class="text-center" style="width:45%;">
                            <p class="mb-0">Ketua RW 21</p>
                            <div style="height:70px;" class="d-flex align-items-center justify-content-center">
                                <span class="text-muted small fst-italic">(Menunggu TTD Anda)</span>
                            </div>
                            <p class="mb-0">.............................</p>
                        </div>
                        <div class="text-center" style="width:45%;">
                            <p class="mb-0">Ketua RT</p>
                            <div style="height:70px; position:relative;" class="d-flex align-items-center justify-content-center">
                                @if($item->stempel_rt_url)
                                    <img src="{{ $item->stempel_rt_url }}" style="max-height:60px;opacity:0.7;position:absolute;z-index:1;">
                                @endif
                                @if($item->ttd_rt_url)
                                    <img src="{{ $item->ttd_rt_url }}" style="max-height:55px;position:relative;z-index:2;">
                                @else
                                    <span class="text-danger small">TTD tidak ditemukan</span>
                                @endif
                            </div>
                            <p class="mb-0">.............................</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4"><button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button></div>
        </div>
    </div>
</div>

{{-- Modal TTD RW --}}
<div class="modal fade" id="modalTtdRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pen-fill text-success me-2"></i>Tanda Tangan & Sahkan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                <p class="text-muted small mb-3">Bubuhkan tanda tangan sebagai Ketua RW untuk mensahkan surat <strong>{{ $item->nama_pemohon }}</strong>.</p>
                <div class="mb-4">
                    <label class="form-label fw-bold small">Tanda Tangan RW <span class="text-danger">*</span></label>
                    <div class="border rounded-3 position-relative" style="height: 200px; background: #fafafa; touch-action: none;">
                        <canvas id="sigCanvasRw{{ $item->id }}" style="width:100%;height:100%;display:block;cursor:crosshair;"></canvas>
                        <div id="sigPlaceholderRw{{ $item->id }}" class="position-absolute top-50 start-50 translate-middle text-muted small text-center" style="pointer-events:none;">
                            <i class="bi bi-pencil-square fs-3 d-block mb-1"></i>Coretan tanda tangan di sini
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill mt-2" onclick="clearSig('Rw', '{{ $item->id }}')"><i class="bi bi-arrow-counterclockwise me-1"></i>Ulangi</button>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small">Stempel RW (Opsional)</label>
                    <div class="border border-2 rounded-3 p-4 text-center" style="border-style: dashed !important; cursor: pointer;" onclick="document.getElementById('stempelRw{{ $item->id }}').click()">
                        <input type="file" id="stempelRw{{ $item->id }}" class="d-none" accept=".png" onchange="previewStempel(this, 'stempelPreviewRw{{ $item->id }}')">
                        <div id="stempelPlaceholderRw{{ $item->id }}"><i class="bi bi-stamp text-success fs-2 d-block mb-2"></i><span class="small">Unggah Stempel (PNG, Max 2MB)</span></div>
                        <div id="stempelPreviewRw{{ $item->id }}" class="d-none"><img src="" class="img-fluid" style="max-height:80px;" alt="Stempel"><p class="text-success small mt-1 mb-0"><i class="bi bi-check-circle me-1"></i>Siap</p></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="submitApprove('Rw', '{{ $item->id }}')"><i class="bi bi-check-circle me-1"></i>Sahkan & Selesai</button>
            </div>
            <form id="formApproveRw{{ $item->id }}" action="{{ route('rw.surat.approve', $item->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
                <input type="hidden" name="signature_rw" id="hiddenSigRw{{ $item->id }}">
            </form>
        </div>
    </div>
</div>

{{-- Modal Tolak RW --}}
<div class="modal fade" id="modalRejectRw{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle me-2"></i>Tolak Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rw.surat.reject', $item->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-3"><textarea name="catatan_penolakan" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan..." required></textarea></div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@empty
<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-body p-5 text-center bg-white" style="border-radius: 16px;">
        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
        <h5 class="fw-bold">Belum Ada Pengajuan</h5>
        <p class="text-muted">Belum ada dokumen yang perlu disahkan saat ini.</p>
    </div>
</div>
@endforelse

{{-- Modal Global Preview Gambar --}}
<div class="modal fade" id="rwImgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold" id="rwImgTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4 text-center">
                <img src="" id="rwImgSrc" class="img-fluid rounded shadow" style="max-height: 70vh;" alt="Preview">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRwImg(src, title) {
    document.getElementById('rwImgSrc').src = src;
    document.getElementById('rwImgTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('rwImgModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="modalTtdRw"]').forEach(modal => {
        const id = modal.id.replace('modalTtdRw', '');
        const canvas = document.getElementById('sigCanvasRw' + id);
        if (!canvas) return;
        const placeholder = document.getElementById('sigPlaceholderRw' + id);
        const ctx = canvas.getContext('2d');
        let drawing = false, lx = 0, ly = 0;
        modal.addEventListener('shown.bs.modal', function() {
            const r = canvas.parentElement.getBoundingClientRect();
            canvas.width = r.width; canvas.height = r.height;
            ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
        });
        function gp(e) { const r = canvas.getBoundingClientRect(); return { x: e.clientX - r.left, y: e.clientY - r.top }; }
        canvas.addEventListener('pointerdown', function(e) { drawing = true; const p = gp(e); lx = p.x; ly = p.y; canvas.setPointerCapture(e.pointerId); e.preventDefault(); });
        canvas.addEventListener('pointermove', function(e) { if (!drawing) return; e.preventDefault(); if (placeholder) placeholder.classList.add('d-none'); const p = gp(e); ctx.beginPath(); ctx.moveTo(lx, ly); ctx.lineTo(p.x, p.y); ctx.strokeStyle = '#000'; ctx.lineWidth = 3; ctx.lineCap = 'round'; ctx.stroke(); lx = p.x; ly = p.y; });
        canvas.addEventListener('pointerup', function(e) { drawing = false; canvas.releasePointerCapture(e.pointerId); });
    });
});

window.clearSig = function(role, id) {
    const canvas = document.getElementById('sigCanvas' + role + id);
    const ph = document.getElementById('sigPlaceholder' + role + id);
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
    if (ph) ph.classList.remove('d-none');
};

window.previewStempel = function(input, previewId) {
    if (input.files && input.files[0]) {
        const f = input.files[0];
        if (f.type !== 'image/png') { alert('Format harus PNG!'); input.value = ''; return; }
        if (f.size > 2*1024*1024) { alert('Max 2MB!'); input.value = ''; return; }
        const reader = new FileReader();
        reader.onload = function(e) {
            const el = document.getElementById(previewId);
            el.querySelector('img').src = e.target.result;
            el.classList.remove('d-none');
            el.previousElementSibling.classList.add('d-none');
        };
        reader.readAsDataURL(f);
    }
};

window.submitApprove = function(role, id) {
    const form = document.getElementById('formApprove' + role + id);
    const canvas = document.getElementById('sigCanvas' + role + id);
    if (canvas) document.getElementById('hiddenSig' + role + id).value = canvas.toDataURL('image/png');
    const fileInput = document.getElementById('stempel' + role + id);
    if (fileInput && fileInput.files.length > 0) { fileInput.name = 'stempel_' + role.toLowerCase(); form.appendChild(fileInput); }
    form.submit();
};
</script>
@endpush