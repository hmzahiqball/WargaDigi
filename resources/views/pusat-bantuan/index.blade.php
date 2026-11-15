@extends('layouts.global')

@section('title', 'Pusat Bantuan')

@push('styles')
    @include('pusat-bantuan.partials.gaya')
@endpush

@php
    use App\Support\TemaPanduan;
    use Illuminate\Support\Str;
    $tampilAwal = 3; // jumlah topik yang langsung terlihat di setiap kartu bab
@endphp

@section('content')
{{-- Banner sambutan + pencarian --}}
<section class="pb-banner" aria-labelledby="judulBantuan">
    <h1 id="judulBantuan">Halo, ada yang bisa kami bantu?</h1>
    <p class="pb-banner-sub mb-3">
        Panduan untuk peran <span class="pb-peran">{{ $peran ?? 'Anda' }}</span>. Ketik apa yang ingin Anda lakukan, lalu ikuti langkahnya.
    </p>
    @include('pusat-bantuan.partials.kotak-cari')
    @if($populer->isNotEmpty())
        <div class="pb-chips" data-testid="topik-populer">
            <span><i class="bi bi-lightning-charge-fill me-1"></i>Sering dicari:</span>
            @foreach($populer as $t)
                <a href="{{ route('bantuan.show', $t->slug) }}">{{ Str::limit($t->judul, 34) }}</a>
            @endforeach
        </div>
    @endif
</section>

{{-- Hasil pencarian --}}
@if($q !== '')
    <section class="mb-4" data-testid="hasil-cari-bantuan">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <h2 class="pb-judul-bagian mb-0">Hasil untuk “{{ $q }}” <small>{{ $hasil->count() }} topik</small></h2>
            <a href="{{ route('bantuan.index') }}" class="small text-decoration-none text-success"><i class="bi bi-x-circle me-1"></i>Hapus pencarian</a>
        </div>
        <div class="pb-faq">
            @forelse($hasil as $t)
                @php $tema = TemaPanduan::untuk($t->jenis === 'faq' ? 'Pertanyaan' : $t->bab_judul); @endphp
                <a href="{{ route('bantuan.show', $t->slug) }}" class="pb-hasil pb-tema-{{ $tema['nama'] }}" data-testid="hasil-bantuan">
                    <span class="pb-ikon"><i class="bi {{ $t->jenis === 'faq' ? 'bi-question-circle' : $tema['ikon'] }}"></i></span>
                    <span class="flex-grow-1">
                        <span class="pb-hasil-bab d-block">
                            {{ $t->jenis === 'faq' ? 'Pertanyaan yang sering muncul' : $t->bab_judul }}
                            @if($t->peran === \App\Models\PanduanTopik::PERAN_SEMUA)<span class="pb-label-umum ms-1">Umum</span>@endif
                        </span>
                        <span class="pb-hasil-judul d-block">{{ $t->judul }}</span>
                        @if($t->ringkasan)<span class="pb-hasil-ringkas d-block">{{ Str::limit($t->ringkasan, 170) }}</span>@endif
                    </span>
                    <i class="bi bi-chevron-right text-muted align-self-center"></i>
                </a>
            @empty
                <div class="text-center py-5 px-3" data-testid="hasil-bantuan-kosong">
                    <span class="pb-ikon pb-tema-hijau mx-auto mb-3"><i class="bi bi-search"></i></span>
                    <p class="mb-1 fw-semibold">Panduan untuk “{{ $q }}” belum ditemukan.</p>
                    <p class="text-muted small mb-0">Coba kata lain yang lebih umum, misalnya “surat”, “iuran”, atau “ganti peran”, atau pilih topik di bawah.</p>
                </div>
            @endforelse
        </div>
    </section>
@endif

@if($bab->isEmpty() && $umum->isEmpty())
    <div class="alert alert-warning border-0 shadow-sm" style="border-radius: 12px;" data-testid="bantuan-belum-tersedia">
        Panduan untuk peran ini belum tersedia.
    </div>
@endif

{{-- Kartu bab untuk peran aktif dan panduan umum --}}
@foreach([['judul' => 'Panduan ' . $peran, 'sub' => 'Sesuai menu Anda', 'data' => $bab, 'kunci' => 'p'], ['judul' => 'Panduan umum', 'sub' => 'Berlaku untuk semua pengguna', 'data' => $umum, 'kunci' => 'u']] as $kelompok)
    @continue($kelompok['data']->isEmpty())
    <section class="mb-4">
        <h2 class="pb-judul-bagian">{{ $kelompok['judul'] }} <small>{{ $kelompok['sub'] }}</small></h2>
        <div class="row g-3">
            @foreach($kelompok['data'] as $judulBab => $daftar)
                @php
                    $tema = TemaPanduan::untuk($judulBab);
                    $idLain = 'pbLain' . $kelompok['kunci'] . $loop->index;
                    $sisa = $daftar->slice($tampilAwal);
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="pb-bab pb-tema-{{ $tema['nama'] }}" data-testid="kartu-bab-bantuan">
                        <div class="pb-bab-kepala">
                            <span class="pb-ikon"><i class="bi {{ $tema['ikon'] }}"></i></span>
                            <div>
                                <div class="pb-bab-judul">{{ $judulBab }}</div>
                                <div class="pb-bab-jumlah">{{ $daftar->count() }} topik</div>
                            </div>
                        </div>
                        @foreach($daftar->take($tampilAwal) as $t)
                            <a href="{{ route('bantuan.show', $t->slug) }}" class="pb-item"><span>{{ $t->judul }}</span><i class="bi bi-chevron-right"></i></a>
                        @endforeach
                        @if($sisa->isNotEmpty())
                            <div class="collapse" id="{{ $idLain }}">
                                @foreach($sisa as $t)
                                    <a href="{{ route('bantuan.show', $t->slug) }}" class="pb-item"><span>{{ $t->judul }}</span><i class="bi bi-chevron-right"></i></a>
                                @endforeach
                            </div>
                            <button type="button" class="pb-lainnya" data-bs-toggle="collapse" data-bs-target="#{{ $idLain }}" aria-expanded="false" aria-controls="{{ $idLain }}"
                                    data-teks-buka="Lihat semua {{ $daftar->count() }} topik" data-teks-tutup="Tampilkan lebih sedikit">
                                <span>Lihat semua {{ $daftar->count() }} topik</span> <i class="bi bi-chevron-down"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endforeach

{{-- FAQ sebagai akordeon --}}
@if($faq->isNotEmpty())
    <section class="mb-2" id="faq" style="scroll-margin-top: 90px;">
        <h2 class="pb-judul-bagian">Pertanyaan yang sering muncul <small>Klik untuk melihat jawaban</small></h2>
        <div class="pb-faq" data-testid="daftar-faq-bantuan">
            @foreach($faq as $t)
                <details>
                    <summary>
                        <span class="flex-grow-1">{{ $t->judul }}</span>
                        @if($t->peran === \App\Models\PanduanTopik::PERAN_SEMUA)<span class="pb-label-umum">Umum</span>@endif
                    </summary>
                    {{-- Isi dibangkitkan parser Manual Book: teks sudah di-escape. --}}
                    <div class="pb-faq-jawab">{!! $t->isi_html !!}</div>
                </details>
            @endforeach
        </div>
        @if($faq->hasPages())
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2 mt-3" data-testid="paginasi-faq-bantuan">
                <small class="text-muted">Menampilkan {{ $faq->firstItem() }}-{{ $faq->lastItem() }} dari {{ $faq->total() }} pertanyaan</small>
                @include('pusat-bantuan.partials.paginasi', ['halaman' => $faq])
            </div>
        @endif
    </section>
@endif
@endsection

@push('scripts')
<script>
    // Ganti teks tombol "Lihat semua" saat daftar dibuka/ditutup.
    document.querySelectorAll('.pb-lainnya').forEach((tombol) => {
        const target = document.querySelector(tombol.dataset.bsTarget);
        if (!target) return;
        target.addEventListener('shown.bs.collapse', () => { tombol.querySelector('span').textContent = tombol.dataset.teksTutup; });
        target.addEventListener('hidden.bs.collapse', () => { tombol.querySelector('span').textContent = tombol.dataset.teksBuka; });
    });
</script>
@endpush
