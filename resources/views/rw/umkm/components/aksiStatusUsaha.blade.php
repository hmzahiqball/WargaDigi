{{--
    Tombol Nonaktifkan / Aktifkan usaha yang sudah disetujui (khusus Admin RW).
    Variabel: $item (UmkmUsaha), $kecil (bool, ukuran tombol kartu).
--}}
@php $kelasUkuran = ($kecil ?? false) ? 'btn-sm px-3 py-2' : 'px-3 py-2'; @endphp
@if($item->status_verifikasi === 'Approved')
    @if($item->is_active)
        <button type="button" class="btn btn-outline-danger {{ $kelasUkuran }} rounded-3 fw-semibold"
                data-bs-toggle="modal" data-bs-target="#modalNonaktifkanUmkm{{ $item->id }}"
                data-testid="btn-nonaktifkan-umkm">
            <i class="bi bi-pause-circle me-1"></i> Nonaktifkan
        </button>

        <div class="modal fade" id="modalNonaktifkanUmkm{{ $item->id }}" tabindex="-1" aria-labelledby="judulNonaktifkan{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <form action="{{ route('rw.umkm.nonaktifkan', $item->id) }}" method="POST">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="judulNonaktifkan{{ $item->id }}">Nonaktifkan Usaha</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p class="small text-muted mb-3">
                                Usaha <strong>{{ $item->nama_usaha }}</strong> tidak akan tampil di Pojok UMKM sampai diaktifkan kembali.
                                Produk tetap tersimpan dan pemilik menerima notifikasi beserta alasannya.
                            </p>
                            <label for="alasanNonaktif{{ $item->id }}" class="form-label small fw-semibold">Alasan penonaktifan</label>
                            <textarea id="alasanNonaktif{{ $item->id }}" name="catatan_verifikasi" class="form-control" rows="3" maxlength="500" required
                                      placeholder="Contoh: Informasi usaha tidak sesuai atau melanggar ketentuan RW."></textarea>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Nonaktifkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <form action="{{ route('rw.umkm.aktifkan', $item->id) }}" method="POST" class="d-inline"
              data-konfirmasi="Aktifkan kembali usaha {{ $item->nama_usaha }}? Usaha akan tampil lagi di Pojok UMKM."
              data-konfirmasi-judul="Aktifkan Usaha" data-konfirmasi-tombol="Ya, aktifkan">
            @csrf
            <button type="submit" class="btn btn-outline-success {{ $kelasUkuran }} rounded-3 fw-semibold" data-testid="btn-aktifkan-umkm">
                <i class="bi bi-play-circle me-1"></i> Aktifkan
            </button>
        </form>
    @endif
@endif
