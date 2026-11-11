{{-- Isian umum satu setoran: tanggal, metode, bukti, catatan (dipakai modal setor bulanan dan Iuran Lainnya) --}}
<div class="row g-2 mb-3">
    <div class="col-6">
        <label class="form-label small fw-bold text-muted" for="{{ $prefix }}Tanggal">Tanggal setor</label>
        <input type="date" name="tanggal_setor" id="{{ $prefix }}Tanggal" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
    </div>
    <div class="col-6">
        <label class="form-label small fw-bold text-muted" for="{{ $prefix }}Metode">Metode</label>
        <select name="metode" id="{{ $prefix }}Metode" class="form-select" required>
            <option value="tunai">Tunai</option>
            <option value="transfer">Transfer</option>
        </select>
    </div>
</div>
<div class="mb-3">
    <label class="form-label small fw-bold text-muted" for="{{ $prefix }}Bukti">Bukti (opsional)</label>
    <input type="file" name="bukti_file" id="{{ $prefix }}Bukti" class="form-control" accept=".jpg,.jpeg,.png">
</div>
<div>
    <label class="form-label small fw-bold text-muted" for="{{ $prefix }}Catatan">Catatan (opsional)</label>
    <textarea name="catatan" id="{{ $prefix }}Catatan" rows="2" maxlength="500" class="form-control"></textarea>
</div>
