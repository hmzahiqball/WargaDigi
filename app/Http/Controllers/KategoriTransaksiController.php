<?php

namespace App\Http\Controllers;

use App\Models\KategoriTransaksi;
use App\Models\MasterRt;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Pengelolaan master kategori transaksi.
 *
 * - Admin Aplikasi: mengelola kategori UMUM (unit = null) yang berlaku untuk
 *   semua unit, memudahkan rekap lintas unit.
 * - Operator Keuangan RT: mengelola kategori KHUSUS RT-nya.
 * - Operator Keuangan RW: mengelola kategori RW dan menentukan unit penerapannya (kombinasi RW, RT, DKM).
 * - Operator DKM: tidak mengelola kategori; kategori DKM dibuat oleh Operator Keuangan RW.
 *   Kategori umum hanya tampil sebagai referensi (tidak bisa diubah operator).
 */
class KategoriTransaksiController extends Controller
{
    /**
     * Konteks pengelola: mode 'admin' (umum) atau 'operator' (unit tertentu).
     *
     * @return array{mode:string, unit:?string, rt_id:?string}
     */
    private function konteks(): array
    {
        $user = Auth::user();
        $role = trim($user->activeRole() ?? '');

        if ($role === 'Admin Aplikasi') {
            return ['mode' => 'admin', 'unit' => null, 'rt_id' => null];
        }

        $unit = null;
        $rtId = null;
        if (in_array($role, ['Op Keuangan RT', 'Op. Keuangan RT', 'Bendahara RT'], true)) {
            $unit = 'RT';
            $rtId = $user->rt_id;
            if (! $rtId) {
                $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();
                $rtId = $penduduk?->keluarga?->rt_id ?? optional(MasterRt::first())->id;
            }
        } elseif (in_array($role, ['Op Keuangan RW', 'Op. Keuangan RW', 'Bendahara RW'], true)) {
            $unit = 'RW';
        } elseif (in_array($role, ['DKM', 'Bendahara DKM', 'Op Keuangan DKM', 'Op. Keuangan DKM'], true)) {
            $unit = 'DKM';
        }

        return ['mode' => 'operator', 'unit' => $unit, 'rt_id' => $rtId];
    }

    public function index(Request $request)
    {
        $k = $this->konteks();

        $query = KategoriTransaksi::query();

        if ($k['mode'] === 'admin') {
            // Admin: hanya kategori umum.
            $query->umum();
        } else {
            $this->pastikanOperatorBolehMengelola($k);

            // Operator: kategori umum + kategori khusus unitnya (tampil setara).
            // RW juga melihat kategori buatan DKM (sebagai referensi).
            $query->where(function ($q) use ($k) {
                if ($k['unit'] === 'DKM') {
                    // DKM: kategori buatannya + kategori RW yang diterapkan untuk DKM. Kategori umum tidak ikut.
                    $q->where('unit', 'DKM')
                        ->orWhere(fn ($s) => $s->where('unit', 'RW')->where('berlaku_dkm', true));

                    return;
                }
                $q->whereNull('unit')
                    ->orWhere(function ($sub) use ($k) {
                        if ($k['unit'] === 'RW') {
                            $sub->whereIn('unit', ['RW', 'DKM']);

                            return;
                        }
                        $sub->where('unit', $k['unit']);
                        if ($k['unit'] === 'RT' && $k['rt_id']) {
                            $sub->where(fn ($r) => $r->whereNull('rt_id')->orWhere('rt_id', $k['rt_id']));
                        }
                    });
                // RT juga melihat kategori buatan RW yang diterapkan untuk RT (hanya referensi).
                if ($k['unit'] === 'RT') {
                    $q->orWhere(fn ($sub) => $sub->where('unit', 'RW')->where('berlaku_rt', true));
                }
            });

            // Filter asal (umum / unit; untuk RW juga per unit penerapan).
            $asal = $request->input('asal');
            if ($asal === 'umum') {
                $query->whereNull('unit');
            } elseif ($asal === 'unit') {
                // Buatan unit sendiri: DKM = kategori DKM; lainnya = semua kategori berunit.
                $k['unit'] === 'DKM' ? $query->where('unit', 'DKM') : $query->whereNotNull('unit');
            } elseif ($asal === 'dari_rw') {
                $query->where('unit', 'RW');
            } elseif (in_array($asal, ['rw', 'rt', 'dkm'], true)) {
                $query->where('unit', 'RW')->where('berlaku_' . $asal, true);
            }
        }
        // Search nama.
        if ($search = trim((string) $request->input('search'))) {
            $query->where('nama', 'like', "%{$search}%");
        }
        // Filter tipe.
        if (in_array($request->input('tipe'), ['pemasukan', 'pengeluaran', 'keduanya'], true)) {
            $query->where('tipe', $request->input('tipe'));
        }
        // Filter status.
        if ($request->input('status') === 'aktif') {
            $query->where('aktif', true);
        } elseif ($request->input('status') === 'nonaktif') {
            $query->where('aktif', false);
        }

        $kategori = $query->orderBy('urutan')->orderBy('nama')->paginate(15)->withQueryString();

        return view('kategori-transaksi.index', [
            'kategori'  => $kategori,
            'mode'      => $k['mode'],
            'unit'      => $k['unit'],
        ]);
    }

    public function store(Request $request)
    {
        $k = $this->konteks();
        $data = $this->validateInput($request);
        $data += ['berlaku_rw' => true, 'berlaku_rt' => false, 'berlaku_dkm' => false];

        if ($k['mode'] === 'admin') {
            $data['unit'] = null;
            $data['rt_id'] = null;
        } else {
            $this->pastikanOperatorBolehMengelola($k);
            $data['unit'] = $k['unit'];
            $data['rt_id'] = $k['unit'] === 'RT' ? $k['rt_id'] : null;
            if ($k['unit'] === 'DKM') {
                // Kategori buatan DKM: milik DKM, tidak berlaku di RW.
                $data['berlaku_rw'] = false;
                $data['berlaku_dkm'] = true;
            }

            // RW menentukan unit penerapan lewat kotak centang (RW, RT, DKM; minimal satu).
            if ($k['unit'] === 'RW') {
                $data = array_merge($data, $this->penandaDariUnit($this->validasiBerlakuDi($request)));
            }
        }
        unset($data['berlaku_di']);

        KategoriTransaksi::create($data);

        return back()->with('success', 'Kategori transaksi berhasil ditambahkan.');
    }
    public function update(Request $request, string $id)
    {
        $kategori = KategoriTransaksi::findOrFail($id);
        $this->pastikanBolehKelola($kategori);

        $data = $this->validateInput($request);

        // RW dapat mengubah unit penerapan. Memperluas selalu boleh; melepas unit hanya bila belum dipakai di unit itu.
        if ($this->konteks()['unit'] === 'RW' && $request->has('berlaku_di')) {
            $baru = $this->validasiBerlakuDi($request);
            $lama = $kategori->unit_berlaku;

            foreach (array_diff($lama, $baru) as $dilepas) {
                if ($this->dipakaiDiUnit($kategori, $dilepas)) {
                    return back()->with('error', "Unit {$dilepas} tidak dapat dilepas karena kategori ini sudah dipakai di {$dilepas}. "
                        . 'Nonaktifkan kategori ini bila tidak ingin dipakai lagi.');
                }
            }
            $data = array_merge($data, ['unit' => 'RW'], $this->penandaDariUnit($baru));
        }

        $kategori->update($data);

        return back()->with('success', 'Kategori transaksi berhasil diperbarui.');
    }
    public function destroy(string $id)
    {
        $kategori = KategoriTransaksi::findOrFail($id);
        $this->pastikanBolehKelola($kategori);

        // Cegah hapus bila sudah dipakai transaksi; nonaktifkan saja agar rekap utuh.
        $dipakai = \App\Models\TransaksiKeuangan::where('kategori_id', $kategori->id)->exists()
            || \App\Models\Tagihan::where('kategori_id', $kategori->id)->exists();
        if ($dipakai) {
            $kategori->update(['aktif' => false]);

            return back()->with('success', 'Kategori sudah dipakai transaksi, sehingga dinonaktifkan (bukan dihapus) agar rekap tetap utuh.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori transaksi berhasil dihapus.');
    }

    private function validateInput(Request $request): array
    {
        $dkm = $this->konteks()['unit'] === 'DKM' && $this->konteks()['mode'] === 'operator';

        return $request->validate([
            'nama'   => 'required|string|max:100',
            // Kategori buatan DKM hanya untuk pengeluaran; pemasukan DKM memakai kategori yang diterapkan RW.
            'tipe'   => $dkm ? 'required|in:pengeluaran' : 'required|in:pemasukan,pengeluaran,keduanya',
            'aktif'  => 'nullable|boolean',
            'urutan' => 'nullable|integer|min:0',
        ], [
            'tipe.in' => $dkm ? 'Kategori DKM hanya dapat dibuat untuk pengeluaran.' : 'Tipe kategori tidak valid.',
        ]) + ['aktif' => $request->boolean('aktif', true), 'urutan' => (int) $request->input('urutan', 0)];
    }

    /** Unit tak dikenal ditolak. Operator DKM boleh mengelola, tetapi hanya kategori pengeluaran miliknya. */
    private function pastikanOperatorBolehMengelola(array $k): void
    {
        abort_if(! $k['unit'], 403, 'Unit keuangan tidak terdefinisi untuk akun ini.');
    }

    /** Pastikan user berhak mengelola kategori ini (admin: umum; RW: kategori RW; DKM: kategori DKM; RT: RT sendiri). */
    private function pastikanBolehKelola(KategoriTransaksi $kategori): void
    {
        $k = $this->konteks();
        if ($k['mode'] === 'admin') {
            abort_if($kategori->unit !== null, 403, 'Admin hanya dapat mengelola kategori umum.');

            return;
        }
        $this->pastikanOperatorBolehMengelola($k);

        if ($k['unit'] === 'RW') {
            abort_if($kategori->unit !== 'RW', 403, 'Anda tidak berhak mengelola kategori ini.');

            return;
        }

        abort_if($kategori->unit !== $k['unit'], 403, 'Anda tidak berhak mengelola kategori ini.');
        if ($k['unit'] === 'RT' && $kategori->rt_id && $kategori->rt_id !== $k['rt_id']) {
            abort(403, 'Kategori milik RT lain.');
        }
    }

    /**
     * Validasi kotak centang "Berlaku di": minimal satu dari RW, RT, DKM.
     *
     * @return string[] unit huruf besar, mis. ['RW', 'DKM']
     */
    private function validasiBerlakuDi(Request $request): array
    {
        $pilih = $request->validate([
            'berlaku_di'   => 'required|array|min:1',
            'berlaku_di.*' => 'in:rw,rt,dkm',
        ], [
            'berlaku_di.required' => 'Pilih minimal satu unit tempat kategori ini berlaku (RW, RT, atau DKM).',
            'berlaku_di.min'      => 'Pilih minimal satu unit tempat kategori ini berlaku (RW, RT, atau DKM).',
        ])['berlaku_di'];

        return array_map('strtoupper', array_values(array_unique($pilih)));
    }

    /** @param string[] $unit */
    private function penandaDariUnit(array $unit): array
    {
        return [
            'berlaku_rw'  => in_array('RW', $unit, true),
            'berlaku_rt'  => in_array('RT', $unit, true),
            'berlaku_dkm' => in_array('DKM', $unit, true),
        ];
    }

    /** Apakah kategori sudah dipakai transaksi atau Iuran Lainnya di unit tertentu. */
    private function dipakaiDiUnit(KategoriTransaksi $kategori, string $unit): bool
    {
        return \App\Models\TransaksiKeuangan::where('kategori_id', $kategori->id)->where('unit_sumber', $unit)->exists()
            || \App\Models\Tagihan::where('kategori_id', $kategori->id)->where('unit', $unit)->exists();
    }
}
