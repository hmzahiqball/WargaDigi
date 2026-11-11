<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Konfirmasi dan pemberitahuan memakai dialog (modal), bukan popup bawaan browser.
 */
class DialogKonfirmasiTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<int,string> baris "berkas:nomor" yang masih memakai confirm()/alert() bawaan. */
    private function popupBawaan(): array
    {
        $temuan = [];
        $akar = resource_path('views');
        $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($akar, \FilesystemIterator::SKIP_DOTS));

        foreach ($iter as $berkas) {
            if (! str_ends_with($berkas->getFilename(), '.php')) {
                continue;
            }
            // Komponen dialog sendiri menyebut kedua fungsi itu pada komentar.
            if ($berkas->getFilename() === 'alertModal.blade.php') {
                continue;
            }
            foreach (file($berkas->getPathname()) as $no => $baris) {
                if (preg_match('/(?<![\w.$])(confirm|alert)\s*\(/', $baris)) {
                    $temuan[] = str_replace($akar . DIRECTORY_SEPARATOR, '', $berkas->getPathname()) . ':' . ($no + 1);
                }
            }
        }

        return $temuan;
    }

    public function test_tidak_ada_confirm_atau_alert_bawaan_browser_di_view(): void
    {
        $this->assertSame([], $this->popupBawaan(), 'Gunakan data-konfirmasi atau tampilkanKonfirmasi()/tampilkanPemberitahuan().');
    }

    public function test_komponen_dialog_dimuat_di_layout_dan_menyediakan_fungsi_global(): void
    {
        $komponen = file_get_contents(resource_path('views/components/alertModal.blade.php'));

        $this->assertStringContainsString('id="modalDialogUmum"', $komponen);
        $this->assertStringContainsString('window.tampilkanKonfirmasi', $komponen);
        $this->assertStringContainsString('window.tampilkanPemberitahuan', $komponen);
        // alert() bawaan dialihkan ke dialog sebagai pengaman.
        $this->assertStringContainsString('window.alert = function', $komponen);
        $this->assertStringContainsString("@include('components.alertModal')", file_get_contents(resource_path('views/layouts/global.blade.php')));
    }

    public function test_aksi_hapus_dan_pengajuan_memakai_atribut_data_konfirmasi(): void
    {
        $semua = [
            'opKeuangan/laporan.blade.php' => 3,
            'opKeuangan/partials/_aksi-transaksi.blade.php' => 1,
            'opKeuangan/setoran-rt.blade.php' => 1,
            'opKeuangan/dashboard-rt.blade.php' => 1,
            'kategori-transaksi/index.blade.php' => 1,
        ];

        foreach ($semua as $berkas => $minimal) {
            $isi = file_get_contents(resource_path('views/' . $berkas));
            $this->assertGreaterThanOrEqual($minimal, substr_count($isi, 'data-konfirmasi="'), $berkas);
            $this->assertStringNotContainsString('return confirm', $isi, $berkas);
        }
    }

    public function test_halaman_laporan_operator_memuat_dialog_dan_atribut_konfirmasi(): void
    {
        $op = User::create([
            'nik' => '3217010101011101', 'username' => 'oprw_dialog', 'role' => 'Op Keuangan RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);

        $this->actingAs($op)->get(route('opkeuangan.laporan.index'))
            ->assertOk()
            ->assertSee('modalDialogUmum', false)
            ->assertDontSee('return confirm', false);
    }
}