{{--
    Daftar kartu pengajuan UMKM (beserta modal detail pemohon) + pagination + keadaan kosong. Reusable.
    Variabel: $daftar (paginator UmkmUsaha), $isPimpinan (bool), $label (teks pagination),
              $kosongJudul, $kosongPesan.
--}}
@if($daftar->count() > 0)
    <div class="d-flex flex-column gap-3 mb-4">
        @foreach($daftar as $item)
            @include('rw.umkm.components.cardApprovalUmkm', ['item' => $item, 'isPimpinan' => $isPimpinan])
            @include('rw.umkm.components.modalDetailPengaju', ['item' => $item, 'isPimpinan' => $isPimpinan])
        @endforeach
    </div>

    @include('components.pagination', ['paginator' => $daftar, 'label' => $label])
@else
    <div class="card border shadow-sm rounded-4 p-5 bg-white text-center">
        <div class="p-3 mb-3 mx-auto">
            <i class="bi bi-check2-circle text-success fs-1"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">{{ $kosongJudul }}</h5>
        <p class="text-muted small mb-0">{{ $kosongPesan }}</p>
    </div>
@endif
