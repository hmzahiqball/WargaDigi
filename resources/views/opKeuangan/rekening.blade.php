@extends('layouts.global')

@section('title', 'Pengaturan Rekening')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Pengaturan Rekening</h2>
        <p class="text-muted mb-0">Atur rekening bank dan QRIS untuk penerimaan tagihan iuran unit <span class="badge bg-primary text-uppercase px-2 py-1">{{ $unit ?? 'Keuangan' }}</span>.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('opkeuangan.rekening.save') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Bank <span class="text-danger">*</span></label>
                        <input type="text" name="bank" class="form-control" placeholder="Contoh: BCA / Mandiri / BRI" value="{{ old('bank', $rekening->bank ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="no_rek" class="form-control" placeholder="Contoh: 1234567890" value="{{ old('no_rek', $rekening->no_rek ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Atas Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama_rek" class="form-control" placeholder="Atas nama di rekening" value="{{ old('nama_rek', $rekening->nama_rek ?? '') }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload QRIS (Opsional)</label>
                        <input type="file" name="qris_file" id="qrisFileInput" class="form-control" accept="image/*">
                        <input type="hidden" name="qris_base64" id="qrisBase64Input">
                        <div class="form-text">Upload gambar QRIS (opsional) agar warga bisa scan untuk membayar. Format: JPG, PNG, WEBP (maksimal 5MB).</div>
                        
                        <div id="qrisNewPreviewContainer" class="mt-3" style="display: none;">
                            <p class="mb-1 text-success small fw-semibold"><i class="bi bi-check-circle me-1"></i> Preview QRIS Baru yang Dipilih:</p>
                            <img id="qrisNewPreviewImg" src="" alt="Preview QRIS" class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
                        </div>

                        @if(isset($rekening) && $rekening->qris_file)
                            <div class="mt-3" id="qrisCurrentContainer">
                                <p class="mb-1 text-muted small">QRIS Saat Ini:</p>
                                @php
                                    $cleanQris = str_replace('\\', '/', $rekening->qris_file);
                                    $qrisUrl = str_starts_with($cleanQris, 'storage/') ? asset($cleanQris) : asset('storage/' . $cleanQris);
                                @endphp
                                <img src="{{ $qrisUrl }}" alt="QRIS" class="img-fluid rounded border" style="max-height: 200px;">
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('qrisFileInput');
    const base64Input = document.getElementById('qrisBase64Input');
    const previewContainer = document.getElementById('qrisNewPreviewContainer');
    const previewImg = document.getElementById('qrisNewPreviewImg');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (base64Input) base64Input.value = e.target.result;
                    if (previewImg) previewImg.src = e.target.result;
                    if (previewContainer) previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                if (base64Input) base64Input.value = '';
                if (previewContainer) previewContainer.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
