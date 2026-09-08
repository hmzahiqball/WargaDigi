@php
    $isPimpinan = $isPimpinan ?? in_array(Auth::user()->role ?? '', ['Pimpinan RW', 'Pimpinan']);
    $fotoUsahaUrl = $item->foto_usaha 
        ? asset('storage/' . $item->foto_usaha) 
        : 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop';
    
    $statusVerifikasi = $item->status_verifikasi ?? 'Pending';
    $statusBadgeClass = match($statusVerifikasi) {
        'Approved' => 'bg-success text-light',
        'Rejected' => 'bg-danger text-light',
        default => 'bg-light text-dark'
    };
    $statusText = match($statusVerifikasi) {
        'Approved' => 'Disetujui',
        'Rejected' => 'Ditolak',
        default => 'Pending Review'
    };
@endphp

<div class="modal fade" id="modalPengaju{{ $item->id }}" tabindex="-1" aria-labelledby="modalPengajuLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            {{-- Header Modal --}}
            <div class="modal-header bg-light border-bottom px-4 py-3 align-items-center">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalPengajuLabel{{ $item->id }}">
                        Detail Pengajuan UMKM
                    </h5>
                    <p class="text-muted small mb-0">Rincian data pengaju dan profil bisnis warga</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body Modal --}}
            <div class="modal-body p-4">
                <div class="row g-4">
                    {{-- Foto Usaha --}}
                    <div class="col-md-5">
                        <div class="rounded-3 overflow-hidden border shadow-sm mb-3" style="height: 200px;">
                            <img src="{{ $fotoUsahaUrl }}" class="w-100 h-100 object-fit-cover" alt="{{ $item->nama_usaha }}">
                        </div>
                        <div class="p-3 bg-light rounded-3 text-center">
                            <span class="text-muted small d-block mb-1">Status Pengajuan</span>
                            <span class="badge rounded-pill px-3 py-2 small fw-semibold border {{ $statusBadgeClass }}">
                                {{ $statusText }}
                            </span>
                            @if(!empty($item->catatan_verifikasi))
                                <div class="mt-2 text-start p-2 bg-white rounded border small text-muted">
                                    <strong>Catatan:</strong> {{ $item->catatan_verifikasi }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Data Pengaju Lengkap --}}
                    <div class="col-md-7">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-info-circle me-1"></i> Informasi Pemohon
                        </h6>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted small fw-semibold" style="width: 140px;">NIK</td>
                                        <td class="fw-bold text-dark small">{{ $item->nik ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-semibold">Nama Lengkap</td>
                                        <td class="fw-bold text-dark small">
                                            {{ $item->pemilik->penduduk->nama_lengkap ?? $item->pemilik->username ?? 'Warga' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-semibold">Nama Usaha</td>
                                        <td class="">{{ $item->nama_usaha }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-semibold">Kategori Usaha</td>
                                        <td>
                                            <span class="text-dark py-1 rounded small">
                                                {{ $item->kategori_umkm->nama_kategori ?? 'UMKM' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-semibold align-top">Deskripsi Usaha</td>
                                        <td class="text-muted small" style="line-height: 1.5;">
                                            {{ $item->deskripsi ?? 'Tidak ada deskripsi.' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-semibold align-top">Alamat & Lokasi</td>
                                        <td class="text-muted small">
                                            <div>{{ $item->alamat_usaha ?? '-' }}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-semibold">No. WhatsApp</td>
                                        <td class="small">
                                            @if(!empty($item->no_wa))
                                                @php
                                                    $waClean = preg_replace('/[^0-9]/', '', $item->no_wa);
                                                    if (str_starts_with($waClean, '0')) { $waClean = '62' . substr($waClean, 1); }
                                                @endphp
                                                <a href="https://wa.me/{{ $waClean }}" target="_blank" class="text-dark text-decoration-none fw-semibold">
                                                    +{{ $item->no_wa }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-semibold">Status Approval</td>
                                        <td>
                                            <span class="badge rounded-pill py-1 small fw-semibold {{ $statusBadgeClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

      
            <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                <div>
                    @if(!empty($item->created_at))
                        <span class="text-muted small">
                            Diajukan pada {{ $item->created_at->translatedFormat('d F Y, H:i') }}
                        </span>
                    @endif
                </div>
                
                <div class="d-flex gap-2">
                    
                    @if(!$isPimpinan && $statusVerifikasi === 'Pending')
                        <form action="{{ route('rw.umkm.reject', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-3 px-3 py-2 small fw-semibold">
                                Tolak
                            </button>
                        </form>
                        <form action="{{ route('rw.umkm.approve', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success rounded-3 px-3 py-2 small fw-semibold text-white" style="background-color: #5b9b76; border-color: #5b9b76;">
                                Setujui Profil
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
