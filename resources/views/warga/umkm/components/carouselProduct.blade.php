@php
    $items = $items ?? $produk ?? $daftarProdukTerbaru ?? $produkTerbaru ?? [];
    $cardWidth = $cardWidth ?? 270;
    $showOwner = $showOwner ?? true;
    $showEmpty = $showEmpty ?? false;
@endphp

@if(isset($items) && count($items) > 0)
    <div class="carousel-products-wrapper position-relative mb-4" data-carousel-wrapper>
        <button type="button" 
                class="btn btn-white bg-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute top-50 start-0 translate-middle-y carousel-nav-btn ms-1 ms-md-2" 
                data-carousel-prev
                style="width: 42px; height: 42px; z-index: 20; cursor: pointer; transition: all 0.2s ease;" 
                title="Geser Kiri"
                aria-label="Geser Kiri">
            <i class="bi bi-chevron-left fs-5 text-dark"></i>
        </button>

        <div class="d-flex gap-3 overflow-x-auto py-2 px-1 carousel-track" 
             data-carousel-track 
             style="scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($items as $item)
                <div class="flex-shrink-0" style="width: {{ $cardWidth }}px;">
                    @include('warga.umkm.components.cardProduct', [
                        'item' => $item, 
                        'colClass' => false, 
                        'showOwner' => $showOwner
                    ])
                </div>
            @endforeach
        </div>

        <button type="button" 
                class="btn btn-white bg-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute top-50 end-0 translate-middle-y carousel-nav-btn me-1 me-md-2" 
                data-carousel-next
                style="width: 42px; height: 42px; z-index: 20; cursor: pointer; transition: all 0.2s ease;" 
                title="Geser Kanan"
                aria-label="Geser Kanan">
            <i class="bi bi-chevron-right fs-5 text-dark"></i>
        </button>
    </div>
@elseif($showEmpty)
    <div class="card card-custom p-4 text-center mb-4 shadow-sm border-0">
        <div class="py-3">
            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center p-3 mb-3 text-success" style="width: 64px; height: 64px;">
                <i class="bi bi-shop fs-2"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Belum Ada Produk UMKM Aktif</h6>
            <p class="text-muted small mb-3" style="max-width: 480px; margin: 0 auto;">Miliki usaha atau produk rumahan? Promosikan produk kuliner, kriya, atau jasa Anda ke seluruh warga RW 21.</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ route('warga.umkm.daftar') }}" class="btn btn-success px-4 py-2 small fw-semibold rounded-3 text-white shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Daftarkan UMKM Anda
                </a>
                <a href="{{ route('warga.umkm.galeri') }}" class="btn btn-outline-success px-3 py-2 small fw-semibold rounded-3">
                    Kunjungi Galeri UMKM
                </a>
            </div>
        </div>
    </div>
@endif

@once
@push('styles')
<style>
.carousel-track::-webkit-scrollbar {
    display: none;
}
.carousel-track {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.carousel-nav-btn {
    opacity: 0.92;
    background-color: #ffffff !important;
}
.carousel-nav-btn:hover {
    opacity: 1;
    transform: translateY(-50%) scale(1.08) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-carousel-wrapper]').forEach(function(wrapper) {
        const track = wrapper.querySelector('[data-carousel-track]');
        const prevBtn = wrapper.querySelector('[data-carousel-prev]');
        const nextBtn = wrapper.querySelector('[data-carousel-next]');

        if (track && prevBtn && nextBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                track.scrollBy({ left: -290, behavior: 'smooth' });
            });
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                track.scrollBy({ left: 290, behavior: 'smooth' });
            });
        }
    });
});
</script>
@endpush
@endonce
