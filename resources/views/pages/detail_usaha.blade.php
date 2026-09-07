@php
    $isDashboard = $isDashboard ?? (request()->is('warga/*') || (Auth::check() && !request()->is('usaha/*') && !request()->is('pojok-umkm/*')));
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
        ? (Route::has('warga.umkm.usaha.show') ? route('warga.umkm.usaha.show', $usaha->id ?? '') : (Route::has('warga.umkm.detail_usaha') ? route('warga.umkm.detail_usaha', $usaha->id ?? '') : request()->url()))
        : (Route::has('pojok-umkm.detail_usaha') ? route('pojok-umkm.detail_usaha', $usaha->id ?? '') : request()->url());
@endphp

@extends($layout)

@section('title', 'Detail UMKM — ' . $namaUsaha)

@section('content')
<div class="{{ $isDashboard ? 'container-fluid px-0' : 'container py-4' }}">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-success mb-1">{{ $namaUsaha }}</h2>
            <div class="text-muted small">
                <i class="bi bi-shop me-1"></i> {{ $ownerName }} ({{ $namaRt }})
            </div>
        </div>


        <div>
            @if($isDashboard)
                <a href="{{ route('warga.umkm.galeri') }}" class="btn btn-white bg-white border rounded-3 px-3 py-2 small fw-semibold text-dark shadow-sm text-decoration-none d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Galeri UMKM
                </a>
            @else
                <a href="{{ route('pojok-umkm') }}" class="btn btn-white bg-white border rounded-3 px-3 py-2 small fw-semibold text-dark shadow-sm text-decoration-none d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Pojok UMKM
                </a>
            @endif
        </div>
    </div>


    <div class="mb-5">
        <div class="usaha-banner-card rounded-4 position-relative overflow-hidden" style="background-image: url('{{ $fotoUsaha }}'); min-height: 320px;">
            <div class="banner-overlay"></div>
            <div class="banner-content p-4 p-md-5">
                <span class="badge badge-featured px-3 py-2 mb-3 rounded-2 fw-semibold" style="background-color: #f59e0b; color: #fff; font-size: 13px;">
                    {{ $kategoriNama }} Unggulan
                </span>
                <h3 class="fw-bold mb-2 text-white fs-3">
                    {{ $usaha && $usaha->nama_usaha ? 'Seni ' . $usaha->nama_usaha . ' Asli Tanimulya' : 'Seni Pahat Kayu Jati Asli Tanimulya' }}
                </h3>
                <p class="text-white-50 mb-4 small" style="max-width: 650px; line-height: 1.6;">
                    {{ $deskripsiUsaha }}
                </p>
                <div>
                    <a href="{{ $linkDirectChatUsaha }}" target="_blank" class="btn {{ $messagingBtnHeroClass }} rounded-pill px-4 py-2 fw-semibold text-decoration-none shadow-sm text-white d-inline-flex align-items-center gap-2" style="background-color: {{ $messagingColor }}; border-color: {{ $messagingColor }};">
                        <i class="{{ $messagingIcon }}"></i> Hubungi via {{ $messagingLabel }}
                    </a>
                </div>
            </div>
        </div>
    </div>


    @include('warga.umkm.components.filterSearchBar', [
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
