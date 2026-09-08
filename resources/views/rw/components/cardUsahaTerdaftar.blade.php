@php
    $fotoUsahaUrl = !empty($item->foto_usaha) 
        ? asset('storage/' . $item->foto_usaha) 
        : 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=600&auto=format&fit=crop';
    $totalProduk = $item->produk ? $item->produk->count() : 0;
@endphp

<div class="card border shadow-sm rounded-4 overflow-hidden h-100 bg-white hover-shadow transition-all d-flex flex-column justify-content-between">
    <div>
        {{-- Banner Foto Toko --}}
        <div class="position-relative overflow-hidden" style="height: 180px;">
            <img src="{{ $fotoUsahaUrl }}" class="w-100 h-100 object-fit-cover" alt="{{ $item->nama_usaha }}">
            <div class="position-absolute top-0 start-0 m-3">
                <span class="badge px-3 py-1 fw-bold text-dark rounded-2 small" style="background-color: #00ffeeff;">
                    {{ strtoupper($item->kategori_umkm->nama_kategori ?? 'UMKM') }}
                </span>
            </div>
            <div class="position-absolute top-0 end-0 m-3">
                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 small">
                    <i class="bi bi-circle-fill {{ ($item->is_active ?? true) ? 'text-success' : 'text-secondary' }} me-1" style="font-size: 7px;"></i>
                    {{ ($item->is_active ?? true) ? 'Aktif' : 'Non-Aktif' }}
                </span>
            </div>
        </div>

        <div class="p-4">
            <h5 class="fw-bold text-dark mb-1 text-truncate" title="{{ $item->nama_usaha }}">
                {{ $item->nama_usaha }}
            </h5>
            
            <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                <span><i class="bi bi-person text-success me-1"></i> {{ $item->pemilik->penduduk->nama_lengkap ?? $item->pemilik->username ?? 'Warga' }}</span>
                <span>•</span>
                <span>RT {{ $item->pemilik->penduduk->keluarga->rt->nama_rt ?? '12' }}</span>
            </div>

            <p class="text-muted small mb-3" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px;">
                {{ $item->deskripsi ?? 'Tidak ada deskripsi usaha.' }}
            </p>

            <div class="d-flex flex-column gap-1 small text-muted mt-5">
                <div class="text-truncate">
                    <i class="bi bi-geo-alt me-1 text-dark"></i> {{ $item->alamat_usaha ?? 'Alamat belum diisi' }}
                </div>
                @if(!empty($item->no_wa))
                    <div>
                        <i class="bi bi-telephone me-1 text-dark"></i> {{ $item->no_wa }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="px-4 py-3  border-top d-flex align-items-center justify-content-between">
        <span class="small fw-semibold text-muted">
            <i class="bi bi-box-seam text-success me-1"></i> {{ $totalProduk }} Produk
        </span>
        
        <a href="{{ route('rw.umkm.usaha.detail', $item->id) }}" 
           class="btn btn-success btn-sm rounded-3 px-3 py-2 text-white fw-semibold shadow-sm text-decoration-none d-inline-flex align-items-center gap-1"
           style="background-color: #5b9b76; border-color: #5b9b76;">
            <span>Detail & Produk</span>
        </a>
    </div>
</div>
