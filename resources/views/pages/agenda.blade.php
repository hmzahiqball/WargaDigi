@extends('layouts.app')

@section('title', 'Agenda Kegiatan')
@section('meta_description', 'Agenda kegiatan RW 21: jadwal kegiatan warga yang akan datang dan arsip kegiatan yang sudah terlaksana.')

@section('content')
<section class="berita-hero">
    <div class="container">
        <span class="section-badge mb-2"><i class="bi bi-calendar3"></i> Kegiatan Warga</span>
        <h1>Agenda Kegiatan</h1>
        <p>Jadwal kegiatan dan acara di lingkungan kita. Catat tanggalnya dan ikut berpartisipasi.</p>
    </div>
</section>

<section class="container pb-5">
    <div class="row g-4">
        {{-- Kalender --}}
        <div class="col-lg-5 order-lg-2">
            <div class="agenda-kalender-sticky">
                <h2 class="berita-subjudul mb-3">Kalender Kegiatan</h2>
                <div class="calendar-widget" id="calendarAgenda" aria-label="Kalender kegiatan"></div>
                <p class="text-muted small mt-2 mb-0"><span class="agenda-legenda" aria-hidden="true"></span> Tanggal bertanda memiliki kegiatan.</p>
            </div>
        </div>

        {{-- Agenda mendatang --}}
        <div class="col-lg-7 order-lg-1">
            <h2 class="berita-subjudul mb-3">Agenda Mendatang</h2>
            @forelse($mendatang as $a)
                <div class="mb-3">@include('pages.partials.kartu-agenda', ['a' => $a])</div>
            @empty
                <div class="kartu-agenda-kosong text-center text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                    Belum ada agenda mendatang. Pantau terus halaman ini untuk kegiatan berikutnya.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Arsip --}}
    @if($selesai->total() > 0)
        <div class="mt-5" id="arsip">
            <h2 class="berita-subjudul mb-3">Kegiatan yang Telah Terlaksana</h2>
            <div class="row g-3">
                @foreach($selesai as $a)
                    <div class="col-md-6">@include('pages.partials.kartu-agenda', ['a' => $a, 'redup' => true])</div>
                @endforeach
            </div>
            <div class="mt-3">
                {{ $selesai->fragment('arsip')->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof initCalendar === 'function') {
            initCalendar('calendarAgenda', @json($tandaKalender));
        }
    });
</script>
@endpush
