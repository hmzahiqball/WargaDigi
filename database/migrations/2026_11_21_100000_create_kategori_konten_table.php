<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Master kategori konten (berita, agenda, pengumuman), dikelola Operator Konten RW dan berlaku
 * untuk semua RT. Kategori lama yang tertulis langsung di form dipindahkan menjadi data master,
 * konten lama disambungkan ke master lewat kategori_id (nama tetap disimpan di kolom kategori).
 */
return new class extends Migration
{
    private const WARNA = ['hijau', 'biru', 'kuning', 'ungu', 'tosca', 'merah-muda', 'oranye', 'abu'];

    /** Kategori bawaan lama: [nama, dipakai di berita]. Agenda dan pengumuman selalu diizinkan. */
    private const BAWAAN = [
        ['Sosial', true], ['Infrastruktur', true], ['Hiburan', true],
        ['Kesehatan', true], ['Keamanan', true], ['Organisasi', false],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('kategori_konten')) {
            Schema::create('kategori_konten', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama', 60)->unique();
                $table->string('warna', 20)->default('abu');
                $table->unsignedSmallInteger('urutan')->default(0);
                $table->boolean('aktif')->default(true);
                $table->boolean('berlaku_berita')->default(true);
                $table->boolean('berlaku_agenda')->default(true);
                $table->boolean('berlaku_pengumuman')->default(true);
                $table->timestamps();
            });
        }

        foreach (['berita', 'agenda', 'pengumuman'] as $tabel) {
            Schema::table($tabel, function (Blueprint $table) use ($tabel) {
                if ($tabel === 'pengumuman' && ! Schema::hasColumn($tabel, 'kategori')) {
                    $table->string('kategori', 255)->nullable()->after('judul_pengumuman');
                }
                if (! Schema::hasColumn($tabel, 'kategori_id')) {
                    $table->foreignUuid('kategori_id')->nullable()->constrained('kategori_konten')->nullOnDelete();
                }
            });
        }

        $this->isiMaster();
    }

    private function isiMaster(): void
    {
        $now = now();
        $urut = 0;
        $dibuat = [];

        $tambah = function (string $nama, bool $berita, bool $agenda, bool $pengumuman) use (&$dibuat, &$urut, $now) {
            $kunci = mb_strtolower($nama);
            if (isset($dibuat[$kunci])) {
                return;
            }
            $dibuat[$kunci] = true;
            DB::table('kategori_konten')->insertOrIgnore([
                'id' => (string) Str::uuid(), 'nama' => $nama, 'warna' => self::WARNA[$urut % count(self::WARNA)],
                'urutan' => ++$urut, 'aktif' => true,
                'berlaku_berita' => $berita, 'berlaku_agenda' => $agenda, 'berlaku_pengumuman' => $pengumuman,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        };

        foreach (self::BAWAAN as [$nama, $berita]) {
            $tambah($nama, $berita, true, true);
        }

        // Nama lain yang sudah terpakai di data lama (mis. dari seeder) ikut menjadi master.
        $pakaiBerita = DB::table('berita')->whereNotNull('kategori')->where('kategori', '<>', '')->distinct()->pluck('kategori')->all();
        $pakaiAgenda = DB::table('agenda')->whereNotNull('kategori')->where('kategori', '<>', '')->distinct()->pluck('kategori')->all();
        foreach (array_unique(array_merge($pakaiBerita, $pakaiAgenda)) as $nama) {
            $tambah($nama, in_array($nama, $pakaiBerita, true), in_array($nama, $pakaiAgenda, true), true);
        }

        // Organisasi sudah terpakai pada berita? Maka izinkan juga di berita.
        foreach ($pakaiBerita as $nama) {
            DB::table('kategori_konten')->where('nama', $nama)->update(['berlaku_berita' => true]);
        }
        foreach ($pakaiAgenda as $nama) {
            DB::table('kategori_konten')->where('nama', $nama)->update(['berlaku_agenda' => true]);
        }

        foreach (['berita', 'agenda'] as $tabel) {
            DB::statement("UPDATE {$tabel} k JOIN kategori_konten m ON m.nama = k.kategori SET k.kategori_id = m.id WHERE k.kategori_id IS NULL");
        }
    }

    public function down(): void
    {
        foreach (['berita', 'agenda', 'pengumuman'] as $tabel) {
            Schema::table($tabel, function (Blueprint $table) use ($tabel) {
                if (Schema::hasColumn($tabel, 'kategori_id')) {
                    $table->dropConstrainedForeignId('kategori_id');
                }
                if ($tabel === 'pengumuman' && Schema::hasColumn($tabel, 'kategori')) {
                    $table->dropColumn('kategori');
                }
            });
        }
        Schema::dropIfExists('kategori_konten');
    }
};
