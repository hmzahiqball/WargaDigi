@php
    $isDashboard = isset($isDashboard) ? (bool)$isDashboard : Auth::check();
    if (Auth::check()) {
        $isDashboard = true;
    }
    $layout = $isDashboard ? 'layouts.global' : 'layouts.app';
    $isFromKelola = $isFromKelola 
        ?? (request('from') === 'kelola_umkm' 
            || request('from') === 'kelola' 
            || str_contains(url()->previous(), 'galeri/kelola') 
            || str_contains(request()->header('referer', ''), 'galeri/kelola'));

    $namaProduk = $produk->nama_produk ?? 'Patung Kayu Jati Tradisional';
    $harga = number_format($produk->harga ?? 350000, 0, ',', '.');
    $statusStok = strtolower($produk->status_stok ?? 'tersedia');
    $deskripsi = $produk->deskripsi ?? 'Karya seni patung tradisional yang dipahat tangan secara detail dan cermat oleh pengrajin lokal berpengalaman menggunakan teknik ukir tradisional nusantara, menghasilkan lekukan artistik bernilai estetika tinggi tanpa cetakan mesin massal.';
    $fotoProduk = !empty($produk->foto_produk) 
        ? asset('storage/' . $produk->foto_produk) 
        : 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop';

    $usaha = $usaha ?? $produk->usaha ?? null;
    $namaUsaha = $usaha->nama_usaha ?? 'Pahatan Kayu Jati Custom';
    $fotoUsaha = !empty($usaha->foto_usaha) 
        ? asset('storage/' . $usaha->foto_usaha) 
        : 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=1200&auto=format&fit=crop';
    $kategoriNama = $produk->kategori_produk->nama_kategori 
        ?? $usaha->kategori_umkm->nama_kategori 
        ?? 'Kriya & Kerajinan';
    $ownerName = $usaha->user->penduduk->nama_lengkap 
        ?? $usaha->user->username 
        ?? 'Bpk. Jago';
    $namaRt = $usaha->user->penduduk->keluarga->rt->nama_rt 
        ?? 'RT 03';
    $alamatUsaha = $usaha->alamat_usaha 
        ?? 'Jl. Dahlia No. 14, RT 03 / RW 12, Kelurahan Tanimulya';

    $rawWa = $usaha->no_wa ?? '628123456789';
    $cleanWa = preg_replace('/[^0-9]/', '', $rawWa);
    if (str_starts_with($cleanWa, '0')) {
        $cleanWa = '62' . substr($cleanWa, 1);
    } elseif (!str_starts_with($cleanWa, '62') && !empty($cleanWa)) {
        $cleanWa = '62' . $cleanWa;
    }

    if (strlen($cleanWa) >= 10) {
        $formattedWa = '+62 ' . substr($cleanWa, 2, 3) . '-' . substr($cleanWa, 5, 4) . '-' . substr($cleanWa, 9);
    } else {
        $formattedWa = '+' . $cleanWa;
    }


    $messagingLabel = \App\Services\Messaging\MessagingService::getLabel();
    $messagingIcon = \App\Services\Messaging\MessagingService::getIcon();
    $messagingDriverName = \App\Services\Messaging\MessagingService::getName();
    $messagingColor = ($messagingDriverName === 'telegram') ? '#24A1DE' : '#16a34a';
    $messagingLightBg = ($messagingDriverName === 'telegram') ? '#f0f9ff' : '#f0fdf4';
    $messagingBorderColor = ($messagingDriverName === 'telegram') ? '#bae6fd' : '#bbf7d0';

    $pesanText = "Halo {$namaUsaha}, saya tertarik untuk memesan produk {$namaProduk} seharga Rp {$harga}. Apakah stok masih tersedia?";
    $linkDirectChat = \App\Services\Messaging\MessagingService::getDirectChatUrl($cleanWa, $pesanText);

    $universalProductUrl = route('produk.show', $produk->id);
    $bagikanText = "Lihat produk {$namaProduk} dari {$namaUsaha} di WargaDigi:";
    $linkShare = \App\Services\Messaging\MessagingService::getShareUrl($bagikanText, $universalProductUrl);

    $words = explode(' ', $namaUsaha);
    $initials = '';
    foreach (array_slice($words, 0, 2) as $w) {
        $initials .= strtoupper(substr($w, 0, 1));
    }
    if (empty($initials)) $initials = 'UM';

    if (stripos($kategoriNama, 'kayu') !== false || stripos($kategoriNama, 'kerajinan') !== false || stripos($kategoriNama, 'kriya') !== false) {
        $specs = [
            'Material Bahan' => 'Kayu Jati Solid TPK Perhutani pilihan',
            'Dimensi Ukuran' => 'Tinggi 25 cm, Lebar 12 cm, Tebal 10 cm',
            'Bobot Produk' => '±1.4 kg',
            'Pengerjaan' => 'Seni pahat tangan tradisional (Hand-carved)',
        ];
    } elseif (stripos($kategoriNama, 'kuliner') !== false || stripos($kategoriNama, 'makanan') !== false || stripos($kategoriNama, 'minuman') !== false) {
        $specs = [
            'Bahan Utama' => 'Bahan alami segar & 100% Halal pilihan',
            'Porsi / Kemasan' => 'Kemasan higienis siap santap',
            'Daya Tahan' => '1 - 2 hari (Suhu ruang / lemari pendingin)',
            'Sertifikasi' => 'Olahan rumahan higienis RW 12 Tanimulya',
        ];
    } elseif (stripos($kategoriNama, 'fashion') !== false || stripos($kategoriNama, 'batik') !== false) {
        $specs = [
            'Material Bahan' => 'Kain Katun Prima Sanforized halus & adem',
            'Pilihan Ukuran' => 'All size / Regular fit',
            'Teknik Pembuatan' => 'Batik kombinasi khas Tanimulya',
            'Petunjuk Rawat' => 'Cuci lembut dengan tangan / deterjen cair',
        ];
    } else {
        $specs = [
            'Kategori Produk' => $kategoriNama,
            'Kondisi Produk' => 'Baru & Original dari Pengrajin',
            'Status Stok' => ucfirst($statusStok),
            'Pemesanan' => 'Pemesanan langsung via WhatsApp',
        ];
    }
@endphp

@extends($layout)

@section('title', $namaProduk . ' — Detail Produk')

@section('content')
<div class="{{ $isDashboard ? 'container-fluid px-0' : 'container py-4' }}">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                @if($isFromKelola)
                    <li class="breadcrumb-item">
                        <a href="{{ route('warga.umkm.galeri') }}" class="text-decoration-none text-muted">Galeri UMKM</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('warga.umkm.kelola', isset($usaha->id) ? ['usaha_id' => $usaha->id] : []) }}" class="text-decoration-none text-muted">Kelola UMKM</a>
                    </li>
                    <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                        {{ $namaProduk }}
                    </li>
                @elseif($isDashboard)
                    <li class="breadcrumb-item">
                        <a href="{{ route('warga.umkm.galeri') }}" class="text-decoration-none text-muted">Galeri UMKM</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ isset($usaha->id) ? route('warga.umkm.detail_usaha', $usaha->id) : (Route::has('warga.umkm.galeri') ? route('warga.umkm.galeri') : '#') }}" class="text-decoration-none text-muted">{{ $namaUsaha }}</a>
                    </li>
                    <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                        {{ $namaProduk }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ route('pojok-umkm') }}" class="text-decoration-none text-muted">Pojok UMKM</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ isset($usaha->id) ? route('pojok-umkm.detail_usaha', $usaha->id) : (Route::has('pojok-umkm') ? route('pojok-umkm') : '#') }}" class="text-decoration-none text-muted">{{ $namaUsaha }}</a>
                    </li>
                    <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                        {{ $namaProduk }}
                    </li>
                @endif
            </ol>
        </nav>
    </div>

    <div class="mb-4">
        <div class="usaha-banner-card rounded-4 position-relative overflow-hidden" style="background-image: url('{{ $fotoUsaha }}'); min-height: 280px;">
            <div class="banner-overlay"></div>
            <div class="banner-content p-4 p-md-5">
                <span class="badge px-3 py-2 mb-3 rounded-2 fw-semibold" style="background-color: #f59e0b; color: #fff; font-size: 12px;">
                    {{ $usaha->kategori_umkm->nama_kategori ?? 'Kerajinan' }} Unggulan
                </span>
                <h3 class="fw-bold mb-2 text-white fs-3">
                    {{ $usaha && $usaha->nama_usaha ? 'Seni ' . $usaha->nama_usaha . ' Asli Tanimulya' : 'Seni Pahat Kayu Jati Asli Tanimulya' }}
                </h3>
                <p class="text-white-50 mb-4 small" style="max-width: 650px; line-height: 1.6;">
                    {{ $usaha->deskripsi ?? 'Karya tangan Pak Budi, RT 03. Cocok untuk hiasan rumah atau hadiah eksklusif. Dibuat dengan dedikasi tinggi menggunakan kayu jati pilihan terbaik.' }}
                </p>
                <div>
                    <a href="{{ $linkDirectChat }}" target="_blank" class="btn rounded-pill px-4 py-2 fw-semibold text-decoration-none shadow-sm text-white d-inline-flex align-items-center" style="background-color: {{ $messagingColor }}; border-color: {{ $messagingColor }};">
                        <i class="{{ $messagingIcon }} me-2"></i> Hubungi via {{ $messagingLabel }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-4 mb-4 bg-white">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <div class="position-relative overflow-hidden rounded-4 shadow-sm border bg-light" style="height: 480px;">
                    <img src="{{ $fotoProduk }}" alt="{{ $namaProduk }}" class="w-100 h-100" style="object-fit: cover;">
                </div>
            </div>

            <div class="col-lg-7 ps-lg-4">

                <div class="mb-3">
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #dcfce7; color: #166534; font-size: 12px;">
                        {{ $kategoriNama }}
                    </span>
                </div>


                <div class="store-info-card p-3 rounded-3 mb-3" style="background-color: #fafbfa; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; background-color: #dcfce7; color: #166534; font-size: 16px;">
                            {{ $initials }}
                        </div>
                        <div>
                            @php
                                $storeDetailUrl = $isDashboard 
                                    ? (isset($usaha->id) && Route::has('warga.umkm.detail_usaha') ? route('warga.umkm.detail_usaha', $usaha->id) : '#') 
                                    : (isset($usaha->id) && Route::has('pojok-umkm.detail_usaha') ? route('pojok-umkm.detail_usaha', $usaha->id) : '#');
                            @endphp
                            <h6 class="fw-bold text-dark mb-0 fs-6">
                                <a href="{{ $storeDetailUrl }}" class="text-dark text-decoration-none">
                                    {{ $namaUsaha }}
                                </a>
                            </h6>
                            <div class="text-muted small" style="font-size: 12px;">Pemilik: {{ $ownerName }} ({{ $namaRt }})</div>
                        </div>
                    </div>
                    <div class="pt-2 border-top d-flex align-items-center text-muted" style="font-size: 12px;">
                        <i class="bi bi-geo-alt text-success me-2 fs-6"></i>
                        <span><strong>Alamat Usaha:</strong> {{ $alamatUsaha }}</span>
                    </div>
                </div>


                <h2 class="fw-bold text-dark mb-3 fs-3">{{ $namaProduk }}</h2>

  
                <div class="price-stock-box p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center" style="background-color: #fafbfa; border: 1px solid #f1f5f9;">
                    <div>
                        <div class="text-muted fw-bold text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">
                            HARGA PRODUK
                        </div>
                        <div class="fw-bold text-success" style="font-size: 30px; line-height: 1.1;">
                            Rp {{ $harga }}
                        </div>
                    </div>
                    <div>
                        @if($statusStok === 'habis')
                            <span class="badge rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" style="background-color: #fee2e2; color: #dc2626; font-size: 13px;">
                                <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Habis
                            </span>
                        @elseif($statusStok === 'menipis')
                            <span class="badge rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" style="background-color: #fef3c7; color: #d97706; font-size: 13px;">
                                <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Menipis
                            </span>
                        @else
                            <span class="badge rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" style="background-color: #dcfce7; color: #16a34a; font-size: 13px;">
                                <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Tersedia
                            </span>
                        @endif
                    </div>
                </div>


                <div class="mb-4">
                    <div class="fw-bold text-uppercase text-dark mb-2" style="font-size: 12px; letter-spacing: 0.5px;">
                        DESKRIPSI PRODUK
                    </div>
                    <p class="text-muted small mb-0" style="line-height: 1.7; font-size: 13.5px;">
                        {{ $deskripsi }}
                    </p>
                </div>

    
                <div class="specs-table-wrapper rounded-3 mb-4 overflow-hidden" style="border: 1px solid #e2e8f0; background-color: #fff;">
                    @foreach($specs as $label => $val)
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="background-color: {{ $loop->odd ? '#ffffff' : '#fafbfa' }}; font-size: 13px;">
                            <span class="text-muted">{{ $label }}:</span>
                            <span class="text-dark fw-semibold text-end">{{ $val }}</span>
                        </div>
                    @endforeach
                </div>


                <div class="row g-3 mt-1">
                    @auth
        
                    <div class="col-md-7">
                        <a href="{{ $linkDirectChat }}" target="_blank" class="btn w-100 rounded-3 text-white text-decoration-none p-3 shadow-sm d-flex align-items-center gap-3 text-start h-100" style="background-color: {{ $messagingColor }}; border-color: {{ $messagingColor }};">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.2);">
                                <i class="{{ $messagingIcon }} fs-5 text-white"></i>
                            </div>
                            <div class="text-truncate">
                                <div class="fw-bold fs-6 text-white text-truncate">Pesan via {{ $messagingLabel }} ({{ $formattedWa }})</div>
                                <div class="small text-white-50 text-truncate" style="font-size: 12px;">{{ $ownerName }} - {{ $namaUsaha }}</div>
                            </div>
                        </a>
                    </div>


                    <div class="col-md-5">
                        <a href="{{ $linkShare }}" target="_blank" class="btn w-100 rounded-3 text-decoration-none p-3 shadow-sm d-flex align-items-center justify-content-center gap-2 h-100 fw-semibold" style="background-color: {{ $messagingLightBg }}; border: 1.5px solid {{ $messagingBorderColor }}; color: {{ $messagingColor }}; font-size: 14px;">
                            <i class="{{ $messagingIcon }} fs-5"></i>
                            <span>Bagikan ke {{ $messagingLabel }}</span>
                        </a>
                    </div>
                    @else
                    {{-- Tampilan Pengguna Anonim: Tanpa tombol share --}}
                    <div class="col-12">
                        <a href="{{ $linkDirectChat }}" target="_blank" class="btn w-100 rounded-3 text-white text-decoration-none p-3 shadow-sm d-flex align-items-center gap-3 text-start h-100" style="background-color: {{ $messagingColor }}; border-color: {{ $messagingColor }};">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.2);">
                                <i class="{{ $messagingIcon }} fs-5 text-white"></i>
                            </div>
                            <div class="text-truncate">
                                <div class="fw-bold fs-6 text-white text-truncate">Pesan via {{ $messagingLabel }} ({{ $formattedWa }})</div>
                                <div class="small text-white-50 text-truncate" style="font-size: 12px;">{{ $ownerName }} - {{ $namaUsaha }}</div>
                            </div>
                        </a>
                    </div>
                    @endauth
                </div>

            </div>
        </div>
    </div>


    @include('warga.umkm.components.footerUmkm')

</div>
@endsection
