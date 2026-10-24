@php
    $userRole = Auth::check() ? Auth::user()->activeRole() : null;
    $allMenus = config('sidebarMenu', []);

    $mainMenus = array_filter($allMenus, function($item) use ($userRole) {
        $position = $item['position'] ?? 'main';
        if ($position !== 'main') return false;
        if (empty($item['roles'])) return true;
        if (!$userRole) return true; // Fallback: show all if guest/preview
        return in_array($userRole, $item['roles']);
    });

    $bottomMenus = array_filter($allMenus, function($item) use ($userRole) {
        $position = $item['position'] ?? 'main';
        if ($position !== 'bottom') return false;
        if (empty($item['roles'])) return true;
        if (!$userRole) return true; // Fallback: show all if guest/preview
        return in_array($userRole, $item['roles']);
    });

    $brandRole = 'RW 21 TANIMULYA';
    $brandIcon = 'bi-geo-alt-fill';

    if ($userRole === 'Admin Aplikasi') {
        $brandRole = 'Administrator';
        $brandIcon = 'bi-globe2';
    } elseif (in_array($userRole, ['Admin RW', 'Pimpinan RW'])) {
        $brandRole = 'RW 12 TANIMULYA';
    } elseif ($userRole === 'Ketua RT') {
        $kodeRt = optional(Auth::user()->rt)->kode_rt ?? optional(Auth::user()->rt)->nama_rt ?? '01';
        $brandRole = 'RT ' . $kodeRt . ' RW 12';
    } elseif (in_array($userRole, ['Op. Konten RW', 'Op. Konten RT'])) {
        $brandRole = 'OPERATOR KONTEN';
    } elseif (in_array($userRole, ['Op. Keuangan RW', 'Op. Keuangan RT', 'DKM'])) {
        $brandRole = 'OPERATOR KEUANGAN';
    } elseif ($userRole === 'Warga') {
        $brandRole = 'RW 21 TANIMULYA';
    } elseif ($userRole) {
        $brandRole = strtoupper($userRole);
    }
@endphp

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="{{ \App\Models\SystemSetting::getLogoUrl() }}" alt="WargaDigi 21 Logo" style="height: 38px; width: auto; object-fit: contain;">
            <div class="sidebar-brand-text">
                <span class="brand-name">WargaDigi</span>
                <span class="brand-role">{{ $brandRole }}</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            @foreach($mainMenus as $item)
                @php
                    $url = '#';
                    if (!empty($item['route']) && Route::has($item['route'])) {
                        $url = route($item['route']);
                    } elseif (!empty($item['url']) && $item['url'] !== '#') {
                        $url = url($item['url']);
                    }

                    $isActive = false;
                    if (isset($item['active'])) {
                        foreach ((array)$item['active'] as $pattern) {
                            if (request()->routeIs($pattern) || request()->is($pattern)) {
                                $isActive = true;
                                break;
                            }
                        }
                    }
                @endphp
                <li class="sidebar-item">
                    <a href="{{ $url }}" class="sidebar-link {{ $isActive ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                        <span>{{ $item['title'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        @php
            $canSwitchRole = Auth::check() && Auth::user()->hasMultipleRoles();
        @endphp
        @if(count($bottomMenus) > 0 || $canSwitchRole)
            <ul class="sidebar-menu sidebar-menu-bottom">
                @if($canSwitchRole)
                    <li class="sidebar-item">
                        <form action="{{ route('ganti-peran') }}" method="POST" id="sidebar-switch-role-form" class="w-100">
                            @csrf
                            <a href="#" class="sidebar-link" onclick="event.preventDefault(); document.getElementById('sidebar-switch-role-form').submit();">
                                <i class="bi bi-arrow-repeat"></i>
                                <span>Ganti Peran</span>
                            </a>
                        </form>
                    </li>
                @endif
                @foreach($bottomMenus as $item)
                    @php
                        $url = '#';
                        if (!empty($item['route']) && Route::has($item['route'])) {
                            $url = route($item['route']);
                        } elseif (!empty($item['url']) && $item['url'] !== '#') {
                            $url = url($item['url']);
                        }

                        $isActive = false;
                        if (isset($item['active'])) {
                            foreach ((array)$item['active'] as $pattern) {
                                if (request()->routeIs($pattern) || request()->is($pattern)) {
                                    $isActive = true;
                                    break;
                                }
                            }
                        }

                        $isLogout = $item['is_logout'] ?? false;
                    @endphp
                    <li class="sidebar-item">
                        @if($isLogout)
                            <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form" class="w-100">
                                @csrf
                                <a href="#" class="sidebar-link text-danger" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                                    <i class="bi {{ $item['icon'] ?? 'bi-box-arrow-left' }}"></i>
                                    <span>{{ $item['title'] }}</span>
                                </a>
                            </form>
                        @else
                            <a href="{{ $url }}" class="sidebar-link {{ $isActive ? 'active' : '' }}" @if($url === '#') onclick="event.preventDefault();" @endif>
                                <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                                <span>{{ $item['title'] }}</span>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </nav>
</aside>
