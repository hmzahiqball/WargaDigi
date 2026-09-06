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

<!-- Modal Tambah Produk Baru -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 align-items-center">
                <h4 class="modal-title fw-bold text-dark fs-5 mb-0" id="modalTambahProdukLabel">Tambah Produk Baru</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body Form -->
            <form action="{{ route('warga.umkm.produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($usahaId)
                    <input type="hidden" name="umkm_usaha_id" value="{{ $usahaId }}">
                @endif

                <div class="modal-body px-4 py-3">
                    <!-- Unggah Foto Produk -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-2">Unggah Foto Produk</label>
                        <div class="upload-drag-drop-box text-center p-4 rounded-3 border-2 border-dashed position-relative" 
                             style="border: 2px dashed #cbd5e1; background-color: #fafbfa; cursor: pointer;"
                             onclick="document.getElementById('inputFotoProdukTambah').click()"
                             ondragover="event.preventDefault(); this.style.borderColor='#4E8D5F';"
                             ondragleave="this.style.borderColor='#cbd5e1';"
                             ondrop="handleDropFotoTambah(event)">
                            
                            <div id="previewWrapperTambah" class="d-none mb-2">
                                <img id="imgPreviewTambah" src="" alt="Preview" class="rounded-3 shadow-sm" style="max-height: 120px; max-width: 100%; object-fit: cover;">
                            </div>

                            <div id="placeholderTambah">
                                <div class="mb-2 text-dark">
                                    <i class="bi bi-cloud-arrow-up fs-1" style="color: #334155;"></i>
                                </div>
                                <div class="small text-muted mb-1">
                                    Tarik dan lepas gambar di sini, atau <span class="fw-semibold" style="color: #2e7d32;">pilih file</span>
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    Maksimal 5MB, format JPG/PNG
                                </div>
                            </div>

                            <input type="file" name="foto_produk" id="inputFotoProdukTambah" class="d-none" accept="image/png, image/jpeg, image/jpg" onchange="previewFotoTambah(this)" required>
                        </div>
                    </div>

                    <!-- Nama Produk -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control rounded-3 py-2 px-3 border shadow-none" placeholder="Contoh: Kopi Kenangan Mantan" required>
                    </div>

                    <!-- Status Stok & Kategori -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Status Stok</label>
                            <select name="status_stok" class="form-select rounded-3 py-2 px-3 border shadow-none" required>
                                <option value="tersedia" selected>Tersedia</option>
                                <option value="menipis">Menipis</option>
                                <option value="habis">Habis</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Kategori</label>
                            <select name="kategori_produk_id" class="form-select rounded-3 py-2 px-3 border shadow-none text-dark" required>
                                <option value="" selected disabled>Pilih Kategori</option>
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
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted px-3">Rp</span>
                            <input type="number" name="harga" class="form-control border-start-0 rounded-end-3 py-2 px-3 shadow-none" value="0" min="0" required>
                        </div>
                    </div>

                    <!-- Deskripsi Produk -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">Deskripsi Produk</label>
                        <textarea name="deskripsi" class="form-control rounded-3 py-2 px-3 border shadow-none" rows="3" placeholder="Jelaskan detail produk Anda..."></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-end gap-2 px-4 py-3 border-top mt-2">
                    <button type="button" class="btn btn-white bg-white border px-4 py-2 rounded-3 small fw-semibold text-dark shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white px-4 py-2 rounded-3 small fw-semibold shadow-sm" style="background-color: #4E8D5F; border-color: #4E8D5F;">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewFotoTambah(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreviewTambah').src = e.target.result;
            document.getElementById('previewWrapperTambah').classList.remove('d-none');
            document.getElementById('placeholderTambah').classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function handleDropFotoTambah(e) {
    e.preventDefault();
    e.currentTarget.style.borderColor = '#cbd5e1';
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        const fileInput = document.getElementById('inputFotoProdukTambah');
        fileInput.files = e.dataTransfer.files;
        previewFotoTambah(fileInput);
    }
}
</script>
