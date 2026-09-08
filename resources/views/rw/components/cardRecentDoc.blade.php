<div class="col-12 col-sm-6">
    <div class="card card-custom p-3 shadow-sm border-0 h-100">
        <div class="bg-light rounded p-3 text-center mb-3">
            <i class="bi {{ $doc['icon'] ?? 'bi-file-earmark' }} text-secondary fs-1"></i>
        </div>
        <div class="d-flex justify-content-between align-items-start mb-1">
            <h6 class="fw-bold doc-title text-truncate mb-0">{{ $doc['title'] ?? 'Dokumen' }}</h6>
            <button class="btn btn-link text-muted p-0 border-0 ms-1" type="button">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
        </div>
        <p class="text-muted small doc-desc mb-3">{{ $doc['desc'] ?? '' }}</p>
        <div class="d-flex justify-content-between align-items-center mt-auto">
            <span class="badge uppercase-badge {{ $doc['status_class'] ?? 'bg-secondary' }}">{{ $doc['status'] ?? 'DRAFT' }}</span>
            <span class="text-muted text-xs">{{ $doc['date'] ?? '' }}</span>
        </div>
    </div>
</div>
