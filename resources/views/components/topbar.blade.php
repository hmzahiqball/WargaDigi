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

    // Dynamic Notifications for UMKM status
    $dynamicNotifs = [];
    if (Auth::check()) {
        if (in_array($userRole, ['Admin RW', 'Pimpinan RW'])) {
            try {
                $pendingUsahaRw = \App\Models\UmkmUsaha::with('pemilik.penduduk')
                    ->where('status_verifikasi', 'Pending')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                foreach ($pendingUsahaRw as $pUsaha) {
                    $namaPemilik = $pUsaha->pemilik->penduduk->nama_lengkap ?? $pUsaha->pemilik->username ?? 'Warga';
                    $dynamicNotifs[] = [
                        'type'   => 'warning',
                        'icon'   => 'bi-shop',
                        'text'   => "UMKM Baru: {$pUsaha->nama_usaha} ({$namaPemilik}) menunggu verifikasi RW.",
                        'time'   => $pUsaha->created_at ? $pUsaha->created_at->diffForHumans() : 'Baru saja',
                        'unread' => true,
                        'url'    => Route::has('rw.umkm.index') ? route('rw.umkm.index') : '#',
                    ];
                }
            } catch (\Throwable $e) {}
        } elseif ($userRole === 'Warga') {
            try {
                // 1. Notifikasi resmi dari Database Notifications
                $dbNotifs = Auth::user()->notifications()->take(10)->get();
                $existingUmkmNotifKeys = [];
                $existingPembayaranNotifIds = [];
                foreach ($dbNotifs as $dbN) {
                    $type = $dbN->data['type'] ?? 'info';
                    $icon = $dbN->data['icon'] ?? 'bi-bell-fill';
                    $judul = $dbN->data['judul'] ?? '';
                    $pesan = $dbN->data['pesan'] ?? '';
                    $fullText = $judul ? "{$judul}: {$pesan}" : $pesan;

                    if (!empty($dbN->data['umkm_usaha_id'])) {
                        $existingUmkmNotifKeys[] = $dbN->data['umkm_usaha_id'] . '_' . ($dbN->data['status'] ?? '');
                    }

                    if (!empty($dbN->data['pembayaran_id'])) {
                        $existingPembayaranNotifIds[] = $dbN->data['pembayaran_id'];
                    }

                    $dynamicNotifs[] = [
                        'type'   => $type,
                        'icon'   => $icon,
                        'text'   => $fullText,
                        'time'   => $dbN->created_at ? $dbN->created_at->diffForHumans() : 'Baru saja',
                        'unread' => is_null($dbN->read_at),
                        'url'    => route('warga.notifications.read', $dbN->id),
                    ];
                }

                // 2. Sinkronisasi Realtime Tagihan Belum Dibayar Warga (EV-018)
                if (Auth::user()->nik) {
                    $pendudukWarga = \App\Models\Penduduk::with('keluarga')->where('nik', Auth::user()->nik)->first();
                    if ($pendudukWarga && $pendudukWarga->keluarga) {
                        $unpaidPembayaran = \App\Models\PembayaranTagihan::with('tagihan')
                            ->where('keluarga_id', $pendudukWarga->keluarga->id)
                            ->where('status', 'Unpaid')
                            ->orderBy('created_at', 'desc')
                            ->get();

                        foreach ($unpaidPembayaran as $up) {
                            if ($up->tagihan && !in_array($up->id, $existingPembayaranNotifIds)) {
                                $dynamicNotifs[] = [
                                    'type'   => 'warning',
                                    'icon'   => 'bi-receipt-cutoff',
                                    'text'   => "Tagihan Belum Lunas: {$up->tagihan->judul} ({$up->tagihan->formatted_nominal})",
                                    'time'   => $up->created_at ? $up->created_at->diffForHumans() : 'Belum Dibayar',
                                    'unread' => true,
                                    'url'    => route('warga.tagihan.index'),
                                ];
                            }
                        }
                    }
                }

                // 3. Query status usaha warga (Approved, Rejected, Pending)
                if (Auth::user()->nik) {
                    $usahaWarga = \App\Models\UmkmUsaha::where('nik', Auth::user()->nik)
                        ->whereIn('status_verifikasi', ['Pending', 'Approved', 'Rejected'])
                        ->orderBy('updated_at', 'desc')
                        ->limit(5)
                        ->get();

                    foreach ($usahaWarga as $u) {
                        $key = $u->id . '_' . $u->status_verifikasi;
                        if (in_array($key, $existingUmkmNotifKeys)) {
                            continue;
                        }

                        if ($u->status_verifikasi === 'Approved') {
                            $dynamicNotifs[] = [
                                'type'   => 'success',
                                'icon'   => 'bi-check-circle-fill',
                                'text'   => "Pendaftaran UMKM Disetujui: Selamat! Usaha '{$u->nama_usaha}' telah disetujui RW.",
                                'time'   => $u->updated_at ? $u->updated_at->diffForHumans() : 'Baru saja',
                                'unread' => true,
                                'url'    => Route::has('warga.umkm.kelola') ? route('warga.umkm.kelola', ['usaha_id' => $u->id]) : '#',
                            ];
                        } elseif ($u->status_verifikasi === 'Rejected') {
                            $alasan = !empty($u->catatan_verifikasi) ? " Alasan: " . \Illuminate\Support\Str::limit($u->catatan_verifikasi, 50) : "";
                            $dynamicNotifs[] = [
                                'type'   => 'danger',
                                'icon'   => 'bi-x-circle-fill',
                                'text'   => "Pendaftaran UMKM Ditolak: Pengajuan usaha '{$u->nama_usaha}' ditolak oleh RW.{$alasan}",
                                'time'   => $u->updated_at ? $u->updated_at->diffForHumans() : 'Baru saja',
                                'unread' => true,
                                'url'    => Route::has('warga.umkm.galeri') ? route('warga.umkm.galeri') : '#',
                            ];
                        } elseif ($u->status_verifikasi === 'Pending') {
                            $dynamicNotifs[] = [
                                'type'   => 'warning',
                                'icon'   => 'bi-hourglass-split',
                                'text'   => "Pendaftaran UMKM: Usaha '{$u->nama_usaha}' berstatus Pending (menunggu peninjauan RW).",
                                'time'   => $u->created_at ? $u->created_at->diffForHumans() : 'Baru saja',
                                'unread' => true,
                                'url'    => Route::has('warga.umkm.kelola') ? route('warga.umkm.kelola', ['usaha_id' => $u->id]) : '#',
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }
    }

    if (!empty($dynamicNotifs)) {
        $notifications = array_merge($dynamicNotifs, $notifications);
    }

    $notifTitle = $config['notif_title'] ?? 'Notifikasi';
    $unreadCount = 0;
    foreach ($notifications as $n) {
        if (!empty($n['unread'])) {
            $unreadCount++;
        }
    }
    $notifBadge = $unreadCount;
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

<div class="admin-notif-panel" id="notifPanel">
    <div class="notif-panel-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-bell-fill me-1"></i> {{ $notifTitle }}</h6>
        <button class="notif-close" id="notifClose" type="button" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="notif-panel-body" id="notifPanelBody">
        @forelse($notifications as $index => $notif)
            @php 
                $hasUrl = !empty($notif['url']) && $notif['url'] !== '#'; 
                $notifKey = md5(($notif['text'] ?? '') . ($notif['time'] ?? ''));
                $isDeferred = $index >= 8;
            @endphp
            <div class="notif-item position-relative bg-white {{ !empty($notif['unread']) ? 'unread' : '' }} {{ $isDeferred ? 'notif-deferred' : '' }}"
                 data-notif-key="{{ $notifKey }}"
                 data-unread="{{ !empty($notif['unread']) ? '1' : '0' }}"
                 style="{{ $isDeferred ? 'display: none !important;' : '' }}"
                 @if($hasUrl) onclick="window.location.href='{{ $notif['url'] }}'" style="cursor: pointer;" title="Klik untuk membuka" @endif>
                
                {{-- Tombol Sembunyikan Notifikasi (Icon Mata Silang di Pojok Kanan Atas) --}}
                <button type="button" 
                        class="notif-hide-btn" 
                        onclick="hideNotification(event, this, '{{ $notifKey }}')" 
                        title="Sembunyikan notifikasi ini"
                        aria-label="Sembunyikan notifikasi">
                    <i class="bi bi-eye-slash"></i>
                </button>

                <div class="notif-icon {{ $notif['type'] ?? 'info' }}" @if(($notif['type'] ?? '') === 'danger') style="background: #ffebee; color: #c62828;" @endif>
                    <i class="bi {{ $notif['icon'] ?? 'bi-info-circle-fill' }}"></i>
                </div>
                <div class="notif-content">
                    <p class="notif-text mb-1" style="line-height: 1.35;">{{ $notif['text'] }}</p>
                    <span class="notif-time">{{ $notif['time'] }}</span>
                </div>
            </div>
        @empty
            <div class="p-3 text-center text-muted small" id="notifEmptyState">
                <i class="bi bi-bell-slash d-block fs-4 mb-1"></i>
                Tidak ada notifikasi baru
            </div>
        @endforelse

        {{-- State kosong jika seluruh notifikasi disembunyikan --}}
        <div class="p-4 text-center text-muted small" id="notifEmptyHiddenState" style="display: none;">
            <i class="bi bi-eye-slash d-block fs-3 mb-2 opacity-50"></i>
            <div>Semua notifikasi telah disembunyikan</div>
            <button type="button" class="btn btn-sm btn-outline-success mt-2 rounded-pill px-3 py-1 fw-semibold" onclick="resetHiddenNotifications()">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan Notifikasi
            </button>
        </div>

        {{-- Tombol Muat Lebih Banyak (Progressive Chunk Rendering 8 per klik) --}}
        @if(count($notifications) > 8)
            <div class="p-2 border-top text-center" id="notifLoadMoreWrapper">
                <button type="button" class="btn btn-sm btn-light border w-100 py-2 rounded-3 text-muted fw-semibold small" id="btnLoadMoreNotifs" onclick="loadMoreNotifications()">
                    <i class="bi bi-arrow-down-circle me-1"></i> Tampilkan Lebih Banyak (<span id="notifRemainingCount">{{ count($notifications) - 8 }}</span>)
                </button>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initHiddenNotifications();
});

function getHiddenNotifKeys() {
    try {
        return JSON.parse(localStorage.getItem('wargadigi_hidden_notifs') || '[]');
    } catch (e) {
        return [];
    }
}

function initHiddenNotifications() {
    const hiddenKeys = getHiddenNotifKeys();
    if (hiddenKeys.length > 0) {
        const items = document.querySelectorAll('.notif-item');
        let unreadHidden = 0;

        items.forEach(function(item) {
            const key = item.getAttribute('data-notif-key');
            if (hiddenKeys.includes(key)) {
                if (item.getAttribute('data-unread') === '1') {
                    unreadHidden++;
                }
                item.remove();
            }
        });

        const badge = document.querySelector('.topbar-badge');
        if (badge) {
            let currentBadge = parseInt(badge.textContent.trim()) || 0;
            let newBadge = Math.max(0, currentBadge - unreadHidden);
            if (newBadge === 0) {
                badge.remove();
            } else {
                badge.textContent = newBadge;
            }
        }
    }

    // Auto-reveal deferred items agar tetap tampil hingga 8 notifikasi jika tersedia
    const visibleCount = document.querySelectorAll('.notif-item:not(.notif-deferred)').length;
    if (visibleCount < 8) {
        const deferredItems = document.querySelectorAll('.notif-item.notif-deferred');
        let needed = 8 - visibleCount;
        deferredItems.forEach(function(item) {
            if (needed > 0) {
                item.style.display = '';
                item.classList.remove('notif-deferred');
                needed--;
            }
        });
    }

    checkNotifEmptyState();
    updateRemainingCount();
}

function hideNotification(e, btn, notifKey) {
    e.stopPropagation();

    const item = btn.closest('.notif-item');
    if (!item) return;

    const hiddenKeys = getHiddenNotifKeys();
    if (!hiddenKeys.includes(notifKey)) {
        hiddenKeys.push(notifKey);
        try {
            localStorage.setItem('wargadigi_hidden_notifs', JSON.stringify(hiddenKeys));
        } catch (err) {}
    }

    item.style.opacity = '0';
    item.style.transform = 'translateX(15px)';

    setTimeout(function() {
        const isUnread = item.getAttribute('data-unread') === '1';
        item.remove();

        if (isUnread) {
            const badge = document.querySelector('.topbar-badge');
            if (badge) {
                let currentBadge = parseInt(badge.textContent.trim()) || 0;
                let newBadge = Math.max(0, currentBadge - 1);
                if (newBadge === 0) {
                    badge.remove();
                } else {
                    badge.textContent = newBadge;
                }
            }
        }

        // Tampilkan 1 notifikasi berikutnya dari daftar deferred (jika ada) untuk menjaga kuota 8 tampilan
        const nextDeferred = document.querySelector('.notif-item.notif-deferred');
        if (nextDeferred) {
            nextDeferred.style.display = '';
            nextDeferred.classList.remove('notif-deferred');
            nextDeferred.style.opacity = '0';
            setTimeout(function() {
                nextDeferred.style.opacity = '1';
            }, 30);
        }

        checkNotifEmptyState();
        updateRemainingCount();
    }, 250);
}

function resetHiddenNotifications() {
    try {
        localStorage.removeItem('wargadigi_hidden_notifs');
    } catch (e) {}
    window.location.reload();
}

function loadMoreNotifications() {
    const deferredItems = document.querySelectorAll('.notif-item.notif-deferred');
    const chunkSize = 8;
    let loaded = 0;

    deferredItems.forEach(function(item) {
        if (loaded < chunkSize && item.style.display === 'none') {
            item.style.display = '';
            item.classList.remove('notif-deferred');
            item.style.opacity = '0';
            setTimeout(function() {
                item.style.opacity = '1';
            }, 30);
            loaded++;
        }
    });

    updateRemainingCount();
}

function updateRemainingCount() {
    const remainingItems = document.querySelectorAll('.notif-item.notif-deferred');
    const counterSpan = document.getElementById('notifRemainingCount');
    const wrapper = document.getElementById('notifLoadMoreWrapper');

    if (counterSpan) {
        counterSpan.textContent = remainingItems.length;
    }

    if (wrapper) {
        wrapper.style.display = remainingItems.length > 0 ? '' : 'none';
    }
}

function checkNotifEmptyState() {
    const visibleItems = document.querySelectorAll('.notif-item');
    const emptyHiddenState = document.getElementById('notifEmptyHiddenState');
    const emptyState = document.getElementById('notifEmptyState');

    if (visibleItems.length === 0 && !emptyState) {
        if (emptyHiddenState) {
            emptyHiddenState.style.display = 'block';
        }
    }
}
</script>
