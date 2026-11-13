{{-- Satu baris partner di Pengaturan Halaman Utama. Variabel: $i (indeks), $p (['nama','url','logo'|'logo_lama']). --}}
@php
    $logoLama = $p['logo'] ?? ($p['logo_lama'] ?? null);
    $urlLogo = \App\Services\HalamanUtama::urlLogoPartner($logoLama);
@endphp
<div class="baris-partner border rounded-3 p-2 p-md-3">
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width:56px;height:56px;">
                <img src="{{ $urlLogo ?? '' }}" alt="" class="pratinjau-logo mw-100 mh-100 {{ $urlLogo ? '' : 'd-none' }}" style="object-fit:contain;">
                @unless($urlLogo)<i class="bi bi-image text-muted"></i>@endunless
            </div>
        </div>
        <div class="col-12 col-md">
            <label class="visually-hidden" for="partner_nama_{{ $i }}">Nama partner</label>
            <input type="text" id="partner_nama_{{ $i }}" name="partner[{{ $i }}][nama]" maxlength="80" required class="form-control form-control-sm"
                   value="{{ $p['nama'] ?? '' }}" placeholder="Nama partner">
        </div>
        <div class="col-12 col-md">
            <label class="visually-hidden" for="partner_url_{{ $i }}">Tautan partner</label>
            <input type="url" id="partner_url_{{ $i }}" name="partner[{{ $i }}][url]" maxlength="255" class="form-control form-control-sm"
                   value="{{ $p['url'] ?? '' }}" placeholder="https://... (opsional)">
        </div>
        <div class="col-9 col-md-3">
            <label class="visually-hidden" for="partner_logo_{{ $i }}">Logo partner</label>
            <input type="file" id="partner_logo_{{ $i }}" name="partner[{{ $i }}][logo]" accept=".jpg,.jpeg,.png,.webp" class="form-control form-control-sm input-logo-partner">
            <input type="hidden" name="partner[{{ $i }}][logo_lama]" value="{{ $logoLama }}">
        </div>
        <div class="col-3 col-md-auto text-end">
            <button type="button" class="btn btn-outline-danger btn-sm btn-hapus-partner" aria-label="Hapus partner">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</div>
