{{--
    Partial riwayat pejabat.
    Params: $riwayat (Collection Pejabat), $fmt (closure format tanggal)
    Aksi "Jadikan Aktif" hanya mengubah status (route admin.pejabat.aktifkan), tanpa menambah data baru.
--}}
@if($riwayat->isEmpty())
    <p class="text-muted small mb-0">Belum ada riwayat.</p>
@else
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead>
                <tr class="text-muted small">
                    <th>Nama</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $p)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $p->penduduk->nama_lengkap ?? '(penduduk dihapus)' }}</span>
                            <small class="d-block text-muted" style="font-family: monospace;">{{ $p->penduduk_nik }}</small>
                        </td>
                        <td class="small">{{ $fmt($p->mulai_menjabat) }}</td>
                        <td class="small">{{ $fmt($p->selesai_menjabat) }}</td>
                        <td>
                            @if($p->is_active)
                                <span class="badge bg-success">Aktif Sekarang</span>
                            @else
                                <span class="badge bg-light text-muted border">Riwayat</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @unless($p->is_active)
                                <form method="POST" action="{{ route('admin.pejabat.aktifkan', $p->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        <i class="bi bi-arrow-up-circle"></i> Jadikan Aktif
                                    </button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
