{{-- Modal Broadcast Notifikasi Tagihan --}}
<div class="modal fade" id="modalBroadcastNotifikasi" tabindex="-1" aria-labelledby="modalBroadcastLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-success bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-megaphone-fill text-success"></i>
                    </div>
                    <h5 class="modal-title fw-bold mb-0" id="modalBroadcastLabel">Broadcast Notifikasi Tagihan</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pt-4">
                {{-- Tujuan Pengiriman --}}
                <div class="mb-4">
                    <label class="form-label fw-bold small">Tujuan Pengiriman</label>
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center position-relative broadcast-target-card active" data-target="semua">
                                <input type="radio" name="targetBroadcast" value="semua" class="position-absolute top-0 end-0 m-2" checked>
                                <i class="bi bi-people-fill d-block fs-4 mb-1"></i>
                                <span class="fw-bold small d-block">Semua Warga</span>
                                <span class="text-muted" style="font-size: 0.7rem;">Seluruh penghuni RW 12</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center position-relative broadcast-target-card" data-target="belum_bayar">
                                <input type="radio" name="targetBroadcast" value="belum_bayar" class="position-absolute top-0 end-0 m-2">
                                <i class="bi bi-cash-stack d-block fs-4 mb-1"></i>
                                <span class="fw-bold small d-block">Belum Bayar</span>
                                <span class="text-muted" style="font-size: 0.7rem;">Hanya yang menunggak</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center position-relative broadcast-target-card" data-target="blok_tertentu">
                                <input type="radio" name="targetBroadcast" value="blok_tertentu" class="position-absolute top-0 end-0 m-2">
                                <i class="bi bi-buildings d-block fs-4 mb-1"></i>
                                <span class="fw-bold small d-block">Blok Tertentu</span>
                                <span class="text-muted" style="font-size: 0.7rem;">Pilih blok/rumah</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jalur Pengiriman --}}
                <div class="mb-4">
                    <label class="form-label fw-bold small">Jalur Pengiriman</label>
                    <div class="d-flex gap-3">
                        <div class="d-flex align-items-center gap-2 border rounded-pill px-3 py-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="jalurAplikasi" checked>
                            </div>
                            <i class="bi bi-phone"></i>
                            <span class="small fw-semibold">Notifikasi Aplikasi</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 border rounded-pill px-3 py-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="jalurWhatsApp" checked>
                            </div>
                            <i class="bi bi-whatsapp text-success"></i>
                            <span class="small fw-semibold">WhatsApp</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 mt-2">
                        <i class="bi bi-info-circle text-muted" style="font-size: 0.75rem;"></i>
                        <span class="text-muted" style="font-size: 0.75rem;">Pesan akan dikirim melalui jalur yang dipilih secara bersamaan.</span>
                    </div>
                </div>

                {{-- Pesan Notifikasi --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold small mb-0">Pesan Notifikasi</label>
                        <span class="text-muted" style="font-size: 0.7rem;">Gunakan [Variabel] untuk personalisasi</span>
                    </div>
                    <textarea class="form-control rounded-3" id="pesanBroadcast" rows="4">Yth. Warga RW 12, Tagihan iuran bulanan Anda untuk periode [Bulan/Tahun] telah tersedia. Mohon segera melakukan pembayaran melalui portal WargaDigi. Terima kasih.</textarea>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="insertVariable('[Nama Warga]')">+ [Nama Warga]</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="insertVariable('[Bulan/Tahun]')">+ [Bulan/Tahun]</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="insertVariable('[Nominal]')">+ [Nominal]</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success px-4 rounded-3 fw-semibold"
                        onclick="bootstrap.Modal.getInstance(document.getElementById('modalBroadcastNotifikasi')).hide(); showSuccessDialog('Notifikasi Berhasil Dikirim', 'Pesan tagihan telah dikirimkan ke warga yang dipilih.');">
                    <i class="bi bi-send-fill me-1"></i> Kirim Notifikasi
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Broadcast target card selection
document.querySelectorAll('.broadcast-target-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.broadcast-target-card').forEach(c => c.classList.remove('active', 'border-success'));
        this.classList.add('active', 'border-success');
        this.querySelector('input[type=radio]').checked = true;
    });
});

// Insert variable into textarea
function insertVariable(variable) {
    const textarea = document.getElementById('pesanBroadcast');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    textarea.value = text.substring(0, start) + variable + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + variable.length;
    textarea.focus();
}
</script>
@endpush
