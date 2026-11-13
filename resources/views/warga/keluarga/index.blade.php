@extends('layouts.global')

@section('title', 'Profil Keluarga')

@php
    // Susun alamat KK lengkap (gaya RT).
    $bagian = [];
    if (! empty($keluarga->alamat)) { $bagian[] = $keluarga->alamat; }
    $rtKode = $keluarga->rt?->kode_rt;
    if ($rtKode || $keluarga->rw) {
        $bagian[] = 'RT ' . str_pad((string) ($rtKode ?? '000'), 3, '0', STR_PAD_LEFT)
            . '/RW ' . str_pad((string) ($keluarga->rw ?? '000'), 3, '0', STR_PAD_LEFT);
    }
    if (! empty($keluarga->kelurahan)) { $bagian[] = 'Kel. ' . $keluarga->kelurahan; }
    if (! empty($keluarga->kecamatan)) { $bagian[] = 'Kec. ' . $keluarga->kecamatan; }
    if (! empty($keluarga->kabupaten)) { $bagian[] = $keluarga->kabupaten; }
    $prov = $keluarga->provinsi;
    if (! empty($keluarga->kode_pos)) { $prov = ($prov ? $prov . ' ' : '') . $keluarga->kode_pos; }
    if (! empty($prov)) { $bagian[] = $prov; }
    $alamatLengkap = implode(', ', $bagian);

    $badgeStatus = function ($status) {
        return match ($status) {
            'disetujui'   => ['bg-success bg-opacity-10 text-success', 'Disetujui'],
            'ditolak'     => ['bg-danger bg-opacity-10 text-danger', 'Ditolak'],
            'menunggu_rw' => ['bg-info bg-opacity-10 text-info', 'Menunggu RW'],
            default       => ['bg-warning bg-opacity-10 text-warning', 'Menunggu RT'],
        };
    };
@endphp

@section('content')
<div class="container-fluid px-0">

    <!-- Alert Akses Terbatas -->
    <div class="alert alert-success bg-success bg-opacity-10 border-0 border-start border-success border-4 d-flex align-items-start p-3 mb-4 rounded-3" role="alert">
        <i class="bi bi-info-circle-fill text-success fs-5 me-3 mt-1"></i>
        <div>
            <h6 class="fw-bold text-success mb-1" style="font-size: 0.95rem;">Akses Terbatas</h6>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Data ini merupakan rekaman resmi administrasi warga. Penambahan maupun perubahan data dilakukan melalui <strong>pengajuan</strong> yang disetujui oleh <strong>Staff RT dan RW</strong>. Data penduduk hanya berubah setelah RW menyetujui.</p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Profil Keluarga</h3>
            <p class="text-muted mb-0">Data Kartu Keluarga dan anggota penduduk.</p>
        </div>
        @if($isPemegangAkun)
        <div class="d-flex gap-2">
            <a href="{{ route('warga.keluarga.edit') }}" class="btn btn-outline-success" style="border-radius:0.5rem;">
                <i class="bi bi-pencil-square me-1"></i> Ajukan Perubahan KK
            </a>
            <a href="{{ route('warga.keluarga.anggota.create') }}" class="btn btn-success" style="background-color: #559e66; border: none; border-radius:0.5rem;">
                <i class="bi bi-person-plus me-1"></i> Tambah Anggota
            </a>
        </div>
        @endif
    </div>

    {{-- Profil KK --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-house-door me-2 text-success"></i>Profil Kartu Keluarga</h6>
            <dl class="row mb-0">
                <dt class="col-sm-3">Nomor KK</dt>
                <dd class="col-sm-9" style="font-family: monospace;">{{ $keluarga->no_kk }}</dd>
                <dt class="col-sm-3">Kepala Keluarga</dt>
                <dd class="col-sm-9">{{ $keluarga->kepalaKeluarga?->nama_lengkap ?? '-' }}</dd>
                <dt class="col-sm-3">Alamat (sesuai KK)</dt>
                <dd class="col-sm-9">{{ $alamatLengkap ?: '-' }}</dd>
                <dt class="col-sm-3">Alamat Domisili</dt>
                <dd class="col-sm-9">{{ $keluarga->alamat_domisili ?: '-' }}</dd>
                <dt class="col-sm-3">Status KK</dt>
                <dd class="col-sm-9">
                    @if($keluarga->status_warga === 'tetap')
                        <span class="badge bg-success bg-opacity-10 text-success">Warga Tetap</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning">Bukan Warga Tetap</span>
                    @endif
                </dd>
                <dt class="col-sm-3">Pemilik Rumah</dt>
                <dd class="col-sm-9">{{ $keluarga->pemilik_rumah ? 'Ya' : 'Bukan' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Akun Keluarga --}}
    @if($akun)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-person-lock me-2 text-success"></i>Akun Keluarga</h6>
            <dl class="row mb-0">
                <dt class="col-sm-3">Username</dt>
                <dd class="col-sm-9">{{ $akun->username }}</dd>
                <dt class="col-sm-3">Pemegang Akun</dt>
                <dd class="col-sm-9">{{ $keluarga->pemegangAkun?->nama_lengkap ?? $keluarga->kepalaKeluarga?->nama_lengkap ?? '-' }}</dd>
                <dt class="col-sm-3">Status Akun</dt>
                <dd class="col-sm-9">
                    @if($akun->status_akun === 'Active')
                        <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                    @elseif($akun->status_akun === 'Nonaktif')
                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning">Belum Diaktivasi</span>
                    @endif
                </dd>
            </dl>
            @if($isPemegangAkun)
            <div class="mt-3 pt-3 border-top">
                <a href="{{ route('warga.keluarga.pindah-akun') }}" class="btn btn-outline-success" style="border-radius:0.5rem;">
                    <i class="bi bi-arrow-left-right me-1"></i> Pindahkan Akun ke Anggota Lain
                </a>
                <p class="text-muted small mb-0 mt-2">
                    Pindahkan hak pemegang akun keluarga kepada anggota keluarga lain. Akun Anda akan dinonaktifkan dan anggota terpilih menerima link aktivasi melalui Ketua RT.
                </p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Anggota Keluarga --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-people me-2 text-success"></i>Anggota Keluarga ({{ $anggota->count() }} jiwa)</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted small fw-semibold py-3">NAMA</th>
                            <th class="text-muted small fw-semibold py-3">NIK</th>
                            <th class="text-muted small fw-semibold py-3">L/P</th>
                            <th class="text-muted small fw-semibold py-3">HUBUNGAN</th>
                            <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota as $a)
                        <tr>
                            <td class="py-3"><div class="fw-semibold text-dark">{{ $a->nama_lengkap }}</div></td>
                            <td class="py-3"><span style="font-family: monospace;">{{ $a->nik }}</span></td>
                            <td class="py-3">{{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td>
                            <td class="py-3">{{ $a->status_hubungan_keluarga }}</td>
                            <td class="py-3 text-end">
                                @if($isPemegangAkun)
                                <a href="{{ route('warga.keluarga.anggota.show', $a->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="{{ route('warga.keluarga.anggota.edit', $a->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                @else
                                <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada anggota.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Riwayat Pengajuan --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-success"></i>Riwayat Pengajuan</h6>
                @if($riwayatTotal > $riwayat->count())
                    <a href="{{ route('warga.keluarga.riwayat') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Lihat Semua ({{ $riwayatTotal }})</a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted small fw-semibold py-3">TANGGAL</th>
                            <th class="text-muted small fw-semibold py-3">JENIS</th>
                            <th class="text-muted small fw-semibold py-3">STATUS</th>
                            <th class="text-muted small fw-semibold py-3">KETERANGAN</th>
                            <th class="text-muted small fw-semibold py-3 text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        @php [$badgeClass, $badgeLabel] = $badgeStatus($r->status); @endphp
                        <tr>
                            <td class="py-3">{{ $r->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3">{{ $r->labelJenis() }}</td>
                            <td class="py-3"><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                            <td class="py-3 text-muted small">{{ $r->keterangan_tolak ?: '-' }}</td>
                            <td class="py-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalRiwayatIdx-{{ $r->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal detail riwayat (di luar tabel agar HTML valid & modal berfungsi) --}}
@foreach($riwayat as $r)
<div class="modal fade" id="modalRiwayatIdx-{{ $r->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">{{ $r->labelJenis() }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-table me-2 text-success"></i>Perubahan Data</h6>
                @include('partials._mutasi-diff', ['mutasi' => $r])

                <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-clock-history me-2 text-success"></i>Riwayat Proses</h6>
                @include('partials._mutasi-timeline', ['mutasi' => $r])
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
