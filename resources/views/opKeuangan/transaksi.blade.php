@extends('layouts.global')

@section('title', 'Catat Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Catat Transaksi</h2>
        <p class="text-muted mb-0">Kelola arus kas masuk dan keluar dengan mudah.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCatatTransaksi">
            <i class="bi bi-plus-lg me-1"></i> Catat Transaksi Baru
        </button>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalBroadcast">
            <i class="bi bi-megaphone me-1"></i> Buat Tagihan Baru
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center mb-1">
        <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
        <strong class="fw-semibold">Terjadi kesalahan validasi:</strong>
    </div>
    <ul class="mb-0 ps-4 small">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('opkeuangan.transaksi.index') }}" class="row g-3 mb-4">
            <div class="col-md-2">
                <label class="form-label text-muted small">Tahun</label>
                <select name="tahun" class="form-select bg-light border-0">
                    <option value="">Semua</option>
                    @for($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Bulan</label>
                <select name="bulan" class="form-select bg-light border-0">
                    <option value="">Semua</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small">Tipe Transaksi</label>
                <select name="tipe" class="form-select bg-light border-0">
                    <option value="">Semua Tipe</option>
                    <option value="pemasukan" {{ request('tipe') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="pengeluaran" {{ request('tipe') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small">Kategori</label>
                <select name="kategori" class="form-select bg-light border-0">
                    <option value="">Semua Kategori</option>
                    <option value="Iuran Warga" {{ request('kategori') == 'Iuran Warga' ? 'selected' : '' }}>Iuran Warga</option>
                    <option value="Operasional" {{ request('kategori') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                    <option value="Dana Kematian" {{ request('kategori') == 'Dana Kematian' ? 'selected' : '' }}>Dana Kematian</option>
                    <option value="Donasi" {{ request('kategori') == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                    <option value="Lainnya" {{ request('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->anyFilled(['tahun', 'bulan', 'tipe', 'kategori']))
                <a href="{{ route('opkeuangan.transaksi.index') }}" class="btn btn-light border text-danger" title="Reset Filter">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-muted small fw-semibold py-3">TANGGAL</th>
                        <th class="text-muted small fw-semibold py-3">PERIHAL</th>
                        <th class="text-muted small fw-semibold py-3">KATEGORI</th>
                        <th class="text-muted small fw-semibold py-3">JUMLAH (IDR)</th>
                        <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($transaksi as $tx)
                    <tr>
                        <td class="py-3 text-dark">{{ $tx->tanggal->format('d M Y') }}</td>
                        <td class="py-3">
                            <div class="fw-semibold text-dark">{{ $tx->judul }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 200px;">{{ $tx->deskripsi }}</div>
                            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                @if($tx->bukti_file)
                                    <a href="{{ asset('storage/' . $tx->bukti_file) }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none px-2 py-1 border border-primary border-opacity-25 rounded-pill">
                                        <i class="bi bi-paperclip me-1"></i> Lampiran Bukti
                                    </a>
                                @endif
                                @if(!empty($tx->is_locked))
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 rounded-pill" title="Laporan periode ini sudah diajukan/disetujui">
                                        <i class="bi bi-lock-fill me-1"></i> Terkunci
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $tx->kategori }}</span>
                        </td>
                        <td class="py-3 fw-bold {{ $tx->tipe == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                            {{ $tx->tipe == 'pemasukan' ? '+' : '-' }} {{ $tx->formatted_jumlah }}
                        </td>
                        <td class="py-3 text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper-config='{"strategy":"fixed"}'>
                                    <i class="bi bi-three-dots-vertical text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2">
                                    @if($tx->bukti_file)
                                    <li>
                                        <a class="dropdown-item text-secondary py-2" href="{{ asset('storage/' . $tx->bukti_file) }}" target="_blank">
                                            <i class="bi bi-file-earmark-arrow-down me-2 text-primary"></i> Lihat Bukti
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    @endif

                                    @if(!empty($tx->is_locked))
                                    <li>
                                        <button class="dropdown-item py-2 text-muted disabled" disabled title="Terkunci: Laporan periode ini sudah diajukan/disetujui">
                                            <i class="bi bi-lock-fill me-2 text-muted"></i> Edit <span class="badge bg-light text-muted border ms-1">Terkunci</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item py-2 text-muted disabled" disabled title="Terkunci: Laporan periode ini sudah diajukan/disetujui">
                                            <i class="bi bi-lock-fill me-2 text-muted"></i> Hapus <span class="badge bg-light text-muted border ms-1">Terkunci</span>
                                        </button>
                                    </li>
                                    @else
                                    <li>
                                        <button class="dropdown-item py-2" onclick="openEditModal('{{ $tx->id }}', '{{ $tx->tipe }}', '{{ addslashes($tx->judul) }}', '{{ $tx->kategori }}', '{{ $tx->tanggal->format('Y-m-d') }}', '{{ $tx->jumlah }}', '{{ addslashes($tx->deskripsi ?? '') }}', '{{ $tx->bukti_file ? asset('storage/' . $tx->bukti_file) : '' }}')">
                                            <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('opkeuangan.transaksi.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger py-2">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Menampilkan {{ $transaksi->firstItem() ?? 0 }} - {{ $transaksi->lastItem() ?? 0 }} dari {{ $transaksi->total() }} transaksi</span>
            {{ $transaksi->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Catat Transaksi Baru -->
<div class="modal fade" id="modalCatatTransaksi" tabindex="-1" aria-labelledby="modalCatatTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0 mt-2 mx-2">
                <h5 class="modal-title fw-bold" id="modalCatatTransaksiLabel">Catat Transaksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('opkeuangan.transaksi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Transaksi</label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline form-radio-custom">
                                <input class="form-check-input" type="radio" name="tipe" id="tipePemasukan" value="pemasukan" checked>
                                <label class="form-check-label" for="tipePemasukan">Pemasukan (+)</label>
                            </div>
                            <div class="form-check form-check-inline form-radio-custom">
                                <input class="form-check-input" type="radio" name="tipe" id="tipePengeluaran" value="pengeluaran">
                                <label class="form-check-label" for="tipePengeluaran">Pengeluaran (-)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Transaksi</label>
                        <input type="text" class="form-control bg-light border-0 py-2" placeholder="Cth: Iuran Warga Blok A Bulan Oktober" name="judul" value="{{ old('judul') }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select class="form-select bg-light border-0 py-2" name="kategori" required>
                                <option value="Iuran Warga" {{ old('kategori') == 'Iuran Warga' ? 'selected' : '' }}>Iuran Warga</option>
                                <option value="Operasional" {{ old('kategori') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                                <option value="Donasi" {{ old('kategori') == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                                <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (Rp)</label>
                        <input type="number" class="form-control bg-light border-0 py-2" placeholder="0" name="jumlah" value="{{ old('jumlah') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi (Opsional)</label>
                        <textarea class="form-control bg-light border-0 py-2" rows="2" name="deskripsi" placeholder="Keterangan tambahan...">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Upload Bukti Transaksi (Opsional)</label>
                        <div class="border border-2 border-dashed rounded-3 p-4 text-center upload-box" id="uploadAreaTambah" style="cursor: pointer; background: #fafafa;">
                            <div id="uploadDefaultTambah">
                                <i class="bi bi-cloud-arrow-up fs-2 text-primary"></i>
                                <div class="mt-2 text-dark fw-medium">Drag & drop file Anda disini atau klik untuk browse</div>
                                <div class="small text-muted">Format: JPG, PNG, PDF (Maks. 5MB)</div>
                            </div>
                            <div id="uploadPreviewTambah" class="d-none">
                                <img id="previewImgTambah" src="" class="img-thumbnail rounded-3 shadow-sm mb-2 d-none" style="max-height: 140px;">
                                <div id="previewPdfTambah" class="d-none p-2 mb-2 bg-white rounded-3 border d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                                    <span id="previewPdfNameTambah" class="small fw-semibold text-dark"></span>
                                </div>
                                <div class="small text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> <span id="previewFileNameTambah"></span></div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2 py-1 px-3 rounded-pill" id="btnBatalUploadTambah"><i class="bi bi-x me-1"></i> Batal Pilih File</button>
                            </div>
                            <input type="file" name="bukti_file" id="inputFileTambah" class="d-none" accept=".jpg,.jpeg,.png,.pdf">
                            <input type="hidden" name="bukti_base64" id="buktiBase64Tambah">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold rounded-3">Simpan Transaksi</button>
                        <button type="button" class="btn btn-light py-2 fw-semibold rounded-3 text-muted" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Transaksi -->
<div class="modal fade" id="modalEditTransaksi" tabindex="-1" aria-labelledby="modalEditTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0 mt-2 mx-2">
                <h5 class="modal-title fw-bold" id="modalEditTransaksiLabel">Edit Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEditTransaksi" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Transaksi</label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline form-radio-custom">
                                <input class="form-check-input" type="radio" name="tipe" id="editTipePemasukan" value="pemasukan">
                                <label class="form-check-label" for="editTipePemasukan">Pemasukan (+)</label>
                            </div>
                            <div class="form-check form-check-inline form-radio-custom">
                                <input class="form-check-input" type="radio" name="tipe" id="editTipePengeluaran" value="pengeluaran">
                                <label class="form-check-label" for="editTipePengeluaran">Pengeluaran (-)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Transaksi</label>
                        <input type="text" class="form-control bg-light border-0 py-2" name="judul" id="editJudul" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select class="form-select bg-light border-0 py-2" name="kategori" id="editKategori" required>
                                <option value="Iuran Warga">Iuran Warga</option>
                                <option value="Operasional">Operasional</option>
                                <option value="Donasi">Donasi</option>
                                <option value="Setoran ke RW">Setoran ke RW</option>
                                <option value="Dana Kematian">Dana Kematian</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="tanggal" id="editTanggal" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (Rp)</label>
                        <input type="number" class="form-control bg-light border-0 py-2" name="jumlah" id="editJumlah" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi (Opsional)</label>
                        <textarea class="form-control bg-light border-0 py-2" rows="2" name="deskripsi" id="editDeskripsi" placeholder="Keterangan tambahan..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Bukti Transaksi (Opsional)</label>
                        <!-- Existing File Box -->
                        <div id="existingFileBoxEdit" class="d-none p-3 mb-2 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-check-fill fs-3 text-success"></i>
                                <div>
                                    <div class="small fw-semibold text-dark">File Bukti Terlampir</div>
                                    <a href="#" id="existingFileLinkEdit" target="_blank" class="small text-primary text-decoration-none">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka File
                                    </a>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success">Tersimpan</span>
                        </div>

                        <div class="border border-2 border-dashed rounded-3 p-4 text-center upload-box" id="uploadAreaEdit" style="cursor: pointer; background: #fafafa;">
                            <div id="uploadDefaultEdit">
                                <i class="bi bi-cloud-arrow-up fs-2 text-primary"></i>
                                <div class="mt-2 text-dark fw-medium">Drag & drop file baru atau klik untuk browse/ganti</div>
                                <div class="small text-muted">Format: JPG, PNG, PDF (Maks. 5MB)</div>
                            </div>
                            <div id="uploadPreviewEdit" class="d-none">
                                <img id="previewImgEdit" src="" class="img-thumbnail rounded-3 shadow-sm mb-2 d-none" style="max-height: 140px;">
                                <div id="previewPdfEdit" class="d-none p-2 mb-2 bg-white rounded-3 border d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                                    <span id="previewPdfNameEdit" class="small fw-semibold text-dark"></span>
                                </div>
                                <div class="small text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> <span id="previewFileNameEdit"></span></div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2 py-1 px-3 rounded-pill" id="btnBatalUploadEdit"><i class="bi bi-x me-1"></i> Batal Ganti File</button>
                            </div>
                            <input type="file" name="bukti_file" id="inputFileEdit" class="d-none" accept=".jpg,.jpeg,.png,.pdf">
                            <input type="hidden" name="bukti_base64" id="buktiBase64Edit">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold rounded-3"><i class="bi bi-check-lg me-1"></i> Simpan Perubahan</button>
                        <button type="button" class="btn btn-light py-2 fw-semibold rounded-3 text-muted" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Broadcast Tagihan -->
<div class="modal fade" id="modalBroadcast" tabindex="-1" aria-labelledby="modalBroadcastLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0 mt-2 mx-2">
                <h5 class="modal-title fw-bold" id="modalBroadcastLabel">Broadcast Notifikasi Tagihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="#" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Penerima</label>
                        <select class="form-select bg-light border-0 py-2">
                            <option>Semua Warga RT 01</option>
                            <option>Warga yang belum membayar</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Notifikasi</label>
                        <input type="text" class="form-control bg-light border-0 py-2" value="Tagihan Iuran Bulanan Oktober 2026">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pesan</label>
                        <textarea class="form-control bg-light border-0 py-2" rows="4">Yth. Bapak/Ibu Warga RT 01, mohon untuk segera melakukan pembayaran iuran bulanan sebesar Rp 250.000 untuk periode bulan Oktober 2026. Terima kasih.</textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold rounded-3"><i class="bi bi-send me-2"></i> Kirim Broadcast</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed {
        border-style: dashed !important;
        border-color: #dee2e6 !important;
    }
    .form-radio-custom input[type="radio"]:checked + label {
        color: var(--bs-primary);
        font-weight: 600;
    }
    /* EV-010: Prevent dropdown menu clipping inside table-responsive even with 1 row */
    .table-responsive {
        min-height: 180px;
        overflow-x: auto;
        overflow-y: visible !important;
    }
    .upload-box:hover {
        background: #f1f5f9 !important;
        border-color: #93c5fd !important;
    }
</style>

@push('scripts')
<script>
function openEditModal(id, tipe, judul, kategori, tanggal, jumlah, deskripsi, buktiUrl) {
    const form = document.getElementById('formEditTransaksi');
    form.action = '/op-keuangan/transaksi/' + id;

    // Set tipe radio
    if (tipe === 'pemasukan') {
        document.getElementById('editTipePemasukan').checked = true;
    } else {
        document.getElementById('editTipePengeluaran').checked = true;
    }

    document.getElementById('editJudul').value = judul;
    document.getElementById('editKategori').value = kategori;
    document.getElementById('editTanggal').value = tanggal;
    document.getElementById('editJumlah').value = jumlah;
    document.getElementById('editDeskripsi').value = deskripsi;

    // Reset edit upload state
    resetUploadState('Edit');

    const existingBox = document.getElementById('existingFileBoxEdit');
    const existingLink = document.getElementById('existingFileLinkEdit');
    if (buktiUrl && buktiUrl.trim() !== '') {
        existingBox.classList.remove('d-none');
        existingLink.href = buktiUrl;
    } else {
        existingBox.classList.add('d-none');
        existingLink.href = '#';
    }

    var modal = new bootstrap.Modal(document.getElementById('modalEditTransaksi'));
    modal.show();
}

function setupUpload(prefix) {
    const area = document.getElementById('uploadArea' + prefix);
    const input = document.getElementById('inputFile' + prefix);
    const base64Input = document.getElementById('buktiBase64' + prefix);
    const def = document.getElementById('uploadDefault' + prefix);
    const prev = document.getElementById('uploadPreview' + prefix);
    const img = document.getElementById('previewImg' + prefix);
    const pdfBox = document.getElementById('previewPdf' + prefix);
    const pdfName = document.getElementById('previewPdfName' + prefix);
    const fileName = document.getElementById('previewFileName' + prefix);
    const btnBatal = document.getElementById('btnBatalUpload' + prefix);

    if (!area || !input) return;

    area.addEventListener('click', function(e) {
        if (e.target.closest('#btnBatalUpload' + prefix)) return;
        input.click();
    });

    input.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Drag & drop support
    area.addEventListener('dragover', function(e) {
        e.preventDefault();
        area.style.background = '#e2e8f0';
    });
    area.addEventListener('dragleave', function() {
        area.style.background = '#fafafa';
    });
    area.addEventListener('drop', function(e) {
        e.preventDefault();
        area.style.background = '#fafafa';
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        }
    });

    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file bukti transaksi maksimal 5 MB.');
                resetUploadState(prefix);
                return;
            }

            fileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            def.classList.add('d-none');
            prev.classList.remove('d-none');

            const reader = new FileReader();
            reader.onload = function(ev) {
                if (base64Input) {
                    base64Input.value = ev.target.result;
                }
                if (file.type.startsWith('image/')) {
                    img.src = ev.target.result;
                    img.classList.remove('d-none');
                    pdfBox.classList.add('d-none');
                } else if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
                    img.classList.add('d-none');
                    pdfBox.classList.remove('d-none');
                    pdfName.textContent = file.name;
                } else {
                    img.classList.add('d-none');
                    pdfBox.classList.add('d-none');
                }
            };
            reader.readAsDataURL(file);
        }
    });

    btnBatal.addEventListener('click', function(e) {
        e.stopPropagation();
        resetUploadState(prefix);
    });
}

function resetUploadState(prefix) {
    const input = document.getElementById('inputFile' + prefix);
    const base64Input = document.getElementById('buktiBase64' + prefix);
    const def = document.getElementById('uploadDefault' + prefix);
    const prev = document.getElementById('uploadPreview' + prefix);
    const img = document.getElementById('previewImg' + prefix);
    const pdfBox = document.getElementById('previewPdf' + prefix);
    if (input) {
        input.value = '';
        input.setAttribute('name', 'bukti_file');
    }
    if (base64Input) base64Input.value = '';
    if (def) def.classList.remove('d-none');
    if (prev) prev.classList.add('d-none');
    if (img) { img.src = ''; img.classList.add('d-none'); }
    if (pdfBox) pdfBox.classList.add('d-none');
}

document.addEventListener('DOMContentLoaded', function() {
    setupUpload('Tambah');
    setupUpload('Edit');

    // Anti-gagal upload Windows: Saat Base64 sudah siap, copot name="bukti_file" agar browser tidak mengirim file binary ganda ke PHP temp directory
    const formCatat = document.querySelector('#modalCatatTransaksi form');
    if (formCatat) {
        formCatat.addEventListener('submit', function() {
            const b64 = document.getElementById('buktiBase64Tambah');
            const fileIn = document.getElementById('inputFileTambah');
            if (b64 && b64.value && b64.value.startsWith('data:') && fileIn) {
                fileIn.removeAttribute('name');
            }
        });
    }

    const formEdit = document.getElementById('formEditTransaksi');
    if (formEdit) {
        formEdit.addEventListener('submit', function() {
            const b64 = document.getElementById('buktiBase64Edit');
            const fileIn = document.getElementById('inputFileEdit');
            if (b64 && b64.value && b64.value.startsWith('data:') && fileIn) {
                fileIn.removeAttribute('name');
            }
        });
    }

    @if($errors->any())
    var modalTambah = new bootstrap.Modal(document.getElementById('modalCatatTransaksi'));
    modalTambah.show();
    @endif
});
</script>
@endpush
@endsection
