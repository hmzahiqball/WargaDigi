@extends($layout)

@section('title', 'Pusat Bantuan')
@section('meta_description', 'Pusat Bantuan WargaDigi: panduan langkah demi langkah memakai aplikasi, mulai dari aktivasi akun, mengajukan surat, sampai membayar iuran.')

@push('styles')
    @include('pusat-bantuan.partials.gaya')
@endpush

@php
    use App\Models\PanduanTopik;
    use App\Support\TemaPanduan;
    use Illuminate\Support\Str;
    $tampilAwal = 3; // jumlah topik yang langsung terlihat di setiap kartu bab
    // Pengunjung: topik peran non-publik hanya tampil judulnya; isinya terbuka setelah masuk.
    $terkunci = fn ($t) => $peranTerkunci && $t->peran !== PanduanTopik::PERAN_SEMUA;
@endphp

@section('content')
@unless($login)<div class="container py-4 py-lg-5" data-testid="bantuan-publik">@endunless

@if(session('bantuan_info'))
    <div class="alert alert-info border-0 shadow-sm d-flex gap-2" style="border-radius: 12px;" role="status" data-testid="info-bantuan">
        <i class="bi bi-info-circle"></i><span>{{ session('bantuan_info') }}</span>
    </div>
@endif

{{-- Banner sambutan + pencarian --}}
<section class="pb-banner" aria-labelledby="judulBantuan">
    <h1 id="judulBantuan">Halo, ada yang bisa kami bantu?</h1>
    @if($login)
        <p class="pb-banner-sub mb-3">
            Panduan untuk peran <span class="pb-peran">{{ $peran ?? 'Anda' }}</span>. Ketik apa yang ingin Anda lakukan, lalu ikuti langkahnya.
        </p>
    @else
        <p class="pb-banner-sub mb-2">
            Panduan memakai WargaDigi. Pilih peran Anda, ketik apa yang ingin dilakukan, lalu ikuti langkahnya.
        </p>
        <nav class="pb-pilih-peran" aria-label="Pilih peran" data-testid="pilih-peran-bantuan">
            @foreach($daftarPeran as $p)
                <a href="{{ route('bantuan.index', ['peran' => $p['slug']]) }}" class="{{ $p['nama'] === $peran ? 'aktif' : '' }}"
                   @if($p['nama'] === $peran) aria-current="page" @endif>
                    @unless($p['publik'])<i class="bi bi-lock-fill me-1" aria-hidden="true"></i>@endunless{{ $p['nama'] }}
                </a>
            @endforeach
        </nav>
        @if($peranTerkunci)
            <p class="pb-banner-sub small mb-3" data-testid="catatan-perlu-masuk">
                <i class="bi bi-lock-fill me-1"></i>Panduan {{ $peran }} bisa dibaca setelah
                <a href="{{ route('login') }}" class="text-white fw-semibold">masuk</a> dengan akun {{ $peran }}.
            </p>
        @endif
    @endif
    @include('pusat-bantuan.partials.kotak-cari', ['peranTamu' => $login ? null : Str::slug($peran)])
    @if($populer->isNotEmpty())
        <div class="pb-chips" data-testid="topik-populer">
            <span><i class="bi bi-lightning-charge-fill me-1"></i>Sering dicari:</span>
            @foreach($populer as $t)
                <a href="{{ route('bantuan.show', $t->slug) }}">@if($terkunci($t))<i class="bi bi-lock-fill me-1" aria-label="Perlu masuk"></i>@endif{{ Str::limit($t->judul, 34) }}</a>
            @endforeach
        </div>
    @endif
</section>

{{-- Hasil pencarian --}}
@if($q !== '')
    <section class="mb-4" data-testid="hasil-cari-bantuan">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <h2 class="pb-judul-bagian mb-0">Hasil untuk “{{ $q }}” <small>{{ $hasil->count() }} topik</small></h2>
            <a href="{{ route('bantuan.index', $login ? [] : ['peran' => Str::slug($peran)]) }}" class="small text-decoration-none text-success"><i class="bi bi-x-circle me-1"></i>Hapus pencarian</a>
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
                        @if($terkunci($t))
                            <span class="pb-hasil-ringkas d-block"><i class="bi bi-lock-fill me-1"></i>Masuk untuk membaca panduan ini.</span>
                        @elseif($t->ringkasan)
                            <span class="pb-hasil-ringkas d-block">{{ Str::limit($t->ringkasan, 170) }}</span>
                        @endif
                    </span>
                    <i class="bi {{ $terkunci($t) ? 'bi-lock' : 'bi-chevron-right' }} text-muted align-self-center"></i>
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
@php
    $subPeran = $login ? 'Sesuai menu Anda' : ($peranTerkunci ? 'Masuk untuk membaca isinya' : 'Panduan untuk ' . $peran);
@endphp
@foreach([['judul' => 'Panduan ' . $peran, 'sub' => $subPeran, 'data' => $bab, 'kunci' => 'p'], ['judul' => 'Panduan umum', 'sub' => 'Berlaku untuk semua pengguna', 'data' => $umum, 'kunci' => 'u']] as $kelompok)
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
                            <a href="{{ route('bantuan.show', $t->slug) }}" class="pb-item"@if($terkunci($t)) title="Masuk untuk membaca" data-testid="topik-terkunci"@endif><span>{{ $t->judul }}</span><i class="bi {{ $terkunci($t) ? 'bi-lock' : 'bi-chevron-right' }}"></i></a>
                        @endforeach
                        @if($sisa->isNotEmpty())
                            <div class="collapse" id="{{ $idLain }}">
                                @foreach($sisa as $t)
                                    <a href="{{ route('bantuan.show', $t->slug) }}" class="pb-item"@if($terkunci($t)) title="Masuk untuk membaca" data-testid="topik-terkunci"@endif><span>{{ $t->judul }}</span><i class="bi {{ $terkunci($t) ? 'bi-lock' : 'bi-chevron-right' }}"></i></a>
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
                    @if($terkunci($t))
                        <div class="pb-faq-jawab" data-testid="faq-terkunci">
                            <p><i class="bi bi-lock-fill me-1"></i>Jawaban ini untuk pengguna {{ $t->peran }}.
                                <a href="{{ route('bantuan.show', $t->slug) }}" class="text-success fw-semibold">Masuk untuk membaca</a></p>
                        </div>
                    @else
                        <div class="pb-faq-jawab">{!! $t->isi_html !!}</div>
                    @endif
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
@unless($login)</div>@endunless
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
