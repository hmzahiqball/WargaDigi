@php
    $usahaId = $usaha->id ?? request('usaha_id') ?? null;
    $kategoriList = $kategoriProdukList ?? null;
    if (!$kategoriList) {
        if ($usahaId) {
            $kategoriList = \App\Models\KategoriProduk::where('umkm_usaha_id', $usahaId)->get();
        }
        if (!$kategoriList || $kategoriList->isEmpty()) {
            $kategoriList = \App\Models\KategoriProduk::all();
        }
    }
@endphp


<div class="modal fade" id="modalEditProduk" tabindex="-1" aria-labelledby="modalEditProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 align-items-center">
                <h4 class="modal-title fw-bold text-dark fs-5 mb-0" id="modalEditProdukLabel">Edit Produk</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditProduk" action="#" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-2">Foto Produk</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative overflow-hidden rounded-3 border bg-light" style="width: 80px; height: 80px; min-width: 80px;">
                                <img id="editProdukFotoPreview" src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop" alt="Foto Produk" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            <div>
                                <button type="button" class="btn btn-light bg-light border rounded-3 px-3 py-2 small fw-semibold text-dark d-flex align-items-center gap-2 mb-1" onclick="document.getElementById('editProdukFotoInput').click()">
                                    <i class="bi bi-upload"></i> Ganti Foto
                                </button>
                                <input type="file" name="foto_produk" id="editProdukFotoInput" class="d-none" accept="image/png, image/jpeg, image/jpg" onchange="previewEditFoto(this)">
                                <div class="text-muted" style="font-size: 11px; line-height: 1.4;">
                                    Format: JPG, PNG, max 2MB. Rasio 1:1 disarankan.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" id="editProdukNama" class="form-control rounded-3 py-2 px-3 border shadow-none" placeholder="Nama Produk" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Status Produk <span class="text-danger">*</span></label>
                            <select name="status_produk" id="editProdukStatusProduk" class="form-select rounded-3 py-2 px-3 border shadow-none" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Status Stok <span class="text-danger">*</span></label>
                            <select name="status_stok" id="editProdukStatusStok" class="form-select rounded-3 py-2 px-3 border shadow-none" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="menipis">Menipis</option>
                                <option value="habis">Habis</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_produk_id" id="editProdukKategori" class="form-select rounded-3 py-2 px-3 border shadow-none" required>
                                <option value="" disabled>Pilih Kategori</option>
                                @if(isset($kategoriList) && count($kategoriList) > 0)
                                    @foreach($kategoriList as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                @else
                                    <option value="Umum">Umum</option>
                                    <option value="Unggulan">Unggulan</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted px-3">Rp</span>
                                <input type="text" name="harga" id="editProdukHarga" class="form-control border-start-0 rounded-end-3 py-2 px-3 shadow-none" placeholder="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">Deskripsi Produk</label>
                        <textarea name="deskripsi" id="editProdukDeskripsi" class="form-control rounded-3 py-2 px-3 border shadow-none" rows="3" placeholder="Jelaskan detail produk Anda..."></textarea>
                    </div>
                </div>

                <div class="modal-footer justify-content-end gap-2 px-4 py-3 border-top mt-2">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 small fw-semibold text-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white px-4 py-2 rounded-3 small fw-semibold shadow-sm d-flex align-items-center gap-2" style="background-color: #4E8D5F; border-color: #4E8D5F;">
                        <i class="bi bi-journal-check"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEdit = document.getElementById('modalEditProduk');
    if (modalEdit) {
        modalEdit.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const form = modalEdit.querySelector('#formEditProduk');
            const action = button.getAttribute('data-action');
            const nama = button.getAttribute('data-nama');
            const statusProduk = button.getAttribute('data-status-produk') || 'Aktif';
            const statusStok = (button.getAttribute('data-stok') || 'tersedia').toLowerCase();
            const kategori = button.getAttribute('data-kategori');
            const harga = button.getAttribute('data-harga');
            const wa = button.getAttribute('data-wa');
            const deskripsi = button.getAttribute('data-deskripsi');
            const foto = button.getAttribute('data-foto');

            if (action) form.action = action;
            modalEdit.querySelector('#editProdukNama').value = nama || '';
            
            const statusProdukSelect = modalEdit.querySelector('#editProdukStatusProduk');
            if (statusProdukSelect) {
                statusProdukSelect.value = (statusProduk === 'Non-Aktif') ? 'Tidak Aktif' : statusProduk;
            }

            const statusStokSelect = modalEdit.querySelector('#editProdukStatusStok');
            if (statusStokSelect) {
                statusStokSelect.value = statusStok;
            }

            const kategoriSelect = modalEdit.querySelector('#editProdukKategori');
            if (kategoriSelect && kategori) kategoriSelect.value = kategori;

            modalEdit.querySelector('#editProdukHarga').value = harga ? Math.round(harga) : '';
            
            let cleanWa = wa ? wa.replace(/^(\+62|62|0)/, '') : '';
            const editWaInput = modalEdit.querySelector('#editProdukWa');
            if (editWaInput) {
                editWaInput.value = cleanWa;
            }

            modalEdit.querySelector('#editProdukDeskripsi').value = deskripsi || '';
            if (foto) {
                modalEdit.querySelector('#editProdukFotoPreview').src = foto;
            }
        });
    }
});

function previewEditFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('editProdukFotoPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
