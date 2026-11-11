<?php

namespace App\Http\Controllers\Rw;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MutasiWarga;
use App\Models\Penduduk;

class MutasiWargaController extends Controller
{
    public function index()
    {
        $mutasi = MutasiWarga::with(['keluarga.kepalaKeluarga', 'penduduk'])
            ->where('status', 'menunggu_rw')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rw.mutasi-warga.index', compact('mutasi'));
    }

    public function approve(Request $request, $id)
    {
        $mutasi = MutasiWarga::where('status', 'menunggu_rw')->where('id', $id)->first();
        if (! $mutasi) {
            return back()->withErrors(['mutasi' => 'Status pengajuan tidak valid untuk diproses oleh RW.']);
        }

        $data = $mutasi->data_pengajuan;

        if ($mutasi->jenis_mutasi == 'tambah') {
            if (Penduduk::where('nik', $data['nik'] ?? null)->exists()) {
                return back()->withErrors(['nik' => 'NIK sudah terdaftar pada data penduduk.']);
            }

            Penduduk::create([
                'keluarga_id' => $mutasi->keluarga_id,
                'nama_lengkap' => $data['nama_lengkap'],
                'nik' => $data['nik'],
                'no_wa' => $data['no_wa'] ?? null,
                'status_hubungan_keluarga' => $data['status_hubungan_keluarga'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'agama' => $data['agama'],
                'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                'pekerjaan' => $data['pekerjaan'],
                'status_perkawinan' => $data['status_perkawinan'],
                'kewarganegaraan' => $data['kewarganegaraan'],
                'alamat' => $data['alamat'] ?? null,
                'alamat_domisili' => $data['alamat_domisili'] ?? null,
                'status_warga' => in_array($data['status_warga'] ?? null, ['tetap', 'tidak_tetap'], true) ? $data['status_warga'] : 'tidak_tetap',
                'nama_ayah' => $data['nama_ayah'] ?? null,
                'nama_ibu' => $data['nama_ibu'] ?? null,
                'file_kk' => $mutasi->file_bukti,
                'file_ktp' => $data['file_ktp_path'] ?? null,
            ]);
        } else if ($mutasi->jenis_mutasi == 'edit') {
            $penduduk = Penduduk::findOrFail($mutasi->penduduk_id);
            if (isset($data['nik']) && Penduduk::where('nik', $data['nik'])->where('id', '!=', $penduduk->id)->exists()) {
                return back()->withErrors(['nik' => 'NIK sudah terdaftar pada data penduduk lain.']);
            }
            $updateData = [];

            $fieldsToUpdate = [
                'nama_lengkap', 'nik', 'no_wa', 'pendidikan_terakhir', 'pekerjaan',
                'status_perkawinan', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
                'agama', 'kewarganegaraan', 'alamat', 'alamat_domisili', 'status_warga',
                'nama_ayah', 'nama_ibu',
            ];

            foreach ($fieldsToUpdate as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            if ($mutasi->file_bukti) {
                $updateData['file_kk'] = $mutasi->file_bukti;
            }

            $penduduk->update($updateData);
        } else if ($mutasi->jenis_mutasi == 'edit_kk') {
            // Pengajuan perubahan identitas KK. Perbarui tabel keluarga dengan
            // whitelist field; no_kk / nik_kepala_keluarga / rt_id tidak diubah.
            $keluarga = $mutasi->keluarga;
            if ($keluarga) {
                $updateData = [];
                $fieldsToUpdate = [
                    'alamat', 'alamat_domisili', 'no_wa', 'pemilik_rumah', 'status_warga',
                    'kelurahan', 'kecamatan', 'kabupaten', 'provinsi', 'kode_pos', 'rw',
                ];
                foreach ($fieldsToUpdate as $field) {
                    if (isset($data[$field])) {
                        $updateData[$field] = $data[$field];
                    }
                }
                if (! empty($updateData)) {
                    $keluarga->update($updateData);
                }
            }
        }

        $mutasi->status = 'disetujui';
        $mutasi->diproses_rw_oleh = auth()->id();
        $mutasi->diproses_rw_at = now();
        $mutasi->save();

        // Notifikasi: disetujui RW → pengaju.
        \App\Services\NotificationService::notifikasiMutasiHasil($mutasi, 'RW', true);

        return redirect()->back()->with('success', 'Pengajuan data disetujui dan data penduduk telah diperbarui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        $mutasi = MutasiWarga::where('status', 'menunggu_rw')->where('id', $id)->first();
        if (! $mutasi) {
            return back()->withErrors(['mutasi' => 'Status pengajuan tidak valid untuk diproses oleh RW.']);
        }
        $mutasi->status = 'ditolak';
        $mutasi->keterangan_tolak = $request->alasan_penolakan;
        $mutasi->ditolak_oleh = auth()->id();
        $mutasi->diproses_rw_at = now();
        $mutasi->save();

        // Notifikasi: ditolak RW → pengaju.
        \App\Services\NotificationService::notifikasiMutasiHasil($mutasi, 'RW', false, $request->alasan_penolakan);

        return redirect()->back()->with('success', 'Pengajuan data ditolak.');
    }
}
