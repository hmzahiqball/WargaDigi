@extends('layouts.global')

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
        <form method="GET" action="{{ route('admin.log-aktivitas') }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label class="form-label small fw-semibold text-muted">Cari Pengguna / Aktivitas</label>
                    <div class="admin-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="admin-input form-control" placeholder="Masukkan nama atau deskripsi..." id="searchLog" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-4">
                    <label class="form-label small fw-semibold text-muted">Kategori Aktivitas</label>
                    <select class="form-select admin-input" id="filterCategory" name="category">
                        <option value="">Semua Kategori</option>
                        <option value="login" {{ request('category') === 'login' ? 'selected' : '' }}>Login / Logout</option>
                        <option value="pengaturan" {{ request('category') === 'pengaturan' ? 'selected' : '' }}>Perubahan Pengaturan</option>
                        <option value="master_rt" {{ request('category') === 'master_rt' ? 'selected' : '' }}>Manajemen RT</option>
                        <option value="pengguna" {{ request('category') === 'pengguna' ? 'selected' : '' }}>Manajemen Pengguna</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-success w-100" style="border-radius: 0.5rem;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </div>
        </form>
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
                    @forelse($logs as $log)
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
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada aktivitas yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-footer">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small" id="paginationInfo">
                @if(isset($paginator) && $paginator->total() > 0)
                    Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }} aktivitas
                @else
                    Menampilkan 0 dari {{ $totalLogs }} aktivitas
                @endif
            </span>
            @if(isset($paginator))
                <div>{{ $paginator->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Export (client-side notice; filtering & pagination handled server-side).
    document.getElementById('exportLog')?.addEventListener('click', function() {
        if (typeof showAdminToast === 'function') {
            showAdminToast('Fitur ekspor log akan segera tersedia.', 'info');
        }
    });
});
</script>
@endpush
