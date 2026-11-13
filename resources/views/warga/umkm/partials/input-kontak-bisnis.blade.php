{{--
    Input "Nomor Kontak Bisnis (Nomor WA)" + ceklis "Gunakan nomor kontak di profil".
    Nomor profil diambil dari database (penduduk pemilik akun, cadangan nomor WA keluarga).
    Variabel: $idInput (unik per form), $nilai (nomor usaha saat ini, opsional).
--}}
@php
    $nomorProfil = \App\Models\UmkmUsaha::nomorKontakProfil(auth()->user());
    $tampilProfil = $nomorProfil ? preg_replace('/^62/', '', $nomorProfil) : '';
    $nilaiAwal = old('no_wa', isset($nilai) && $nilai ? preg_replace('/^(\+?62|0)/', '', $nilai) : '');
    $pakaiProfil = (bool) old('pakai_kontak_profil', $nomorProfil && (
        empty($nilai) ? false : \App\Models\UmkmUsaha::normalisasiWa($nilai) === $nomorProfil
    ));
    if ($pakaiProfil) {
        $nilaiAwal = $tampilProfil;
    }
@endphp
<label for="{{ $idInput }}" class="form-label small fw-semibold text-dark mb-1">Nomor Kontak Bisnis (Nomor WA)</label>
<div class="input-group">
    <span class="input-group-text bg-light text-dark rounded-start-3 small fw-semibold px-3">+62</span>
    <input type="text" inputmode="tel" id="{{ $idInput }}" name="no_wa"
           class="form-control rounded-end-3 py-2 px-3 border-1 shadow-none input-kontak-bisnis"
           value="{{ $nilaiAwal }}" placeholder="8123456789" maxlength="20" required
           @if($pakaiProfil) readonly @endif>
</div>
<div class="form-check mt-2">
    <input class="form-check-input ceklis-dari-profil" type="checkbox" name="pakai_kontak_profil" value="1"
           id="{{ $idInput }}_profil" data-target="#{{ $idInput }}" data-nomor="{{ $tampilProfil }}" data-nilai="{{ $tampilProfil }}"
           @checked($pakaiProfil) @disabled(! $nomorProfil)>
    <label class="form-check-label small" for="{{ $idInput }}_profil">
        Gunakan nomor kontak di profil
        @if($nomorProfil)
            <span class="text-muted">(+62 {{ $tampilProfil }})</span>
        @else
            <span class="text-muted">(nomor WA belum diisi di profil)</span>
        @endif
    </label>
</div>

@include('warga.umkm.partials.script-ceklis-profil')
