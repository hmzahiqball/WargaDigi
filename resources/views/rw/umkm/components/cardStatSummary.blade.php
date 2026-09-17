<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #ecfdf5; color: #10b981;">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Produk Aktif</div>
                    <h4 class="fw-bold text-dark mb-0">{{ $jumlahProdukAktif ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #f1f5f9; color: #64748b;">
                    <i class="bi bi-archive fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Produk Non-Aktif</div>
                    <h4 class="fw-bold text-dark mb-0">{{ $jumlahProdukTidakAktif ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #eff6ff; color: #3b82f6;">
                    <i class="bi bi-tags fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Kategori Produk</div>
                    <h4 class="fw-bold text-dark mb-0">{{ $jumlahKategoriProduk ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #fffbeb; color: #f59e0b;">
                    <i class="bi bi-exclamation-triangle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Stok Menipis</div>
                    <h4 class="fw-bold text-dark mb-0">{{ $jumlahProdukStokMenipis ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
