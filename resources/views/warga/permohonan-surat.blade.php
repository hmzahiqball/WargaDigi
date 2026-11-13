@extends('layouts.global')

@section('title', 'Layanan Permohonan Surat')

@section('content')
@php
    // Pisahkan pengajuan untuk tampilan: sedang diproses, selesai, ditolak.
    $suratProses = $pengajuan->whereIn('status', ['Diajukan', 'Disetujui RT'])->values();
    $suratSelesai = $pengajuan->where('status', 'Selesai')->values();
    $suratDitolak = $pengajuan->whereIn('status', ['Ditolak RT', 'Ditolak RW'])->values();
@endphp

{{-- Header --}}
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #198754;">Layanan Permohonan Surat</h2>
        <p class="text-muted mb-0">Ajukan permohonan surat pengantar RT/RW secara mandiri.</p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-3 fw-bold flex-shrink-0 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRiwayatSurat">
        <i class="bi bi-clock-history me-1"></i>Riwayat Pengajuan Surat
        @if($suratSelesai->count() + $suratDitolak->count() > 0)
            <span class="badge rounded-pill bg-white text-success ms-1">{{ $suratSelesai->count() + $suratDitolak->count() }}</span>
        @endif
    </button>
</div>

{{-- Flash Messages --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <ul class="mb-0 list-unstyled">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- Kolom Utama (Kiri) --}}
    <div class="col-lg-8">
        {{-- Modal detail status per item (dipakai oleh card "Sedang Diproses" & modal Riwayat) --}}
        @foreach($pengajuan as $item)
                @php
                    $sc = match($item->status) {
                        'Diajukan' => ['bg' => '#FFF3CD', 'color' => '#856404', 'label' => 'Menunggu RT', 'icon' => 'bi-hourglass-split'],
                        'Disetujui RT' => ['bg' => '#D1ECF1', 'color' => '#0C5460', 'label' => 'Menunggu RW', 'icon' => 'bi-hourglass-split'],
                        'Selesai' => ['bg' => '#D4EDDA', 'color' => '#155724', 'label' => 'Selesai', 'icon' => 'bi-check-circle-fill'],
                        'Ditolak RT' => ['bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak RT', 'icon' => 'bi-x-circle-fill'],
                        'Ditolak RW' => ['bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak RW', 'icon' => 'bi-x-circle-fill'],
                        default => ['bg' => '#E2E3E5', 'color' => '#383D41', 'label' => $item->status, 'icon' => 'bi-info-circle'],
                    };
                @endphp
                {{-- Modal Detail Status & Alasan Penolakan untuk Warga --}}
                <div class="modal fade" id="modalStatusWarga{{ $item->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
                            <div class="modal-header border-0 px-4 pt-4 pb-0">
                                <h5 class="modal-title fw-bold">
                                    @if(in_array($item->status, ['Ditolak RT', 'Ditolak RW']))
                                        <i class="bi bi-x-circle-fill text-danger me-2"></i>Alasan Penolakan Surat
                                    @elseif($item->status === 'Selesai')
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>Surat Telah Selesai
                                    @else
                                        <i class="bi bi-clock-history text-warning me-2"></i>Status Peninjauan Surat
                                    @endif
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body px-4 pt-3 pb-3">
                                <div class="mb-3 p-3 rounded-3" style="background:#f8f9fa;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark">{{ $item->tipe_surat }}</span>
                                        <span class="badge rounded-pill px-3 py-1" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:11px;">{{ $sc['label'] }}</span>
                                    </div>
                                    <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i>Diajukan pada {{ $item->created_at->format('d M Y, H:i') }} WIB</div>
                                </div>

                                @if($item->status === 'Ditolak RT' || $item->status === 'Ditolak RW')
                                    <div class="alert alert-danger border-0 d-flex align-items-start gap-2 mb-3" style="border-radius: 12px;">
                                        <i class="bi bi-exclamation-triangle-fill fs-5 mt-n1"></i>
                                        <div>
                                            <strong>Pengajuan Ditolak oleh {{ $item->status === 'Ditolak RT' ? 'Ketua RT' : 'Ketua RW' }}</strong>
                                            <div class="small mt-1">Berikut adalah alasan penolakan atau catatan yang diberikan oleh pengurus:</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Alasan / Catatan Penolakan:</label>
                                        <div class="p-3 rounded-3 border small" style="min-height: 70px; border-color: #f1aeb5 !important; background-color: #fff8f8 !important;">
                                            @php
                                                $alasan = $item->status === 'Ditolak RT' ? ($item->catatan_rt ?: null) : ($item->catatan_rw ?: null);
                                            @endphp
                                            @if($alasan)
                                                <p class="mb-0 fw-semibold text-danger">{{ $alasan }}</p>
                                            @else
                                                <p class="mb-0 text-muted fst-italic">Tidak ada catatan alasan penolakan yang disertakan oleh pengurus. Silakan hubungi pengurus RT/RW Anda untuk informasi lebih lanjut.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-2 rounded-3 bg-light text-muted small d-flex align-items-center gap-2">
                                        <i class="bi bi-lightbulb-fill text-warning"></i>
                                        <span>Anda dapat mengajukan permohonan surat baru setelah memperbaiki persyaratan atau data di atas.</span>
                                    </div>

                                @elseif($item->status === 'Diajukan')
                                    <div class="alert alert-warning border-0 d-flex align-items-start gap-2 mb-3" style="border-radius: 12px;">
                                        <i class="bi bi-hourglass-split fs-5 mt-n1 text-warning"></i>
                                        <div>
                                            <strong>Masih Menunggu Peninjauan RT</strong>
                                            <div class="small mt-1">Surat permohonan Anda telah berhasil diajukan dan saat ini sedang menunggu peninjauan serta tanda tangan dari Ketua RT setempat.</div>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-3 bg-light border small">
                                        <div class="fw-bold text-muted mb-2">Alur Verifikasi Surat:</div>
                                        <div class="d-flex align-items-center justify-content-between text-center gap-1">
                                            <div class="p-2 rounded bg-success text-white small flex-fill">1. Diajukan &check;</div>
                                            <i class="bi bi-arrow-right text-muted"></i>
                                            <div class="p-2 rounded bg-warning text-dark small fw-bold flex-fill shadow-sm">2. Verifikasi RT &bull;</div>
                                            <i class="bi bi-arrow-right text-muted"></i>
                                            <div class="p-2 rounded bg-white border text-muted small flex-fill">3. Pengesahan RW</div>
                                            <i class="bi bi-arrow-right text-muted"></i>
                                            <div class="p-2 rounded bg-white border text-muted small flex-fill">4. Selesai</div>
                                        </div>
                                    </div>
                                    <div class="small text-muted mt-3">
                                        <i class="bi bi-info-circle me-1 text-primary"></i>Setelah Ketua RT menyetujui, surat akan otomatis diteruskan ke Ketua RW untuk ditandatangani dan disahkan.
                                    </div>

                                @elseif($item->status === 'Disetujui RT')
                                    <div class="alert alert-info border-0 d-flex align-items-start gap-2 mb-3" style="border-radius: 12px;">
                                        <i class="bi bi-clock-history fs-5 mt-n1 text-info"></i>
                                        <div>
                                            <strong>Disetujui RT &bull; Menunggu Pengesahan RW</strong>
                                            <div class="small mt-1">Surat permohonan Anda telah <strong>disetujui dan ditandatangani oleh Ketua RT</strong>. Saat ini surat sedang menunggu peninjauan dan pengesahan tanda tangan dari Ketua RW.</div>
                                        </div>
                                    </div>
                                    @if($item->catatan_rt)
                                        <div class="mb-3 p-3 rounded-3 bg-light border small">
                                            <span class="text-muted fw-bold">Catatan dari RT:</span>
                                            <p class="mb-0 text-dark">{{ $item->catatan_rt }}</p>
                                        </div>
                                    @endif
                                    <div class="p-3 rounded-3 bg-light border small">
                                        <div class="fw-bold text-muted mb-2">Alur Verifikasi Surat:</div>
                                        <div class="d-flex align-items-center justify-content-between text-center gap-1">
                                            <div class="p-2 rounded bg-success text-white small flex-fill">1. Diajukan &check;</div>
                                            <i class="bi bi-arrow-right text-muted"></i>
                                            <div class="p-2 rounded bg-success text-white small flex-fill">2. Disetujui RT &check;</div>
                                            <i class="bi bi-arrow-right text-muted"></i>
                                            <div class="p-2 rounded bg-info text-dark small fw-bold flex-fill shadow-sm">3. Pengesahan RW &bull;</div>
                                            <i class="bi bi-arrow-right text-muted"></i>
                                            <div class="p-2 rounded bg-white border text-muted small flex-fill">4. Selesai</div>
                                        </div>
                                    </div>
                                    <div class="small text-muted mt-3">
                                        <i class="bi bi-info-circle me-1 text-primary"></i>Setelah disahkan oleh Ketua RW, Anda dapat langsung mengunduh surat resmi dalam format PDF berstempel dan bertanda tangan lengkap.
                                    </div>

                                @elseif($item->status === 'Selesai')
                                    <div class="alert alert-success border-0 d-flex align-items-start gap-2 mb-3" style="border-radius: 12px;">
                                        <i class="bi bi-check-circle-fill fs-5 mt-n1 text-success"></i>
                                        <div>
                                            <strong>Surat Telah Selesai & Disahkan</strong>
                                            <div class="small mt-1">Dokumen telah disahkan secara resmi oleh Ketua RT dan Ketua RW. Silakan unduh dokumen surat resmi Anda di bawah ini.</div>
                                        </div>
                                    </div>
                                    @if($item->catatan_rw)
                                        <div class="mb-3 p-3 rounded-3 bg-light border small">
                                            <span class="text-muted fw-bold">Catatan dari RW:</span>
                                            <p class="mb-0 text-dark">{{ $item->catatan_rw }}</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="modal-footer border-0 px-4 pb-4">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                @if($item->status === 'Selesai')
                                    <a href="{{ route('warga.surat.download-pdf', $item->id) }}" class="btn btn-success rounded-pill px-4 fw-bold"><i class="bi bi-download me-1"></i>Unduh PDF</a>
                                @elseif(in_array($item->status, ['Ditolak RT', 'Ditolak RW']))
                                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-dismiss="modal" onclick="document.getElementById('jenisSuratSelect').scrollIntoView({behavior: 'smooth'});"><i class="bi bi-arrow-repeat me-1"></i>Ajukan Surat Baru</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        @endforeach

        {{-- Section 2: Ajukan Permohonan Surat --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h5 class="fw-bold mb-1">Ajukan Permohonan Surat</h5>
                <p class="text-muted small mb-3">Pilih jenis surat untuk persyaratan yang dibutuhkan.</p>
                <label class="form-label fw-bold small text-muted">JENIS SURAT</label>
                <select class="form-select bg-white border" id="jenisSuratSelect">
                    <option value="">Pilih jenis surat yang ingin diajukan...</option>
                    @foreach($tipeSurat as $i => $surat)
                        <option value="{{ $surat }}">{{ $surat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Section 3: Formulir (hidden by default) --}}
        <div class="card border-0 shadow-sm mb-4 d-none" id="formPengajuan" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h5 class="fw-bold mb-3" id="formTitle">Formulir Permohonan</h5>
                <form action="{{ route('warga.surat.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipe_surat" id="tipeSuratHidden">

                    <input type="hidden" name="pemohon_id" id="pemohonIdHidden" value="{{ $penduduk->id ?? '' }}">

                    {{-- Pilih Pemohon (anggota keluarga) --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-people-fill me-2 text-success"></i>Pilih Pemohon</h6>
                        <p class="text-muted small mb-2">Pilih anggota keluarga yang akan mengajukan surat ini.</p>
                        <select class="form-select bg-white border" id="pemohonSelect">
                            @foreach($anggotaKeluarga as $a)
                                <option value="{{ $a['id'] }}" {{ ($penduduk && $a['id'] === $penduduk->id) ? 'selected' : '' }}>
                                    {{ $a['nama_lengkap'] }} — {{ $a['status_hubungan_keluarga'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('pemohon_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Data Diri Pemohon (readonly, otomatis mengikuti pemohon terpilih) --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-person-fill me-2 text-success"></i>Data Diri Pemohon</h6>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" class="form-control bg-light border" id="pemohonNama" value="{{ $penduduk->nama_lengkap ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">NIK</label>
                                <input type="text" class="form-control bg-light border" id="pemohonNik" value="{{ $penduduk->nik ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tempat Lahir</label>
                                <input type="text" class="form-control bg-light border" id="pemohonTempatLahir" value="{{ $penduduk->tempat_lahir ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tanggal Lahir</label>
                                <input type="text" class="form-control bg-light border" id="pemohonTanggalLahir" value="{{ $penduduk && $penduduk->tanggal_lahir ? $penduduk->tanggal_lahir->format('d-m-Y') : '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Jenis Kelamin</label>
                                <input type="text" class="form-control bg-light border" id="pemohonJk" value="{{ $penduduk ? ($penduduk->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Pekerjaan</label>
                                <input type="text" class="form-control bg-light border" id="pemohonPekerjaan" value="{{ $penduduk->pekerjaan ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Agama</label>
                                <input type="text" class="form-control bg-light border" id="pemohonAgama" value="{{ $penduduk->agama ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Status Perkawinan</label>
                                <input type="text" class="form-control bg-light border" id="pemohonStatusKawin" value="{{ $penduduk->status_perkawinan ?? '-' }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Alamat Domisili</label>
                                <textarea class="form-control bg-light border" id="pemohonAlamat" rows="2" readonly>{{ $penduduk && $penduduk->keluarga ? $penduduk->keluarga->alamat : '-' }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Keperluan Pengajuan --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-send-fill me-2 text-success"></i>Keperluan Pengajuan</h6>
                        <label class="form-label small fw-bold text-muted mt-1">Jelaskan Tujuan Pengajuan Surat (Opsional)</label>
                        <textarea class="form-control bg-white border" name="keperluan" rows="3" placeholder="Contoh: Persyaratan pembuatan rekening bank baru..."></textarea>
                    </div>

                    {{-- Isian tambahan sesuai jenis surat (tampilan saja, belum disimpan) --}}
                    <div class="mb-4 d-none" id="isianTambahanWrap" data-testid="isian-tambahan">
                        <h6 class="fw-bold"><i class="bi bi-ui-checks me-2 text-success"></i>Isian Tambahan Surat</h6>
                        <p class="text-muted small mb-2">Lengkapi data berikut sesuai jenis surat. Bagian yang kosong akan dicetak berupa titik-titik untuk diisi pengurus.</p>
                        <div class="row g-3" id="isianTambahanFields"></div>
                    </div>                    {{-- Dokumen Pendukung (Preview Otomatis) --}}
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="bi bi-file-earmark-image me-2 text-success"></i>Dokumen Pendukung (KTP & KK)</h6>
                        <p class="text-muted small mb-3">Dokumen KTP dan KK Anda otomatis ditampilkan dari data yang sudah terdaftar.</p>
                        <div class="row g-3">
                            {{-- Preview KTP (mengikuti pemohon terpilih) --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Foto KTP</label>
                                <div class="border rounded-3 p-3 text-center" id="previewKtpBox" style="background: rgba(25,135,84,0.02); min-height: 180px;"></div>
                            </div>
                            {{-- Preview KK (mengikuti pemohon terpilih) --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Kartu Keluarga</label>
                                <div class="border rounded-3 p-3 text-center" id="previewKkBox" style="background: rgba(25,135,84,0.02); min-height: 180px;"></div>
                            </div>
                        </div>
                        {{-- Input unggah (dipakai bila pemohon belum punya dokumen) --}}
                        <input type="file" id="fileKtp" name="file_ktp" class="d-none" accept=".jpg,.jpeg,.png">
                        <input type="file" id="fileKk" name="file_kk" class="d-none" accept=".jpg,.jpeg,.png">
                    </div>

                    {{-- Pernyataan & Submit --}}
                    <div class="border-top pt-4">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input border-success" id="pernyataanCheck" name="pernyataan" value="1" required>
                            <label class="form-check-label small text-muted" for="pernyataanCheck">
                                Saya menyatakan bahwa data yang saya isikan di atas adalah benar dan dapat dipertanggungjawabkan sesuai dengan peraturan perundang-undangan yang berlaku.
                            </label>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" onclick="resetForm()">Batal</button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-send me-1"></i>Kirim Pengajuan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Sidebar (Kanan) --}}
    <div class="col-lg-4">
        {{-- Card: Surat Sedang Diproses --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h6 class="fw-bold mb-3"><i class="bi bi-hourglass-split text-warning me-1"></i>Sedang Diproses</h6>
                @forelse($suratProses as $item)
                    @php
                        $pc = $item->status === 'Diajukan'
                            ? ['bg' => '#FFF3CD', 'color' => '#856404', 'label' => 'Menunggu RT', 'icon' => 'bi-hourglass-split']
                            : ['bg' => '#D1ECF1', 'color' => '#0C5460', 'label' => 'Menunggu RW', 'icon' => 'bi-hourglass-split'];
                    @endphp
                    <button type="button" class="w-100 text-start border rounded-3 p-3 mb-2 bg-transparent d-flex justify-content-between align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#modalStatusWarga{{ $item->id }}" style="cursor:pointer;">
                        <div class="text-truncate">
                            <span class="fw-bold small d-block text-truncate">{{ $item->tipe_surat }}</span>
                            <span class="text-muted" style="font-size: 11px;">{{ $item->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <span class="badge rounded-pill flex-shrink-0 d-inline-flex align-items-center gap-1" style="background:{{ $pc['bg'] }};color:{{ $pc['color'] }};font-size:10px;">
                            <i class="bi {{ $pc['icon'] }}"></i>{{ $pc['label'] }}
                        </span>
                    </button>
                @empty
                    <div class="text-center py-3">
                        <i class="bi bi-inbox text-muted fs-3 d-block mb-1"></i>
                        <p class="text-muted small mb-0">Tidak ada surat yang sedang diproses.</p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h6 class="fw-bold mb-2">Butuh Bantuan?</h6>
                <p class="text-muted small mb-3">Jika ada kendala dalam pengajuan dokumen, silakan hubungi pengurus RT/RW.</p>
                <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-success rounded-pill px-4 w-100 fw-bold">
                    <i class="bi bi-whatsapp me-1"></i>Hubungi Pengurus
                </a>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4 bg-white" style="border-radius: 16px;">
                <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb-fill text-warning me-1"></i>Informasi Proses</h6>
                <p class="text-muted small mb-0">
                    Proses verifikasi: <strong>Warga &rarr; RT &rarr; RW</strong>. Setelah disetujui kedua pihak, surat bisa langsung diunduh dalam format PDF.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Riwayat Pengajuan Surat (tab Selesai & Ditolak) --}}
<style>
    /* Tab non-aktif: netral abu-abu; tab aktif: hijau solid. */
    #modalRiwayatSurat .riwayat-tab { background:#f1f3f5; color:#495057; font-weight:600; }
    #modalRiwayatSurat .riwayat-tab:hover { background:#e2e6ea; }
    #modalRiwayatSurat .riwayat-tab .riwayat-tab-badge { background:#ced4da; color:#343a40; }
    #modalRiwayatSurat .riwayat-tab.active { background:#198754; color:#fff; }
    #modalRiwayatSurat .riwayat-tab.active .riwayat-tab-badge { background:#fff; color:#198754; }
</style>
<div class="modal fade" id="modalRiwayatSurat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow bg-white" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history text-success me-2"></i>Riwayat Pengajuan Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <ul class="nav nav-pills gap-2 mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active riwayat-tab rounded-pill px-3 py-1" id="tabSelesai-tab" data-bs-toggle="pill" data-bs-target="#tabSelesai" type="button" role="tab">
                            <i class="bi bi-check-circle me-1"></i>Selesai <span class="badge rounded-pill ms-1 riwayat-tab-badge">{{ $suratSelesai->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link riwayat-tab rounded-pill px-3 py-1" id="tabDitolak-tab" data-bs-toggle="pill" data-bs-target="#tabDitolak" type="button" role="tab">
                            <i class="bi bi-x-circle me-1"></i>Ditolak <span class="badge rounded-pill ms-1 riwayat-tab-badge">{{ $suratDitolak->count() }}</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    {{-- Tab Selesai --}}
                    <div class="tab-pane fade show active" id="tabSelesai" role="tabpanel">
                        @forelse($suratSelesai as $item)
                            <div class="border rounded-3 p-3 mb-2 d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2" style="background:#fafafa;">
                                <div class="d-flex align-items-center gap-2" style="min-width:0;">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width:38px;height:38px;background:#D4EDDA;color:#155724;">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </span>
                                    <div style="min-width:0;">
                                        <span class="fw-bold small d-block text-truncate">{{ $item->tipe_surat }}</span>
                                        <span class="text-muted" style="font-size: 11px;"><i class="bi bi-calendar3 me-1"></i>{{ ($item->tanggal_selesai ?? $item->updated_at)->format('d M Y H:i') }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('warga.surat.download-pdf', $item->id) }}" class="btn btn-sm btn-success rounded-pill px-3 flex-shrink-0"><i class="bi bi-download me-1"></i>Unduh PDF</a>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted small mb-0">Belum ada surat yang selesai.</p>
                            </div>
                        @endforelse
                    </div>
                    {{-- Tab Ditolak --}}
                    <div class="tab-pane fade" id="tabDitolak" role="tabpanel">
                        @forelse($suratDitolak as $item)
                            @php $alasan = $item->status === 'Ditolak RT' ? $item->catatan_rt : $item->catatan_rw; @endphp
                            <div class="border rounded-3 p-3 mb-2" style="background:#fff8f8; border-color:#f1aeb5 !important;">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="text-truncate">
                                        <span class="fw-bold small d-block">{{ $item->tipe_surat }}</span>
                                        <span class="text-muted" style="font-size: 11px;"><i class="bi bi-calendar3 me-1"></i>{{ $item->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    <span class="badge rounded-pill flex-shrink-0" style="background:#F8D7DA;color:#721C24;font-size:10px;">{{ $item->status }}</span>
                                </div>
                                <div class="small mt-2">
                                    <span class="text-muted fw-bold d-block mb-1">Alasan:</span>
                                    @if($alasan)
                                        <span class="text-danger">{{ $alasan }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Tidak ada catatan. Silakan hubungi pengurus RT/RW.</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted small mb-0">Tidak ada surat yang ditolak.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview Gambar --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold" id="imagePreviewTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4 text-center">
                <img src="" id="imagePreviewSrc" class="img-fluid rounded shadow" style="max-height: 70vh;" alt="Preview">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectEl = document.getElementById('jenisSuratSelect');
    const formCard = document.getElementById('formPengajuan');
    const formTitle = document.getElementById('formTitle');
    const hiddenInput = document.getElementById('tipeSuratHidden');

    // --- Isian tambahan per jenis surat (UI saja) ---
    const isianPerJenis = @json(collect(config('surat.jenis', []))->map(fn ($j) => $j['fields'] ?? []));
    const isianWrap = document.getElementById('isianTambahanWrap');
    const isianFields = document.getElementById('isianTambahanFields');
    // --- Opsi "ambil dari data keluarga" ---
    let pewarisId = '';
    let syncAhliWaris = function () {};
    function labelAnggota(a) {
        const hub = a.status_hubungan_keluarga && a.status_hubungan_keluarga !== '-' ? ' (' + a.status_hubungan_keluarga + ')' : '';
        return a.nama_lengkap + hub;
    }
    function buatPilihanKeluarga(targetInput) {
        const wrap = document.createElement('div');
        wrap.className = 'mb-2';
        const sel = document.createElement('select');
        sel.className = 'form-select form-select-sm bg-white border';
        sel.setAttribute('aria-label', 'Ambil dari data keluarga');
        sel.setAttribute('data-testid', 'pilih-keluarga-' + targetInput.name);
        const o0 = document.createElement('option'); o0.value = ''; o0.textContent = 'Ambil dari data keluarga / tulis manual'; sel.appendChild(o0);
        anggota.forEach(function (a) {
            const o = document.createElement('option'); o.value = a.id; o.textContent = labelAnggota(a); sel.appendChild(o);
        });
        sel.addEventListener('change', function () {
            const a = anggota.find(x => String(x.id) === String(sel.value));
            if (a) { targetInput.value = a.nama_lengkap; }
            if (targetInput.name.indexOf('nama_pewaris') !== -1) {
                pewarisId = a ? String(a.id) : '';
                syncAhliWaris();
            }
            targetInput.focus();
        });
        wrap.appendChild(sel);
        return wrap;
    }
    function buatPilihanAhliWaris(textarea) {
        const wrap = document.createElement('div');
        wrap.className = 'mt-2';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-sm btn-outline-success rounded-pill';
        btn.setAttribute('data-testid', 'btn-ahli-waris-keluarga');
        btn.innerHTML = '<i class="bi bi-people me-1"></i>Isi dari anggota keluarga';
        const box = document.createElement('div');
        box.className = 'border rounded-3 p-2 mt-2 d-none bg-white';
        const semua = anggota.map(labelAnggota);
        const cbs = [];
        anggota.forEach(function (a) {
            const row = document.createElement('div'); row.className = 'form-check';
            const cb = document.createElement('input'); cb.type = 'checkbox'; cb.className = 'form-check-input';
            cb.id = 'ahliWaris_' + a.id; cb.value = a.id;
            const lb = document.createElement('label'); lb.className = 'form-check-label small'; lb.setAttribute('for', cb.id); lb.textContent = labelAnggota(a);
            cb.addEventListener('change', terapkan);
            row.appendChild(cb); row.appendChild(lb); box.appendChild(row); cbs.push(cb);
        });
        // Menggabungkan baris yang diketik manual dengan anggota yang dicentang.
        function terapkan() {
            const manual = textarea.value.split('\n').map(s => s.trim()).filter(s => s !== '' && semua.indexOf(s) === -1);
            const dipilih = cbs.filter(c => c.checked && !c.disabled)
                .map(c => labelAnggota(anggota.find(a => String(a.id) === String(c.value))));
            textarea.value = dipilih.concat(manual).join('\n');
        }
        // Pewaris tidak boleh ikut menjadi ahli waris.
        syncAhliWaris = function () {
            cbs.forEach(function (c) {
                const jadiPewaris = pewarisId !== '' && String(c.value) === pewarisId;
                c.disabled = jadiPewaris;
                if (jadiPewaris) c.checked = false;
            });
            terapkan();
        };
        btn.addEventListener('click', function () { box.classList.toggle('d-none'); });
        syncAhliWaris();
        wrap.appendChild(btn); wrap.appendChild(box);
        return wrap;
    }
    function renderIsianTambahan(jenis) {
        pewarisId = '';
        syncAhliWaris = function () {};
        const fields = isianPerJenis[jenis] || [];
        isianFields.innerHTML = '';
        isianWrap.classList.toggle('d-none', fields.length === 0);
        fields.forEach(function (f) {
            const col = document.createElement('div');
            col.className = f.type === 'textarea' ? 'col-12' : 'col-md-6';
            const id = 'tambahan_' + f.key;
            const label = document.createElement('label');
            label.className = 'form-label small fw-bold text-muted';
            label.setAttribute('for', id);
            label.textContent = f.label + (f.opsional ? ' (opsional)' : '');
            let input;
            if (f.type === 'textarea') {
                input = document.createElement('textarea'); input.rows = 2;
            } else if (f.type === 'select') {
                input = document.createElement('select');
                const o0 = document.createElement('option'); o0.value = ''; o0.textContent = '-- Pilih --'; input.appendChild(o0);
                (f.options || []).forEach(function (op) { const o = document.createElement('option'); o.value = op; o.textContent = op; input.appendChild(o); });
            } else {
                input = document.createElement('input'); input.type = (f.type === 'date' || f.type === 'time') ? f.type : 'text';
            }
            input.id = id; input.name = 'data_tambahan[' + f.key + ']';
            input.className = (f.type === 'select' ? 'form-select' : 'form-control') + ' bg-white border';
            col.appendChild(label);
            if (f.key === 'nama_pewaris' || f.key === 'nama_almarhum') {
                col.appendChild(buatPilihanKeluarga(input));
            }
            col.appendChild(input);
            if (f.key === 'daftar_ahli_waris') {
                col.appendChild(buatPilihanAhliWaris(input));
            }
            isianFields.appendChild(col);        });
    }
    selectEl.addEventListener('change', function() {
        if (this.value) {
            formCard.classList.remove('d-none');
            formTitle.textContent = 'Formulir Permohonan: ' + this.value;
            hiddenInput.value = this.value;
            renderIsianTambahan(this.value);
            formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            formCard.classList.add('d-none');
        }
    });

    // --- Pemilihan pemohon dari anggota keluarga ---
    const anggota = @json($anggotaKeluarga);
    const pemohonSelect = document.getElementById('pemohonSelect');
    const pemohonIdHidden = document.getElementById('pemohonIdHidden');

    function escAttr(s) { return String(s || '').replace(/'/g, "\\'"); }

    // Render kotak preview dokumen (KTP/KK) mengikuti pemohon terpilih.
    function renderDokumen(boxId, url, inputId, judul, warna) {
        const box = document.getElementById(boxId);
        if (!box) return;
        if (url) {
            box.innerHTML =
                '<img src="' + url + '" alt="' + judul + '" class="img-fluid rounded shadow-sm mb-2" style="max-height:140px;cursor:pointer;" onclick="showImageModal(\'' + escAttr(url) + '\', \'' + judul + '\')">' +
                '<div><button type="button" class="btn btn-sm btn-outline-' + warna + ' rounded-pill px-3" onclick="showImageModal(\'' + escAttr(url) + '\', \'' + judul + '\')"><i class="bi bi-zoom-in me-1"></i>Perbesar</button></div>';
        } else {
            box.innerHTML =
                '<div class="py-3">' +
                '<i class="bi bi-camera text-muted fs-1 d-block mb-2"></i>' +
                '<span class="text-muted small d-block mb-2">Belum ada foto ' + judul + ' tersimpan</span>' +
                '<label class="btn btn-sm btn-outline-' + warna + ' rounded-pill px-3" for="' + inputId + '"><i class="bi bi-upload me-1"></i>Unggah ' + judul + '</label>' +
                '</div>';
        }
    }

    function isiDataPemohon(data) {
        if (!data) return;
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.value = val ?? '-'; };
        set('pemohonNama', data.nama_lengkap);
        set('pemohonNik', data.nik);
        set('pemohonTempatLahir', data.tempat_lahir);
        set('pemohonTanggalLahir', data.tanggal_lahir);
        set('pemohonJk', data.jenis_kelamin);
        set('pemohonPekerjaan', data.pekerjaan);
        set('pemohonAgama', data.agama);
        set('pemohonStatusKawin', data.status_perkawinan);
        set('pemohonAlamat', data.alamat);
        pemohonIdHidden.value = data.id;
        renderDokumen('previewKtpBox', data.ktp_url, 'fileKtp', 'KTP', 'success');
        renderDokumen('previewKkBox', data.kk_url, 'fileKk', 'KK', 'info');
    }

    if (pemohonSelect) {
        pemohonSelect.addEventListener('change', function () {
            const data = anggota.find(a => String(a.id) === String(this.value));
            isiDataPemohon(data);
        });
        // Isi preview awal sesuai pemohon default (pemohon terpilih saat load).
        const awal = anggota.find(a => String(a.id) === String(pemohonSelect.value)) || anggota[0];
        isiDataPemohon(awal);
    }

    // Datang dari kartu Layanan Mandiri (?jenis=...): pilih jenis surat otomatis bila valid.
    @php $jenisAwal = in_array(request('jenis'), $tipeSurat, true) ? request('jenis') : null; @endphp
    const jenisAwal = @json($jenisAwal);
    if (jenisAwal && selectEl) {
        selectEl.value = jenisAwal;
        selectEl.dispatchEvent(new Event('change'));
    }
});

function showImageModal(src, title) {
    document.getElementById('imagePreviewSrc').src = src;
    document.getElementById('imagePreviewTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
}

function resetForm() {
    document.getElementById('formPengajuan').classList.add('d-none');
    document.getElementById('jenisSuratSelect').value = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endpush

