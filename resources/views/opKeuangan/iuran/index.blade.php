@extends('layouts.global')

@section('title', 'Pantau Iuran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Pantau Iuran</h2>
        <p class="text-muted mb-0">Kelola tagihan warga dan pantau status pembayaran.</p>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatTagihan">
            <i class="bi bi-plus-lg me-1"></i> Buat Tagihan Insidental
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Judul Tagihan</th>
                        <th>Bulan/Tahun</th>
                        <th>Nominal</th>
                        <th>Jenis</th>
                        <th>Progress Terbayar</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihanList as $tagihan)
                    @php
                        $totalKK = $tagihan->pembayaran->count();
                        $paidKK = $tagihan->paid_count;
                        $percent = $totalKK > 0 ? round(($paidKK / $totalKK) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block">{{ $tagihan->judul }}</span>
                            <span class="text-muted small">Tenggat: {{ $tagihan->tenggat ? \Carbon\Carbon::parse($tagihan->tenggat)->translatedFormat('d M Y') : '-' }}</span>
                        </td>
                        <td>{{ $tagihan->periode_bulan }} / {{ $tagihan->periode_tahun }}</td>
                        <td>{{ $tagihan->formatted_nominal }}</td>
                        <td>
                            @if($tagihan->jenis === 'rutin')
                                <span class="badge bg-primary bg-opacity-10 text-primary">Rutin</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Insidental</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="small fw-semibold">{{ $paidKK }}/{{ $totalKK }}</span>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('opkeuangan.iuran.detail', $tagihan->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <h5 class="fw-bold text-dark mb-1">Belum ada tagihan</h5>
                            <p class="text-muted mb-0">Gunakan tombol Buat Tagihan Insidental di atas, atau tunggu sistem generate tagihan rutin secara otomatis.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tagihanList->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $tagihanList->links() }}
    </div>
    @endif
</div>

<!-- Modal Buat Tagihan Insidental -->
<div class="modal fade" id="modalBuatTagihan" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content border-0 shadow" action="{{ route('opkeuangan.tagihan.store') }}" method="POST">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Tagihan Insidental</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul Tagihan</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Iuran Perbaikan Jalan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nominal (Rp)</label>
                    <input type="number" name="nominal" class="form-control" placeholder="50000" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tenggat Waktu</label>
                    <input type="date" name="tenggat" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tambahkan keterangan tagihan..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Buat & Sebar Tagihan</button>
            </div>
        </form>
    </div>
</div>
@endsection
