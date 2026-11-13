@extends('layouts.global')

@section('title', 'Pengaturan Halaman Utama')

@php
    use App\Services\HalamanUtama;
    $banner = $data['banner'];
    $footer = $data['footer'];
    $partnerAwal = old('partner', $footer['partner']);
@endphp

@section('content')
<div class="admin-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2">
    <div>
        <h1>Pengaturan Halaman Utama</h1>
        <p class="text-muted mb-0">Atur banner, footer, dan daftar partner yang tampil di halaman utama (publik).</p>
    </div>
    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm rounded-3 px-3">
        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Halaman Utama
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius: 0.5rem;" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> Periksa kembali isian berikut:
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.halaman-utama.update') }}" enctype="multipart/form-data" id="formHalamanUtama" data-testid="form-halaman-utama">
    @csrf

    {{-- ===================== BANNER: TEKS ===================== --}}
    <div class="admin-card mb-4">
        <div class="admin-card-header-inline">
            <h5><i class="bi bi-type text-success"></i> Banner: Teks</h5>
        </div>
        <div class="admin-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="bannerBadge" class="form-label small fw-semibold">Label Kecil (badge)</label>
                    <input type="text" id="bannerBadge" name="banner[badge]" maxlength="60" class="form-control admin-input"
                           value="{{ old('banner.badge', $banner['badge']) }}" placeholder="Contoh: Halo Warga RW 21">
                </div>
                <div class="col-md-6">
                    <label for="bannerJudul" class="form-label small fw-semibold">Judul <span class="text-danger">*</span></label>
                    <input type="text" id="bannerJudul" name="banner[judul]" maxlength="120" required class="form-control admin-input"
                           value="{{ old('banner.judul', $banner['judul']) }}">
                </div>
                <div class="col-12">
                    <label for="bannerDeskripsi" class="form-label small fw-semibold">Deskripsi</label>
                    <textarea id="bannerDeskripsi" name="banner[deskripsi]" rows="3" maxlength="400" class="form-control admin-input">{{ old('banner.deskripsi', $banner['deskripsi']) }}</textarea>
                </div>
                <div class="col-md-5">
                    <label for="bannerTombolTeks" class="form-label small fw-semibold">Teks Tombol</label>
                    <input type="text" id="bannerTombolTeks" name="banner[tombol_teks]" maxlength="40" class="form-control admin-input"
                           value="{{ old('banner.tombol_teks', $banner['tombol_teks']) }}" placeholder="Kosongkan untuk menyembunyikan tombol">
                </div>
                <div class="col-md-7">
                    <label for="bannerTombolUrl" class="form-label small fw-semibold">Tautan Tombol</label>
                    <input type="text" id="bannerTombolUrl" name="banner[tombol_url]" maxlength="255" class="form-control admin-input"
                           value="{{ old('banner.tombol_url', $banner['tombol_url']) }}" placeholder="/layanan-mandiri atau https://...">
                    <div class="form-text">Awali dengan <code>/</code> untuk halaman aplikasi, atau alamat lengkap <code>https://</code>.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== BANNER: GAMBAR ===================== --}}
    <div class="admin-card mb-4">
        <div class="admin-card-header-inline">
            <h5><i class="bi bi-images text-success"></i> Banner: Gambar</h5>
        </div>
        <div class="admin-card-body">
            <p class="text-muted small mb-3">
                Setiap gambar dapat memakai gambar bawaan, foto dari <strong>Galeri Dokumentasi</strong> (agenda tingkat RW yang sudah terbit),
                atau unggahan baru (JPG/PNG/WEBP, maksimal 5 MB, otomatis dikompres ke WebP).
            </p>
            <div class="row g-4">
                @foreach(HalamanUtama::SLOT as $slot => [$labelSlot, $bawaanSlot])
                    @php
                        $g = $banner['gambar'][$slot];
                        $sumber = old("gambar.$slot.sumber", $g['sumber']);
                        $pathGaleri = old("gambar.$slot.galeri", $g['sumber'] === 'galeri' ? $g['path'] : '');
                        $urlSaatIni = HalamanUtama::urlGambar($slot, $g);
                    @endphp
                    <div class="col-12 col-lg-4">
                        <div class="border rounded-4 p-3 h-100 slot-banner" data-slot="{{ $slot }}" data-bawaan="{{ asset($bawaanSlot) }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">{{ $labelSlot }}</h6>
                                <span class="badge bg-light text-secondary border small">{{ $slot === 'utama' ? 'Besar' : 'Kecil' }}</span>
                            </div>
                            <div class="ratio {{ $slot === 'utama' ? 'ratio-4x3' : 'ratio-16x9' }} rounded-3 overflow-hidden bg-light mb-3">
                                <img src="{{ $urlSaatIni }}" alt="Pratinjau {{ strtolower($labelSlot) }}" class="w-100 h-100 object-fit-cover pratinjau-slot"
                                     data-awal="{{ $urlSaatIni }}">
                            </div>

                            <div class="btn-group w-100 mb-3" role="group" aria-label="Sumber {{ strtolower($labelSlot) }}">
                                @foreach(['bawaan' => 'Bawaan', 'galeri' => 'Galeri', 'unggah' => 'Unggah'] as $nilai => $teks)
                                    <input type="radio" class="btn-check pilih-sumber" name="gambar[{{ $slot }}][sumber]" value="{{ $nilai }}"
                                           id="sumber_{{ $slot }}_{{ $nilai }}" autocomplete="off" @checked($sumber === $nilai)>
                                    <label class="btn btn-outline-success btn-sm" for="sumber_{{ $slot }}_{{ $nilai }}">{{ $teks }}</label>
                                @endforeach
                            </div>

                            <div class="panel-sumber panel-galeri {{ $sumber === 'galeri' ? '' : 'd-none' }}">
                                <input type="hidden" name="gambar[{{ $slot }}][galeri]" class="input-galeri" value="{{ $pathGaleri }}">
                                <button type="button" class="btn btn-light border w-100 btn-buka-galeri" data-bs-toggle="modal" data-bs-target="#modalPilihGaleri"
                                        @disabled(empty($fotoGaleri))>
                                    <i class="bi bi-images me-1"></i> {{ empty($fotoGaleri) ? 'Galeri Dokumentasi masih kosong' : 'Pilih dari Galeri Dokumentasi' }}
                                </button>
                                @error("gambar.$slot.galeri")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="panel-sumber panel-unggah {{ $sumber === 'unggah' ? '' : 'd-none' }}">
                                <label for="file_{{ $slot }}" class="form-label small fw-semibold mb-1">Unggah gambar</label>
                                <input type="file" id="file_{{ $slot }}" name="gambar[{{ $slot }}][file]" accept=".jpg,.jpeg,.png,.webp" class="form-control form-control-sm input-unggah">
                                @if($g['sumber'] === 'unggah' && $g['path'])
                                    <div class="form-text">Kosongkan untuk tetap memakai gambar yang sudah diunggah.</div>
                                @endif
                                @error("gambar.$slot.file")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            @if($slot !== 'utama')
                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <label for="label_{{ $slot }}" class="form-label small fw-semibold mb-1">Keterangan</label>
                                        <input type="text" id="label_{{ $slot }}" name="gambar[{{ $slot }}][label]" maxlength="40" class="form-control form-control-sm"
                                               value="{{ old("gambar.$slot.label", $g['label']) }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="sublabel_{{ $slot }}" class="form-label small fw-semibold mb-1">Sub-keterangan</label>
                                        <input type="text" id="sublabel_{{ $slot }}" name="gambar[{{ $slot }}][sublabel]" maxlength="60" class="form-control form-control-sm"
                                               value="{{ old("gambar.$slot.sublabel", $g['sublabel']) }}">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===================== FOOTER ===================== --}}
    <div class="admin-card mb-4">
        <div class="admin-card-header-inline">
            <h5><i class="bi bi-layout-text-window-reverse text-success"></i> Footer</h5>
        </div>
        <div class="admin-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="footerNama" class="form-label small fw-semibold">Nama di Footer <span class="text-danger">*</span></label>
                    <input type="text" id="footerNama" name="footer[nama]" maxlength="60" required class="form-control admin-input" value="{{ old('footer.nama', $footer['nama']) }}">
                </div>
                <div class="col-md-8">
                    <label for="footerDeskripsi" class="form-label small fw-semibold">Deskripsi Singkat</label>
                    <textarea id="footerDeskripsi" name="footer[deskripsi]" rows="2" maxlength="300" class="form-control admin-input">{{ old('footer.deskripsi', $footer['deskripsi']) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="footerSekretariat" class="form-label small fw-semibold">Alamat Sekretariat</label>
                    <textarea id="footerSekretariat" name="footer[sekretariat]" rows="3" maxlength="300" class="form-control admin-input">{{ old('footer.sekretariat', $footer['sekretariat']) }}</textarea>
                    <div class="form-text">Satu baris per baris alamat.</div>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="footerEmail" class="form-label small fw-semibold">Email</label>
                            <input type="email" id="footerEmail" name="footer[email]" maxlength="120" class="form-control admin-input" value="{{ old('footer.email', $footer['email']) }}" placeholder="sekretariat@contoh.id">
                        </div>
                        <div class="col-sm-6">
                            <label for="footerTelepon" class="form-label small fw-semibold">Telepon / WA</label>
                            <input type="text" id="footerTelepon" name="footer[telepon]" inputmode="tel" maxlength="30" class="form-control admin-input" value="{{ old('footer.telepon', $footer['telepon']) }}" placeholder="0812...">
                        </div>
                        <div class="col-12">
                            <label for="footerPeta" class="form-label small fw-semibold">Tautan Peta Lokasi</label>
                            <input type="url" id="footerPeta" name="footer[peta_url]" maxlength="500" class="form-control admin-input" value="{{ old('footer.peta_url', $footer['peta_url']) }}" placeholder="https://maps.google.com/...">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <label for="footerCopyright" class="form-label small fw-semibold">Teks Copyright <span class="text-danger">*</span></label>
                    <input type="text" id="footerCopyright" name="footer[copyright]" maxlength="160" required class="form-control admin-input" value="{{ old('footer.copyright', $footer['copyright']) }}">
                    <div class="form-text">Gunakan <code>{tahun}</code> untuk tahun berjalan, misalnya <code>© {tahun} WargaDigi 21</code>.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== PARTNER / DIDUKUNG OLEH ===================== --}}
    <div class="admin-card mb-4">
        <div class="admin-card-header-inline d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="bi bi-people text-success"></i> Partner / Didukung Oleh</h5>
            <button type="button" class="btn btn-success btn-sm rounded-3" id="btnTambahPartner">
                <i class="bi bi-plus-lg me-1"></i> Tambah Partner
            </button>
        </div>
        <div class="admin-card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="footerJudulPartner" class="form-label small fw-semibold">Judul Kolom</label>
                    <input type="text" id="footerJudulPartner" name="footer[judul_partner]" maxlength="60" class="form-control admin-input" value="{{ old('footer.judul_partner', $footer['judul_partner']) }}" placeholder="Didukung oleh">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <p class="text-muted small mb-0">Maksimal {{ HalamanUtama::MAKS_PARTNER }} partner. Kolom ini tidak tampil bila daftar partner kosong.</p>
                </div>
            </div>

            <div id="daftarPartner" class="d-flex flex-column gap-2" data-maks="{{ HalamanUtama::MAKS_PARTNER }}">
                @foreach($partnerAwal as $i => $p)
                    @include('admin.partials.baris-partner', ['i' => $i, 'p' => $p])
                @endforeach
            </div>
            <p class="text-muted small mb-0 mt-2 {{ count($partnerAwal) ? 'd-none' : '' }}" id="partnerKosong">Belum ada partner.</p>
        </div>
    </div>

    <div class="position-sticky bottom-0 bg-white border-top py-3 d-flex justify-content-end gap-2" style="z-index: 5;">
        <a href="{{ route('admin.halaman-utama') }}" class="btn btn-light border">Batal</a>
        <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
    </div>
</form>

<template id="tplPartner">
    @include('admin.partials.baris-partner', ['i' => '__I__', 'p' => ['nama' => '', 'url' => '', 'logo' => null]])
</template>

{{-- Modal pilih foto dari Galeri Dokumentasi --}}
<div class="modal fade" id="modalPilihGaleri" tabindex="-1" aria-labelledby="judulPilihGaleri" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-fullscreen-sm-down">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="judulPilihGaleri">Pilih Foto dari Galeri Dokumentasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                @if(empty($fotoGaleri))
                    <p class="text-muted mb-0">Belum ada foto dokumentasi dari agenda RW yang terbit.</p>
                @else
                    <div class="row g-3">
                        @foreach($fotoGaleri as $f)
                            <div class="col-6 col-md-4 col-lg-3">
                                <button type="button" class="btn p-0 border-0 w-100 text-start pilih-foto-galeri" data-path="{{ $f['path'] }}" data-url="{{ $f['url'] }}"
                                        aria-label="Pilih foto {{ $f['agenda'] }}">
                                    <div class="ratio ratio-4x3 rounded-3 overflow-hidden bg-light border foto-galeri-kotak">
                                        <img src="{{ $f['url'] }}" alt="" loading="lazy" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <small class="d-block text-muted text-truncate mt-1">{{ $f['agenda'] }}</small>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .foto-galeri-kotak { transition: box-shadow .15s, outline-color .15s; outline: 3px solid transparent; outline-offset: 2px; }
    .pilih-foto-galeri:hover .foto-galeri-kotak, .pilih-foto-galeri:focus-visible .foto-galeri-kotak { outline-color: #5b9b76; }
    .pilih-foto-galeri.terpilih .foto-galeri-kotak { outline-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,.25); }
</style>
@endpush

@push('scripts')
<script>
(function () {
    // ---- Sumber gambar banner (bawaan / galeri / unggah) ----
    let slotAktif = null;

    function aturSlot(kotak) {
        const sumber = kotak.querySelector('.pilih-sumber:checked')?.value || 'bawaan';
        kotak.querySelector('.panel-galeri').classList.toggle('d-none', sumber !== 'galeri');
        kotak.querySelector('.panel-unggah').classList.toggle('d-none', sumber !== 'unggah');
        const img = kotak.querySelector('.pratinjau-slot');
        if (sumber === 'bawaan') {
            img.src = kotak.dataset.bawaan;
        } else if (sumber === 'galeri') {
            const terpilih = kotak.querySelector('.input-galeri').value;
            const tombol = terpilih ? document.querySelector('.pilih-foto-galeri[data-path="' + CSS.escape(terpilih) + '"]') : null;
            img.src = tombol ? tombol.dataset.url : img.dataset.awal;
        } else {
            const file = kotak.querySelector('.input-unggah').files[0];
            img.src = file ? URL.createObjectURL(file) : img.dataset.awal;
        }
    }

    document.querySelectorAll('.slot-banner').forEach(function (kotak) {
        kotak.querySelectorAll('.pilih-sumber').forEach(r => r.addEventListener('change', () => aturSlot(kotak)));
        kotak.querySelector('.input-unggah').addEventListener('change', () => aturSlot(kotak));
        kotak.querySelector('.btn-buka-galeri').addEventListener('click', function () {
            slotAktif = kotak;
            const terpilih = kotak.querySelector('.input-galeri').value;
            document.querySelectorAll('.pilih-foto-galeri').forEach(b => b.classList.toggle('terpilih', b.dataset.path === terpilih));
        });
    });

    document.querySelectorAll('.pilih-foto-galeri').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            if (!slotAktif) return;
            slotAktif.querySelector('.input-galeri').value = tombol.dataset.path;
            slotAktif.querySelector('.pratinjau-slot').src = tombol.dataset.url;
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalPilihGaleri'));
            if (modal) modal.hide();
        });
    });

    // ---- Partner: tambah / hapus baris + pratinjau logo ----
    const daftar = document.getElementById('daftarPartner');
    const kosong = document.getElementById('partnerKosong');
    const tpl = document.getElementById('tplPartner');
    let indeks = Date.now();

    function perbaruiKosong() {
        const jumlah = daftar.querySelectorAll('.baris-partner').length;
        kosong.classList.toggle('d-none', jumlah > 0);
        document.getElementById('btnTambahPartner').disabled = jumlah >= Number(daftar.dataset.maks);
    }

    document.getElementById('btnTambahPartner').addEventListener('click', function () {
        const html = tpl.innerHTML.replaceAll('__I__', String(indeks++));
        daftar.insertAdjacentHTML('beforeend', html);
        perbaruiKosong();
        daftar.lastElementChild.querySelector('input[type="text"]')?.focus();
    });

    daftar.addEventListener('click', function (e) {
        const hapus = e.target.closest('.btn-hapus-partner');
        if (!hapus) return;
        hapus.closest('.baris-partner').remove();
        perbaruiKosong();
    });

    daftar.addEventListener('change', function (e) {
        if (!e.target.matches('.input-logo-partner')) return;
        const file = e.target.files[0];
        const img = e.target.closest('.baris-partner').querySelector('.pratinjau-logo');
        if (file && img) { img.src = URL.createObjectURL(file); img.classList.remove('d-none'); }
    });

    perbaruiKosong();
})();
</script>
@endpush
@endsection
