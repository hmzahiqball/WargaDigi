@extends('layouts.rt')

@section('title', 'Persetujuan Dokumen')

@section('content')
{{-- Header & Search --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold text-dark mb-1">Persetujuan Dokumen</h2>
        <p class="text-muted mb-0">Tinjau dan kelola permintaan dokumen dari warga.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="input-group" style="width: 250px;">
            <span class="input-group-text bg-white border-end-0 rounded-start-pill"><i class="bi bi-search text-muted"></i></span>
            <input type="text" class="form-control bg-white border-start-0 rounded-end-pill shadow-none" placeholder="Cari warga...">
        </div>
        <button class="btn btn-white bg-white border rounded-3 px-3 shadow-sm py-2"><i class="bi bi-filter fs-5"></i></button>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filter Boxes --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3 shadow-sm h-100 bg-white" style="border-radius: 12px;">
            <label class="small fw-bold text-dark mb-1" style="font-size: 13px;">Tipe Dokumen</label>
            <select class="form-select border-0 px-0 py-0 shadow-none text-muted bg-white" style="font-size: 14px; background-position: right 0 center;">
                <option selected>Semua Tipe</option>
                <option value="1">SKD</option>
                <option value="2">Surat Pengantar</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 shadow-sm h-100 bg-white" style="border-radius: 12px;">
            <label class="small fw-bold text-dark mb-1" style="font-size: 13px;">Status</label>
            <select class="form-select border-0 px-0 py-0 shadow-none text-muted bg-white" style="font-size: 14px; background-position: right 0 center;">
                <option selected>Diajukan</option>
                <option value="2">Disetujui</option>
                <option value="3">Ditolak</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 shadow-sm h-100 bg-white" style="border-radius: 12px;">
            <label class="small fw-bold text-dark mb-1" style="font-size: 13px;">Rentang Tanggal</label>
            <select class="form-select border-0 px-0 py-0 shadow-none text-muted bg-white" style="font-size: 14px; background-position: right 0 center;">
                <option selected>7 Hari Terakhir</option>
                <option value="2">Bulan Ini</option>
            </select>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card card-custom shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #f3f4f9;">
                <tr class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <th class="ps-4 py-3 border-0 rounded-start-3" style="font-weight: 700; font-size: 11px;">PEMOHON</th>
                    <th class="py-3 border-0 text-center" style="font-weight: 700; font-size: 11px;">TIPE DOKUMEN</th>
                    <th class="py-3 border-0 text-center" style="font-weight: 700; font-size: 11px;">TANGGAL SUBMIT</th>
                    <th class="py-3 border-0 text-center" style="font-weight: 700; font-size: 11px;">STATUS</th>
                    <th class="py-3 text-center border-0 rounded-end-3" style="font-weight: 700; font-size: 11px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $item)
                <tr class="bg-white">
                    <td class="ps-4 py-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                                <span class="text-primary fw-bold">{{ strtoupper(substr($item->nama_pemohon, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold small">{{ $item->nama_pemohon }}</h6>
                                <span class="text-muted text-xs">NIK: {{ $item->nik }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 text-center bg-white">
                        <span class="small fw-semibold text-dark">{{ $item->tipe_surat }}</span>
                    </td>
                    <td class="py-3 text-center bg-white">
                        <span class="text-muted small">{{ $item->tanggal_pengajuan }}</span>
                    </td>
                    <td class="py-3 text-center bg-white">
                        @php
                            $badgeClass = match($item->status) {
                                'Diajukan' => 'bg-warning bg-opacity-20 text-warning',
                                'Disetujui RT' => 'bg-success bg-opacity-10 text-success',
                                'Ditolak RT' => 'bg-danger bg-opacity-10 text-danger',
                                default => 'bg-secondary bg-opacity-10 text-secondary',
                            };
                            if ($item->status == 'Diajukan') {
                                $badgeClass = 'bg-warning bg-opacity-20 text-warning'; // Fallback if warning text class is different
                            }
                        @endphp
                        <span class="badge rounded-pill px-3 py-1 fw-semibold small" style="background-color: {{ $item->status == 'Diajukan' ? '#FFF3CD' : ($item->status == 'Disetujui RT' ? '#E8F5E9' : '#E2E3E5') }}; color: {{ $item->status == 'Diajukan' ? '#FFB300' : ($item->status == 'Disetujui RT' ? '#2E7D32' : '#6C757D') }};">{{ $item->status }}</span>
                    </td>
                    <td class="py-3 text-center bg-white">
                        @if($item->status == 'Diajukan')
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $item->id }}">
                                Lihat Detail
                            </button>
                        @elseif($item->status == 'Disetujui RT')
                            <span class="fst-italic text-dark small fw-semibold">Disetujui</span>
                        @else
                            <span class="fst-italic text-muted small fw-semibold">Belum Diajukan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="bg-white">
                    <td colspan="5" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <i class="bi bi-inbox fs-1 mb-2"></i>
                            <h6 class="fw-bold mb-1">Belum Ada Pengajuan</h6>
                            <p class="small mb-0">Tidak ada pengajuan surat yang perlu diproses saat ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if(count($pengajuan) > 0)
    <div class="d-flex justify-content-between align-items-center p-4 border-top">
        <span class="text-muted small">Menampilkan 1 sampai {{ count($pengajuan) }} dari {{ count($pengajuan) }} entri</span>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-success rounded-2 px-2 disabled"><i class="bi bi-chevron-left"></i></button>
            <button class="btn btn-sm btn-success rounded-2 px-3 fw-bold text-white">1</button>
            <button class="btn btn-sm btn-outline-success rounded-2 px-2 disabled"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
    @endif
</div>

{{-- ==================== MODALS ==================== --}}
@foreach($pengajuan as $item)

{{-- Modal 1: Detail Permintaan Dokumen --}}
<div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalDetailLabel{{ $item->id }}">Detail Permintaan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                {{-- Top Section: Pemohon + Data Lengkap --}}
                <div class="row g-4">
                    {{-- Pemohon Card --}}
                    <div class="col-md-5">
                        <div class="p-3 bg-white border rounded-3 h-100">
                            <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Pemohon</span>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                    <span class="text-primary fw-bold fs-5">{{ strtoupper(substr($item->nama_pemohon, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">{{ $item->nama_pemohon }}</h6>
                                    <span class="text-muted small">NIK: {{ $item->nik }}</span><br>
                                    <span class="text-muted small">{{ $item->alamat }}</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-muted small">Tipe Surat:</span>
                                <h6 class="fw-bold text-dark mt-1 mb-0">{{ $item->tipe_surat }}</h6>
                            </div>
                            <div class="mt-2 d-flex align-items-center gap-1 text-muted small">
                                <i class="bi bi-calendar3"></i>
                                <span>Diajukan: {{ $item->tanggal_pengajuan }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- Data Lengkap Pemohon --}}
                    <div class="col-md-7">
                        <div class="p-3 bg-white border border-success rounded-3 h-100 position-relative">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Data Lengkap Pemohon</span>
                                <span class="badge bg-warning bg-opacity-20 text-dark rounded-pill px-3 py-1 fw-semibold small">{{ $item->status }}</span>
                            </div>
                            <div class="row mt-3 g-2">
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Jenis Kelamin</span>
                                    <p class="fw-bold small mb-2">{{ $item->jenis_kelamin }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Tempat, Tgl Lahir</span>
                                    <p class="fw-bold small mb-2">{{ $item->tempat_tgl_lahir }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Agama</span>
                                    <p class="fw-bold small mb-2">{{ $item->agama }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Pendidikan Terakhir</span>
                                    <p class="fw-bold small mb-2">{{ $item->pendidikan_terakhir }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Jenis Pekerjaan</span>
                                    <p class="fw-bold small mb-2">{{ $item->pekerjaan }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Status Perkawinan</span>
                                    <p class="fw-bold small mb-2">{{ $item->status_perkawinan }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Kewarganegaraan</span>
                                    <p class="fw-bold small mb-2">{{ $item->kewarganegaraan }}</p>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted" style="font-size: 11px;">Nama Orang Tua</span>
                                    <p class="fw-bold small mb-2">{{ $item->nama_orang_tua }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lampiran Dokumen --}}
                <div class="mt-4" id="lampiranSection{{ $item->id }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;" id="lampiranLabel{{ $item->id }}">LAMPIRAN DOKUMEN (KK)</span>
                        <a href="#" class="text-success small fw-bold text-decoration-none" onclick="toggleLampiran({{ $item->id }})">
                            <span id="lampiranToggle{{ $item->id }}">LIHAT KTP →</span>
                        </a>
                    </div>
                    {{-- KK Viewer --}}
                    <div class="bg-white rounded-3 p-4 text-center border mb-4" id="lampiranKK{{ $item->id }}">
                        <i class="bi bi-file-earmark-text text-success fs-2 d-block mb-1"></i>
                        <span class="fw-bold small d-block">{{ $item->file_kk }}</span>
                        <span class="text-muted" style="font-size: 11px;">{{ $item->file_kk_size }}</span>
                        <div class="mt-2">
                            <a href="{{ $item->file_kk_url }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i>Lihat Dokumen
                            </a>
                        </div>
                    </div>
                    {{-- KTP Viewer (hidden by default) --}}
                    <div class="bg-white rounded-3 p-4 text-center border d-none mb-4" id="lampiranKTP{{ $item->id }}">
                        <i class="bi bi-file-earmark-text text-success fs-2 d-block mb-1"></i>
                        <span class="fw-bold small d-block">{{ $item->file_ktp }}</span>
                        <span class="text-muted" style="font-size: 11px;">{{ $item->file_ktp_size }}</span>
                        <div class="mt-2">
                            <a href="{{ $item->file_ktp_url }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i>Lihat Dokumen
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Catatan Persetujuan --}}
                <div class="mb-2">
                    <label class="form-label fw-bold small">Catatan Persetujuan (Opsional)</label>
                    <textarea class="form-control bg-white border" rows="3" placeholder="Tambahkan catatan jika diperlukan..." id="catatanPersetujuan{{ $item->id }}"></textarea>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#modalReject{{ $item->id }}"
                        data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Tolak & Beri Catatan
                </button>
                <button type="button" class="btn btn-outline-success rounded-pill px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#modalPreview{{ $item->id }}"
                        data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-1"></i>Setujui Dokumen
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 2: Tolak & Beri Catatan --}}
<div class="modal fade" id="modalReject{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Tolak Pengajuan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rt.surat.reject', $item->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-3 bg-white">
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 d-flex gap-3 align-items-start mb-4" style="border-radius: 12px;">
                        <i class="bi bi-info-circle-fill text-danger mt-1"></i>
                        <span class="small text-danger">Mohon berikan alasan penolakan atau catatan koreksi agar pemohon dapat melakukan perbaikan pada pengajuan ini.</span>
                    </div>
                    <label class="form-label fw-bold small">Alasan Penolakan / Catatan Koreksi <span class="text-danger">*</span></label>
                    <textarea class="form-control bg-white border" name="catatan_penolakan" rows="4" required placeholder="Contoh: Ada kesalahan input pada data NIK..."></textarea>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">Batal</button>
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                        Tolak & Kirim Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 3: Pratinjau Dokumen (SKD) --}}
<div class="modal fade" id="modalPreview{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Pratinjau Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                {{-- Surat Preview --}}
                <div class="bg-white border rounded-3 p-4 shadow-sm" style="font-family: 'Times New Roman', serif;">
                    <div class="text-center mb-3">
                        <h6 class="fw-bold mb-0" style="font-size: 14px;">PEMERINTAH KABUPATEN BANDUNG BARAT</h6>
                        <h6 class="fw-bold mb-0" style="font-size: 14px;">KECAMATAN NGAMPRAH</h6>
                        <h6 class="fw-bold mb-0" style="font-size: 14px;">DESA TANIMULYA</h6>
                        <p class="small mb-1">Jl. Pasirhalang Raya No. 1, Tanimulya, Kec. Ngamprah, Kab. Bandung Barat,</p>
                        <p class="small mb-2">Kode Pos 40552, Telp. (022) 1234567</p>
                        <hr style="border-top: 3px double #000;" class="my-2">
                    </div>
                    <div class="text-center mb-3">
                        <h6 class="fw-bold text-decoration-underline mb-1">SURAT KETERANGAN DOMISILI</h6>
                        <p class="small">Nomor: {{ $item->nomor_surat ?? '---/SKD/VIII/2024' }}</p>
                    </div>
                    <p class="small mb-2">Yang bertanda tangan dibawah ini :</p>
                    <div class="ms-4 small mb-3">
                        <table>
                            <tr><td style="width:140px">Nama</td><td>: {{ $item->nama_kepala_desa ?? 'Budi Santoso, S.Sos.' }}</td></tr>
                            <tr><td>Jabatan</td><td>: Kepala Desa Tanimulya</td></tr>
                            <tr><td>Alamat</td><td>: {{ $item->alamat_kepala_desa ?? 'RT 03 RW 10, Kp. Pasirhalang, Desa Tanimulya, Ngamprah.' }}</td></tr>
                        </table>
                    </div>
                    <p class="small mb-2">Menerangkan bahwa :</p>
                    <div class="ms-4 small mb-3">
                        <table>
                            <tr><td style="width:180px">Nama</td><td>: {{ $item->nama_pemohon_surat ?? $item->nama_pemohon }}</td></tr>
                            <tr><td>Tempat, Tanggal Lahir</td><td>: {{ $item->tempat_tgl_lahir_surat ?? $item->tempat_tgl_lahir }}</td></tr>
                            <tr><td>Jenis Kelamin</td><td>: {{ $item->jenis_kelamin_surat ?? $item->jenis_kelamin }}</td></tr>
                            <tr><td>Pekerjaan</td><td>: {{ $item->pekerjaan_surat ?? $item->pekerjaan }}</td></tr>
                            <tr><td>Agama</td><td>: {{ $item->agama_surat ?? $item->agama }}</td></tr>
                            <tr><td>Status Perkawinan</td><td>: {{ $item->status_perkawinan_surat ?? $item->status_perkawinan }}</td></tr>
                            <tr><td>Kewarganegaraan</td><td>: {{ $item->kewarganegaraan_surat ?? 'Indonesia' }}</td></tr>
                            <tr><td>Alamat</td><td>: {{ $item->alamat_surat ?? $item->alamat }}</td></tr>
                        </table>
                    </div>
                    <p class="small">Dengan ini menerangkan bahwa orang yang bersangkutan benar tingal berdomisili di Desa Tanimulya Kecamatan Ngamprah Kabupaten Bandung Barat.</p>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-3 d-flex justify-content-between align-items-center">
                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>Diajukan: {{ $item->tanggal_pengajuan }}</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#modalReject{{ $item->id }}"
                            data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Tolak & Beri Catatan
                    </button>
                    <button type="button" class="btn btn-outline-success rounded-pill px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#modalConfirm{{ $item->id }}"
                            data-bs-dismiss="modal">
                        <i class="bi bi-check-circle me-1"></i>Setujui Dokumen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal 4: Konfirmasi Sukses (Surat Siap Dikirim) --}}
<div class="modal fade" id="modalConfirm{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow text-center p-3 bg-white" style="border-radius: 20px;">
            <div class="modal-body p-4 bg-white">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-check-lg text-success fs-3"></i>
                </div>
                <h6 class="fw-bold mb-2">Pengajuan Disetujui & Terkirim</h6>
                <p class="text-muted mb-4" style="font-size: 13px;">Dokumen Telah berhasil disetujui dan berhasil dikirim ke staff RW.</p>
                <form action="{{ route('rt.surat.approve', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success rounded-pill px-4 w-100 fw-bold">Selesai</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal 5: Catatan Telah Dikirim (shown after reject via JS) --}}
<div class="modal fade" id="modalRejectSuccess{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow text-center p-3 bg-white" style="border-radius: 20px;">
            <div class="modal-body p-4 bg-white">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-check-lg text-success fs-3"></i>
                </div>
                <h6 class="fw-bold mb-2">Catatan Telah Dikirim</h6>
                <p class="text-muted mb-4" style="font-size: 13px;">Pengajuan yang telah ditolak berhasil dikirim kembali ke pengaju beserta catatan.</p>
                <a href="{{ route('rt.persetujuan-dokumen') }}" class="btn btn-success rounded-pill px-4 w-100 fw-bold">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</div>

@endforeach
@endsection

@push('scripts')
<script>
function toggleLampiran(id) {
    const kkEl = document.getElementById('lampiranKK' + id);
    const ktpEl = document.getElementById('lampiranKTP' + id);
    const labelEl = document.getElementById('lampiranLabel' + id);
    const toggleEl = document.getElementById('lampiranToggle' + id);

    if (kkEl.classList.contains('d-none')) {
        kkEl.classList.remove('d-none');
        ktpEl.classList.add('d-none');
        labelEl.textContent = 'LAMPIRAN DOKUMEN (KK)';
        toggleEl.textContent = 'LIHAT KTP →';
    } else {
        kkEl.classList.add('d-none');
        ktpEl.classList.remove('d-none');
        labelEl.textContent = 'LAMPIRAN DOKUMEN (KTP)';
        toggleEl.textContent = 'LIHAT KARTU KELUARGA →';
    }
}
</script>
@endpush
