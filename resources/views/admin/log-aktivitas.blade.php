@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
<div class="admin-page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1>Log Aktivitas</h1>
        <p class="text-muted">Pantau rekam jejak aktivitas semua pengguna sistem berdasarkan peran dan kategori.</p>
    </div>
    <button class="btn btn-success px-4" style="border-radius: 0.5rem;" id="exportLog">
        <i class="bi bi-download"></i> Ekspor Log
    </button>
</div>

{{-- Filters --}}
<div class="admin-card mb-4 animate-in delay-1">
    <div class="admin-card-body">
        <div class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label small fw-semibold text-muted">Cari Pengguna / Aktivitas</label>
                <div class="admin-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="admin-input form-control" placeholder="Masukkan nama atau ID..." id="searchLog">
                </div>
            </div>
            <div class="col-lg-3">
                <label class="form-label small fw-semibold text-muted">Rentang Tanggal</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 0.5rem 0 0 0.5rem;"><i class="bi bi-calendar3 text-muted"></i></span>
                    <input type="text" class="form-control admin-input border-start-0" value="01 Agu - 26 Agu 2026" id="dateRange" style="border-radius: 0 0.5rem 0.5rem 0;" readonly>
                </div>
            </div>
            <div class="col-lg-2">
                <label class="form-label small fw-semibold text-muted">Peran Pengguna</label>
                <select class="form-select admin-input" id="filterRole">
                    <option value="">Semua Peran</option>
                    <option value="RW Admin">RW Admin</option>
                    <option value="Opr Keuangan">Opr Keuangan</option>
                    <option value="RT Leader">RT Leader</option>
                    <option value="Opr Konten">Opr Konten</option>
                </select>
            </div>
            <div class="col-lg-3">
                <label class="form-label small fw-semibold text-muted">Kategori Aktivitas</label>
                <select class="form-select admin-input" id="filterCategory">
                    <option value="">Semua Kategori</option>
                    <option value="data">Membuat Data Baru</option>
                    <option value="transaksi">Memasukkan data transaksi</option>
                    <option value="persetujuan">Mengubah status persetujuan</option>
                    <option value="publikasi">Membuat Data Publikasi</option>
                    <option value="login">Login / Logout</option>
                    <option value="settings">Perubahan Pengaturan</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Activity Table --}}
<div class="admin-card animate-in delay-2">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table log-table mb-0" id="logTable">
                <thead>
                    <tr>
                        <th style="width: 140px;">Waktu</th>
                        <th style="width: 200px;">Pengguna & Peran</th>
                        <th style="width: 160px;">Jenis Aktivitas</th>
                        <th>Deskripsi</th>
                        <th style="width: 90px;" class="text-end">Status</th>
                    </tr>
                </thead>
                <tbody id="logTableBody">
                    @foreach($logs as $log)
                    <tr data-role="{{ $log['role'] }}" data-category="{{ $log['category'] }}">
                        <td>
                            <div class="log-time">
                                <span class="log-date fw-bold">{{ $log['date'] }}</span>
                                <span class="log-clock text-muted">{{ $log['time'] }} WIB</span>
                            </div>
                        </td>
                        <td>
                            <div class="log-user">
                                <div class="log-avatar" style="background: {{ $log['avatar_bg'] }};">{{ $log['initials'] }}</div>
                                <div class="log-user-info">
                                    <span class="log-user-name">{{ $log['name'] }}</span>
                                    <span class="log-user-role" style="background: {{ $log['role_bg'] }}; color: {{ $log['role_color'] }};">{{ $log['role'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="log-activity-type">
                                <i class="bi {{ $log['activity_icon'] }} text-muted"></i>
                                <span>{{ $log['activity_type'] }}</span>
                            </div>
                        </td>
                        <td class="log-desc-cell">{{ $log['description'] }}</td>
                        <td class="text-end">
                            <span class="log-status-badge {{ $log['status_class'] }}">{{ $log['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small" id="paginationInfo">Menampilkan 1-{{ count($logs) }} dari {{ $totalLogs }} aktivitas</span>
            <div class="admin-pagination" id="pagination">
                <button class="page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn" data-page="2">2</button>
                <button class="page-btn" data-page="3">3</button>
                <button class="page-btn dots" disabled>...</button>
                <button class="page-btn" data-page="48">48</button>
                <button class="page-btn" data-page="2"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchLog');
    const filterRole = document.getElementById('filterRole');
    const filterCategory = document.getElementById('filterCategory');
    const rows = document.querySelectorAll('#logTableBody tr');

    function applyFilters() {
        const query = searchInput.value.toLowerCase();
        const role = filterRole.value;
        const category = filterCategory.value;
        let visible = 0;

        rows.forEach(row => {
            const name = row.querySelector('.log-user-name')?.textContent.toLowerCase() || '';
            const desc = row.querySelector('.log-desc-cell')?.textContent.toLowerCase() || '';
            const rowRole = row.dataset.role || '';
            const rowCat = row.dataset.category || '';

            const matchSearch = !query || name.includes(query) || desc.includes(query);
            const matchRole = !role || rowRole === role;
            const matchCategory = !category || rowCat === category;

            const show = matchSearch && matchRole && matchCategory;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('paginationInfo').textContent =
            `Menampilkan 1-${visible} dari ${visible} aktivitas`;
    }

    searchInput?.addEventListener('input', applyFilters);
    filterRole?.addEventListener('change', applyFilters);
    filterCategory?.addEventListener('change', applyFilters);

    // Pagination buttons
    document.querySelectorAll('.admin-pagination .page-btn[data-page]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.admin-pagination .page-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            showAdminToast(`Halaman ${this.dataset.page} dimuat`, 'info');
        });
    });

    // Export
    document.getElementById('exportLog')?.addEventListener('click', function() {
        const btn = this;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengekspor...';
        btn.disabled = true;
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-download"></i> Ekspor Log';
            btn.disabled = false;
            showAdminToast('Log aktivitas berhasil diekspor sebagai CSV!', 'success');
        }, 2000);
    });
});
</script>
@endpush
