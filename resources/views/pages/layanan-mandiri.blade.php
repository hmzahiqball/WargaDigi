@extends('layouts.app')

@section('title', 'Layanan Mandiri')
@section('meta_description', 'Layanan Mandiri WargaDigi 21: ajukan surat pengantar RT/RW secara online.')

@section('content')
<section class="berita-hero">
    <div class="container">
        <span class="section-badge mb-2"><i class="bi bi-grid-fill"></i> Layanan Warga</span>
        <h1>Layanan Mandiri</h1>
        <p>Ajukan surat pengantar RT/RW secara online. Pilih jenis surat, lengkapi data, lalu pantau prosesnya sampai surat siap diunduh.</p>
        @guest
            <div class="alert alert-light border d-inline-flex align-items-center gap-2 mb-0 py-2 px-3 rounded-3 small" role="note">
                <i class="bi bi-lock-fill text-success"></i>
                <span>Pengajuan surat memerlukan akun warga. <a href="{{ route('login') }}" class="link-green fw-semibold">Masuk</a> terlebih dahulu.</span>
            </div>
        @endguest
    </div>
</section>

<section class="container pb-5">
    <div class="row g-4">
        @foreach($services as $service)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ $service['url'] }}" class="layanan-card" data-testid="kartu-layanan">
                <div class="layanan-icon"><i class="bi {{ $service['icon'] }}" aria-hidden="true"></i></div>
                <h2>{{ $service['title'] }}</h2>
                @if($service['desc'])
                    <p>{{ $service['desc'] }}</p>
                @endif
                <span class="berita-baca mt-auto">
                    @auth Buat Pengajuan @else Masuk untuk Mengajukan @endauth <i class="bi bi-arrow-right"></i>
                </span>
            </a>
        </div>
        @endforeach
    </div>

    <div class="layanan-alur mt-5">
        <h2 class="berita-subjudul mb-3">Alur Pengajuan</h2>
        <ol class="list-unstyled row g-3 mb-0">
            <li class="col-md-4"><div class="layanan-langkah"><span>1</span> Warga mengajukan surat dan melengkapi data.</div></li>
            <li class="col-md-4"><div class="layanan-langkah"><span>2</span> Ketua RT lalu Ketua RW memeriksa dan menyetujui.</div></li>
            <li class="col-md-4"><div class="layanan-langkah"><span>3</span> Surat yang sudah disahkan dapat diunduh dalam format PDF.</div></li>
        </ol>
    </div>
</section>
@endsection
