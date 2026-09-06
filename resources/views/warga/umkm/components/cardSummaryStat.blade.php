@php
    $cardsList = $cards ?? [
        [
            'title' => 'TOTAL PRODUK AKTIF',
            'total' => ($jumlahProdukAktif ?? 0) . ' Produk',
            'subtext' => ($jumlahKategoriProduk ?? 0) . ' kategori barang',
            'icon' => 'bi-box-seam',
            'bg' => '#e8f5e9',
            'text_color' => '#198754',
            'subtext_class' => 'text-success',
        ],
        [
            'title' => 'TOTAL PRODUK TIDAK AKTIF',
            'total' => ($jumlahProdukTidakAktif ?? $jumlahProdukPending ?? 0) . ' Produk',
            'subtext' => ($jumlahProdukStokMenipis ?? 0) . ' produk stok menipis',
            'icon' => 'bi-dash-circle',
            'bg' => '#fffbe6',
            'text_color' => '#d97706',
            'subtext_color' => '#d97706',
            'subtext_class' => '',
        ],
        [
            'title' => 'PEMINAT ' . strtoupper(\App\Services\Messaging\MessagingService::getLabel()),
            'total' => number_format($usaha->klikWA ?? 0, 0, ',', '.') . ' Klik',
            'subtext' => ($usaha && ($usaha->klikWA ?? 0) > 0) ? '+' . $usaha->klikWA . ' interaksi bulan ini' : 'Belum ada interaksi',
            'icon' => \App\Services\Messaging\MessagingService::getIcon(),
            'bg' => (\App\Services\Messaging\MessagingService::getName() === 'telegram') ? '#e0f2fe' : '#e8f5e9',
            'text_color' => (\App\Services\Messaging\MessagingService::getName() === 'telegram') ? '#0284c7' : '#198754',
            'subtext_class' => ($usaha && ($usaha->klikWA ?? 0) > 0) ? ((\App\Services\Messaging\MessagingService::getName() === 'telegram') ? 'text-info' : 'text-success') : 'text-muted',
        ],
    ];
@endphp

<div class="row g-3 mb-4">
    @foreach($cardsList as $card)
        <div class="{{ $card['col'] ?? 'col-sm-6 col-md-4' }}">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted fw-bold small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">
                            {{ $card['title'] ?? '' }}
                        </div>
                        <div class="h3 fw-bold {{ $card['total_class'] ?? 'text-dark' }} mb-1">
                            {{ $card['total'] ?? '' }}
                        </div>
                        <div class="small fw-semibold {{ $card['subtext_class'] ?? 'text-success' }}" style="{{ isset($card['subtext_color']) ? 'color: ' . $card['subtext_color'] . ';' : '' }}">
                            {{ $card['subtext'] ?? '' }}
                        </div>
                    </div>
                    <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background-color: {{ $card['bg'] ?? '#e8f5e9' }}; color: {{ $card['text_color'] ?? '#198754' }}; width: 48px; height: 48px;">
                        <i class="bi {{ $card['icon'] ?? 'bi-box-seam' }} fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
