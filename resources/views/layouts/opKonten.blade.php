<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaDigi 21 — Dashboard Operator Konten')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Operator Konten WargaDigi</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-body">
    {{-- Sidebar --}}
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand mb-3">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div class="sidebar-brand-text">
                    <span class="brand-name">WargaDigi</span>
                    <span class="brand-role">RW 12 TANIMULYA</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="{{ route('opkonten.dashboard') }}" class="sidebar-link @if(request()->routeIs('opkonten.dashboard') || request()->is('op-konten*') || request()->is('opkonten*')) active @endif">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('opkonten/berita*')) active @endif">
                        <i class="bi bi-newspaper"></i>
                        <span>Berita</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('opkonten/agenda*')) active @endif">
                        <i class="bi bi-calendar-event"></i>
                        <span>Agenda</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('opkonten/pengumuman*')) active @endif">
                        <i class="bi bi-megaphone"></i>
                        <span>Pengumuman</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('opkonten/galeri*')) active @endif">
                        <i class="bi bi-images"></i>
                        <span>Galeri Dokumentasi</span>
                    </a>
                </li>
            </ul>

            <ul class="sidebar-menu sidebar-menu-bottom">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link" onclick="event.preventDefault();">
                        <i class="bi bi-question-circle"></i>
                        <span>Bantuan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <form action="{{ route('logout') }}" method="POST" id="logout-form-op" class="w-100">
                        @csrf
                        <a href="#" class="sidebar-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form-op').submit();">
                            <i class="bi bi-box-arrow-left"></i>
                            <span>Keluar</span>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    {{-- Main Wrapper --}}
    <div class="admin-main" id="adminMain">
        {{-- Top Bar --}}
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari di WargaDigi..." id="globalSearch">
                </div>
            </div>
            <div class="topbar-right">
                <button class="topbar-icon" id="notifBtn">
                    <i class="bi bi-bell"></i>
                    <span class="topbar-badge">.</span>
                </button>
                <a href="#" class="topbar-icon text-decoration-none text-success" id="settingsBtn">
                    <i class="bi bi-gear"></i>
                </a>
                <div class="topbar-avatar" id="avatarDropdown">
                    <div class="avatar-circle">
                        <span>{{ substr(Auth::user()->name ?? 'O', 0, 1) }}</span>
                    </div>
                </div>
                <span class="fw-bold text-dark small ms-1">{{ Auth::user()->name ?? 'OP konten' }}</span>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="admin-content">
            @yield('content')
        </main>
    </div>

    {{-- Toast Container --}}
    <div class="admin-toast-container" id="toastContainer"></div>

    {{-- Notification Panel --}}
    <div class="admin-notif-panel" id="notifPanel">
        <div class="notif-panel-header">
            <h6><i class="bi bi-bell-fill"></i> Notifikasi Operator Konten</h6>
            <button class="notif-close" id="notifClose"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="notif-panel-body">
            <div class="notif-item unread">
                <div class="notif-icon warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="notif-content">
                    <p class="notif-text">24 konten artikel memerlukan tinjauan</p>
                    <span class="notif-time">1 jam yang lalu</span>
                </div>
            </div>
            <div class="notif-item unread">
                <div class="notif-icon success"><i class="bi bi-check-circle-fill"></i></div>
                <div class="notif-content">
                    <p class="notif-text">Agenda baru telah disetujui</p>
                    <span class="notif-time">3 jam yang lalu</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Page Scripts --}}
    @stack('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.getElementById('adminMain');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle?.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Notification panel
        const notifBtn = document.getElementById('notifBtn');
        const notifPanel = document.getElementById('notifPanel');
        const notifClose = document.getElementById('notifClose');

        notifBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            notifPanel.classList.toggle('open');
        });

        notifClose?.addEventListener('click', function() {
            notifPanel.classList.remove('open');
        });

        document.addEventListener('click', function(e) {
            if (!notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
                notifPanel.classList.remove('open');
            }
        });

        // Toast function (global)
        window.showAdminToast = function(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };

            const toast = document.createElement('div');
            toast.className = `admin-toast ${type}`;
            toast.innerHTML = `
                <i class="bi ${icons[type] || icons.info}"></i>
                <span>${message}</span>
                <button class="toast-close"><i class="bi bi-x"></i></button>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('show'));

            toast.querySelector('.toast-close').addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            });

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        // Responsive: close sidebar on mobile when clicking content
        if (window.innerWidth < 992) {
            sidebar?.classList.add('collapsed');
            mainContent?.classList.add('expanded');
        }
    });
    </script>
</body>
</html>
