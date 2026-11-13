<div class="col-12 col-sm-6">
    <div class="card card-custom p-3 shadow-sm border-0 h-100 position-relative">
        <div class="bg-light rounded p-3 text-center mb-3">
            <i class="bi {{ $doc['icon'] ?? 'bi-file-earmark' }} text-secondary fs-1"></i>
        </div>
        <div class="d-flex gap-1 flex-wrap mb-2">
            @if(!empty($doc['jenis']))
                <span class="badge bg-light text-secondary border">{{ $doc['jenis'] }}</span>
            @endif
            @if(!empty($doc['asal']))
                <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $doc['asal'] }}</span>
            @endif
        </div>
        <h6 class="fw-bold doc-title text-truncate mb-1">
            @if(!empty($doc['link']))
                <a href="{{ $doc['link'] }}" class="text-dark text-decoration-none stretched-link">{{ $doc['title'] ?? 'Konten' }}</a>
            @else
                {{ $doc['title'] ?? 'Konten' }}
            @endif
        </h6>
        <p class="text-muted small doc-desc mb-3">{{ $doc['desc'] ?? '' }}</p>
        <div class="d-flex justify-content-between align-items-center mt-auto">
            <span class="badge uppercase-badge {{ $doc['status_class'] ?? 'bg-secondary' }}">{{ $doc['status'] ?? '' }}</span>
            <span class="text-muted text-xs">{{ $doc['date'] ?? '' }}</span>
        </div>
    </div>
</div>
