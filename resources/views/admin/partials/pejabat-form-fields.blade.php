{{--
    Field form "Set Ketua RW/RT".
    Params:
      $pendudukList : koleksi penduduk (punya nik, nama_lengkap)
      $selectId     : id unik untuk elemen select
--}}
<div class="mb-3">
    <label class="form-label small fw-semibold">Pilih Penduduk <span class="text-danger">*</span></label>
    <select name="penduduk_nik" id="{{ $selectId }}" class="form-select admin-input" data-tomselect data-placeholder="Cari nama atau NIK..." required>
        <option value="">— Pilih penduduk —</option>
        @foreach($pendudukList as $p)
            <option value="{{ $p->nik }}" {{ old('penduduk_nik') === $p->nik ? 'selected' : '' }}>{{ $p->nama_lengkap }} — {{ $p->nik }}</option>
        @endforeach
    </select>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Mulai Menjabat <span class="text-danger">*</span></label>
        <input type="date" name="mulai_menjabat" class="form-control admin-input" value="{{ old('mulai_menjabat', now()->toDateString()) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Tanggal Selesai Menjabat</label>
        <input type="date" name="selesai_menjabat" class="form-control admin-input" value="{{ old('selesai_menjabat') }}">
    </div>
</div>
<div class="alert alert-light border small mt-3 mb-0" style="border-radius: 0.5rem;">
    <i class="bi bi-info-circle me-1"></i>
    Menyimpan akan membuat data pejabat baru dan menjadikannya aktif. Pejabat sebelumnya dipindah ke riwayat tanpa mengubah tanggalnya.
</div>
