@extends('layouts.opKonten')

@section('title', 'Dashboard Operator Konten')

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Dashboard</h2>
        <p class="text-muted mb-0">Gambaran umum status konten dan aktivitas terkini.</p>
    </div>
    <button class="btn btn-outline-secondary btn-sm rounded-2 px-3 py-2 fw-semibold bg-white shadow-sm border-light-subtle">
        <i class="bi bi-download me-1"></i> Export Laporan
    </button>
</div>

{{-- Top 4 Stat Cards --}}
<div class="row g-3 mb-4">
    {{-- Card 1: Artikel Berita Aktif --}}
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-file-earmark-text fs-5"></i>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-2 py-1 text-xs">
                    {{ $stats['berita_aktif']['change'] }}
                </span>
            </div>
            <h2 class="fw-bold text-dark mb-1">{{ $stats['berita_aktif']['total'] }}</h2>
            <span class="text-muted small">{{ $stats['berita_aktif']['label'] }}</span>
        </div>
    </div>

    {{-- Card 2: Agenda Akan Datang --}}
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="bg-secondary bg-opacity-10 p-2 rounded-3 text-secondary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-calendar-event fs-5"></i>
                </div>
                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold rounded-pill px-2 py-1 text-xs">
                    {{ $stats['agenda_akan_datang']['badge'] }}
                </span>
            </div>
            <h2 class="fw-bold text-dark mb-1">{{ $stats['agenda_akan_datang']['total'] }}</h2>
            <span class="text-muted small">{{ $stats['agenda_akan_datang']['label'] }}</span>
        </div>
    </div>

    {{-- Card 3: Tinjauan Sedang Diprosese --}}
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-calendar-check fs-5"></i>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold rounded-pill px-2 py-1 text-xs">
                    {{ $stats['tinjauan_proses']['badge'] }}
                </span>
            </div>
            <h2 class="fw-bold text-dark mb-1">{{ $stats['tinjauan_proses']['total'] }}</h2>
            <span class="text-muted small">{{ $stats['tinjauan_proses']['label'] }}</span>
        </div>
    </div>

    {{-- Card 4: Galeri Foto Lengkap --}}
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-images fs-5"></i>
                </div>
            </div>
            <h2 class="fw-bold text-dark mb-1">{{ $stats['galeri_foto']['total'] }}</h2>
            <span class="text-muted small">{{ $stats['galeri_foto']['label'] }}</span>
        </div>
    </div>
</div>

{{-- Main Grid --}}
<div class="row g-4">
    {{-- Left: Status Berita Terkini --}}
    <div class="col-lg-8">
        <div class="card card-custom p-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Status Berita Terkini</h5>
                <a href="#" class="text-success text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted text-uppercase text-xs fw-bold" style="letter-spacing: 0.5px;">
                            <th scope="col" class="py-3">TITLE</th>
                            <th scope="col" class="py-3">KATEGORI</th>
                            <th scope="col" class="py-3">STATUS</th>
                            <th scope="col" class="py-3">UPDATE TERAKHIR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statusBerita as $item)
                            <tr>
                                <td class="fw-bold text-dark py-3 small">{{ $item['title'] }}</td>
                                <td class="text-muted small py-3">{{ $item['kategori'] }}</td>
                                <td class="py-3">
                                    <span class="badge rounded-pill px-3 py-2 {{ $item['status_class'] }} fw-semibold small">
                                        {{ $item['status'] }}
                                    </span>
                                </td>
                                <td class="text-muted small py-3">{{ $item['update'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: Upcoming Agenda --}}
    <div class="col-lg-4">
        <div class="card card-custom p-4 shadow-sm border-0 h-100 d-flex flex-column">
            <h5 class="fw-bold mb-4">Upcoming Agenda</h5>

            <div class="d-flex flex-column gap-3 mb-auto">
                @foreach($upcomingAgenda as $agenda)
                    <div class="p-3 rounded-3 bg-light border border-light-subtle">
                        <div class="d-flex align-items-start gap-3 mb-2">
                            <div class="bg-white border rounded text-center px-2 py-1 shadow-sm flex-shrink-0" style="min-width: 54px;">
                                <span class="d-block text-muted uppercase text-xs fw-bold">{{ $agenda['month'] }}</span>
                                <span class="d-block text-dark fw-bold fs-5" style="line-height: 1.1;">{{ $agenda['day'] }}</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1 small">{{ $agenda['title'] }}</h6>
                                <p class="text-muted small mb-0" style="font-size: 0.78rem; line-height: 1.3;">{{ $agenda['desc'] }}</p>
                            </div>
                        </div>
                        <div class="mt-2 text-start">
                            <span class="badge rounded-pill px-3 py-1 {{ $agenda['status_class'] }} fw-semibold text-xs">
                                {{ $agenda['status'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4 pt-3 border-top">
                <a href="#" class="text-success text-decoration-none small fw-semibold">Lihat Semua Agenda</a>
            </div>
        </div>
    </div>
</div>
@endsection
