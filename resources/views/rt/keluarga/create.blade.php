@extends('layouts.global')

@section('title', 'Tambah Keluarga')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">Tambah Keluarga</h2>
        <p class="text-muted mb-0">Unggah KK untuk mengisi otomatis, atau isi data secara manual.</p>
    </div>
    <a href="{{ route('rt.penduduk.index') }}" class="btn btn-light border" style="border-radius: 0.5rem;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger" style="border-radius:0.5rem;">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('rt.keluarga.store') }}" id="formKeluarga">
    @csrf

    {{-- ===================== SEGMEN 1: PROFIL KELUARGA ===================== --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <h5 class="fw-bold mb-1"><i class="bi bi-house-door me-2 text-success"></i>Profil Keluarga</h5>
            <p class="text-muted small mb-3">Unggah foto/scan Kartu Keluarga. Sistem akan mencoba membaca data header otomatis - periksa &amp; lengkapi bila ada yang kurang tepat.</p>

            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Upload Kartu Keluarga (KK)</label>
                    <input type="file" id="fileKk" class="form-control admin-input" accept=".jpg,.jpeg,.png,.pdf">
                    <input type="hidden" name="file_kk_path" id="fileKkPath">
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-success btn-sm" id="btnBacaKk" disabled><i class="bi bi-magic me-1"></i>Ambil Data KK dari File</button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="btnKameraKk"><i class="bi bi-camera me-1"></i>Ambil KK dari Kamera</button>
                    </div>
                    <div class="mt-2"><span class="small" id="statusKk"></span></div>
                    <div id="previewKk" class="mt-3"></div>
                </div>
                <div class="col-md-7">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Nomor KK <span class="text-danger">*</span></label>
                            <input type="text" name="no_kk" id="no_kk" class="form-control admin-input" maxlength="16" value="{{ old('no_kk') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alamat (sesuai KK) <span class="text-danger">*</span></label>
                            <input type="text" name="alamat" id="alamat" class="form-control admin-input" value="{{ old('alamat') }}" required>
                        </div>
                        {{-- Wilayah bertingkat: Provinsi -> Kabupaten -> Kecamatan -> Kelurahan (dari database) --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Provinsi</label>
                            <select name="provinsi" id="provinsi" class="form-select admin-input" data-wilayah="provinsi">
                                <option value="">- pilih provinsi -</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Kabupaten/Kota</label>
                            <select name="kabupaten" id="kabupaten" class="form-select admin-input" data-wilayah="kabupaten" disabled>
                                <option value="">- pilih kabupaten/kota -</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Kecamatan</label>
                            <select name="kecamatan" id="kecamatan" class="form-select admin-input" data-wilayah="kecamatan" disabled>
                                <option value="">- pilih kecamatan -</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Kelurahan/Desa</label>
                            <select name="kelurahan" id="kelurahan" class="form-select admin-input" data-wilayah="kelurahan" disabled>
                                <option value="">- pilih kelurahan/desa -</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">RT</label>
                            <input type="text" name="rt" id="rt" class="form-control admin-input" value="{{ old('rt') }}" placeholder="Dari KK">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">RW</label>
                            <input type="text" name="rw" id="rw" class="form-control admin-input" value="{{ old('rw') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Kode Pos</label>
                            <input type="text" name="kode_pos" id="kode_pos" class="form-control admin-input" value="{{ old('kode_pos') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alamat Domisili (tempat tinggal)</label>
                            <input type="text" name="alamat_domisili" id="alamat_domisili" class="form-control admin-input" value="{{ old('alamat_domisili') }}" placeholder="Isi manual bila berbeda dari alamat KK">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status KK</label>
                            <select name="status_warga" id="status_warga" class="form-select admin-input">
                                <option value="tidak_tetap" {{ old('status_warga') === 'tetap' ? '' : 'selected' }}>Bukan Warga Tetap</option>
                                <option value="tetap" {{ old('status_warga') === 'tetap' ? 'selected' : '' }}>Warga Tetap</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="pemilik_rumah" id="pemilik_rumah" value="1" {{ old('pemilik_rumah') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="pemilik_rumah">Pemilik rumah</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== SEGMEN 2: DATA PENDUDUK ===================== --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h5 class="fw-bold mb-0"><i class="bi bi-people me-2 text-success"></i>Data Penduduk</h5>
                <button type="button" class="btn btn-outline-success btn-sm" id="btnTambahPenduduk"><i class="bi bi-plus-lg me-1"></i>Tambah Penduduk</button>
            </div>
            <p class="text-muted small mb-3">Minimal satu penduduk. Tandai satu orang sebagai pemegang akun keluarga. Unggah KTP untuk mengisi otomatis - <strong>periksa kembali NIK</strong> karena angka rawan salah baca.</p>

            <div id="pendudukContainer"></div>
        </div>
    </div>

    {{-- ===================== SEGMEN 3: AKUN KELUARGA ===================== --}}
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <h5 class="fw-bold mb-1"><i class="bi bi-person-lock me-2 text-success"></i>Akun Keluarga</h5>
            <p class="text-muted small mb-3">Akun login untuk penduduk yang ditandai sebagai pemegang akun keluarga: <strong id="namaPemegang" class="text-success">- belum dipilih -</strong></p>
            <input type="hidden" name="pemegang_akun" id="pemegangAkun" value="{{ old('pemegang_akun') }}">
            <div class="alert alert-info small border-0 mb-0" style="background-color:#e8f5e9;color:#1B5E20;">
                <i class="bi bi-info-circle me-1"></i>
                Username dan password tidak diisi di sini. Setelah disimpan, sistem membuat <strong>link aktivasi</strong>
                yang Anda bagikan kepada pemegang akun. Pemegang akun akan menentukan <strong>username</strong> dan
                <strong>password</strong> sendiri saat aktivasi.
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="{{ route('rt.penduduk.index') }}" class="btn btn-light border">Batal</a>
        <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i>Simpan Keluarga</button>
    </div>
</form>

{{-- Modal ambil KK dari kamera --}}
<div class="modal fade" id="modalKameraKk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:0.75rem;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-camera me-2 text-success"></i>Ambil KK dari Kamera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted small">Posisikan Kartu Keluarga di dalam bingkai, pastikan pencahayaan cukup dan teks terbaca jelas, lalu tekan Jepret.</p>
                <div class="position-relative d-inline-block w-100" style="background:#000;border-radius:0.5rem;overflow:hidden;">
                    <video id="videoKameraKk" autoplay playsinline muted style="width:100%;max-height:60vh;object-fit:contain;background:#000;"></video>
                </div>
                <canvas id="canvasKameraKk" class="d-none"></canvas>
                <div class="mt-2"><span class="small" id="statusKameraKk"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnJepretKk"><i class="bi bi-camera-fill me-1"></i>Jepret</button>
            </div>
        </div>
    </div>
</div>

@include('partials._modal-kamera-ktp')

{{-- Template baris penduduk (dipakai JS) --}}
@include('partials._tpl-penduduk')
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const container = document.getElementById('pendudukContainer');
    const tpl = document.getElementById('tplPenduduk');
    let idx = 0;

    // Susun alamat lengkap dari field-field KK (Profil Keluarga) jadi satu string:
    // "<alamat>, RT 001/RW 021, Kel. <kelurahan>, Kec. <kecamatan>, <kabupaten>, <provinsi> <kode pos>".
    function susunAlamatKk() {
        function val(id) { const el = document.getElementById(id); return el ? (el.value || '').trim() : ''; }
        function pad(v) { v = (v || '').replace(/\D/g, ''); return v ? v.padStart(3, '0') : ''; }

        const alamat = val('alamat');
        const rt = pad(val('rt'));
        const rw = pad(val('rw'));
        const kel = val('kelurahan');
        const kec = val('kecamatan');
        const kab = val('kabupaten');
        const prov = val('provinsi');
        const pos = val('kode_pos');

        const bagian = [];
        if (alamat) bagian.push(alamat);
        if (rt || rw) bagian.push('RT ' + (rt || '000') + '/RW ' + (rw || '000'));
        if (kel) bagian.push('Kel. ' + kel);
        if (kec) bagian.push('Kec. ' + kec);
        if (kab) bagian.push(kab);
        let prv = prov;
        if (pos) prv = (prv ? prv + ' ' : '') + pos;
        if (prv) bagian.push(prv);

        return bagian.join(', ');
    }

    // Ambil alamat domisili KK: pakai field domisili KK bila diisi,
    // selain itu pakai alamat KK lengkap (karena domisili = alamat bila tak diisi).
    function ambilDomisiliKk() {
        const d = document.getElementById('alamat_domisili');
        const dom = d ? (d.value || '').trim() : '';
        return dom !== '' ? dom : susunAlamatKk();
    }

    // Perbarui alamat pada semua baris penduduk yang mencentang "Alamat sama dengan KK".
    function sinkronkanAlamatKk() {
        const teks = susunAlamatKk();
        const domisili = ambilDomisiliKk();
        container.querySelectorAll('.penduduk-item').forEach(function (wrap) {
            const chk = wrap.querySelector('.chk-alamat-kk');
            const alamatEl = wrap.querySelector('.alamat-ktp');
            if (chk && chk.checked && alamatEl) {
                alamatEl.value = teks;
                hitungStatusWargaPenduduk(wrap);
            }
            const chkDom = wrap.querySelector('.chk-domisili-kk');
            const domEl = wrap.querySelector('.domisili-ktp');
            if (chkDom && chkDom.checked && domEl) {
                domEl.value = domisili;
            }
        });
    }

    // Tentukan status warga satu baris penduduk dari alamat KTP-nya.
    // Tetap bila alamat memuat "Tanimulya" DAN RW 21 (021/21/RW.21), selain itu tidak tetap.
    function hitungStatusWargaPenduduk(wrap) {
        const sel = wrap.querySelector('.status-warga-penduduk');
        const alamatEl = wrap.querySelector('.alamat-ktp');
        if (!sel || !alamatEl) return;
        const alamat = (alamatEl.value || '').toLowerCase();
        const adaTanimulya = alamat.includes('tanimulya');
        // Cari pola RW 21 pada alamat (toleran: "rw 021", "rw21", "rw.21", "/021", dst).
        let rwCocok = false;
        const m = alamat.match(/rw[\s.:/-]*0*(\d{1,3})/);
        if (m) {
            rwCocok = m[1] === '21';
        } else {
            // Pola "RT/RW 001/021" tanpa label "rw".
            const m2 = alamat.match(/\d{1,3}\s*\/\s*0*(\d{1,3})/);
            if (m2) rwCocok = m2[1] === '21';
        }
        sel.value = (adaTanimulya && rwCocok) ? 'tetap' : 'tidak_tetap';
    }

    function filePreview(wrapper, url, mime) {
        wrapper.innerHTML = '';
        if (!url) return;
        if (mime && mime.includes('pdf')) {
            wrapper.innerHTML = '<a href="' + url + '" target="_blank" class="small"><i class="bi bi-file-earmark-pdf"></i> Lihat PDF</a>';
        } else {
            wrapper.innerHTML = '<img src="' + url + '" style="max-width:100%;max-height:220px;border-radius:8px;border:1px solid #eee;">';
        }
    }

    // Tambah baris penduduk. optIdx: index tetap untuk name array.
    function tambahPenduduk(prefill) {
        const node = tpl.content.cloneNode(true);
        const wrap = node.querySelector('.penduduk-item');
        const myIdx = idx++;
        wrap.dataset.idx = myIdx;
        wrap.querySelector('.penduduk-title').textContent = 'Penduduk #' + (myIdx + 1);

        // Beri name array pada field data-name.
        wrap.querySelectorAll('[data-name]').forEach(function (el) {
            el.name = 'penduduk[' + myIdx + '][' + el.getAttribute('data-name') + ']';
        });
        const radio = wrap.querySelector('.radio-pemegang');
        radio.value = myIdx;
        radio.addEventListener('change', function () {
            document.getElementById('pemegangAkun').value = myIdx;
            const namaEl = wrap.querySelector('[name$="[nama_lengkap]"]');
            const nama = (namaEl && namaEl.value) || ('Penduduk #' + (myIdx + 1));
            document.getElementById('namaPemegang').textContent = nama || '- belum dipilih -';
            tandaiWaWajib();
        });

        // Hapus baris.
        wrap.querySelector('.btn-hapus-penduduk').addEventListener('click', function () {
            wrap.remove();
        });

        // Status warga dihitung ulang bila alamat KTP diubah manual.
        const alamatEl = wrap.querySelector('.alamat-ktp');
        if (alamatEl) {
            alamatEl.addEventListener('input', function () { hitungStatusWargaPenduduk(wrap); });
        }

        // Checkbox "Alamat sama dengan KK": isi alamat dari gabungan field KK.
        const chkAlamatKk = wrap.querySelector('.chk-alamat-kk');
        if (chkAlamatKk && alamatEl) {
            chkAlamatKk.addEventListener('change', function () {
                if (chkAlamatKk.checked) {
                    alamatEl.value = susunAlamatKk();
                    alamatEl.readOnly = true;
                    alamatEl.classList.add('bg-light');
                } else {
                    alamatEl.readOnly = false;
                    alamatEl.classList.remove('bg-light');
                }
                hitungStatusWargaPenduduk(wrap);
            });
            // Simpan referensi agar bisa disinkronkan saat data KK berubah.
            wrap._chkAlamatKk = chkAlamatKk;
            wrap._alamatEl = alamatEl;
        }

        // Checkbox "Alamat domisili sama dengan KK": ambil dari field domisili KK
        // (bila kosong, pakai alamat KK lengkap sebagai domisili).
        const domisiliEl = wrap.querySelector('.domisili-ktp');
        const chkDomisiliKk = wrap.querySelector('.chk-domisili-kk');
        if (chkDomisiliKk && domisiliEl) {
            chkDomisiliKk.addEventListener('change', function () {
                if (chkDomisiliKk.checked) {
                    domisiliEl.value = ambilDomisiliKk();
                    domisiliEl.readOnly = true;
                    domisiliEl.classList.add('bg-light');
                } else {
                    domisiliEl.readOnly = false;
                    domisiliEl.classList.remove('bg-light');
                }
            });
        }

        // OCR KTP.
        const fileKtp = wrap.querySelector('.file-ktp');
        const btnBaca = wrap.querySelector('.btn-baca-ktp');
        const statusEl = wrap.querySelector('.status-ktp');
        const pathEl = wrap.querySelector('.ktp-path');
        fileKtp.addEventListener('change', function () { btnBaca.disabled = !fileKtp.files.length; });
        btnBaca.addEventListener('click', function () {
            if (!fileKtp.files.length) return;
            prosesFileKtp(fileKtp.files[0], statusEl, wrap.querySelector('.preview-ktp'), pathEl, wrap);
        });

        // Ambil KTP dari kamera (modal bersama). Catat baris penduduk aktif.
        const btnKamKtp = wrap.querySelector('.btn-kamera-ktp');
        if (btnKamKtp) {
            btnKamKtp.addEventListener('click', function () {
                bukaKameraKtp({
                    fileInput: fileKtp,
                    btnBaca: btnBaca,
                    statusEl: statusEl,
                    previewEl: wrap.querySelector('.preview-ktp'),
                    pathEl: pathEl,
                    wrap: wrap,
                });
            });
        }

        // Prefill (dari anggota KK yang terbaca).
        if (prefill) {
            Object.keys(prefill).forEach(function (k) {
                const el = wrap.querySelector('[name$="[' + k + ']"]');
                if (el && prefill[k]) el.value = prefill[k];
            });
        }

        container.appendChild(node);
    }

    document.getElementById('btnTambahPenduduk').addEventListener('click', function () { tambahPenduduk(); });

    // Mulai dengan 1 baris penduduk.
    tambahPenduduk();

    // ---- OCR KK (OCR.space, server) ----
    const fileKk = document.getElementById('fileKk');
    const btnKk = document.getElementById('btnBacaKk');
    const statusKk = document.getElementById('statusKk');

    // Bila field alamat/domisili KK diubah manual, sinkronkan baris penduduk yang centang "sama dengan KK".
    ['alamat', 'rt', 'rw', 'kode_pos', 'alamat_domisili'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', sinkronkanAlamatKk);
    });

    // ===== Dropdown wilayah bertingkat (Provinsi -> Kabupaten -> Kecamatan -> Kelurahan) =====
    const selProv = document.getElementById('provinsi');
    const selKab = document.getElementById('kabupaten');
    const selKec = document.getElementById('kecamatan');
    const selKel = document.getElementById('kelurahan');
    const WILAYAH_URL = '{{ route('wilayah.anak') }}';

    function muatWilayah(kodeInduk) {
        const url = WILAYAH_URL + (kodeInduk ? ('?kode=' + encodeURIComponent(kodeInduk)) : '');
        return fetch(url, { headers: { 'Accept': 'application/json' } }).then(function (r) { return r.json(); });
    }
    function isiSelect(sel, items, placeholder, kodeTerpilih, namaTerpilih) {
        let html = '<option value="">' + placeholder + '</option>';
        items.forEach(function (o) {
            const terpilih = (kodeTerpilih && o.kode === kodeTerpilih) || (namaTerpilih && o.nama === namaTerpilih);
            html += '<option value="' + o.nama + '" data-kode="' + o.kode + '"' + (terpilih ? ' selected' : '') + '>' + o.nama + '</option>';
        });
        sel.innerHTML = html;
    }
    function kodeOpsiTerpilih(sel) {
        const opt = sel.options[sel.selectedIndex];
        return opt ? (opt.getAttribute('data-kode') || '') : '';
    }

    // Muat provinsi saat halaman dibuka.
    muatWilayah('').then(function (items) {
        isiSelect(selProv, items, '- pilih provinsi -');
    });

    // Cascading onchange.
    selProv.addEventListener('change', function () {
        selKab.innerHTML = '<option value="">- pilih kabupaten/kota -</option>'; selKab.disabled = true;
        selKec.innerHTML = '<option value="">- pilih kecamatan -</option>'; selKec.disabled = true;
        selKel.innerHTML = '<option value="">- pilih kelurahan/desa -</option>'; selKel.disabled = true;
        const kode = kodeOpsiTerpilih(selProv);
        if (!kode) return;
        muatWilayah(kode).then(function (items) { isiSelect(selKab, items, '- pilih kabupaten/kota -'); selKab.disabled = false; });
    });
    selKab.addEventListener('change', function () {
        selKec.innerHTML = '<option value="">- pilih kecamatan -</option>'; selKec.disabled = true;
        selKel.innerHTML = '<option value="">- pilih kelurahan/desa -</option>'; selKel.disabled = true;
        const kode = kodeOpsiTerpilih(selKab);
        if (!kode) return;
        muatWilayah(kode).then(function (items) { isiSelect(selKec, items, '- pilih kecamatan -'); selKec.disabled = false; });
    });
    selKec.addEventListener('change', function () {
        selKel.innerHTML = '<option value="">- pilih kelurahan/desa -</option>'; selKel.disabled = true;
        const kode = kodeOpsiTerpilih(selKec);
        if (!kode) return;
        muatWilayah(kode).then(function (items) { isiSelect(selKel, items, '- pilih kelurahan/desa -'); selKel.disabled = false; });
    });
    selKel.addEventListener('change', hitungStatusWarga);
    // Perubahan wilayah KK ikut menyinkronkan alamat penduduk yang "sama dengan KK".
    [selProv, selKab, selKec, selKel].forEach(function (sel) {
        sel.addEventListener('change', sinkronkanAlamatKk);
    });

    // Set wilayah dari hasil OCR (auto-select bertingkat berdasarkan kode).
    async function setWilayahDariOcr(d) {
        if (!d.kode_provinsi) return;
        // Provinsi
        isiSelect(selProv, await muatWilayah(''), '- pilih provinsi -', d.kode_provinsi);
        // Kabupaten
        isiSelect(selKab, await muatWilayah(d.kode_provinsi), '- pilih kabupaten/kota -', d.kode_kabupaten);
        selKab.disabled = false;
        // Kecamatan
        if (d.kode_kabupaten) {
            isiSelect(selKec, await muatWilayah(d.kode_kabupaten), '- pilih kecamatan -', d.kode_kecamatan);
            selKec.disabled = false;
        }
        // Kelurahan
        if (d.kode_kecamatan) {
            isiSelect(selKel, await muatWilayah(d.kode_kecamatan), '- pilih kelurahan/desa -', d.kode_kelurahan || null, d.kelurahan || null);
            selKel.disabled = false;
        }
        hitungStatusWarga();
    }

    // Proses sebuah File KK (dari upload file maupun hasil foto kamera) via OCR.space.
    function prosesFileKk(file) {
        if (!file) return;
        statusKk.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm"></span> Memproses KK...</span>';
        bacaDokumen('{{ route('ocr.baca-kk') }}', file, statusKk, function (res) {
            document.getElementById('fileKkPath').value = res.path || '';
            filePreview(document.getElementById('previewKk'), res.url, file.type);
            if (res.terbaca && res.data) {
                const d = res.data;
                ['no_kk','alamat','rt','rw','kode_pos'].forEach(function (k) {
                    if (d[k]) { const el = document.getElementById(k); if (el) el.value = d[k]; }
                });
                setWilayahDariOcr(d);
                // Wilayah di-set async; sinkronkan alamat KK setelah selesai.
                setTimeout(sinkronkanAlamatKk, 400);
            }
        });
    }

    fileKk.addEventListener('change', function () { btnKk.disabled = !fileKk.files.length; });
    btnKk.addEventListener('click', function () {
        if (!fileKk.files.length) return;
        prosesFileKk(fileKk.files[0]);
    });

    // ---- Ambil KK dari Kamera ----
    const btnKamKk = document.getElementById('btnKameraKk');
    const modalKamEl = document.getElementById('modalKameraKk');
    const videoKam = document.getElementById('videoKameraKk');
    const canvasKam = document.getElementById('canvasKameraKk');
    const btnJepret = document.getElementById('btnJepretKk');
    const statusKamKk = document.getElementById('statusKameraKk');
    let streamKam = null;
    let modalKam = null;

    function hentikanKamera() {
        if (streamKam) {
            streamKam.getTracks().forEach(function (t) { t.stop(); });
            streamKam = null;
        }
    }

    // Ambil instance modal Bootstrap secara lazy (app.js bisa selesai memuat setelah script ini).
    function dapatkanModal() {
        if (modalKam) return modalKam;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            modalKam = new bootstrap.Modal(modalKamEl);
        }
        return modalKam;
    }

    if (btnKamKk && modalKamEl) {
        btnKamKk.addEventListener('click', function () {
            statusKamKk.textContent = '';
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                window.tampilkanPemberitahuan('Kamera tidak didukung di browser ini. Gunakan HTTPS/localhost dan browser modern.');
                return;
            }
            const m = dapatkanModal();
            if (!m) {
                window.tampilkanPemberitahuan('Komponen tampilan belum siap. Muat ulang halaman lalu coba lagi.');
                return;
            }
            m.show();
        });

        // Saat modal tampil: nyalakan kamera belakang.
        modalKamEl.addEventListener('shown.bs.modal', function () {
            statusKamKk.innerHTML = '<span class="text-muted">Mengaktifkan kamera...</span>';
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' } }, audio: false
            }).then(function (stream) {
                streamKam = stream;
                videoKam.srcObject = stream;
                statusKamKk.textContent = '';
            }).catch(function () {
                statusKamKk.innerHTML = '<span class="text-danger">Gagal mengakses kamera. Pastikan izin kamera diberikan.</span>';
            });
        });

        // Saat modal ditutup: matikan kamera.
        modalKamEl.addEventListener('hidden.bs.modal', function () {
            hentikanKamera();
            videoKam.srcObject = null;
        });

        // Jepret foto -> jadikan File -> set ke input file KK -> proses OCR.
        btnJepret.addEventListener('click', function () {
            if (!streamKam || !videoKam.videoWidth) {
                statusKamKk.innerHTML = '<span class="text-warning">Kamera belum siap.</span>';
                return;
            }
            const w = videoKam.videoWidth, h = videoKam.videoHeight;
            canvasKam.width = w;
            canvasKam.height = h;
            canvasKam.getContext('2d').drawImage(videoKam, 0, 0, w, h);
            canvasKam.toBlob(function (blob) {
                if (!blob) {
                    statusKamKk.innerHTML = '<span class="text-danger">Gagal mengambil foto.</span>';
                    return;
                }
                const file = new File([blob], 'kk-kamera-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                // Masukkan ke input file agar konsisten (preview & nama file).
                try {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    fileKk.files = dt.files;
                    btnKk.disabled = false;
                } catch (e) { /* sebagian browser tidak izinkan set files; tetap lanjut proses */ }
                if (modalKam) modalKam.hide();
                prosesFileKk(file);
            }, 'image/jpeg', 0.92);
        });
    }

    // ---- Ambil KTP dari Kamera (modal bersama untuk semua baris penduduk) ----
    const modalKtpEl = document.getElementById('modalKameraKtp');
    const videoKtp = document.getElementById('videoKameraKtp');
    const canvasKtp = document.getElementById('canvasKameraKtp');
    const btnJepretKtp = document.getElementById('btnJepretKtp');
    const statusKamKtp = document.getElementById('statusKameraKtp');
    let streamKtp = null;
    let modalKtp = null;
    let targetKtp = null; // baris penduduk yang sedang memakai kamera

    function hentikanKameraKtp() {
        if (streamKtp) {
            streamKtp.getTracks().forEach(function (t) { t.stop(); });
            streamKtp = null;
        }
    }

    function dapatkanModalKtp() {
        if (modalKtp) return modalKtp;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            modalKtp = new bootstrap.Modal(modalKtpEl);
        }
        return modalKtp;
    }

    // Dipanggil dari tombol kamera pada tiap baris penduduk.
    function bukaKameraKtp(ctx) {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            window.tampilkanPemberitahuan('Kamera tidak didukung di browser ini. Gunakan HTTPS/localhost dan browser modern.');
            return;
        }
        const m = dapatkanModalKtp();
        if (!m) {
            window.tampilkanPemberitahuan('Komponen tampilan belum siap. Muat ulang halaman lalu coba lagi.');
            return;
        }
        targetKtp = ctx;
        if (statusKamKtp) statusKamKtp.textContent = '';
        m.show();
    }

    if (modalKtpEl) {
        modalKtpEl.addEventListener('shown.bs.modal', function () {
            statusKamKtp.innerHTML = '<span class="text-muted">Mengaktifkan kamera...</span>';
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' } }, audio: false
            }).then(function (stream) {
                streamKtp = stream;
                videoKtp.srcObject = stream;
                statusKamKtp.textContent = '';
            }).catch(function () {
                statusKamKtp.innerHTML = '<span class="text-danger">Gagal mengakses kamera. Pastikan izin kamera diberikan.</span>';
            });
        });

        modalKtpEl.addEventListener('hidden.bs.modal', function () {
            hentikanKameraKtp();
            videoKtp.srcObject = null;
        });

        btnJepretKtp.addEventListener('click', function () {
            if (!streamKtp || !videoKtp.videoWidth) {
                statusKamKtp.innerHTML = '<span class="text-warning">Kamera belum siap.</span>';
                return;
            }
            if (!targetKtp) {
                statusKamKtp.innerHTML = '<span class="text-danger">Baris penduduk tidak dikenali.</span>';
                return;
            }
            const w = videoKtp.videoWidth, h = videoKtp.videoHeight;
            canvasKtp.width = w;
            canvasKtp.height = h;
            canvasKtp.getContext('2d').drawImage(videoKtp, 0, 0, w, h);
            canvasKtp.toBlob(function (blob) {
                if (!blob) {
                    statusKamKtp.innerHTML = '<span class="text-danger">Gagal mengambil foto.</span>';
                    return;
                }
                const file = new File([blob], 'ktp-kamera-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                const t = targetKtp;
                try {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    t.fileInput.files = dt.files;
                    if (t.btnBaca) t.btnBaca.disabled = false;
                } catch (e) { /* sebagian browser tidak izinkan set files; tetap lanjut proses */ }
                if (modalKtp) modalKtp.hide();
                prosesFileKtp(file, t.statusEl, t.previewEl, t.pathEl, t.wrap);
            }, 'image/jpeg', 0.92);
        });
    }

    // OCR KTP di server (OCR.space): kirim file -> baca & parse di server -> isi field.
    function prosesFileKtp(file, statusEl, previewEl, pathEl, wrap) {
        if (!file) return;
        const fd = new FormData();
        fd.append('file', file);
        statusEl.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm"></span> Membaca KTP...</span>';
        fetch('{{ route('ocr.baca-ktp') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: fd })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (pathEl) pathEl.value = res.path || '';
                filePreview(previewEl, res.url, file.type);
                statusEl.innerHTML = res.terbaca
                    ? '<span class="text-success"><i class="bi bi-check-circle"></i> ' + (res.pesan || 'Terbaca') + '</span>'
                    : '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> ' + (res.pesan || 'Tidak terbaca, isi manual') + '</span>';
                if (res.terbaca && res.data) {
                    ['nik','nama_lengkap','alamat','jenis_kelamin','tempat_lahir','tanggal_lahir','agama','status_perkawinan','pekerjaan','kewarganegaraan'].forEach(function (k) {
                        const el = wrap.querySelector('[name$="[' + k + ']"]');
                        if (el && res.data[k]) el.value = res.data[k];
                    });
                    hitungStatusWargaPenduduk(wrap);
                }
            })
            .catch(function () {
                statusEl.innerHTML = '<span class="text-danger">Gagal membaca KTP. Isi manual.</span>';
            });
    }

    function bacaDokumen(url, file, statusEl, onDone) {
        const fd = new FormData();
        fd.append('file', file);
        statusEl.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm"></span> Membaca...</span>';
        fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: fd })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                statusEl.innerHTML = res.terbaca
                    ? '<span class="text-success"><i class="bi bi-check-circle"></i> ' + (res.pesan || 'Terbaca') + '</span>'
                    : '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> ' + (res.pesan || 'Tidak terbaca, isi manual') + '</span>';
                onDone(res);
            })
            .catch(function () {
                statusEl.innerHTML = '<span class="text-danger">Gagal membaca dokumen. Isi manual.</span>';
            });
    }

    // ---- Status KK otomatis (dropdown, bisa dikoreksi manual) ----
    // Tetap bila RW 21 & Kelurahan Tanimulya; selain itu tidak tetap.
    function hitungStatusWarga() {
        const sel = document.getElementById('status_warga');
        if (!sel) return;
        const rw = (document.getElementById('rw').value || '').replace(/^0+/, '');
        const kel = (document.getElementById('kelurahan').value || '').toLowerCase();
        const tetap = rw === '21' && kel.includes('tanimulya');
        sel.value = tetap ? 'tetap' : 'tidak_tetap';
    }
    ['rw', 'kelurahan'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', hitungStatusWarga);
    });
    hitungStatusWarga();

    // Tandai field Nomor WA sebagai wajib (*) pada baris penduduk yang dipilih pemegang akun.
    function tandaiWaWajib() {
        const idx = document.getElementById('pemegangAkun').value;
        container.querySelectorAll('.penduduk-item').forEach(function (wrap) {
            const star = wrap.querySelector('.label-wa-wajib');
            const wa = wrap.querySelector('.no-wa-input');
            const pemegang = String(wrap.dataset.idx) === String(idx);
            if (star) star.style.display = pemegang ? '' : 'none';
            if (wa) wa.required = pemegang;
        });
    }

    // Validasi ringan sebelum submit: pastikan pemegang akun dipilih & punya Nomor WA.
    document.getElementById('formKeluarga').addEventListener('submit', function (e) {
        const idx = document.getElementById('pemegangAkun').value;
        if (!idx) {
            e.preventDefault();
            window.tampilkanPemberitahuan('Pilih satu penduduk sebagai pemegang akun keluarga.');
            return;
        }
        const wrap = container.querySelector('.penduduk-item[data-idx="' + idx + '"]');
        const wa = wrap ? wrap.querySelector('.no-wa-input') : null;
        if (wa && !wa.value.trim()) {
            e.preventDefault();
            window.tampilkanPemberitahuan('Nomor WA wajib diisi untuk pemegang akun keluarga.');
            wa.focus();
        }
    });
})();
</script>
@endpush
