{{--
    Skrip pendukung form anggota warga pada HALAMAN PENUH (non-modal, non-AJAX).
    Menyediakan: OCR KTP (ocr.baca-ktp), kamera KTP (modal bersama #modalKameraKtp),
    checkbox "sama dengan KK", dan perhitungan status warga otomatis dari alamat.

    Form tetap disubmit secara biasa (standar) agar controller dapat redirect.
--}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function filePreview(wrapper, url, mime) {
        if (!wrapper) return;
        wrapper.innerHTML = '';
        if (!url) return;
        if (mime && mime.includes('pdf')) {
            wrapper.innerHTML = '<a href="' + url + '" target="_blank" class="small"><i class="bi bi-file-earmark-pdf"></i> Lihat PDF</a>';
        } else {
            wrapper.innerHTML = '<img src="' + url + '" style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid #eee;">';
        }
    }

    function hitungStatusWargaForm(form) {
        const sel = form.querySelector('.status-warga-penduduk');
        const alamatEl = form.querySelector('.alamat-ktp');
        if (!sel || !alamatEl) return;
        const alamat = (alamatEl.value || '').toLowerCase();
        const adaTanimulya = alamat.includes('tanimulya');
        let rwCocok = false;
        const m = alamat.match(/rw[\s.:/-]*0*(\d{1,3})/);
        if (m) {
            rwCocok = m[1] === '21';
        } else {
            const m2 = alamat.match(/\d{1,3}\s*\/\s*0*(\d{1,3})/);
            if (m2) rwCocok = m2[1] === '21';
        }
        sel.value = (adaTanimulya && rwCocok) ? 'tetap' : 'tidak_tetap';
    }

    function prosesFileKtp(file, form, statusEl, previewEl, pathEl) {
        if (!file) return;
        const fd = new FormData();
        fd.append('file', file);
        if (statusEl) statusEl.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm"></span> Membaca KTP...</span>';
        fetch('{{ route('ocr.baca-ktp') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: fd
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (pathEl) pathEl.value = res.path || '';
            filePreview(previewEl, res.url, file.type);
            if (statusEl) {
                statusEl.innerHTML = res.terbaca
                    ? '<span class="text-success"><i class="bi bi-check-circle"></i> ' + (res.pesan || 'Terbaca') + '</span>'
                    : '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> ' + (res.pesan || 'Tidak terbaca, isi manual') + '</span>';
            }
            if (res.terbaca && res.data) {
                ['nik', 'nama_lengkap', 'alamat', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'status_perkawinan', 'pekerjaan', 'kewarganegaraan'].forEach(function (k) {
                    const el = form.querySelector('[name="' + k + '"]');
                    if (el && res.data[k]) el.value = res.data[k];
                });
                hitungStatusWargaForm(form);
            }
        })
        .catch(function () {
            if (statusEl) statusEl.innerHTML = '<span class="text-danger">Gagal membaca KTP. Isi manual.</span>';
        });
    }

    // ---------- Modal kamera KTP (bersama) ----------
    const modalKtpEl = document.getElementById('modalKameraKtp');
    const videoKtp = document.getElementById('videoKameraKtp');
    const canvasKtp = document.getElementById('canvasKameraKtp');
    const btnJepretKtp = document.getElementById('btnJepretKtp');
    const statusKamKtp = document.getElementById('statusKameraKtp');
    let streamKtp = null;
    let modalKtp = null;
    let targetKtp = null;

    function hentikanKameraKtp() {
        if (streamKtp) {
            streamKtp.getTracks().forEach(function (t) { t.stop(); });
            streamKtp = null;
        }
    }
    function dapatkanModalKtp() {
        if (modalKtp) return modalKtp;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            modalKtp = new bootstrap.Modal(modalKtpEl);
        }
        return modalKtp;
    }
    function bukaKameraKtp(ctx) {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            window.tampilkanPemberitahuan('Kamera tidak didukung di browser ini. Gunakan HTTPS/localhost dan browser modern.');
            return;
        }
        const m = dapatkanModalKtp();
        if (!m) {
            window.tampilkanPemberitahuan('Komponen tampilan belum siap. Muat ulang halaman lalu coba lagi.');
            return;
        }
        targetKtp = ctx;
        if (statusKamKtp) statusKamKtp.textContent = '';
        m.show();
    }

    if (modalKtpEl) {
        modalKtpEl.addEventListener('shown.bs.modal', function () {
            statusKamKtp.innerHTML = '<span class="text-muted">Mengaktifkan kamera...</span>';
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' } }, audio: false
            }).then(function (stream) {
                streamKtp = stream;
                videoKtp.srcObject = stream;
                statusKamKtp.textContent = '';
            }).catch(function () {
                statusKamKtp.innerHTML = '<span class="text-danger">Gagal mengakses kamera. Pastikan izin kamera diberikan.</span>';
            });
        });
        modalKtpEl.addEventListener('hidden.bs.modal', function () {
            hentikanKameraKtp();
            videoKtp.srcObject = null;
        });
        if (btnJepretKtp) {
            btnJepretKtp.addEventListener('click', function () {
                if (!streamKtp || !videoKtp.videoWidth) {
                    statusKamKtp.innerHTML = '<span class="text-warning">Kamera belum siap.</span>';
                    return;
                }
                if (!targetKtp) {
                    statusKamKtp.innerHTML = '<span class="text-danger">Form tidak dikenali.</span>';
                    return;
                }
                const w = videoKtp.videoWidth, h = videoKtp.videoHeight;
                canvasKtp.width = w;
                canvasKtp.height = h;
                canvasKtp.getContext('2d').drawImage(videoKtp, 0, 0, w, h);
                canvasKtp.toBlob(function (blob) {
                    if (!blob) {
                        statusKamKtp.innerHTML = '<span class="text-danger">Gagal mengambil foto.</span>';
                        return;
                    }
                    const file = new File([blob], 'ktp-kamera-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                    const t = targetKtp;
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        t.fileInput.files = dt.files;
                        if (t.btnBaca) t.btnBaca.disabled = false;
                    } catch (e) { /* sebagian browser tidak izinkan set files; tetap lanjut proses */ }
                    if (modalKtp) modalKtp.hide();
                    prosesFileKtp(file, t.form, t.statusEl, t.previewEl, t.pathEl);
                }, 'image/jpeg', 0.92);
            });
        }
    }

    // ---------- Inisialisasi tiap form anggota ----------
    document.querySelectorAll('.form-anggota-warga').forEach(function (form) {
        const fileKtp = form.querySelector('.file-ktp');
        const btnBaca = form.querySelector('.btn-baca-ktp');
        const btnKamera = form.querySelector('.btn-kamera-ktp');
        const statusEl = form.querySelector('.status-ktp');
        const previewEl = form.querySelector('.preview-ktp');
        const pathEl = form.querySelector('.ktp-path');
        const alamatEl = form.querySelector('.alamat-ktp');
        const domEl = form.querySelector('.domisili-ktp');
        const chkAlamat = form.querySelector('.chk-alamat-kk');
        const chkDom = form.querySelector('.chk-domisili-kk');
        const kkAlamat = form.getAttribute('data-kk-alamat') || '';
        const kkDomisili = form.getAttribute('data-kk-domisili') || '';

        if (fileKtp && btnBaca) {
            fileKtp.addEventListener('change', function () { btnBaca.disabled = !fileKtp.files.length; });
            btnBaca.addEventListener('click', function () {
                if (!fileKtp.files.length) return;
                prosesFileKtp(fileKtp.files[0], form, statusEl, previewEl, pathEl);
            });
        }

        if (btnKamera) {
            btnKamera.addEventListener('click', function () {
                bukaKameraKtp({ fileInput: fileKtp, btnBaca: btnBaca, statusEl: statusEl, previewEl: previewEl, pathEl: pathEl, form: form });
            });
        }

        if (alamatEl) {
            alamatEl.addEventListener('input', function () { hitungStatusWargaForm(form); });
        }

        if (chkAlamat && alamatEl) {
            chkAlamat.addEventListener('change', function () {
                if (chkAlamat.checked) {
                    alamatEl.value = kkAlamat;
                    alamatEl.readOnly = true;
                    alamatEl.classList.add('bg-light');
                } else {
                    alamatEl.readOnly = false;
                    alamatEl.classList.remove('bg-light');
                }
                hitungStatusWargaForm(form);
            });
        }

        if (chkDom && domEl) {
            chkDom.addEventListener('change', function () {
                if (chkDom.checked) {
                    domEl.value = kkDomisili;
                    domEl.readOnly = true;
                    domEl.classList.add('bg-light');
                } else {
                    domEl.readOnly = false;
                    domEl.classList.remove('bg-light');
                }
            });
        }

        // Pastikan field alamat readonly tetap terkirim (readonly tetap terkirim,
        // tapi kita amankan dengan menghapus readonly saat submit).
        form.addEventListener('submit', function () {
            if (alamatEl) alamatEl.readOnly = false;
            if (domEl) domEl.readOnly = false;
        });
    });
});
</script>
