@extends('layouts.global')

@section('title', 'Detail Tagihan: ' . $tagihan->judul)

@section('content')
<div class="mb-4">
    <a href="{{ route('opkeuangan.iuran.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Iuran
    </a>
    <h2 class="fw-bold text-dark mb-1">{{ $tagihan->judul }}</h2>
    <p class="text-muted mb-0">Total Tagihan: <span class="fw-bold text-primary">{{ $tagihan->formatted_nominal }}</span> | Tenggat: {{ $tagihan->tenggat ? \Carbon\Carbon::parse($tagihan->tenggat)->translatedFormat('d M Y') : '-' }}</p>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kepala Keluarga</th>
                        <th>Status</th>
                        <th>Metode</th>
                        <th>Waktu Bayar</th>
                        <th>Bukti</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihan->pembayaran as $bayar)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block">{{ $bayar->keluarga->kepalaKeluarga->nama_lengkap ?? 'Tidak Diketahui' }}</span>
                            <span class="text-muted small">No. KK: {{ $bayar->keluarga->no_kk }}</span>
                        </td>
                        <td>
                            @if($bayar->status === 'Paid')
                                <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i> Lunas</span>
                            @elseif($bayar->status === 'Pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock"></i> Menunggu Verifikasi</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle"></i> Belum Bayar</span>
                            @endif
                        </td>
                        <td>{{ $bayar->metode ?? '-' }}</td>
                        <td>
                            @if($bayar->status !== 'Unpaid')
                                <span class="small">{{ \Carbon\Carbon::parse($bayar->updated_at)->translatedFormat('d M Y H:i') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($bayar->bukti_file)
                                <a href="{{ asset('storage/' . $bayar->bukti_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-image"></i> Lihat
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($bayar->status === 'Pending')
                                <form action="{{ route('opkeuangan.pembayaran.approve', $bayar->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button class="btn btn-sm btn-success" title="Setujui Pembayaran">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $bayar->id }}" title="Tolak Pembayaran">
                                    <i class="bi bi-x-lg"></i>
                                </button>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $bayar->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form class="modal-content text-start" method="POST" action="{{ route('opkeuangan.pembayaran.reject', $bayar->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Tolak Pembayaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda akan menolak pembayaran dari <strong>{{ $bayar->keluarga->kepalaKeluarga->nama_lengkap ?? 'Keluarga' }}</strong>.</p>
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan Penolakan</label>
                                                    <input type="text" name="alasan" class="form-control" placeholder="Contoh: Bukti transfer buram/tidak valid" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @elseif($bayar->status === 'Unpaid' && $bayar->catatan)
                                <span class="text-danger small" title="{{ $bayar->catatan }}"><i class="bi bi-info-circle"></i> Ditolak</span>
                                <form action="{{ route('opkeuangan.pembayaran.broadcast', $bayar->id) }}" method="POST" class="d-inline mt-1">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" title="Kirim Pengingat (Broadcast)">
                                        <i class="bi bi-bell"></i>
                                    </button>
                                </form>
                            @elseif($bayar->status === 'Unpaid')
                                <form action="{{ route('opkeuangan.pembayaran.broadcast', $bayar->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" title="Kirim Pengingat (Broadcast)">
                                        <i class="bi bi-bell"></i>
                                    </button>
                                </form>
                            @elseif($bayar->status === 'Paid')
                                <span class="text-success small"><i class="bi bi-check-all"></i> Disetujui</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
