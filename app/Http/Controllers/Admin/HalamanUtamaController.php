<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HalamanUtama;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Admin Aplikasi: Pengaturan Halaman Utama (banner dan footer landing page).
 */
class HalamanUtamaController extends Controller
{
    private const ATURAN_GAMBAR = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

    public function edit()
    {
        return view('admin.halaman-utama', [
            'data'       => HalamanUtama::data(),
            'fotoGaleri' => HalamanUtama::fotoGaleri(),
        ]);
    }

    public function update(Request $request)
    {
        $slot = array_keys(HalamanUtama::SLOT);
        $aturan = [
            'banner.badge'       => ['nullable', 'string', 'max:60'],
            'banner.judul'       => ['required', 'string', 'max:120'],
            'banner.deskripsi'   => ['nullable', 'string', 'max:400'],
            'banner.tombol_teks' => ['nullable', 'string', 'max:40'],
            'banner.tombol_url'  => ['nullable', 'string', 'max:255', 'regex:/^(\/(?!\/)[^\s]*|https?:\/\/[^\s]+|#[^\s]*)$/'],

            'footer.nama'          => ['required', 'string', 'max:60'],
            'footer.deskripsi'     => ['nullable', 'string', 'max:300'],
            'footer.sekretariat'   => ['nullable', 'string', 'max:300'],
            'footer.email'         => ['nullable', 'email', 'max:120'],
            'footer.telepon'       => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'footer.peta_url'      => ['nullable', 'url', 'max:500'],
            'footer.copyright'     => ['required', 'string', 'max:160'],
            'footer.judul_partner' => ['nullable', 'string', 'max:60'],

            'partner'           => ['nullable', 'array', 'max:' . HalamanUtama::MAKS_PARTNER],
            'partner.*.nama'    => ['required', 'string', 'max:80'],
            'partner.*.url'     => ['nullable', 'url', 'max:255'],
            'partner.*.logo'    => self::ATURAN_GAMBAR,
            'partner.*.logo_lama' => ['nullable', 'string', 'max:255'],
        ];
        foreach ($slot as $s) {
            $aturan["gambar.$s.sumber"]   = ['required', Rule::in(['bawaan', 'galeri', 'unggah'])];
            $aturan["gambar.$s.galeri"]   = ['nullable', 'string', 'max:255'];
            $aturan["gambar.$s.file"]     = self::ATURAN_GAMBAR;
            $aturan["gambar.$s.label"]    = ['nullable', 'string', 'max:40'];
            $aturan["gambar.$s.sublabel"] = ['nullable', 'string', 'max:60'];
        }

        $v = $request->validate($aturan, [
            'banner.judul.required'     => 'Judul banner wajib diisi.',
            'banner.tombol_url.regex'   => 'Tautan tombol harus diawali "/" (halaman aplikasi), "http(s)://", atau "#".',
            'footer.telepon.regex'      => 'Nomor telepon hanya boleh angka, spasi, +, -, dan tanda kurung.',
            'footer.copyright.required' => 'Teks copyright wajib diisi.',
            'partner.max'               => 'Maksimal ' . HalamanUtama::MAKS_PARTNER . ' partner.',
            'partner.*.nama.required'   => 'Nama partner wajib diisi.',
            'partner.*.url.url'         => 'Tautan partner harus berupa URL lengkap (https://...).',
            '*.image'                   => 'Berkas harus berupa gambar.',
            '*.mimes'                   => 'Format gambar harus JPG, PNG, atau WEBP.',
            '*.max'                     => 'Ukuran gambar maksimal 5 MB.',
        ]);

        $lama = HalamanUtama::data();
        $data = $lama;
        $berkasBuang = [];

        $data['banner'] = array_merge($lama['banner'], array_map(fn ($x) => $x ?? '', $v['banner']));
        $data['banner']['tombol_url'] = $v['banner']['tombol_url'] ?? '';
        $data['footer'] = array_merge($lama['footer'], array_map(fn ($x) => $x ?? '', $v['footer']));

        // Gambar banner.
        foreach ($slot as $s) {
            $input = $v['gambar'][$s];
            $sebelum = $lama['banner']['gambar'][$s] ?? ['sumber' => 'bawaan', 'path' => null];
            $baru = [
                'sumber'   => $input['sumber'],
                'path'     => null,
                'label'    => $input['label'] ?? '',
                'sublabel' => $input['sublabel'] ?? '',
            ];

            if ($input['sumber'] === 'galeri') {
                $path = $input['galeri'] ?? '';
                if (! HalamanUtama::fotoGaleriSah($path)) {
                    throw ValidationException::withMessages(["gambar.$s.galeri" => 'Pilih foto dari Galeri Dokumentasi untuk ' . strtolower(HalamanUtama::SLOT[$s][0]) . '.']);
                }
                $baru['path'] = $path;
            } elseif ($input['sumber'] === 'unggah') {
                if ($request->hasFile("gambar.$s.file")) {
                    $baru['path'] = HalamanUtama::unggah($request->file("gambar.$s.file"), 'banner');
                } elseif (($sebelum['sumber'] ?? '') === 'unggah' && ! empty($sebelum['path'])) {
                    $baru['path'] = $sebelum['path']; // tetap memakai unggahan sebelumnya
                } else {
                    throw ValidationException::withMessages(["gambar.$s.file" => 'Unggah gambar untuk ' . strtolower(HalamanUtama::SLOT[$s][0]) . '.']);
                }
            }

            if (($sebelum['sumber'] ?? '') === 'unggah' && ($sebelum['path'] ?? null) !== $baru['path']) {
                $berkasBuang[] = $sebelum['path'] ?? null;
            }
            $data['banner']['gambar'][$s] = $baru;
        }

        // Partner / "Didukung oleh". Logo lama hanya dipertahankan bila memang milik daftar sebelumnya.
        $logoSebelumnya = collect($lama['footer']['partner'])->pluck('logo')->filter()->all();
        $partner = [];
        foreach ($v['partner'] ?? [] as $i => $p) {
            $logo = in_array($p['logo_lama'] ?? null, $logoSebelumnya, true) ? $p['logo_lama'] : null;
            if ($request->hasFile("partner.$i.logo")) {
                $logo = HalamanUtama::unggah($request->file("partner.$i.logo"), 'partner');
            }
            $partner[] = ['nama' => $p['nama'], 'url' => $p['url'] ?? '', 'logo' => $logo];
        }
        $data['footer']['partner'] = $partner;
        $logoDipakai = collect($partner)->pluck('logo')->filter()->all();
        foreach ($logoSebelumnya as $l) {
            if (! in_array($l, $logoDipakai, true)) {
                $berkasBuang[] = $l;
            }
        }

        HalamanUtama::simpanData($data);
        foreach ($berkasBuang as $b) {
            HalamanUtama::hapusBerkas($b);
        }

        return redirect()->route('admin.halaman-utama')->with('success', 'Pengaturan halaman utama berhasil disimpan.');
    }
}
