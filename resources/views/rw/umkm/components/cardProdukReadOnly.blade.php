@php
    $hasCol = $hasCol ?? false;
    $colClass = $colClass ?? 'col-sm-6 col-md-4 col-lg-3';
    $statusStok = strtolower($item->status_stok ?? 'tersedia');
    $isHabis = ($statusStok === 'habis');
    $foto = !empty($item->foto_produk) 
        ? (str_starts_with($item->foto_produk, 'http') ? $item->foto_produk : asset('storage/' . $item->foto_produk)) 
        : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop';
    $namaProduk = $item->nama_produk ?? 'Produk';
    $deskripsi = $item->deskripsi ?? 'Tidak ada deskripsi produk.';
    $harga = number_format($item->harga ?? 0, 0, ',', '.');
    $usahaNama = $item->usaha->nama_usaha ?? ($usaha->nama_usaha ?? 'Warga RW 12');
    
    $messagingLabel = \App\Services\Messaging\MessagingService::getLabel();
    $messagingIcon = \App\Services\Messaging\MessagingService::getIcon();
    $messagingDriverName = \App\Services\Messaging\MessagingService::getName();
    $messagingColor = ($messagingDriverName === 'telegram') ? '#24A1DE' : '#16a34a';
    $productDetailUrl = route('produk.show', $item->id);
    $shareText = "Lihat produk {$namaProduk} seharga Rp {$harga} dari {$usahaNama} di WargaDigi RW 21:";
    $linkMessaging = \App\Services\Messaging\MessagingService::getShareUrl($shareText, $productDetailUrl);

    if (!empty($item->link_wa)) {
        $linkMessaging = $item->link_wa;
    } elseif (!empty($usaha->no_wa) || !empty($item->usaha->no_wa)) {
        $targetWa = !empty($usaha->no_wa) ? $usaha->no_wa : $item->usaha->no_wa;
        $chatText = "Halo, saya tertarik dengan produk {$namaProduk} seharga Rp {$harga}. Apakah masih tersedia?";
        $linkMessaging = \App\Services\Messaging\MessagingService::getDirectChatUrl($targetWa, $chatText);
    }

    $statusClass = $item->status_produk ?? 'Aktif';
    $statusText = $item->status_produk ?? 'Aktif';
    if ($statusClass == 'Aktif') {
        $statusClass = 'bg-success';
    } elseif ($statusClass == 'Pending') {
        $statusClass = 'bg-warning';
    } elseif ($statusClass == 'Tidak Aktif') {
        $statusClass = 'bg-secondary';
    } else {
        $statusClass = 'bg-success';
    }
@endphp

@if($hasCol)
<div class="{{ $colClass }}">
@endif
    <div class="product-manage-card h-100 d-flex flex-column {{ $isHabis ? 'out-of-stock' : '' }}">
        <div class="product-manage-img position-relative overflow-hidden rounded-top-3">
            <a href="{{ $productDetailUrl }}">
                <img src="{{ $foto }}" alt="{{ $namaProduk }}" class="img-fluid w-100" style="height: 180px; object-fit: cover;">
            </a>
            <span class="badge {{ $statusClass }} text-white rounded-pill px-3 py-1 fw-semibold position-absolute top-0 end-0 m-3 small shadow-sm">
                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> {{ $statusText }}
            </span>
        </div>

        <div class="p-3 d-flex flex-column flex-grow-1">
            <h6 class="fw-bold text-dark mb-1">
                <a href="{{ $productDetailUrl }}" class="text-dark text-decoration-none">
                    {{ $namaProduk }}
                </a>
            </h6>
            <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $deskripsi }}
            </p>

            <div class="pt-2 border-top mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block" style="font-size: 14px;">Harga</small>
                        <span class="fw-bold text-success fs-6">Rp {{ $harga }}</span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 14px;">Status Stok</small>
                        @if($statusStok === 'habis')
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 fw-semibold" style="font-size: 14px;">
                                 Habis
                            </span>
                        @elseif($statusStok === 'menipis')
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 fw-semibold" style="font-size: 14px;">
                                 Menipis
                            </span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 14px;">
                                 Tersedia
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-3 pt-1">
                <a href="{{ $linkMessaging }}" target="_blank" class="text-decoration-none" style="color: {{ $messagingColor }};" title="Hubungi via {{ $messagingLabel }}">
                    <i class="{{ $messagingIcon }} fs-5"></i>
                </a>
            </div>
        </div>
    </div>
@if($hasCol)
</div>
@endif
