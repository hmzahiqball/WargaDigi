@extends('layouts.global')

@section('title', 'Persetujuan Berita')

@push('styles')
<style>
    .berita-table {
        table-layout: fixed;
        border-collapse: collapse;
        min-width: 900px;
    }
    .berita-table th,
    .berita-table td {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #E5E7EB;
    }
    .berita-table thead th {
        text-align: center;
        border-bottom: 2px solid #D1D5DB;
    }
    .berita-table td:nth-child(1),
    .berita-table td:nth-child(3),
    .berita-table td:nth-child(4),
    .berita-table td:nth-child(5),
    .berita-table td:nth-child(6),
    .berita-table th:nth-child(1),
    .berita-table th:nth-child(3),
    .berita-table th:nth-child(4),
    .berita-table th:nth-child(5),
    .berita-table th:nth-child(6) {
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Persetujuan Berita</h2>
        <p class="text-muted mb-0">Tinjau, setujui, atau tolak konten berita yang diajukan oleh Operator.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Data Table --}}
<div class="card card-custom shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 berita-table">
            <colgroup>
                <col style="width: 14%;">
                <col style="width: 32%;">
                <col style="width: 12%;">
                <col style="width: 15%;">
                <col style="width: 12%;">
                <col style="width: 15%;">
            </colgroup>
            <thead class="table-light">
                <tr class="text-muted text-uppercase text-xs fw-bold" style="letter-spacing: 0.5px;">
                    <th scope="col" class="py-3 px-3">Thumbnail</th>
                    <th scope="col" class="py-3 px-3">Judul</th>
                    <th scope="col" class="py-3 px-3">Kategori</th>
                    <th scope="col" class="py-3 px-3">Penulis</th>
                    <th scope="col" class="py-3 px-3">Status</th>
                    <th scope="col" class="py-3 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($berita as $item)
                    @php
                        // Mapping status colors
                        $statusColors = [
                            'Draft' => ['bg' => '#F3F4F6', 'color' => '#6B7280'],
                            'Review' => ['bg' => '#FEF3C7', 'color' => '#D97706'],
                            'Revisi' => ['bg' => '#FEE2E2', 'color' => '#991B1B'],
                            'Publish' => ['bg' => '#D1FAE5', 'color' => '#065F46'],
                            'Archive' => ['bg' => '#E5E7EB', 'color' => '#4B5563'],
                        ];
                        $s_bg = $statusColors[$item->status]['bg'] ?? '#F3F4F6';
                        $s_color = $statusColors[$item->status]['color'] ?? '#6B7280';
                        $imageUrl = $item->featured_image ? asset($item->featured_image) : 'https://placehold.co/400x300?text=No+Image';
                    @endphp
                    <tr>
                        <td class="py-3 px-3">
                            <div class="rounded-1 overflow-hidden bg-light mx-auto" style="width: 150px; height: 95px;">
                                <img src="{{ $imageUrl }}" alt="{{ $item->judul_berita }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </td>
                        <td class="py-3 px-3">
                            <div class="fw-bold text-dark small">{{ $item->judul_berita }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="badge rounded-1 px-2 py-1 fw-medium" style="font-size: 10px; background: #E5E7EB; color: #374151;">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="text-muted small py-3 px-3">{{ $item->operator->username ?? 'Unknown' }}</td>
                        <td class="py-3 px-3">
                            <span class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-1" style="background: {{ $s_bg }}; color: {{ $s_color }};">
                                <span class="rounded-circle" style="width: 6px; height: 6px; display: inline-block; background: {{ $s_color }};"></span>
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <button type="button" class="btn btn-sm rounded-3 fw-semibold px-3" style="font-size: 12px; background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;" onclick="openDetailModal('{{ $item->id }}')">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-newspaper fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada berita yang perlu ditinjau.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-4 py-3 border-top text-muted small">
        {{ $berita->links() }}
    </div>
</div>

{{-- Modal Detail Berita --}}
<div class="modal fade" id="modalDetailBerita" tabindex="-1" aria-labelledby="modalDetailBeritaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark fs-5" id="modalDetailBeritaLabel">Detail Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    {{-- Left Column: Image & Info --}}
                    <div class="col-lg-4">
                        <div class="rounded-3 overflow-hidden mb-3 border" style="height: 200px; background-color: #f8f9fa;">
                            <img id="detailImage" src="" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        
                        <div class="p-4 rounded-3" style="background-color: #F3F4F6; border: 1px solid #E5E7EB;">
                            <h6 class="fw-bold text-secondary mb-4" style="font-size: 13px; letter-spacing: 0.5px;">INFORMASI PUBLIKASI</h6>
                            
                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">Status</span>
                                <span id="detailStatus" class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                                    <span class="rounded-circle status-dot" style="width: 6px; height: 6px; display: inline-block;"></span>
                                    <span class="status-text">Publish</span>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">Kategori</span>
                                <span id="detailKategori" class="badge rounded-1 px-2 py-1 fw-medium" style="background: #E5E7EB; color: #374151; font-size: 12px; border: 1px solid #D1D5DB;">
                                    SOSIAL
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">Penulis</span>
                                <div class="text-dark fw-medium fs-6 d-flex align-items-center gap-2">
                                    <i class="bi bi-person text-secondary"></i>
                                    <span id="detailPenulis">Budi Santoso</span>
                                </div>
                            </div>
                            
                            <div>
                                <span class="d-block text-muted small mb-1">Tanggal Publikasi</span>
                                <div class="text-dark fw-medium fs-6 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-event text-secondary"></i>
                                    <span id="detailTanggal">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Right Column: Content --}}
                    <div class="col-lg-8">
                        <h3 id="detailJudul" class="fw-bold text-dark mb-4" style="line-height: 1.4;"></h3>
                        
                        <div id="detailKonten" class="text-dark" style="line-height: 1.7; font-size: 15px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-light border-top" id="modalFooterActions">
                <button type="button" class="btn btn-danger fw-semibold px-4" onclick="bukaModalTolak()">
                    Tolak
                </button>
                <form id="formApprove" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success fw-semibold px-4 text-white">
                        Setujui & Publikasikan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Catatan Revisi --}}
<div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 rounded-4 shadow" id="formTolak" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="modal-header px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold text-danger fs-5">Tolak Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-3 small">Silakan berikan catatan perbaikan agar Operator dapat merevisi konten tersebut.</p>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Catatan Perbaikan <span class="text-danger">*</span></label>
                    <textarea class="form-control bg-light" name="catatan_revisi" rows="4" placeholder="Masukkan catatan perbaikan..." required></textarea>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-light border-top">
                <button type="button" class="btn btn-outline-secondary fw-semibold px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-semibold px-4 text-white">
                    Tolak Berita
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const dataBerita = @json($berita->items());
    let currentBeritaId = null;

    function openDetailModal(id) {
        const item = dataBerita.find(b => b.id == id);
        if (!item) return;

        currentBeritaId = item.id;

        // Populate elements
        document.getElementById('detailImage').src = item.featured_image ? item.featured_image : 'https://placehold.co/400x300?text=No+Image';
        document.getElementById('detailImage').alt = item.judul_berita;
        
        const statusBadge = document.getElementById('detailStatus');
        
        const statusColors = {
            'Draft': { bg: '#F3F4F6', color: '#6B7280' },
            'Review': { bg: '#FEF3C7', color: '#D97706' },
            'Revisi': { bg: '#FEE2E2', color: '#991B1B' },
            'Publish': { bg: '#D1FAE5', color: '#065F46' },
            'Archive': { bg: '#E5E7EB', color: '#4B5563' }
        };
        const s_bg = statusColors[item.status]?.bg || '#F3F4F6';
        const s_color = statusColors[item.status]?.color || '#6B7280';
        
        statusBadge.style.background = s_bg;
        statusBadge.style.color = s_color;
        statusBadge.querySelector('.status-dot').style.background = s_color;
        statusBadge.querySelector('.status-text').textContent = item.status;

        document.getElementById('detailKategori').textContent = item.kategori;
        document.getElementById('detailPenulis').textContent = item.operator ? item.operator.username : 'Unknown';
        document.getElementById('detailJudul').textContent = item.judul_berita;
        document.getElementById('detailKonten').innerHTML = item.isi_berita || '<p class="text-muted italic">Tidak ada konten</p>';
        
        const dateStr = item.tanggal_publish ? new Date(item.tanggal_publish).toLocaleString('id-ID') : '-';
        document.getElementById('detailTanggal').textContent = dateStr;

        // Update actions based on status
        const footer = document.getElementById('modalFooterActions');
        if (item.status === 'Review') {
            footer.classList.remove('d-none');
            document.getElementById('formApprove').action = `/rw/berita/${item.id}/approve`;
            document.getElementById('formTolak').action = `/rw/berita/${item.id}/reject`;
        } else {
            footer.classList.add('d-none');
        }

        const detailModal = new bootstrap.Modal(document.getElementById('modalDetailBerita'));
        detailModal.show();
    }

    function bukaModalTolak() {
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('modalDetailBerita'));
        if (detailModal) detailModal.hide();
        
        const modalTolak = new bootstrap.Modal(document.getElementById('modalTolak'));
        modalTolak.show();
    }
</script>
@endpush
@endsection