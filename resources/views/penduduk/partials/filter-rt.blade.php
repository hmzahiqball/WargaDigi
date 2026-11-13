{{-- Filter RT (hanya lingkup RW). Dipakai kedua tab Manajemen Penduduk. --}}
<div class="col-md-3">
    <label for="{{ $idFilter }}" class="form-label text-muted small">RT</label>
    <select id="{{ $idFilter }}" name="rt_id" class="form-select bg-light border-0">
        <option value="">Semua RT</option>
        @foreach($konfigurasi['daftarRt'] as $rt)
            <option value="{{ $rt->id }}" @selected($konfigurasi['rtTerpilih'] === $rt->id)>RT {{ $rt->kode_rt }}</option>
        @endforeach
    </select>
</div>
