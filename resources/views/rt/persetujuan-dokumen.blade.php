@extends('layouts.global')

@section('title', 'Persetujuan Dokumen')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">Portal Persetujuan Dokumen</h2>
        <p class="text-muted mb-0">Dokumen pengajuan warga yang menunggu persetujuan RT.</p>
    </div>
    <a href="{{ route('rt.riwayat-dokumen') }}" class="btn btn-outline-secondary" style="border-radius: 0.5rem;">
        <i class="bi bi-clock-history me-1"></i> Riwayat Pengajuan
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@forelse($pengajuan as $item)
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4 bg-white" style="border-radius: 16px;">
        @php
            $rtSc = match($item->status) {
                'Diajukan' => ['bg' => '#FFF3CD', 'color' => '#856404', 'label' => 'Menunggu Verifikasi RT', 'icon' => 'bi-hourglass-split'],
                'Disetujui RT' => ['bg' => '#D1ECF1', 'color' => '#0C5460', 'label' => 'Menunggu RW', 'icon' => 'bi-clock-history'],
                'Selesai' => ['bg' => '#D1E7DD', 'color' => '#0F5132', 'label' => 'Selesai / Disahkan RW', 'icon' => 'bi-check-circle-fill'],
                'Ditolak RT' => ['bg' => '#F8D7DA', 'color' => '#842029', 'label' => 'Ditolak RT', 'icon' => 'bi-x-circle-fill'],
                'Ditolak RW' => ['bg' => '#F8D7DA', 'color' => '#842029', 'label' => 'Ditolak RW', 'icon' => 'bi-x-circle-fill'],
                default => ['bg' => '#E2E3E5', 'color' => '#383D41', 'label' => $item->status, 'icon' => 'bi-info-circle'],
            };
        @endphp
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#198754,#20c997);"><i class="bi bi-person-fill text-white fs-5"></i></div>
                <div>
                    <h6 class="fw-bold mb-0">{{ $item->nama_pemohon }}</h6>
                    <span class="text-muted small">NIK: {{ $item->nik }} &bull; {{ $item->tanggal_pengajuan }}</span>
                </div>
            </div>
            @if($item->status === 'Selesai')
                <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold border-0 shadow-none d-inline-flex align-items-center gap-1"
                        style="background:{{ $rtSc['bg'] }};color:{{ $rtSc['color'] }};font-size:12px; cursor:pointer;"
                        data-bs-toggle="modal" data-bs-target="#modalPreviewSelesaiRt{{ $item->id }}"
                        title="Klik untuk melihat pratinjau surat lengkap">
                    <i class="bi {{ $rtSc['icon'] }}"></i>
                    <span>{{ $rtSc['label'] }}</span>
                </button>
            @elseif($item->status === 'Diajukan')
                <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold border-0 shadow-none d-inline-flex align-items-center gap-1"
                        style="background:{{ $rtSc['bg'] }};color:{{ $rtSc['color'] }};font-size:12px; cursor:pointer;"
                        data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}"
                        title="Klik untuk melihat detail permohonan">
                    <i class="bi {{ $rtSc['icon'] }}"></i>
                    <span>{{ $rtSc['label'] }}</span>
                </button>
            @else
                <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold border-0 shadow-none d-inline-flex align-items-center gap-1"
                        style="background:{{ $rtSc['bg'] }};color:{{ $rtSc['color'] }};font-size:12px; cursor:pointer;"
                        data-bs-toggle="modal" data-bs-target="#modalStatusRt{{ $item->id }}"
                        title="Klik untuk melihat catatan / status">
                    <i class="bi {{ $rtSc['icon'] }}"></i>
                    <span>{{ $rtSc['label'] }}</span>
                </button>
            @endif
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4"><span class="text-muted small">Jenis Surat</span><br><span class="fw-semibold small">{{ $item->tipe_surat }}</span></div>
            <div class="col-md-4"><span class="text-muted small">Alamat</span><br><span class="fw-semibold small">{{ $item->alamat }}</span></div>
            <div class="col-md-4"><span class="text-muted small">Pekerjaan</span><br><span class="fw-semibold small">{{ $item->pekerjaan }}</span></div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($item->status === 'Diajukan')
                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}"><i class="bi bi-eye me-1"></i>Detail Permintaan</button>
                <button class="btn btn-outline-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTtdRt{{ $item->id }}"><i class="bi bi-pen me-1"></i>Tanda Tangan & Setujui</button>
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalReject{{ $item->id }}"><i class="bi bi-x-circle me-1"></i>Tolak</button>
            @elseif($item->status === 'Ditolak RT')
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalStatusRt{{ $item->id }}"><i class="bi bi-info-circle me-1"></i>Lihat Alasan Penolakan RT</button>
            @elseif($item->status === 'Ditolak RW')
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalStatusRt{{ $item->id }}"><i class="bi bi-info-circle me-1"></i>Lihat Alasan Penolakan RW</button>
            @elseif($item->status === 'Disetujui RT')
                <button class="btn btn-outline-info btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalStatusRt{{ $item->id }}"><i class="bi bi-clock-history me-1"></i>Menunggu Peninjauan RW</button>
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalPreviewSelesaiRt{{ $item->id }}"><i class="bi bi-eye me-1"></i>Pratinjau Surat Disetujui</button>
            @elseif($item->status === 'Selesai')
                <button class="btn btn-outline-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalPreviewSelesaiRt{{ $item->id }}"><i class="bi bi-file-earmark-check me-1"></i>Lihat Surat Selesai (TTD Lengkap)</button>
            @endif
        </div>
    </div>
</div>

{{-- Modal Status & Alasan Penolakan untuk RT --}}
<div class="modal fade" id="modalStatusRt{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">
                    @if(in_array($item->status, ['Ditolak RT', 'Ditolak RW']))
                        <i class="bi bi-x-circle-fill text-danger me-2"></i>Alasan Penolakan Dokumen
                    @elseif($item->status === 'Disetujui RT')
                        <i class="bi bi-clock-history text-info me-2"></i>Status Peninjauan Dokumen
                    @else
                        <i class="bi bi-info-circle-fill text-primary me-2"></i>Status Dokumen
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-3">
                <div class="mb-3 p-3 rounded-3" style="background:#f8f9fa;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">{{ $item->nama_pemohon }}</span>
                        <span class="badge rounded-pill px-3 py-1" style="background:{{ $rtSc['bg'] }};color:{{ $rtSc['color'] }};font-size:11px;">{{ $rtSc['label'] }}</span>
                    </div>
                    <div class="text-muted small">
                        <div><strong>NIK:</strong> {{ $item->nik }} &bull; {{ $item->tipe_surat }}</div>
                        <div><i class="bi bi-calendar3 me-1"></i>Diajukan: {{ $item->tanggal_pengajuan }}</div>
                    </div>
                </div>

                @if($item->status === 'Ditolak RT')
                    <div class="alert alert-danger border-0 d-flex align-items-start gap-2 mb-3" style="border-radius: 12px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 mt-n1"></i>
                        <div>
                            <strong>Surat Ditolak oleh RT</strong>
                            <div class="small mt-1">Berikut adalah catatan alasan penolakan yang telah dikirimkan kepada pemohon:</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Alasan Penolakan RT:</label>
                        <div class="p-3 rounded-3 border small" style="min-height: 70px; border-color: #f1aeb5 !important; background-color: #fff8f8 !important;">
                            @if(!empty($item->catatan_rt))
                                <p class="mb-0 fw-semibold text-danger">{{ $item->catatan_rt }}</p>
                            @else
                                <p class="mb-0 text-muted fst-italic">Tidak ada catatan alasan penolakan yang tercatat.</p>
                            @endif
                        </div>
                    </div>

                @elseif($item->status === 'Ditolak RW')
                    <div class="alert alert-danger border-0 d-flex align-items-start gap-2 mb-3" style="border-radius: 12px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 mt-n1"></i>
                        <div>
                            <strong>Surat Ditolak oleh RW</strong>
                            <div class="small mt-1">Berikut adalah catatan penolakan yang diberikan oleh Ketua RW:</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Alasan Penolakan RW:</label>
                        <div class="p-3 rounded-3 border small" style="min-height: 70px; border-color: #f1aeb5 !important; background-color: #fff8f8 !important;">
                            @if(!empty($item->catatan_rw))
                                <p class="mb-0 fw-semibold text-danger">{{ $item->catatan_rw }}</p>
                            @else
                                <p class="mb-0 text-muted fst-italic">Tidak ada catatan penolakan dari RW.</p>
                            @endif
                        </div>
                    </div>

                @elseif($item->status === 'Disetujui RT')
                    <div class="alert alert-info border-0 d-flex align-items-start gap-2 mb-3" style="border-radius: 12px;">
                        <i class="bi bi-clock-history fs-5 mt-n1 text-info"></i>
                        <div>
                            <strong>Surat Telah Disetujui RT & Sedang Ditinjau RW</strong>
                            <div class="small mt-1">Surat permohonan ini telah Anda setujui dan tandatangani. Dokumen saat ini sedang dalam proses peninjauan dan pengesahan tanda tangan akhir dari Ketua RW.</div>
                        </div>
                    </div>
                    @if(!empty($item->catatan_rt))
                        <div class="mb-3 p-3 rounded-3 bg-light border small">
                            <span class="text-muted fw-bold">Catatan Persetujuan RT:</span>
                            <p class="mb-0 text-dark">{{ $item->catatan_rt }}</p>
                        </div>
                    @endif
                    <div class="p-3 rounded-3 bg-light border small text-muted">
                        <i class="bi bi-info-circle me-1 text-primary"></i>Setelah disahkan oleh Ketua RW, status dokumen akan otomatis menjadi <strong>Selesai / Disahkan RW</strong> dan surat resmi dapat dipratinjau secara lengkap.
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview Surat Selesai untuk RT --}}
<div class="modal fade" id="modalPreviewSelesaiRt{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-check-fill text-success me-2"></i>Pratinjau Surat Resmi</h5>
                    <p class="text-muted small mb-0">Surat pengantar pemohon: <strong>{{ $item->nama_pemohon }}</strong> ({{ $item->tipe_surat }})</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <div class="d-flex justify-content-center">
                    <div class="bg-white border rounded-3 shadow-sm" style="width: 595px; max-width: 100%; overflow: hidden;">
                        <iframe src="{{ route('rt.surat.preview', $item->id) }}" style="width: 595px; height: 842px; border: none; display: block;" title="Preview Surat"></iframe>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 justify-content-center">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Detail Permintaan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <div class="border rounded-3 p-3 h-100">
                            <span class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px;">Pemohon</span>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#198754,#20c997);"><i class="bi bi-person-fill text-white fs-5"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="font-size:14px;">{{ $item->nama_pemohon }}</h6>
                                    <span class="text-muted" style="font-size:12px;">NIK: {{ $item->nik }}</span><br>
                                    <span class="text-muted" style="font-size:12px;">RT {{ $item->kode_rt ?? '01' }} / RW 21</span>
                                </div>
                            </div>
                            <div class="mt-3"><span class="text-muted" style="font-size:12px;">Tipe Surat:</span><br><span class="fw-bold" style="font-size:13px;">{{ $item->tipe_surat }}</span></div>
                            <div class="mt-2"><span class="text-muted" style="font-size:12px;"><i class="bi bi-calendar3 me-1"></i>Diajukan: {{ $item->tanggal_pengajuan }}</span></div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="border rounded-3 p-3 h-100" style="border-color:#198754 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px;">Data Lengkap Pemohon</span>
                                <span class="badge rounded-pill px-3 py-1" style="background:#FFF3CD;color:#856404;">{{ $item->status }}</span>
                            </div>
                            <div class="row g-2" style="font-size:13px;">
                                <div class="col-6"><span class="text-muted">Jenis Kelamin</span><br><span class="fw-bold">{{ $item->jenis_kelamin }}</span></div>
                                <div class="col-6"><span class="text-muted">Tempat, Tgl Lahir</span><br><span class="fw-bold">{{ $item->tempat_tgl_lahir }}</span></div>
                                <div class="col-6"><span class="text-muted">Agama</span><br><span class="fw-bold">{{ $item->agama }}</span></div>
                                <div class="col-6"><span class="text-muted">Pendidikan Terakhir</span><br><span class="fw-bold">{{ $item->pendidikan ?? '-' }}</span></div>
                                <div class="col-6"><span class="text-muted">Jenis Pekerjaan</span><br><span class="fw-bold">{{ $item->pekerjaan }}</span></div>
                                <div class="col-6"><span class="text-muted">Status Perkawinan</span><br><span class="fw-bold">{{ $item->status_perkawinan }}</span></div>
                                <div class="col-6"><span class="text-muted">Kewarganegaraan</span><br><span class="fw-bold">{{ $item->kewarganegaraan ?? 'WNI' }}</span></div>
                                <div class="col-6"><span class="text-muted">Alamat</span><br><span class="fw-bold">{{ $item->alamat }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="letter-spacing:1px;" id="lampiranLabel{{ $item->id }}">Lampiran Dokumen (KTP)</span>
                        <a href="#" class="text-success fw-bold small text-decoration-none" id="lampiranToggleBtn{{ $item->id }}" onclick="event.preventDefault(); toggleLampiran('{{ $item->id }}');">Lihat Kartu Keluarga &rarr;</a>
                    </div>
                    <div id="lampiranKtpBox{{ $item->id }}" class="border rounded-3 p-3 text-center" style="background:rgba(13,110,253,0.02); min-height:180px;">
                        @if($item->file_ktp_url)
                            <img src="{{ $item->file_ktp_url }}" alt="KTP" class="img-fluid rounded shadow-sm" style="max-height:160px; cursor:pointer;" onclick="showRtImageModal('{{ $item->file_ktp_url }}', 'Preview KTP')">
                            <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="showRtImageModal('{{ $item->file_ktp_url }}', 'Preview KTP')"><i class="bi bi-eye me-1"></i>Lihat Dokumen</button></div>
                        @else
                            <div class="py-4"><i class="bi bi-file-earmark-x text-muted fs-1 d-block mb-2"></i><span class="text-muted small">Belum diunggah</span></div>
                        @endif
                    </div>
                    <div id="lampiranKkBox{{ $item->id }}" class="border rounded-3 p-3 text-center d-none" style="background:rgba(13,110,253,0.02); min-height:180px;">
                        @if($item->file_kk_url)
                            <img src="{{ $item->file_kk_url }}" alt="KK" class="img-fluid rounded shadow-sm d-block mx-auto" style="max-height:160px; cursor:pointer;" onclick="showRtImageModal('{{ $item->file_kk_url }}', 'Preview KK')">
                            <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="showRtImageModal('{{ $item->file_kk_url }}', 'Preview KK')"><i class="bi bi-eye me-1"></i>Lihat Dokumen</button></div>
                        @else
                            <div class="py-4"><i class="bi bi-file-earmark-x text-muted fs-1 d-block mb-2"></i><span class="text-muted small">Belum diunggah</span></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalReject{{ $item->id }}"><i class="bi bi-x-circle me-1"></i>Tolak & Beri Catatan</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalTtdRt{{ $item->id }}"><i class="bi bi-check-circle me-1"></i>Setujui Dokumen</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal TTD RT with Live Preview --}}
<div class="modal fade" id="modalTtdRt{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pen-fill text-success me-2"></i>Tanda Tangan & Setujui</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <p class="text-muted small mb-3">Bubuhkan tanda tangan sebagai Ketua RT untuk menyetujui surat <strong>{{ $item->nama_pemohon }}</strong>.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small" for="nomorUrutRT{{ $item->id }}">Nomor Urut Surat</label>
                            <input type="text" id="nomorUrutRT{{ $item->id }}" class="form-control" maxlength="30" value="" placeholder="Contoh: 012" oninput="updateNomorSurat('RT', '{{ $item->id }}', this.value)">
                            <div class="form-text">Hanya bagian urut. Sisanya (SK/RT/RW/bulan/tahun) disusun otomatis.</div>
                        </div>
                        @php $akunRt = auth()->user(); @endphp
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tanda Tangan RT <span class="text-danger">*</span></label>

                            @if($akunRt && $akunRt->ttd_default)
                                <div class="btn-group btn-group-sm w-100 mb-2" role="group">
                                    <input type="radio" class="btn-check" name="modeTtdRt{{ $item->id }}" id="modeTtdDefaultRt{{ $item->id }}" checked onchange="setModeTtd('Rt','{{ $item->id }}','default')">
                                    <label class="btn btn-outline-success" for="modeTtdDefaultRt{{ $item->id }}"><i class="bi bi-bookmark-check me-1"></i>TTD Tersimpan</label>
                                    <input type="radio" class="btn-check" name="modeTtdRt{{ $item->id }}" id="modeTtdManualRt{{ $item->id }}" onchange="setModeTtd('Rt','{{ $item->id }}','manual')">
                                    <label class="btn btn-outline-success" for="modeTtdManualRt{{ $item->id }}"><i class="bi bi-pencil me-1"></i>Gambar Manual</label>
                                </div>
                                <div id="ttdDefaultBoxRt{{ $item->id }}" class="border rounded-3 p-2 text-center mb-2" style="background:#fafafa;">
                                    <img src="{{ route('pengaturan.ttd.file', ['jenis' => 'ttd']) }}" alt="TTD Default" style="max-height:120px;" class="img-fluid">
                                </div>
                            @endif

                            <div id="ttdManualBoxRt{{ $item->id }}" class="{{ ($akunRt && $akunRt->ttd_default) ? 'd-none' : '' }}">
                                <div class="border rounded-3 position-relative" style="height: 180px; background: #fafafa; touch-action: none;">
                                    <canvas id="sigCanvasRt{{ $item->id }}" style="width:100%;height:100%;display:block;cursor:crosshair;"></canvas>
                                    <div id="sigPlaceholderRt{{ $item->id }}" class="position-absolute top-50 start-50 translate-middle text-muted small text-center" style="pointer-events:none;">
                                        <i class="bi bi-pencil-square fs-3 d-block mb-1"></i>Coretan tanda tangan di sini
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="clearSig('Rt', '{{ $item->id }}')"><i class="bi bi-arrow-counterclockwise me-1"></i>Ulangi</button>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="simpanTtdRt{{ $item->id }}">
                                        <label class="form-check-label small text-muted" for="simpanTtdRt{{ $item->id }}">Simpan jadi default</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Stempel RT (Opsional)</label>
                            @if($akunRt && $akunRt->stempel_default)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="pakaiStempelRt{{ $item->id }}" checked onchange="toggleStempelDefault('Rt','{{ $item->id }}')">
                                    <label class="form-check-label small" for="pakaiStempelRt{{ $item->id }}">Gunakan stempel tersimpan</label>
                                </div>
                                <div id="stempelDefaultBoxRt{{ $item->id }}" class="border rounded-3 p-2 text-center mb-2" style="background:#fafafa;">
                                    <img src="{{ route('pengaturan.ttd.file', ['jenis' => 'stempel']) }}" alt="Stempel Default" style="max-height:70px;" class="img-fluid">
                                </div>
                            @endif
                            <div id="stempelUploadBoxRt{{ $item->id }}" class="{{ ($akunRt && $akunRt->stempel_default) ? 'd-none' : '' }}">
                                <div class="border border-2 rounded-3 p-3 text-center" style="border-style: dashed !important; cursor: pointer;" onclick="document.getElementById('stempelRt{{ $item->id }}').click()">
                                    <input type="file" id="stempelRt{{ $item->id }}" class="d-none" accept=".png" onchange="previewStempel(this, 'stempelPreviewRt{{ $item->id }}')">
                                    <div id="stempelPlaceholderRt{{ $item->id }}"><i class="bi bi-file-earmark-image text-success fs-2 d-block mb-1"></i><span class="small">Unggah Stempel (PNG, Max 2MB)</span></div>
                                    <div id="stempelPreviewRt{{ $item->id }}" class="d-none"><img src="" class="img-fluid" style="max-height:70px;" alt="Stempel"><p class="text-success small mt-1 mb-0"><i class="bi bi-check-circle me-1"></i>Siap</p><button type="button" class="btn btn-sm btn-outline-danger rounded-pill mt-1" onclick="clearStempel('Rt', '{{ $item->id }}')"><i class="bi bi-trash me-1"></i>Hapus Stempel</button></div>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="simpanStempelRt{{ $item->id }}">
                                    <label class="form-check-label small text-muted" for="simpanStempelRt{{ $item->id }}">Simpan jadi default</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <label class="form-label fw-bold small"><i class="bi bi-file-earmark-text me-1"></i>Pratinjau Surat</label>
                        <div class="bg-white border rounded-3 shadow-sm" style="overflow:hidden;">
                            <iframe id="previewFrameRt{{ $item->id }}" src="{{ route('rt.surat.preview', $item->id) }}" style="width:100%;height:500px;border:none;" title="Preview Surat"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="submitApprove('Rt', '{{ $item->id }}')"><i class="bi bi-check-circle me-1"></i>Setujui & Teruskan ke RW</button>
            </div>
            <form id="formApproveRt{{ $item->id }}" action="{{ route('rt.surat.approve', $item->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
                <input type="hidden" name="signature_rt" id="hiddenSigRt{{ $item->id }}">
            </form>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="modalReject{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">Tolak Pengajuan Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rt.surat.reject', $item->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-3">
                    <div class="rounded-3 p-3 mb-3" style="background:#FFF3CD; border: 1px solid #FFE69C;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-warning mt-1"></i>
                            <span class="small" style="color:#664D03;">Mohon berikan alasan penolakan atau catatan koreksi agar pemohon dapat melakukan perbaikan pada pengajuan ini.</span>
                        </div>
                    </div>
                    <label class="form-label fw-bold small">Alasan Penolakan / Catatan Koreksi <span class="text-danger">*</span></label>
                    <textarea name="catatan_penolakan" class="form-control" rows="4" placeholder="Contoh: Data KTP tidak sesuai dengan yang terdaftar..." required style="border-radius: 12px;"></textarea>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold"><i class="bi bi-x-circle me-1"></i>Tolak & Kirim Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@empty
<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-body p-5 text-center bg-white" style="border-radius: 16px;">
        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
        <h5 class="fw-bold">Belum Ada Pengajuan</h5>
        <p class="text-muted">Belum ada dokumen yang perlu diverifikasi saat ini.</p>
    </div>
</div>
@endforelse

<div class="modal fade" id="rtImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold" id="rtImageTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4 text-center">
                <img src="" id="rtImageSrc" class="img-fluid rounded shadow" style="max-height: 70vh;" alt="Preview">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRtImageModal(src, title) {
    document.getElementById('rtImageSrc').src = src;
    document.getElementById('rtImageTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('rtImageModal')).show();
}

function toggleLampiran(id) {
    var ktpBox = document.getElementById('lampiranKtpBox' + id);
    var kkBox = document.getElementById('lampiranKkBox' + id);
    var label = document.getElementById('lampiranLabel' + id);
    var btn = document.getElementById('lampiranToggleBtn' + id);
    if (kkBox.classList.contains('d-none')) {
        kkBox.classList.remove('d-none');
        ktpBox.classList.add('d-none');
        label.textContent = 'Lampiran Dokumen (KK)';
        btn.innerHTML = 'Lihat KTP &rarr;';
    } else {
        ktpBox.classList.remove('d-none');
        kkBox.classList.add('d-none');
        label.textContent = 'Lampiran Dokumen (KTP)';
        btn.innerHTML = 'Lihat Kartu Keluarga &rarr;';
    }
}

function injectImgToIframe(iframe, selector, src, className) {
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({
            type: 'update-preview',
            selector: selector,
            src: src,
            className: className
        }, '*');
    }
}

function removeImgFromIframe(iframe, selector, className) {
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({
            type: 'clear-preview',
            selector: selector,
            className: className
        }, '*');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="modalTtdRt"]').forEach(modal => {
        const id = modal.id.replace('modalTtdRt', '');
        const canvas = document.getElementById('sigCanvasRt' + id);
        if (!canvas) return;
        const placeholder = document.getElementById('sigPlaceholderRt' + id);
        const ctx = canvas.getContext('2d');
        let drawing = false, lx = 0, ly = 0;
        modal.addEventListener('shown.bs.modal', function() {
            if (window.resizeSigCanvas) resizeSigCanvas('Rt', id);
            // Inject TTD/stempel default ke pratinjau bila mode default aktif.
            const iframe = document.getElementById('previewFrameRt' + id);
            const terapkan = function() { if (window.syncPreviewTtdStempel) syncPreviewTtdStempel('Rt', id); };
            if (iframe) { iframe.addEventListener('load', terapkan, { once: true }); setTimeout(terapkan, 600); }
        });
        function gp(e) { const r = canvas.getBoundingClientRect(); return { x: e.clientX - r.left, y: e.clientY - r.top }; }
        canvas.addEventListener('pointerdown', function(e) { drawing = true; const p = gp(e); lx = p.x; ly = p.y; canvas.setPointerCapture(e.pointerId); e.preventDefault(); });
        canvas.addEventListener('pointermove', function(e) {
            if (!drawing) return; e.preventDefault();
            if (placeholder) placeholder.classList.add('d-none');
            const p = gp(e);
            ctx.beginPath(); ctx.moveTo(lx, ly); ctx.lineTo(p.x, p.y);
            ctx.strokeStyle = '#000'; ctx.lineWidth = 3; ctx.lineCap = 'round'; ctx.stroke();
            lx = p.x; ly = p.y;
            // Real-time update to iframe preview (RT column)
            const iframe = document.getElementById('previewFrameRt' + id);
            if (iframe) injectImgToIframe(iframe, '.rt-col .ttd-box', canvas.toDataURL('image/png'), 'ttd-img');
        });
        canvas.addEventListener('pointerup', function(e) {
            drawing = false; canvas.releasePointerCapture(e.pointerId);
            const iframe = document.getElementById('previewFrameRt' + id);
            if (iframe) injectImgToIframe(iframe, '.rt-col .ttd-box', canvas.toDataURL('image/png'), 'ttd-img');
        });
    });
});

window.clearSig = function(role, id) {
    const canvas = document.getElementById('sigCanvas' + role + id);
    const ph = document.getElementById('sigPlaceholder' + role + id);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if (ph) ph.classList.remove('d-none');
    // Remove from iframe preview
    const colSelector = role === 'Rt' ? '.rt-col .ttd-box' : '.rw-col .ttd-box';
    const iframe = document.getElementById('previewFrame' + role + id);
    if (iframe) removeImgFromIframe(iframe, colSelector, 'ttd-img');
};

window.previewStempel = function(input, previewId) {
    if (input.files && input.files[0]) {
        const f = input.files[0];
        if (f.type !== 'image/png') { window.tampilkanPemberitahuan('Format harus PNG!'); input.value = ''; return; }
        if (f.size > 2*1024*1024) { window.tampilkanPemberitahuan('Max 2MB!'); input.value = ''; return; }
        const reader = new FileReader();
        reader.onload = function(e) {
            const el = document.getElementById(previewId);
            el.querySelector('img').src = e.target.result;
            el.classList.remove('d-none');
            el.previousElementSibling.classList.add('d-none');
            // Inject stempel into iframe preview
            const role = previewId.includes('Rt') ? 'Rt' : 'Rw';
            const id = previewId.replace('stempelPreview' + role, '');
            const colSelector = role === 'Rt' ? '.rt-col .ttd-box' : '.rw-col .ttd-box';
            const iframe = document.getElementById('previewFrame' + role + id);
            if (iframe) injectImgToIframe(iframe, colSelector, e.target.result, 'stempel-img');
        };
        reader.readAsDataURL(f);
    }
};

window.clearStempel = function(role, id) {
    const fileInput = document.getElementById('stempel' + role + id);
    if (fileInput) fileInput.value = '';
    const previewEl = document.getElementById('stempelPreview' + role + id);
    const placeholderEl = document.getElementById('stempelPlaceholder' + role + id);
    if (previewEl) { previewEl.classList.add('d-none'); previewEl.querySelector('img').src = ''; }
    if (placeholderEl) placeholderEl.classList.remove('d-none');
    // Remove from iframe preview
    const colSelector = role === 'Rt' ? '.rt-col .ttd-box' : '.rw-col .ttd-box';
    const iframe = document.getElementById('previewFrame' + role + id);
    if (iframe) removeImgFromIframe(iframe, colSelector, 'stempel-img');
};

// Tambah hidden input ke form approve.
function tambahHidden(form, name, value) {
    const h = document.createElement('input'); h.type = 'hidden'; h.name = name; h.value = value; form.appendChild(h);
}

// Pindah mode TTD (default vs manual) pada modal.
// URL file TTD/stempel default akun (bila ada). Dipakai untuk inject ke pratinjau.
const TTD_DEFAULT_URL = @json(auth()->user()?->ttd_default ? route('pengaturan.ttd.file', ['jenis' => 'ttd']) : null);
const STEMPEL_DEFAULT_URL = @json(auth()->user()?->stempel_default ? route('pengaturan.ttd.file', ['jenis' => 'stempel']) : null);

// Sinkronkan pratinjau surat (iframe) dengan mode TTD & stempel terpilih.
window.syncPreviewTtdStempel = function(role, id) {
    const iframe = document.getElementById('previewFrame' + role + id);
    if (!iframe) return;
    const col = role === 'Rt' ? '.rt-col .ttd-box' : '.rw-col .ttd-box';

    // TTD
    const modeDefault = document.getElementById('modeTtdDefault' + role + id);
    if (modeDefault && modeDefault.checked && TTD_DEFAULT_URL) {
        injectImgToIframe(iframe, col, TTD_DEFAULT_URL, 'ttd-img');
    } else {
        const canvas = document.getElementById('sigCanvas' + role + id);
        const adaCoretan = canvas && canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data.some(v => v !== 0);
        if (adaCoretan) injectImgToIframe(iframe, col, canvas.toDataURL('image/png'), 'ttd-img');
        else removeImgFromIframe(iframe, col, 'ttd-img');
    }

    // Stempel
    const pakaiStempel = document.getElementById('pakaiStempel' + role + id);
    if (pakaiStempel && pakaiStempel.checked && STEMPEL_DEFAULT_URL) {
        injectImgToIframe(iframe, col, STEMPEL_DEFAULT_URL, 'stempel-img');
    }
};

// Ukur ulang canvas agar sesuai lebar kontainer (penting setelah box tampil dari d-none).
window.resizeSigCanvas = function(role, id) {
    const canvas = document.getElementById('sigCanvas' + role + id);
    if (!canvas) return;
    const r = canvas.parentElement.getBoundingClientRect();
    if (r.width > 0 && r.height > 0) {
        canvas.width = r.width; canvas.height = r.height;
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    }
};

window.setModeTtd = function(role, id, mode) {
    const manual = document.getElementById('ttdManualBox' + role + id);
    const def = document.getElementById('ttdDefaultBox' + role + id);
    if (mode === 'manual') {
        if (manual) manual.classList.remove('d-none'); if (def) def.classList.add('d-none');
        // Box baru saja tampil: ukur canvas setelah layout siap.
        setTimeout(function() { resizeSigCanvas(role, id); }, 50);
    } else {
        if (manual) manual.classList.add('d-none'); if (def) def.classList.remove('d-none');
    }
    syncPreviewTtdStempel(role, id);
};

// Tampil/sembunyikan kotak unggah stempel saat memakai stempel default.
window.toggleStempelDefault = function(role, id) {
    const pakai = document.getElementById('pakaiStempel' + role + id);
    const upload = document.getElementById('stempelUploadBox' + role + id);
    const def = document.getElementById('stempelDefaultBox' + role + id);
    const iframe = document.getElementById('previewFrame' + role + id);
    const col = role === 'Rt' ? '.rt-col .ttd-box' : '.rw-col .ttd-box';
    if (pakai && pakai.checked) {
        if (upload) upload.classList.add('d-none'); if (def) def.classList.remove('d-none');
        if (iframe && STEMPEL_DEFAULT_URL) injectImgToIframe(iframe, col, STEMPEL_DEFAULT_URL, 'stempel-img');
    } else {
        if (upload) upload.classList.remove('d-none'); if (def) def.classList.add('d-none');
        if (iframe) removeImgFromIframe(iframe, col, 'stempel-img');
    }
};

window.submitApprove = function(role, id) {
    const lc = role.toLowerCase();
    const form = document.getElementById('formApprove' + role + id);

    // --- Mode TTD ---
    const modeDefault = document.getElementById('modeTtdDefault' + role + id);
    const pakaiTtdDefault = modeDefault && modeDefault.checked;
    if (pakaiTtdDefault) {
        tambahHidden(form, 'pakai_ttd_default_' + lc, '1');
    } else {
        const canvas = document.getElementById('sigCanvas' + role + id);
        if (canvas) document.getElementById('hiddenSig' + role + id).value = canvas.toDataURL('image/png');
        const simpan = document.getElementById('simpanTtd' + role + id);
        if (simpan && simpan.checked) tambahHidden(form, 'simpan_ttd_default_' + lc, '1');
    }

    // --- Mode stempel ---
    const pakaiStempel = document.getElementById('pakaiStempel' + role + id);
    if (pakaiStempel && pakaiStempel.checked) {
        tambahHidden(form, 'pakai_stempel_default_' + lc, '1');
    } else {
        const fileInput = document.getElementById('stempel' + role + id);
        if (fileInput && fileInput.files.length > 0) { fileInput.name = 'stempel_' + lc; form.appendChild(fileInput); }
        const simpanS = document.getElementById('simpanStempel' + role + id);
        if (simpanS && simpanS.checked) tambahHidden(form, 'simpan_stempel_default_' + lc, '1');
    }

    const nomor = document.getElementById('nomorUrut' + role + id);
    if (nomor && nomor.value.trim() !== '') tambahHidden(form, 'nomor_urut', nomor.value.trim());

    form.submit();
};
window.updateNomorSurat = function(role, id, value) {
    const iframe = document.getElementById('previewFrame' + role + id);
    if (iframe && iframe.contentWindow) iframe.contentWindow.postMessage({ type: 'update-nomor', value: value }, '*');
};
</script>
@endpush
