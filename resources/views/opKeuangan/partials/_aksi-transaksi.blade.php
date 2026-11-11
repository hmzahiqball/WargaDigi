{{-- Menu aksi transaksi (dipakai tampilan desktop & mobile) --}}
<div class="dropdown d-inline-block">
    <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper-config='{"strategy":"fixed"}'>
        <i class="bi bi-three-dots-vertical text-muted"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2">
        @if($tx->bukti_file)
        <li>
            <a class="dropdown-item text-secondary py-2" href="{{ asset('storage/' . $tx->bukti_file) }}" target="_blank">
                <i class="bi bi-file-earmark-arrow-down me-2 text-primary"></i> Lihat Bukti
            </a>
        </li>
        <li><hr class="dropdown-divider my-1"></li>
        @endif

        @if(!empty($tx->dari_iuran))
        <li>
            <span class="dropdown-item-text py-2 small text-muted">
                <i class="bi bi-calendar-check me-2 text-success"></i>Transaksi Iuran Bulanan
                <span class="d-block ms-4" style="font-size:11px;">Kelola di menu <strong>Iuran Bulanan</strong>.</span>
            </span>
        </li>
        @elseif(!empty($tx->is_locked))
        <li>
            <button class="dropdown-item py-2 text-muted disabled" disabled title="Terkunci: Laporan periode ini sudah diajukan/disetujui">
                <i class="bi bi-lock-fill me-2 text-muted"></i> Edit <span class="badge bg-light text-muted border ms-1">Terkunci</span>
            </button>
        </li>
        <li>
            <button class="dropdown-item py-2 text-muted disabled" disabled title="Terkunci: Laporan periode ini sudah diajukan/disetujui">
                <i class="bi bi-lock-fill me-2 text-muted"></i> Hapus <span class="badge bg-light text-muted border ms-1">Terkunci</span>
            </button>
        </li>
        @else
        <li>
            <button class="dropdown-item py-2" onclick="openEditModal('{{ $tx->id }}', '{{ $tx->tipe }}', '{{ addslashes($tx->judul) }}', '{{ $tx->kategori_id }}', '{{ $tx->tanggal->format('Y-m-d') }}', '{{ $tx->jumlah }}', '{{ addslashes($tx->deskripsi ?? '') }}', '{{ $tx->bukti_file ? asset('storage/' . $tx->bukti_file) : '' }}')">
                <i class="bi bi-pencil me-2 text-warning"></i> Edit
            </button>
        </li>
        <li>
            <form action="{{ route('opkeuangan.transaksi.destroy', $tx->id) }}" method="POST" data-konfirmasi="Apakah Anda yakin ingin menghapus transaksi ini?" data-konfirmasi-judul="Hapus Transaksi" data-konfirmasi-tombol="Hapus" data-konfirmasi-jenis="bahaya">
                @csrf
                @method('DELETE')
                <button type="submit" class="dropdown-item text-danger py-2">
                    <i class="bi bi-trash me-2"></i> Hapus
                </button>
            </form>
        </li>
        @endif
    </ul>
</div>
