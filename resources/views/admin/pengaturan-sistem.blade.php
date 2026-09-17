@extends('layouts.global')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="admin-page-header">
    <h1>Pengaturan Sistem</h1>
    <p class="text-muted">Konfigurasi instance, notifikasi, keamanan, dan mode pemeliharaan.</p>
</div>

@if (session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="border-radius: 0.5rem;">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius: 0.5rem;">
        <i class="bi bi-exclamation-triangle-fill"></i> Terjadi kesalahan:
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="settingsForm" method="POST" action="{{ route('admin.pengaturan-sistem.update') }}" enctype="multipart/form-data">
    @csrf

{{-- Informasi Instance --}}
<div class="admin-card mb-4 animate-in delay-1">
    <div class="admin-card-header-inline">
        <h5><i class="bi bi-building text-success"></i> Informasi Instance</h5>
    </div>
    <div class="admin-card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nama Instance</label>
                <input type="text" name="instance_name" class="form-control admin-input" value="{{ old('instance_name', $settings['instance_name']) }}" id="instanceName" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Domain</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-globe2 text-success"></i></span>
                    <input type="text" name="domain" class="form-control admin-input" value="{{ old('domain', $settings['domain']) }}" id="instanceDomain">
                </div>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Deskripsi</label>
                <textarea class="form-control admin-input" name="description" rows="3" id="instanceDesc">{{ old('description', $settings['description']) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Logo Instance</label>
                @if (!empty($settings['logo']))
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo" style="height: 48px; border-radius: 0.4rem; border: 1px solid #e0e0e0; padding: 4px; background:#fff;">
                    </div>
                @endif
                <input type="file" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp" class="form-control admin-input">
                <small class="text-muted">Format PNG/JPG/SVG/WEBP, maksimal 2 MB.</small>
            </div>
        </div>
    </div>
</div>

{{-- Pengaturan Notifikasi --}}
<div class="admin-card mb-4 animate-in delay-2">
    <div class="admin-card-header-inline">
        <h5><i class="bi bi-bell text-success"></i> Pengaturan Notifikasi</h5>
    </div>
    <div class="admin-card-body">
        <div class="settings-list">
            <div class="setting-item">
                <div class="setting-info">
                    <div class="setting-icon whatsapp"><i class="bi bi-whatsapp"></i></div>
                    <div>
                        <h6>Notifikasi WhatsApp</h6>
                        <p class="text-muted small mb-0">Kirim pemberitahuan penting ke grup WhatsApp warga</p>
                    </div>
                </div>
                <label class="admin-toggle">
                    <input type="checkbox" name="notif_whatsapp" value="1" {{ $settings['notif_whatsapp'] ? 'checked' : '' }} data-setting="notif_whatsapp">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="setting-item">
                <div class="setting-info">
                    <div class="setting-icon email"><i class="bi bi-envelope"></i></div>
                    <div>
                        <h6>Notifikasi Email</h6>
                        <p class="text-muted small mb-0">Kirim laporan dan notifikasi melalui email</p>
                    </div>
                </div>
                <label class="admin-toggle">
                    <input type="checkbox" name="notif_email" value="1" {{ $settings['notif_email'] ? 'checked' : '' }} data-setting="notif_email">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="setting-item">
                <div class="setting-info">
                    <div class="setting-icon push"><i class="bi bi-app-indicator"></i></div>
                    <div>
                        <h6>Push Notification</h6>
                        <p class="text-muted small mb-0">Notifikasi browser untuk pengurus yang aktif</p>
                    </div>
                </div>
                <label class="admin-toggle">
                    <input type="checkbox" name="notif_push" value="1" {{ $settings['notif_push'] ? 'checked' : '' }} data-setting="notif_push">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>

{{-- Keamanan --}}
<div class="admin-card mb-4 animate-in delay-3">
    <div class="admin-card-header-inline">
        <h5><i class="bi bi-shield-lock text-success"></i> Keamanan</h5>
    </div>
    <div class="admin-card-body">
        <div class="settings-list">
            <div class="setting-item">
                <div class="setting-info">
                    <div class="setting-icon security"><i class="bi bi-phone"></i></div>
                    <div>
                        <h6>Verifikasi 2 Langkah (2FA)</h6>
                        <p class="text-muted small mb-0">Tambahkan lapisan keamanan ekstra dengan OTP via WhatsApp</p>
                    </div>
                </div>
                <label class="admin-toggle">
                    <input type="checkbox" name="two_factor" value="1" {{ $settings['two_factor'] ? 'checked' : '' }} data-setting="two_factor">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="setting-item">
                <div class="setting-info">
                    <div class="setting-icon security"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <h6>Session Timeout</h6>
                        <p class="text-muted small mb-0">Otomatis logout setelah tidak aktif</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select admin-input" style="width: auto;" id="sessionTimeout" name="session_timeout">
                        <option value="15" {{ $settings['session_timeout'] == 15 ? 'selected' : '' }}>15 menit</option>
                        <option value="30" {{ $settings['session_timeout'] == 30 ? 'selected' : '' }}>30 menit</option>
                        <option value="60" {{ $settings['session_timeout'] == 60 ? 'selected' : '' }}>1 jam</option>
                        <option value="120" {{ $settings['session_timeout'] == 120 ? 'selected' : '' }}>2 jam</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mode Pemeliharaan --}}
<div class="admin-card mb-4 animate-in delay-4">
    <div class="admin-card-header-inline">
        <h5><i class="bi bi-tools text-warning"></i> Mode Pemeliharaan</h5>
    </div>
    <div class="admin-card-body">
        <div class="setting-item">
            <div class="setting-info">
                <div class="setting-icon maintenance"><i class="bi bi-cone-striped"></i></div>
                <div>
                    <h6>Aktifkan Mode Pemeliharaan</h6>
                    <p class="text-muted small mb-0">Semua warga akan melihat halaman pemeliharaan. Hanya admin yang bisa mengakses sistem.</p>
                </div>
            </div>
            <label class="admin-toggle">
                <input type="checkbox" name="maintenance_mode" value="1" {{ $settings['maintenance_mode'] ? 'checked' : '' }} data-setting="maintenance_mode" id="maintenanceToggle">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="maintenance-warning d-none mt-3" id="maintenanceWarning">
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-0" style="border-radius: 0.5rem;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>Perhatian!</strong> Mode pemeliharaan aktif. Warga tidak dapat mengakses situs.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Simpan Semua --}}
<div class="d-flex justify-content-end mb-5">
    <button type="submit" class="btn btn-success px-5 py-2" style="border-radius: 0.5rem;">
        <i class="bi bi-check-lg"></i> Simpan Semua Perubahan
    </button>
</div>

</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle switch interactions
    document.querySelectorAll('.admin-toggle input').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const setting = this.dataset.setting;
            const state = this.checked ? 'diaktifkan' : 'dinonaktifkan';
            const names = {
                'notif_whatsapp': 'Notifikasi WhatsApp',
                'notif_email': 'Notifikasi Email',
                'notif_push': 'Push Notification',
                'two_factor': 'Verifikasi 2 Langkah',
                'maintenance_mode': 'Mode Pemeliharaan'
            };
            showAdminToast(`${names[setting] || setting} ${state}`, this.checked ? 'success' : 'warning');
        });
    });

    // Maintenance mode warning
    const maintenanceToggle = document.getElementById('maintenanceToggle');
    const maintenanceWarning = document.getElementById('maintenanceWarning');

    function updateMaintenanceWarning() {
        if (maintenanceToggle.checked) {
            maintenanceWarning.classList.remove('d-none');
        } else {
            maintenanceWarning.classList.add('d-none');
        }
    }

    maintenanceToggle?.addEventListener('change', updateMaintenanceWarning);
    updateMaintenanceWarning();

    // Session timeout change
    document.getElementById('sessionTimeout')?.addEventListener('change', function() {
        showAdminToast(`Session timeout diubah ke ${this.options[this.selectedIndex].text}`, 'info');
    });
});
</script>
@endpush
