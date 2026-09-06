@php
    $colClass = $colClass ?? 'col-lg-4 col-md-6';
    $foto = !empty($usaha->foto_usaha) 
        ? asset('storage/' . $usaha->foto_usaha) 
        : 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop';
    $namaUsaha = $usaha->nama_usaha ?? 'Nama Usaha';
    $kategori = $usaha->kategori_umkm->nama_kategori ?? $usaha->kategori ?? 'UMKM';
    $owner = $usaha->user->penduduk->nama_lengkap ?? $usaha->user->username ?? 'Warga';
    $rt = $usaha->user->penduduk->keluarga->rt->nama_rt ?? 'RT 01';
    $detailUrl = route('pojok-umkm.detail_usaha', $usaha->id);
    $totalProduk = $usaha->produk ? $usaha->produk->where('status_produk', 'Aktif')->count() : 0;
    
    // Hitung harga terendah dari produk aktif
    $minHarga = $usaha->produk 
        ? $usaha->produk->where('status_produk', 'Aktif')->where('harga', '>', 0)->min('harga') 
        : null;
    if (!$minHarga && $usaha->produk) {
        $minHarga = $usaha->produk->where('harga', '>', 0)->min('harga');
    }
    $badgeHarga = $minHarga ? 'Mulai Rp ' . number_format($minHarga, 0, ',', '.') : 'Harga Bervariasi';
@endphp

<div class="{{ $colClass }}">
    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-usaha-hover" 
         onclick="window.location.href='{{ $detailUrl }}'" 
         style="cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease; background-color: #fff;">
        <div class="position-relative overflow-hidden" style="height: 200px;">
            <img src="{{ $foto }}" alt="{{ $namaUsaha }}" class="w-100 h-100 object-fit-cover transition-img">
            <span class="badge bg-white text-dark border px-3 py-1 rounded-pill position-absolute top-0 end-0 m-3 shadow-sm small fw-semibold">
                {{ $kategori }}
            </span>
            @if($badgeHarga)
                <span class="badge bg-white text-success border-0 px-3 py-1 rounded-pill position-absolute bottom-0 start-0 m-3 fw-bold d-inline-flex align-items-center" 
                      style="font-size: 0.82rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;">
                    <i class="bi bi-tag-fill me-1 text-success"></i> {{ $badgeHarga }}
                </span>
            @endif
        </div>
        <div class="card-body p-4 d-flex flex-column">
            <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                <i class="bi bi-person-circle text-success"></i>
                <span>{{ $owner }} ({{ $rt }})</span>
            </div>
            <h5 class="fw-bold text-dark mb-2">
                <a href="{{ $detailUrl }}" class="text-dark text-decoration-none hover-success">
                    {{ $namaUsaha }}
                </a>
            </h5>
            <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $usaha->deskripsi ?? 'Pusat usaha dan jasa mandiri warga RW 21 Tanimulya.' }}
            </p>
            <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small fw-semibold">
                    <i class="bi bi-box-seam me-1"></i> {{ $totalProduk }} Produk
                </span>
                <span class="text-success small fw-semibold d-inline-flex align-items-center gap-1">
                    Kunjungi Toko <i class="bi bi-arrow-right"></i>
                </span>
            </div>
        </div>
    </div>
</div>
