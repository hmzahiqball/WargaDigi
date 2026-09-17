@php
    $bannerUrl = !empty($usaha->foto_usaha) 
        ? asset('storage/' . $usaha->foto_usaha) 
        : 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=1200&auto=format&fit=crop';
    
    $cleanWa = !empty($usaha->no_wa) ? preg_replace('/[^0-9]/', '', $usaha->no_wa) : null;
    if ($cleanWa && str_starts_with($cleanWa, '0')) { 
        $cleanWa = '62' . substr($cleanWa, 1); 
    }
@endphp

<div class="card border-0 rounded-4 overflow-hidden mb-4 shadow-sm position-relative text-white" 
     style="min-height: 300px; background: linear-gradient(180deg, rgba(0,0,0,0.35) 0%, rgba(0,0,0,0.88) 100%), url('{{ $bannerUrl }}') center/cover no-repeat;">
    <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-between">
        <!-- Top Badges -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <span class="badge px-3 py-2 fw-bold text-dark rounded-2" style="background-color: #ff9800; font-size: 11px; letter-spacing: 0.5px;">
                {{ strtoupper($usaha->kategori_umkm->nama_kategori ?? 'UMKM') }}
            </span>
            <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-circle-fill {{ ($usaha->is_active ?? true) ? 'text-success' : 'text-secondary' }} me-1" style="font-size: 8px;"></i> 
                {{ ($usaha->is_active ?? true) ? 'Toko Sedang Aktif' : 'Toko Non-Aktif' }}
            </span>
        </div>

        <div>
            <h2 class="fw-bold text-white mb-2 fs-1">{{ $usaha->nama_usaha }}</h2>
            <p class="text-white-50 mb-3 fs-6" style="max-width: 720px; line-height: 1.6;">
                {{ $usaha->deskripsi ?? 'Tidak ada deskripsi tertulis untuk usaha ini.' }}
            </p>
            <div class="d-flex flex-wrap align-items-center gap-3 text-white small">
                <div class="d-flex align-items-center gap-2 bg-black bg-opacity-40 px-3 py-2 rounded-3">
                    <i class="bi bi-geo-alt-fill text-warning"></i>
                    <span>{{ $usaha->alamat_usaha ?? 'Alamat belum dilengkapi' }}</span>
                </div>
                @if($cleanWa)
                    <a href="https://wa.me/{{ $cleanWa }}" target="_blank" class="btn btn-success btn-sm rounded-3 px-3 py-2 text-white fw-semibold shadow-sm text-decoration-none d-inline-flex align-items-center gap-2" style="background-color: #25D366; border-color: #25D366;">
                        <i class="bi bi-whatsapp"></i> Hubungi Pemilik Usaha
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
