@php
    $isPimpinan = $isPimpinan ?? in_array(Auth::user()->role ?? '', ['Pimpinan RW', 'Pimpinan']);
    $fotoUsahaUrl = $item->foto_usaha 
        ? asset('storage/' . $item->foto_usaha) 
        : 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop';
    
    $statusVerifikasi = $item->status_verifikasi ?? 'Pending';
    $statusBadgeClass = match($statusVerifikasi) {
        'Approved' => 'bg-success-subtle text-dark border-success-subtle',
        'Rejected' => 'bg-danger-subtle text-dark border-danger-subtle',
        default => ' text-dark border-warning-subtle'
    };
    $statusText = match($statusVerifikasi) {
        'Approved' => 'Disetujui',
        'Rejected' => 'Ditolak',
        default => 'Pending Review'
    };
@endphp

<div class="card border shadow-sm rounded-4 overflow-hidden p-3 p-md-4 bg-white hover-shadow transition-all" 
     style="cursor: pointer;"
     data-bs-toggle="modal" 
     data-bs-target="#modalPengaju{{ $item->id }}"
     title="Klik untuk melihat detail pengaju">
    <div class="row g-3 g-md-4 align-items-center">
        <div class="col-md-5 col-lg-4">
            <div class="position-relative rounded-3 overflow-hidden" style="height: 170px;">
                <img src="{{ $fotoUsahaUrl }}" class="img-fluid object-fit-cover w-100 h-100" alt="{{ $item->nama_usaha }}">
                <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white small">
                    <i class="bi bi-eye me-1"></i> Klik untuk Detail
                </span>
            </div>
        </div>
        
        <div class="col-md-7 col-lg-8 d-flex flex-column justify-content-between h-100">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <span class="badge text-dark border px-3 py-1 rounded-pill small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">
                        {{ $item->kategori_umkm->nama_kategori ?? 'UMKM' }}
                    </span>
                    <span class="badge rounded-pill px-3 py-1 small fw-semibold border {{ $statusBadgeClass }}">
                        <i class="bi bi-journal-text me-1"></i> {{ $statusText }}
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $item->nama_usaha }}</h4>
                <p class="text-muted small mb-2" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $item->deskripsi ?? 'Pendaftaran usaha baru dari warga RW.' }}
                </p>
            </div>

            <div>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 mt-3">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <span><i class="bi bi-person me-1"></i> {{ $item->pemilik->penduduk->nama_lengkap ?? $item->pemilik->username ?? 'Warga' }}</span>
                        <span class="opacity-50">|</span>
                        <span><i class="bi bi-geo-alt me-1"></i> RT {{ $item->pemilik->penduduk->keluarga->rt->nama_rt ?? '-' }}</span>
                        <span class="opacity-50">|</span>
                        <span><i class="bi bi-telephone me-1"></i> {{ $item->no_wa ?? '-' }}</span>
                    </div>
                </div>

                @if(!$isPimpinan && $statusVerifikasi === 'Pending')
                    <div class="d-flex justify-content-end gap-2 pt-1">
                        <form action="{{ route('rw.umkm.reject', $item->id) }}" method="POST" onclick="event.stopPropagation();">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-3 px-3 py-2 small fw-semibold text-dark">
                                Reject
                            </button>
                        </form>
                        <form action="{{ route('rw.umkm.approve', $item->id) }}" method="POST" onclick="event.stopPropagation();">
                            @csrf
                            <button type="submit" class="btn text-white rounded-3 px-4 py-2 small fw-semibold shadow-sm" style="background-color: #5b9b76; border-color: #5b9b76;">
                                <i class="bi bi-check-circle me-1"></i> Approve Profile
                            </button>
                        </form>
                    </div>
                @elseif($isPimpinan)
                    <div class="d-flex justify-content-end pt-1">
                        <span class="text-success small fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Lihat Rincian Pengaju</span>
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
