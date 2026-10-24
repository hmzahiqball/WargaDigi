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

<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 align-items-center">
                <h4 class="modal-title fw-bold text-dark fs-5 mb-0" id="modalTambahProdukLabel">Tambah Produk Baru</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formTambahProduk" action="{{ route('warga.umkm.produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($usahaId)
                    <input type="hidden" name="umkm_usaha_id" value="{{ $usahaId }}">
                @endif

                <div class="modal-body px-4 py-3">
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

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control rounded-3 py-2 px-3 border shadow-none" placeholder="Contoh: Kopi Kenangan Mantan" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark mb-1">Status Stok</label>
                            <select name="status_stok" class="form-select rounded-3 py-2 px-3 border shadow-none" required>
                                <option value="tersedia" selected>Tersedia</option>
                                <option value="menipis">Menipis</option>
                                <option value="habis">Habis</option>
                            </select>
                        </div>
                        <div class="col-6 position-relative">
                            <label class="form-label small fw-semibold text-dark mb-1">Kategori</label>

                            <!-- Custom Dropdown Kategori (Scrollable ~5-7 item) -->
                            <div class="dropdown" id="dropdownKategoriTambahWrapper">
                                <button class="btn border bg-white rounded-3 py-2 px-3 text-start w-100 d-flex justify-content-between align-items-center shadow-none text-dark"
                                        type="button"
                                        id="btnSelectKategoriTambah"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        style="font-size: 14px; border-color: #cbd5e1 !important; height: 42px;">
                                    <span id="selectedKategoriLabelTambah" class="text-truncate text-muted">Pilih Kategori</span>
                                    <i class="bi bi-chevron-down small text-muted ms-1 flex-shrink-0"></i>
                                </button>

                                <ul class="dropdown-menu w-100 shadow-sm border rounded-3 p-1 mt-1 dropdown-kategori-menu"
                                    aria-labelledby="btnSelectKategoriTambah"
                                    style="border-color: #e2e8f0; z-index: 1060; min-width: 100%;">
                                    <!-- Area scrollable: Dibatasi max-height ~200px (muat 5-7 item) -->
                                    <div class="kategori-scrollable-container" style="max-height: 200px; overflow-y: auto; overflow-x: hidden;">
                                        @if(isset($kategoriList) && count($kategoriList) > 0)
                                            @foreach($kategoriList as $kat)
                                                <li>
                                                    <button type="button" class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center justify-content-between option-kategori-item"
                                                            onclick="pilihKategoriTambah('{{ $kat->id }}', '{{ addslashes($kat->nama_kategori) }}')">
                                                        <span class="text-truncate">{{ $kat->nama_kategori }}</span>
                                                    </button>
                                                </li>
                                            @endforeach
                                        @else
                                            <li>
                                                <button type="button" class="dropdown-item rounded-2 py-2 px-3 small option-kategori-item"
                                                        onclick="pilihKategoriTambah('Umum', 'Umum')">
                                                    Umum
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item rounded-2 py-2 px-3 small option-kategori-item"
                                                        onclick="pilihKategoriTambah('Unggulan', 'Unggulan')">
                                                    Unggulan
                                                </button>
                                            </li>
                                        @endif
                                    </div>

                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-2 px-3 small text-success fw-bold d-flex align-items-center gap-2"
                                                onclick="tampilkanInputKategoriBaru()">
                                            <i class="bi bi-plus-circle-fill"></i>Kategori Baru
                                        </button>
                                    </li>
                                </ul>

                                <!-- Hidden input untuk nilai kategori_produk_id yang dikirim ke controller -->
                                <input type="hidden" name="kategori_produk_id" id="inputKategoriProdukId" value="">
                            </div>

                            <!-- Input Kategori Baru (Muncul saat opsi + Tambah Kategori Baru dipilih) -->
                            <div id="wrapperKategoriBaru" class="mt-2 d-none">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="nama_kategori_baru" id="inputKategoriBaru" class="form-control rounded-start-3 shadow-none border" placeholder="Nama kategori" maxlength="50" style="border-color: #cbd5e1;">
                                    <button type="button" class="btn btn-outline-secondary rounded-end-3 px-2" onclick="batalKategoriBaru()" title="Batal tambah kategori baru">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="text-muted mt-1" style="font-size: 11px;">
                                    <i class="bi bi-info-circle me-1 text-success"></i>Kategori baru akan otomatis dibuat & tersimpan.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted px-3">Rp</span>
                            <input type="text" inputmode="numeric" data-money name="harga" class="form-control border-start-0 rounded-end-3 py-2 px-3 shadow-none" placeholder="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark mb-1">Deskripsi Produk</label>
                        <textarea name="deskripsi" class="form-control rounded-3 py-2 px-3 border shadow-none" rows="3" placeholder="Jelaskan detail produk Anda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-end gap-2 px-4 py-3 border-top mt-2">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 small fw-semibold text-dark" data-bs-dismiss="modal">Batal</button>
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

// Handler Dropdown & Tambah Kategori Baru
function pilihKategoriTambah(id, nama) {
    document.getElementById('inputKategoriProdukId').value = id;
    const label = document.getElementById('selectedKategoriLabelTambah');
    label.innerText = nama;
    label.classList.remove('text-muted');
    label.classList.add('text-dark', 'fw-semibold');

    // Sembunyikan input kategori baru jika sebelumnya terbuka
    document.getElementById('wrapperKategoriBaru').classList.add('d-none');
    const inputBaru = document.getElementById('inputKategoriBaru');
    inputBaru.value = '';

    // Reset validasi border merah
    document.getElementById('btnSelectKategoriTambah').style.borderColor = '#cbd5e1';
}

function tampilkanInputKategoriBaru() {
    document.getElementById('inputKategoriProdukId').value = 'NEW_CATEGORY';
    const label = document.getElementById('selectedKategoriLabelTambah');
    label.innerText = '+ Kategori Baru';
    label.classList.remove('text-muted');
    label.classList.add('text-success', 'fw-bold');

    const wrapper = document.getElementById('wrapperKategoriBaru');
    wrapper.classList.remove('d-none');
    const inputBaru = document.getElementById('inputKategoriBaru');
    setTimeout(() => {
        inputBaru.focus();
    }, 150);

    // Reset validasi border
    document.getElementById('btnSelectKategoriTambah').style.borderColor = '#cbd5e1';
}

function batalKategoriBaru() {
    document.getElementById('wrapperKategoriBaru').classList.add('d-none');
    const inputBaru = document.getElementById('inputKategoriBaru');
    inputBaru.value = '';

    document.getElementById('inputKategoriProdukId').value = '';
    const label = document.getElementById('selectedKategoriLabelTambah');
    label.innerText = 'Pilih Kategori';
    label.classList.remove('text-dark', 'text-success', 'fw-semibold', 'fw-bold');
    label.classList.add('text-muted');
}

// Validasi Form sebelum submit
document.addEventListener('DOMContentLoaded', function() {
    const formTambah = document.getElementById('formTambahProduk');
    if (formTambah) {
        formTambah.addEventListener('submit', function(e) {
            const katId = document.getElementById('inputKategoriProdukId').value;
            const katBaru = document.getElementById('inputKategoriBaru').value.trim();
            const btnSelect = document.getElementById('btnSelectKategoriTambah');

            if (!katId) {
                e.preventDefault();
                btnSelect.style.borderColor = '#dc3545';
                btnSelect.focus();
                return false;
            }

            if (katId === 'NEW_CATEGORY' && !katBaru) {
                e.preventDefault();
                const inputBaru = document.getElementById('inputKategoriBaru');
                inputBaru.style.borderColor = '#dc3545';
                inputBaru.focus();
                return false;
            }
        });
    }

    const modalEl = document.getElementById('modalTambahProduk');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            batalKategoriBaru();
        });
    }
});
</script>
