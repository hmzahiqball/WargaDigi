@extends('layouts.admin')

@section('title', 'Manajemen Data')

@section('content')
<div class="admin-page-header">
    <h1>Manajemen Data</h1>
    <p class="text-muted">Kelola arsip data warga, backup sistem berkala, dan pemulihan data dari penampungan.</p>
</div>

{{-- System Health Status --}}
<div class="admin-card mb-4 animate-in delay-1">
    <div class="admin-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="health-icon {{ $systemHealth['status'] }}">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Sistem Sehat</h5>
                    <p class="text-muted small mb-0">Semua layanan berjalan normal.</p>
                </div>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Terakhir Backup:</small>
                <span class="fw-semibold">{{ $systemHealth['last_backup'] }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Backup & Restore Row --}}
<div class="row g-4 mb-4">
    {{-- Backup Data Berkala --}}
    <div class="col-lg-7">
        <div class="admin-card h-100 animate-in delay-2">
            <div class="admin-card-header-inline">
                <h5><i class="bi bi-cloud-arrow-up text-success"></i> Backup Data Berkala</h5>
            </div>
            <div class="admin-card-body">
                <p class="text-muted small mb-4">Amankan seluruh data warga, arsip, dan laporan keuangan ke server cadangan.</p>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="data-info-card">
                            <small class="text-muted">Total Ukuran Data</small>
                            <h4 class="fw-bold mb-0">{{ $backup['total_size'] }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="data-info-card">
                            <small class="text-muted">Jadwal Otomatis</small>
                            <h4 class="fw-bold mb-0">{{ $backup['schedule'] }}</h4>
                        </div>
                    </div>
                </div>

                {{-- Backup Progress (hidden by default) --}}
                <div class="backup-progress d-none mb-4" id="backupProgress">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-semibold">Backup sedang berjalan...</span>
                        <span class="small text-success fw-bold" id="backupPercent">0%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="backupBar" style="width: 0%;"></div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted" id="backupStatus">Mempersiapkan data...</small>
                    </div>
                </div>

                <div class="d-flex gap-3" id="backupButtons">
                    <button class="btn btn-outline-success px-4" style="border-radius: 0.5rem;" id="scheduleBtn">
                        Pengaturan Jadwal
                    </button>
                    <button class="btn btn-success px-4" style="border-radius: 0.5rem;" id="runBackup">
                        <i class="bi bi-cloud-arrow-up"></i> Jalankan Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Restore Data --}}
    <div class="col-lg-5">
        <div class="admin-card h-100 animate-in delay-3">
            <div class="admin-card-header-inline">
                <h5><i class="bi bi-arrow-counterclockwise text-warning"></i> Restore Data</h5>
            </div>
            <div class="admin-card-body">
                <p class="text-muted small mb-3">Pulihkan data sistem dari titik penyimpanan terakhir di penampungan.</p>

                <div class="restore-warning mb-4">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-exclamation-triangle-fill text-warning mt-1"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Perhatian</h6>
                            <p class="text-muted small mb-0">Proses restore akan menimpa data saat ini. Pastikan Anda memilih titik pemulihan yang tepat.</p>
                        </div>
                    </div>
                </div>

                <button class="btn btn-outline-secondary w-100" style="border-radius: 0.5rem;" id="restoreBtn">
                    <i class="bi bi-arrow-counterclockwise"></i> Pilih Data Restore
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Archive Data Table --}}
<div class="admin-card animate-in delay-4">
    <div class="admin-card-header-inline">
        <h5><i class="bi bi-database text-success"></i> Manajemen Arsip Data Warga</h5>
        <a href="/admin/arsip-data-warga" class="btn btn-success btn-sm px-3" style="border-radius: 0.5rem;">
            Buka Menu Arsip <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="admin-card-body">
        <p class="text-muted small mb-3">Kelola data warga yang telah meninggal atau pindah (>10 Tahun).</p>

        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>KATEGORI ARSIP</th>
                        <th>JUMLAH DATA</th>
                        <th>TINDAKAN DISARANKAN</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($archiveData as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item['category'] }}</td>
                        <td>{{ $item['count'] }} Data</td>
                        <td>
                            <span style="color: {{ $item['action_color'] }}; font-weight: 600;">{{ $item['action'] }}</span>
                        </td>
                        <td>
                            <span class="admin-badge" style="background-color: {{ $item['status_color'] }}15; color: {{ $item['status_color'] }}; border: 1px solid {{ $item['status_color'] }}30;">
                                {{ $item['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Schedule Modal --}}
<div class="admin-modal-overlay d-none" id="scheduleModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h5><i class="bi bi-calendar-check text-success"></i> Pengaturan Jadwal Backup</h5>
            <button class="admin-modal-close" onclick="document.getElementById('scheduleModal').classList.add('d-none')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="admin-modal-body">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Frekuensi</label>
                <select class="form-select admin-input" id="scheduleFreq">
                    <option>Setiap Hari</option>
                    <option selected>Setiap Minggu</option>
                    <option>Setiap Bulan</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Waktu</label>
                <input type="time" class="form-control admin-input" value="00:00" id="scheduleTime">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Retensi Backup</label>
                <select class="form-select admin-input" id="scheduleRetention">
                    <option>7 hari terakhir</option>
                    <option selected>30 hari terakhir</option>
                    <option>90 hari terakhir</option>
                </select>
            </div>
        </div>
        <div class="admin-modal-footer">
            <button class="btn btn-outline-secondary" onclick="document.getElementById('scheduleModal').classList.add('d-none')" style="border-radius: 0.5rem;">Batal</button>
            <button class="btn btn-success" style="border-radius: 0.5rem;" onclick="document.getElementById('scheduleModal').classList.add('d-none'); showAdminToast('Jadwal backup berhasil diperbarui!', 'success');">
                <i class="bi bi-check-lg"></i> Simpan Jadwal
            </button>
        </div>
    </div>
</div>

{{-- Restore Modal --}}
<div class="admin-modal-overlay d-none" id="restoreModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h5><i class="bi bi-arrow-counterclockwise text-warning"></i> Pilih Titik Pemulihan</h5>
            <button class="admin-modal-close" onclick="document.getElementById('restoreModal').classList.add('d-none')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="admin-modal-body">
            <div class="restore-point-list">
                <label class="restore-point">
                    <input type="radio" name="restorePoint" value="1" checked>
                    <div class="restore-point-info">
                        <span class="fw-semibold">Hari ini, 08:30 WIB</span>
                        <small class="text-muted d-block">Ukuran: 1.2 GB — Otomatis</small>
                    </div>
                </label>
                <label class="restore-point">
                    <input type="radio" name="restorePoint" value="2">
                    <div class="restore-point-info">
                        <span class="fw-semibold">Kemarin, 08:30 WIB</span>
                        <small class="text-muted d-block">Ukuran: 1.1 GB — Otomatis</small>
                    </div>
                </label>
                <label class="restore-point">
                    <input type="radio" name="restorePoint" value="3">
                    <div class="restore-point-info">
                        <span class="fw-semibold">19 Agu 2026, 00:00 WIB</span>
                        <small class="text-muted d-block">Ukuran: 1.0 GB — Mingguan</small>
                    </div>
                </label>
            </div>
        </div>
        <div class="admin-modal-footer">
            <button class="btn btn-outline-secondary" onclick="document.getElementById('restoreModal').classList.add('d-none')" style="border-radius: 0.5rem;">Batal</button>
            <button class="btn btn-warning" style="border-radius: 0.5rem;" onclick="document.getElementById('restoreModal').classList.add('d-none'); showAdminToast('Data berhasil dipulihkan dari titik pemulihan!', 'success');">
                <i class="bi bi-arrow-counterclockwise"></i> Mulai Restore
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Schedule modal
    document.getElementById('scheduleBtn')?.addEventListener('click', function() {
        document.getElementById('scheduleModal').classList.remove('d-none');
    });

    // Restore modal
    document.getElementById('restoreBtn')?.addEventListener('click', function() {
        document.getElementById('restoreModal').classList.remove('d-none');
    });

    // Run backup simulation
    document.getElementById('runBackup')?.addEventListener('click', function() {
        const progressEl = document.getElementById('backupProgress');
        const buttonsEl = document.getElementById('backupButtons');
        const bar = document.getElementById('backupBar');
        const percent = document.getElementById('backupPercent');
        const status = document.getElementById('backupStatus');

        progressEl.classList.remove('d-none');
        buttonsEl.classList.add('d-none');

        let progress = 0;
        const stages = [
            { at: 15, text: 'Mengumpulkan data warga...' },
            { at: 35, text: 'Mengompres arsip keuangan...' },
            { at: 55, text: 'Menyinkronkan ke server cadangan...' },
            { at: 75, text: 'Memverifikasi integritas data...' },
            { at: 90, text: 'Menyelesaikan backup...' },
        ];

        const interval = setInterval(() => {
            progress += Math.random() * 5 + 1;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                bar.style.width = '100%';
                percent.textContent = '100%';
                status.textContent = 'Backup selesai!';

                setTimeout(() => {
                    progressEl.classList.add('d-none');
                    buttonsEl.classList.remove('d-none');
                    bar.style.width = '0%';
                    showAdminToast('Backup data berhasil diselesaikan! (1.2 GB)', 'success');
                }, 1500);
                return;
            }

            bar.style.width = progress + '%';
            percent.textContent = Math.round(progress) + '%';

            const stage = stages.find(s => progress >= s.at && progress < s.at + 5);
            if (stage) status.textContent = stage.text;
        }, 300);
    });
});
</script>
@endpush
