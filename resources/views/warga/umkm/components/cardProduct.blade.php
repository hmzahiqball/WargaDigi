@php
    $colClass = $colClass ?? 'col-sm-6 col-md-4 col-lg-3';
    $showOwner = $showOwner ?? true;
    $statusStok = strtolower($item->status_stok ?? 'tersedia');
    $isHabis = ($statusStok === 'habis');
    $foto = !empty($item->foto_produk) 
        ? asset('storage/' . $item->foto_produk) 
        : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop';
    $namaProduk = $item->nama_produk ?? 'Produk UMKM';
    $harga = number_format($item->harga ?? 0, 0, ',', '.');
    $kategoriNama = $item->usaha->kategori_umkm->nama_kategori 
        ?? 'UMKM';
    $usahaNama = $item->usaha->nama_usaha ?? 'Warga RW 12';
    $usahaId = $item->umkm_usaha_id ?? $item->usaha->id ?? '';
    $deskripsi = $item->deskripsi ?? 'Tidak ada deskripsi produk.';
    $productDetailUrl = request()->is('warga/*')
        ? (Route::has('warga.umkm.produk.detail') ? route('warga.umkm.produk.detail', $item->id) : route('produk.show', $item->id))
        : route('produk.show', $item->id);

    // Messaging Integration (Telegram / WhatsApp)
    $messagingLabel = \App\Services\Messaging\MessagingService::getLabel();
    $messagingIcon = \App\Services\Messaging\MessagingService::getIcon();
    $messagingBtnOutline = \App\Services\Messaging\MessagingService::getOutlineButtonClass();
    $shareText = "Lihat produk {$namaProduk} seharga Rp {$harga} dari {$usahaNama} di WargaDigi RW 21:";
    $shareLink = \App\Services\Messaging\MessagingService::getShareUrl($shareText, $productDetailUrl);

    $isBaru = !empty($item->created_at) && ($item->created_at->diffInDays(now()) < 14);
    $isPopuler = ($item->jumlah_akses ?? 0) >= 20;
    $hasCol = isset($colClass) ? ($colClass !== false && $colClass !== '') : true;
    $colClass = $colClass ?? 'col-sm-6 col-md-4 col-lg-3';
@endphp

@if($hasCol)
<div class="{{ $colClass }}">
@endif
    <div class="product-card h-100 d-flex flex-column {{ $isHabis ? 'out-of-stock' : '' }}">
        <div class="product-img-wrapper position-relative overflow-hidden rounded-top-3">
            <a href="{{ $productDetailUrl }}">
                <img src="{{ $foto }}" alt="{{ $namaProduk }}" class="img-fluid w-100" style="height: 200px; object-fit: cover;">
            </a>

            {{-- Badges Kiri Atas: Baru & Populer --}}
            @if($isBaru || $isPopuler)
                <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1" style="z-index: 2;">
                    @if($isBaru)
                        <span class="badge bg-success text-white px-2 py-1 rounded-pill small fw-bold shadow-sm" style="font-size: 11px;">
                            <i class="bi bi-stars me-1"></i> Baru
                        </span>
                    @endif
                    @if($isPopuler)
                        <span class="badge px-2 py-1 rounded-pill small fw-bold shadow-sm text-white" style="background-color: #f97316; font-size: 11px;">
                            <i class="bi bi-fire me-1"></i> Populer
                        </span>
                    @endif
                </div>
            @endif

            <span class="badge bg-light text-dark border px-2 py-1 position-absolute top-0 end-0 m-2 shadow-sm small fw-semibold">
                {{ $kategoriNama }}
            </span>
        </div>
        <div class="p-3 d-flex flex-column flex-grow-1">
            <h6 class="fw-bold text-dark mb-1">
                <a href="{{ $productDetailUrl }}" class="text-dark text-decoration-none">
                    {{ $namaProduk }}
                </a>
            </h6>

            @if($showOwner)
                @php
                    $ownerDetailUrl = request()->is('warga/*')
                        ? ($usahaId ? (Route::has('warga.umkm.usaha.show') ? route('warga.umkm.usaha.show', $usahaId) : (Route::has('warga.umkm.detail_usaha') ? route('warga.umkm.detail_usaha', $usahaId) : '#')) : '#')
                        : ($usahaId ? (Route::has('pojok-umkm.detail_usaha') ? route('pojok-umkm.detail_usaha', $usahaId) : (Route::has('public.umkm.usaha.show') ? route('public.umkm.usaha.show', $usahaId) : '#')) : '#');
                @endphp
                <small class="text-muted d-block mb-3">
                    <a href="{{ $ownerDetailUrl }}" class="text-muted text-decoration-none">
                        <i class="bi bi-person me-1"></i> {{ $usahaNama }}
                    </a>
                </small>
            @else
                <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $deskripsi }}
                </p>
            @endif

            <div class="mt-auto pt-2 border-top">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted d-block" style="font-size: 14px;">Harga</small>
                        <span class="fw-bold text-success fs-6">Rp {{ $harga }}</span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 14px;">Status Stok</small>
                        @if($statusStok === 'habis')
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 fw-semibold" style="font-size: 14px;">
                                <i class="bi bi-x-circle me-1"></i> Habis
                            </span>
                        @elseif($statusStok === 'menipis')
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 fw-semibold" style="font-size: 14px;">
                                <i class="bi bi-exclamation-triangle me-1"></i> Menipis
                            </span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 14px;">
                                <i class="bi bi-check-circle me-1"></i> Tersedia
                            </span>
                        @endif
                    </div>
                </div>
                @auth
                    <a href="{{ $shareLink }}" target="_blank" class="btn {{ $messagingBtnOutline }} w-100 rounded-3 fw-semibold small text-decoration-none d-inline-flex align-items-center justify-content-center gap-1">
                        <i class="{{ $messagingIcon }}"></i> Share to {{ $messagingLabel }}
                    </a>
                @else
                    <a href="{{ $productDetailUrl }}" class="btn btn-outline-success w-100 rounded-3 fw-semibold small text-decoration-none">
                        <i class="bi bi-eye me-1"></i> Lihat Detail Produk
                    </a>
                @endauth
            </div>
        </div>
    </div>
@if($hasCol)
</div>
@endif
