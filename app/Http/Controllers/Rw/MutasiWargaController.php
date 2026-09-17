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
        $mutasi = MutasiWarga::findOrFail($id);
        
        $data = $mutasi->data_pengajuan;

        if ($mutasi->jenis_mutasi == 'tambah') {
            Penduduk::create([
                'keluarga_id' => $mutasi->keluarga_id,
                'nama_lengkap' => $data['nama_lengkap'],
                'nik' => $data['nik'],
                'status_hubungan_keluarga' => $data['status_hubungan_keluarga'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'agama' => $data['agama'],
                'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                'pekerjaan' => $data['pekerjaan'],
                'status_perkawinan' => $data['status_perkawinan'],
                'kewarganegaraan' => $data['kewarganegaraan'],
                'nama_ayah' => $data['nama_ayah'] ?? null,
                'nama_ibu' => $data['nama_ibu'] ?? null,
                'file_kk' => $mutasi->file_bukti,
            ]);
        } else if ($mutasi->jenis_mutasi == 'edit') {
            $penduduk = Penduduk::findOrFail($mutasi->penduduk_id);
            $updateData = [];
            
            $fieldsToUpdate = [
                'nama_lengkap', 'nik', 'pendidikan_terakhir', 'pekerjaan', 'status_perkawinan'
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
        }

        $mutasi->status = 'disetujui';
        $mutasi->save();

        return redirect()->back()->with('success', 'Mutasi warga disetujui dan data penduduk telah diperbarui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        $mutasi = MutasiWarga::findOrFail($id);
        $mutasi->status = 'ditolak';
        $mutasi->keterangan_tolak = $request->alasan_penolakan;
        $mutasi->save();

        return redirect()->back()->with('success', 'Mutasi warga ditolak.');
    }
}
