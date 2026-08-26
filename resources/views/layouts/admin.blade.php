<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaDigi 21 — Panel Administrator')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin WargaDigi</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-body">
    {{-- Sidebar --}}
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-globe2"></i>
                </div>
                <div class="sidebar-brand-text">
                    <span class="brand-name">WargaDigi</span>
                    <span class="brand-role">Administrator</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="/admin/dashboard" class="sidebar-link @if(request()->is('admin/dashboard')) active @endif">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/admin/pengaturan-sistem" class="sidebar-link @if(request()->is('admin/pengaturan-sistem')) active @endif">
                        <i class="bi bi-gear-fill"></i>
                        <span>Pengaturan Sistem</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/admin/manajemen-hak-akses" class="sidebar-link @if(request()->is('admin/manajemen-hak-akses')) active @endif">
                        <i class="bi bi-people-fill"></i>
                        <span>Manajemen Hak Akses</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/admin/log-aktivitas" class="sidebar-link @if(request()->is('admin/log-aktivitas')) active @endif">
                        <i class="bi bi-list-task"></i>
                        <span>Log Aktivitas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/admin/arsip-data-warga" class="sidebar-link @if(request()->is('admin/arsip-data-warga')) active @endif">
                        <i class="bi bi-archive-fill"></i>
                        <span>Arsip Data Warga</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/admin/manajemen-data" class="sidebar-link @if(request()->is('admin/manajemen-data')) active @endif">
                        <i class="bi bi-database-fill-gear"></i>
                        <span>Manajemen Data</span>
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
                    <a href="#" class="sidebar-link" id="adminLogout">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Keluar</span>
                    </a>
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
                    <input type="text" placeholder="Search settings..." id="globalSearch">
                </div>
            </div>
            <div class="topbar-right">
                <button class="topbar-icon" id="notifBtn">
                    <i class="bi bi-bell"></i>
                    <span class="topbar-badge">3</span>
                </button>
                <button class="topbar-icon" id="settingsBtn">
                    <i class="bi bi-gear"></i>
                </button>
                <div class="topbar-avatar" id="avatarDropdown">
                    <div class="avatar-circle">
                        <span>A</span>
                    </div>
                </div>
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
            <h6><i class="bi bi-bell-fill"></i> Notifikasi</h6>
            <button class="notif-close" id="notifClose"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="notif-panel-body">
            <div class="notif-item unread">
                <div class="notif-icon success"><i class="bi bi-check-circle-fill"></i></div>
                <div class="notif-content">
                    <p class="notif-text">Backup data berhasil diselesaikan</p>
                    <span class="notif-time">5 menit yang lalu</span>
                </div>
            </div>
            <div class="notif-item unread">
                <div class="notif-icon warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="notif-content">
                    <p class="notif-text">Storage mencapai 80% kapasitas</p>
                    <span class="notif-time">1 jam yang lalu</span>
                </div>
            </div>
            <div class="notif-item">
                <div class="notif-icon info"><i class="bi bi-info-circle-fill"></i></div>
                <div class="notif-content">
                    <p class="notif-text">Pengguna baru terdaftar: Ahmad S.</p>
                    <span class="notif-time">2 jam yang lalu</span>
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

        // Logout
        document.getElementById('adminLogout')?.addEventListener('click', function(e) {
            e.preventDefault();
            localStorage.removeItem('wargadigi_user');
            window.location.href = '/login';
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

            // Animate in
            requestAnimationFrame(() => toast.classList.add('show'));

            // Close button
            toast.querySelector('.toast-close').addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            });

            // Auto dismiss
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        // Responsive: close sidebar on mobile when clicking content
        if (window.innerWidth < 992) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
    });
    </script>
</body>
</html>
