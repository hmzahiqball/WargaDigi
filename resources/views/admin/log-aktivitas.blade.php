@extends('layouts.global')

@section('title', 'Log Aktivitas')

@section('content')
<div class="admin-page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1>Log Aktivitas</h1>
        <p class="text-muted">Pantau rekam jejak aktivitas semua pengguna sistem berdasarkan peran dan kategori.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-danger px-3" style="border-radius: 0.5rem;" data-bs-toggle="modal" data-bs-target="#modalHapusLog">
            <i class="bi bi-trash"></i> Hapus Log
        </button>
        <button class="btn btn-success px-4" style="border-radius: 0.5rem;" id="exportLog">
            <i class="bi bi-download"></i> Ekspor Log
        </button>
    </div>
</div>

{{-- Filters --}}
<div class="admin-card mb-4 animate-in delay-1">
    <div class="admin-card-body">
        <form method="GET" action="{{ route('admin.log-aktivitas') }}" id="filterLogForm">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small fw-semibold text-muted">Cari Pengguna / Aktivitas</label>
                    <div class="admin-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="admin-input form-control" placeholder="Ketik kata kunci lalu tekan Enter..." id="searchLog" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-muted">Kategori Aktivitas</label>
                    <select class="form-select admin-input" id="filterCategory" name="category">
                        <option value="">Semua Kategori</option>
                        <option value="login" {{ request('category') === 'login' ? 'selected' : '' }}>Login / Logout</option>
                        <option value="pengaturan" {{ request('category') === 'pengaturan' ? 'selected' : '' }}>Perubahan Pengaturan</option>
                        <option value="master_rt" {{ request('category') === 'master_rt' ? 'selected' : '' }}>Manajemen RT</option>
                        <option value="pengguna" {{ request('category') === 'pengguna' ? 'selected' : '' }}>Manajemen Pengguna</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                    <input type="date" name="tgl_dari" class="form-control admin-input" value="{{ $filterDari ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                    <input type="date" name="tgl_sampai" class="form-control admin-input" value="{{ $filterSampai ?? '' }}">
                </div>
                <div class="col-lg-1 col-md-4 d-grid">
                    <button type="submit" class="btn btn-success"><i class="bi bi-funnel"></i></button>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="text-muted" style="font-size: 11px;"><i class="bi bi-info-circle me-1"></i>Secara bawaan menampilkan 7 hari terakhir. Atur rentang tanggal untuk melihat lebih lama.</span>
                @if(request('search') || request('category') || request('tgl_dari') || request('tgl_sampai'))
                    <a href="{{ route('admin.log-aktivitas') }}" class="small text-decoration-none text-danger" style="font-size: 11px;">
                        <i class="bi bi-x-circle"></i> Reset Filter
                    </a>
                @endif
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
{{-- ===================== MODAL: Hapus Log Aktivitas ===================== --}}
<div class="modal fade" id="modalHapusLog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0.75rem;">
            <form method="POST" action="{{ route('admin.log-aktivitas.hapus') }}" id="formHapusLog">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-trash me-2 text-danger"></i>Hapus Log Aktivitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger small" style="border-radius: 0.5rem;">
                            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                    <p class="text-muted small mb-3">Pilih rentang tanggal log yang ingin dihapus. Data log pada rentang tersebut akan dihapus permanen.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Dari Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="dari" class="form-control admin-input" value="{{ old('dari') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Sampai Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="sampai" class="form-control admin-input" value="{{ old('sampai') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnHapusLog"><i class="bi bi-trash me-1"></i>Hapus</button>
                </div>
            </form>
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

    // Hapus Log: validasi rentang, lalu minta konfirmasi sebelum submit.
    const formHapusLog = document.getElementById('formHapusLog');
    const btnHapusLog = document.getElementById('btnHapusLog');
    if (btnHapusLog && formHapusLog) {
        btnHapusLog.addEventListener('click', function() {
            const dari = formHapusLog.querySelector('[name="dari"]');
            const sampai = formHapusLog.querySelector('[name="sampai"]');

            if (!dari.value || !sampai.value) {
                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Lengkapi rentang tanggal terlebih dahulu.', 'error');
                } else {
                    window.tampilkanPemberitahuan('Lengkapi rentang tanggal terlebih dahulu.');
                }
                return;
            }
            if (dari.value > sampai.value) {
                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Tanggal akhir tidak boleh sebelum tanggal mulai.', 'error');
                } else {
                    window.tampilkanPemberitahuan('Tanggal akhir tidak boleh sebelum tanggal mulai.');
                }
                return;
            }

            // Tutup modal rentang lalu tampilkan konfirmasi hapus.
            const modalRentang = bootstrap.Modal.getInstance(document.getElementById('modalHapusLog'));
            if (modalRentang) modalRentang.hide();

            window.showDeleteModal({
                prefix: 'Hapus Log Aktivitas',
                itemName: 'rentang ' + dari.value + ' s.d. ' + sampai.value,
                subtitle: 'Data log pada rentang tanggal ini akan dihapus permanen. Lanjutkan?',
                confirmButtonText: 'Ya, Hapus',
                formId: 'formHapusLog',
            });
        });
    }

    // Buka kembali modal rentang bila ada error validasi server.
    @if($errors->any() && (old('dari') || old('sampai')))
        var mlog = document.getElementById('modalHapusLog');
        if (mlog) new bootstrap.Modal(mlog).show();
    @endif
});
</script>
@endpush
