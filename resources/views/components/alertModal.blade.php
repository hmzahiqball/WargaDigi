<!-- Component Modal Konfirmasi & Notifikasi Alert Dinamis -->


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


            <h4 class="fw-bold text-dark mb-2" id="modalConfirmDeleteTitle" style="font-size: 1.35rem; line-height: 1.35; color: #1e293b;">
                <span id="modalConfirmDeletePrefix">Hapus Produk</span> <span id="modalConfirmDeleteItemName" class="text-success fw-bold" style="color: #2E7D32 !important;">Produk ini?</span>
            </h4>


            <p class="text-muted mb-4" id="modalConfirmDeleteSubtitle" style="font-size: 0.95rem; color: #64748b; line-height: 1.5;">
                Anda yakin untuk menghapus produk ini?
            </p>


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


            <h4 class="fw-bold text-dark mb-2" id="modalAlertSuccessTitle" style="font-size: 1.35rem; line-height: 1.35; color: #1e293b;">
                Berhasil!
            </h4>


            <p class="text-muted mb-4" id="modalAlertSuccessMessage" style="font-size: 0.95rem; color: #64748b; line-height: 1.5;">
                Data berhasil diperbarui.
            </p>


            <div class="d-flex justify-content-center align-items-center w-100">
                <button type="button" class="btn w-100 text-white" data-bs-dismiss="modal" id="btnAlertSuccessAction" style="background-color: #2E7D32; border: none; border-radius: 12px; font-weight: 600; padding: 0.7rem 1.5rem; font-size: 1rem; transition: all 0.2s;">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Dialog umum: pengganti confirm() dan alert() bawaan browser -->
<div class="modal fade" id="modalDialogUmum" tabindex="-1" aria-labelledby="dialogUmumJudul" aria-hidden="true" style="z-index: 1075;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg text-center" style="border-radius: 24px; padding: 2.25rem 2rem 2rem 2rem; background: #ffffff;">
            <div id="dialogUmumIkon" class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 76px; height: 76px; border-radius: 50%;">
                <i class="bi" style="font-size: 2.4rem; line-height: 1;"></i>
            </div>
            <h4 class="fw-bold mb-2" id="dialogUmumJudul" style="font-size: 1.35rem; line-height: 1.35; color: #1e293b;">Konfirmasi</h4>
            <p class="mb-4" id="dialogUmumPesan" style="font-size: 0.95rem; color: #64748b; line-height: 1.5; white-space: pre-line;"></p>
            <div class="d-flex justify-content-center align-items-center gap-3 w-100">
                <button type="button" id="dialogUmumBatal" class="btn w-50" data-bs-dismiss="modal" style="border: 1.5px solid #2E7D32; color: #2E7D32; background: transparent; border-radius: 12px; font-weight: 600; padding: 0.65rem 1.25rem;">Batal</button>
                <button type="button" id="dialogUmumOk" class="btn text-white w-50" style="border: none; border-radius: 12px; font-weight: 600; padding: 0.65rem 1.25rem;">OK</button>
            </div>
        </div>
    </div>
</div><style>
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

    // ===== Dialog umum (pengganti confirm() dan alert() bawaan browser) =====
    const VARIAN_DIALOG = {
        konfirmasi: { ikon: 'bi-question-circle-fill', warna: '#2E7D32', latar: '#E8F8EE', tombol: '#2E7D32' },
        bahaya:     { ikon: 'bi-exclamation-triangle-fill', warna: '#dc3545', latar: '#FDECEC', tombol: '#dc3545' },
        info:       { ikon: 'bi-info-circle-fill', warna: '#0d6efd', latar: '#E7F1FF', tombol: '#0d6efd' },
    };
    let aksiDialogOk = null;

    function tampilkanDialog(opsi) {
        const modalEl = document.getElementById('modalDialogUmum');
        if (!modalEl || !window.bootstrap) { return false; }
        const v = VARIAN_DIALOG[opsi.jenis] || VARIAN_DIALOG.konfirmasi;
        const ikon = document.getElementById('dialogUmumIkon');
        ikon.style.backgroundColor = v.latar;
        ikon.querySelector('i').className = 'bi ' + v.ikon;
        ikon.querySelector('i').style.color = v.warna;
        document.getElementById('dialogUmumJudul').textContent = opsi.judul || 'Konfirmasi';
        document.getElementById('dialogUmumPesan').textContent = opsi.pesan || '';
        const ok = document.getElementById('dialogUmumOk');
        ok.textContent = opsi.tombol || 'OK';
        ok.style.backgroundColor = v.tombol;
        const batal = document.getElementById('dialogUmumBatal');
        batal.style.display = opsi.tampilBatal === false ? 'none' : '';
        batal.textContent = opsi.tombolBatal || 'Batal';
        ok.classList.toggle('w-50', opsi.tampilBatal !== false);
        ok.classList.toggle('w-100', opsi.tampilBatal === false);
        aksiDialogOk = typeof opsi.onConfirm === 'function' ? opsi.onConfirm : null;
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
        return true;
    }

    /** Konfirmasi: opsi = {judul, pesan, tombol, jenis: konfirmasi|bahaya|info, onConfirm}. */
    window.tampilkanKonfirmasi = function (opsi) {
        if (!tampilkanDialog(Object.assign({ jenis: 'konfirmasi', tampilBatal: true }, opsi || {}))) {
            // Cadangan bila komponen belum siap: tetap jalankan aksi setelah konfirmasi bawaan.
            if (window.__alertAsli && window.confirm((opsi && opsi.pesan) || 'Lanjutkan?') && opsi && opsi.onConfirm) { opsi.onConfirm(); }
        }
    };

    /** Pemberitahuan: satu tombol, tanpa aksi lanjutan. */
    window.tampilkanPemberitahuan = function (pesan, opsi) {
        const ok = tampilkanDialog(Object.assign({ jenis: 'info', judul: 'Pemberitahuan', tombol: 'Mengerti', tampilBatal: false, pesan: String(pesan) }, opsi || {}));
        if (!ok && window.__alertAsli) { window.__alertAsli(String(pesan)); }
    };

    // alert() bawaan dialihkan ke dialog agar tidak ada popup browser yang tersisa.
    window.__alertAsli = window.alert.bind(window);
    window.alert = function (pesan) { window.tampilkanPemberitahuan(pesan); };

    const tombolDialogOk = document.getElementById('dialogUmumOk');
    if (tombolDialogOk) {
        tombolDialogOk.addEventListener('click', function () {
            const aksi = aksiDialogOk;
            aksiDialogOk = null;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDialogUmum')).hide();
            if (aksi) { aksi(); }
        });
    }

    // Deklaratif: tambahkan data-konfirmasi="pesan" pada <form>, <button>, atau <a>.
    // Opsional: data-konfirmasi-judul, data-konfirmasi-tombol, data-konfirmasi-jenis (konfirmasi|bahaya|info).
    function opsiDariAtribut(el, aksi) {
        return {
            pesan: el.getAttribute('data-konfirmasi'),
            judul: el.getAttribute('data-konfirmasi-judul') || 'Konfirmasi',
            tombol: el.getAttribute('data-konfirmasi-tombol') || 'Ya, lanjutkan',
            jenis: el.getAttribute('data-konfirmasi-jenis') || 'konfirmasi',
            onConfirm: aksi,
        };
    }

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.hasAttribute || !form.hasAttribute('data-konfirmasi') || form.dataset.konfirmasiLolos === '1') { return; }
        e.preventDefault();
        const pengirim = e.submitter || null;
        window.tampilkanKonfirmasi(opsiDariAtribut(form, function () {
            form.dataset.konfirmasiLolos = '1';
            if (form.requestSubmit) { form.requestSubmit(pengirim || undefined); } else { form.submit(); }
            delete form.dataset.konfirmasiLolos;
        }));
    }, true);

    document.addEventListener('click', function (e) {
        const el = e.target.closest ? e.target.closest('[data-konfirmasi]') : null;
        if (!el || el.tagName === 'FORM' || el.dataset.konfirmasiLolos === '1') { return; }
        e.preventDefault();
        e.stopPropagation();
        window.tampilkanKonfirmasi(opsiDariAtribut(el, function () {
            if (el.tagName === 'A' && el.href) { window.location.href = el.href; return; }
            el.dataset.konfirmasiLolos = '1';
            el.click();
            delete el.dataset.konfirmasiLolos;
        }));
    }, true);
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

        if (btnAction) {
            btnAction.disabled = false;
            btnAction.innerText = options.confirmButtonText || 'Hapus';
        }

        targetDeleteForm = options.formId ? document.getElementById(options.formId) : null;
        targetDeleteCallback = (typeof options.onConfirm === 'function') ? options.onConfirm : null;

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    };

    // Helper Global: Tampilkan Modal Berhasil (Aman dari DOM XSS)
    window.showSuccessModal = function(options = {}) {
        const modalEl = document.getElementById('modalAlertSuccess');
        if (!modalEl) return;

        const titleEl = document.getElementById('modalAlertSuccessTitle');
        const messageEl = document.getElementById('modalAlertSuccessMessage');
        const btnAction = document.getElementById('btnAlertSuccessAction');

        if (titleEl) {
            titleEl.textContent = options.title || 'Berhasil!';
        }
        if (messageEl) {
            messageEl.textContent = options.message || options.text || 'Data berhasil disimpan.';
        }
        if (btnAction && options.buttonText) {
            btnAction.innerText = options.buttonText;
        }

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    };

    // Tombol Konfirmasi Hapus diklik (Anti Double-Click)
    const btnActionDelete = document.getElementById('btnConfirmDeleteAction');
    if (btnActionDelete) {
        btnActionDelete.addEventListener('click', function() {
            if (btnActionDelete.disabled) return;
            btnActionDelete.disabled = true;
            btnActionDelete.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghapus...';

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

    @if(session('success'))
        setTimeout(function() {
            window.showSuccessModal({
                title: 'Berhasil!',
                message: {!! json_encode(session('success')) !!}
            });
        }, 150);
    @endif

    {{-- Satu-satunya tempat pesan flash sesi ditampilkan (dialog). Halaman tidak perlu menampilkan alert/toast sendiri. --}}
    @if(session('error'))
        setTimeout(function() {
            window.tampilkanPemberitahuan({!! json_encode(session('error')) !!}, { jenis: 'bahaya', judul: 'Terjadi Kesalahan' });
        }, 150);
    @endif

    @if(session('warning'))
        setTimeout(function() {
            window.tampilkanPemberitahuan({!! json_encode(session('warning')) !!}, { jenis: 'bahaya', judul: 'Perhatian' });
        }, 150);
    @endif
});
</script>
