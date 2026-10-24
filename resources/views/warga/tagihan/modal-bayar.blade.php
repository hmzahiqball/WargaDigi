<div class="modal fade" id="modalBayar{{ $bayar->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @php
                $unit = $bayar->tagihan->unit;
                $rek = match($unit) {
                    'RT' => $rekeningRt,
                    'RW' => $rekeningRw,
                    'DKM' => $rekeningDkm,
                    default => null,
                };
            @endphp

            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <i class="bi bi-cash-coin text-success fs-4 me-2"></i> Pembayaran Iuran {{ $unit }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4 bg-white">
                <!-- Info Tagihan -->
                <div class="card border-0 bg-success bg-opacity-10 rounded-3 mb-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted small mb-1">Total Tagihan</span>
                                <h3 class="fw-bold text-dark mb-0">{{ $bayar->tagihan->formatted_nominal }}</h3>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Belum Lunas</span>
                                <div class="small text-muted mt-2">{{ $bayar->tagihan->judul }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="fw-semibold text-dark mb-2">Metode Pembayaran</p>
                
                <!-- Custom Tabs -->
                <ul class="nav nav-pills nav-fill bg-light p-1 rounded-3 mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-3 py-2 fw-medium text-dark" data-bs-toggle="pill" data-bs-target="#tf-{{ $bayar->id }}" type="button" role="tab">
                            <i class="bi bi-bank me-1"></i> Transfer Bank
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-3 py-2 fw-medium text-dark" data-bs-toggle="pill" data-bs-target="#qris-{{ $bayar->id }}" type="button" role="tab">
                            <i class="bi bi-qr-code-scan me-1"></i> QRIS
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-3 py-2 fw-medium text-dark" data-bs-toggle="pill" data-bs-target="#cash-{{ $bayar->id }}" type="button" role="tab">
                            <i class="bi bi-wallet2 me-1"></i> Cash
                        </button>
                    </li>
                </ul>

                <form action="{{ route('warga.tagihan.bayar', $bayar->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="tab-content mb-3">
                        <!-- TRANSFER TAB -->
                        <div class="tab-pane fade show active" id="tf-{{ $bayar->id }}" role="tabpanel">
                            @if($rek)
                                <div class="border rounded-3 p-3 mb-3 position-relative bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Nomor Rekening {{ $unit }}</span>
                                        <span class="badge bg-success text-uppercase fw-bold">{{ $rek->bank }}</span>
                                    </div>
                                    <h4 class="fw-bold tracking-wide mb-3 font-monospace d-flex align-items-center justify-content-between text-primary">
                                        <span>{{ $rek->no_rek }}</span>
                                        <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-circle" onclick="navigator.clipboard.writeText('{{ $rek->no_rek }}')" title="Salin Nomor Rekening">
                                            <i class="bi bi-copy text-muted"></i>
                                        </button>
                                    </h4>
                                    <span class="text-muted small d-block">Atas Nama</span>
                                    <span class="fw-bold d-block">{{ $rek->nama_rek }}</span>
                                </div>
                                <div class="alert alert-success bg-success bg-opacity-10 border-0 rounded-3 small d-flex mb-0">
                                    <i class="bi bi-info-circle-fill text-success fs-5 me-2 mt-1"></i>
                                    <div>Pastikan nama rekening penerima sesuai dengan <strong>{{ $rek->nama_rek }}</strong> sebelum mentransfer.</div>
                                </div>
                            @else
                                <div class="alert alert-warning border-0 rounded-3 mb-0">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Rekening bank untuk unit <strong>{{ $unit }}</strong> belum diatur oleh bendahara.
                                </div>
                            @endif
                        </div>

                        <!-- QRIS TAB -->
                        <div class="tab-pane fade" id="qris-{{ $bayar->id }}" role="tabpanel">
                            @if($rek && $rek->qris_file)
                                <div class="text-center mb-2">
                                    <div class="bg-success bg-opacity-10 text-success d-inline-block px-3 py-1 rounded-pill fw-bold small mb-3 text-uppercase">QRIS Unit {{ $unit }}</div>
                                    <div class="border rounded-4 p-3 d-inline-block bg-white shadow-sm">
                                        @php
                                            $cleanQris = str_replace('\\', '/', $rek->qris_file);
                                            $qrisUrl = str_starts_with($cleanQris, 'storage/') ? asset($cleanQris) : asset('storage/' . $cleanQris);
                                        @endphp
                                        <img src="{{ $qrisUrl }}" alt="QRIS {{ $unit }}" class="img-fluid" style="max-height: 230px;">
                                    </div>
                                    <p class="text-muted small mt-2 px-3 mb-0">Scan QR code di atas menggunakan BCA Mobile, Livin, GoPay, OVO, Dana, ShopeePay, dll.</p>
                                </div>
                            @else
                                <div class="alert alert-warning border-0 rounded-3 mb-0">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Kode QRIS untuk unit <strong>{{ $unit }}</strong> belum diunggah oleh bendahara.
                                </div>
                            @endif
                        </div>

                        <!-- CASH TAB -->
                        <div class="tab-pane fade" id="cash-{{ $bayar->id }}" role="tabpanel">
                            <div class="text-center py-3">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                    <i class="bi bi-wallet2 text-success fs-2"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Bayar Tunai Langsung (Cash)</h5>
                                <p class="text-muted small px-3 mb-0">Serahkan uang tunai secara langsung kepada Bendahara <strong>{{ $unit }}</strong>. Klik tombol konfirmasi di bawah jika Anda telah atau berencana menyerahkan uang tunai.</p>
                            </div>
                        </div>
                    </div>

                    <!-- UPLOAD SECTION (Aktif untuk Transfer & QRIS) -->
                    <div class="mb-3" id="uploadSection-{{ $bayar->id }}">
                        <label class="form-label fw-semibold">
                            Unggah Bukti Pembayaran <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="bukti_file" id="buktiFileInput-{{ $bayar->id }}" class="form-control" accept="image/*">
                        <input type="hidden" name="bukti_base64" id="buktiBase64Input-{{ $bayar->id }}">
                        <div class="form-text small">Pilih foto atau screenshot struk transfer / bukti transaksi QRIS (format JPG/PNG/WEBP, maks 5MB).</div>

                        <div id="buktiPreviewWrapper-{{ $bayar->id }}" class="mt-2 text-center" style="display: none;">
                            <span class="text-muted small d-block mb-1">Preview Bukti:</span>
                            <img id="buktiPreview-{{ $bayar->id }}" src="" alt="Preview Bukti" class="img-fluid rounded border shadow-sm" style="max-height: 180px;">
                        </div>
                    </div>
                    
                    <input type="hidden" name="metode" id="realMetode-{{ $bayar->id }}" value="Transfer">
                    
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4 fw-semibold" id="submitBtn-{{ $bayar->id }}">
                            <i class="bi bi-upload me-1"></i> Konfirmasi & Unggah Bukti
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalBayar{{ $bayar->id }}');
        if (!modal) return;
        
        const realMetode = document.getElementById('realMetode-{{ $bayar->id }}');
        const submitBtn = document.getElementById('submitBtn-{{ $bayar->id }}');
        const uploadSection = document.getElementById('uploadSection-{{ $bayar->id }}');
        const buktiInput = document.getElementById('buktiFileInput-{{ $bayar->id }}');
        const base64Input = document.getElementById('buktiBase64Input-{{ $bayar->id }}');
        const previewWrapper = document.getElementById('buktiPreviewWrapper-{{ $bayar->id }}');
        const previewImg = document.getElementById('buktiPreview-{{ $bayar->id }}');

        if (buktiInput) {
            buktiInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (previewImg) previewImg.src = e.target.result;
                        if (previewWrapper) previewWrapper.style.display = 'block';
                        if (base64Input) base64Input.value = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    if (previewWrapper) previewWrapper.style.display = 'none';
                    if (base64Input) base64Input.value = '';
                }
            });
        }
        
        modal.querySelectorAll('button[data-bs-toggle="pill"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (event) {
                const target = event.target.getAttribute('data-bs-target');
                if (target === '#tf-{{ $bayar->id }}') {
                    if (realMetode) realMetode.value = 'Transfer';
                    if (uploadSection) uploadSection.style.display = 'block';
                    if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Konfirmasi & Unggah Bukti';
                } else if (target === '#qris-{{ $bayar->id }}') {
                    if (realMetode) realMetode.value = 'QRIS';
                    if (uploadSection) uploadSection.style.display = 'block';
                    if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Konfirmasi & Unggah Bukti';
                } else {
                    if (realMetode) realMetode.value = 'Cash';
                    if (uploadSection) uploadSection.style.display = 'none';
                    if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Konfirmasi Penyerahan Cash';
                }
            });
        });
    });
</script>
