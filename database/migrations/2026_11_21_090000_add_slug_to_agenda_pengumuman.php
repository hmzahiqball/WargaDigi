<?php

use App\Support\SlugKonten;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Slug untuk agenda dan pengumuman (berita sudah punya). Data lama diberi slug unik dari judul.
 * Slug berita berakhiran acak (mis. judul-6adc2c518ae65) dirapikan; slug bersih lain dipertahankan.
 */
return new class extends Migration
{
    private const KOLOM_JUDUL = [
        'berita'     => 'judul_berita',
        'agenda'     => 'judul_agenda',
        'pengumuman' => 'judul_pengumuman',
    ];

    public function up(): void
    {
        foreach (['agenda', 'pengumuman'] as $tabel) {
            if (! Schema::hasColumn($tabel, 'slug')) {
                Schema::table($tabel, function (Blueprint $table) {
                    $table->string('slug', 255)->nullable()->after('id');
                });
            }
        }

        foreach (self::KOLOM_JUDUL as $tabel => $kolom) {
            $baris = DB::table($tabel)->orderBy('created_at')->orderBy('id')->get(['id', $kolom . ' AS judul', 'slug']);
            foreach ($baris as $b) {
                $perluBaru = empty($b->slug) || preg_match('/-[0-9a-f]{13}$/', $b->slug) === 1;
                if (! $perluBaru) {
                    continue;
                }
                DB::table($tabel)->where('id', $b->id)->update([
                    'slug' => SlugKonten::buat($tabel, (string) $b->judul, $b->id),
                ]);
            }
        }

        foreach (['agenda', 'pengumuman'] as $tabel) {
            Schema::table($tabel, function (Blueprint $table) use ($tabel) {
                $table->unique('slug', $tabel . '_slug_unique');
            });
        }
    }

    public function down(): void
    {
        foreach (['agenda', 'pengumuman'] as $tabel) {
            Schema::table($tabel, function (Blueprint $table) use ($tabel) {
                $table->dropUnique($tabel . '_slug_unique');
                $table->dropColumn('slug');
            });
        }
    }
};
