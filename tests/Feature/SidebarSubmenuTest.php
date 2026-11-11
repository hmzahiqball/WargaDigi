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

    public function test_ketua_rw_melihat_menu_persetujuan_dengan_tiga_submenu(): void
    {
        $user = $this->buatUser('Ketua RW');

        $html = $this->actingAs($user)->get(route('rw.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('sidebar-submenu', $html);
        $this->assertStringContainsString('Persetujuan Dokumen', $html);
        $this->assertStringContainsString('Persetujuan Keuangan', $html);
        $this->assertStringContainsString('Pengajuan Data', $html);
    }

    public function test_pimpinan_punya_menu_persetujuan_induk_dengan_satu_submenu(): void
    {
        // Pimpinan bukan nilai enum kolom users.role, jadi diverifikasi lewat config
        // menu: harus ada menu induk "Persetujuan" dengan satu submenu keuangan.
        $menus = collect(config('sidebarMenu'))->filter(function ($m) {
            return ($m['title'] ?? '') === 'Persetujuan' && in_array('Pimpinan', $m['roles'] ?? []);
        });

        $this->assertCount(1, $menus, 'Pimpinan harus punya satu menu induk "Persetujuan".');

        $induk = $menus->first();
        $this->assertArrayHasKey('children', $induk);
        $this->assertCount(1, $induk['children']);
        $this->assertSame('Persetujuan Keuangan', $induk['children'][0]['title']);
    }
}
