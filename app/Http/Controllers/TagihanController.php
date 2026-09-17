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

class TagihanController extends Controller
{
    private function getRoleFilterData()
    {
        $user = Auth::user();
        $unit = null;
        $rt_id = null;

        if ($user->role === 'Op Keuangan RT') {
            $unit = 'RT';
            $rt_id = $user->rt_id;
        } elseif ($user->role === 'Op Keuangan RW') {
            $unit = 'RW';
        } elseif ($user->role === 'DKM') {
            $unit = 'DKM';
        }

        return compact('unit', 'rt_id');
    }

    public function rekeningIndex()
    {
        extract($this->getRoleFilterData());
        $query = RekeningBendahara::where('unit', $unit);
        if ($unit === 'RT') {
            $query->where('rt_id', $rt_id);
        }
        $rekening = $query->first();

        return view('opKeuangan.rekening', compact('rekening', 'unit', 'rt_id'));
    }

    public function rekeningSave(Request $request)
    {
        extract($this->getRoleFilterData());
        $request->validate([
            'bank' => 'required|string|max:50',
            'no_rek' => 'required|string|max:50',
            'nama_rek' => 'required|string|max:100',
            'qris_file' => 'nullable|image|max:2048',
        ]);

        $query = RekeningBendahara::where('unit', $unit);
        if ($unit === 'RT') {
            $query->where('rt_id', $rt_id);
        }
        $rekening = $query->first();

        $qrisPath = $rekening ? $rekening->qris_file : null;
        if ($request->hasFile('qris_file')) {
            $qrisPath = $request->file('qris_file')->store('rekening', 'public');
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
                'rt_id' => $rt_id,
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

        return view('opKeuangan.iuran.detail', compact('tagihan', 'unit'));
    }

    public function tagihanStore(Request $request)
    {
        extract($this->getRoleFilterData());
        $request->validate([
            'judul' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'tenggat' => 'nullable|date',
        ]);

        $tagihan = Tagihan::create([
            'id' => Str::uuid(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'jenis' => 'insidental',
            'nominal' => $request->nominal,
            'periode_bulan' => date('n'),
            'periode_tahun' => date('Y'),
            'unit' => $unit,
            'rt_id' => $rt_id,
            'pembuat_id' => Auth::id(),
            'status' => 'Active',
            'tenggat' => $request->tenggat,
        ]);

        // Assign ke keluarga terkait
        if ($unit === 'RT') {
            $keluargas = Keluarga::where('rt_id', $rt_id)->get();
        } else {
            $keluargas = Keluarga::all();
        }

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

        if (count($data) > 0) {
            PembayaranTagihan::insert($data);
        }

        return redirect()->back()->with('success', 'Tagihan insidental berhasil dibuat dan disebar ke warga.');
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

        $kepalaKeluarga = $pembayaran->keluarga->kepalaKeluarga;
        if ($kepalaKeluarga) {
            $user = \App\Models\User::where('nik', $kepalaKeluarga->nik)->first();
            if ($user) {
                $user->notify(new \App\Notifications\TagihanReminder($pembayaran));
            }
        }

        return redirect()->back()->with('success', 'Pengingat berhasil dikirim ke dashboard warga.');
    }
}
