@extends('layouts.global')

@section('title', $agenda->judul_agenda . ' - Agenda Warga')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
    .agenda-show-hero {
        height: 350px;
        position: relative;
        background: #000;
        overflow: hidden;
    }
    .agenda-show-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.75;
    }
    .content-html img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 16px 0;
    }
    .content-html p {
        margin-bottom: 1rem;
        color: #374151;
    }
    .rsvp-action-btn {
        transition: all 0.2s ease;
        font-weight: 600;
    }
    .rsvp-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    #showMap {
        height: 300px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4 px-md-5">
    {{-- Breadcrumb --}}
    <div class="d-flex align-items-center mb-4">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0" style="font-size: 15px; font-weight: 500;">
                <li class="breadcrumb-item"><a href="{{ route('warga.dashboard') }}" class="text-success text-decoration-none">Beranda Warga</a></li>
                <li class="breadcrumb-item"><a href="{{ route('warga.agenda.index') }}" class="text-success text-decoration-none">Agenda</a></li>
                <li class="breadcrumb-item active text-dark text-truncate" aria-current="page" style="max-width: 300px;">{{ $agenda->judul_agenda }}</li>
            </ol>
        </nav>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; min-height: 70vh;">
        {{-- Hero Image --}}
        @if($agenda->banner_flyer)
        <div class="agenda-show-hero">
            <img src="{{ asset($agenda->banner_flyer) }}" alt="{{ $agenda->judul_agenda }}">
        </div>
        @endif

        <div class="card-body p-4 p-md-5">
            {{-- Badge & ID --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">
                        <i class="bi bi-calendar-event me-1"></i>Agenda
                    </span>
                    @if($agenda->kategori)
                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background: white; color: #374151; font-size: 12px; border: 1px solid #D1D5DB;">
                        {{ $agenda->kategori }}
                    </span>
                    @endif
                </div>
                <span class="text-muted small" style="font-family: monospace;">{{ strtoupper(substr($agenda->id, 0, 8)) }}</span>
            </div>

            {{-- Title --}}
            <h1 class="fw-bold text-dark mb-4" style="font-size: 2.2rem; line-height: 1.3;">{{ $agenda->judul_agenda }}</h1>

            {{-- Meta Info --}}
            <div class="d-flex flex-wrap align-items-center gap-4 mb-5 pb-4 border-bottom">
                @if($agenda->operator)
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="bi bi-person-circle fs-4"></i>
                    <span class="fw-semibold text-dark">{{ $agenda->operator->name ?? 'Admin' }}</span>
                </div>
                @endif
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="bi bi-calendar3"></i>
                    <span>
                        @if($isMultiDay)
                            {{ $start->translatedFormat('l, d F Y') }} s/d {{ $end->translatedFormat('l, d F Y') }}
                        @else
                            {{ $start->translatedFormat('l, d F Y') }}
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="bi bi-clock"></i>
                    <span>{{ $start->format('H:i') }} - {{ $end->format('H:i') }} WIB</span>
                </div>
                @if($agenda->lokasi)
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span>{{ $agenda->lokasi }}</span>
                </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="content-html text-dark" style="font-size: 1.1rem; line-height: 1.8;">
                {!! $agenda->detail_pengumuman !!}
            </div>

            {{-- Extra Info: RSVP + Map --}}
            <div class="mt-5">
                <hr class="mb-4" style="border-color: #E5E7EB;">

                {{-- Konfirmasi Kehadiran --}}
                @if($agenda->is_rsvp_enabled)
                <div class="p-4 bg-light rounded-4 border mb-4" style="border-color: #E5E7EB;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill text-success me-2 fs-5"></i>
                            <span class="fw-bold text-dark" style="font-size: 16px;">Konfirmasi Kehadiran</span>
                        </div>
                        <span id="showTotalHadir" class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2" style="font-size: 0.85rem;">
                            <i class="bi bi-people-fill me-1"></i><span id="showTotalHadirNum">{{ $totalHadir }}</span> Warga Hadir
                        </span>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.85rem;">Pilih status kehadiran Anda untuk agenda ini:</p>
                    <div class="d-flex gap-3" id="showRsvpBtns">
                        <button type="button"
                            class="btn rsvp-action-btn rounded-pill flex-fill {{ $userRsvp === 'Hadir' ? 'btn-success text-white' : 'btn-outline-success' }}"
                            data-agenda="{{ $agenda->id }}" data-status="Hadir" onclick="sendShowRsvp(this)">
                            <i class="bi bi-check-circle me-1"></i>Hadir
                        </button>
                        <button type="button"
                            class="btn rsvp-action-btn rounded-pill flex-fill {{ $userRsvp === 'Tidak Hadir' ? 'btn-danger text-white' : 'btn-outline-danger' }}"
                            data-agenda="{{ $agenda->id }}" data-status="Tidak Hadir" onclick="sendShowRsvp(this)">
                            <i class="bi bi-x-circle me-1"></i>Tidak Hadir
                        </button>
                        <button type="button"
                            class="btn rsvp-action-btn rounded-pill flex-fill {{ $userRsvp === 'Ragu-ragu' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}"
                            data-agenda="{{ $agenda->id }}" data-status="Ragu-ragu" onclick="sendShowRsvp(this)">
                            <i class="bi bi-question-circle me-1"></i>Ragu-ragu
                        </button>
                    </div>
                </div>
                @endif

                {{-- Peta Lokasi --}}
                @if($agenda->latitude && $agenda->longitude)
                <div class="mb-4">
                    <span class="d-block text-muted mb-2" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Titik Lokasi Peta</span>
                    <div class="rounded-4 overflow-hidden border" style="border-color: #E5E7EB;">
                        <div id="showMap"></div>
                    </div>
                </div>
                @endif

                {{-- Link Google Maps --}}
                @if($agenda->link_gmaps)
                <a href="{{ $agenda->link_gmaps }}" target="_blank" class="btn btn-outline-success rounded-pill px-4">
                    <i class="bi bi-map me-2"></i>Buka di Google Maps
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($agenda->latitude && $agenda->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $agenda->latitude }};
    const lng = {{ $agenda->longitude }};
    const map = L.map('showMap', { scrollWheelZoom: false }).setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);
    L.marker([lat, lng]).addTo(map).bindPopup('<b>{{ $agenda->lokasi ?? "Lokasi Agenda" }}</b>').openPopup();
    setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endif

@if($agenda->is_rsvp_enabled)
<script>
window.sendShowRsvp = function(btnEl) {
    const agendaId = btnEl.dataset.agenda;
    const status = btnEl.dataset.status;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const buttons = document.querySelectorAll('#showRsvpBtns .rsvp-action-btn');
    buttons.forEach(b => b.disabled = true);
    const originalHtml = btnEl.innerHTML;
    btnEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch('/warga/agenda/rsvp', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ agenda_id: agendaId, status_kehadiran: status }),
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(data => {
        if (data.success) {
            buttons.forEach(b => {
                b.disabled = false;
                const st = b.dataset.status;
                if (st === 'Hadir') {
                    b.className = (st === status) ? 'btn rsvp-action-btn rounded-pill flex-fill btn-success text-white' : 'btn rsvp-action-btn rounded-pill flex-fill btn-outline-success';
                } else if (st === 'Tidak Hadir') {
                    b.className = (st === status) ? 'btn rsvp-action-btn rounded-pill flex-fill btn-danger text-white' : 'btn rsvp-action-btn rounded-pill flex-fill btn-outline-danger';
                } else if (st === 'Ragu-ragu') {
                    b.className = (st === status) ? 'btn rsvp-action-btn rounded-pill flex-fill btn-warning text-dark' : 'btn rsvp-action-btn rounded-pill flex-fill btn-outline-warning text-dark';
                }
            });
            document.getElementById('showTotalHadirNum').textContent = data.total_hadir;
        }
    })
    .catch(err => {
        console.error('RSVP error:', err);
        buttons.forEach(b => b.disabled = false);
        alert('Gagal menyimpan respons kehadiran.');
    })
    .finally(() => {
        if (btnEl.innerHTML.includes('spinner')) btnEl.innerHTML = originalHtml;
    });
};
</script>
@endif
@endpush
