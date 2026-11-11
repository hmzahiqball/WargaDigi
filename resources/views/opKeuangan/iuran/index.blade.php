@extends('layouts.global')

@section('title', 'Iuran Lainnya')

@section('content')
<div class="dash-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="dash-header-title">
        <h2 class="fw-bold text-dark mb-1">Iuran Lainnya</h2>
        <p class="text-muted mb-0">Iuran sekali bayar di luar iuran wajib bulanan (mis. kegiatan, perbaikan).</p>
    </div>
    <div class="dash-header-actions d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatTagihan">
            <i class="bi bi-plus-lg me-1"></i> Buat Iuran Lainnya
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;"><ul class="mb-0 list-unstyled">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Judul Iuran</th>
                        <th>Nominal</th>
                        <th>Tenggat</th>
                        <th>Progress Terbayar</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihanList as $tagihan)
                    @php
                        $totalKK = $tagihan->total_kk ?? $tagihan->pembayaran->count();
                        $paidKK = $tagihan->paid_count;
                        $percent = $totalKK > 0 ? round(($paidKK / $totalKK) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block">{{ $tagihan->judul }}</span>
                            <span class="text-muted small">
                                @if($tagihan->unit === 'RW' && $tagihan->via_rt)
                                    <span class="badge bg-primary bg-opacity-10 text-primary" data-testid="badge-via-rt">{{ $unit === 'RT' ? 'Dari RW, setor ke RW' : 'Lewat RT' }}</span>
                                @endif
                                @if($tagihan->nominal_fix)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Nominal fix {{ $tagihan->formatted_nominal }}</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info">Minimal {{ $tagihan->formatted_nominal }}</span>
                                @endif
                            </span>
                        </td>
                        <td>{{ $tagihan->formatted_nominal }}</td>
                        <td>{{ $tagihan->tenggat ? \Carbon\Carbon::parse($tagihan->tenggat)->translatedFormat('d M Y') : '-' }}</td>
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
                        <td colspan="5" class="text-center py-5">
                            <h5 class="fw-bold text-dark mb-1">Belum ada iuran lainnya</h5>
                            <p class="text-muted mb-0">Gunakan tombol Buat Iuran Lainnya di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tagihanList->hasPages())
    <div class="card-footer bg-white border-0 py-3">{{ $tagihanList->links() }}</div>
    @endif
</div>

<!-- Modal Buat Iuran Lainnya -->
<div class="modal fade" id="modalBuatTagihan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" action="{{ route('opkeuangan.tagihan.store') }}" method="POST">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Iuran Lainnya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul Iuran</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Iuran Perbaikan Jalan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nominal (Rp)</label>
                    <input type="text" inputmode="numeric" data-money name="nominal" class="form-control" placeholder="50.000" required>
                    <div class="form-text">Isi sebagai <strong>nominal minimal</strong> iuran. Centang di bawah bila nominal pasti (tidak bisa diubah saat bayar).</div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="nominal_fix" value="1" id="nominalFix">
                    <label class="form-check-label small" for="nominalFix">Nominal fix (sesuai tertera, tidak bisa diubah)</label>
                </div>

                @if($unit === 'RW')
                <div class="border rounded-3 p-3 mb-3 bg-light" data-testid="opsi-via-rt">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="via_rt" value="1" id="viaRt" checked>
                        <label class="form-check-label fw-semibold" for="viaRt">Dikumpulkan lewat RT</label>
                    </div>
                    <div class="form-text mb-0">Warga membayar ke RT (rekening RT). Operator RT memverifikasi, lalu menyetor seluruh yang terkumpul ke RW. Hilangkan centang bila warga membayar langsung ke RW. Pilihan ini tidak dapat diubah setelah iuran dibuat.</div>
                </div>
                @endif
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori Kas (pemasukan)</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">-- Pilih kategori --</option>
                        @foreach($kategoriPemasukan as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tenggat Waktu</label>
                    <input type="date" name="tenggat" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Pengingat</label>
                    <div class="d-flex flex-wrap gap-1" style="max-height:120px;overflow-y:auto;">
                        @for($d = 1; $d <= 31; $d++)
                            <div class="form-check form-check-inline me-0" style="width:44px;">
                                <input class="form-check-input" type="checkbox" name="reminder_tanggal[]" value="{{ $d }}" id="rem{{ $d }}">
                                <label class="form-check-label small" for="rem{{ $d }}">{{ $d }}</label>
                            </div>
                        @endfor
                    </div>
                    <div class="form-text">Pengingat dikirim ke warga yang belum bayar pada tanggal-tanggal ini tiap bulan selama iuran aktif.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" class="form-control" rows="2" placeholder="Keterangan iuran..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Buat &amp; Sebar</button>
            </div>
        </form>
    </div>
</div>
@endsection
