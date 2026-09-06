<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaDigi 21 — Digitalisasi Gotong Royong')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — WargaDigi</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="admin-body">
    {{-- Dynamic Sidebar Component --}}
    @include('components.sidebar')

    {{-- Main Wrapper --}}
    <div class="admin-main" id="adminMain">
        {{-- Dynamic Topbar Component --}}
        @include('components.topbar')

        {{-- Page Content --}}
        <main class="admin-content">
            @yield('content')
        </main>
    </div>

    {{-- Dynamic Alert & Confirmation Modals --}}
    @include('components.alertModal')

    {{-- Toast Container --}}
    <div class="admin-toast-container" id="toastContainer"></div>

    {{-- Page Scripts --}}
    @stack('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.getElementById('adminMain');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle?.addEventListener('click', function() {
            sidebar?.classList.toggle('collapsed');
            mainContent?.classList.toggle('expanded');
        });

        // Notification panel toggle
        const notifBtn = document.getElementById('notifBtn');
        const notifPanel = document.getElementById('notifPanel');
        const notifClose = document.getElementById('notifClose');

        notifBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            notifPanel?.classList.toggle('open');
        });

        notifClose?.addEventListener('click', function() {
            notifPanel?.classList.remove('open');
        });

        document.addEventListener('click', function(e) {
            if (notifPanel && !notifPanel.contains(e.target) && notifBtn && !notifBtn.contains(e.target)) {
                notifPanel.classList.remove('open');
            }
        });

        // Global Toast Notification Helper
        window.showAdminToast = function(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

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
                <button class="toast-close" type="button"><i class="bi bi-x"></i></button>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('show'));

            toast.querySelector('.toast-close')?.addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            });

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        // Mobile Responsive Handling
        if (window.innerWidth < 992) {
            sidebar?.classList.add('collapsed');
            mainContent?.classList.add('expanded');
        }

        // Global Anti Double-Submit Handler
        document.addEventListener('submit', function(e) {
            const form = e.target;
            // Abaikan form dengan method GET (seperti form pencarian atau filter)
            if (form.method && form.method.toUpperCase() === 'GET') {
                return;
            }

            // Jika form sudah dalam proses kirim, hentikan pengiriman ganda
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }

            // Tandai form sedang dalam pengiriman
            form.dataset.submitting = 'true';

            // Cari tombol submit dalam form
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled', 'opacity-75');
                if (submitBtn.tagName.toLowerCase() === 'button') {
                    submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';
                }
            }
        }, true);

        // Pulihkan tombol jika pengguna kembali melalui cache browser (bfcache)
        window.addEventListener('pageshow', function(event) {
            document.querySelectorAll('form[data-submitting="true"]').forEach(form => {
                delete form.dataset.submitting;
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('disabled', 'opacity-75');
                    if (submitBtn.dataset.originalHtml) {
                        submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
