<?php

namespace Tests\Feature;

use App\Models\MasterRt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Menu "Persetujuan" dikelompokkan sebagai submenu untuk Ketua RT, Admin/Ketua RW,
 * dan Pimpinan, dengan submenu yang sesuai per role.
 */
class SidebarSubmenuTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(string $role): User
    {
        Role::findOrCreate($role, 'web');
        $rt = MasterRt::create(['kode_rt' => '03', 'nama_rt' => 'RT 03']);
        $user = User::create([
            'nik' => null,
            'username' => 'user' . str_replace(' ', '', $role),
            'role' => $role,
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $user->syncRoles([$role]);

        return $user;
    }

    public function test_ketua_rt_melihat_menu_persetujuan_dengan_tiga_submenu(): void
    {
        $user = $this->buatUser('Ketua RT');

        $html = $this->actingAs($user)->get(route('rt.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('sidebar-submenu', $html);
        $this->assertStringContainsString('>Persetujuan<', $html);
        $this->assertStringContainsString('Persetujuan Dokumen', $html);
        $this->assertStringContainsString('Persetujuan Keuangan', $html);
        $this->assertStringContainsString('Pengajuan Data', $html);
    }

    public function test_ketua_rw_melihat_menu_persetujuan_tanpa_persetujuan_keuangan_yang_disembunyikan(): void
    {
        $user = $this->buatUser('Ketua RW');
        $html = $this->actingAs($user)->get(route('rw.dashboard'))->assertOk()->getContent();
        $this->assertStringContainsString('sidebar-submenu', $html);
        $this->assertStringContainsString('Persetujuan Dokumen', $html);
        $this->assertStringContainsString('Pengajuan Data', $html);
        // Menu Persetujuan Keuangan di RW di-comment sementara (rute tetap ada).
        $this->assertStringNotContainsString('Persetujuan Keuangan', $html);
        $this->assertStringNotContainsString(route('rw.persetujuan-keuangan.index'), $html);
    }
    public function test_admin_rw_juga_tidak_melihat_persetujuan_keuangan_di_sidebar_dan_dashboard(): void
    {
        $user = $this->buatUser('Admin RW');
        $html = $this->actingAs($user)->get(route('rw.dashboard'))->assertOk()->getContent();
        $this->assertStringContainsString('Persetujuan Dokumen', $html);
        $this->assertStringNotContainsString('Persetujuan Keuangan', $html);
        $this->assertStringNotContainsString(route('rw.persetujuan-keuangan.index'), $html);
    }
    public function test_alias_peran_pimpinan_tidak_dipakai_lagi_di_menu(): void
    {
        // "Pimpinan" bukan peran nyata (akun pimpinan berperan Ketua RW), jadi tidak boleh ada di menu.
        $peran = collect(config('sidebarMenu'))
            ->flatMap(fn ($m) => array_merge($m['roles'] ?? [], collect($m['children'] ?? [])->flatMap(fn ($c) => $c['roles'] ?? [])->all()));
        $this->assertNotContains('Pimpinan', $peran->all());
    }

    /** Judul menu utama untuk satu peran, sesuai urutan config. */
    private function menuUtama(string $role): array
    {
        return collect(config('sidebarMenu'))
            ->filter(fn ($m) => ($m['position'] ?? 'main') === 'main' && in_array($role, $m['roles'] ?? [], true))
            ->pluck('title')->values()->all();
    }

    public function test_urutan_menu_konsisten_per_kelompok_untuk_setiap_peran(): void
    {
        $this->assertSame(
            ['Dashboard', 'Manajemen Penduduk', 'Manajemen RT', 'Persetujuan', 'Laporan Keuangan', 'Rekap Iuran RT', 'Pusat Informasi', 'Pojok UMKM'],
            $this->menuUtama('Admin RW')
        );
        $this->assertSame($this->menuUtama('Admin RW'), $this->menuUtama('Ketua RW'));
        $this->assertSame(
            ['Dashboard', 'Manajemen Penduduk', 'Persetujuan', 'Laporan Keuangan', 'Pusat Informasi', 'Pojok UMKM'],
            $this->menuUtama('Ketua RT')
        );
        $this->assertSame(
            ['Dashboard', 'Profil Keluarga', 'Permohonan Surat', 'Iuran Warga', 'Laporan Keuangan', 'Pusat Informasi', 'Agenda Warga', 'Pojok UMKM'],
            $this->menuUtama('Warga')
        );
        $this->assertSame(
            ['Dashboard', 'Berita', 'Agenda', 'Pengumuman', 'Kategori Konten', 'Galeri Dokumentasi', 'Pojok UMKM'],
            $this->menuUtama('Op Konten RW')
        );
        $this->assertSame(
            ['Dashboard', 'Catat Transaksi', 'Laporan Keuangan', 'Iuran Warga', 'Kategori Transaksi', 'Pengaturan Rekening'],
            $this->menuUtama('Op Keuangan RT')
        );
        $this->assertSame(
            ['Dashboard', 'Kepengurusan', 'Kategori Transaksi', 'Pojok UMKM', 'Pengaturan Sistem', 'Pengaturan Halaman Utama', 'Manajemen Hak Akses', 'Log Aktivitas'],
            $this->menuUtama('Admin Aplikasi')
        );

        // Setiap peran yang punya Pojok UMKM menaruhnya di posisi terakhir kelompok umum (sebelum menu sistem).
        foreach (['Admin RW', 'Ketua RW', 'Ketua RT', 'Warga', 'Op Konten RW', 'Op Konten RT'] as $role) {
            $menu = $this->menuUtama($role);
            $this->assertSame('Pojok UMKM', end($menu), "Pojok UMKM harus menu terakhir untuk {$role}.");
        }
    }

    public function test_admin_rw_melihat_persetujuan_umkm_di_submenu_dan_pojok_umkm(): void
    {
        $html = $this->actingAs($this->buatUser('Admin RW'))->get(route('rw.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('Persetujuan UMKM', $html);
        $this->assertStringContainsString(route('rw.persetujuan-umkm'), $html);
        $this->assertStringContainsString('Pojok UMKM', $html);
        $this->assertStringNotContainsString('>UMKM<', $html);
    }
}
