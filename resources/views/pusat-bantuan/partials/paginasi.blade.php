{{-- Paginasi ringkas berbahasa Indonesia untuk Pusat Bantuan. Parameter: $halaman (LengthAwarePaginator). --}}
@php
    $kini = $halaman->currentPage();
    $akhir = $halaman->lastPage();
    // Tampilkan halaman pertama, terakhir, dan satu halaman di kiri/kanan halaman aktif.
    $nomor = collect([1, $akhir, $kini - 1, $kini, $kini + 1])
        ->filter(fn ($n) => $n >= 1 && $n <= $akhir)->unique()->sort()->values();
@endphp
<nav aria-label="Halaman pertanyaan">
    <ul class="pagination pagination-sm mb-0 pb-paginasi">
        <li class="page-item {{ $halaman->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $halaman->onFirstPage() ? '#' : $halaman->previousPageUrl() }}" aria-label="Sebelumnya" @if($halaman->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
        @foreach($nomor as $i => $n)
            @if($i > 0 && $n - $nomor[$i - 1] > 1)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
            <li class="page-item {{ $n === $kini ? 'active' : '' }}" @if($n === $kini) aria-current="page" @endif>
                <a class="page-link" href="{{ $halaman->url($n) }}">{{ $n }}</a>
            </li>
        @endforeach
        <li class="page-item {{ $halaman->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $halaman->hasMorePages() ? $halaman->nextPageUrl() : '#' }}" aria-label="Berikutnya" @unless($halaman->hasMorePages()) tabindex="-1" aria-disabled="true" @endunless>
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
