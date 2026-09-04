<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaDigi 21 — Dashboard Admin RW')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin RW WargaDigi</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Sidebar Active State Customization */
        .sidebar-link.active {
            background-color: rgba(46, 125, 50, 0.1) !important; /* Forest Green opacity */
            background-image: none !important;
            box-shadow: none !important;
            color: #2E7D32 !important;
            border-right: 4px solid #2E7D32 !important;
            font-weight: 700;
        }
        .sidebar-link.active i {
            color: #2E7D32 !important;
        }
        
        /* Notification Bell Fix */
        #notifBtn {
            position: relative;
        }
        #notifBtn .topbar-badge {
            position: absolute;
            top: 2px;
            right: 4px;
            width: 8px;
            height: 8px;
            background-color: #E53935;
            border-radius: 50%;
            border: 2px solid #fff;
            padding: 0;
            color: transparent;
            font-size: 0;
            display: inline-block;
        }
    </style>
</head>
<body class="admin-body">
    {{-- Sidebar --}}
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
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
                    <a href="{{ route('rw.dashboard') }}" class="sidebar-link @if(request()->routeIs('rw.dashboard') || request()->is('rw')) active @endif">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('rw/penduduk*')) active @endif">
                        <i class="bi bi-people-fill"></i>
                        <span>Manajemen Penduduk</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('rw/rt*')) active @endif">
                        <i class="bi bi-building"></i>
                        <span>Manajemen RT</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('rw.persetujuan-dokumen') }}" class="sidebar-link @if(request()->routeIs('rw.persetujuan-dokumen')) active @endif">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Persetujuan Dokumen</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('rw/keuangan*')) active @endif">
                        <i class="bi bi-cash-stack"></i>
                        <span>Persetujuan Keuangan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('rw/laporan-keuangan*')) active @endif">
                        <i class="bi bi-wallet2"></i>
                        <span>Laporan Keuangan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('rw/umkm*')) active @endif">
                        <i class="bi bi-shop"></i>
                        <span>UMKM</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('rw/agenda*')) active @endif">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span>Agenda & Berita Approval</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link @if(request()->is('rw/koperasi*')) active @endif">
                        <i class="bi bi-shop"></i>
                        <span>Koperasi</span>
                    </a>
                </li>
            </ul>

            <ul class="sidebar-menu sidebar-menu-bottom">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link" onclick="event.preventDefault();">
                        <i class="bi bi-question-circle"></i>
                        <span>Pusat Bantuan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link text-danger" id="rwLogout">
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
                    <input type="text" placeholder="Cari di WargaDigi..." id="globalSearch">
                </div>
            </div>
            <div class="topbar-right">
                <button class="topbar-icon" id="notifBtn">
                    <i class="bi bi-bell"></i>
                    <span class="topbar-badge">3</span>
                </button>
                <a href="#" class="topbar-icon text-decoration-none text-dark" id="settingsBtn">
                    <i class="bi bi-gear"></i>
                </a>
                <div class="topbar-avatar" id="avatarDropdown">
                    <div class="avatar-circle">
                        <span>{{ substr(Auth::user()->name ?? 'RW', 0, 1) }}</span>
                    </div>
                </div>
                <span class="fw-bold text-dark small ms-1">{{ Auth::user()->name ?? 'Ketua RW' }}</span>
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
            <h6><i class="bi bi-bell-fill"></i> Notifikasi RW</h6>
            <button class="notif-close" id="notifClose"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="notif-panel-body">
            <div class="notif-item unread">
                <div class="notif-icon warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="notif-content">
                    <p class="notif-text">12 dokumen menunggu verifikasi RW</p>
                    <span class="notif-time">10 menit yang lalu</span>
                </div>
            </div>
            <div class="notif-item unread">
                <div class="notif-icon info"><i class="bi bi-info-circle-fill"></i></div>
                <div class="notif-content">
                    <p class="notif-text">RT 04 telah mengunggah laporan keuangan</p>
                    <span class="notif-time">1 jam yang lalu</span>
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
