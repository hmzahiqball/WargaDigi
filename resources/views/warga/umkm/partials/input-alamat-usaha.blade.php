{{--
    Input "Alamat Usaha (RT/RW)" + ceklis "Gunakan alamat di profil".
    Alamat profil diambil dari database (domisili penduduk/keluarga, lalu alamat KK).
    Variabel: $idInput (unik per form), $nilai (alamat usaha saat ini, opsional).
--}}
@php
    $alamatProfil = \App\Models\UmkmUsaha::alamatProfil(auth()->user());
    $nilaiAwal = old('alamat_usaha', $nilai ?? '');
    $pakaiProfil = (bool) old('pakai_alamat_profil', $alamatProfil && ! empty($nilai) && trim($nilai) === $alamatProfil);
    if ($pakaiProfil) {
        $nilaiAwal = $alamatProfil;
    }
@endphp
<label for="{{ $idInput }}" class="form-label small fw-semibold text-dark mb-1">Alamat Usaha (RT/RW)</label>
<input type="text" id="{{ $idInput }}" name="alamat_usaha"
       class="form-control rounded-3 py-2 px-3 border-1 shadow-none"
       value="{{ $nilaiAwal }}" placeholder="Contoh: Jl. Mawar No. 12, RT 03/RW 12" maxlength="255" required
       @if($pakaiProfil) readonly @endif>
<div class="form-check mt-2">
    <input class="form-check-input ceklis-dari-profil" type="checkbox" name="pakai_alamat_profil" value="1"
           id="{{ $idInput }}_profil" data-target="#{{ $idInput }}" data-nilai="{{ $alamatProfil }}"
           @checked($pakaiProfil) @disabled(! $alamatProfil)>
    <label class="form-check-label small" for="{{ $idInput }}_profil">
        Gunakan alamat di profil
        @if($alamatProfil)
            <span class="text-muted">({{ \Illuminate\Support\Str::limit($alamatProfil, 60) }})</span>
        @else
            <span class="text-muted">(alamat belum diisi di profil)</span>
        @endif
    </label>
</div>
@include('warga.umkm.partials.script-ceklis-profil')
