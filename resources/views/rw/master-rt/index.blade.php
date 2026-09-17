@extends('layouts.global')

@section('title', 'Manajemen RT')

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">Manajemen Data RT</h2>
        <p class="text-muted mb-0">Kelola daftar Rukun Tetangga (RT) di lingkungan RW.</p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahRt">
        <i class="bi bi-plus-lg me-1"></i> Tambah RT
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any() && !$errors->has('edit'))
<div class="alert alert-danger border-0 shadow-sm">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-body p-4">
        {{-- Search --}}
        <form method="GET" action="{{ route('rw.master-rt.index') }}" class="mb-3">
            <div class="input-group" style="max-width: 360px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari kode atau nama RT..." value="{{ $search }}">
                <button class="btn btn-outline-success" type="submit">Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode RT</th>
                        <th>Nama RT</th>
                        <th class="text-center">Jumlah Keluarga</th>
                        <th class="text-end" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarRt as $index => $rt)
                    <tr>
                        <td>{{ $daftarRt->firstItem() + $index }}</td>
                        <td><span class="fw-semibold">RT {{ $rt->kode_rt }}</span></td>
                        <td>{{ $rt->nama_rt }}</td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ $rt->keluarga_count }} KK</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalEditRt{{ $rt->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#modalHapusRt{{ $rt->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Modal Edit --}}
                    <div class="modal fade" id="modalEditRt{{ $rt->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0" style="border-radius: 16px;">
                                <form method="POST" action="{{ route('rw.master-rt.update', $rt->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit RT {{ $rt->kode_rt }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Kode RT</label>
                                            <input type="text" name="kode_rt" class="form-control" value="{{ $rt->kode_rt }}" required maxlength="10">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Nama RT</label>
                                            <input type="text" name="nama_rt" class="form-control" value="{{ $rt->nama_rt }}" required maxlength="50">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Hapus --}}
                    <div class="modal fade" id="modalHapusRt{{ $rt->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0" style="border-radius: 16px;">
                                <form method="POST" action="{{ route('rw.master-rt.destroy', $rt->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus RT</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-0">Yakin ingin menghapus <strong>RT {{ $rt->kode_rt }} - {{ $rt->nama_rt }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                                        @if($rt->keluarga_count > 0)
                                            <div class="alert alert-warning mt-3 mb-0 small">
                                                <i class="bi bi-info-circle me-1"></i> RT ini masih memiliki {{ $rt->keluarga_count }} data keluarga dan tidak dapat dihapus.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger rounded-pill px-4" {{ $rt->keluarga_count > 0 ? 'disabled' : '' }}>Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada data RT.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $daftarRt->links() }}
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahRt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px;">
            <form method="POST" action="{{ route('rw.master-rt.store') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-building-add text-success me-2"></i>Tambah RT Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode RT</label>
                        <input type="text" name="kode_rt" class="form-control" placeholder="mis. 07" value="{{ old('kode_rt') }}" required maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama RT</label>
                        <input type="text" name="nama_rt" class="form-control" placeholder="mis. 07" value="{{ old('nama_rt') }}" required maxlength="50">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Tambah RT</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Re-open the "Tambah RT" modal if validation failed on create.
    @if($errors->any() && old('kode_rt'))
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalTambahRt'));
        modal.show();
    });
    @endif
</script>
@endpush
