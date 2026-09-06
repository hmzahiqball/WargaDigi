<!-- Component Modal Konfirmasi & Notifikasi Alert Dinamis -->

<!-- 1. Modal Konfirmasi Hapus (Dinamis Sesuai Desain) -->
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-labelledby="modalConfirmDeleteTitle" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg text-center" style="border-radius: 24px; padding: 2.25rem 2rem 2rem 2rem; background: #ffffff;">
            <!-- Icon Bulat Merah X dengan Lingkaran Mint Halus -->
            <div class="delete-icon-wrapper mx-auto mb-3" style="width: 76px; height: 76px; background-color: #E8F8EE; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="24" cy="24" r="18" stroke="#FF0000" stroke-width="3.5" fill="none" />
                    <path d="M18 18L30 30M30 18L18 30" stroke="#FF0000" stroke-width="3.5" stroke-linecap="round" />
                </svg>
            </div>

            <!-- Judul Modal -->
            <h4 class="fw-bold text-dark mb-2" id="modalConfirmDeleteTitle" style="font-size: 1.35rem; line-height: 1.35; color: #1e293b;">
                <span id="modalConfirmDeletePrefix">Hapus Produk</span> <span id="modalConfirmDeleteItemName" class="text-success fw-bold" style="color: #2E7D32 !important;">Produk ini?</span>
            </h4>

            <!-- Subtitle Modal -->
            <p class="text-muted mb-4" id="modalConfirmDeleteSubtitle" style="font-size: 0.95rem; color: #64748b; line-height: 1.5;">
                Anda yakin untuk menghapus produk ini?
            </p>

            <!-- Action Buttons: Batal & Hapus -->
            <div class="d-flex justify-content-center align-items-center gap-3 w-100">
                <button type="button" class="btn btn-batal-confirm w-50" data-bs-dismiss="modal" style="border: 1.5px solid #2E7D32; color: #2E7D32; background-color: transparent; border-radius: 12px; font-weight: 600; padding: 0.65rem 1.25rem; font-size: 1rem; transition: all 0.2s;">
                    Batal
                </button>
                <button type="button" id="btnConfirmDeleteAction" class="btn btn-hapus-confirm w-50" style="background-color: #FF0000; color: #FFFFFF; border: none; border-radius: 12px; font-weight: 600; padding: 0.65rem 1.25rem; font-size: 1rem; transition: all 0.2s;">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 2. Modal Konfirmasi Berhasil (Dinamis Sukses Usaha, Produk, Update) -->
<div class="modal fade" id="modalAlertSuccess" tabindex="-1" aria-labelledby="modalAlertSuccessTitle" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg text-center" style="border-radius: 24px; padding: 2.25rem 2rem 2rem 2rem; background: #ffffff;">
            <!-- Icon Bulat Hijau Centang dengan Lingkaran Mint Halus -->
            <div class="success-icon-wrapper mx-auto mb-3" style="width: 76px; height: 76px; background-color: #E8F8EE; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="24" cy="24" r="18" stroke="#2E7D32" stroke-width="3.5" fill="none" />
                    <path d="M16 24L22 30L32 18" stroke="#2E7D32" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <!-- Judul Sukses -->
            <h4 class="fw-bold text-dark mb-2" id="modalAlertSuccessTitle" style="font-size: 1.35rem; line-height: 1.35; color: #1e293b;">
                Berhasil!
            </h4>

            <!-- Pesan Sukses -->
            <p class="text-muted mb-4" id="modalAlertSuccessMessage" style="font-size: 0.95rem; color: #64748b; line-height: 1.5;">
                Data berhasil diperbarui.
            </p>

            <!-- Tombol Selesai -->
            <div class="d-flex justify-content-center align-items-center w-100">
                <button type="button" class="btn w-100 text-white" data-bs-dismiss="modal" id="btnAlertSuccessAction" style="background-color: #2E7D32; border: none; border-radius: 12px; font-weight: 600; padding: 0.7rem 1.5rem; font-size: 1rem; transition: all 0.2s;">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-batal-confirm:hover {
    background-color: #E8F8EE !important;
    color: #2E7D32 !important;
}
.btn-hapus-confirm:hover {
    background-color: #e00000 !important;
    box-shadow: 0 4px 14px rgba(255, 0, 0, 0.3) !important;
}
#btnAlertSuccessAction:hover {
    background-color: #246428 !important;
    box-shadow: 0 4px 14px rgba(46, 125, 50, 0.3) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let targetDeleteForm = null;
    let targetDeleteCallback = null;

    // Helper Global: Tampilkan Modal Konfirmasi Hapus
    window.showDeleteModal = function(options = {}) {
        const modalEl = document.getElementById('modalConfirmDelete');
        if (!modalEl) return;

        const prefixEl = document.getElementById('modalConfirmDeletePrefix');
        const nameEl = document.getElementById('modalConfirmDeleteItemName');
        const subtitleEl = document.getElementById('modalConfirmDeleteSubtitle');
        const btnAction = document.getElementById('btnConfirmDeleteAction');

        if (prefixEl) prefixEl.innerText = options.prefix || 'Hapus Produk';
        
        let itemName = options.itemName || options.name || '';
        if (itemName && !itemName.endsWith('?')) {
            itemName = itemName + '?';
        }
        if (nameEl) nameEl.innerText = itemName;

        if (subtitleEl) {
            subtitleEl.innerText = options.subtitle || 'Anda yakin untuk menghapus produk ini?';
        }

        if (btnAction && options.confirmButtonText) {
            btnAction.innerText = options.confirmButtonText;
        }

        targetDeleteForm = options.formId ? document.getElementById(options.formId) : null;
        targetDeleteCallback = (typeof options.onConfirm === 'function') ? options.onConfirm : null;

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    };

    // Helper Global: Tampilkan Modal Berhasil
    window.showSuccessModal = function(options = {}) {
        const modalEl = document.getElementById('modalAlertSuccess');
        if (!modalEl) return;

        const titleEl = document.getElementById('modalAlertSuccessTitle');
        const messageEl = document.getElementById('modalAlertSuccessMessage');
        const btnAction = document.getElementById('btnAlertSuccessAction');

        if (titleEl) {
            titleEl.innerHTML = options.title || 'Berhasil!';
        }
        if (messageEl) {
            messageEl.innerHTML = options.message || options.text || 'Data berhasil disimpan.';
        }
        if (btnAction && options.buttonText) {
            btnAction.innerText = options.buttonText;
        }

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    };

    // Tombol Konfirmasi Hapus diklik
    const btnActionDelete = document.getElementById('btnConfirmDeleteAction');
    if (btnActionDelete) {
        btnActionDelete.addEventListener('click', function() {
            const modalEl = document.getElementById('modalConfirmDelete');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();

            if (targetDeleteForm) {
                targetDeleteForm.submit();
            } else if (targetDeleteCallback) {
                targetDeleteCallback();
            }
        });
    }

    // Tangkap klik pada elemen dengan data-bs-target="#modalConfirmDelete"
    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('[data-bs-target="#modalConfirmDelete"], .btn-trigger-delete');
        if (!trigger) return;

        const itemName = trigger.getAttribute('data-item-name') || trigger.getAttribute('data-nama') || '';
        const formId = trigger.getAttribute('data-form-id');
        const prefix = trigger.getAttribute('data-title-prefix') || 'Hapus Produk';
        const subtitle = trigger.getAttribute('data-subtitle') || 'Anda yakin untuk menghapus produk ini?';

        window.showDeleteModal({
            itemName: itemName,
            formId: formId,
            prefix: prefix,
            subtitle: subtitle
        });
    });

    // Otomatis Munculkan Modal Berhasil jika ada flash session('success') dari Laravel
    @if(session('success'))
        setTimeout(function() {
            window.showSuccessModal({
                title: 'Berhasil!',
                message: "{{ addslashes(session('success')) }}"
            });
        }, 150);
    @endif
});
</script>
