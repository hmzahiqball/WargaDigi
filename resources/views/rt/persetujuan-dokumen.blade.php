@extends('layouts.global')

@section('title', 'Persetujuan Dokumen')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">Portal Persetujuan Dokumen</h2>
    <p class="text-muted mb-0">Verifikasi dan tanda tangani dokumen pengajuan warga.</p>
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
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#198754,#20c997);"><i class="bi bi-person-fill text-white fs-5"></i></div>
                <div>
                    <h6 class="fw-bold mb-0">{{ $item->nama_pemohon }}</h6>
                    <span class="text-muted small">NIK: {{ $item->nik }} &bull; {{ $item->tanggal_pengajuan }}</span>
                </div>
            </div>
            <span class="badge rounded-pill px-3 py-2" style="background:#FFF3CD;color:#856404;">{{ $item->status }}</span>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4"><span class="text-muted small">Jenis Surat</span><br><span class="fw-semibold small">{{ $item->tipe_surat }}</span></div>
            <div class="col-md-4"><span class="text-muted small">Alamat</span><br><span class="fw-semibold small">{{ $item->alamat }}</span></div>
            <div class="col-md-4"><span class="text-muted small">Pekerjaan</span><br><span class="fw-semibold small">{{ $item->pekerjaan }}</span></div>
        </div>
        @if($item->status === 'Diajukan')
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}"><i class="bi bi-eye me-1"></i>Detail Permintaan</button>
            <button class="btn btn-outline-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTtdRt{{ $item->id }}"><i class="bi bi-pen me-1"></i>Tanda Tangan & Setujui</button>
            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalReject{{ $item->id }}"><i class="bi bi-x-circle me-1"></i>Tolak</button>
        </div>
        @endif
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Detail Permintaan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <div class="border rounded-3 p-3 h-100">
                            <span class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px;">Pemohon</span>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#198754,#20c997);"><i class="bi bi-person-fill text-white fs-5"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="font-size:14px;">{{ $item->nama_pemohon }}</h6>
                                    <span class="text-muted" style="font-size:12px;">NIK: {{ $item->nik }}</span><br>
                                    <span class="text-muted" style="font-size:12px;">RT {{ $item->kode_rt ?? '01' }} / RW 21</span>
                                </div>
                            </div>
                            <div class="mt-3"><span class="text-muted" style="font-size:12px;">Tipe Surat:</span><br><span class="fw-bold" style="font-size:13px;">{{ $item->tipe_surat }}</span></div>
                            <div class="mt-2"><span class="text-muted" style="font-size:12px;"><i class="bi bi-calendar3 me-1"></i>Diajukan: {{ $item->tanggal_pengajuan }}</span></div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="border rounded-3 p-3 h-100" style="border-color:#198754 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px;">Data Lengkap Pemohon</span>
                                <span class="badge rounded-pill px-3 py-1" style="background:#FFF3CD;color:#856404;">{{ $item->status }}</span>
                            </div>
                            <div class="row g-2" style="font-size:13px;">
                                <div class="col-6"><span class="text-muted">Jenis Kelamin</span><br><span class="fw-bold">{{ $item->jenis_kelamin }}</span></div>
                                <div class="col-6"><span class="text-muted">Tempat, Tgl Lahir</span><br><span class="fw-bold">{{ $item->tempat_tgl_lahir }}</span></div>
                                <div class="col-6"><span class="text-muted">Agama</span><br><span class="fw-bold">{{ $item->agama }}</span></div>
                                <div class="col-6"><span class="text-muted">Pendidikan Terakhir</span><br><span class="fw-bold">{{ $item->pendidikan ?? '-' }}</span></div>
                                <div class="col-6"><span class="text-muted">Jenis Pekerjaan</span><br><span class="fw-bold">{{ $item->pekerjaan }}</span></div>
                                <div class="col-6"><span class="text-muted">Status Perkawinan</span><br><span class="fw-bold">{{ $item->status_perkawinan }}</span></div>
                                <div class="col-6"><span class="text-muted">Kewarganegaraan</span><br><span class="fw-bold">{{ $item->kewarganegaraan ?? 'WNI' }}</span></div>
                                <div class="col-6"><span class="text-muted">Alamat</span><br><span class="fw-bold">{{ $item->alamat }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px;" id="lampiranLabel{{ $item->id }}">Lampiran Dokumen (KTP)</span>
                        <a href="#" class="text-success fw-bold small text-decoration-none" id="lampiranToggleBtn{{ $item->id }}" onclick="event.preventDefault(); toggleLampiran('{{ $item->id }}');">Lihat Kartu Keluarga &rarr;</a>
                    </div>
                    <div id="lampiranKtpBox{{ $item->id }}" class="border rounded-3 p-3 text-center" style="background:rgba(13,110,253,0.02); min-height:180px;">
                        @if($item->file_ktp_url)
                            <img src="{{ $item->file_ktp_url }}" alt="KTP" class="img-fluid rounded shadow-sm" style="max-height:160px; cursor:pointer;" onclick="showRtImageModal('{{ $item->file_ktp_url }}', 'Preview KTP')">
                            <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="showRtImageModal('{{ $item->file_ktp_url }}', 'Preview KTP')"><i class="bi bi-eye me-1"></i>Lihat Dokumen</button></div>
                        @else
                            <div class="py-4"><i class="bi bi-file-earmark-x text-muted fs-1 d-block mb-2"></i><span class="text-muted small">Belum diunggah</span></div>
                        @endif
                    </div>
                    <div id="lampiranKkBox{{ $item->id }}" class="border rounded-3 p-3 text-center d-none" style="background:rgba(13,110,253,0.02); min-height:180px;">
                        @if($item->file_kk_url)
                            <img src="{{ $item->file_kk_url }}" alt="KK" class="img-fluid rounded shadow-sm d-block mx-auto" style="max-height:160px; cursor:pointer;" onclick="showRtImageModal('{{ $item->file_kk_url }}', 'Preview KK')">
                            <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="showRtImageModal('{{ $item->file_kk_url }}', 'Preview KK')"><i class="bi bi-eye me-1"></i>Lihat Dokumen</button></div>
                        @else
                            <div class="py-4"><i class="bi bi-file-earmark-x text-muted fs-1 d-block mb-2"></i><span class="text-muted small">Belum diunggah</span></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalReject{{ $item->id }}"><i class="bi bi-x-circle me-1"></i>Tolak & Beri Catatan</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalTtdRt{{ $item->id }}"><i class="bi bi-check-circle me-1"></i>Setujui Dokumen</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal TTD RT with Live Preview --}}
<div class="modal fade" id="modalTtdRt{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pen-fill text-success me-2"></i>Tanda Tangan & Setujui</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <p class="text-muted small mb-3">Bubuhkan tanda tangan sebagai Ketua RT untuk menyetujui surat <strong>{{ $item->nama_pemohon }}</strong>.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tanda Tangan RT <span class="text-danger">*</span></label>
                            <div class="border rounded-3 position-relative" style="height: 180px; background: #fafafa; touch-action: none;">
                                <canvas id="sigCanvasRt{{ $item->id }}" style="width:100%;height:100%;display:block;cursor:crosshair;"></canvas>
                                <div id="sigPlaceholderRt{{ $item->id }}" class="position-absolute top-50 start-50 translate-middle text-muted small text-center" style="pointer-events:none;">
                                    <i class="bi bi-pencil-square fs-3 d-block mb-1"></i>Coretan tanda tangan di sini
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill mt-2" onclick="clearSig('Rt', '{{ $item->id }}')"><i class="bi bi-arrow-counterclockwise me-1"></i>Ulangi</button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Stempel RT (Opsional)</label>
                            <div class="border border-2 rounded-3 p-3 text-center" style="border-style: dashed !important; cursor: pointer;" onclick="document.getElementById('stempelRt{{ $item->id }}').click()">
                                <input type="file" id="stempelRt{{ $item->id }}" class="d-none" accept=".png" onchange="previewStempel(this, 'stempelPreviewRt{{ $item->id }}')">
                                <div id="stempelPlaceholderRt{{ $item->id }}"><i class="bi bi-file-earmark-image text-success fs-2 d-block mb-1"></i><span class="small">Unggah Stempel (PNG, Max 2MB)</span></div>
                                <div id="stempelPreviewRt{{ $item->id }}" class="d-none"><img src="" class="img-fluid" style="max-height:70px;" alt="Stempel"><p class="text-success small mt-1 mb-0"><i class="bi bi-check-circle me-1"></i>Siap</p><button type="button" class="btn btn-sm btn-outline-danger rounded-pill mt-1" onclick="clearStempel('Rt', '{{ $item->id }}')"><i class="bi bi-trash me-1"></i>Hapus Stempel</button></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <label class="form-label fw-bold small"><i class="bi bi-file-earmark-text me-1"></i>Pratinjau Surat (Real-time)</label>
                        <div class="bg-light border rounded-3 shadow-sm" style="overflow:hidden;">
                            <iframe id="previewFrameRt{{ $item->id }}" src="{{ route('rt.surat.preview', $item->id) }}" style="width:100%;height:500px;border:none;" title="Preview Surat"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="submitApprove('Rt', '{{ $item->id }}')"><i class="bi bi-check-circle me-1"></i>Setujui & Teruskan ke RW</button>
            </div>
            <form id="formApproveRt{{ $item->id }}" action="{{ route('rt.surat.approve', $item->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
                <input type="hidden" name="signature_rt" id="hiddenSigRt{{ $item->id }}">
            </form>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="modalReject{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Tolak Pengajuan Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rt.surat.reject', $item->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-3">
                    <div class="rounded-3 p-3 mb-3" style="background:#FFF3CD; border: 1px solid #FFE69C;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-warning mt-1"></i>
                            <span class="small" style="color:#664D03;">Mohon berikan alasan penolakan atau catatan koreksi agar pemohon dapat melakukan perbaikan pada pengajuan ini.</span>
                        </div>
                    </div>
                    <label class="form-label fw-bold small">Alasan Penolakan / Catatan Koreksi <span class="text-danger">*</span></label>
                    <textarea name="catatan_penolakan" class="form-control" rows="4" placeholder="Contoh: Data KTP tidak sesuai dengan yang terdaftar..." required style="border-radius: 12px;"></textarea>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold"><i class="bi bi-x-circle me-1"></i>Tolak & Kirim Catatan</button>
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
        <p class="text-muted">Belum ada dokumen yang perlu diverifikasi saat ini.</p>
    </div>
</div>
@endforelse

<div class="modal fade" id="rtImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold" id="rtImageTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4 text-center">
                <img src="" id="rtImageSrc" class="img-fluid rounded shadow" style="max-height: 70vh;" alt="Preview">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRtImageModal(src, title) {
    document.getElementById('rtImageSrc').src = src;
    document.getElementById('rtImageTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('rtImageModal')).show();
}

function toggleLampiran(id) {
    var ktpBox = document.getElementById('lampiranKtpBox' + id);
    var kkBox = document.getElementById('lampiranKkBox' + id);
    var label = document.getElementById('lampiranLabel' + id);
    var btn = document.getElementById('lampiranToggleBtn' + id);
    if (kkBox.classList.contains('d-none')) {
        kkBox.classList.remove('d-none');
        ktpBox.classList.add('d-none');
        label.textContent = 'Lampiran Dokumen (KK)';
        btn.innerHTML = 'Lihat KTP &rarr;';
    } else {
        ktpBox.classList.remove('d-none');
        kkBox.classList.add('d-none');
        label.textContent = 'Lampiran Dokumen (KTP)';
        btn.innerHTML = 'Lihat Kartu Keluarga &rarr;';
    }
}

function injectImgToIframe(iframe, selector, src, className) {
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({
            type: 'update-preview',
            selector: selector,
            src: src,
            className: className
        }, '*');
    }
}

function removeImgFromIframe(iframe, selector, className) {
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({
            type: 'clear-preview',
            selector: selector,
            className: className
        }, '*');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="modalTtdRt"]').forEach(modal => {
        const id = modal.id.replace('modalTtdRt', '');
        const canvas = document.getElementById('sigCanvasRt' + id);
        if (!canvas) return;
        const placeholder = document.getElementById('sigPlaceholderRt' + id);
        const ctx = canvas.getContext('2d');
        let drawing = false, lx = 0, ly = 0;
        modal.addEventListener('shown.bs.modal', function() {
            const r = canvas.parentElement.getBoundingClientRect();
            canvas.width = r.width; canvas.height = r.height;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
        function gp(e) { const r = canvas.getBoundingClientRect(); return { x: e.clientX - r.left, y: e.clientY - r.top }; }
        canvas.addEventListener('pointerdown', function(e) { drawing = true; const p = gp(e); lx = p.x; ly = p.y; canvas.setPointerCapture(e.pointerId); e.preventDefault(); });
        canvas.addEventListener('pointermove', function(e) {
            if (!drawing) return; e.preventDefault();
            if (placeholder) placeholder.classList.add('d-none');
            const p = gp(e);
            ctx.beginPath(); ctx.moveTo(lx, ly); ctx.lineTo(p.x, p.y);
            ctx.strokeStyle = '#000'; ctx.lineWidth = 3; ctx.lineCap = 'round'; ctx.stroke();
            lx = p.x; ly = p.y;
            // Real-time update to iframe preview (RT column)
            const iframe = document.getElementById('previewFrameRt' + id);
            if (iframe) injectImgToIframe(iframe, '.rt-col .ttd-box', canvas.toDataURL('image/png'), 'ttd-img');
        });
        canvas.addEventListener('pointerup', function(e) {
            drawing = false; canvas.releasePointerCapture(e.pointerId);
            const iframe = document.getElementById('previewFrameRt' + id);
            if (iframe) injectImgToIframe(iframe, '.rt-col .ttd-box', canvas.toDataURL('image/png'), 'ttd-img');
        });
    });
});

window.clearSig = function(role, id) {
    const canvas = document.getElementById('sigCanvas' + role + id);
    const ph = document.getElementById('sigPlaceholder' + role + id);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if (ph) ph.classList.remove('d-none');
    // Remove from iframe preview
    const colSelector = role === 'Rt' ? '.rt-col .ttd-box' : '.rw-col .ttd-box';
    const iframe = document.getElementById('previewFrame' + role + id);
    if (iframe) removeImgFromIframe(iframe, colSelector, 'ttd-img');
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
            // Inject stempel into iframe preview
            const role = previewId.includes('Rt') ? 'Rt' : 'Rw';
            const id = previewId.replace('stempelPreview' + role, '');
            const colSelector = role === 'Rt' ? '.rt-col .ttd-box' : '.rw-col .ttd-box';
            const iframe = document.getElementById('previewFrame' + role + id);
            if (iframe) injectImgToIframe(iframe, colSelector, e.target.result, 'stempel-img');
        };
        reader.readAsDataURL(f);
    }
};

window.clearStempel = function(role, id) {
    const fileInput = document.getElementById('stempel' + role + id);
    if (fileInput) fileInput.value = '';
    const previewEl = document.getElementById('stempelPreview' + role + id);
    const placeholderEl = document.getElementById('stempelPlaceholder' + role + id);
    if (previewEl) { previewEl.classList.add('d-none'); previewEl.querySelector('img').src = ''; }
    if (placeholderEl) placeholderEl.classList.remove('d-none');
    // Remove from iframe preview
    const colSelector = role === 'Rt' ? '.rt-col .ttd-box' : '.rw-col .ttd-box';
    const iframe = document.getElementById('previewFrame' + role + id);
    if (iframe) removeImgFromIframe(iframe, colSelector, 'stempel-img');
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