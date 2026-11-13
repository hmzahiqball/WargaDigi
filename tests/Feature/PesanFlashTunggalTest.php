<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi: pesan flash sesi (success/error/warning) hanya ditampilkan sekali,
 * lewat dialog global layout (components.alertModal), tidak diulang oleh alert/toast halaman.
 */
class PesanFlashTunggalTest extends TestCase
{
    use RefreshDatabase;

    private function adminRw(): User
    {
        return User::create([
            'nik' => '3217010101010002', 'username' => 'Admin RW', 'role' => 'Admin RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
    }

    public static function halamanRw(): array
    {
        return [
            'persetujuan dokumen' => ['rw.persetujuan-dokumen'],
            'mutasi warga' => ['rw.mutasi.index'],
            'master rt' => ['rw.master-rt.index'],
            'dashboard' => ['rw.dashboard'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('halamanRw')]
    public function test_pesan_sukses_dan_galat_hanya_muncul_sekali(string $route): void
    {
        $html = $this->actingAs($this->adminRw())
            ->withSession(['success' => 'PesanSuksesUnikUji', 'error' => 'PesanGalatUnikUji', 'warning' => 'PesanPeringatanUnikUji'])
            ->get(route($route))
            ->assertOk()
            ->getContent();

        $this->assertSame(1, substr_count($html, 'PesanSuksesUnikUji'), 'Pesan sukses harus tampil sekali (dialog).');
        $this->assertSame(1, substr_count($html, 'PesanGalatUnikUji'), 'Pesan galat harus tampil sekali (dialog).');
        $this->assertSame(1, substr_count($html, 'PesanPeringatanUnikUji'), 'Pesan peringatan harus tampil sekali (dialog).');
        // Galat tampil sebagai dialog, bukan toast.
        $this->assertStringContainsString('tampilkanPemberitahuan("PesanGalatUnikUji"', $html);
        $this->assertStringNotContainsString('showAdminToast("PesanGalatUnikUji"', $html);
    }
}
