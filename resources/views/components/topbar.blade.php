@php
    $userRole = Auth::user()->role ?? 'default';
    $config = config("topbar.{$userRole}") ?? config('topbar.default');

    if ($userRole === 'Warga') {
        $userName = Auth::user()->username ?? Auth::user()->name ?? $config['default_name'] ?? 'Warga';
    } else {
        $userName = $config['default_name'] ?? Auth::user()->username ?? Auth::user()->name ?? 'Pengguna';
    }

    $userInitial = strtoupper(substr($userName, 0, 1));
    $searchPlaceholder = $config['search_placeholder'] ?? 'Cari di WargaDigi...';

    $settingsUrl = isset($config['settings_route']) && Route::has($config['settings_route'])
        ? route($config['settings_route'])
        : ($config['settings_url'] ?? '#');

    $notifications = $config['notifications'] ?? [];
    $notifTitle = $config['notif_title'] ?? 'Notifikasi';
    $notifBadge = $config['notif_badge'] ?? count($notifications);
@endphp

<header class="admin-topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="{{ $searchPlaceholder }}" id="globalSearch">
        </div>
    </div>
    <div class="topbar-right">
        <button class="topbar-icon" id="notifBtn" type="button" aria-label="Notifikasi">
            <i class="bi bi-bell"></i>
            @if($notifBadge)
                <span class="topbar-badge">{{ $notifBadge }}</span>
            @endif
        </button>
        <a href="{{ $settingsUrl }}" class="topbar-icon text-decoration-none text-dark" id="settingsBtn" aria-label="Pengaturan">
            <i class="bi bi-gear"></i>
        </a>
        <div class="topbar-avatar" id="avatarDropdown">
            <div class="avatar-circle">
                <span>{{ $userInitial }}</span>
            </div>
        </div>
        <span class="fw-bold text-dark small ms-1">{{ $userName }}</span>
    </div>
</header>

{{-- Dynamic Notification Panel --}}
<div class="admin-notif-panel" id="notifPanel">
    <div class="notif-panel-header">
        <h6><i class="bi bi-bell-fill"></i> {{ $notifTitle }}</h6>
        <button class="notif-close" id="notifClose" type="button"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="notif-panel-body">
        @forelse($notifications as $notif)
            <div class="notif-item {{ !empty($notif['unread']) ? 'unread' : '' }}">
                <div class="notif-icon {{ $notif['type'] ?? 'info' }}">
                    <i class="bi {{ $notif['icon'] ?? 'bi-info-circle-fill' }}"></i>
                </div>
                <div class="notif-content">
                    <p class="notif-text">{{ $notif['text'] }}</p>
                    <span class="notif-time">{{ $notif['time'] }}</span>
                </div>
            </div>
        @empty
            <div class="p-3 text-center text-muted small">
                <i class="bi bi-bell-slash d-block fs-4 mb-1"></i>
                Tidak ada notifikasi baru
            </div>
        @endforelse
    </div>
</div>
