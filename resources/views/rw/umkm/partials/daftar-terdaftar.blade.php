{{--
    Daftar UMKM yang sudah disetujui (kartu + filter + pagination). Reusable.
    Variabel: $daftarUsahaTerdaftar (paginator UmkmUsaha), $isPimpinan (bool, true = hanya pantau),
              $actionUrl (opsional, target form filter), $daftarKategoriUmkm (opsional).
--}}
<div class="mb-4" data-testid="daftar-umkm-terdaftar">
    @include('components.filterSearchBar', [
        'actionUrl' => $actionUrl ?? route('rw.umkm.index'),
        'placeholder' => 'Cari nama usaha, pemilik, atau kategori UMKM...',
        'daftarKategoriUmkm' => $daftarKategoriUmkm ?? null,
    ])

    @if($daftarUsahaTerdaftar->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($daftarUsahaTerdaftar as $item)
                <div class="col-md-6 col-lg-4">
                    @include('rw.umkm.components.cardUsahaTerdaftar', ['item' => $item, 'isPimpinan' => $isPimpinan])
                </div>
            @endforeach
        </div>

        @include('components.pagination', ['paginator' => $daftarUsahaTerdaftar, 'label' => 'usaha'])
    @else
        <div class="card border shadow-sm rounded-4 p-5 bg-white text-center">
            <div class="rounded-circle bg-light d-inline-flex p-3 mb-3 mx-auto text-muted">
                <i class="bi bi-shop fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Belum Ada UMKM Terdaftar</h5>
            <p class="text-muted small mb-0">
                {{ request()->hasAny(['q', 'kategori', 'status']) ? 'Tidak ada usaha yang cocok dengan pencarian atau filter.' : 'Belum ada usaha warga yang berstatus disetujui saat ini.' }}
            </p>
        </div>
    @endif
</div>
