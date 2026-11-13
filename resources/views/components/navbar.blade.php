<nav class="navbar navbar-expand-lg navbar-warga">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="{{ \App\Models\SystemSetting::getLogoUrl() }}" alt="WargaDigi 21 Logo">
            WargaDigi 21
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                {{-- Disembunyikan: menu Informasi (statistik warga terlalu terbuka untuk publik).
                <li class="nav-item">
                    <a class="nav-link" href="/informasi">Informasi</a>
                </li>
                --}}
                {{-- Disembunyikan sementara: menu Transparansi di landing page.
                     Aktifkan kembali bila diperlukan.
                <li class="nav-item">
                    <a class="nav-link" href="/transparansi">Transparansi</a>
                </li>
                --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('berita*') ? 'active' : '' }}" href="/berita">Berita</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('agenda*') ? 'active' : '' }}" href="{{ route('agenda') }}">Agenda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('layanan-mandiri*') ? 'active' : '' }}" href="/layanan-mandiri">Layanan Mandiri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pojok-umkm*') ? 'active' : '' }}" href="/pojok-umkm">Pojok UMKM</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="/login" class="btn btn-masuk">Masuk</a>
            </div>
        </div>
    </div>
</nav>
