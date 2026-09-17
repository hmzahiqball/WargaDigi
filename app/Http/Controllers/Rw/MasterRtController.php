<?php

namespace App\Http\Controllers\Rw;

use App\Http\Controllers\Controller;
use App\Models\MasterRt;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterRtController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $daftarRt = MasterRt::query()
            ->when($search, function ($q) use ($search) {
                $q->where('kode_rt', 'like', "%{$search}%")
                    ->orWhere('nama_rt', 'like', "%{$search}%");
            })
            ->withCount(['keluarga'])
            ->orderBy('kode_rt')
            ->paginate(10)
            ->withQueryString();

        return view('rw.master-rt.index', compact('daftarRt', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_rt' => ['required', 'string', 'max:10', Rule::unique('master_rt', 'kode_rt')],
            'nama_rt' => ['required', 'string', 'max:50'],
        ], [
            'kode_rt.unique' => 'Kode RT tersebut sudah terdaftar.',
        ]);

        MasterRt::create($validated);

        return redirect()
            ->route('rw.master-rt.index')
            ->with('success', 'Data RT ' . $validated['kode_rt'] . ' berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $rt = MasterRt::findOrFail($id);

        $validated = $request->validate([
            'kode_rt' => ['required', 'string', 'max:10', Rule::unique('master_rt', 'kode_rt')->ignore($rt->id)],
            'nama_rt' => ['required', 'string', 'max:50'],
        ], [
            'kode_rt.unique' => 'Kode RT tersebut sudah terdaftar.',
        ]);

        $rt->update($validated);

        return redirect()
            ->route('rw.master-rt.index')
            ->with('success', 'Data RT ' . $validated['kode_rt'] . ' berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $rt = MasterRt::withCount('keluarga')->findOrFail($id);

        // Prevent deleting an RT that still has households attached (FK restrictOnDelete).
        if ($rt->keluarga_count > 0) {
            return redirect()
                ->route('rw.master-rt.index')
                ->with('error', 'RT ' . $rt->kode_rt . ' tidak dapat dihapus karena masih memiliki ' . $rt->keluarga_count . ' data keluarga.');
        }

        $kode = $rt->kode_rt;
        $rt->delete();

        return redirect()
            ->route('rw.master-rt.index')
            ->with('success', 'Data RT ' . $kode . ' berhasil dihapus.');
    }
}
