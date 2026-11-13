@extends('layouts.global')

@section('title', 'Catat Transaksi')

@section('content')
<div class="dash-header d-flex justify-content-between align-items-center mb-4">
    <div class="dash-header-title">
        <h2 class="fw-bold text-dark mb-1">Catat Transaksi</h2>
        <p class="text-muted mb-0">Kelola arus kas masuk dan keluar dengan mudah.</p>
    </div>
    <div class="dash-header-actions d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCatatTransaksi">
            <i class="bi bi-plus-lg me-1"></i> Catat Transaksi Baru
        </button>
    </div>
</div>



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
        {{-- Hint istilah buku kas --}}
        <div class="alert alert-light border small text-muted d-flex align-items-start gap-2 mb-3" role="note">
            <i class="bi bi-info-circle mt-1"></i>
            <span><strong>Debet</strong> = uang masuk, <strong>Kredit</strong> = uang keluar. Kolom <strong>Saldo</strong> berjalan sejak awal pencatatan. Saat difilter per bulan, baris total menampilkan <strong>saldo awal</strong> (akumulasi bulan sebelumnya) dan <strong>saldo akhir</strong> periode. Transaksi diurutkan dari yang terbaru.</span>
        </div>
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('opkeuangan.transaksi.index') }}" class="row g-3 mb-4">
            <div class="col-md-2">
                <label class="form-label text-muted small">Tahun</label>
                <select name="tahun" class="form-select bg-light border-0">
                    <option value="">Semua</option>
                    @for($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ (string)($tahunAktif ?? '') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Bulan</label>
                <select name="bulan" class="form-select bg-light border-0">
                    <option value="">Semua</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ (string)($bulanAktif ?? '') === sprintf('%02d', $m) ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
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
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->nama }}" {{ request('kategori') == $kat->nama ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
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

        {{-- ===== Tabel Buku Kas (desktop) ===== --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-muted small fw-semibold py-3">TANGGAL</th>
                        <th class="text-muted small fw-semibold py-3">KETERANGAN</th>
                        <th class="text-muted small fw-semibold py-3 text-end">DEBET</th>
                        <th class="text-muted small fw-semibold py-3 text-end">KREDIT</th>
                        <th class="text-muted small fw-semibold py-3 text-end">SALDO</th>
                        <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                    </tr>
                    {{-- Baris total periode, tepat di atas kolomnya --}}
                    <tr class="table-light">
                        <th class="py-2 text-muted small fw-normal" colspan="2">
                            Total periode terpilih
                            <span class="d-block text-muted" style="font-size:11px;">Saldo awal periode: Rp {{ number_format($ringkasan['saldo_awal'], 0, ',', '.') }}</span>
                        </th>
                        <th class="py-2 text-end text-success">Rp {{ number_format($ringkasan['debet'], 0, ',', '.') }}</th>
                        <th class="py-2 text-end text-danger">Rp {{ number_format($ringkasan['kredit'], 0, ',', '.') }}</th>
                        <th class="py-2 text-end {{ $ringkasan['saldo_akhir'] < 0 ? 'text-danger' : 'text-dark' }}">
                            <span class="d-block text-muted fw-normal" style="font-size:11px;">Saldo akhir</span>
                            Rp {{ number_format($ringkasan['saldo_akhir'], 0, ',', '.') }}
                        </th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($transaksi as $tx)
                    <tr>
                        <td class="py-3 text-dark text-nowrap">{{ $tx->tanggal->format('d M Y') }}</td>
                        <td class="py-3">
                            <div class="fw-semibold text-dark">{{ $tx->judul }}</div>
                            @if($tx->deskripsi)
                                <div class="text-muted small text-truncate" style="max-width: 260px;">{{ $tx->deskripsi }}</div>
                            @endif
                            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $tx->kategori }}</span>
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
                        <td class="py-3 text-end fw-semibold text-success">{{ $tx->tipe == 'pemasukan' ? $tx->formatted_jumlah : '-' }}</td>
                        <td class="py-3 text-end fw-semibold text-danger">{{ $tx->tipe == 'pengeluaran' ? $tx->formatted_jumlah : '-' }}</td>
                        <td class="py-3 text-end fw-bold {{ ($tx->saldo_berjalan ?? 0) < 0 ? 'text-danger' : 'text-dark' }}">
                            {{ $tx->saldo_berjalan !== null ? 'Rp ' . number_format($tx->saldo_berjalan, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 text-end">
                            @include('opKeuangan.partials._aksi-transaksi', ['tx' => $tx])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Ringkasan total untuk tampilan mobile --}}
        <div class="d-md-none border rounded-3 p-3 mb-2 bg-light small">
            <div class="d-flex justify-content-between"><span class="text-muted">Saldo Awal</span><span class="fw-semibold">Rp {{ number_format($ringkasan['saldo_awal'], 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Total Debet</span><span class="fw-semibold text-success">Rp {{ number_format($ringkasan['debet'], 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Total Kredit</span><span class="fw-semibold text-danger">Rp {{ number_format($ringkasan['kredit'], 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between border-top pt-1 mt-1"><span class="text-muted">Saldo Akhir</span><span class="fw-bold {{ $ringkasan['saldo_akhir'] < 0 ? 'text-danger' : 'text-dark' }}">Rp {{ number_format($ringkasan['saldo_akhir'], 0, ',', '.') }}</span></div>
        </div>

        {{-- ===== Daftar Buku Kas (mobile) ===== --}}
        <div class="d-md-none">
            @forelse($transaksi as $tx)
            <div class="border rounded-3 p-3 mb-2" style="background:#fafafa;">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="text-truncate">
                        <div class="fw-semibold text-dark text-truncate">{{ $tx->judul }}</div>
                        <div class="text-muted" style="font-size:11px;"><i class="bi bi-calendar3 me-1"></i>{{ $tx->tanggal->format('d M Y') }}</div>
                        <span class="badge bg-light text-dark border px-2 py-1 mt-1">{{ $tx->kategori }}</span>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="fw-bold {{ $tx->tipe == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                            {{ $tx->tipe == 'pemasukan' ? '+' : '-' }} {{ $tx->formatted_jumlah }}
                        </div>
                        <div class="text-muted" style="font-size:11px;">Saldo: Rp {{ number_format($tx->saldo_berjalan ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="d-flex gap-1 flex-wrap">
                        @if($tx->bukti_file)
                            <a href="{{ asset('storage/' . $tx->bukti_file) }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none px-2 py-1 border border-primary border-opacity-25 rounded-pill"><i class="bi bi-paperclip me-1"></i>Bukti</a>
                        @endif
                        @if(!empty($tx->is_locked))
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 rounded-pill"><i class="bi bi-lock-fill me-1"></i>Terkunci</span>
                        @endif
                    </div>
                    @include('opKeuangan.partials._aksi-transaksi', ['tx' => $tx])
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">Belum ada transaksi ditemukan.</div>
            @endforelse
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
                                <input class="form-check-input" type="radio" name="tipe" id="tipePemasukan" value="pemasukan" checked onchange="filterKategori('tambah','pemasukan')">
                                <label class="form-check-label" for="tipePemasukan">Pemasukan (+)</label>
                            </div>
                            <div class="form-check form-check-inline form-radio-custom">
                                <input class="form-check-input" type="radio" name="tipe" id="tipePengeluaran" value="pengeluaran" onchange="filterKategori('tambah','pengeluaran')">
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
                            <select class="form-select bg-light border-0 py-2" name="kategori_id" id="kategoriTambah" required>
                                <option value="">-- Pilih kategori --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (Rp)</label>
                        <input type="text" inputmode="numeric" data-money class="form-control bg-light border-0 py-2" placeholder="0" name="jumlah" value="{{ old('jumlah') }}" required>
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
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="bukaKameraBukti('Tambah')"><i class="bi bi-camera me-1"></i>Ambil Foto</button>
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
                                <input class="form-check-input" type="radio" name="tipe" id="editTipePemasukan" value="pemasukan" onchange="filterKategori('edit','pemasukan')">
                                <label class="form-check-label" for="editTipePemasukan">Pemasukan (+)</label>
                            </div>
                            <div class="form-check form-check-inline form-radio-custom">
                                <input class="form-check-input" type="radio" name="tipe" id="editTipePengeluaran" value="pengeluaran" onchange="filterKategori('edit','pengeluaran')">
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
                            <select class="form-select bg-light border-0 py-2" name="kategori_id" id="editKategori" required>
                                <option value="">-- Pilih kategori --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="tanggal" id="editTanggal" max="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (Rp)</label>
                        <input type="text" inputmode="numeric" data-money class="form-control bg-light border-0 py-2" name="jumlah" id="editJumlah" required>
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
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="bukaKameraBukti('Edit')"><i class="bi bi-camera me-1"></i>Ambil Foto</button>
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
// Master kategori (lingkup unit + umum, aktif). Dipakai untuk mengisi dropdown
// kategori sesuai tipe transaksi yang dipilih.
const KATEGORI_MASTER = @json($kategoriList->map(fn ($k) => ['id' => $k->id, 'nama' => $k->nama, 'tipe' => $k->tipe])->values());

// Isi dropdown kategori untuk form 'tambah'/'edit' sesuai tipe (termasuk 'keduanya').
window.filterKategori = function(form, tipe, selectedId) {
    const sel = document.getElementById(form === 'tambah' ? 'kategoriTambah' : 'editKategori');
    if (!sel) return;
    const terpilih = selectedId ?? sel.value;
    sel.innerHTML = '<option value="">-- Pilih kategori --</option>';
    KATEGORI_MASTER.filter(k => k.tipe === tipe || k.tipe === 'keduanya').forEach(function (k) {
        const o = document.createElement('option');
        o.value = k.id; o.textContent = k.nama;
        if (String(k.id) === String(terpilih)) o.selected = true;
        sel.appendChild(o);
    });
};

// Inisialisasi dropdown kategori form tambah (default tipe: pemasukan).
document.addEventListener('DOMContentLoaded', function () {
    const awal = document.querySelector('input[name="tipe"]:checked')?.value || 'pemasukan';
    if (window.filterKategori) filterKategori('tambah', awal);
});

function openEditModal(id, tipe, judul, kategoriId, tanggal, jumlah, deskripsi, buktiUrl) {
    const form = document.getElementById('formEditTransaksi');
    form.action = '/op-keuangan/transaksi/' + id;

    // Set tipe radio
    if (tipe === 'pemasukan') {
        document.getElementById('editTipePemasukan').checked = true;
    } else {
        document.getElementById('editTipePengeluaran').checked = true;
    }

    document.getElementById('editJudul').value = judul;
    filterKategori('edit', tipe, kategoriId);
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
                window.tampilkanPemberitahuan('Ukuran file bukti transaksi maksimal 5 MB.');
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

// Tampilkan hasil jepret kamera sebagai bukti (mengisi preview + base64),
// menyamai alur input file agar submit tetap memakai base64.
function terapkanFotoBukti(prefix, dataUrl) {
    const base64Input = document.getElementById('buktiBase64' + prefix);
    const input = document.getElementById('inputFile' + prefix);
    const def = document.getElementById('uploadDefault' + prefix);
    const prev = document.getElementById('uploadPreview' + prefix);
    const img = document.getElementById('previewImg' + prefix);
    const pdfBox = document.getElementById('previewPdf' + prefix);
    const fileName = document.getElementById('previewFileName' + prefix);

    if (input) { input.value = ''; input.removeAttribute('name'); } // pakai base64, bukan file
    if (base64Input) base64Input.value = dataUrl;
    if (def) def.classList.add('d-none');
    if (prev) prev.classList.remove('d-none');
    if (pdfBox) pdfBox.classList.add('d-none');
    if (img) { img.src = dataUrl; img.classList.remove('d-none'); }
    if (fileName) fileName.textContent = 'Foto dari kamera';
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

// ---- Kamera bukti transaksi (webcam desktop / kamera mobile) ----
let streamBukti = null;
let prefixKameraBukti = null;

window.bukaKameraBukti = function(prefix) {
    prefixKameraBukti = prefix;
    const modalEl = document.getElementById('modalKameraBukti');
    const modal = new bootstrap.Modal(modalEl);
    const video = document.getElementById('videoKameraBukti');
    const status = document.getElementById('statusKameraBukti');
    status.textContent = 'Memulai kamera...';
    modal.show();

    navigator.mediaDevices?.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(s) {
            streamBukti = s;
            video.srcObject = s;
            status.textContent = '';
        })
        .catch(function() {
            status.textContent = 'Tidak dapat mengakses kamera. Pastikan izin diberikan dan memakai HTTPS/localhost.';
        });

    modalEl.addEventListener('hidden.bs.modal', hentikanKameraBukti, { once: true });
};

function hentikanKameraBukti() {
    if (streamBukti) { streamBukti.getTracks().forEach(t => t.stop()); streamBukti = null; }
}

document.addEventListener('DOMContentLoaded', function() {
    const btnJepret = document.getElementById('btnJepretBukti');
    if (btnJepret) {
        btnJepret.addEventListener('click', function() {
            const video = document.getElementById('videoKameraBukti');
            if (!video || !video.videoWidth) return;
            const canvas = document.getElementById('canvasKameraBukti');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            if (prefixKameraBukti) terapkanFotoBukti(prefixKameraBukti, dataUrl);
            bootstrap.Modal.getInstance(document.getElementById('modalKameraBukti'))?.hide();
        });
    }
});
</script>
@endpush

{{-- Modal Kamera Bukti Transaksi --}}
<div class="modal fade" id="modalKameraBukti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:0.75rem;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-camera me-2 text-success"></i>Ambil Foto Bukti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted small">Arahkan kamera ke bukti transaksi lalu tekan Jepret.</p>
                <div class="position-relative d-inline-block w-100" style="background:#000;border-radius:0.5rem;overflow:hidden;">
                    <video id="videoKameraBukti" autoplay playsinline muted style="width:100%;max-height:60vh;object-fit:contain;background:#000;"></video>
                </div>
                <canvas id="canvasKameraBukti" class="d-none"></canvas>
                <div class="mt-2"><span class="small text-muted" id="statusKameraBukti"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnJepretBukti"><i class="bi bi-camera-fill me-1"></i>Jepret</button>
            </div>
        </div>
    </div>
</div>
@endsection
