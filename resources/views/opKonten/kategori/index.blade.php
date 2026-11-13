@extends('layouts.global')

@section('title', 'Kategori Konten')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Kategori Konten</h2>
        <p class="text-muted mb-0">Satu daftar kategori untuk berita, agenda, dan pengumuman. Berlaku untuk RW dan semua RT.</p>
    </div>
    <button type="button" class="btn btn-success fw-semibold px-4" data-bs-toggle="modal" data-bs-target="#modalKategori" onclick="bukaTambah()" data-testid="tambah-kategori">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </button>
</div>

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm" role="alert">
        <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0" data-testid="tabel-kategori-konten">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th class="ps-4">Kategori</th>
                    <th>Berlaku untuk</th>
                    <th class="text-center">Dipakai</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($kategori as $k)
                @php
                    $w = $warna[$k->warna] ?? $warna['abu'];
                    $p = $k->pemakaian;
                    $dataUbah = [
                        'id' => $k->id, 'nama' => $k->nama, 'warna' => $k->warna, 'aktif' => $k->aktif,
                        'berlaku' => collect(array_keys($jenis))->filter(fn ($j) => $k->{'berlaku_' . $j})->values()->all(),
                    ];
                @endphp
                <tr>
                    <td class="ps-4">
                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background: {{ $w['bg'] }}; color: {{ $w['text'] }}; border: 1px solid {{ $w['border'] }};">{{ $k->nama }}</span>
                    </td>
                    <td>
                        @foreach($jenis as $kunci => $label)
                            @if($k->{'berlaku_' . $kunci})
                                <span class="badge bg-success bg-opacity-10 text-success me-1">{{ $label }}</span>
                            @endif
                        @endforeach
                    </td>
                    <td class="text-center small text-muted" title="Berita {{ $p['berita'] }}, Agenda {{ $p['agenda'] }}, Pengumuman {{ $p['pengumuman'] }}">{{ $p['total'] }}</td>
                    <td>
                        @if($k->aktif)
                            <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end pe-4 text-nowrap">
                        <button type="button" class="btn btn-sm btn-light border" title="Ubah" aria-label="Ubah {{ $k->nama }}"
                                data-bs-toggle="modal" data-bs-target="#modalKategori"
                                onclick='bukaUbah(@json($dataUbah))'
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('opkonten.kategori.destroy', $k->id) }}" method="POST" class="d-inline"
                              data-konfirmasi="Hapus kategori &quot;{{ $k->nama }}&quot;?" data-konfirmasi-judul="Hapus Kategori" data-konfirmasi-tombol="Hapus" data-konfirmasi-jenis="bahaya">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="{{ $p['total'] > 0 ? 'Sudah dipakai, nonaktifkan saja' : 'Hapus' }}" aria-label="Hapus {{ $k->nama }}" @disabled($p['total'] > 0)>
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<p class="small text-muted mt-3">Kategori yang sudah dipakai konten tidak dapat dihapus. Nonaktifkan agar tidak muncul sebagai pilihan baru, konten lama tetap menampilkannya. Mengubah nama ikut mengubah semua konten yang memakainya.</p>

{{-- Modal tambah / ubah --}}
<div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="judulModalKategori" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formKategori" action="{{ route('opkonten.kategori.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
            @csrf
            <input type="hidden" name="_method" id="metodeKategori" value="POST" disabled>
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="judulModalKategori">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="namaKategori" class="form-label fw-semibold">Nama kategori</label>
                    <input type="text" name="nama" id="namaKategori" class="form-control" maxlength="60" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold d-block">Berlaku untuk</label>
                    @foreach($jenis as $kunci => $label)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input jenis-berlaku" type="checkbox" name="berlaku[]" value="{{ $kunci }}" id="berlaku_{{ $kunci }}" checked>
                            <label class="form-check-label" for="berlaku_{{ $kunci }}">{{ $label }}</label>
                        </div>
                    @endforeach
                    <div class="form-text">Pilih minimal satu. Kategori hanya muncul pada jenis konten yang dicentang.</div>
                </div>
                <div class="mb-3">
                    <label for="warnaKategori" class="form-label fw-semibold">Warna</label>
                    <select name="warna" id="warnaKategori" class="form-select" required>
                        @foreach($warna as $kunci => $w)
                            <option value="{{ $kunci }}">{{ $w['label'] }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Dipakai pada kalender agenda warga.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="aktif" value="1" id="aktifKategori" checked>
                    <label class="form-check-label" for="aktifKategori">Aktif (muncul sebagai pilihan baru)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success fw-semibold px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const formKategori = document.getElementById('formKategori');
    const urlTambah = @json(route('opkonten.kategori.store'));
    const urlUbah = id => @json(url('/op-konten/kategori-konten')) + '/' + id;

    function bukaTambah() {
        formKategori.reset();
        formKategori.action = urlTambah;
        document.getElementById('metodeKategori').disabled = true;
        document.getElementById('judulModalKategori').textContent = 'Tambah Kategori';
    }

    function bukaUbah(k) {
        formKategori.action = urlUbah(k.id);
        const metode = document.getElementById('metodeKategori');
        metode.value = 'PUT';
        metode.disabled = false;
        document.getElementById('judulModalKategori').textContent = 'Ubah Kategori';
        document.getElementById('namaKategori').value = k.nama;
        document.getElementById('warnaKategori').value = k.warna;
        document.getElementById('aktifKategori').checked = !!k.aktif;
        document.querySelectorAll('.jenis-berlaku').forEach(c => c.checked = k.berlaku.includes(c.value));
    }

    formKategori.addEventListener('submit', function (e) {
        if (!document.querySelector('.jenis-berlaku:checked')) {
            e.preventDefault();
            if (window.resetFormSubmitting) window.resetFormSubmitting(this);
            if (window.tampilkanPemberitahuan) window.tampilkanPemberitahuan('Pilih minimal satu jenis konten.');
        }
    });
</script>
@endpush