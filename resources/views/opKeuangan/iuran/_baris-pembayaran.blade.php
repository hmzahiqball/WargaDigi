{{-- Satu baris pembayaran tagihan (dipakai di tab Daftar & Verifikasi) --}}
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
    <td>{{ $bayar->nominal ? 'Rp ' . number_format($bayar->nominal, 0, ',', '.') : '-' }}</td>
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
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#buktiModal{{ $bayar->id }}">
                <i class="bi bi-image"></i> Lihat
            </button>
        @else
            -
        @endif
    </td>
    <td class="text-end pe-4">
        @if($bayar->status === 'Pending')
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#verifModal{{ $bayar->id }}" data-testid="periksa-pembayaran">
                <i class="bi bi-search me-1"></i>Periksa
            </button>

            <!-- Dialog detail pembayaran: setujui atau tolak (dengan alasan) -->
            <div class="modal fade" id="verifModal{{ $bayar->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content text-start border-0 shadow-lg rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Detail Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="bg-light rounded-3 p-3 small mb-3">
                                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Warga</span><strong class="text-end">{{ $bayar->keluarga->kepalaKeluarga->nama_lengkap ?? 'Keluarga' }}</strong></div>
                                <div class="d-flex justify-content-between mb-1"><span class="text-muted">No. KK</span><span>{{ $bayar->keluarga->no_kk }}</span></div>
                                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Tagihan</span><span class="text-end">{{ $tagihan->judul }}</span></div>
                                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Nominal</span><strong class="text-success">{{ $bayar->nominal ? 'Rp ' . number_format($bayar->nominal, 0, ',', '.') : $tagihan->formatted_nominal }}</strong></div>
                                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Metode</span><span>{{ $bayar->metode ?? '-' }}</span></div>
                                <div class="d-flex justify-content-between"><span class="text-muted">Waktu kirim</span><span>{{ \Carbon\Carbon::parse($bayar->updated_at)->translatedFormat('d M Y H:i') }}</span></div>
                            </div>
                            @if($bayar->bukti_file)
                                @php
                                    $cleanB = str_replace('\\', '/', $bayar->bukti_file);
                                    $urlB = str_starts_with($cleanB, 'storage/') ? asset($cleanB) : asset('storage/' . $cleanB);
                                @endphp
                                <div class="text-center mb-3">
                                    <a href="{{ $urlB }}" target="_blank" rel="noopener">
                                        <img src="{{ $urlB }}" alt="Bukti pembayaran" class="img-fluid rounded border" style="max-height:320px;object-fit:contain;">
                                    </a>
                                </div>
                            @else
                                <div class="text-muted small text-center mb-3">Tidak ada bukti pembayaran dilampirkan.</div>
                            @endif

                            <form method="POST" action="{{ route('opkeuangan.pembayaran.reject', $bayar->id) }}" id="formTolakTagihan{{ $bayar->id }}" class="d-none">
                                @csrf @method('PUT')
                                <label class="form-label fw-semibold" for="alasanTagihan{{ $bayar->id }}">Alasan penolakan</label>
                                <textarea id="alasanTagihan{{ $bayar->id }}" name="alasan" rows="3" maxlength="500" class="form-control" placeholder="Contoh: Bukti transfer buram atau nominal tidak sesuai" required></textarea>
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <button type="button" class="btn btn-light" onclick="toggleTolakTagihan('{{ $bayar->id }}', false)">Batal</button>
                                    <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-0 pt-0" id="aksiVerifTagihan{{ $bayar->id }}">
                            <button type="button" class="btn btn-outline-danger" onclick="toggleTolakTagihan('{{ $bayar->id }}', true)"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                            <form action="{{ route('opkeuangan.pembayaran.approve', $bayar->id) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                                <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Setujui</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @once
                <script>
                    function toggleTolakTagihan(id, tampil) {
                        document.getElementById('formTolakTagihan' + id).classList.toggle('d-none', !tampil);
                        document.getElementById('aksiVerifTagihan' + id).classList.toggle('d-none', tampil);
                        if (tampil) document.getElementById('alasanTagihan' + id).focus();
                    }
                </script>
            @endonce
        @elseif($bayar->status === 'Unpaid')
            @if($bayar->catatan)
                <span class="text-danger small me-1" title="{{ $bayar->catatan }}"><i class="bi bi-info-circle"></i> Ditolak</span>
            @endif
            <button type="button" class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#ceklisModal{{ $bayar->id }}" title="Catat Pembayaran">
                <i class="bi bi-check-lg"></i> Catat Bayar
            </button>
            <form action="{{ route('opkeuangan.pembayaran.broadcast', $bayar->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-outline-warning d-inline-flex align-items-center justify-content-center" style="vertical-align: middle;" title="Kirim Pengingat (Broadcast)">
                    <i class="bi bi-bell"></i>
                </button>
            </form>

            <!-- Modal Ceklis Bayar (bendahara) -->
            <div class="modal fade" id="ceklisModal{{ $bayar->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content text-start" method="POST" action="{{ route('opkeuangan.pembayaran.ceklis', $bayar->id) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Catat Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">Warga: <strong>{{ $bayar->keluarga->kepalaKeluarga->nama_lengkap ?? 'Keluarga' }}</strong></p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominal (Rp)</label>
                                <input type="text" inputmode="numeric" data-money name="nominal" class="form-control"
                                    value="{{ number_format($tagihan->nominal, 0, ',', '.') }}" {{ $tagihan->nominal_fix ? 'readonly' : '' }} required>
                                <div class="form-text">
                                    @if($tagihan->nominal_fix)
                                        Nominal fix, tidak dapat diubah.
                                    @else
                                        Minimal Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}. Boleh lebih bila warga membayar lebih.
                                    @endif
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Tanggal Bayar</label>
                                    <input type="date" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Metode</label>
                                    <select name="metode" class="form-select" required>
                                        <option value="Cash">Tunai</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="QRIS">QRIS</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Bukti (opsional)</label>
                                    <input type="file" name="bukti_file" class="form-control" accept=".jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan &amp; Masuk Kas</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($bayar->status === 'Paid')
            @if($bayar->bukti_file)
                <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#buktiModal{{ $bayar->id }}" title="Lihat Bukti Pembayaran">
                    <i class="bi bi-eye"></i>
                </button>
            @endif
            <span class="text-success small"><i class="bi bi-check-all"></i> Disetujui</span>
        @endif

        @if($bayar->bukti_file)
            <!-- Modal Preview Bukti Pembayaran Warga -->
            <div class="modal fade" id="buktiModal{{ $bayar->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-start border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-light border-0 py-3">
                            <h5 class="modal-title fw-bold mb-0">
                                <i class="bi bi-receipt text-primary me-2"></i> Bukti Pembayaran Warga
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <div class="mb-3 text-start bg-light p-3 rounded-3 small">
                                <div class="row g-2">
                                    <div class="col-6"><span class="text-muted">Keluarga:</span> <strong class="d-block text-dark">{{ $bayar->keluarga->kepalaKeluarga->nama_lengkap ?? 'Keluarga' }}</strong></div>
                                    <div class="col-6"><span class="text-muted">Metode:</span> <strong class="d-block text-primary">{{ $bayar->metode ?? '-' }}</strong></div>
                                    <div class="col-6"><span class="text-muted">Nominal:</span> <strong class="d-block text-success">{{ $bayar->nominal ? 'Rp ' . number_format($bayar->nominal, 0, ',', '.') : $tagihan->formatted_nominal }}</strong></div>
                                    <div class="col-6"><span class="text-muted">Waktu Kirim:</span> <strong class="d-block text-dark">{{ \Carbon\Carbon::parse($bayar->updated_at)->translatedFormat('d M Y H:i') }}</strong></div>
                                </div>
                            </div>
                            <div class="border rounded-3 p-2 bg-light d-inline-block shadow-sm mb-3">
                                @php
                                    $cleanBukti = str_replace('\\', '/', $bayar->bukti_file);
                                    $buktiUrl = str_starts_with($cleanBukti, 'storage/') ? asset($cleanBukti) : asset('storage/' . $cleanBukti);
                                @endphp
                                <img src="{{ $buktiUrl }}" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 380px; object-fit: contain;">
                            </div>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ $buktiUrl }}" target="_blank" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Gambar Penuh
                                </a>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light py-2">
                            <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </td>
</tr>
