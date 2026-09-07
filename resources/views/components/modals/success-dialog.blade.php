{{-- Reusable Success Dialog --}}
<div class="modal fade" id="modalSuccessDialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 text-center p-4">
            <div class="mb-3">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
            <h5 class="fw-bold mb-2" id="successDialogTitle">Berhasil!</h5>
            <p class="text-muted small mb-4" id="successDialogMessage">Operasi berhasil dilakukan.</p>
            <button type="button" class="btn btn-success w-100 py-2 rounded-3 fw-semibold" data-bs-dismiss="modal">
                Selesai
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global function to show success dialog with custom title and message
function showSuccessDialog(title, message) {
    document.getElementById('successDialogTitle').textContent = title || 'Berhasil!';
    document.getElementById('successDialogMessage').textContent = message || 'Operasi berhasil dilakukan.';
    new bootstrap.Modal(document.getElementById('modalSuccessDialog')).show();
}
</script>
@endpush
