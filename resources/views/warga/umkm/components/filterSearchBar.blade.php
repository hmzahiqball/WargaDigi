@php
    $actionUrl = $actionUrl ?? request()->url();
    $searchPlaceholder = $placeholder ?? 'Cari Produk Berdasarkan Nama';
    $showStatus = $showStatus ?? true;

    if (!isset($categoryOptions) || empty($categoryOptions)) {
        if (isset($usaha) && $usaha && $usaha->kategori_produk && $usaha->kategori_produk->isNotEmpty()) {
            $categoryOptions = $usaha->kategori_produk->pluck('nama_kategori')->unique()->values()->all();
        } elseif (isset($kategoriProdukList) && $kategoriProdukList->isNotEmpty()) {
            $categoryOptions = $kategoriProdukList->pluck('nama_kategori')->unique()->values()->all();
        } elseif (isset($daftarKategoriUmkm) && count($daftarKategoriUmkm) > 0 && !isset($usaha)) {
            $categoryOptions = $daftarKategoriUmkm->pluck('nama_kategori')->all();
        } elseif (isset($produk) && count($produk) > 0) {
            $extracted = $produk->map(function($item) {
                return $item->kategori_produk->nama_kategori 
                    ?? $item->kategori_umkm->nama_kategori 
                    ?? $item->usaha->kategori_umkm->nama_kategori 
                    ?? null;
            })->filter()->unique()->values()->all();

            $categoryOptions = !empty($extracted) ? $extracted : ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Koperasi'];
        } else {
            $categoryOptions = ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Koperasi'];
        }
    }
@endphp

<div class="bg-white card border-0 shadow-sm rounded-3 p-3 mb-4">
    <form action="{{ $actionUrl }}" method="GET" class="row g-2 g-md-3 align-items-center">
        @php
            $currentUsahaId = request('usaha_id') ?? ($usahaId ?? null) ?? ($usaha->id ?? null) ?? session('selected_umkm_usaha_id');
        @endphp
        @if(!empty($currentUsahaId))
            <input type="hidden" name="usaha_id" value="{{ $currentUsahaId }}">
        @endif
        
        <!-- Search Input -->
        <div class="{{ $showStatus ? 'col-12 col-md-6' : 'col-12 col-md-8' }}">
            <div class="input-group bg-white">
                <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted ps-3">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 border-end-0 fs-6 bg-white" placeholder="{{ $searchPlaceholder }}" autocomplete="off">
                @if(request('q'))
                    <a href="{{ $actionUrl }}?{{ http_build_query(array_merge(request()->except(['q', 'page']), !empty($currentUsahaId) ? ['usaha_id' => $currentUsahaId] : [])) }}" class="btn btn-white border border-start-0 border-end-0 bg-white text-muted d-flex align-items-center px-2" title="Hapus Pencarian">
                        <i class="bi bi-x-circle-fill text-dark"></i>
                    </a>
                @endif
                <button class="btn border border-start-3 border-end-3 bg-white text-muted px-3 fw-semibold rounded-end-3" type="submit">
                    Cari
                </button>
            </div>
        </div>

        @if($showStatus)
            <!-- Filter Kategori -->
            <div class="col-6 col-md-3 ms-md-auto">
                <select name="kategori" class="form-select border-1 rounded-3 py-2 text-dark bg-white w-100" style="font-size: 0.9rem;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categoryOptions as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-6 col-md-3">
                <select name="status" class="form-select border-1 rounded-3 py-2 text-dark bg-white w-100" style="font-size: 0.9rem;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ in_array(request('status'), ['Tidak Aktif', 'Non-Aktif']) ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="Stok Tersedia" {{ request('status') == 'Stok Tersedia' ? 'selected' : '' }}>Stok Tersedia</option>
                    <option value="Stok Menipis" {{ request('status') == 'Stok Menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="Stok Habis" {{ request('status') == 'Stok Habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
        @else
            <!-- Filter Kategori Tunggal -->
            <div class="col-12 col-md-4 col-lg-auto ms-md-auto">
                <select name="kategori" class="form-select border-1 rounded-3 py-2 text-dark bg-white w-100" style="font-size: 0.9rem;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categoryOptions as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        @endif

    </form>
</div>
