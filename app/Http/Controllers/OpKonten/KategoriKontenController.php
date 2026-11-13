<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use App\Models\KategoriKonten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Master kategori konten (berita, agenda, pengumuman). Hanya Operator Konten RW yang mengelola;
 * kategori berlaku untuk semua RT. Akses dibatasi di rute (role:Op Konten RW).
 */
class KategoriKontenController extends Controller
{
    public function index()
    {
        $kategori = KategoriKonten::query()->urut()->get()->each(function (KategoriKonten $k) {
            $k->setAttribute('pemakaian', $k->pemakaian());
        });

        return view('opKonten.kategori.index', [
            'kategori' => $kategori,
            'warna'    => KategoriKonten::WARNA,
            'jenis'    => KategoriKonten::JENIS,
        ]);
    }

    private function validasi(Request $request, ?string $id = null): array
    {
        $data = $request->validate([
            'nama'  => ['required', 'string', 'max:60', Rule::unique('kategori_konten', 'nama')->ignore($id)],
            'warna' => ['required', Rule::in(array_keys(KategoriKonten::WARNA))],
            'berlaku'   => ['required', 'array', 'min:1'],
            'berlaku.*' => [Rule::in(array_keys(KategoriKonten::JENIS))],
        ], [
            'nama.required'   => 'Nama kategori wajib diisi.',
            'nama.max'        => 'Nama kategori maksimal 60 karakter.',
            'nama.unique'     => 'Nama kategori sudah ada.',
            'warna.required'  => 'Warna wajib dipilih.',
            'berlaku.required' => 'Pilih minimal satu jenis konten.',
            'berlaku.min'     => 'Pilih minimal satu jenis konten.',
        ]);

        $berlaku = $data['berlaku'];

        return [
            'nama'               => trim($data['nama']),
            'warna'              => $data['warna'],
            'berlaku_berita'     => in_array('berita', $berlaku, true),
            'berlaku_agenda'     => in_array('agenda', $berlaku, true),
            'berlaku_pengumuman' => in_array('pengumuman', $berlaku, true),
            'aktif'              => $request->boolean('aktif'),
        ];
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['urutan'] = (int) KategoriKonten::max('urutan') + 1;

        KategoriKonten::create($data);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $kategori = KategoriKonten::findOrFail($id);
        $data = $this->validasi($request, $kategori->id);

        DB::transaction(function () use ($kategori, $data) {
            $namaLama = $kategori->nama;
            $kategori->update($data);

            // Nama disalin ke setiap konten agar tampilan lama tetap konsisten setelah diganti nama.
            if ($namaLama !== $data['nama']) {
                foreach (array_keys(KategoriKonten::JENIS) as $tabel) {
                    DB::table($tabel)->where('kategori_id', $kategori->id)->update(['kategori' => $data['nama']]);
                }
            }
        });

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $kategori = KategoriKonten::findOrFail($id);

        if ($kategori->pemakaian()['total'] > 0) {
            return back()->with('error', 'Kategori sudah dipakai oleh konten dan tidak dapat dihapus. Nonaktifkan saja agar tidak muncul di pilihan baru.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
