<div class="timeline-item">
    <div class="timeline-icon">
        <i class="bi {{ $activity['icon'] ?? 'bi-bell' }}"></i>
    </div>
    <div class="timeline-content">
        <div class="fw-bold text-dark mb-1 timeline-title">
            @if(!empty($activity['link']))
                <a href="{{ $activity['link'] }}" class="text-dark text-decoration-none">{{ $activity['title'] ?? '' }}</a>
            @else
                {{ $activity['title'] ?? '' }}
            @endif
        </div>
        <div class="text-muted text-xs mb-2">{{ $activity['time'] ?? '' }}</div>
        @if(!empty($activity['badge']))
            <span class="badge uppercase-badge {{ $activity['badge_class'] ?? 'bg-secondary' }}">
                {{ $activity['badge'] }}
            </span>
        @endif
        @if(!empty($activity['quote']))
            <div class="quote-box p-3 mt-2 rounded">
                &ldquo;{{ $activity['quote'] }}&rdquo;
            </div>
        @endif
    </div>
</div>
