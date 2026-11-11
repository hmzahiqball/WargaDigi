<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    /**
     * Menampilkan halaman Layanan Permohonan Surat (indeks + form).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();

        // Riwayat pengajuan surat milik penduduk ini
        $pengajuan = collect();
        if ($penduduk) {
            $pengajuan = PengajuanSurat::where('penduduk_id', $penduduk->id)
                ->orderByDesc('created_at')
                ->get();
        }

        // Daftar tipe surat dari model (9 kategori sesuai format RW 21)
        $tipeSurat = PengajuanSurat::TIPE_SURAT;

        // URL KTP/KK dari data penduduk (untuk preview otomatis)
        $ktpUrl = null;
        $kkUrl = null;
        if ($penduduk) {
            if ($penduduk->file_ktp) {
                $ktpUrl = asset('storage/' . $penduduk->file_ktp);
            }
            if ($penduduk->file_kk) {
                $kkUrl = asset('storage/' . $penduduk->file_kk);
            }
        }

        // Anggota keluarga (termasuk KK) sebagai pilihan pemohon.
        // Kepala Keluarga dapat mengajukan surat untuk dirinya atau anggota keluarganya.
        $anggotaKeluarga = collect();
        if ($penduduk && $penduduk->keluarga) {
            $alamat = $penduduk->keluarga->alamat;
            $anggotaKeluarga = Penduduk::where('keluarga_id', $penduduk->keluarga_id)
                ->orderByRaw("CASE WHEN status_hubungan_keluarga = 'Kepala Keluarga' THEN 0 ELSE 1 END")
                ->orderBy('nama_lengkap')
                ->get()
                ->map(fn (Penduduk $a) => [
                    'id' => $a->id,
                    'nama_lengkap' => $a->nama_lengkap,
                    'nik' => $a->nik,
                    'tempat_lahir' => $a->tempat_lahir,
                    'tanggal_lahir' => $a->tanggal_lahir ? $a->tanggal_lahir->format('d-m-Y') : '-',
                    'jenis_kelamin' => $a->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                    'pekerjaan' => $a->pekerjaan ?? '-',
                    'agama' => $a->agama ?? '-',
                    'status_perkawinan' => $a->status_perkawinan ?? '-',
                    'status_hubungan_keluarga' => $a->status_hubungan_keluarga ?? '-',
                    'alamat' => $alamat ?? '-',
                    'ktp_url' => $a->file_ktp ? asset('storage/' . $a->file_ktp) : null,
                    'kk_url' => $a->file_kk ? asset('storage/' . $a->file_kk) : null,
                ])
                ->values();
        }

        return view('warga.permohonan-surat', compact('user', 'penduduk', 'pengajuan', 'tipeSurat', 'ktpUrl', 'kkUrl', 'anggotaKeluarga'));
    }

    /**
     * Menyimpan pengajuan surat baru ke database.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();

        if (!$penduduk) {
            return back()->withErrors(['error' => 'Data penduduk tidak ditemukan. Silakan hubungi pengurus RT/RW.']);
        }

        $request->validate([
            'tipe_surat' => 'required|string|max:100',
            'pemohon_id' => 'nullable|string',
            'keperluan' => 'nullable|string|max:1000',
            'data_tambahan' => 'nullable|array',
            'data_tambahan.*' => 'nullable|string|max:2000',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'pernyataan' => 'accepted',
        ], [
            'tipe_surat.required' => 'Silakan pilih jenis surat terlebih dahulu.',
            'pernyataan.accepted' => 'Anda harus menyetujui pernyataan kebenaran data.',
        ]);

        // Tentukan pemohon: default KK yang login. Bila dipilih anggota lain,
        // pastikan anggota tsb berada di keluarga yang sama (keamanan).
        $pemohon = $penduduk;
        if ($request->filled('pemohon_id') && $request->pemohon_id !== $penduduk->id) {
            $kandidat = Penduduk::where('id', $request->pemohon_id)
                ->where('keluarga_id', $penduduk->keluarga_id)
                ->first();

            if (! $kandidat) {
                return back()->withErrors([
                    'pemohon_id' => 'Pemohon yang dipilih tidak terdaftar sebagai anggota keluarga Anda.',
                ])->withInput();
            }
            $pemohon = $kandidat;
        }

        // Tentukan file KTP: upload baru > data pemohon
        $fileKtp = $pemohon->file_ktp;
        if ($request->hasFile('file_ktp')) {
            $fileKtp = $request->file('file_ktp')->store('dokumen/ktp', 'public');
            // Update juga di data pemohon supaya tersimpan permanen
            $pemohon->update(['file_ktp' => $fileKtp]);
        }

        // Tentukan file KK: upload baru > data pemohon
        $fileKk = $pemohon->file_kk;
        if ($request->hasFile('file_kk')) {
            $fileKk = $request->file('file_kk')->store('dokumen/kk', 'public');
            $pemohon->update(['file_kk' => $fileKk]);
        }

        // Isian tambahan khusus jenis surat: hanya simpan field yang memang
        // dikenal untuk tipe tersebut, dan buang nilai kosong.
        $dataTambahan = $this->bersihkanDataTambahan($request->tipe_surat, (array) $request->input('data_tambahan', []));

        // Simpan pengajuan ke database (atas nama pemohon terpilih).
        $pengajuan = PengajuanSurat::create([
            'penduduk_id' => $pemohon->id,
            'tipe_surat' => $request->tipe_surat,
            'keterangan_tambahan' => $request->keperluan,
            'data_tambahan' => $dataTambahan ?: null,
            'file_ktp' => $fileKtp,
            'file_kk' => $fileKk,
            'status' => 'Diajukan',
        ]);

        // Notifikasi: ke Ketua RT di RT pemohon.
        \App\Services\NotificationService::notifikasiSuratDiajukan($pengajuan);

        return redirect()->route('warga.surat.index')
            ->with('success', 'Pengajuan surat berhasil dikirim! Silakan tunggu proses verifikasi oleh pengurus RT.');
    }

    /**
     * Menyaring isian tambahan: hanya field yang terdaftar di config untuk
     * jenis surat tsb, dan hanya nilai yang terisi. Mencegah data liar masuk.
     */
    private function bersihkanDataTambahan(string $tipeSurat, array $input): array
    {
        $fields = config("surat.jenis.{$tipeSurat}.fields", []);
        $hasil = [];
        foreach ($fields as $f) {
            $key = $f['key'] ?? null;
            if (! $key) {
                continue;
            }
            $nilai = trim((string) ($input[$key] ?? ''));
            if ($nilai !== '') {
                $hasil[$key] = $nilai;
            }
        }

        return $hasil;
    }

    /**
     * Download surat yang sudah selesai dalam format PDF.
     */
    public function downloadPdf($id, Request $request)
    {
        $user = $request->user();
        $penduduk = Penduduk::where('nik', $user->nik)->first();

        if (!$penduduk) {
            abort(403, 'Unauthorized access.');
        }

        $query = PengajuanSurat::with('penduduk.keluarga')->where('id', $id);

        if ($user->activeRole() == 'Warga') {
            $query->where('penduduk_id', $penduduk->id);
        }

        $item = $query->firstOrFail();

        if ($item->status !== 'Selesai') {
            abort(403, 'Surat belum selesai diproses.');
        }

        $data = app(\App\Services\SuratDataBuilder::class)->build($item, false);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.surat', compact('data'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('Surat_Pengantar_' . str_replace(' ', '_', $data->nama_lengkap) . '.pdf');
    }
}


