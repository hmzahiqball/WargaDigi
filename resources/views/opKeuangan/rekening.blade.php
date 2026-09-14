@extends('layouts.global')

@section('title', 'Pengaturan Rekening')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Pengaturan Rekening</h2>
        <p class="text-muted mb-0">Atur rekening bank dan QRIS untuk penerimaan tagihan iuran.</p>
    </div>
</div>

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
                        <input type="file" name="qris_file" class="form-control" accept="image/*">
                        <div class="form-text">Upload gambar QRIS (opsional) agar warga bisa scan untuk membayar.</div>
                        @if(isset($rekening) && $rekening->qris_file)
                            <div class="mt-3">
                                <p class="mb-1 text-muted small">QRIS Saat Ini:</p>
                                <img src="{{ asset('storage/' . $rekening->qris_file) }}" alt="QRIS" class="img-fluid rounded border" style="max-height: 200px;">
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
