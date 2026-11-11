<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekeningBendahara;
use App\Models\Tagihan;
use App\Models\PembayaranTagihan;
use App\Models\MasterRt;
use App\Models\Keluarga;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Concerns\NormalizesMoney;

class TagihanController extends Controller
{
    use NormalizesMoney;

    private function getRoleFilterData()
    {
        $user = Auth::user();
        $unit = null;
        $rt_id = null;

        $role = trim($user->activeRole() ?? '');

        if (in_array($role, ['Op Keuangan RT', 'Op. Keuangan RT', 'Bendahara RT'])) {
            $unit = 'RT';
            $rt_id = $user->rt_id;
            if (!$rt_id) {
                $penduduk = \App\Models\Penduduk::with('keluarga')->where('nik', $user->nik)->first();
                if ($penduduk && $penduduk->keluarga) {
                    $rt_id = $penduduk->keluarga->rt_id;
                }
                if (!$rt_id) {
                    $rt01 = MasterRt::where('kode_rt', '01')->first() ?? MasterRt::first();
                    $rt_id = $rt01 ? $rt01->id : null;
                }
            }
        } elseif (in_array($role, ['Op Keuangan RW', 'Op. Keuangan RW', 'Bendahara RW', 'Admin RW', 'Ketua RW'])) {
            $unit = 'RW';
            $rt_id = null;
        } elseif (in_array($role, ['DKM', 'Bendahara DKM', 'Op Keuangan DKM', 'Op. Keuangan DKM'])) {
            $unit = 'DKM';
            $rt_id = null;
        }

        return compact('unit', 'rt_id');
    }

    /** Apakah akun (unit, rt_id) berwenang atas tagihan ini. */
    private function berwenang(Tagihan $t, ?string $unit, ?string $rtId): bool
    {
        if ($t->unit === $unit && ($unit !== 'RT' || $t->rt_id === $rtId)) {
            return true;
        }

        // RT mengelola warganya untuk Iuran Lainnya RW yang dikumpulkan lewat RT.
        return $unit === 'RT' && $t->unit === 'RW' && (bool) $t->via_rt;
    }

    /**
     * Aksi per pembayaran (verifikasi, catat, tolak, ingatkan).
     * Iuran RW yang dikumpulkan lewat RT: hanya RT pemilik KK; RW tidak memverifikasi per warga.
     */
    private function bolehKelolaPembayaran(PembayaranTagihan $p, ?string $unit, ?string $rtId): bool
    {
        $t = $p->tagihan;
        if ($t->unit === 'RW' && $t->via_rt) {
            return $unit === 'RT' && $rtId && optional($p->keluarga)->rt_id === $rtId;
        }

        return $this->berwenang($t, $unit, $rtId);
    }

    public function rekeningIndex()
    {
        extract($this->getRoleFilterData());
        if (!$unit) {
            return redirect()->route('opkeuangan.dashboard')->with('error', 'Unit keuangan tidak terdefinisi.');
        }

        $query = RekeningBendahara::where('unit', $unit);
        if ($unit === 'RT') {
            $query->where('rt_id', $rt_id);
        } else {
            $query->whereNull('rt_id');
        }
        $rekening = $query->first();

        // Pastikan file QRIS tersinkronisasi ke public/storage untuk akses langsung browser
        if ($rekening && $rekening->qris_file) {
            $src = storage_path('app/public/' . $rekening->qris_file);
            $dst = public_path('storage/' . $rekening->qris_file);
            if (file_exists($src) && !file_exists($dst)) {
                $dstDir = dirname($dst);
                if (!is_dir($dstDir)) {
                    @mkdir($dstDir, 0755, true);
                }
                @copy($src, $dst);
            }
        }

        return view('opKeuangan.rekening', compact('rekening', 'unit', 'rt_id'));
    }

    public function rekeningSave(Request $request)
    {
        extract($this->getRoleFilterData());
        if (!$unit) {
            return redirect()->back()->with('error', 'Unit keuangan tidak terdefinisi untuk akun ini.');
        }

        $request->validate([
            'bank' => 'required|string|max:50',
            'no_rek' => 'required|string|max:50',
            'nama_rek' => 'required|string|max:100',
        ], [
            'bank.required' => 'Nama bank wajib diisi.',
            'no_rek.required' => 'Nomor rekening wajib diisi.',
            'nama_rek.required' => 'Atas nama rekening wajib diisi.',
        ]);

        $query = RekeningBendahara::where('unit', $unit);
        if ($unit === 'RT') {
            $query->where('rt_id', $rt_id);
        } else {
            $query->whereNull('rt_id');
        }
        $rekening = $query->first();

        $qrisPath = $rekening ? $rekening->qris_file : null;

        // Prioritas 1: Base64 payload (anti-gagal jika PHP temp directory bermasalah)
        if ($request->filled('qris_base64') && str_starts_with($request->qris_base64, 'data:image')) {
            $savedBase64 = $this->saveBase64Image($request->qris_base64, 'rekening');
            if ($savedBase64) {
                $qrisPath = $savedBase64;
            }
        }
        // Prioritas 2: Multipart file upload standar
        elseif ($request->hasFile('qris_file')) {
            $file = $request->file('qris_file');
            if ($file->isValid()) {
                $qrisPath = $file->store('rekening', 'public');
                // Sinkronkan juga langsung ke public/storage
                try {
                    $dst = public_path('storage/' . $qrisPath);
                    $dstDir = dirname($dst);
                    if (!is_dir($dstDir)) {
                        @mkdir($dstDir, 0755, true);
                    }
                    @copy(storage_path('app/public/' . $qrisPath), $dst);
                } catch (\Throwable $e) {}
            } else {
                return redirect()->back()->with('error', 'Gagal memproses file QRIS: ' . $file->getErrorMessage())->withInput();
            }
        }

        if ($rekening) {
            $rekening->update([
                'bank' => $request->bank,
                'no_rek' => $request->no_rek,
                'nama_rek' => $request->nama_rek,
                'qris_file' => $qrisPath,
            ]);
        } else {
            RekeningBendahara::create([
                'id' => Str::uuid(),
                'unit' => $unit,
                'rt_id' => $unit === 'RT' ? $rt_id : null,
                'bank' => $request->bank,
                'no_rek' => $request->no_rek,
                'nama_rek' => $request->nama_rek,
                'qris_file' => $qrisPath,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan rekening berhasil disimpan.');
    }

    public function iuranIndex()
    {
        extract($this->getRoleFilterData());

        // RT: tagihan RT sendiri + tagihan RW yang dikumpulkan lewat RT. Hitungan hanya untuk KK di RT-nya.
        $scopeKk = fn ($q) => $unit === 'RT'
            ? $q->whereHas('keluarga', fn ($k) => $k->where('rt_id', $rt_id))
            : $q;

        $query = Tagihan::withCount([
            'pembayaran as paid_count' => fn ($q) => $scopeKk($q->where('status', 'Paid')),
            'pembayaran as total_kk'   => fn ($q) => $scopeKk($q),
        ])->where(function ($outer) use ($unit, $rt_id) {
            $outer->where(function ($q) use ($unit, $rt_id) {
                $q->where('unit', $unit);
                if ($unit === 'RT') {
                    $q->where('rt_id', $rt_id);
                }
            });
            if ($unit === 'RT') {
                $outer->orWhere(fn ($q) => $q->where('unit', 'RW')->where('via_rt', true));
            }
        });

        $tagihanList = $query->orderBy('created_at', 'desc')->paginate(10);

        // Kategori pemasukan untuk pilihan saat membuat Iuran Lainnya.
        $kategoriPemasukan = \App\Models\KategoriTransaksi::where('tipe', 'pemasukan')->where('aktif', true)
            ->untukUnit($unit, $rt_id)->orderBy('urutan')->get();

        return view('opKeuangan.iuran.index', compact('tagihanList', 'unit', 'rt_id', 'kategoriPemasukan'));
    }
    public function iuranDetail($id)
    {
        extract($this->getRoleFilterData());
        $tagihan = Tagihan::with('pembayaran.keluarga.kepalaKeluarga')->findOrFail($id);

        // Ensure access control
        abort_unless($this->berwenang($tagihan, $unit, $rt_id), 403);

        // Auto-sync seluruh bukti pembayaran warga ke public/storage agar langsung tampil di browser
        foreach ($tagihan->pembayaran as $p) {
            if ($p->bukti_file) {
                $src = storage_path('app/public/' . $p->bukti_file);
                $dst = public_path('storage/' . $p->bukti_file);
                if (file_exists($src) && !file_exists($dst)) {
                    $dstDir = dirname($dst);
                    if (!is_dir($dstDir)) {
                        @mkdir($dstDir, 0755, true);
                    }
                    @copy($src, $dst);
                }
            }
        }

        // RT hanya melihat KK di RT-nya (relevan untuk Iuran RW yang dikumpulkan lewat RT).
        if ($unit === 'RT') {
            $tagihan->setRelation('pembayaran', $tagihan->pembayaran
                ->filter(fn ($p) => optional($p->keluarga)->rt_id === $rt_id)->values());
        }

        // RW melihat ringkasan per RT (bukan daftar per warga) untuk iuran yang dikumpulkan lewat RT.
        $ringkasanRt = ($unit === 'RW' && $tagihan->via_rt)
            ? \App\Services\SetoranRtService::rekapTagihanSemuaRt($tagihan)
            : null;

        return view('opKeuangan.iuran.detail', compact('tagihan', 'unit', 'ringkasanRt'));
    }

    public function tagihanStore(Request $request)
    {
        extract($this->getRoleFilterData());
        if (!$unit) {
            return redirect()->back()->with('error', 'Unit keuangan tidak terdefinisi untuk akun ini.')->withInput();
        }

        // Normalisasi nominal dari number formatter sebelum validasi.
        $request->merge(['nominal' => $this->normalizeMoney($request->input('nominal'))]);

        $request->validate([
            'judul' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:0',
            'kategori_id' => 'nullable|uuid',
            'deskripsi' => 'nullable|string',
            'tenggat' => 'nullable|date',
            'nominal_fix' => 'nullable|boolean',
            'via_rt' => 'nullable|boolean',
            'reminder_tanggal' => 'nullable|array',
            'reminder_tanggal.*' => 'integer|min:1|max:31',
        ], [
            'nominal.required' => 'Nominal wajib diisi sebagai nominal minimal iuran.',
        ]);

        // Iuran Lainnya selalu insidental (sekali bayar); jalur "rutin" dihapus.
        $jenis = 'insidental';

        // IR44: Tentukan daftar keluarga penerima SEBELUM membuat tagihan,
        // agar tagihan tanpa penerima tidak dibuat (hindari "target 0 warga").
        if ($unit === 'RT') {
            if (!$rt_id) {
                return redirect()->back()->with('error', 'Tagihan gagal dibuat: RT untuk akun bendahara ini tidak dapat ditentukan.')->withInput();
            }
            $keluargas = Keluarga::where('rt_id', $rt_id)->get();
        } else {
            $keluargas = Keluarga::all();
        }

        if ($keluargas->isEmpty()) {
            return redirect()->back()->with('error', 'Tagihan gagal dibuat: tidak ada warga/keluarga pada unit ini untuk ditagih.')->withInput();
        }

        $tagihan = Tagihan::create([
            'id' => Str::uuid(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'jenis' => $jenis,
            'nominal' => $request->nominal,
            'kategori_id' => $request->input('kategori_id'),
            'nominal_fix' => $request->boolean('nominal_fix'),
            // Hanya iuran buatan RW yang dapat dikumpulkan lewat RT.
            'via_rt' => $unit === 'RW' && $request->boolean('via_rt'),
            'periode_bulan' => date('n'),
            'periode_tahun' => date('Y'),
            'unit' => $unit,
            'rt_id' => $unit === 'RT' ? $rt_id : null,
            'pembuat_id' => Auth::id(),
            'status' => 'Active',
            'tenggat' => $request->tenggat,
            'reminder_tanggal' => $request->input('reminder_tanggal', []),
        ]);

        $data = [];
        foreach ($keluargas as $kk) {
            $data[] = [
                'id' => Str::uuid(),
                'tagihan_id' => $tagihan->id,
                'keluarga_id' => $kk->id,
                'status' => 'Unpaid',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PembayaranTagihan::insert($data);

        $labelJenis = $jenis === 'rutin' ? 'rutin' : 'insidental';

        return redirect()->back()->with('success', "Tagihan {$labelJenis} berhasil dibuat dan disebar ke {$keluargas->count()} warga.");
    }

    public function approvePembayaran($id)
    {
        extract($this->getRoleFilterData());
        $pembayaran = PembayaranTagihan::with(['tagihan', 'keluarga'])->findOrFail($id);
        $tagihan = $pembayaran->tagihan;

        abort_unless($this->bolehKelolaPembayaran($pembayaran, $unit, $rt_id), 403, 'Pembayaran ini bukan wewenang akun Anda.');

        // Nominal aktual: yang diajukan warga bila ada, selain itu nominal tagihan.
        $nominal = $pembayaran->nominal ?: $tagihan->nominal;

        $transaksi = $this->catatKasTagihan($tagihan, $pembayaran, $nominal);

        $pembayaran->update([
            'status' => 'Paid',
            'nominal' => $nominal,
            'transaksi_id' => $transaksi->id,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil disetujui dan dicatat ke keuangan.');
    }

    /**
     * Ceklis pembayaran oleh bendahara (tanpa alur pengajuan warga).
     * Nominal mengikuti tagihan bila fix; selain itu dapat diisi (minimal nominal tagihan).
     */
    public function ceklisBayar(Request $request, $id)
    {
        extract($this->getRoleFilterData());
        $pembayaran = PembayaranTagihan::with(['tagihan', 'keluarga.kepalaKeluarga'])->findOrFail($id);
        $tagihan = $pembayaran->tagihan;

        abort_unless($this->bolehKelolaPembayaran($pembayaran, $unit, $rt_id), 403, 'Pembayaran ini bukan wewenang akun Anda.');
        if ($pembayaran->status === 'Paid') {
            return back()->with('error', 'Pembayaran ini sudah lunas.');
        }

        $request->merge(['nominal' => $this->normalizeMoney($request->input('nominal'))]);
        $request->validate([
            'nominal'       => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date|before_or_equal:today',
            'metode'        => 'required|in:Transfer,QRIS,Cash',
            'bukti_file'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Bila nominal fix, paksa ke nominal tagihan; bila tidak, minimal nominal tagihan.
        $nominal = (int) $request->nominal;
        if ($tagihan->nominal_fix) {
            $nominal = (int) $tagihan->nominal;
        } elseif ($nominal < (int) $tagihan->nominal) {
            return back()->with('error', 'Nominal pembayaran tidak boleh kurang dari nominal minimal (Rp ' . number_format($tagihan->nominal, 0, ',', '.') . ').')->withInput();
        }

        $buktiPath = $pembayaran->bukti_file;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('bukti_pembayaran', 'public');
        }

        $pembayaran->fill([
            'nominal'       => $nominal,
            'tanggal_bayar' => $request->tanggal_bayar,
            'metode'        => $request->metode,
            'bukti_file'    => $buktiPath,
        ]);

        $transaksi = $this->catatKasTagihan($tagihan, $pembayaran, $nominal, $request->tanggal_bayar);

        $pembayaran->fill([
            'status'       => 'Paid',
            'transaksi_id' => $transaksi->id,
            'approved_by'  => Auth::id(),
            'approved_at'  => now(),
        ])->save();

        return back()->with('success', 'Pembayaran dicatat dan masuk ke kas.');
    }

    /** Buat transaksi kas pemasukan untuk satu pembayaran tagihan (Iuran Lainnya). */
    private function catatKasTagihan(Tagihan $tagihan, PembayaranTagihan $pembayaran, int $nominal, ?string $tanggal = null): TransaksiKeuangan
    {
        $kategori = $tagihan->kategori;
        $viaRt = $tagihan->unit === 'RW' && $tagihan->via_rt;
        $namaKk = optional(optional($pembayaran->keluarga)->kepalaKeluarga)->nama_lengkap
            ?: optional($pembayaran->keluarga)->no_kk;

        return TransaksiKeuangan::create([
            'id' => Str::uuid(),
            'kode_transaksi' => 'IN-TAG-' . strtoupper(Str::random(6)),
            'tipe' => 'pemasukan',
            'kategori' => $kategori?->nama ?? 'Iuran Lainnya',
            'kategori_id' => $kategori?->id,
            'judul' => $tagihan->judul . ($namaKk ? ' - ' . $namaKk : ''),
            'deskripsi' => 'Pembayaran ' . $tagihan->judul . ' via ' . ($pembayaran->metode ?? 'Tunai') . ($viaRt ? ' (titipan iuran RW, disetor kolektif ke RW).' : '.'),
            'jumlah' => $nominal,
            'tanggal' => $tanggal ?? now()->toDateString(),
            'bukti_file' => $pembayaran->bukti_file,
            'status' => 'Verified',
            // Iuran RW via RT: uang diterima RT (titipan), dicatat di kas RT pemilik KK lalu disetor ke RW.
            'unit_sumber' => $viaRt ? 'RT' : $tagihan->unit,
            'rt_id' => $viaRt ? optional($pembayaran->keluarga)->rt_id : $tagihan->rt_id,
            'dicatat_oleh' => Auth::id(),
            'diverifikasi_oleh' => Auth::id(),
            'tanggal_verifikasi' => now(),
        ]);
    }

    public function rejectPembayaran(Request $request, $id)
    {
        extract($this->getRoleFilterData());
        $request->validate(['alasan' => 'required|string|max:500']);
        $pembayaran = PembayaranTagihan::with(['tagihan', 'keluarga'])->findOrFail($id);

        abort_unless($this->bolehKelolaPembayaran($pembayaran, $unit, $rt_id), 403, 'Pembayaran ini bukan wewenang akun Anda.');

        $pembayaran->update([
            'status' => 'Unpaid',
            'catatan' => 'Ditolak: ' . $request->input('alasan'),
        ]);

        \App\Services\NotificationService::notifikasiIuranDitolak(
            $pembayaran->keluarga,
            'tagihan ' . ($pembayaran->tagihan->judul ?? 'iuran'),
            $request->input('alasan'),
            route('warga.tagihan.index')
        );

        return redirect()->back()->with('success', 'Pembayaran berhasil ditolak.');
    }

    public function broadcastReminder($id)
    {
        extract($this->getRoleFilterData());
        $pembayaran = PembayaranTagihan::with(['tagihan', 'keluarga.kepalaKeluarga'])->findOrFail($id);

        abort_unless($this->bolehKelolaPembayaran($pembayaran, $unit, $rt_id), 403, 'Pembayaran ini bukan wewenang akun Anda.');

        if ($pembayaran->status !== 'Unpaid') {
            return redirect()->back()->with('error', 'Tagihan sudah dibayar atau sedang diverifikasi.');
        }

        // Pengingat lewat notifikasi aplikasi + push browser (WhatsApp/email belum dipakai).
        $kepala = $pembayaran->keluarga->kepalaKeluarga;
        $user = \App\Services\NotificationService::userDariNik($kepala?->nik);
        if (! $user) {
            return redirect()->back()->with('warning', 'Warga ini belum memiliki akun aktif, pengingat tidak dapat dikirim.');
        }
        if (! \App\Services\NotificationService::isPushEnabled()) {
            return redirect()->back()->with('error', 'Notifikasi aplikasi sedang dinonaktifkan di Pengaturan Sistem.');
        }

        \App\Services\NotificationService::kirimKeUser($user, [
            'type'     => 'warning',
            'icon'     => 'bi-cash-coin',
            'judul'    => 'Pengingat Pembayaran',
            'pesan'    => "Tagihan \"{$pembayaran->tagihan->judul}\" sebesar {$pembayaran->tagihan->formatted_nominal} belum dibayar. Mohon segera melakukan pembayaran.",
            'url'      => route('warga.tagihan.index'),
            'kategori' => 'iuran',
        ]);

        $nama = $kepala?->nama_lengkap ?? 'warga';
        $punyaBrowser = \App\Services\NotificationService::isWebPushEnabled() && $user->pushSubscriptions()->exists();
        $info = $punyaBrowser
            ? 'Notifikasi aplikasi dan push browser terkirim ke ' . $nama . '.'
            : 'Notifikasi aplikasi terkirim ke ' . $nama . '. Push browser tidak terkirim karena browser warga belum mengaktifkan notifikasi.';

        return redirect()->back()->with('success', $info);
    }

    private function saveBase64Image(string $base64Data, string $folder): ?string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg+xml'])) {
                $type = 'jpg';
            }
            if ($type === 'svg+xml') $type = 'svg';

            $data = base64_decode($data);
            if ($data === false) {
                return null;
            }

            $filename = $folder . '/' . Str::uuid() . '.' . $type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $data);

            // Simpan juga langsung ke public/storage untuk web native serving
            try {
                $dst = public_path('storage/' . $filename);
                $dstDir = dirname($dst);
                if (!is_dir($dstDir)) {
                    @mkdir($dstDir, 0755, true);
                }
                @file_put_contents($dst, $data);
            } catch (\Throwable $e) {}

            return $filename;
        }

        return null;
    }
}

