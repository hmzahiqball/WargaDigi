{{-- Modal Catat Transaksi Iuran --}}
<div class="modal fade" id="modalCatatTransaksi" tabindex="-1" aria-labelledby="modalCatatTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalCatatTransaksiLabel">Catat Transaksi Iuran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pt-3">
                <form id="formCatatTransaksi" action="{{ route('opkeuangan.transaksi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Keluarga (KK) --}}
                    <div class="mb-3">
                        <label for="keluarga_id" class="form-label fw-semibold small">Keluarga (No. KK) <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" id="keluarga_id" name="keluarga_id" required>
                            <option selected disabled value="">Pilih Keluarga</option>
                            @foreach($keluargaList ?? [] as $kk)
                                <option value="{{ $kk->id }}">{{ $kk->no_kk }} — {{ $kk->nik_kepala_keluarga }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- RT Tujuan --}}
                    <div class="mb-3">
                        <label for="rt_id" class="form-label fw-semibold small">RT Tujuan Setoran <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" id="rt_id" name="rt_id" required>
                            <option selected disabled value="">Pilih RT</option>
                            @foreach($rtList ?? [] as $rt)
                                <option value="{{ $rt->id }}">{{ $rt->kode_rt }} — {{ $rt->nama_rt }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Periode Bulan & Tahun --}}
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="periode_bulan" class="form-label fw-semibold small">Periode Bulan <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" id="periode_bulan" name="periode_bulan" required>
                                <option selected disabled value="">Pilih Bulan</option>
                                @php
                                    $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                @endphp
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ (int)date('m') === $i ? 'selected' : '' }}>{{ $namaBulan[$i-1] }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="periode_tahun" class="form-label fw-semibold small">Tahun <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" id="periode_tahun" name="periode_tahun"
                                   value="{{ date('Y') }}" min="2020" max="2030" required>
                        </div>
                    </div>

                    {{-- Jumlah & Metode --}}
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="jumlah" class="form-label fw-semibold small">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white rounded-start-3">Rp</span>
                                <input type="number" class="form-control rounded-end-3" id="jumlah" name="jumlah"
                                       placeholder="0" min="1" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="metode_pembayaran" class="form-label fw-semibold small">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan (Opsional)</label>
                        <textarea class="form-control rounded-3" id="keterangan" name="keterangan" rows="2"
                                  placeholder="Tambahkan catatan untuk transaksi ini..." maxlength="500"></textarea>
                    </div>

                    {{-- Upload Bukti --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Unggah Bukti (Opsional)</label>
                        <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative"
                             style="cursor: pointer;">
                            <i class="bi bi-cloud-arrow-up fs-3 text-muted d-block mb-1"></i>
                            <p class="mb-0 small">
                                <span class="text-success fw-bold">Unggah file</span> atau tarik dan lepas
                            </p>
                            <span class="text-muted" style="font-size: 0.75rem;">PNG, JPG, PDF up to 10MB</span>
                            <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                   id="bukti_pembayaran" name="bukti_pembayaran" accept=".png,.jpg,.jpeg,.pdf" style="cursor: pointer;">
                        </div>
                        <small class="text-muted d-none" id="selectedFileName"></small>
                    </div>

                    {{-- Footer di dalam form agar submit bekerja --}}
                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4 rounded-3 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Show selected file name
document.getElementById('bukti_pembayaran')?.addEventListener('change', function() {
    const label = document.getElementById('selectedFileName');
    if (this.files.length > 0) {
        label.textContent = 'File dipilih: ' + this.files[0].name;
        label.classList.remove('d-none');
    } else {
        label.classList.add('d-none');
    }
});
</script>
@endpush
