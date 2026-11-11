{{-- Modal ambil KTP dari kamera (dipakai bersama oleh RT & Warga) --}}
<div class="modal fade" id="modalKameraKtp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:0.75rem;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-camera me-2 text-success"></i>Ambil KTP dari Kamera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted small">Posisikan KTP di dalam bingkai, pastikan pencahayaan cukup dan teks terbaca jelas, lalu tekan Jepret.</p>
                <div class="position-relative d-inline-block w-100" style="background:#000;border-radius:0.5rem;overflow:hidden;">
                    <video id="videoKameraKtp" autoplay playsinline muted style="width:100%;max-height:60vh;object-fit:contain;background:#000;"></video>
                </div>
                <canvas id="canvasKameraKtp" class="d-none"></canvas>
                <div class="mt-2"><span class="small" id="statusKameraKtp"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnJepretKtp"><i class="bi bi-camera-fill me-1"></i>Jepret</button>
            </div>
        </div>
    </div>
</div>
