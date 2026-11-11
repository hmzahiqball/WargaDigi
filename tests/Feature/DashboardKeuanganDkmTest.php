<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MasterRt;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Dashboard Operator DKM: hanya kas DKM, tanpa kas RW/RT. */
class DashboardKeuanganDkmTest extends TestCase
{
    use RefreshDatabase;

    private User $dkm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dkm = User::create([
            'nik' => '3217010101012301', 'username' => 'dkm971', 'role' => 'DKM',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
    }

    private function trx(string $unit, string $tipe, int $jumlah, string $kategori = 'Umum', string $status = 'Verified'): void
    {
        static $n = 0;
        $n++;
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-DKM-' . $n, 'tipe' => $tipe, 'kategori' => $kategori, 'judul' => 'Uji DKM ' . $n,
            'jumlah' => $jumlah, 'tanggal' => now()->toDateString(), 'status' => $status,
            'unit_sumber' => $unit, 'rt_id' => null,
        ]);
    }

    public function test_dashboard_dkm_hanya_menampilkan_kas_dkm(): void
    {
        $this->trx('DKM', 'pemasukan', 700000, 'Infaq Jumat');
        $this->trx('DKM', 'pengeluaran', 200000, 'Kebersihan Masjid');
        $this->trx('DKM', 'pengeluaran', 999000, 'Kebersihan Masjid', 'Rejected'); // tidak dihitung
        $this->trx('RW', 'pemasukan', 8000000);                                      // kas RW tidak tampil

        $this->actingAs($this->dkm)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Keuangan DKM')
            ->assertSee('Rp 500.000')
            ->assertDontSee('8.000.000')
            ->assertDontSee('Sheet Saldo')
            ->assertSee('Infaq Jumat')
            ->assertSee('Kebersihan Masjid');
    }

    public function test_dashboard_dkm_menampilkan_perlu_tindakan_pengeluaran_menunggu(): void
    {
        $this->trx('DKM', 'pengeluaran', 150000, 'Listrik', 'Pending');

        $this->actingAs($this->dkm)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertSee('Pengeluaran menunggu persetujuan')
            ->assertSee('Rp 150.000');
    }
}
