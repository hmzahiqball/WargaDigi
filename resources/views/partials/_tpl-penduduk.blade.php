{{-- Template baris penduduk (dipakai JS halaman RT Tambah Keluarga).
     Membutuhkan: $agamaOpts, $statusKawinOpts, $hubunganOpts, $pendidikanOpts --}}
<template id="tplPenduduk">
    <div class="border rounded-3 p-3 mb-3 penduduk-item" style="background:#fafafa;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold small penduduk-title">Penduduk</span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-penduduk"><i class="bi bi-trash"></i></button>
        </div>
        <div class="row g-2">
            <div class="col-md-5">
                <label class="form-label small fw-semibold">Upload KTP</label>
                <input type="file" class="form-control form-control-sm admin-input file-ktp" accept=".jpg,.jpeg,.png,.pdf">
                <input type="hidden" class="ktp-path" data-name="file_ktp_path">
                <div class="d-flex flex-wrap gap-2 mt-1">
                    <button type="button" class="btn btn-success btn-sm btn-baca-ktp" disabled><i class="bi bi-magic me-1"></i>Baca KTP</button>
                    <button type="button" class="btn btn-outline-success btn-sm btn-kamera-ktp"><i class="bi bi-camera me-1"></i>Ambil KTP dari Kamera</button>
                </div>
                <div class="mt-1"><span class="small status-ktp"></span></div>
                <div class="preview-ktp mt-2"></div>
            </div>
            <div class="col-md-7">
                <div class="row g-2">
                    <div class="col-md-6"><label class="form-label small">NIK *</label><input type="text" maxlength="16" class="form-control form-control-sm admin-input" data-name="nik" required></div>
                    <div class="col-md-6"><label class="form-label small">Nama Lengkap *</label><input type="text" class="form-control form-control-sm admin-input" data-name="nama_lengkap" required></div>
                    <div class="col-md-6">
                        <label class="form-label small">Nomor WA <span class="label-wa-wajib text-danger" style="display:none;">*</span></label>
                        <input type="text" inputmode="tel" class="form-control form-control-sm admin-input no-wa-input" data-name="no_wa" placeholder="Wajib bila pemegang akun keluarga">
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small mb-0">Alamat (sesuai KTP)</label>
                            <div class="form-check form-check-sm">
                                <input class="form-check-input chk-alamat-kk" type="checkbox">
                                <label class="form-check-label small text-muted">Alamat sama dengan KK</label>
                            </div>
                        </div>
                        <input type="text" class="form-control form-control-sm admin-input alamat-ktp mt-1" data-name="alamat" placeholder="Terisi otomatis dari KTP, atau centang 'sama dengan KK'">
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small mb-0">Alamat Domisili (tempat tinggal)</label>
                            <div class="form-check form-check-sm">
                                <input class="form-check-input chk-domisili-kk" type="checkbox">
                                <label class="form-check-label small text-muted">Alamat domisili sama dengan KK</label>
                            </div>
                        </div>
                        <input type="text" class="form-control form-control-sm admin-input domisili-ktp mt-1" data-name="alamat_domisili" placeholder="Isi bila berbeda dari alamat KTP, atau centang 'sama dengan KK'">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Jenis Kelamin *</label>
                        <select class="form-select form-select-sm admin-input" data-name="jenis_kelamin" required>
                            <option value="L">Laki-laki</option><option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6"><label class="form-label small">Tempat Lahir *</label><input type="text" class="form-control form-control-sm admin-input" data-name="tempat_lahir" required></div>
                    <div class="col-md-6"><label class="form-label small">Tanggal Lahir *</label><input type="date" class="form-control form-control-sm admin-input" data-name="tanggal_lahir" required></div>
                    <div class="col-md-4">
                        <label class="form-label small">Agama *</label>
                        <select class="form-select form-select-sm admin-input" data-name="agama" required>
                            @foreach($agamaOpts as $a)<option value="{{ $a }}">{{ $a }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><label class="form-label small">Pekerjaan *</label><input type="text" class="form-control form-control-sm admin-input" data-name="pekerjaan" required></div>
                    <div class="col-md-4">
                        <label class="form-label small">Status Kawin *</label>
                        <select class="form-select form-select-sm admin-input" data-name="status_perkawinan" required>
                            @foreach($statusKawinOpts as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Kewarganegaraan</label>
                        <select class="form-select form-select-sm admin-input" data-name="kewarganegaraan">
                            <option value="WNI">WNI</option><option value="WNA">WNA</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Hubungan Keluarga *</label>
                        <select class="form-select form-select-sm admin-input" data-name="status_hubungan_keluarga" required>
                            @foreach($hubunganOpts as $h)<option value="{{ $h }}">{{ $h }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Pendidikan</label>
                        <select class="form-select form-select-sm admin-input" data-name="pendidikan_terakhir">
                            <option value="">-</option>
                            @foreach($pendidikanOpts as $p)<option value="{{ $p }}">{{ $p }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Status Warga</label>
                        <select class="form-select form-select-sm admin-input status-warga-penduduk" data-name="status_warga">
                            <option value="tidak_tetap">Bukan Warga Tetap</option>
                            <option value="tetap">Warga Tetap</option>
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input radio-pemegang" type="radio" name="__pemegang_radio">
                            <label class="form-check-label small">Pemegang akun keluarga</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
