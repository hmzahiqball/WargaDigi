<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pengaturan Tanda Tangan & Stempel default per akun pengurus (Ketua RT/RW).
 *
 * TTD/stempel disimpan di disk privat 'local' (folder ttd/). File hanya bisa
 * diakses oleh pemilik akun melalui route streaming (tidak ada URL publik).
 */
class TandaTanganController extends Controller
{
    private const DISK = 'local';
    private const DIR = 'ttd';

    public function edit(Request $request)
    {
        return view('pengaturan.tanda-tangan', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'ttd_file'        => 'nullable|image|mimes:png|max:2048',
            'ttd_base64'      => 'nullable|string',
            'stempel_file'    => 'nullable|image|mimes:png|max:2048',
            'hapus_ttd'       => 'nullable|boolean',
            'hapus_stempel'   => 'nullable|boolean',
        ], [
            'ttd_file.mimes'     => 'Tanda tangan harus berupa file PNG.',
            'ttd_file.max'       => 'Ukuran tanda tangan maksimal 2MB.',
            'stempel_file.mimes' => 'Stempel harus berupa file PNG.',
            'stempel_file.max'   => 'Ukuran stempel maksimal 2MB.',
        ]);

        $user = $request->user();

        // --- Tanda tangan: hapus > upload file > gambar (base64) ---
        if ($request->boolean('hapus_ttd')) {
            $this->hapusFile($user->ttd_default);
            $user->ttd_default = null;
        } elseif ($request->hasFile('ttd_file')) {
            $this->hapusFile($user->ttd_default);
            $user->ttd_default = $request->file('ttd_file')->store(self::DIR, self::DISK);
        } elseif ($request->filled('ttd_base64')) {
            if ($path = $this->simpanBase64($request->input('ttd_base64'))) {
                $this->hapusFile($user->ttd_default);
                $user->ttd_default = $path;
            }
        }

        // --- Stempel: hapus > upload file ---
        if ($request->boolean('hapus_stempel')) {
            $this->hapusFile($user->stempel_default);
            $user->stempel_default = null;
        } elseif ($request->hasFile('stempel_file')) {
            $this->hapusFile($user->stempel_default);
            $user->stempel_default = $request->file('stempel_file')->store(self::DIR, self::DISK);
        }

        $user->save();

        return redirect()->route('pengaturan.ttd.edit')
            ->with('success', 'Pengaturan tanda tangan dan stempel berhasil disimpan.');
    }

    /**
     * Streaming file TTD/stempel milik akun yang sedang login.
     * Hanya pemilik akun yang dapat mengakses (tanpa URL publik).
     */
    public function file(Request $request, string $jenis)
    {
        abort_unless(in_array($jenis, ['ttd', 'stempel'], true), 404);

        $user = $request->user();
        $path = $jenis === 'ttd' ? $user->ttd_default : $user->stempel_default;

        abort_if(! $path || ! Storage::disk(self::DISK)->exists($path), 404);

        return response()->file(Storage::disk(self::DISK)->path($path));
    }

    /** Simpan data URL base64 PNG menjadi file; mengembalikan path atau null. */
    private function simpanBase64(string $dataUrl): ?string
    {
        if (! preg_match('/^data:image\/png;base64,/', $dataUrl)) {
            return null;
        }
        $isi = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);
        if ($isi === false) {
            return null;
        }
        $path = self::DIR . '/' . Str::uuid()->toString() . '.png';
        Storage::disk(self::DISK)->put($path, $isi);

        return $path;
    }

    private function hapusFile(?string $path): void
    {
        if ($path && Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
