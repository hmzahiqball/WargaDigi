<div class="col-6 col-md-3">
    @if(isset($link) && $link !== '#')
        <a href="{{ $link }}" class="text-decoration-none">
    @endif
    <div class="card card-custom p-3 h-100 shadow-sm border-0 position-relative">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <small class="{{ $titleClass ?? 'text-muted' }} fw-bold">{{ strtoupper($title ?? '') }}</small>
            <i class="bi {{ $icon ?? 'bi-bar-chart' }} fs-4 {{ $iconColor ?? 'text-success' }}"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">{{ $value ?? '0' }}</h3>
        <small class="{{ $subtitleClass ?? 'text-muted' }}">
            @if(!empty($subtitleIcon))
                <i class="bi {{ $subtitleIcon }} me-1"></i>
            @endif
            {{ $subtitle ?? '' }}
        </small>
    </div>
    @if(isset($link) && $link !== '#')
        </a>
    @endif
</div>
