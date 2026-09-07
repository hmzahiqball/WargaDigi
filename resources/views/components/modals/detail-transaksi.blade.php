{{-- Modal Detail Transaksi --}}
<div class="modal fade" id="modalDetailTransaksi" tabindex="-1" aria-labelledby="modalDetailTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="modalDetailTransaksiLabel">Detail Transaksi</h5>
                    <span class="text-muted small" id="detailTanggal">-</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                {{-- Status Badge --}}
                <div class="mb-3">
                    <span class="badge rounded-pill px-3 py-2 fs-6" id="detailStatusBadge">-</span>
                </div>

                {{-- Bukti Gambar (jika ada) --}}
                <div class="mb-3 text-center d-none" id="detailBuktiContainer">
                    <img src="" alt="Bukti Transaksi" class="img-fluid rounded-3 shadow-sm" id="detailBuktiImage" style="max-height: 180px;">
                </div>

                {{-- Detail Fields --}}
                <div class="bg-light rounded-3 p-3 mb-3">
                    <div class="row g-3">
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">PERIHAL</span>
                            <span class="fw-bold small" id="detailPerihal">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">KATEGORI</span>
                            <span class="fw-bold small" id="detailKategori">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">NOMINAL</span>
                            <span class="fw-bold small text-success" id="detailNominal">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">METODE PEMBAYARAN</span>
                            <span class="fw-bold small" id="detailMetode">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">OPERATOR</span>
                            <span class="fw-bold small" id="detailOperator">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">TANGGAL VERIFIKASI</span>
                            <span class="fw-bold small" id="detailVerifiedAt">-</span>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mb-2">
                    <span class="text-muted d-block mb-1" style="font-size: 0.7rem;">CATATAN</span>
                    <p class="small mb-0" id="detailCatatan">-</p>
                </div>
            </div>

            {{-- Footer — buttons tergantung role --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>

                @php $role = Auth::user()?->role ?? ''; @endphp

                {{-- Cetak Kuitansi: hanya untuk transaksi Verified/Approved --}}
                @if(in_array($role, ['Op. Keuangan RT', 'Op. Keuangan RW', 'DKM']))
                    <button type="button" class="btn btn-success px-4 rounded-3 fw-semibold d-none" id="btnCetakKuitansiDetail">
                        <i class="bi bi-printer me-1"></i> Cetak Kuitansi
                    </button>
                @endif

                {{-- Approve/Reject: Ketua RT --}}
                @if($role === 'Ketua RT')
                    <button type="button" class="btn btn-outline-danger px-3 rounded-3 d-none" id="btnTolakDetail">
                        <i class="bi bi-x-circle me-1"></i> Tolak
                    </button>
                    <button type="button" class="btn btn-success px-3 rounded-3 fw-semibold d-none" id="btnSetujuiDetail"
                            onclick="bootstrap.Modal.getInstance(document.getElementById('modalDetailTransaksi')).hide(); showSuccessDialog('Transaksi Disetujui', 'Transaksi telah berhasil disetujui oleh Ketua RT.');">
                        <i class="bi bi-check-circle me-1"></i> Setujui
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Populate detail modal with transaction data
function showDetailTransaksi(data) {
    document.getElementById('detailTanggal').textContent = data.tanggal || '-';
    document.getElementById('detailPerihal').textContent = data.perihal || '-';
    document.getElementById('detailKategori').textContent = data.kategori || '-';
    document.getElementById('detailNominal').textContent = data.jumlah_formatted || '-';
    document.getElementById('detailMetode').textContent = data.metode || 'Tunai';
    document.getElementById('detailOperator').textContent = data.operator || 'Operator';
    document.getElementById('detailVerifiedAt').textContent = data.verified_at || '-';
    document.getElementById('detailCatatan').textContent = data.keterangan || 'Tidak ada catatan.';

    // Status badge
    const badge = document.getElementById('detailStatusBadge');
    badge.textContent = data.status;
    badge.className = 'badge rounded-pill px-3 py-2 fs-6 ' + getStatusClass(data.status);

    // Bukti image
    const buktiContainer = document.getElementById('detailBuktiContainer');
    if (data.bukti) {
        document.getElementById('detailBuktiImage').src = data.bukti;
        buktiContainer.classList.remove('d-none');
    } else {
        buktiContainer.classList.add('d-none');
    }

    // Show/hide action buttons based on status
    const cetakBtn = document.getElementById('btnCetakKuitansiDetail');
    const tolakBtn = document.getElementById('btnTolakDetail');
    const setujuiBtn = document.getElementById('btnSetujuiDetail');

    if (cetakBtn) {
        cetakBtn.classList.toggle('d-none', !['Verified', 'Terverifikasi', 'Approved'].includes(data.status));
    }
    if (tolakBtn && setujuiBtn) {
        const showApproval = ['Pending', 'Diajukan'].includes(data.status);
        tolakBtn.classList.toggle('d-none', !showApproval);
        setujuiBtn.classList.toggle('d-none', !showApproval);
    }

    new bootstrap.Modal(document.getElementById('modalDetailTransaksi')).show();
}

function getStatusClass(status) {
    const map = {
        'Draft': 'bg-warning bg-opacity-10 text-warning',
        'Pending': 'bg-info bg-opacity-10 text-info',
        'Verified': 'bg-success bg-opacity-10 text-success',
        'Terverifikasi': 'bg-success bg-opacity-10 text-success',
        'Approved': 'bg-primary bg-opacity-10 text-primary',
        'Ditolak': 'bg-danger bg-opacity-10 text-danger',
    };
    return map[status] || 'bg-secondary bg-opacity-10 text-secondary';
}
</script>
@endpush
