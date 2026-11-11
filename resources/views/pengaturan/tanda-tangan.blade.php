@extends('layouts.global')

@section('title', 'Pengaturan Tanda Tangan')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold" style="color: #198754;">Pengaturan Tanda Tangan &amp; Stempel</h2>
    <p class="text-muted mb-0">Simpan tanda tangan dan stempel default agar tidak perlu mengisi ulang setiap menyetujui surat.</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
    <ul class="mb-0 list-unstyled">@foreach($errors->all() as $e)<li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('pengaturan.ttd.update') }}" method="POST" enctype="multipart/form-data" id="formTtd">
    @csrf
    <input type="hidden" name="ttd_base64" id="ttdBase64">
    <input type="hidden" name="hapus_ttd" id="hapusTtd" value="0">
    <input type="hidden" name="hapus_stempel" id="hapusStempel" value="0">

    <div class="row g-4">
        {{-- Tanda Tangan --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-pen-fill text-success me-2"></i>Tanda Tangan Default</h5>

                    @if($user->ttd_default)
                        <div class="mb-3 text-center border rounded-3 p-3" style="background:#fafafa;" id="ttdTersimpan">
                            <p class="text-muted small mb-2">Tanda tangan tersimpan saat ini:</p>
                            <img src="{{ route('pengaturan.ttd.file', ['jenis' => 'ttd']) }}?v={{ $user->updated_at->timestamp }}" alt="TTD" class="img-fluid" style="max-height:120px;">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="hapusTtd()"><i class="bi bi-trash me-1"></i>Hapus</button>
                            </div>
                        </div>
                    @endif

                    <ul class="nav nav-pills gap-2 mb-3" role="tablist">
                        <li class="nav-item"><button class="nav-link active rounded-pill px-3 py-1" data-bs-toggle="pill" data-bs-target="#modeUpload" type="button"><i class="bi bi-upload me-1"></i>Unggah File</button></li>
                        <li class="nav-item"><button class="nav-link rounded-pill px-3 py-1" data-bs-toggle="pill" data-bs-target="#modeGambar" type="button"><i class="bi bi-pencil me-1"></i>Gambar</button></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="modeUpload">
                            <label class="form-label small fw-bold text-muted">File PNG (disarankan latar transparan, maks 2MB)</label>
                            <input type="file" name="ttd_file" class="form-control" accept=".png">
                        </div>
                        <div class="tab-pane fade" id="modeGambar">
                            <label class="form-label small fw-bold text-muted">Gambar tanda tangan</label>
                            <div class="border rounded-3 position-relative" style="height:180px;background:#fafafa;touch-action:none;">
                                <canvas id="ttdCanvas" style="width:100%;height:100%;display:block;cursor:crosshair;"></canvas>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill mt-2" onclick="clearTtdCanvas()"><i class="bi bi-arrow-counterclockwise me-1"></i>Ulangi</button>
                            <p class="text-muted small mb-0 mt-1">Hasil gambar akan disimpan saat Anda menekan tombol Simpan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stempel --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-image text-success me-2"></i>Stempel Default</h5>

                    @if($user->stempel_default)
                        <div class="mb-3 text-center border rounded-3 p-3" style="background:#fafafa;">
                            <p class="text-muted small mb-2">Stempel tersimpan saat ini:</p>
                            <img src="{{ route('pengaturan.ttd.file', ['jenis' => 'stempel']) }}?v={{ $user->updated_at->timestamp }}" alt="Stempel" class="img-fluid" style="max-height:120px;">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="hapusStempel()"><i class="bi bi-trash me-1"></i>Hapus</button>
                            </div>
                        </div>
                    @endif

                    <label class="form-label small fw-bold text-muted">File PNG (disarankan latar transparan, maks 2MB)</label>
                    <input type="file" name="stempel_file" class="form-control" accept=".png">
                    <p class="text-muted small mb-0 mt-2">Stempel bersifat opsional.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-save me-1"></i>Simpan Pengaturan</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const canvas = document.getElementById('ttdCanvas');
    let drawing = false, ctx = null, kosong = true;
    function siapkanCanvas() {
        if (!canvas) return;
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width; canvas.height = rect.height;
        ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#000'; ctx.lineWidth = 2; ctx.lineCap = 'round';
    }
    function pos(e) {
        const r = canvas.getBoundingClientRect();
        const t = e.touches ? e.touches[0] : e;
        return { x: t.clientX - r.left, y: t.clientY - r.top };
    }
    function start(e) { drawing = true; kosong = false; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); e.preventDefault(); }
    function move(e) { if (!drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); e.preventDefault(); }
    function end() { drawing = false; }

    if (canvas) {
        // Inisialisasi ukuran saat tab Gambar pertama kali ditampilkan.
        document.querySelector('[data-bs-target="#modeGambar"]').addEventListener('shown.bs.tab', siapkanCanvas);
        setTimeout(siapkanCanvas, 300);
        canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move);
        window.addEventListener('mouseup', end);
        canvas.addEventListener('touchstart', start); canvas.addEventListener('touchmove', move);
        canvas.addEventListener('touchend', end);
    }
    window.clearTtdCanvas = function () { if (ctx) { ctx.clearRect(0, 0, canvas.width, canvas.height); kosong = true; } };

    document.getElementById('formTtd').addEventListener('submit', function () {
        // Sertakan hasil gambar hanya bila tab Gambar aktif dan canvas tidak kosong.
        const tabGambarAktif = document.getElementById('modeGambar').classList.contains('active');
        if (tabGambarAktif && !kosong) {
            document.getElementById('ttdBase64').value = canvas.toDataURL('image/png');
        }
    });

    window.hapusTtd = function () {
        window.tampilkanKonfirmasi({
            judul: 'Hapus Tanda Tangan', pesan: 'Hapus tanda tangan default tersimpan?', tombol: 'Hapus', jenis: 'bahaya',
            onConfirm: function () {
                document.getElementById('hapusTtd').value = '1';
                document.getElementById('formTtd').submit();
            },
        });
    };
    window.hapusStempel = function () {
        window.tampilkanKonfirmasi({
            judul: 'Hapus Stempel', pesan: 'Hapus stempel default tersimpan?', tombol: 'Hapus', jenis: 'bahaya',
            onConfirm: function () {
                document.getElementById('hapusStempel').value = '1';
                document.getElementById('formTtd').submit();
            },
        });
    };
})();
</script>
@endpush
