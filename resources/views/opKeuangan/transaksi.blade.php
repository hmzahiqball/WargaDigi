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
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $tx->kategori }}</span>
                        </td>
                        <td class="py-3 fw-bold {{ $tx->tipe == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                            {{ $tx->tipe == 'pemasukan' ? '+' : '-' }} {{ $tx->formatted_jumlah }}
                        </td>
                        <td class="py-3 text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <button class="dropdown-item" onclick="openEditModal('{{ $tx->id }}', '{{ $tx->tipe }}', '{{ addslashes($tx->judul) }}', '{{ $tx->kategori }}', '{{ $tx->tanggal->format('Y-m-d') }}', '{{ $tx->jumlah }}', '{{ addslashes($tx->deskripsi ?? '') }}')">
                                            <i class="bi bi-pencil me-2 text-primary"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('opkeuangan.transaksi.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
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
                        <input type="text" class="form-control bg-light border-0 py-2" placeholder="Cth: Iuran Warga Blok A Bulan Oktober" name="judul" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select class="form-select bg-light border-0 py-2" name="kategori" required>
                                <option value="Iuran Warga">Iuran Warga</option>
                                <option value="Operasional">Operasional</option>
                                <option value="Donasi">Donasi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="tanggal" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (Rp)</label>
                        <input type="number" class="form-control bg-light border-0 py-2" placeholder="0" name="jumlah" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi (Opsional)</label>
                        <textarea class="form-control bg-light border-0 py-2" rows="2" name="deskripsi" placeholder="Keterangan tambahan..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Upload Bukti Transaksi (Opsional)</label>
                        <div class="border border-2 border-dashed rounded-3 p-4 text-center text-muted upload-area" style="cursor: pointer; background: #fafafa;">
                            <i class="bi bi-cloud-arrow-up fs-2"></i>
                            <div class="mt-2">Drag & drop file Anda disini atau klik untuk browse</div>
                            <div class="small">Max file size: 5MB (JPG, PNG, PDF)</div>
                            <input type="file" name="bukti_file" class="d-none">
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
                        <label class="form-label fw-semibold">Ganti Bukti Transaksi (Opsional)</label>
                        <div class="border border-2 border-dashed rounded-3 p-4 text-center text-muted upload-area" style="cursor: pointer; background: #fafafa;">
                            <i class="bi bi-cloud-arrow-up fs-2"></i>
                            <div class="mt-2">Drag & drop file baru atau klik untuk browse</div>
                            <div class="small">Max file size: 5MB (JPG, PNG, PDF)</div>
                            <input type="file" name="bukti_file" class="d-none">
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
</style>

@push('scripts')
<script>
function openEditModal(id, tipe, judul, kategori, tanggal, jumlah, deskripsi) {
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

    var modal = new bootstrap.Modal(document.getElementById('modalEditTransaksi'));
    modal.show();
}

// Upload area click handler
document.querySelectorAll('.upload-area').forEach(function(area) {
    area.addEventListener('click', function() {
        this.querySelector('input[type="file"]').click();
    });
});
</script>
@endpush
@endsection
