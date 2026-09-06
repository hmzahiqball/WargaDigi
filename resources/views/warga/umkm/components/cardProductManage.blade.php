@php
    $colClass = $colClass ?? 'col-sm-6 col-md-4 col-lg-3';
    $statusStok = strtolower($item->status_stok ?? 'tersedia');
    $isHabis = ($statusStok === 'habis');
    $foto = !empty($item->foto_produk) 
        ? asset('storage/' . $item->foto_produk) 
        : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop';
    $namaProduk = $item->nama_produk ?? 'Produk';
    $deskripsi = $item->deskripsi ?? 'Tidak ada deskripsi produk.';
    $harga = number_format($item->harga ?? 0, 0, ',', '.');
    
    $messagingLabel = \App\Services\Messaging\MessagingService::getLabel();
    $messagingIcon = \App\Services\Messaging\MessagingService::getIcon();
    $messagingDriverName = \App\Services\Messaging\MessagingService::getName();
    $messagingColor = ($messagingDriverName === 'telegram') ? '#24A1DE' : '#16a34a';
    $rawWa = $item->usaha->no_wa ?? '628123456789';
    $directMsg = "Halo, saya tertarik dengan produk {$namaProduk} di WargaDigi.";
    $linkMessaging = \App\Services\Messaging\MessagingService::getDirectChatUrl($rawWa, $directMsg);

    $statusClass = $item->status_produk;
    $statusText = $item->status_produk;
    if ($statusClass == 'Aktif') {
        $statusClass = 'bg-success';
    } elseif ($statusClass == 'Pending') {
        $statusClass = 'bg-warning';
    } elseif ($statusClass == 'Tidak Aktif') {
        $statusClass = 'bg-secondary';
    }
@endphp

@php
    $productDetailUrl = route('warga.umkm.produk.detail', [
        'id' => $item->id,
        'from' => 'kelola_umkm',
        'usaha_id' => $item->umkm_usaha_id ?? ($item->usaha->id ?? null),
    ]);
@endphp

<div class="{{ $colClass }}">
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
            </div>

            <div class="d-flex justify-content-end align-items-center gap-3 pt-1">
                {{--
                <a href="{{ $linkMessaging }}" target="_blank" class="text-decoration-none" style="color: {{ $messagingColor }};" title="Hubungi via {{ $messagingLabel }}">
                    <i class="{{ $messagingIcon }} fs-5"></i>
                </a>
                --}}
                <button type="button" 
                        class="btn btn-link p-0 text-secondary text-decoration-none" 
                        title="Edit Produk"
                        data-bs-toggle="modal" 
                        data-bs-target="#modalEditProduk"
                        data-id="{{ $item->id }}"
                        data-nama="{{ $item->nama_produk }}"
                        data-stok="{{ $statusStok }}"
                        data-kategori="{{ $item->kategori_produk_id }}"
                        data-harga="{{ $item->harga }}"
                        data-wa="{{ $item->usaha->no_wa ?? '' }}"
                        data-deskripsi="{{ $item->deskripsi }}"
                        data-foto="{{ $foto }}"
                        data-status-produk="{{ $item->status_produk ?? 'Aktif' }}"
                        data-action="{{ route('warga.umkm.produk.update', $item->id) }}">
                    <i class="bi bi-pencil fs-5"></i>
                </button>
                <form id="deleteProductForm-{{ $item->id }}" action="{{ route('warga.umkm.produk.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            class="btn btn-link p-0 text-danger text-decoration-none btn-trigger-delete" 
                            title="Hapus Produk"
                            data-bs-toggle="modal"
                            data-bs-target="#modalConfirmDelete"
                            data-item-name="{{ $item->nama_produk }}"
                            data-form-id="deleteProductForm-{{ $item->id }}">
                        <i class="bi bi-trash fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
