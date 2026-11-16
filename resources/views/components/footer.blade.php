@php
    // Isi footer diatur Admin Aplikasi di menu Pengaturan Halaman Utama.
    $hu = \App\Services\HalamanUtama::data()['footer'];
    $partnerFooter = collect($hu['partner'])->filter(fn ($p) => ! empty($p['nama']))->values();
@endphp
<footer class="footer-warga">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">{{ $hu['nama'] }}</div>
                @if($hu['deskripsi'])
                    <p class="footer-desc">{!! nl2br(e($hu['deskripsi'])) !!}</p>
                @endif
                @if($hu['email'] || $hu['telepon'] || $hu['peta_url'])
                    <div class="footer-social">
                        @if($hu['email'])
                            <a href="mailto:{{ $hu['email'] }}" aria-label="Email {{ $hu['email'] }}" title="{{ $hu['email'] }}"><i class="bi bi-envelope"></i></a>
                        @endif
                        @if($hu['telepon'])
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hu['telepon']) }}" aria-label="Telepon {{ $hu['telepon'] }}" title="{{ $hu['telepon'] }}"><i class="bi bi-telephone"></i></a>
                        @endif
                        @if($hu['peta_url'])
                            <a href="{{ $hu['peta_url'] }}" target="_blank" rel="noopener" aria-label="Lokasi di peta" title="Lokasi"><i class="bi bi-geo-alt"></i></a>
                        @endif
                    </div>
                @endif
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="footer-title">Tautan Cepat</div>
                <a href="/berita" class="footer-link">Berita</a>
                <a href="{{ route('agenda') }}" class="footer-link">Agenda</a>
                <a href="/layanan-mandiri" class="footer-link">Layanan Mandiri</a>
                <a href="/pojok-umkm" class="footer-link">Pojok UMKM</a>
                <a href="{{ route('bantuan.index') }}" class="footer-link" data-testid="footer-pusat-bantuan">Pusat Bantuan</a>
            </div>
            @if($hu['sekretariat'])
                <div class="col-sm-6 col-lg-4">
                    <div class="footer-title">Sekretariat</div>
                    <p class="footer-desc">{!! nl2br(e($hu['sekretariat'])) !!}</p>
                </div>
            @endif
        </div>

        @if($partnerFooter->isNotEmpty())
            <div class="footer-partner" data-testid="footer-partner">
                <div class="footer-title text-center">{{ $hu['judul_partner'] ?: 'Didukung oleh' }}</div>
                <ul class="footer-partner-list list-unstyled mb-0">
                    @foreach($partnerFooter as $p)
                        @php $logoP = \App\Services\HalamanUtama::urlLogoPartner($p['logo'] ?? null); @endphp
                        <li>
                            @if(! empty($p['url']))<a href="{{ $p['url'] }}" target="_blank" rel="noopener" title="{{ $p['nama'] }}">@endif
                            @if($logoP)
                                <img src="{{ $logoP }}" alt="{{ $p['nama'] }}" loading="lazy">
                            @else
                                <span class="footer-partner-nama">{{ $p['nama'] }}</span>
                            @endif
                            @if(! empty($p['url']))</a>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="footer-bottom">
            {{ \App\Services\HalamanUtama::copyright($hu) }}
        </div>
    </div>
</footer>
