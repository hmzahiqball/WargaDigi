@extends('layouts.global')

@section('title', 'Manajemen Hak Akses')

@section('content')
<div class="admin-page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1>Manajemen Hak Akses</h1>
        <p class="text-muted">Kelola peran (role) setiap pengguna sistem. Perubahan langsung tersinkron dengan sistem hak akses (RBAC).</p>
    </div>
    <button type="button" class="btn btn-success" style="border-radius: 0.5rem;" data-bs-toggle="modal" data-bs-target="#modalBuatPengurus">
        <i class="bi bi-person-badge me-1"></i> Buat Akun Pengurus
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="border-radius: 0.5rem;">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@php
    // Error yang berasal dari dalam modal tidak perlu ditampilkan lagi sebagai banner atas.
    $errorDariModal = session('open_edit_user') || old('sumber') || old('username') || old('nik');
@endphp
@if($errors->any() && ! $errorDariModal)
    <div class="alert alert-danger mb-4" style="border-radius: 0.5rem;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="alert alert-info d-flex align-items-start gap-2 mb-4" style="border-radius: 0.5rem;">
    <i class="bi bi-info-circle-fill mt-1"></i>
    <span class="small">Peran <strong>Ketua RT</strong> dan <strong>Ketua RW</strong> hanya dapat diberikan kepada akun ber-NIK yang sudah ditetapkan sebagai pejabat aktif di menu <strong>Kepengurusan</strong>.</span>
</div>

{{-- Search --}}
<div class="admin-card mb-4 animate-in delay-1">
    <div class="admin-card-body">
        <form method="GET" action="{{ route('admin.manajemen-hak-akses') }}">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="admin-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" placeholder="Cari nama pengguna atau NIK..." id="searchUser" class="admin-input form-control" value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="submit" class="btn btn-success px-4" style="border-radius: 0.5rem;">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Users Table --}}
<div class="admin-card mb-4 animate-in delay-2">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0" id="usersTable">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>NIK</th>
                        <th>Status Akun</th>
                        <th>Role / Hak Akses</th>
                        <th style="width: 90px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php $assigned = $userRoles[$user->id] ?? []; @endphp
                    <tr>
                        <td>
                            <div class="user-info">
                                <span class="fw-semibold">{{ $user->name }}</span>
                                <small class="d-block text-muted">{{ $user->username }}</small>
                            </div>
                        </td>
                        <td><span class="text-muted">{{ $user->nik ?: '—' }}</span></td>
                        <td>
                            @if($user->status_akun === 'Active')
                                <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $user->status_akun }}</span>
                            @endif
                        </td>
                        <td>
                            @forelse($assigned as $role)
                                <span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1">{{ $role }}</span>
                            @empty
                                <span class="text-muted small">Belum ada peran</span>
                            @endforelse
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-success" style="border-radius: 0.5rem;"
                                data-bs-toggle="modal" data-bs-target="#modalEditRole-{{ $user->id }}" title="Edit peran">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="admin-card-footer">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} pengguna</span>
            <div>{{ $users->links() }}</div>
        </div>
    </div>
    @endif
</div>

{{-- ========================= MODAL: Buat Akun Pengurus ========================= --}}
<div class="modal fade" id="modalBuatPengurus" tabindex="-1" aria-labelledby="modalBuatPengurusLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0.75rem;">
            <form method="POST" action="{{ route('admin.manajemen-hak-akses.pengurus.store') }}" id="formBuatPengurus">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBuatPengurusLabel"><i class="bi bi-person-badge me-2 text-success"></i>Buat Akun Pengurus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any() && (old('sumber') || old('username') || old('nik')))
                        <div class="alert alert-danger small" style="border-radius: 0.5rem;">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{-- Pilih sumber akun --}}
                    <label class="form-label small fw-semibold d-block mb-2">Sumber akun</label>
                    @php $sumberLama = old('sumber', 'baru'); @endphp
                    <div class="btn-group w-100 mb-3" role="group" aria-label="Sumber akun pengurus">
                        <input type="radio" class="btn-check" name="sumber" id="sumberWarga" value="warga" {{ $sumberLama === 'warga' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success" for="sumberWarga"><i class="bi bi-people me-1"></i>Ambil dari warga</label>
                        <input type="radio" class="btn-check" name="sumber" id="sumberBaru" value="baru" {{ $sumberLama !== 'warga' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success" for="sumberBaru"><i class="bi bi-person-plus me-1"></i>Username baru</label>
                    </div>

                    {{-- MODE: Ambil dari warga (anggota keluarga non-kepala keluarga yang belum punya akun) --}}
                    <div data-mode="warga" class="mb-1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Pilih warga <span class="text-danger">*</span></label>
                            @if($pendudukOptions->isEmpty())
                                <div class="alert alert-secondary small mb-0" style="border-radius: 0.5rem;">
                                    Semua penduduk sudah memiliki akun. Gunakan Edit Peran untuk menambahkan peran pengurus.
                                </div>
                            @else
                                <select name="nik" id="selectWargaNik" class="form-select admin-input" data-tomselect data-placeholder="Cari nama atau NIK...">
                                    <option value="">— Pilih warga —</option>
                                    @foreach($pendudukOptions as $p)
                                        <option value="{{ $p['nik'] }}"
                                            data-username-saran="{{ $p['username_saran'] }}"
                                            {{ old('nik') === $p['nik'] ? 'selected' : '' }}>
                                            {{ $p['nama_lengkap'] }} — {{ $p['nik'] }}{{ $p['rt'] ? ' (RT ' . $p['rt'] . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hanya penduduk yang belum punya akun. Akun baru akan dibuat untuk warga terpilih.</small>
                            @endif
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username_warga" class="form-control admin-input" placeholder="mis. ketuart01" value="{{ old('username') }}">
                                <div class="invalid-feedback" data-err="username_warga"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_warga" class="form-control admin-input" placeholder="Min. 8 karakter">
                                <div class="invalid-feedback" data-err="password_warga"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_warga_confirmation" class="form-control admin-input" placeholder="Ulangi password">
                                <div class="invalid-feedback" data-err="password_warga_confirmation"></div>
                            </div>
                        </div>

                        <label class="form-label small fw-semibold d-block mt-3 mb-2">Peran (boleh lebih dari satu) <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach($roles as $role)
                                @if($role !== 'Warga')
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles_warga[]" value="{{ $role }}" id="wrole_{{ $loop->index }}">
                                        <label class="form-check-label small" for="wrole_{{ $loop->index }}">
                                            {{ $role }}@if(in_array($role, ['Ketua RT','Ketua RW'], true))<span class="text-muted"> (pejabat aktif)</span>@endif
                                        </label>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        {{-- RT untuk peran operator/ketua RT (tampil bila peran RT dipilih) --}}
                        <div class="mt-3 rt-wrap d-none">
                            <label class="form-label small fw-semibold">RT yang Dikelola <span class="text-danger">*</span></label>
                            <select name="rt_id" class="form-select admin-input">
                                <option value="">-- Pilih RT --</option>
                                @foreach($rtOptions as $rt)
                                    <option value="{{ $rt->id }}">RT {{ $rt->kode_rt }}@if($rt->nama_rt && $rt->nama_rt !== $rt->kode_rt) - {{ $rt->nama_rt }}@endif</option>
                                @endforeach
                            </select>
                            <div class="form-text">Default mengikuti RT warga, dapat diubah.</div>
                        </div>
                        <div class="alert alert-warning small mt-3 mb-0" style="border-radius: 0.5rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Peran <strong>Ketua RT</strong> / <strong>Ketua RW</strong> hanya berhasil jika warga ini sudah menjadi pejabat aktif di menu Kepengurusan.
                        </div>
                    </div>

                    {{-- MODE: Username baru --}}
                    <div data-mode="baru" class="mb-1">
                        <p class="text-muted small mb-3">Akun pengurus tanpa NIK, login via username. Peran Ketua RT / Ketua RW tidak tersedia di mode ini.</p>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username_baru" class="form-control admin-input" placeholder="mis. opkontenrw21" value="{{ old('username') }}">
                                <div class="invalid-feedback" data-err="username_baru"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_baru" class="form-control admin-input" placeholder="Min. 8 karakter">
                                <div class="invalid-feedback" data-err="password_baru"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_baru_confirmation" class="form-control admin-input" placeholder="Ulangi password">
                                <div class="invalid-feedback" data-err="password_baru_confirmation"></div>
                            </div>
                        </div>
                        <label class="form-label small fw-semibold d-block mt-3 mb-2">Peran (boleh lebih dari satu) <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach($pengurusRoles as $role)
                                @if(! in_array($role, ['Ketua RT', 'Ketua RW'], true))
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles_baru[]" value="{{ $role }}" id="brole_{{ $loop->index }}">
                                        <label class="form-check-label small" for="brole_{{ $loop->index }}">{{ $role }}</label>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        {{-- RT untuk peran operator RT (tampil bila peran RT dipilih) --}}
                        <div class="mt-3 rt-wrap d-none">
                            <label class="form-label small fw-semibold">RT yang Dikelola <span class="text-danger">*</span></label>
                            <select name="rt_id" class="form-select admin-input">
                                <option value="">-- Pilih RT --</option>
                                @foreach($rtOptions as $rt)
                                    <option value="{{ $rt->id }}">RT {{ $rt->kode_rt }}@if($rt->nama_rt && $rt->nama_rt !== $rt->kode_rt) - {{ $rt->nama_rt }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================= MODAL: Edit Peran per user ========================= --}}
@foreach($users as $user)
@php $assigned = $userRoles[$user->id] ?? []; @endphp
<div class="modal fade" id="modalEditRole-{{ $user->id }}" tabindex="-1" aria-labelledby="modalEditRoleLabel-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0.75rem;">
            <form method="POST" action="{{ route('admin.manajemen-hak-akses.update', $user->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditRoleLabel-{{ $user->id }}">
                        <i class="bi bi-shield-lock me-2 text-success"></i>Edit Peran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any() && session('open_edit_user') === $user->id)
                        <div class="alert alert-danger small" style="border-radius: 0.5rem;">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <small class="text-muted">{{ $user->username }}{{ $user->nik ? ' · NIK ' . $user->nik : ' · tanpa NIK' }}</small>
                    </div>
                    <label class="form-label small fw-semibold d-block mb-2">Pilih peran (centang satu atau lebih)</label>
                    <div class="row g-2">
                        @foreach($roles as $role)
                            @php
                                $butuhNik = ($role === 'Warga' || in_array($role, ['Ketua RT', 'Ketua RW'], true));
                                $disabled = ($butuhNik && empty($user->nik) && ! in_array($role, $assigned, true));
                                $keterangan = '';
                                if ($role === 'Warga' && empty($user->nik)) { $keterangan = ' (butuh NIK)'; }
                                elseif (in_array($role, ['Ketua RT', 'Ketua RW'], true)) { $keterangan = ' (pejabat aktif)'; }
                            @endphp
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role }}"
                                        id="role_{{ $user->id }}_{{ $loop->index }}"
                                        {{ in_array($role, $assigned, true) ? 'checked' : '' }}
                                        {{ $disabled ? 'disabled' : '' }}>
                                    <label class="form-check-label small" for="role_{{ $user->id }}_{{ $loop->index }}">
                                        {{ $role }}<span class="text-muted">{{ $keterangan }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- RT untuk peran operator/ketua RT --}}
                    <div class="mt-3 rt-wrap-edit {{ array_intersect($assigned, $rolesButuhRt) ? '' : 'd-none' }}">
                        <label class="form-label small fw-semibold">RT yang Dikelola <span class="text-danger">*</span></label>
                        <select name="rt_id" class="form-select admin-input">
                            <option value="">-- Pilih RT --</option>
                            @foreach($rtOptions as $rt)
                                <option value="{{ $rt->id }}" {{ ($userRtId[$user->id] ?? null) === $rt->id ? 'selected' : '' }}>RT {{ $rt->kode_rt }}@if($rt->nama_rt && $rt->nama_rt !== $rt->kode_rt) - {{ $rt->nama_rt }}@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-warning small mt-3 mb-0" style="border-radius: 0.5rem;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Peran <strong>Ketua RT</strong> / <strong>Ketua RW</strong> hanya berhasil disimpan jika akun ini sudah menjadi pejabat aktif di menu Kepengurusan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-floppy me-1"></i>Simpan Peran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('formBuatPengurus');
        if (!form) return;

        var modeWarga = form.querySelector('[data-mode="warga"]');
        var modeBaru  = form.querySelector('[data-mode="baru"]');
        var radios    = form.querySelectorAll('input[name="sumber"]');

        function currentMode() {
            var checked = form.querySelector('input[name="sumber"]:checked');
            return checked ? checked.value : 'baru';
        }

        function applyMode() {
            var mode = currentMode();
            modeWarga.style.display = (mode === 'warga') ? '' : 'none';
            modeBaru.style.display  = (mode === 'baru')  ? '' : 'none';
        }

        radios.forEach(function (r) { r.addEventListener('change', applyMode); });
        applyMode();

        // Isi otomatis saran username saat warga dipilih (admin tetap bisa ubah).
        var selectWarga = form.querySelector('#selectWargaNik');
        var inputUsernameWarga = modeWarga.querySelector('[name="username_warga"]');
        if (selectWarga && inputUsernameWarga) {
            selectWarga.addEventListener('change', function () {
                var opt = selectWarga.options[selectWarga.selectedIndex];
                var saran = opt ? (opt.getAttribute('data-username-saran') || '') : '';
                // Hanya isi bila field masih kosong atau berisi saran sebelumnya.
                if (!inputUsernameWarga.value || inputUsernameWarga.dataset.autofill === '1') {
                    inputUsernameWarga.value = saran;
                    inputUsernameWarga.dataset.autofill = '1';
                }
            });
            inputUsernameWarga.addEventListener('input', function () {
                inputUsernameWarga.dataset.autofill = '0';
            });
        }

        function setError(el, msg) {
            el.classList.add('is-invalid');
            var fb = el.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.textContent = msg;
        }
        function clearErrors(box) {
            box.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
        }

        // Validasi client-side. Mengembalikan true bila lolos.
        function validate() {
            var mode = currentMode();
            var ok = true;

            if (mode === 'warga') {
                clearErrors(modeWarga);
                var nik = modeWarga.querySelector('[name="nik"]');
                var uw  = modeWarga.querySelector('[name="username_warga"]');
                var pw  = modeWarga.querySelector('[name="password_warga"]');
                var cw  = modeWarga.querySelector('[name="password_warga_confirmation"]');
                var rolesChecked = modeWarga.querySelectorAll('[name="roles_warga[]"]:checked').length;

                if (!nik || !nik.value) {
                    if (nik) setError(nik, 'Pilih warga terlebih dahulu.');
                    ok = false;
                }
                if (uw && !uw.value.trim()) { setError(uw, 'Username wajib diisi.'); ok = false; }
                if (pw && !pw.value) { setError(pw, 'Password wajib diisi.'); ok = false; }
                else if (pw && pw.value.length < 8) { setError(pw, 'Password minimal 8 karakter.'); ok = false; }
                if (pw && cw && pw.value !== cw.value) { setError(cw, 'Konfirmasi password tidak sama.'); ok = false; }
                if (rolesChecked === 0) { ok = false; window.tampilkanPemberitahuan('Pilih minimal satu peran.'); }
            } else {
                clearErrors(modeBaru);
                var ub = modeBaru.querySelector('[name="username_baru"]');
                var pb = modeBaru.querySelector('[name="password_baru"]');
                var cb = modeBaru.querySelector('[name="password_baru_confirmation"]');
                var rolesCheckedB = modeBaru.querySelectorAll('[name="roles_baru[]"]:checked').length;

                if (!ub.value.trim()) { setError(ub, 'Username wajib diisi.'); ok = false; }
                if (!pb.value) { setError(pb, 'Password wajib diisi.'); ok = false; }
                else if (pb.value.length < 8) { setError(pb, 'Password minimal 8 karakter.'); ok = false; }
                if (pb.value !== cb.value) { setError(cb, 'Konfirmasi password tidak sama.'); ok = false; }
                if (rolesCheckedB === 0) { ok = false; window.tampilkanPemberitahuan('Pilih minimal satu peran.'); }
            }

            return ok;
        }

        // Saat submit: validasi dulu, lalu petakan field mode aktif ke nama yang
        // diharapkan server, dan matikan field mode non-aktif agar tidak dikirim.
        form.addEventListener('submit', function (e) {
            if (!validate()) { e.preventDefault(); return; }

            var mode = currentMode();
            var suffix = (mode === 'warga') ? 'warga' : 'baru';

            // Rename field aktif -> nama kanonik yang diharapkan server.
            var activeBox = (mode === 'warga') ? modeWarga : modeBaru;
            activeBox.querySelectorAll('input, select').forEach(function (el) {
                if (!el.name) return;
                el.name = el.name
                    .replace('password_' + suffix + '_confirmation', 'password_confirmation')
                    .replace('username_' + suffix, 'username')
                    .replace('password_' + suffix, 'password')
                    .replace('roles_' + suffix + '[]', 'roles[]');
            });

            // Nonaktifkan seluruh input mode non-aktif agar tidak ikut terkirim.
            var box = (mode === 'warga') ? modeBaru : modeWarga;
            box.querySelectorAll('input, select').forEach(function (el) { el.disabled = true; });
        });

        // --- Tampilkan dropdown RT bila peran yang butuh RT dicentang ---
        var ROLES_BUTUH_RT = @json($rolesButuhRt);
        function toggleRtWrap(scope, checkboxSelector) {
            if (!scope) return;
            var wrap = scope.querySelector('.rt-wrap');
            if (!wrap) return;
            var perluRt = Array.prototype.some.call(
                scope.querySelectorAll(checkboxSelector + ':checked'),
                function (c) { return ROLES_BUTUH_RT.indexOf(c.value) !== -1; }
            );
            wrap.classList.toggle('d-none', !perluRt);
        }
        if (modeWarga) {
            modeWarga.addEventListener('change', function (e) {
                if (e.target.matches('[name="roles_warga[]"]')) toggleRtWrap(modeWarga, '[name="roles_warga[]"]');
            });
        }
        if (modeBaru) {
            modeBaru.addEventListener('change', function (e) {
                if (e.target.matches('[name="roles_baru[]"]')) toggleRtWrap(modeBaru, '[name="roles_baru[]"]');
            });
        }
        // Modal edit peran: toggle RT per modal.
        document.querySelectorAll('[id^="modalEditRole-"]').forEach(function (modal) {
            modal.addEventListener('change', function (e) {
                if (!e.target.matches('[name="roles[]"]')) return;
                var wrap = modal.querySelector('.rt-wrap-edit');
                if (!wrap) return;
                var perluRt = Array.prototype.some.call(
                    modal.querySelectorAll('[name="roles[]"]:checked'),
                    function (c) { return ROLES_BUTUH_RT.indexOf(c.value) !== -1; }
                );
                wrap.classList.toggle('d-none', !perluRt);
            });
        });

        // Buka kembali modal "Buat Pengurus" bila error validasi berasal dari sana.
        @if($errors->any() && (old('sumber') || old('username') || old('nik')))
            var m = document.getElementById('modalBuatPengurus');
            if (m) new bootstrap.Modal(m).show();
        @endif

        // Buka kembali modal "Edit Peran" milik user yang gagal validasi.
        @if($errors->any() && session('open_edit_user'))
            var em = document.getElementById('modalEditRole-{{ session('open_edit_user') }}');
            if (em) new bootstrap.Modal(em).show();
        @endif
    });
</script>
@endpush
