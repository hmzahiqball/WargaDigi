{{-- Modal Buat Tagihan Baru --}}
<div class="modal fade" id="modalBuatTagihan" tabindex="-1" aria-labelledby="modalBuatTagihanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalBuatTagihanLabel">Buat Tagihan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                <form id="formBuatTagihan">
                    @csrf
                    {{-- Judul Tagihan --}}
                    <div class="mb-3">
                        <label for="judulTagihan" class="form-label fw-semibold small">Judul Tagihan</label>
                        <input type="text" class="form-control rounded-3" id="judulTagihan"
                               placeholder="Masukkan judul tagihan (mis: Iuran Bulanan September)">
                    </div>

                    {{-- Periode --}}
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="periodebulanTagihan" class="form-label fw-semibold small">Periode Bulan</label>
                            <select class="form-select rounded-3" id="periodebulanTagihan">
                                <option selected disabled>Pilih Bulan</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="periodeTahunTagihan" class="form-label fw-semibold small">Tahun</label>
                            <input type="number" class="form-control rounded-3" id="periodeTahunTagihan"
                                   value="{{ date('Y') }}" min="2020" max="2030">
                        </div>
                    </div>

                    {{-- Nominal --}}
                    <div class="mb-3">
                        <label for="nominalTagihan" class="form-label fw-semibold small">Nominal per KK (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white rounded-start-3">Rp</span>
                            <input type="number" class="form-control rounded-end-3" id="nominalTagihan" placeholder="0">
                        </div>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-3">
                        <label for="metodeTagihan" class="form-label fw-semibold small">Metode Pembayaran</label>
                        <select class="form-select rounded-3" id="metodeTagihan">
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-3">
                        <label for="catatanTagihan" class="form-label fw-semibold small">Catatan (Opsional)</label>
                        <textarea class="form-control rounded-3" id="catatanTagihan" rows="2"
                                  placeholder="Tambahkan catatan untuk tagihan ini..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success px-4 rounded-3 fw-semibold"
                        onclick="document.getElementById('formBuatTagihan').reset(); bootstrap.Modal.getInstance(document.getElementById('modalBuatTagihan')).hide(); showSuccessDialog('Tagihan Berhasil Dibuat', 'Tagihan iuran telah dibuat dan siap dikirimkan ke warga.');">
                    Simpan Tagihan
                </button>
            </div>
        </div>
    </div>
</div>
