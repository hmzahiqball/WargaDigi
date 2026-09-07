@extends('layouts.global')

@section('title', 'Kelola UMKM Saya')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-success mb-1">{{ $usaha->nama_usaha ?? 'Pahatan Kayu Jati Custom' }}</h2>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted small">
                    <i class="bi bi-shop text-success me-1"></i> 
                    {{ $usaha->user->username ?? $usaha->user->penduduk->nama_lengkap ?? 'Bpk. Jago' }} 
                    ({{ $usaha->user->penduduk->keluarga->rt->nama_rt ?? 'RT 03' }})
                </span>
                @if(isset($usaha) && $usaha->status_verifikasi === 'Approved')
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small fw-semibold">
                        <i class="bi bi-patch-check-fill me-1"></i> Terverifikasi RW 12
                    </span>
                @elseif(isset($usaha) && $usaha->status_verifikasi === 'Pending')
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 small fw-semibold">
                        <i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi
                    </span>
                @elseif(isset($usaha) && $usaha->status_verifikasi === 'Rejected')
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 small fw-semibold">
                        <i class="bi bi-x-circle me-1"></i> Verifikasi Ditolak
                    </span>
                @else
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small fw-semibold">
                        Terverifikasi RW 12
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(isset($daftarUsaha) && $daftarUsaha->count() > 0)
            <div class="dropdown">
                <button class="btn btn-white bg-white border shadow-sm rounded-3 px-3 py-2 dropdown-toggle d-flex align-items-center gap-2 text-dark" type="button" id="dropdownPilihUsaha" data-bs-toggle="dropdown" aria-expanded="false" style="height: 46px;">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div class="text-start me-2">
                        <div class="small fw-bold text-dark text-truncate d-flex align-items-center gap-1" style="max-width: 180px; line-height: 1.2;">
                            <span class="text-truncate">{{ $usaha->nama_usaha ?? 'Pilih Toko' }}</span>
                            @if(isset($usaha) && $usaha->status_verifikasi === 'Pending')
                                <span class="badge bg-warning text-dark rounded-pill flex-shrink-0" style="font-size: 9px; padding: 2px 6px;">Pending</span>
                            @endif
                        </div>
                        <div class="text-muted" style="font-size: 11px; line-height: 1.2;">Ganti Usaha ({{ $daftarUsaha->count() }})</div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2 mt-1" aria-labelledby="dropdownPilihUsaha" style="min-width: 290px; z-index: 1050;">
                    <li class="dropdown-header small text-uppercase text-muted fw-bold px-3 py-2" style="font-size: 11px; letter-spacing: 0.5px;">
                        <i class="bi bi-collection me-1"></i> Pilih Usaha Anda
                    </li>
                    @foreach($daftarUsaha as $itemUsaha)
                        @php
                            $isSelected = $usaha && ($usaha->id === $itemUsaha->id);
                            $isPending = $itemUsaha->status_verifikasi === 'Pending';
                        @endphp
                        <li>
                            @if($isPending)
                                <div class="dropdown-item rounded-2 py-2 px-3 mb-1 d-flex align-items-center justify-content-between text-muted" 
                                     style="cursor: not-allowed; background-color: #f8fafc; opacity: 0.85;" 
                                     title="Usaha sedang dalam peninjauan RW (Pending) dan belum dapat dipilih">
                                    <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                        <i class="bi bi-clock-history text-warning"></i>
                                        <div class="text-truncate">
                                            <div class="fw-semibold small text-truncate text-secondary" style="max-width: 140px;">{{ $itemUsaha->nama_usaha }}</div>
                                            <div class="text-muted" style="font-size: 10px;">
                                                {{ $itemUsaha->kategori_umkm->nama_kategori ?? 'UMKM' }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-2 py-1 flex-shrink-0" style="font-size: 10px;">
                                        Pending
                                    </span>
                                </div>
                            @else
                                <a class="dropdown-item rounded-2 py-2 px-3 mb-1 d-flex align-items-center justify-content-between {{ $isSelected ? 'active bg-success text-white' : 'text-dark' }}" 
                                   href="{{ route('warga.umkm.kelola', ['usaha_id' => $itemUsaha->id]) }}">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="bi bi-shop {{ $isSelected ? 'text-white' : 'text-success' }}"></i>
                                        <div class="text-truncate">
                                            <div class="fw-semibold small text-truncate" style="max-width: 160px;">{{ $itemUsaha->nama_usaha }}</div>
                                            <div class="{{ $isSelected ? 'text-white-50' : 'text-muted' }}" style="font-size: 11px;">
                                                {{ $itemUsaha->kategori_umkm->nama_kategori ?? 'UMKM' }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($isSelected)
                                        <i class="bi bi-check2 fs-5 ms-2"></i>
                                    @endif
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <a href="{{ route('warga.umkm.galeri') }}" class="btn btn-white bg-white border rounded-3 px-3 py-2 small fw-semibold text-dark shadow-sm text-decoration-none d-inline-flex align-items-center gap-2" style="height: 46px;">
                <i class="bi bi-arrow-left"></i> Kembali ke Galeri UMKM
            </a>
        </div>
    </div>

    @if(isset($usaha) && $usaha->status_verifikasi === 'Pending')
    <div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 p-md-4 mb-4 rounded-4 gap-3" style="background-color: #fffbeb; border-left: 6px solid #f59e0b !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(245, 158, 11, 0.2); width: 46px; height: 46px;">
                <i class="bi bi-hourglass-split text-warning fs-4"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h5 class="fw-bold mb-0 text-dark">Pendaftaran Usaha "{{ $usaha->nama_usaha }}" Sedang Ditinjau RW</h5>
                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill small fw-bold">Menunggu Verifikasi</span>
                </div>
                <p class="text-muted small mb-0">Usaha Anda telah masuk antrean peninjauan oleh Admin RW. Penambahan dan pengelolaan produk dapat dilakukan setelah usaha disetujui.</p>
            </div>
        </div>
        <div class="text-md-end flex-shrink-0">
            <span class="badge rounded-pill bg-white text-secondary border px-3 py-2 fw-semibold small">
                <i class="bi bi-shield-lock text-warning me-1"></i> Penambahan Produk Dikunci
            </span>
        </div>
    </div>
    @endif

    <div class="card border-0 rounded-4 overflow-hidden mb-4 shadow-sm position-relative text-white" style="min-height: 320px; background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.85) 100%), url('{{ !empty($usaha->foto_usaha) ? asset('storage/' . $usaha->foto_usaha) : 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=1200&auto=format&fit=crop' }}') center/cover no-repeat;">
        <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-between">
            <!-- Top Badges -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge px-3 py-2 fw-bold text-dark rounded-2" style="background-color: #ff9800; font-size: 11px; letter-spacing: 0.5px;">
                    {{ strtoupper($usaha->kategori_umkm->nama_kategori ?? 'KERAJINAN') }} UNGGULAN
                </span>
                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-3 py-2 fw-semibold">
                    <i class="bi bi-circle-fill {{ ($usaha->is_active ?? true) ? 'text-success' : 'text-secondary' }} me-1" style="font-size: 8px;"></i> 
                    {{ ($usaha->is_active ?? true) ? 'Toko Sedang Aktif' : 'Toko Non-Aktif' }}
                </span>
            </div>

            <div>
                <h2 class="fw-bold text-white mb-2 fs-1">{{ $usaha->nama_usaha ?? 'Seni Pahat Kayu Jati Asli Tanimulya' }}</h2>
                <p class="text-white-50 mb-4 fs-6" style="max-width: 680px; line-height: 1.5;">
                    {{ $usaha->deskripsi ?? 'Karya tangan warga Tanimulya. Cocok untuk hiasan rumah atau hadiah eksklusif. Dibuat dengan dedikasi tinggi menggunakan bahan pilihan terbaik.' }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success fw-semibold px-3 py-2 rounded-3 text-white" style="background-color: #5b9b76; border-color: #5b9b76;" data-bs-toggle="modal" data-bs-target="#modalEditUMKM">
                        <i class="bi bi-pencil-square me-1"></i> Edit Data UMKM
                    </button>
                    <button type="button" class="btn btn-dark bg-opacity-50 text-white border-0 fw-semibold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalGantiSampul">
                        <i class="bi bi-image me-1"></i> Ganti Foto Sampul
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('warga.umkm.components.cardSummaryStat')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Produk Teratas & Katalog Etalase</h5>
            <p class="text-muted small mb-0">Kelola daftar produk terlaris yang ditampilkan kepada warga RW 12.</p>
        </div>
        <div class="d-flex gap-2">
            @if(isset($usaha) && $usaha->status_verifikasi === 'Pending')
                <button type="button" class="btn btn-secondary text-white rounded-3 px-3 py-2 fw-semibold shadow-sm small disabled" style="cursor: not-allowed; opacity: 0.7;" title="Menunggu persetujuan RW sebelum dapat menambah produk">
                    <i class="bi bi-lock-fill me-1"></i> Tambah Produk (Terkunci)
                </button>
            @else
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahProduk" class="btn btn-success text-white rounded-3 px-3 py-2 fw-semibold shadow-sm small" style="background-color: #198754;">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
                </a>
            @endif
        </div>
    </div>

    @include('warga.umkm.components.filterSearchBar', [
        'actionUrl' => route('warga.umkm.kelola'),
        'placeholder' => 'Cari Produk Berdasarkan Nama',
        'produk' => $produk ?? null,
        'usaha' => $usaha ?? null,
        'kategoriProdukList' => $kategoriProdukList ?? null,
        'showStatus' => true,
        'usahaId' => $usaha->id ?? null
    ])

    <div class="row g-4 mb-4">
        @if(isset($produk) && count($produk) > 0)
            @foreach($produk as $item)
                @include('warga.umkm.components.cardProductManage', ['item' => $item])
            @endforeach
        @endif
        <div class="col-sm-6 col-md-4 col-lg-3">
            @if(isset($usaha) && $usaha->status_verifikasi === 'Pending')
                <div class="add-product-card text-muted" style="cursor: not-allowed; opacity: 0.75; background-color: #f8fafc; border-color: #cbd5e1;">
                    <div class="add-product-icon bg-light text-muted">
                        <i class="bi bi-lock fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-secondary mb-2">Penambahan Produk Dikunci</h6>
                    <p class="text-muted small mb-0">
                        Profil UMKM Anda sedang ditinjau Pengurus RW. Penambahan produk akan aktif otomatis setelah disetujui.
                    </p>
                </div>
            @else
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahProduk" class="add-product-card">
                    <div class="add-product-icon">
                        <i class="bi bi-plus-lg fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">Tambah Produk Baru</h6>
                    <p class="text-muted small mb-0">
                        Perluas katalog UMKM Anda dengan menambahkan item baru ke galeri.
                    </p>
                </a>
            @endif
        </div>
    </div>

    @include('components.pagination', ['paginator' => $produk, 'label' => 'produk'])

    @include('warga.umkm.components.footerUmkm')
</div>

@include('warga.umkm.components.modalTambahProduk')
@include('warga.umkm.components.modalEditProduk')

<div class="modal fade" id="modalEditUMKM" tabindex="-1" aria-labelledby="modalEditUMKMLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 align-items-center">
                <h4 class="modal-title fw-bold text-dark" id="modalEditUMKMLabel">Edit Data UMKM</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ isset($usaha) ? route('warga.umkm.update-usaha', $usaha->id) : '#' }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Nama Usaha</label>
                            <input type="text" name="nama_usaha" class="form-control rounded-3 py-2 px-3 border-1 shadow-none" value="{{ $usaha->nama_usaha ?? '' }}" placeholder="Contoh: Keripik Tempe Renyah" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Kategori</label>
                            <select name="kategori_umkm_id" class="form-select rounded-3 py-2 px-3 border-1 shadow-none text-dark" required>
                                @if(isset($daftarKategoriUmkm) && count($daftarKategoriUmkm) > 0)
                                    @foreach($daftarKategoriUmkm as $kat)
                                        <option value="{{ $kat->id }}" {{ (isset($usaha) && $usaha->kategori_umkm_id == $kat->id) ? 'selected' : '' }}>
                                            {{ $kat->nama_kategori }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="Kerajinan" {{ ($usaha->kategori_umkm->nama_kategori ?? '') == 'Kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                                    <option value="Kuliner" {{ ($usaha->kategori_umkm->nama_kategori ?? '') == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                                    <option value="Fashion" {{ ($usaha->kategori_umkm->nama_kategori ?? '') == 'Fashion' ? 'selected' : '' }}>Fashion</option>
                                    <option value="Jasa" {{ ($usaha->kategori_umkm->nama_kategori ?? '') == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Alamat Usaha (RT/RW)</label>
                            <input type="text" name="alamat_usaha" class="form-control rounded-3 py-2 px-3 border-1 shadow-none" value="{{ $usaha->alamat_usaha ?? '' }}" placeholder="Contoh: Jl. Mawar No. 12, RT 03/RW 12" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">No. WhatsApp Business</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark rounded-start-3 small fw-semibold px-3">+62</span>
                                <input type="text" name="no_wa" class="form-control rounded-end-3 py-2 px-3 border-1 shadow-none" value="{{ preg_replace('/^(62|\+62|0)/', '', $usaha->no_wa ?? '') }}" placeholder="8123456789" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-dark">Deskripsi Usaha</label>
                        <textarea name="deskripsi" class="form-control rounded-3 py-2 px-3 border-1 shadow-none" rows="3" placeholder="Ceritakan tentang usaha Anda...">{{ $usaha->deskripsi ?? '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-end gap-2 px-4 py-3 border-top" style="background-color: #f1f8f3; border-top-color: #e2ece4 !important; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 small fw-semibold text-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 small fw-semibold text-white" style="background-color: #5b9b76; border-color: #5b9b76;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGantiSampul" tabindex="-1" aria-labelledby="modalGantiSampulLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 align-items-center">
                <h4 class="modal-title fw-bold text-dark" id="modalGantiSampulLabel">Ganti Foto Sampul</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ isset($usaha) ? route('warga.umkm.update-sampul', $usaha->id) : '#' }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="upload-dashed-box text-center p-4 rounded-3" onclick="document.getElementById('inputEditSampul').click()">
                        <i class="bi bi-image fs-3 text-dark mb-2 d-block"></i>
                        <span class="small fw-semibold text-dark d-block" id="labelEditSampul">Pilih Foto Sampul Baru</span>
                        <input type="file" name="foto_sampul" id="inputEditSampul" class="d-none" accept="image/*" onchange="previewFileName(this, 'labelEditSampul')" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-end gap-2 px-4 py-3 border-top" style="background-color: #f1f8f3; border-top-color: #e2ece4 !important; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 small fw-semibold text-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 small fw-semibold text-white" style="background-color: #5b9b76; border-color: #5b9b76;">Upload Sampul</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewFileName(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        label.innerText = input.files[0].name;
        label.classList.remove('text-secondary');
        label.classList.add('text-success', 'fw-bold');
    }
}
</script>
@endpush
@endsection