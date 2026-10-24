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
        } elseif (in_array($role, ['Op Keuangan RW', 'Op. Keuangan RW', 'Bendahara RW', 'Admin RW', 'Pimpinan RW'])) {
            $unit = 'RW';
            $rt_id = null;
        } elseif (in_array($role, ['DKM', 'Bendahara DKM', 'Op Keuangan DKM', 'Op. Keuangan DKM'])) {
            $unit = 'DKM';
            $rt_id = null;
        }

        return compact('unit', 'rt_id');
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
        $query = Tagihan::withCount(['pembayaran as paid_count' => function($q) {
            $q->where('status', 'Paid');
        }])->where('unit', $unit);

        if ($unit === 'RT') {
            $query->where('rt_id', $rt_id);
        }

        $tagihanList = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('opKeuangan.iuran.index', compact('tagihanList', 'unit', 'rt_id'));
    }

    public function iuranDetail($id)
    {
        extract($this->getRoleFilterData());
        $tagihan = Tagihan::with('pembayaran.keluarga.kepalaKeluarga')->findOrFail($id);

        // Ensure access control
        if ($tagihan->unit !== $unit || ($unit === 'RT' && $tagihan->rt_id !== $rt_id)) {
            abort(403);
        }

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

        return view('opKeuangan.iuran.detail', compact('tagihan', 'unit'));
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
            'deskripsi' => 'nullable|string',
            'tenggat' => 'nullable|date',
            'jenis' => 'nullable|in:rutin,insidental',
        ]);

        $jenis = $request->input('jenis', 'insidental');

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
            'periode_bulan' => date('n'),
            'periode_tahun' => date('Y'),
            'unit' => $unit,
            'rt_id' => $unit === 'RT' ? $rt_id : null,
            'pembuat_id' => Auth::id(),
            'status' => 'Active',
            'tenggat' => $request->tenggat,
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

        if ($tagihan->unit !== $unit || ($unit === 'RT' && $tagihan->rt_id !== $rt_id)) {
            abort(403);
        }

        $pembayaran->update([
            'status' => 'Paid',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Catat sebagai Pemasukan di Transaksi Keuangan
        TransaksiKeuangan::create([
            'id' => Str::uuid(),
            'kode_transaksi' => 'IN-TAG-' . strtoupper(Str::random(6)),
            'tipe' => 'pemasukan',
            'kategori' => 'Iuran Warga',
            'judul' => 'Pembayaran ' . $tagihan->judul . ' - KK ' . $pembayaran->keluarga->no_kk,
            'deskripsi' => 'Pembayaran lunas via ' . ($pembayaran->metode ?? 'Cash'),
            'jumlah' => $tagihan->nominal,
            'tanggal' => now()->toDateString(),
            'bukti_file' => $pembayaran->bukti_file,
            'status' => 'Verified', // Auto valid karena dicatat/diapprove bendahara
            'unit_sumber' => $tagihan->unit,
            'rt_id' => $tagihan->rt_id,
            'dicatat_oleh' => Auth::id(),
            'diverifikasi_oleh' => Auth::id(),
            'tanggal_verifikasi' => now(),
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil disetujui dan dicatat ke keuangan.');
    }

    public function rejectPembayaran(Request $request, $id)
    {
        extract($this->getRoleFilterData());
        $pembayaran = PembayaranTagihan::with('tagihan')->findOrFail($id);

        if ($pembayaran->tagihan->unit !== $unit || ($unit === 'RT' && $pembayaran->tagihan->rt_id !== $rt_id)) {
            abort(403);
        }

        $pembayaran->update([
            'status' => 'Unpaid',
            'catatan' => 'Ditolak: ' . $request->input('alasan', 'Bukti tidak valid'),
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil ditolak.');
    }

    public function broadcastReminder($id)
    {
        extract($this->getRoleFilterData());
        $pembayaran = PembayaranTagihan::with(['tagihan', 'keluarga.kepalaKeluarga'])->findOrFail($id);

        if ($pembayaran->tagihan->unit !== $unit || ($unit === 'RT' && $pembayaran->tagihan->rt_id !== $rt_id)) {
            abort(403);
        }

        if ($pembayaran->status !== 'Unpaid') {
            return redirect()->back()->with('error', 'Tagihan sudah dibayar atau sedang diverifikasi.');
        }

        // Validate active notification channels from System Settings (AC-08.9)
        $activeChannels = \App\Services\NotificationService::getActiveChannels();
        if (empty($activeChannels)) {
            return redirect()->back()->with('error', 'Pengiriman pengingat gagal: Seluruh kanal notifikasi (WhatsApp, Email, Push) sedang dinonaktifkan di Pengaturan Sistem.');
        }

        $channelsSent = [];
        $channelsSkipped = [];

        // 1. WhatsApp Delivery
        $kepalaKeluarga = $pembayaran->keluarga->kepalaKeluarga;
        $targetWa = $pembayaran->keluarga->no_wa ?: null;

        if (\App\Services\NotificationService::isWhatsAppEnabled()) {
            if ($targetWa) {
                $pesan = "Pengingat Tagihan: Tagihan \"{$pembayaran->tagihan->judul}\" sebesar {$pembayaran->tagihan->formatted_nominal} belum dibayar. Mohon segera melakukan pembayaran.";
                \App\Services\NotificationService::sendWhatsApp($targetWa, $pesan);
                $channelsSent[] = 'WhatsApp';
            }
        } else {
            $channelsSkipped[] = 'WhatsApp (dinonaktifkan)';
        }

        // 2. In-App / Push & Email Delivery via User Notification
        if ($kepalaKeluarga) {
            $user = \App\Models\User::where('nik', $kepalaKeluarga->nik)->first();
            if ($user) {
                if (\App\Services\NotificationService::isPushEnabled() || \App\Services\NotificationService::isEmailEnabled()) {
                    $user->notify(new \App\Notifications\TagihanReminder($pembayaran));
                    if (\App\Services\NotificationService::isPushEnabled()) {
                        $channelsSent[] = 'Dashboard / Push';
                    }
                    if (\App\Services\NotificationService::isEmailEnabled()) {
                        $channelsSent[] = 'Email';
                    }
                }
            }
        }

        if (!\App\Services\NotificationService::isPushEnabled()) {
            $channelsSkipped[] = 'Push (dinonaktifkan)';
        }
        if (!\App\Services\NotificationService::isEmailEnabled()) {
            $channelsSkipped[] = 'Email (dinonaktifkan)';
        }

        if (empty($channelsSent)) {
            return redirect()->back()->with('warning', 'Pengingat tidak dapat dikirim karena tujuan tidak memiliki data kontak valid.');
        }

        $message = 'Pengingat tagihan berhasil dikirim melalui kanal: ' . implode(', ', $channelsSent) . '.';
        if (!empty($channelsSkipped)) {
            $message .= ' [Kanal dinonaktifkan: ' . implode(', ', $channelsSkipped) . ']';
        }

        return redirect()->back()->with('success', $message);
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

