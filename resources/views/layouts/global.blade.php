<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaDigi 21 — Digitalisasi Gotong Royong')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ \App\Models\SystemSetting::getLogoUrl() }}">
    <title>@yield('title', 'Dashboard') — WargaDigi 21</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="admin-body">
    {{-- Dynamic Sidebar Component --}}
    @include('components.sidebar')

    {{-- Overlay gelap untuk drawer sidebar di mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Main Wrapper --}}
    <div class="admin-main" id="adminMain">
        {{-- Dynamic Topbar Component --}}
        @include('components.topbar')

        {{-- Maintenance Mode Warning Banner for Admins --}}
        @if(\App\Models\SystemSetting::get('maintenance_mode', false))
            <div class="alert alert-warning mb-0 py-2 px-4 d-flex align-items-center justify-content-between rounded-0 border-0 border-bottom border-warning" style="background-color: #FEF3C7; color: #92400E; font-size: 13px;" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                    <span><strong>Mode Pemeliharaan Aktif!</strong> Situs publik dan akses warga saat ini diblokir (hanya Admin yang dapat mengakses sistem).</span>
                </div>
                <a href="{{ route('admin.pengaturan-sistem') }}" class="btn btn-sm btn-outline-warning text-dark py-0 px-2 fw-semibold" style="font-size: 11px;">Kelola Pengaturan</a>
            </div>
        @endif

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
        // Sidebar toggle (responsif: drawer di mobile, collapse di desktop)
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.getElementById('adminMain');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        const isMobile = () => window.innerWidth < 992;

        function closeMobileSidebar() {
            sidebar?.classList.remove('open');
            sidebarOverlay?.classList.remove('show');
        }

        sidebarToggle?.addEventListener('click', function() {
            if (isMobile()) {
                // Mode drawer: buka/tutup sidebar dengan overlay
                const willOpen = !sidebar?.classList.contains('open');
                sidebar?.classList.toggle('open', willOpen);
                sidebarOverlay?.classList.toggle('show', willOpen);
            } else {
                // Mode desktop: lebarkan/sempitkan area konten
                sidebar?.classList.toggle('collapsed');
                mainContent?.classList.toggle('expanded');
            }
        });

        // Tutup drawer saat overlay diklik
        sidebarOverlay?.addEventListener('click', closeMobileSidebar);

        // Tutup drawer saat memilih menu di mobile
        sidebar?.querySelectorAll('.sidebar-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (isMobile()) {
                    closeMobileSidebar();
                }
            });
        });

        // Reset state saat ukuran layar berubah (hindari state nyangkut antar breakpoint)
        window.addEventListener('resize', function() {
            if (!isMobile()) {
                closeMobileSidebar();
            } else {
                sidebar?.classList.remove('collapsed');
                mainContent?.classList.remove('expanded');
            }
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

        // State awal mobile: pastikan sidebar tertutup sebagai drawer (tanpa class desktop)
        if (isMobile()) {
            sidebar?.classList.remove('collapsed');
            mainContent?.classList.remove('expanded');
            closeMobileSidebar();
        }

        // Helper untuk memulihkan tombol submit form jika validasi gagal
        window.resetFormSubmitting = function(form) {
            if (!form) return;
            delete form.dataset.submitting;
            const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitBtns.forEach(submitBtn => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('disabled', 'opacity-75');
                if (submitBtn.dataset.originalHtml) {
                    submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                    delete submitBtn.dataset.originalHtml;
                }
            });
        };

        // Global Anti Double-Submit Handler
        document.addEventListener('submit', function(e) {
            const form = e.target;

            // Abaikan jika submit sudah dibatalkan oleh event listener form (validasi gagal, konfirmasi, dsb.)
            if (e.defaultPrevented) {
                return;
            }

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
        }, false);

        // Pulihkan tombol jika pengguna kembali melalui cache browser (bfcache)
        window.addEventListener('pageshow', function(event) {
            document.querySelectorAll('form[data-submitting="true"]').forEach(form => {
                window.resetFormSubmitting(form);
            });
        });
    });
    </script>
</body>
</html>
