{{--
    Tabel riwayat pengajuan surat.
    Params: $riwayat (paginator PengajuanSurat), $backRoute (nama route tombol kembali)
--}}
@php
    $badge = function ($status) {
        return match ($status) {
            'Diajukan'     => ['bg-warning bg-opacity-10 text-warning', 'Menunggu RT'],
            'Disetujui RT' => ['bg-info bg-opacity-10 text-info', 'Menunggu RW'],
            'Selesai'      => ['bg-success bg-opacity-10 text-success', 'Selesai'],
            'Ditolak RT'   => ['bg-danger bg-opacity-10 text-danger', 'Ditolak RT'],
            'Ditolak RW'   => ['bg-danger bg-opacity-10 text-danger', 'Ditolak RW'],
            default        => ['bg-secondary bg-opacity-10 text-secondary', $status],
        };
    };
@endphp

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">Riwayat Pengajuan</h2>
        <p class="text-muted mb-0">Daftar seluruh pengajuan surat beserta statusnya.</p>
    </div>
    <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary" style="border-radius: 0.5rem;">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Persetujuan
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-body p-4">
        {{-- Filter --}}
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0" placeholder="Cari nama, NIK, atau jenis surat...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light border-0">
                    <option value="">Semua Status</option>
                    @foreach(['Diajukan','Disetujui RT','Selesai','Ditolak RT','Ditolak RW'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                @if(request('q') || request('status'))
                    <a href="{{ route(request()->route()->getName()) }}" class="btn btn-light border text-danger" title="Reset"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold text-muted py-3">Tanggal</th>
                        <th class="small fw-semibold text-muted py-3">Pemohon</th>
                        <th class="small fw-semibold text-muted py-3">Jenis Surat</th>
                        <th class="small fw-semibold text-muted py-3">RT</th>
                        <th class="small fw-semibold text-muted py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        @php [$cls, $label] = $badge($item->status); @endphp
                        <tr>
                            <td class="small py-3">{{ $item->created_at?->format('d M Y, H:i') }}</td>
                            <td class="py-3">
                                <div class="fw-semibold text-dark">{{ $item->penduduk->nama_lengkap ?? 'Tidak diketahui' }}</div>
                                <small class="text-muted" style="font-family: monospace;">{{ $item->penduduk->nik ?? '-' }}</small>
                            </td>
                            <td class="py-3">{{ $item->tipe_surat }}</td>
                            <td class="py-3">{{ $item->penduduk->keluarga->rt->kode_rt ?? '-' }}</td>
                            <td class="py-3"><span class="badge {{ $cls }}">{{ $label }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada pengajuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayat->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">Menampilkan {{ $riwayat->firstItem() }}-{{ $riwayat->lastItem() }} dari {{ $riwayat->total() }}</span>
                <div>{{ $riwayat->links('pagination::bootstrap-5') }}</div>
            </div>
        @endif
    </div>
</div>
