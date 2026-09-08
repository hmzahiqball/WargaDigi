@php
    $isDashboard = isset($isDashboard) ? (Auth::check() ? true : (bool)$isDashboard) : Auth::check();
    if (Auth::check()) {
        $isDashboard = true;
    }
    $layout = $isDashboard ? 'layouts.global' : 'layouts.app';

    $namaUsaha = $usaha->nama_usaha ?? 'Pahatan Kayu Jati Custom';
    $fotoUsaha = !empty($usaha->foto_usaha) 
        ? asset('storage/' . $usaha->foto_usaha) 
        : 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=1000&auto=format&fit=crop';
    $kategoriNama = $usaha->kategori_umkm->nama_kategori ?? $usaha->kategori ?? 'Kerajinan';
    $ownerName = $usaha->user->penduduk->nama_lengkap ?? $usaha->user->username ?? 'Bpk. Jago';
    $namaRt = $usaha->user->penduduk->keluarga->rt->nama_rt ?? 'RT 03';
    $deskripsiUsaha = $usaha->deskripsi ?? 'Karya tangan Pak Budi, RT 03. Cocok untuk hiasan rumah atau hadiah eksklusif. Dibuat dengan dedikasi tinggi menggunakan kayu jati pilihan terbaik.';

    $rawWa = $usaha->no_wa ?? '628123456789';
    $cleanWa = preg_replace('/[^0-9]/', '', $rawWa);
    if (str_starts_with($cleanWa, '0')) {
        $cleanWa = '62' . substr($cleanWa, 1);
    } elseif (!str_starts_with($cleanWa, '62') && !empty($cleanWa)) {
        $cleanWa = '62' . $cleanWa;
    }


    $messagingLabel = \App\Services\Messaging\MessagingService::getLabel();
    $messagingIcon = \App\Services\Messaging\MessagingService::getIcon();
    $messagingDriverName = \App\Services\Messaging\MessagingService::getName();
    $messagingBtnHeroClass = ($messagingDriverName === 'telegram') ? 'btn-telegram-hero' : 'btn-whatsapp-hero';
    $messagingColor = ($messagingDriverName === 'telegram') ? '#24A1DE' : '#22c55e';

    $hubungiText = "Halo {$namaUsaha}, saya tertarik dengan produk usaha Anda di WargaDigi RW 21.";
    $linkDirectChatUsaha = \App\Services\Messaging\MessagingService::getDirectChatUrl($cleanWa, $hubungiText);

    $filterActionUrl = $isDashboard 
        ? (Route::has('umkm.usaha.show') ? route('umkm.usaha.show', $usaha->id ?? '') : request()->url()) 
        : (Route::has('pojok-umkm.detail_usaha') ? route('pojok-umkm.detail_usaha', $usaha->id ?? '') : request()->url());

    $isFromKelola = request('from') === 'kelola' 
        || request('from') === 'kelola_umkm' 
        || str_contains(url()->previous(), 'galeri/kelola') 
        || str_contains(request()->header('referer', ''), 'galeri/kelola');

    $backUrl = $isFromKelola && Route::has('warga.umkm.kelola')
        ? route('warga.umkm.kelola', ['usaha_id' => $usaha->id ?? null])
        : ($isDashboard ? (Route::has('umkm.galeri') ? route('umkm.galeri') : route('warga.umkm.galeri')) : route('pojok-umkm'));
    $backLabel = $isFromKelola ? 'Kembali ke Kelola Usaha' : ($isDashboard ? 'Kembali ke Galeri' : 'Kembali ke Pojok UMKM');
@endphp

@extends($layout)

@section('title', 'Detail UMKM — ' . $namaUsaha)

@section('content')
<div class="{{ $isDashboard ? 'container-fluid px-0' : 'container py-4' }}">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-success mb-1">{{ $namaUsaha }}</h2>
            <div class="d-flex align-items-center gap-2 text-muted small">
                <span><i class="bi bi-person me-1"></i> {{ $ownerName }}</span>
                <span>•</span>
                <span><i class="bi bi-geo-alt me-1"></i> {{ $namaRt }}</span>
                <span>•</span>
                <span><i class="bi bi-tag me-1"></i> {{ $kategoriNama }}</span>
            </div>
        </div>

        <div>
            <a href="{{ $backUrl }}" class="btn btn-white bg-white border rounded-3 px-3 py-2 small fw-semibold text-dark shadow-sm text-decoration-none d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> {{ $backLabel }}
            </a>
        </div>
    </div>


    <div class="card border-0 rounded-4 overflow-hidden mb-4 shadow-sm position-relative text-white" style="min-height: 280px; background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.8) 100%), url('{{ $fotoUsaha }}') center/cover no-repeat;">
        <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-between">
            <div>
                <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold small mb-2 shadow-sm">
                    {{ $kategoriNama }}
                </span>
            </div>
            <div>
                <h2 class="fw-bold text-white mb-2 fs-1">{{ $namaUsaha }}</h2>
                <p class="text-white-50 mb-3 fs-6" style="max-width: 650px; line-height: 1.5;">
                    {{ $deskripsiUsaha }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ $linkDirectChatUsaha }}" target="_blank" class="btn {{ $messagingBtnHeroClass }} rounded-pill px-4 py-2 fw-semibold text-decoration-none shadow-sm text-white d-inline-flex align-items-center gap-2" style="background-color: {{ $messagingColor }}; border-color: {{ $messagingColor }};">
                        <i class="{{ $messagingIcon }}"></i> Hubungi via {{ $messagingLabel }}
                    </a>
                </div>
            </div>
        </div>
    </div>


    @include('components.filterSearchBar', [
        'actionUrl' => $filterActionUrl,
        'placeholder' => 'Cari Produk Usaha...',
        'produk' => $produk ?? null,
        'showStatus' => false,
        'usahaId' => $usaha->id ?? null,
        'usaha' => $usaha ?? null
    ])


    <div class="row g-4 mb-4">
        @forelse($produk as $item)
            @include('warga.umkm.components.cardProduct', ['item' => $item, 'showOwner' => false])
        @empty
           
        @endforelse
    </div>


    @include('components.pagination', ['paginator' => $produk, 'label' => 'produk'])


    @include('warga.umkm.components.footerUmkm')
</div>
@endsection
