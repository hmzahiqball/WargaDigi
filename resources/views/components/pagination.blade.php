@php
    $paginator = $paginator ?? ($items ?? ($produk ?? null));
    $label = $label ?? 'produk';
@endphp

@if(isset($paginator) && $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="custom-pagination-container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 my-4 py-3 border-top">
        {{-- Counter Text --}}
        <div class="text-muted small">
            @if($paginator->total() > 0)
                Menampilkan <span class="fw-semibold text-dark">{{ $paginator->firstItem() ?? 1 }}</span> sampai <span class="fw-semibold text-dark">{{ $paginator->lastItem() ?? $paginator->total() }}</span> dari <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> {{ $label }}
            @else
                Menampilkan <span class="fw-semibold text-dark">0</span> {{ $label }}
            @endif
        </div>

        {{-- Page Buttons --}}
        <nav aria-label="Navigasi Halaman">
            <div class="admin-pagination d-flex align-items-center gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <button class="page-btn" disabled aria-disabled="true" aria-label="Sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-btn text-decoration-none" rel="prev" aria-label="Sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $start = max(1, $currentPage - 1);
                    $end = min($lastPage, $currentPage + 1);

                    // Ensure at least 3 pages displayed if available
                    if ($currentPage == 1) {
                        $end = min($lastPage, 3);
                    } elseif ($currentPage == $lastPage) {
                        $start = max(1, $lastPage - 2);
                    }
                @endphp

                {{-- First Page if not in range --}}
                @if($start > 1)
                    <a href="{{ $paginator->url(1) }}" class="page-btn text-decoration-none">1</a>
                    @if($start > 2)
                        <span class="page-btn dots disabled" aria-disabled="true">&hellip;</span>
                    @endif
                @endif

                {{-- Numbered Page Links --}}
                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $currentPage)
                        <span class="page-btn active" aria-current="page">{{ $i }}</span>
                    @else
                        <a href="{{ $paginator->url($i) }}" class="page-btn text-decoration-none">{{ $i }}</a>
                    @endif
                @endfor

                {{-- Last Page if not in range --}}
                @if($end < $lastPage)
                    @if($end < $lastPage - 1)
                        <span class="page-btn dots disabled" aria-disabled="true">&hellip;</span>
                    @endif
                    <a href="{{ $paginator->url($lastPage) }}" class="page-btn text-decoration-none">{{ $lastPage }}</a>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-btn text-decoration-none" rel="next" aria-label="Berikutnya">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <button class="page-btn" disabled aria-disabled="true" aria-label="Berikutnya">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                @endif
            </div>
        </nav>
    </div>

    @once
    <style>
        .admin-pagination {
            display: flex;
            gap: 0.35rem;
            align-items: center;
        }
        .admin-pagination .page-btn {
            width: 34px;
            height: 34px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 0.45rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            color: #4b5563;
            text-decoration: none;
            user-select: none;
        }
        .admin-pagination .page-btn:hover:not(:disabled):not(.disabled) {
            border-color: #198754;
            color: #198754;
            background-color: #f0fdf4;
        }
        .admin-pagination .page-btn.active {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(25, 135, 84, 0.2);
        }
        .admin-pagination .page-btn:disabled,
        .admin-pagination .page-btn.disabled {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
            background: #f8f9fa;
            border-color: #e9ecef;
            color: #9ca3af;
        }
        .admin-pagination .page-btn.dots {
            border: none;
            background: transparent;
            cursor: default;
            width: 24px;
            color: #9ca3af;
        }
    </style>
    @endonce
@endif
