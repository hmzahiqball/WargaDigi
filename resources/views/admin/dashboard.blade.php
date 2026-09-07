@extends('layouts.global')

@section('title', 'Dashboard')

@section('content')
<div class="admin-page-header">
    <h1>Gambaran RW 21 -Tanimulya</h1>
    <p class="text-muted">Rangkuman data warga dari seluruh RW aktif dan aplikasi.</p>
</div>

{{-- Stat Cards --}}
<div class="row g-4 mb-4">
    @foreach($stats as $index => $stat)
    <div class="col-md-4">
        <div class="admin-stat-card animate-in delay-{{ $index + 1 }}">
            <div class="stat-card-header">
                <span class="stat-card-label">{{ $stat['label'] }}</span>
                <div class="stat-card-icon" style="color: {{ $stat['color'] }};">
                    <i class="bi {{ $stat['icon'] }}"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <span class="stat-card-value" data-admin-count="{{ $stat['value'] }}">0</span>
                @if(isset($stat['total']))
                    <span class="stat-card-total">/ {{ number_format($stat['total']) }}</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Peringatan Terbaru --}}
<div class="admin-card mb-4 animate-in delay-2">
    <div class="admin-card-header-inline">
        <h5><i class="bi bi-bell-fill text-warning"></i> Peringatan Terbaru</h5>
    </div>
    <div class="admin-card-body">
        @foreach($alerts as $alert)
        <div class="admin-alert-item {{ $alert['type'] }}">
            <div class="alert-icon">
                @if($alert['type'] === 'success')
                    <i class="bi bi-check-circle-fill"></i>
                @elseif($alert['type'] === 'warning')
                    <i class="bi bi-exclamation-triangle-fill"></i>
                @else
                    <i class="bi bi-info-circle-fill"></i>
                @endif
            </div>
            <div class="alert-content">
                <h6>{{ $alert['title'] }}</h6>
                <p>{{ $alert['desc'] }}</p>
                <small class="text-muted">{{ $alert['time'] }}</small>
            </div>
        </div>
        @endforeach
        <div class="text-center mt-3">
            <button class="btn btn-outline-secondary btn-sm px-4" style="border-radius: 0.5rem;" id="viewAllLogs">Lihat Semua log</button>
        </div>
    </div>
</div>

{{-- Instance Log Table --}}
<div class="admin-card animate-in delay-3">
    <div class="admin-card-header-inline">
        <h5>Lihat semua log</h5>
        <a href="/admin/log-aktivitas" class="link-green">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>NAMA INSTANCE</th>
                        <th>DOMAIN</th>
                        <th>STATUS</th>
                        <th>USER AKTIF</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instances as $instance)
                    <tr>
                        <td class="fw-semibold">{{ $instance['name'] }}</td>
                        <td class="text-muted">{{ $instance['domain'] }}</td>
                        <td>
                            <span class="admin-badge success">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> {{ $instance['status'] }}
                            </span>
                        </td>
                        <td class="text-center">{{ number_format($instance['users']) }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="admin-action-btn" title="Pengaturan" onclick="showAdminToast('Pengaturan instance dibuka', 'info')">
                                    <i class="bi bi-gear"></i>
                                </button>
                                <button class="admin-action-btn" title="Statistik" onclick="showAdminToast('Statistik sedang dimuat...', 'info')">
                                    <i class="bi bi-bar-chart-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animated count-up for stat cards
    document.querySelectorAll('[data-admin-count]').forEach(el => {
        const target = parseInt(el.dataset.adminCount);
        const duration = 1500;
        const start = performance.now();

        function update(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(easeOut * target).toLocaleString('id-ID');
            if (progress < 1) requestAnimationFrame(update);
        }

        requestAnimationFrame(update);
    });

    // View all logs button
    document.getElementById('viewAllLogs')?.addEventListener('click', function() {
        window.location.href = '/admin/log-aktivitas';
    });
});
</script>
@endpush
