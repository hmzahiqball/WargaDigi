<div class="col-6">
    <a href="{{ $action['link'] ?? '#' }}" class="text-decoration-none text-dark">
        <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
            <i class="bi {{ $action['icon'] ?? 'bi-star' }} mb-2 fs-3 text-success"></i>
            <span class="fw-semibold small">{{ $action['title'] ?? 'Aksi' }}</span>
        </div>
    </a>
</div>
