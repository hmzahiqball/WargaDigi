@extends('layouts.admin')

@section('title', 'Arsip Data Warga')

@section('content')
<div class="admin-page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1>Arsip Data Warga</h1>
        <p class="text-muted">Mengelola dan menyimpan data serta cadangan data warga untuk jangka panjang.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary px-3" style="border-radius: 0.5rem;" id="exportArsip">
            <i class="bi bi-cloud-download"></i> Export Log
        </button>
        <button class="btn btn-success px-3" style="border-radius: 0.5rem;" id="startBackup">
            <i class="bi bi-shield-check"></i> Mulai Backup
        </button>
    </div>
</div>

<div class="row g-4">
    {{-- Left Column: Archive Table --}}
    <div class="col-lg-8">
        <div class="admin-card animate-in delay-1">
            <div class="admin-card-header-inline">
                <h5><i class="bi bi-archive text-success"></i> Warga yang Diarsipkan</h5>
                <select class="form-select admin-input" id="filterDuration" style="width: auto;">
                    <option value="">Semua Durasi</option>
                    <option value="5">> 5 tahun</option>
                    <option value="10">> 10 tahun</option>
                </select>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table mb-0" id="arsipTable">
                        <thead>
                            <tr>
                                <th>Name / NIK</th>
                                <th>Tanggal Pengarsipan</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($arsip as $data)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <span class="fw-semibold">{{ $data['name'] }}</span>
                                        <small class="d-block text-muted">{{ $data['nik'] }}</small>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $data['date'] }}</td>
                                <td>{{ $data['reason'] }}</td>
                                <td>
                                    <span class="admin-badge" style="background-color: {{ $data['status_color'] }}15; color: {{ $data['status_color'] }}; border: 1px solid {{ $data['status_color'] }}30;">
                                        {{ $data['status'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="admin-action-btn" title="Lihat Detail" onclick="showAdminToast('Detail arsip {{ $data['name'] }}', 'info')">
                                            <i class="bi bi-people"></i>
                                        </button>
                                        <button class="admin-action-btn" title="Hapus Arsip" onclick="confirmDelete('{{ $data['name'] }}')">
                                            <i class="bi bi-file-x"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="admin-card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Showing 1 to 3 of 45 entries</span>
                    <div class="admin-pagination">
                        <button class="page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn" onclick="showAdminToast('Halaman 2 dimuat', 'info')">2</button>
                        <button class="page-btn" onclick="showAdminToast('Halaman 2 dimuat', 'info')"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Management Panel + Storage --}}
    <div class="col-lg-4">
        {{-- Manajemen Data Panel --}}
        <div class="admin-card mb-4 animate-in delay-2">
            <div class="admin-card-header-inline">
                <h5><i class="bi bi-database-fill-gear text-success"></i> Manajemen Data</h5>
            </div>
            <div class="admin-card-body">
                <div class="management-action-item" onclick="showAdminToast('Proses pembersihan data lama dimulai...', 'warning')">
                    <div class="action-icon danger">
                        <i class="bi bi-trash3"></i>
                    </div>
                    <div>
                        <h6>Membersihkan Data Lama</h6>
                        <p class="text-muted small mb-0">Hapus secara permanen catatan warga negara yang telah meninggal dunia yang berusia lebih dari 10 tahun.</p>
                    </div>
                </div>

                <hr class="my-3">

                <div class="management-action-item" onclick="showAdminToast('Memulai proses pemulihan data...', 'info')">
                    <div class="action-icon primary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <div>
                        <h6>Pulihkan dari Penampungan</h6>
                        <p class="text-muted small mb-0">Mengembalikan data yang baru-baru ini diarsipkan ke sistem pengelolaan warga yang aktif.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Storage Status --}}
        <div class="admin-card animate-in delay-3">
            <div class="admin-card-header-inline">
                <h5>STATUS PENYIMPANAN ARSIP</h5>
            </div>
            <div class="admin-card-body">
                <div class="storage-display">
                    <div class="storage-value">
                        <span class="storage-used text-success">{{ $storage['used'] }}<small>GB</small></span>
                        <span class="storage-total text-muted">/ {{ $storage['total'] }} GB digunakan</span>
                    </div>
                    <div class="progress mt-2 mb-3" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-success" id="storageBar" style="width: 0%; transition: width 1.5s ease;" data-target="{{ ($storage['used'] / $storage['total']) * 100 }}"></div>
                        <div class="progress-bar bg-warning" id="storageBarSecondary" style="width: 0%; transition: width 1.5s ease 0.3s;" data-target="{{ (0.4 / $storage['total']) * 100 }}"></div>
                    </div>
                    <div class="storage-breakdown">
                        @foreach($storage['breakdown'] as $item)
                        <div class="storage-item">
                            <span class="storage-dot" style="background: {{ $item['color'] }};"></span>
                            <span class="small">{{ $item['label'] }} ({{ $item['size'] }})</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="admin-modal-overlay d-none" id="deleteModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h5><i class="bi bi-exclamation-triangle text-danger"></i> Konfirmasi Hapus</h5>
            <button class="admin-modal-close" onclick="document.getElementById('deleteModal').classList.add('d-none')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="admin-modal-body">
            <p>Apakah Anda yakin ingin menghapus data arsip <strong id="deleteName"></strong>?</p>
            <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="admin-modal-footer">
            <button class="btn btn-outline-secondary" onclick="document.getElementById('deleteModal').classList.add('d-none')" style="border-radius: 0.5rem;">Batal</button>
            <button class="btn btn-danger" onclick="executeDelete()" style="border-radius: 0.5rem;">
                <i class="bi bi-trash3"></i> Hapus Permanen
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate storage bar
    setTimeout(() => {
        const bar = document.getElementById('storageBar');
        const bar2 = document.getElementById('storageBarSecondary');
        if (bar) bar.style.width = bar.dataset.target + '%';
        if (bar2) bar2.style.width = bar2.dataset.target + '%';
    }, 500);

    // Export
    document.getElementById('exportArsip')?.addEventListener('click', function() {
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengekspor...';
        this.disabled = true;
        setTimeout(() => {
            this.innerHTML = '<i class="bi bi-cloud-download"></i> Export Log';
            this.disabled = false;
            showAdminToast('Data arsip berhasil diekspor!', 'success');
        }, 2000);
    });

    // Start backup
    document.getElementById('startBackup')?.addEventListener('click', function() {
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Membackup...';
        this.disabled = true;
        setTimeout(() => {
            this.innerHTML = '<i class="bi bi-shield-check"></i> Mulai Backup';
            this.disabled = false;
            showAdminToast('Backup arsip data warga berhasil!', 'success');
        }, 3000);
    });

    // Filter
    document.getElementById('filterDuration')?.addEventListener('change', function() {
        const val = this.value;
        showAdminToast(val ? `Filter: > ${val} tahun` : 'Menampilkan semua data', 'info');
    });
});

function confirmDelete(name) {
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteModal').classList.remove('d-none');
}

function executeDelete() {
    const name = document.getElementById('deleteName').textContent;
    document.getElementById('deleteModal').classList.add('d-none');
    showAdminToast(`Data arsip ${name} berhasil dihapus`, 'success');
}
</script>
@endpush
