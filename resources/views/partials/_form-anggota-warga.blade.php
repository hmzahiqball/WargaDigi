{{--
    Form anggota keluarga untuk role Warga (pengajuan mutasi).
    Struktur field identik dengan RT (_tpl-penduduk.blade.php) namun:
    - field name LANGSUNG/flat (nik, nama_lengkap, ...) karena controller Warga membaca flat.
    - form tunggal per aksi (bukan multi-baris dinamis).
    - mendukung OCR KTP (ocr.baca-ktp) & kamera KTP (modal bersama #modalKameraKtp).

    Variabel yang diharapkan:
    - $formId      : id unik form (mis. "formTambahAnggota" atau "formEditAnggota-<id>")
    - $action      : URL action
    - $method      : "POST" atau "PUT"
    - $submitLabel : teks tombol submit
    - $submitClass : kelas warna tombol submit (mis. "btn-success")
    - $member      : instance Penduduk untuk prefill (null bila tambah)
    - $requireKk   : bool, true bila upload KK wajib (tambah anggota)
    Opsi referensi:
    - $agamaOpts, $statusKawinOpts, $hubunganOpts, $pendidikanOpts

    Data KK (untuk checkbox "sama dengan KK") diambil dari atribut data-* pada <form>.
--}}
@php
    $member = $member ?? null;
    $requireKk = $requireKk ?? false;
    $submitClass = $submitClass ?? 'btn-success';
    $val = function ($field, $default = '') use ($member) {
        return old($field, $member->{$field} ?? $default);
    };
    $tglLahir = '';
    if ($member && $member->tanggal_lahir) {
        $tglLahir = $member->tanggal_lahir instanceof \Carbon\Carbon
            ? $member->tanggal_lahir->format('Y-m-d')
            : (string) $member->tanggal_lahir;
    }
@endphp

<form class="form-anggota-warga" id="{{ $formId }}" action="{{ $action }}" method="POST" enctype="multipart/form-data"
      data-kk-alamat="{{ $kkAlamatLengkap ?? '' }}"
      data-kk-domisili="{{ $kkDomisili ?? '' }}">
    @csrf
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(($method ?? 'POST') === 'PUT')
        @method('PUT')
    @endif

    <div class="row g-2">
        {{-- Upload KTP + OCR + Kamera --}}
        <div class="col-md-5">
            <label class="form-label small fw-semibold">Upload KTP</label>
            <input type="file" class="form-control form-control-sm admin-input file-ktp" accept=".jpg,.jpeg,.png,.pdf">
            <input type="hidden" name="file_ktp_path" class="ktp-path">
            <div class="d-flex flex-wrap gap-2 mt-1">
                <button type="button" class="btn btn-success btn-sm btn-baca-ktp" disabled><i class="bi bi-magic me-1"></i>Baca KTP</button>
                <button type="button" class="btn btn-outline-success btn-sm btn-kamera-ktp"><i class="bi bi-camera me-1"></i>Ambil KTP dari Kamera</button>
            </div>
            <div class="mt-1"><span class="small status-ktp"></span></div>
            <div class="preview-ktp mt-2"></div>

            <div class="mt-3">
                <label class="form-label small fw-semibold">Unggah Scan Kartu Keluarga (KK)@if($requireKk) <span class="text-danger">*</span>@endif</label>
                <input type="file" name="file_kk" class="form-control form-control-sm admin-input" accept=".jpg,.jpeg,.png,.pdf" @if($requireKk) required @endif>
                <small class="text-danger d-block mt-1" style="font-size:0.72rem;">
                    <i class="bi bi-exclamation-triangle"></i>
                    @if($requireKk) Wajib saat menambah anggota. @else Wajib jika mengubah NIK/Nama. @endif
                </small>
            </div>
        </div>

        {{-- Field data penduduk --}}
        <div class="col-md-7">
            <div class="row g-2">
                <div class="col-md-6"><label class="form-label small">NIK *</label><input type="text" maxlength="16" minlength="16" class="form-control form-control-sm admin-input" name="nik" value="{{ $val('nik') }}" required></div>
                <div class="col-md-6"><label class="form-label small">Nama Lengkap *</label><input type="text" class="form-control form-control-sm admin-input" name="nama_lengkap" value="{{ $val('nama_lengkap') }}" required></div>
                <div class="col-md-6">
                    <label class="form-label small">Nomor WA</label>
                    <input type="text" inputmode="tel" class="form-control form-control-sm admin-input" name="no_wa" value="{{ $val('no_wa') }}" placeholder="08...">
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label small mb-0">Alamat (sesuai KTP)</label>
                        <div class="form-check form-check-sm">
                            <input class="form-check-input chk-alamat-kk" type="checkbox">
                            <label class="form-check-label small text-muted">Alamat sama dengan KK</label>
                        </div>
                    </div>
                    <input type="text" class="form-control form-control-sm admin-input alamat-ktp mt-1" name="alamat" value="{{ $val('alamat') }}" placeholder="Terisi otomatis dari KTP, atau centang 'sama dengan KK'">
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label small mb-0">Alamat Domisili (tempat tinggal)</label>
                        <div class="form-check form-check-sm">
                            <input class="form-check-input chk-domisili-kk" type="checkbox">
                            <label class="form-check-label small text-muted">Alamat domisili sama dengan KK</label>
                        </div>
                    </div>
                    <input type="text" class="form-control form-control-sm admin-input domisili-ktp mt-1" name="alamat_domisili" value="{{ $val('alamat_domisili') }}" placeholder="Isi bila berbeda dari alamat KTP, atau centang 'sama dengan KK'">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Jenis Kelamin *</label>
                    <select class="form-select form-select-sm admin-input" name="jenis_kelamin" required>
                        <option value="L" {{ $val('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $val('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label small">Tempat Lahir *</label><input type="text" class="form-control form-control-sm admin-input" name="tempat_lahir" value="{{ $val('tempat_lahir') }}" required></div>
                <div class="col-md-6"><label class="form-label small">Tanggal Lahir *</label><input type="date" class="form-control form-control-sm admin-input" name="tanggal_lahir" value="{{ old('tanggal_lahir', $tglLahir) }}" required></div>
                <div class="col-md-6">
                    <label class="form-label small">Agama *</label>
                    <select class="form-select form-select-sm admin-input" name="agama" required>
                        @foreach($agamaOpts as $a)<option value="{{ $a }}" {{ $val('agama') === $a ? 'selected' : '' }}>{{ $a }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label small">Pekerjaan *</label><input type="text" class="form-control form-control-sm admin-input" name="pekerjaan" value="{{ $val('pekerjaan') }}" required></div>
                <div class="col-md-6">
                    <label class="form-label small">Status Kawin *</label>
                    <select class="form-select form-select-sm admin-input" name="status_perkawinan" required>
                        @foreach($statusKawinOpts as $s)<option value="{{ $s }}" {{ $val('status_perkawinan') === $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Kewarganegaraan</label>
                    <select class="form-select form-select-sm admin-input" name="kewarganegaraan" required>
                        <option value="WNI" {{ $val('kewarganegaraan', 'WNI') === 'WNI' ? 'selected' : '' }}>WNI</option>
                        <option value="WNA" {{ $val('kewarganegaraan') === 'WNA' ? 'selected' : '' }}>WNA</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Hubungan Keluarga *</label>
                    <select class="form-select form-select-sm admin-input" name="status_hubungan_keluarga" required>
                        @foreach($hubunganOpts as $h)<option value="{{ $h }}" {{ $val('status_hubungan_keluarga') === $h ? 'selected' : '' }}>{{ $h }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Pendidikan</label>
                    <select class="form-select form-select-sm admin-input" name="pendidikan_terakhir">
                        <option value="">-</option>
                        @foreach($pendidikanOpts as $p)<option value="{{ $p }}" {{ $val('pendidikan_terakhir') === $p ? 'selected' : '' }}>{{ $p }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Status Warga</label>
                    <select class="form-select form-select-sm admin-input status-warga-penduduk" name="status_warga">
                        <option value="tidak_tetap" {{ $val('status_warga', 'tidak_tetap') === 'tetap' ? '' : 'selected' }}>Bukan Warga Tetap</option>
                        <option value="tetap" {{ $val('status_warga') === 'tetap' ? 'selected' : '' }}>Warga Tetap</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        @if(!empty($cancelUrl))
            <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Batal</a>
        @else
            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Batal</button>
        @endif
        <button type="submit" class="btn {{ $submitClass }} px-4 py-2 rounded-3">{{ $submitLabel }}</button>
    </div>
</form>
