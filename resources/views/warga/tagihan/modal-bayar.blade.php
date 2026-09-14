<div class="modal fade" id="modalBayar{{ $bayar->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <i class="bi bi-cash-coin text-success fs-4 me-2"></i> Pembayaran {{ $bayar->tagihan->unit === 'RT' ? 'Iuran RT' : 'Iuran DKM' }}
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
                            <i class="bi bi-bank me-1"></i> Transfer
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
                    
                    <div class="tab-content">
                        <!-- TRANSFER TAB -->
                        <div class="tab-pane fade show active" id="tf-{{ $bayar->id }}" role="tabpanel">
                            @php
                                $rek = $bayar->tagihan->unit === 'RT' ? $rekeningRt : $rekeningDkm;
                            @endphp
                            
                            @if($rek)
                                <div class="border rounded-3 p-3 mb-3 position-relative bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Nomor Rekening</span>
                                        <span class="badge bg-success text-uppercase fw-bold">{{ $rek->bank }}</span>
                                    </div>
                                    <h4 class="fw-bold tracking-wide mb-3 font-monospace d-flex align-items-center justify-content-between">
                                        {{ $rek->no_rek }}
                                        <button type="button" class="btn btn-sm btn-light border-0 shadow-sm rounded-circle" onclick="navigator.clipboard.writeText('{{ $rek->no_rek }}')" title="Salin">
                                            <i class="bi bi-copy text-muted"></i>
                                        </button>
                                    </h4>
                                    <span class="text-muted small d-block">Atas Nama</span>
                                    <span class="fw-bold d-block">{{ $rek->nama_rek }}</span>
                                </div>
                                <div class="alert alert-success bg-success bg-opacity-10 border-0 rounded-3 small d-flex">
                                    <i class="bi bi-info-circle-fill text-success fs-5 me-2 mt-1"></i>
                                    <div>Pastikan nama rekening tujuan sesuai dengan <strong>{{ $rek->nama_rek }}</strong> sebelum melakukan transfer.</div>
                                </div>
                                
                                <input type="hidden" name="metode_tf" value="Transfer" class="metode-input-{{ $bayar->id }}">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Upload Bukti Transfer</label>
                                    <input type="file" name="bukti_file_tf" class="form-control bg-light" accept="image/*">
                                </div>
                            @else
                                <div class="alert alert-warning border-0 rounded-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Rekening bank belum diatur oleh bendahara.
                                </div>
                            @endif
                        </div>

                        <!-- QRIS TAB -->
                        <div class="tab-pane fade" id="qris-{{ $bayar->id }}" role="tabpanel">
                            @if($rek && $rek->qris_file)
                                <div class="text-center mb-4">
                                    <div class="bg-success bg-opacity-10 text-success d-inline-block px-3 py-1 rounded-pill fw-bold small mb-3 text-uppercase">QRIS Tersedia</div>
                                    <div class="border rounded-4 p-3 d-inline-block bg-white shadow-sm">
                                        <img src="{{ asset('storage/' . $rek->qris_file) }}" alt="QRIS" class="img-fluid" style="max-height: 250px;">
                                    </div>
                                    <p class="text-muted small mt-3 px-4">Scan QR code di atas menggunakan aplikasi m-banking atau e-wallet (Gopay, OVO, Dana, dll) pilihan Anda.</p>
                                </div>
                                
                                <input type="hidden" name="metode_qris" value="QRIS" class="metode-input-{{ $bayar->id }}" disabled>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Upload Bukti Scan QRIS</label>
                                    <input type="file" name="bukti_file_qris" class="form-control bg-light" accept="image/*">
                                </div>
                            @else
                                <div class="alert alert-warning border-0 rounded-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> QRIS belum tersedia untuk unit ini.
                                </div>
                            @endif
                        </div>

                        <!-- CASH TAB -->
                        <div class="tab-pane fade" id="cash-{{ $bayar->id }}" role="tabpanel">
                            <div class="text-center py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-wallet2 text-success fs-1"></i>
                                </div>
                                <h5 class="fw-bold">Bayar Langsung (Cash)</h5>
                                <p class="text-muted px-4">Serahkan uang tunai langsung ke Bendahara {{ $bayar->tagihan->unit }}. Klik tombol di bawah untuk mengonfirmasi bahwa Anda telah/akan menyerahkan uang tersebut secara langsung.</p>
                                
                                <input type="hidden" name="metode_cash" value="Cash" class="metode-input-{{ $bayar->id }}" disabled>
                            </div>
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
        
        const inputTf = modal.querySelector('input[name="bukti_file_tf"]');
        const inputQris = modal.querySelector('input[name="bukti_file_qris"]');
        
        const updateName = (active) => {
            if(inputTf) inputTf.name = active === 'Transfer' ? 'bukti_file' : 'bukti_file_tf';
            if(inputQris) inputQris.name = active === 'QRIS' ? 'bukti_file' : 'bukti_file_qris';
        };
        
        // Default
        updateName('Transfer');
        
        modal.querySelectorAll('button[data-bs-toggle="pill"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (event) {
                const target = event.target.getAttribute('data-bs-target');
                if (target === '#tf-{{ $bayar->id }}') {
                    realMetode.value = 'Transfer';
                    submitBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Sudah Bayar & Unggah Bukti';
                    updateName('Transfer');
                } else if (target === '#qris-{{ $bayar->id }}') {
                    realMetode.value = 'QRIS';
                    submitBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Sudah Bayar & Unggah Bukti';
                    updateName('QRIS');
                } else {
                    realMetode.value = 'Cash';
                    submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Konfirmasi Penyerahan Cash';
                    updateName('Cash');
                }
            });
        });
    });
</script>
