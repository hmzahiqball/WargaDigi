@extends('layouts.global')

@section('title', 'Kategori Transaksi')

@php
    // Prefix route menyesuaikan pengelola: Admin (umum) atau Operator (unit).
    $rp = $mode === 'admin' ? 'admin.kategori-transaksi' : 'opkeuangan.kategori-transaksi';
    $operatorRw = $mode !== 'admin' && $unit === 'RW';
    $operatorDkm = $mode !== 'admin' && $unit === 'DKM';
    $labelTipe = fn ($t) => match ($t) {
        'pemasukan'   => ['Pemasukan', 'success'],
        'pengeluaran' => ['Pengeluaran', 'danger'],
        default       => ['Keduanya', 'secondary'],
    };
@endphp

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h2 class="fw-bold" style="color: #198754;">Kategori Transaksi</h2>
        <p class="text-muted mb-0">
            @if($mode === 'admin')
                Kategori umum yang berlaku untuk semua unit (RT, RW, DKM).
            @else
                @if($operatorDkm)
                    Kategori buatan DKM (khusus <strong>pengeluaran</strong>). Kategori pemasukan DKM diterapkan oleh Operator Keuangan RW dan tampil sebagai <em>Dari RW</em>.
                @elseif($operatorRw)
                    Kategori buatan RW. Tentukan unit penerapannya (RW, RT, DKM) lewat <em>Berlaku di</em>. Kategori umum dari Admin otomatis tersedia.
                @else
                    Kategori khusus unit <strong>{{ $unit }}</strong> Anda. Kategori umum dari Admin otomatis tersedia.
                @endif
            @endif
        </p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
    </button>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
    <ul class="mb-0 list-unstyled">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filter --}}
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-{{ $mode === 'admin' ? 5 : 4 }}">
                <label class="form-label small fw-semibold text-muted">Cari Nama Kategori</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Ketik nama kategori...">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Tipe</label>
                <select name="tipe" class="form-select">
                    <option value="">Semua</option>
                    <option value="pemasukan" @selected(request('tipe')==='pemasukan')>Pemasukan</option>
                    <option value="pengeluaran" @selected(request('tipe')==='pengeluaran')>Pengeluaran</option>
                    <option value="keduanya" @selected(request('tipe')==='keduanya')>Keduanya</option>
                </select>
            </div>
            @if($mode !== 'admin')
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Asal</label>
                <select name="asal" class="form-select">
                    <option value="">Semua</option>
                    <option value="umum" @selected(request('asal')==='umum')>Umum</option>
                    @if($operatorDkm)
                        <option value="unit" @selected(request('asal')==='unit')>Buatan DKM</option>
                        <option value="dari_rw" @selected(request('asal')==='dari_rw')>Dari RW</option>
                    @elseif($operatorRw)
                        <option value="rw" @selected(request('asal')==='rw')>Berlaku di RW</option>
                        <option value="rt" @selected(request('asal')==='rt')>Berlaku di RT</option>
                        <option value="dkm" @selected(request('asal')==='dkm')>Berlaku di DKM</option>
                    @else
                        <option value="unit" @selected(request('asal')==='unit')>Unit {{ $unit }}</option>
                    @endif
                </select>
            </div>
            @endif
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="aktif" @selected(request('status')==='aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status')==='nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill"><i class="bi bi-funnel me-1"></i>Filter</button>
                @if(request()->anyFilled(['search','tipe','status','asal']))
                    <a href="{{ route($rp . '.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small fw-semibold">NAMA KATEGORI</th>
                        <th class="py-3 text-muted small fw-semibold">TIPE</th>
                        @if($mode !== 'admin')
                            <th class="py-3 text-muted small fw-semibold">ASAL</th>
                        @endif
                        <th class="py-3 text-muted small fw-semibold">STATUS</th>
                        <th class="pe-4 py-3 text-muted small fw-semibold text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategori as $k)
                    @php
                        [$tl, $tc] = $labelTipe($k->tipe);
                        // Operator hanya boleh mengelola kategori milik unitnya (bukan kategori umum).
                        $bisaKelola = $mode === 'admin'
                            || ($operatorRw && $k->unit === 'RW')
                            || (! $operatorRw && $k->unit === $unit);
                    @endphp
                    <tr>
                        <td class="ps-4 py-3 fw-semibold text-dark">{{ $k->nama }}</td>
                        <td class="py-3"><span class="badge bg-{{ $tc }} bg-opacity-10 text-{{ $tc }}">{{ $tl }}</span></td>
                        @if($mode !== 'admin')
                            <td class="py-3">
                                @if($k->unit === null)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-globe2 me-1"></i>Umum</span>
                                @elseif($operatorRw)
                                    <span class="badge bg-primary bg-opacity-10 text-primary" data-testid="berlaku-di"><i class="bi bi-house me-1"></i>{{ $k->unit === 'DKM' ? 'DKM' : $k->berlaku_di }}</span>
                                @elseif($k->unit === 'RW')
                                    <span class="badge bg-info bg-opacity-10 text-info"><i class="bi bi-building me-1"></i>Dari RW</span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-house me-1"></i>Unit {{ $k->unit }}</span>
                                @endif
                            </td>
                        @endif
                        <td class="py-3">
                            @if($k->aktif)
                                <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="pe-4 py-3 text-end">
                            @if($bisaKelola)
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                        onclick="editKategori('{{ $k->id }}', @js($k->nama), '{{ $k->tipe }}', {{ $k->aktif ? 'true' : 'false' }}, {{ $k->urutan }}, @js($k->unit === 'DKM' ? ['DKM'] : $k->unit_berlaku))">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route($rp . '.destroy', $k->id) }}" method="POST" class="d-inline" data-konfirmasi="Hapus kategori ini? Jika sudah dipakai transaksi, kategori hanya akan dinonaktifkan." data-konfirmasi-judul="Hapus Kategori" data-konfirmasi-tombol="Hapus" data-konfirmasi-jenis="bahaya">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-trash"></i></button>
                                </form>
                            @else
                                <span class="text-muted small"><i class="bi bi-lock"></i> {{ match($k->unit) { 'RW' => 'Dikelola RW', 'DKM' => 'Dikelola DKM', default => 'Dikelola Admin' } }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $mode !== 'admin' ? 5 : 4 }}" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            @if(request()->anyFilled(['search','tipe','status','asal']))
                                Tidak ada kategori yang cocok dengan filter.
                            @else
                                Belum ada kategori. Tambahkan kategori pertama.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($kategori->hasPages())
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <span class="text-muted small">Menampilkan {{ $kategori->firstItem() }}-{{ $kategori->lastItem() }} dari {{ $kategori->total() }} kategori</span>
    <div>{{ $kategori->links() }}</div>
</div>
@endif

@if($mode !== 'admin')
<p class="text-muted small"><i class="bi bi-info-circle me-1"></i>Kategori <strong>Umum</strong> dikelola oleh Admin dan otomatis tersedia untuk unit Anda. Anda hanya dapat mengubah kategori milik unit sendiri.</p>
@endif

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <form action="{{ route($rp . '.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Kategori</label>
                        <input type="text" name="nama" class="form-control" maxlength="100" required>
                    </div>
                    @if($operatorDkm)
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tipe</label>
                        <input type="hidden" name="tipe" value="pengeluaran">
                        <div class="form-control bg-light" data-testid="tipe-dkm-tetap">Pengeluaran</div>
                        <div class="form-text">Kategori DKM hanya untuk pengeluaran. Kategori pemasukan DKM dibuat oleh Operator Keuangan RW.</div>
                    </div>
                    @else
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tipe</label>
                        <select name="tipe" class="form-select" required>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                            <option value="keduanya">Keduanya</option>
                        </select>
                        <div class="form-text">Menentukan kategori ini boleh dipakai untuk transaksi masuk, keluar, atau keduanya.</div>
                    </div>
                    @endif
                    @if($operatorRw)
                    <div class="mb-3" data-testid="pilihan-berlaku-di" data-berlaku-di>
                        <label class="form-label small fw-bold text-muted d-block">Berlaku di</label>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" data-pilih-semua id="tambahSemua">
                            <label class="form-check-label small fw-semibold" for="tambahSemua">Pilih semua</label>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach(['rw' => 'RW', 'rt' => 'RT', 'dkm' => 'DKM'] as $nilai => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="berlaku_di[]" value="{{ $nilai }}" id="tambah{{ $nilai }}" data-unit="{{ $nilai }}" @checked($nilai === 'rw')>
                                    <label class="form-check-label small" for="tambah{{ $nilai }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text">Pilih minimal satu. Memperluas selalu boleh; melepas unit hanya bila belum dipakai di unit itu. Kategori DKM hanya dapat dibuat di sini.</div>
                    </div>
                    @endif                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktifTambah" checked>
                        <label class="form-check-label small" for="aktifTambah">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEditKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <form method="POST" id="formEditKategori">
                @csrf @method('PUT')
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Kategori</label>
                        <input type="text" name="nama" id="editNama" class="form-control" maxlength="100" required>
                    </div>
                    @if($operatorDkm)
                    <input type="hidden" name="tipe" id="editTipe" value="pengeluaran">
                    <div class="form-text mb-3">Kategori DKM hanya untuk pengeluaran.</div>
                    @else
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tipe</label>
                        <select name="tipe" id="editTipe" class="form-select" required>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                            <option value="keduanya">Keduanya</option>
                        </select>
                    </div>
                    @endif
                    @if($operatorRw)
                    <div class="mb-3" data-testid="pilihan-berlaku-di" data-berlaku-di>
                        <label class="form-label small fw-bold text-muted d-block">Berlaku di</label>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" data-pilih-semua id="editSemua">
                            <label class="form-check-label small fw-semibold" for="editSemua">Pilih semua</label>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach(['rw' => 'RW', 'rt' => 'RT', 'dkm' => 'DKM'] as $nilai => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="berlaku_di[]" value="{{ $nilai }}" id="edit{{ $nilai }}" data-unit="{{ $nilai }}" @checked(false)>
                                    <label class="form-check-label small" for="edit{{ $nilai }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text">Pilih minimal satu. Memperluas selalu boleh; melepas unit hanya bila belum dipakai di unit itu. Kategori DKM hanya dapat dibuat di sini.</div>
                    </div>
                    @endif                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="editAktif">
                        <label class="form-check-label small" for="editAktif">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const EDIT_ROUTE_TPL = @json(route($rp . '.update', '__ID__'));
function editKategori(id, nama, tipe, aktif, urutan, unitBerlaku) {
    document.getElementById('formEditKategori').action = EDIT_ROUTE_TPL.replace('__ID__', id);
    document.getElementById('editNama').value = nama;
    document.getElementById('editTipe').value = tipe;
    document.getElementById('editAktif').checked = aktif;
    const grup = document.querySelector('#modalEditKategori [data-berlaku-di]');
    if (grup && Array.isArray(unitBerlaku)) {
        grup.querySelectorAll('input[data-unit]').forEach(function (cb) {
            cb.checked = unitBerlaku.map(u => u.toLowerCase()).includes(cb.dataset.unit);
        });
        sinkronPilihSemua(grup);
    }
    new bootstrap.Modal(document.getElementById('modalEditKategori')).show();
}

// Kotak centang "Berlaku di": "Pilih semua" menyinkronkan RW, RT, DKM.
function sinkronPilihSemua(grup) {
    const semua = grup.querySelector('[data-pilih-semua]');
    const unit = Array.from(grup.querySelectorAll('input[data-unit]'));
    if (semua) {
        semua.checked = unit.length > 0 && unit.every(cb => cb.checked);
        semua.indeterminate = !semua.checked && unit.some(cb => cb.checked);
    }
}
document.querySelectorAll('[data-berlaku-di]').forEach(function (grup) {
    const semua = grup.querySelector('[data-pilih-semua]');
    if (semua) {
        semua.addEventListener('change', function () {
            grup.querySelectorAll('input[data-unit]').forEach(cb => { cb.checked = semua.checked; });
            semua.indeterminate = false;
        });
    }
    grup.querySelectorAll('input[data-unit]').forEach(cb => cb.addEventListener('change', () => sinkronPilihSemua(grup)));
    sinkronPilihSemua(grup);
    // Minimal satu unit harus dipilih sebelum formulir dikirim.
    const form = grup.closest('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!grup.querySelector('input[data-unit]:checked')) {
                e.preventDefault();
                window.tampilkanPemberitahuan('Pilih minimal satu unit tempat kategori ini berlaku (RW, RT, atau DKM).', { judul: 'Perhatian' });
            }
        });
    }
});</script>
@endpush
