@extends('layouts.global')

@section('title', 'Galeri UMKM')

@section('content')
<div class="container-fluid px-0">
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-7">
            <h2 class="fw-bold text-success mb-2">Galeri UMKM</h2>
            <p class="text-muted mb-0" style="max-width: 680px;">
                Dukung usaha tetangga, majukan ekonomi lokal RW 21 Tanimulya. Temukan berbagai produk dan jasa terbaik dari komunitas kita.
            </p>
        </div>
        <div class="col-md-5 text-md-end d-flex gap-2 justify-content-md-end flex-wrap">
            <button type="button" class="btn btn-success fw-semibold px-3 py-2 rounded-3 text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDaftarUsaha">
                <i class="bi bi-plus-circle me-1"></i> Daftarkan Usaha Anda
            </button>
            <a href="{{ route('warga.umkm.kelola') }}" class="btn text-white fw-semibold px-3 py-2 rounded-3 shadow-sm" style="background-color: #5b9b76;">
                <i class="bi bi-shop me-1"></i> Kelola UMKM Anda
            </a>
        </div>
    </div>

    @if(isset($pendingUsahaCount) && $pendingUsahaCount > 0)
    <div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 mb-4 rounded-3 gap-3" style="background-color: #fffbeb; border-left: 5px solid #f59e0b !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(245, 158, 11, 0.2); width: 42px; height: 42px;">
                <i class="bi bi-clock-history text-warning fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Pendaftaran Usaha Anda Sedang Ditinjau RW</h6>
                <p class="text-muted small mb-0">Terdapat <strong>{{ $pendingUsahaCount }}</strong> usaha yang Anda daftarkan berstatus Pending dan menunggu verifikasi dari Pengurus RW.</p>
            </div>
        </div>
        <a href="{{ route('warga.umkm.kelola') }}" class="btn btn-warning btn-sm text-dark fw-bold rounded-pill px-3 py-2 text-nowrap shadow-sm">
            <i class="bi bi-shop me-1"></i> Cek di Kelola UMKM
        </a>
    </div>
    @endif

    @php
        $hero1 = isset($produkUnggulan) ? $produkUnggulan->get(0) : null;
        $hero2 = isset($produkUnggulan) ? $produkUnggulan->get(1) : null;
        $hero3 = isset($produkUnggulan) ? $produkUnggulan->get(2) : null;

        $defaultImg = function($kat) {
            return match(strtolower($kat ?? '')) {
                'kuliner', 'makanan & minuman' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&auto=format&fit=crop',
                'fashion', 'pakaian muslimah' => 'https://images.unsplash.com/photo-1601924994987-69e26d50dc26?w=800&auto=format&fit=crop',
                'sembako', 'sembako warga', 'koperasi' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&auto=format&fit=crop',
                'jasa', 'perawatan & servis' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=800&auto=format&fit=crop',
                default => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop'
            };
        };

        $kat1 = $hero1->kategori_produk->nama_kategori ?? $hero1->usaha->kategori_umkm->nama_kategori ?? 'Kerajinan';
        $bg1 = ($hero1 && !empty($hero1->foto_produk)) 
            ? (str_starts_with($hero1->foto_produk, 'http') ? $hero1->foto_produk : asset('storage/' . $hero1->foto_produk)) 
            : $defaultImg($kat1);
        $badgeStyle1 = match(strtolower($kat1)) {
            'kuliner', 'makanan & minuman' => 'badge-kuliner',
            'fashion', 'pakaian muslimah' => 'badge-fashion',
            'kerajinan', 'aksesoris & hiasan' => 'badge-kerajinan',
            'jasa', 'perawatan & servis' => 'badge-jasa',
            'sembako', 'sembako warga', 'koperasi' => 'badge-koperasi',
            default => 'badge-kerajinan'
        };
        $detailUrl1 = $hero1 ? route('produk.show', $hero1->id) : route('warga.umkm.usaha.show');
        $noWa1 = preg_replace('/[^0-9]/', '', $hero1->usaha->no_wa ?? '628123456789');
        if (str_starts_with($noWa1, '0')) { $noWa1 = '62' . substr($noWa1, 1); }
        $hero1Message = 'Halo, saya tertarik dengan produk ' . ($hero1->nama_produk ?? 'Pahatan Kayu Jati Custom');
        $linkDirectHero1 = \App\Services\Messaging\MessagingService::getDirectChatUrl($noWa1 ?: '628123456789', $hero1Message);
        $messagingLabel = \App\Services\Messaging\MessagingService::getLabel();
        $messagingIcon = \App\Services\Messaging\MessagingService::getIcon();
        $messagingSolidBtn = \App\Services\Messaging\MessagingService::getSolidButtonClass();

        $kat2 = $hero2->kategori_produk->nama_kategori ?? $hero2->usaha->kategori_umkm->nama_kategori ?? 'Kuliner';
        $bg2 = ($hero2 && !empty($hero2->foto_produk)) 
            ? (str_starts_with($hero2->foto_produk, 'http') ? $hero2->foto_produk : asset('storage/' . $hero2->foto_produk)) 
            : $defaultImg($kat2);
        $badgeStyle2 = match(strtolower($kat2)) {
            'kuliner', 'makanan & minuman' => 'badge-kuliner',
            'fashion', 'pakaian muslimah' => 'badge-fashion',
            'kerajinan', 'aksesoris & hiasan' => 'badge-kerajinan',
            'jasa', 'perawatan & servis' => 'badge-jasa',
            'sembako', 'sembako warga', 'koperasi' => 'badge-koperasi',
            default => 'badge-kuliner'
        };
        $detailUrl2 = $hero2 ? route('produk.show', $hero2->id) : route('warga.umkm.usaha.show');

        $kat3 = $hero3->kategori_produk->nama_kategori ?? $hero3->usaha->kategori_umkm->nama_kategori ?? 'Fashion';
        $bg3 = ($hero3 && !empty($hero3->foto_produk)) 
            ? (str_starts_with($hero3->foto_produk, 'http') ? $hero3->foto_produk : asset('storage/' . $hero3->foto_produk)) 
            : $defaultImg($kat3);
        $badgeStyle3 = match(strtolower($kat3)) {
            'kuliner', 'makanan & minuman' => 'badge-kuliner',
            'fashion', 'pakaian muslimah' => 'badge-fashion',
            'kerajinan', 'aksesoris & hiasan' => 'badge-kerajinan',
            'jasa', 'perawatan & servis' => 'badge-jasa',
            'sembako', 'sembako warga', 'koperasi' => 'badge-koperasi',
            default => 'badge-fashion'
        };
        $detailUrl3 = $hero3 ? route('produk.show', $hero3->id) : route('warga.umkm.usaha.show');
    @endphp

    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0">Produk Unggulan RW 21</h5>
            <a href="{{ route('warga.umkm.koleksi', 'unggulan') }}" class="text-success text-decoration-none small fw-semibold">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="umkm-hero-card h-100 position-relative" style="min-height: 380px; background-image: url('{{ $bg1 }}'); cursor: pointer;" onclick="if(!event.target.closest('a')) window.location.href='{{ $detailUrl1 }}'">
                    <div class="umkm-hero-overlay"></div>
                    <div class="umkm-hero-content">
                        <span class="badge {{ $badgeStyle1 }} px-3 py-2 mb-2 fw-semibold rounded-2">{{ $kat1 }}</span>
                        <h3 class="fw-bold mb-2 text-white">
                            <a href="{{ $detailUrl1 }}" class="text-white text-decoration-none">{{ $hero1->nama_produk ?? 'Pahatan Kayu Jati Custom' }}</a>
                        </h3>
                        <p class="text-white-50 mb-3 small">
                            {{ Str::limit($hero1->deskripsi ?? 'Karya tangan Pak Budi, RT 03. Cocok untuk hiasan rumah atau hadiah eksklusif. Dibuat dengan dedikasi tinggi menggunakan kayu jati pilihan terbaik.', 120) }}
                        </p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fs-4 fw-bold text-white">Rp {{ number_format($hero1->harga ?? 350000, 0, ',', '.') }}</span>
                            <div class="d-flex gap-2">
                                <a href="{{ $linkDirectHero1 }}" target="_blank" onclick="event.stopPropagation();" class="btn {{ $messagingSolidBtn }} rounded-pill px-4 py-2 text-white text-decoration-none fw-semibold">
                                    <i class="{{ $messagingIcon }} me-1"></i> {{ $messagingLabel }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  

            <div class="col-lg-5 d-flex flex-column gap-3">
                <div class="umkm-hero-card flex-grow-1" style="min-height: 180px; background-image: url('{{ $bg2 }}'); cursor: pointer;" onclick="if(!event.target.closest('a')) window.location.href='{{ $detailUrl2 }}'">
                    <div class="umkm-hero-overlay"></div>
                    <div class="umkm-hero-content">
                        <span class="badge {{ $badgeStyle2 }} px-2 py-1 mb-1 fw-bold rounded-2">{{ $kat2 }}</span>
                        <h5 class="fw-bold mb-1 text-white">
                            <a href="{{ $detailUrl2 }}" class="text-white text-decoration-none">{{ $hero2->nama_produk ?? 'Kue Tampah Ibu Sari' }}</a>
                        </h5>
                        <span class="fw-semibold text-white">Rp {{ number_format($hero2->harga ?? 150000, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="umkm-hero-card flex-grow-1" style="min-height: 180px; background-image: url('{{ $bg3 }}'); cursor: pointer;" onclick="if(!event.target.closest('a')) window.location.href='{{ $detailUrl3 }}'">
                    <div class="umkm-hero-overlay"></div>
                    <div class="umkm-hero-content">
                        <span class="badge {{ $badgeStyle3 }} px-2 py-1 mb-1 fw-bold rounded-2">{{ $kat3 }}</span>
                        <h5 class="fw-bold mb-1 text-white">
                            <a href="{{ $detailUrl3 }}" class="text-white text-decoration-none">{{ $hero3->nama_produk ?? 'Batik Tulis Tanimulya' }}</a>
                        </h5>
                        <span class="fw-semibold text-white">Rp {{ number_format($hero3->harga ?? 250000, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($produkTerbaru) && count($produkTerbaru) > 0)
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    Produk Terbaru Masuk Sistem
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small ms-2 fw-semibold" style="font-size: 11px;">
                        Baru
                    </span>
                </h5>
                <p class="text-muted small mb-0">Koleksi produk teranyar yang baru didaftarkan oleh warga RW 21.</p>
            </div>
            <div>
                <a href="{{ route('warga.umkm.koleksi', 'terbaru') }}" class="text-success text-decoration-none small fw-semibold text-nowrap d-inline-flex align-items-center gap-1">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="carousel-products-wrapper position-relative">
            <button type="button" 
                    class="btn btn-white bg-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute top-50 start-0 translate-middle-y carousel-nav-btn ms-2 ms-md-3" 
                    id="btnPrevTerbaru" 
                    style="width: 42px; height: 42px; z-index: 20; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease;" 
                    title="Geser Kiri"
                    aria-label="Geser Kiri">
                <i class="bi bi-chevron-left fs-5 text-dark"></i>
            </button>

            <div class="d-flex gap-3 overflow-x-auto py-2 px-1" id="trackProdukTerbaru" style="scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($produkTerbaru as $item)
                    <div class="flex-shrink-0" style="width: 270px;">
                        @include('warga.umkm.components.cardProduct', [
                            'item' => $item, 
                            'colClass' => false, 
                            'showOwner' => true
                        ])
                    </div>
                @endforeach
            </div>

            <button type="button" 
                    class="btn btn-white bg-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute top-50 end-0 translate-middle-y carousel-nav-btn me-2 me-md-3" 
                    id="btnNextTerbaru" 
                    style="width: 42px; height: 42px; z-index: 20; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease;" 
                    title="Geser Kanan"
                    aria-label="Geser Kanan">
                <i class="bi bi-chevron-right fs-5 text-dark"></i>
            </button>
        </div>
    </div>
    @endif

    @include('warga.umkm.components.filterSearchBar', [
        'actionUrl' => route('warga.umkm.galeri'),
        'placeholder' => 'Cari Toko / Produk Berdasarkan Nama',
        'produk' => $produk ?? null,
        'showStatus' => false
    ])

    <div class="row g-4 mb-4">
        @forelse($produk as $item)
            @include('warga.umkm.components.cardProduct', ['item' => $item, 'showOwner' => true])
        @empty
            <div class="col-12 text-center py-5">
                <div class="py-4">
                    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                    <h6 class="fw-bold text-dark">Tidak ada produk ditemukan</h6>
                    <p class="text-muted small mb-3">Coba gunakan kata kunci pencarian lain atau pilih kategori lain.</p>
                    <a href="{{ route('warga.umkm.galeri') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset Pencarian
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @include('components.pagination', ['paginator' => $produk, 'label' => 'produk'])

    @include('warga.umkm.components.footerUmkm')
</div>

<div class="modal fade" id="modalDaftarUsaha" tabindex="-1" aria-labelledby="modalDaftarUsahaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 align-items-center">
                <h4 class="modal-title fw-bold text-dark" id="modalDaftarUsahaLabel">Daftarkan Usaha Anda</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('warga.umkm.store-usaha') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shop fs-5"></i> Informasi Bisnis
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1">Nama Usaha</label>
                                <input type="text" name="nama_usaha" class="form-control rounded-3 py-2 px-3 border-1 shadow-none" placeholder="Contoh: Keripik Tempe Renyah" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1">Kategori</label>
                                <select name="kategori_umkm_id" class="form-select rounded-3 py-2 px-3 border-1 shadow-none text-dark" required>
                                    <option value="" selected disabled>Pilih Kategori</option>
                                    @if(isset($daftarKategoriUmkm) && count($daftarKategoriUmkm) > 0)
                                        @foreach($daftarKategoriUmkm as $kat)
                                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1">Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control rounded-3 py-2 px-3 border-1 shadow-none" rows="3" placeholder="Ceritakan sedikit tentang produk atau jasa Anda..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt fs-5"></i> Kontak & Lokasi
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1">Alamat Lengkap (RT/RW)</label>
                                <input type="text" name="alamat_usaha" class="form-control rounded-3 py-2 px-3 border-1 shadow-none" placeholder="Jl. Mawar No. 12, RT 03/RW 12" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1">No. WhatsApp Business</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-dark rounded-start-3 small fw-semibold px-3">+62</span>
                                    <input type="text" name="no_wa" class="form-control rounded-end-3 py-2 px-3 border-1 shadow-none" placeholder="8123456789" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-image fs-5"></i> Media
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="upload-dashed-box text-center p-4 rounded-3" onclick="document.getElementById('inputFotoSampul').click()">
                                    <i class="bi bi-image fs-3 text-dark mb-2 d-block"></i>
                                    <span class="small fw-semibold text-dark d-block" id="labelFotoSampul">Upload Foto Sampul</span>
                                    <input type="file" name="foto_sampul" id="inputFotoSampul" class="d-none" accept="image/*" onchange="previewFileName(this, 'labelFotoSampul')">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-end gap-2 px-4 py-3 border-top" style="background-color: #f1f8f3; border-top-color: #e2ece4 !important; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 small fw-semibold text-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 small fw-semibold text-white" style="background-color: #5b9b76; border-color: #5b9b76;">Kirim Pendaftaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewFileName(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        label.innerText = input.files[0].name;
        label.classList.remove('text-secondary');
        label.classList.add('text-success', 'fw-bold');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('trackProdukTerbaru');
    const prevBtn = document.getElementById('btnPrevTerbaru');
    const nextBtn = document.getElementById('btnNextTerbaru');

    if (track && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function() {
            track.scrollBy({ left: -290, behavior: 'smooth' });
        });
        nextBtn.addEventListener('click', function() {
            track.scrollBy({ left: 290, behavior: 'smooth' });
        });
    }
});
</script>
@endpush
@endsection
