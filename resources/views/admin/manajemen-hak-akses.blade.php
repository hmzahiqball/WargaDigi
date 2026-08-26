@extends('layouts.admin')

@section('title', 'Manajemen Hak Akses')

@section('content')
<div class="admin-page-header">
    <h1>Manajemen Hak Akses</h1>
    <p class="text-muted">Atur izin dan tingkat akses untuk setiap pengguna sistem berdasarkan peran mereka.</p>
</div>

{{-- Add User Button --}}
<div class="mb-4">
    <button class="btn btn-success px-4 py-2" style="border-radius: 0.5rem;" id="btnAddUser">
        <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
    </button>
</div>

{{-- Search & Filter --}}
<div class="admin-card mb-4 animate-in delay-1">
    <div class="admin-card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="admin-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari nama pengguna..." id="searchUser" class="admin-input">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex align-items-center justify-content-md-end gap-2">
                    <label class="small text-muted fw-semibold">Filter RT:</label>
                    <select class="form-select admin-input" id="filterRT" style="width: auto;">
                        <option value="">Semua RT</option>
                        <option value="RT 01">RT 01</option>
                        <option value="RT 02">RT 02</option>
                        <option value="RT 03">RT 03</option>
                        <option value="RT 04">RT 04</option>
                        <option value="RT 05">RT 05</option>
                    </select>
                </div>
            </div>
        </div>
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
                        <th>Role Utama</th>
                        <th class="text-center" colspan="5">Hak Akses Sistem</th>
                    </tr>
                    <tr class="access-header">
                        <th></th>
                        <th></th>
                        <th class="text-center"><small>Op. Keuangan</small></th>
                        <th class="text-center"><small>Op. Konten</small></th>
                        <th class="text-center"><small>Admin RW</small></th>
                        <th class="text-center"><small>Pimpinan RT</small></th>
                        <th class="text-center"><small>Pimpinan RW</small></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr data-user-index="{{ $index }}">
                        <td>
                            <div class="user-info">
                                <span class="fw-semibold">{{ $user['name'] }}</span>
                                <small class="d-block text-muted">{{ $user['nik'] }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="admin-badge role-badge" style="background-color: {{ $user['role_color'] }}20; color: {{ $user['role_color'] }}; border: 1px solid {{ $user['role_color'] }}40;">
                                {{ $user['role'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input access-check" {{ $user['access']['op_keuangan'] ? 'checked' : '' }} data-access="op_keuangan" style="border-color: #2E7D32;">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input access-check" {{ $user['access']['op_konten'] ? 'checked' : '' }} data-access="op_konten" style="border-color: #2E7D32;">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input access-check" {{ $user['access']['admin_rw'] ? 'checked' : '' }} data-access="admin_rw" style="border-color: #2E7D32;">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input access-check" {{ $user['access']['pimpinan_rt'] ? 'checked' : '' }} data-access="pimpinan_rt" style="border-color: #2E7D32;">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input access-check" {{ $user['access']['pimpinan_rw'] ? 'checked' : '' }} data-access="pimpinan_rw" style="border-color: #2E7D32;">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-footer text-end">
        <button class="btn btn-success px-4" style="border-radius: 0.5rem;" id="saveAccess">
            <i class="bi bi-floppy"></i> Simpan Perubahan
        </button>
    </div>
</div>

{{-- Impor Massal --}}
<div class="admin-card animate-in delay-3">
    <div class="admin-card-header-inline">
        <h5><i class="bi bi-cloud-arrow-up text-primary"></i> Impor Massal Data Warga</h5>
    </div>
    <div class="admin-card-body">
        <p class="text-muted small mb-3">Unggah file Excel (.xlsx) untuk mendaftarkan beberapa penghuni sekaligus. Pastikan file Anda mengikuti templat standar.</p>

        <div class="admin-dropzone" id="dropzone">
            <div class="dropzone-content">
                <div class="dropzone-icon">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>
                <h6>Seret dan lepaskan berkas Anda di sini</h6>
                <p class="text-muted small">Atau klik untuk memilih file dari komputer Anda (Ukuran maksimal: 5 MB)</p>
                <button class="btn btn-outline-secondary btn-sm px-4" style="border-radius: 0.5rem;" id="browseFiles">Browse Files</button>
                <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" class="d-none">
            </div>
            <div class="dropzone-progress d-none" id="dropzoneProgress">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-file-earmark-spreadsheet text-success fs-3"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-semibold" id="uploadFileName">file.xlsx</span>
                            <span class="small text-muted" id="uploadPercent">0%</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 3px;">
                            <div class="progress-bar bg-success" id="uploadBar" style="width: 0%;"></div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" id="cancelUpload"><i class="bi bi-x"></i></button>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="#" class="text-decoration-none small" style="color: #2E7D32; font-weight: 600;" onclick="event.preventDefault(); showAdminToast('Template berhasil didownload!', 'success');">
                <i class="bi bi-download"></i> Download Template
            </a>
        </div>
    </div>
</div>

{{-- Add User Modal --}}
<div class="admin-modal-overlay d-none" id="addUserModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h5><i class="bi bi-person-plus-fill text-success"></i> Tambah Pengguna Baru</h5>
            <button class="admin-modal-close" id="closeAddUser"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="admin-modal-body">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Nama Lengkap</label>
                <input type="text" class="form-control admin-input" placeholder="Masukkan nama lengkap" id="newUserName">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">NIK</label>
                <input type="text" class="form-control admin-input" placeholder="16 digit NIK" id="newUserNik">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Role Utama</label>
                <select class="form-select admin-input" id="newUserRole">
                    <option value="">Pilih role...</option>
                    <option>Ketua RW</option>
                    <option>Bendahara RW</option>
                    <option>Sekretaris RW</option>
                    <option>Pimpinan RT</option>
                    <option>Operator Konten</option>
                    <option>Operator Keuangan</option>
                    <option>Warga</option>
                </select>
            </div>
        </div>
        <div class="admin-modal-footer">
            <button class="btn btn-outline-secondary" id="cancelAddUser" style="border-radius: 0.5rem;">Batal</button>
            <button class="btn btn-success" id="confirmAddUser" style="border-radius: 0.5rem;">
                <i class="bi bi-plus-lg"></i> Tambah Pengguna
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search users
    const searchInput = document.getElementById('searchUser');
    searchInput?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#usersTable tbody tr').forEach(row => {
            const name = row.querySelector('.user-info .fw-semibold')?.textContent.toLowerCase() || '';
            row.style.display = name.includes(query) ? '' : 'none';
        });
    });

    // Save access changes
    document.getElementById('saveAccess')?.addEventListener('click', function() {
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        this.disabled = true;
        setTimeout(() => {
            this.innerHTML = '<i class="bi bi-floppy"></i> Simpan Perubahan';
            this.disabled = false;
            showAdminToast('Hak akses berhasil disimpan!', 'success');
        }, 1500);
    });

    // Checkbox change feedback
    document.querySelectorAll('.access-check').forEach(cb => {
        cb.addEventListener('change', function() {
            this.closest('td').classList.add('highlight-change');
            setTimeout(() => this.closest('td').classList.remove('highlight-change'), 1000);
        });
    });

    // Add User Modal
    const modal = document.getElementById('addUserModal');
    document.getElementById('btnAddUser')?.addEventListener('click', () => modal.classList.remove('d-none'));
    document.getElementById('closeAddUser')?.addEventListener('click', () => modal.classList.add('d-none'));
    document.getElementById('cancelAddUser')?.addEventListener('click', () => modal.classList.add('d-none'));

    document.getElementById('confirmAddUser')?.addEventListener('click', function() {
        const name = document.getElementById('newUserName').value;
        const nik = document.getElementById('newUserNik').value;
        const role = document.getElementById('newUserRole').value;

        if (!name || !nik || !role) {
            showAdminToast('Harap isi semua field!', 'warning');
            return;
        }

        // Add row to table
        const tbody = document.querySelector('#usersTable tbody');
        const roleColors = {
            'Ketua RW': '#43A047', 'Bendahara RW': '#FF9800', 'Sekretaris RW': '#0288D1',
            'Pimpinan RT': '#7B1FA2', 'Operator Konten': '#0288D1', 'Operator Keuangan': '#E53935', 'Warga': '#78909c'
        };
        const color = roleColors[role] || '#78909c';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><div class="user-info"><span class="fw-semibold">${name}</span><small class="d-block text-muted">${nik}</small></div></td>
            <td><span class="admin-badge role-badge" style="background-color: ${color}20; color: ${color}; border: 1px solid ${color}40;">${role}</span></td>
            <td class="text-center"><input type="checkbox" class="form-check-input access-check" style="border-color: #2E7D32;"></td>
            <td class="text-center"><input type="checkbox" class="form-check-input access-check" style="border-color: #2E7D32;"></td>
            <td class="text-center"><input type="checkbox" class="form-check-input access-check" style="border-color: #2E7D32;"></td>
            <td class="text-center"><input type="checkbox" class="form-check-input access-check" style="border-color: #2E7D32;"></td>
            <td class="text-center"><input type="checkbox" class="form-check-input access-check" style="border-color: #2E7D32;"></td>
        `;
        tr.style.animation = 'fadeInUp 0.5s ease';
        tbody.appendChild(tr);

        modal.classList.add('d-none');
        document.getElementById('newUserName').value = '';
        document.getElementById('newUserNik').value = '';
        document.getElementById('newUserRole').value = '';
        showAdminToast(`Pengguna ${name} berhasil ditambahkan!`, 'success');
    });

    // Dropzone
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const browseBtn = document.getElementById('browseFiles');
    const dropzoneContent = dropzone?.querySelector('.dropzone-content');
    const dropzoneProgress = document.getElementById('dropzoneProgress');

    browseBtn?.addEventListener('click', () => fileInput.click());

    dropzone?.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    dropzone?.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });

    dropzone?.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) simulateUpload(e.dataTransfer.files[0]);
    });

    fileInput?.addEventListener('change', function() {
        if (this.files.length > 0) simulateUpload(this.files[0]);
    });

    function simulateUpload(file) {
        document.getElementById('uploadFileName').textContent = file.name;
        dropzoneContent.classList.add('d-none');
        dropzoneProgress.classList.remove('d-none');

        let progress = 0;
        const bar = document.getElementById('uploadBar');
        const percent = document.getElementById('uploadPercent');

        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                setTimeout(() => {
                    dropzoneContent.classList.remove('d-none');
                    dropzoneProgress.classList.add('d-none');
                    bar.style.width = '0%';
                    showAdminToast(`File ${file.name} berhasil diupload! 15 data warga diimpor.`, 'success');
                }, 500);
            }
            bar.style.width = progress + '%';
            percent.textContent = Math.round(progress) + '%';
        }, 200);
    }

    document.getElementById('cancelUpload')?.addEventListener('click', function() {
        dropzoneContent.classList.remove('d-none');
        dropzoneProgress.classList.add('d-none');
        showAdminToast('Upload dibatalkan', 'warning');
    });
});
</script>
@endpush
