@extends($layout)

@section('title', $topik->judul . ' - Pusat Bantuan')
@section('meta_description', \Illuminate\Support\Str::limit((string) $topik->ringkasan, 155))

@push('styles')
    @include('pusat-bantuan.partials.gaya')
@endpush

@php
    use Illuminate\Support\Str;
    $ikonDaftarIsi = ['langkah' => 'bi-list-ol', 'hasil' => 'bi-check-circle', 'catatan' => 'bi-exclamation-triangle', 'subjudul' => 'bi-dot'];
    $namaBab = $topik->isFaq() ? 'Pertanyaan yang sering muncul' : $topik->bab_judul;
@endphp

@section('content')
@guest<div class="container py-4 py-lg-5" data-testid="bantuan-publik">@endguest
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('bantuan.index') }}" class="text-decoration-none text-success"><i class="bi bi-question-circle me-1"></i>Pusat Bantuan</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $namaBab }}</li>
        </ol>
    </nav>
    <div class="flex-grow-1" style="max-width: 460px;">
        @include('pusat-bantuan.partials.kotak-cari', ['q' => '', 'kecil' => true])
    </div>
</div>

<div class="row g-4 pb-tema-{{ $tema['nama'] }}">
    <div class="col-lg-8">
        <article class="card border-0 shadow-sm" style="border-radius: 18px;" data-testid="isi-topik-bantuan">
            <div class="card-body p-4 p-md-5">
                <header class="pb-topik-kepala">
                    <span class="pb-ikon"><i class="bi {{ $topik->isFaq() ? 'bi-question-circle' : $tema['ikon'] }}"></i></span>
                    <div>
                        <div class="pb-topik-bab">{{ $namaBab }}</div>
                        <h1 class="pb-topik-judul">{{ $topik->judul }}</h1>
                    </div>
                </header>

                {{-- Isi dibangkitkan parser Manual Book: teks sudah di-escape, tag hanya buatan parser. --}}
                <div class="pb-isi">{!! $isiHtml !!}</div>

                @if($sebelum || $sesudah)
                    <div class="row g-3 mt-3 pb-navigasi">
                        <div class="col-sm-6">
                            @if($sebelum)
                                <a href="{{ route('bantuan.show', $sebelum->slug) }}"><small><i class="bi bi-arrow-left me-1"></i>Sebelumnya</small>{{ Str::limit($sebelum->judul, 60) }}</a>
                            @endif
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            @if($sesudah)
                                <a href="{{ route('bantuan.show', $sesudah->slug) }}"><small>Berikutnya<i class="bi bi-arrow-right ms-1"></i></small>{{ Str::limit($sesudah->judul, 60) }}</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </article>
    </div>

    <div class="col-lg-4">
        <div class="position-sticky d-flex flex-column gap-3" style="top: 90px;">
            @if(count($daftarIsi) > 1)
                <nav class="pb-panel pb-toc pb-2" aria-label="Isi halaman">
                    <div class="pb-panel-judul">Di halaman ini</div>
                    @foreach($daftarIsi as $d)
                        <a href="#{{ $d['id'] }}" class="pb-toc-{{ $d['jenis'] }}"><i class="bi {{ $ikonDaftarIsi[$d['jenis']] ?? 'bi-dot' }}"></i>{{ $d['label'] }}</a>
                    @endforeach
                </nav>
            @endif

            <nav class="pb-panel pb-sebab pb-2" aria-label="Topik lain">
                <div class="pb-panel-judul">{{ $topik->isFaq() ? 'Pertanyaan lainnya' : 'Topik lain di bab ini' }}</div>
                <div style="max-height: 50vh; overflow-y: auto;">
                    @foreach($sebab as $t)
                        <a href="{{ route('bantuan.show', $t->slug) }}" class="{{ $t->id === $topik->id ? 'aktif' : '' }}" @if($t->id === $topik->id) aria-current="page" @endif>{{ $t->judul }}</a>
                    @endforeach
                </div>
                <div class="px-3 pt-2 pb-1 border-top mt-1">
                    <a href="{{ route('bantuan.index') }}" class="small text-decoration-none text-success px-0"><i class="bi bi-grid me-1"></i>Semua topik bantuan</a>
                </div>
            </nav>
        </div>
    </div>
</div>
@guest</div>@endguest
@endsection
