@extends('layouts.global')

@section('title', 'Manajemen Penduduk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">Manajemen Penduduk</h2>
        <p class="text-muted mb-0">Data penduduk terdaftar di lingkungan RW 21.</p>
    </div>
    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
        <i class="bi bi-people-fill me-1"></i> Total: {{ number_format($totalPenduduk, 0, ',', '.') }} jiwa
    </span>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('rw.penduduk.index') }}" class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-muted small">Cari Penduduk</label>
                <input type="text" name="q" value="{{ $search }}" class="form-control bg-light border-0" placeholder="Nama lengkap atau NIK...">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small">RT</label>
                <select name="rt_id" class="form-select bg-light border-0">
                    <option value="">Semua RT</option>
                    @foreach($daftarRt as $rt)
                        <option value="{{ $rt->id }}" {{ (string) $rtId === (string) $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if($search !== '' || $rtId)
                <a href="{{ route('rw.penduduk.index') }}" class="btn btn-light border text-danger" title="Reset Filter">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-muted small fw-semibold py-3">NAMA / NIK</th>
                        <th class="text-muted small fw-semibold py-3">L/P</th>
                        <th class="text-muted small fw-semibold py-3">HUBUNGAN KELUARGA</th>
                        <th class="text-muted small fw-semibold py-3">NO. KK</th>
                        <th class="text-muted small fw-semibold py-3">RT</th>
                        <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($penduduk as $p)
                    <tr>
                        <td class="py-3">
                            <div class="fw-semibold text-dark">{{ $p->nama_lengkap }}</div>
                            <div class="text-muted small">{{ $p->nik }}</div>
                        </td>
                        <td class="py-3">{{ $p->jenis_kelamin }}</td>
                        <td class="py-3">{{ $p->status_hubungan_keluarga }}</td>
                        <td class="py-3">{{ $p->keluarga?->no_kk ?? '-' }}</td>
                        <td class="py-3">
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $p->keluarga?->rt?->nama_rt ?? '-' }}</span>
                        </td>
                        <td class="py-3 text-end">
                            <a href="{{ route('rw.penduduk.show', $p->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            @if($search !== '' || $rtId)
                                Tidak ada penduduk yang cocok dengan filter.
                            @else
                                Belum ada data penduduk.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small">Menampilkan {{ $penduduk->firstItem() ?? 0 }} - {{ $penduduk->lastItem() ?? 0 }} dari {{ $penduduk->total() }} penduduk</span>
            {{ $penduduk->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
