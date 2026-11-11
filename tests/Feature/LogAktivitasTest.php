<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Hapus log aktivitas berdasarkan rentang tanggal.
 */
class LogAktivitasTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('Admin Aplikasi', 'web');
        $admin = User::create([
            'nik' => null,
            'username' => 'superadmin_log',
            'role' => 'Admin Aplikasi',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
        $admin->syncRoles(['Admin Aplikasi']);

        return $admin;
    }

    private function buatLog(string $tanggal): Activity
    {
        $log = Activity::create([
            'log_name' => 'pengguna',
            'description' => 'Uji log ' . $tanggal,
        ]);
        // Set created_at secara eksplisit (bypass timestamps otomatis).
        Activity::where('id', $log->id)->update(['created_at' => $tanggal . ' 10:00:00']);

        return $log->fresh();
    }

    public function test_hapus_log_pada_rentang_hanya_menghapus_yang_termasuk(): void
    {
        $admin = $this->admin();
        $luarAwal = $this->buatLog('2026-01-01');
        $dalam1   = $this->buatLog('2026-02-10');
        $dalam2   = $this->buatLog('2026-02-20');
        $luarAkhir = $this->buatLog('2026-03-05');

        $this->actingAs($admin)
            ->delete(route('admin.log-aktivitas.hapus'), [
                'dari' => '2026-02-01',
                'sampai' => '2026-02-28',
            ])
            ->assertRedirect(route('admin.log-aktivitas'))
            ->assertSessionHas('success');

        // Hanya log dalam rentang yang terhapus.
        $this->assertDatabaseMissing('activity_log', ['id' => $dalam1->id]);
        $this->assertDatabaseMissing('activity_log', ['id' => $dalam2->id]);
        $this->assertDatabaseHas('activity_log', ['id' => $luarAwal->id]);
        $this->assertDatabaseHas('activity_log', ['id' => $luarAkhir->id]);
    }

    public function test_rentang_inklusif_sampai_akhir_hari(): void
    {
        $admin = $this->admin();
        $soreHariTerakhir = $this->buatLog('2026-02-28'); // 10:00, dalam hari terakhir

        $this->actingAs($admin)
            ->delete(route('admin.log-aktivitas.hapus'), [
                'dari' => '2026-02-01',
                'sampai' => '2026-02-28',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('activity_log', ['id' => $soreHariTerakhir->id]);
    }

    public function test_tanggal_akhir_sebelum_mulai_ditolak(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.log-aktivitas.hapus'), [
                'dari' => '2026-02-28',
                'sampai' => '2026-02-01',
            ])
            ->assertSessionHasErrors('sampai');
    }

    public function test_tanggal_wajib_diisi(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.log-aktivitas.hapus'), [])
            ->assertSessionHasErrors(['dari', 'sampai']);
    }

    public function test_rentang_tanpa_log_memberi_pesan_error(): void
    {
        $admin = $this->admin();
        $this->buatLog('2026-05-01');

        $this->actingAs($admin)
            ->delete(route('admin.log-aktivitas.hapus'), [
                'dari' => '2026-01-01',
                'sampai' => '2026-01-31',
            ])
            ->assertSessionHas('error');
    }
}
