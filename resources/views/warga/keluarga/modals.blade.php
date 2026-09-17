<!-- Modal Tambah Anggota -->
<div class="modal fade" id="modalTambahAnggota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Anggota Keluarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formTambahAnggota" action="{{ route('warga.keluarga.anggota.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.85rem;">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required placeholder="Contoh: Ahmad Subagja">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.85rem;">Unggah Scan Kartu Keluarga (KK)</label>
                            <div class="border border-dashed rounded-3 p-3 text-center position-relative" style="background-color: #f8fafc; border-color: #cbd5e1; border-style: dashed !important; border-width: 2px !important;">
                                <input type="file" name="file_kk" class="position-absolute w-100 h-100 opacity-0 start-0 top-0" style="cursor: pointer;" required accept=".jpg,.jpeg,.png,.pdf">
                                <i class="bi bi-cloud-arrow-up fs-4 text-primary"></i>
                                <p class="mb-0 mt-2" style="font-size: 0.8rem;">Tarik & lepas file di sini atau <span class="text-primary fw-medium">Cari File</span></p>
                                <small class="text-muted" style="font-size: 0.7rem;">Format: JPG, PNG, PDF (Max. 2MB)</small>
                            </div>
                            <small class="text-danger mt-1 d-block" style="font-size: 0.75rem;"><i class="bi bi-exclamation-triangle"></i> Wajib jika menambahkan NIK/Nama</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.85rem;">NIK (16 Digit)</label>
                            <input type="text" name="nik" class="form-control" required minlength="16" maxlength="16" placeholder="3217...">
                        </div>
                        <div class="col-md-6"></div>

                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Hubungan</label>
                            <select name="status_hubungan_keluarga" class="form-select" required>
                                <option value="Istri">Istri</option>
                                <option value="Anak">Anak</option>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Mertua">Mertua</option>
                                <option value="Famili Lain">Famili Lain</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Jenis Kelamin</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_l" value="L" required>
                                    <label class="form-check-label" for="jk_l">Laki-laki</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_p" value="P">
                                    <label class="form-check-label" for="jk_p">Perempuan</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Kewarganegaraan</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kewarganegaraan" id="wni" value="WNI" required checked>
                                    <label class="form-check-label" for="wni">WNI</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kewarganegaraan" id="wna" value="WNA">
                                    <label class="form-check-label" for="wna">WNA</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Tgl Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Agama</label>
                            <select name="agama" class="form-select" required>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" class="form-select" required>
                                <option value="Tidak/Belum Sekolah">Tidak/Belum Sekolah</option>
                                <option value="SD/Sederajat">SD/Sederajat</option>
                                <option value="SMP/Sederajat">SMP/Sederajat</option>
                                <option value="SMA/Sederajat">SMA/Sederajat</option>
                                <option value="D1/D2/D3">D1/D2/D3</option>
                                <option value="S1/D4">S1/D4</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Status Perkawinan</label>
                            <select name="status_perkawinan" class="form-select" required>
                                <option value="Belum Kawin">Belum Kawin</option>
                                <option value="Kawin">Kawin</option>
                                <option value="Cerai Hidup">Cerai Hidup</option>
                                <option value="Cerai Mati">Cerai Mati</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.85rem;">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size: 0.85rem;">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="form-control">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4 py-2 rounded-3" style="background-color: #559e66; border: none;">Ajukan Tambah Anggota</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modals Edit Anggota (Loop) -->
@foreach($anggota as $member)
<div class="modal fade" id="modalEditAnggota-{{ $member->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Data: {{ $member->nama_lengkap }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form class="formEditAnggota" action="{{ route('warga.keluarga.anggota.update', $member->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.85rem;">Status Pendidikan</label>
                        <select name="pendidikan_terakhir" class="form-select" required>
                            @php $pends = ['Tidak/Belum Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1/D2/D3', 'S1/D4', 'S2', 'S3']; @endphp
                            @foreach($pends as $p)
                                <option value="{{ $p }}" {{ $member->pendidikan_terakhir == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.85rem;">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control" value="{{ $member->pekerjaan }}" required>
                    </div>
                    <div class="mb-3 border-bottom pb-4">
                        <label class="form-label" style="font-size: 0.85rem;">Status Pernikahan</label>
                        <select name="status_perkawinan" class="form-select" required>
                            @php $statusKawin = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']; @endphp
                            @foreach($statusKawin as $s)
                                <option value="{{ $s }}" {{ $member->status_perkawinan == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label" style="font-size: 0.85rem;">Nama Lengkap (Sesuai KTP/KK) <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ $member->nama_lengkap }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.85rem;">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" class="form-control" value="{{ $member->nik }}" required minlength="16" maxlength="16">
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.85rem;">Unggah Scan Kartu Keluarga (KK)</label>
                        <div class="border border-dashed rounded-3 p-3 text-center position-relative" style="background-color: #f8fafc; border-color: #cbd5e1; border-style: dashed !important; border-width: 2px !important;">
                            <input type="file" name="file_kk" class="position-absolute w-100 h-100 opacity-0 start-0 top-0" style="cursor: pointer;" accept=".jpg,.jpeg,.png,.pdf">
                            <i class="bi bi-cloud-arrow-up fs-4 text-primary"></i>
                            <p class="mb-0 mt-2" style="font-size: 0.8rem;">Tarik & lepas file di sini atau <span class="text-primary fw-medium">Cari File</span></p>
                            <small class="text-muted" style="font-size: 0.7rem;">Format: JPG, PNG, PDF (Max. 2MB)</small>
                        </div>
                        <small class="text-danger mt-1 d-block" style="font-size: 0.75rem;"><i class="bi bi-exclamation-triangle"></i> Wajib jika mengubah NIK/Nama</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4 py-2 rounded-3" style="background-color: #ff6b6b; border: none;">Ajukan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Success -->
<div class="modal fade" id="modalSuccessMsg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow p-3 text-center">
            <div class="modal-body p-4">
                <div class="mx-auto mb-3 d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10" style="width: 60px; height: 60px;">
                    <i class="bi bi-check-lg text-success fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2">Pengajuan Terkirim</h5>
                <p class="text-muted" style="font-size: 0.85rem;" id="successMessageText">Terima kasih, Permintaan perubahan data Anda telah kami terima dan sedang diproses oleh Admin RT.</p>
                <button type="button" class="btn btn-success w-100 mt-3 py-2 rounded-3" style="background-color: #059669; border: none;" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submissions via AJAX
    const forms = document.querySelectorAll('#formTambahAnggota, .formEditAnggota');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST', // always POST for fetch, method spoofing handled by Laravel via _method
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Close current modal
                    const currentModal = bootstrap.Modal.getInstance(this.closest('.modal'));
                    if (currentModal) {
                        currentModal.hide();
                    }
                    
                    // Update text and show success modal
                    document.getElementById('successMessageText').innerText = data.message;
                    const successModal = new bootstrap.Modal(document.getElementById('modalSuccessMsg'));
                    successModal.show();
                    
                    // Optional: reset form
                    this.reset();
                } else {
                    let errorMsg = data.message || 'Terjadi kesalahan.';
                    if (data.errors) {
                        errorMsg += '\n' + Object.values(data.errors).map(e => e.join('\n')).join('\n');
                    }
                    alert(errorMsg);
                }
            })
            .catch(error => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                alert('Terjadi kesalahan pada server. Silakan coba lagi.');
                console.error(error);
            });
        });
    });
});
</script>
