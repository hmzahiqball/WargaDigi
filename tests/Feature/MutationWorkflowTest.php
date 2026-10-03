<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\MutasiWarga;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_rt_only_sees_mutations_from_its_own_area(): void
    {
        $rtOwn = MasterRt::create(['kode_rt' => '001', 'nama_rt' => 'RT 001']);
        $rtOther = MasterRt::create(['kode_rt' => '002', 'nama_rt' => 'RT 002']);
        $familyOwn = $this->createFamily($rtOwn, '3217010101010001', 'Keluarga Sendiri');
        $familyOther = $this->createFamily($rtOther, '3217010101010002', 'Keluarga Lain');

        MutasiWarga::create([
            'keluarga_id' => $familyOwn->id,
            'jenis_mutasi' => 'tambah',
            'data_pengajuan' => ['nama_lengkap' => 'Pengajuan Sendiri'],
            'status' => 'menunggu_rt',
        ]);
        $otherMutation = MutasiWarga::create([
            'keluarga_id' => $familyOther->id,
            'jenis_mutasi' => 'tambah',
            'data_pengajuan' => ['nama_lengkap' => 'Pengajuan Lain'],
            'status' => 'menunggu_rt',
        ]);
        $rtUser = User::create([
            'nik' => '3217010101010099',
            'username' => 'Ketua RT',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rtOwn->id,
            'password' => 'password123',
        ]);

        $this->actingAs($rtUser)
            ->get(route('rt.mutasi.index'))
            ->assertOk()
            ->assertSee('Pengajuan Sendiri')
            ->assertDontSee('Pengajuan Lain');

        $this->actingAs($rtUser)
            ->post(route('rt.mutasi.approve', $otherMutation->id))
            ->assertRedirect()
            ->assertSessionHasErrors('mutasi');
    }

    public function test_rt_cannot_process_a_mutation_after_it_was_rejected(): void
    {
        $rt = MasterRt::create(['kode_rt' => '003', 'nama_rt' => 'RT 003']);
        $family = $this->createFamily($rt, '3217010101010003', 'Keluarga Status');
        $mutation = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'jenis_mutasi' => 'tambah',
            'data_pengajuan' => ['nama_lengkap' => 'Pengajuan Status'],
            'status' => 'ditolak',
        ]);
        $rtUser = User::create([
            'nik' => '3217010101010098',
            'username' => 'Ketua RT Status',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);

        $this->actingAs($rtUser)
            ->post(route('rt.mutasi.approve', $mutation->id))
            ->assertRedirect()
            ->assertSessionHasErrors('mutasi');

        $this->assertDatabaseHas('mutasi_warga', [
            'id' => $mutation->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_rw_rejects_duplicate_nik_before_approval(): void
    {
        $rt = MasterRt::create(['kode_rt' => '004', 'nama_rt' => 'RT 004']);
        $family = $this->createFamily($rt, '3217010101010004', 'Keluarga NIK');
        $mutation = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'jenis_mutasi' => 'tambah',
            'data_pengajuan' => [
                'nama_lengkap' => 'NIK Duplikat',
                'nik' => '3217010101010004',
                'status_hubungan_keluarga' => 'Anak',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2010-01-01',
                'agama' => 'Islam',
                'pendidikan_terakhir' => 'SD/Sederajat',
                'pekerjaan' => 'Pelajar',
                'status_perkawinan' => 'Belum Kawin',
                'kewarganegaraan' => 'WNI',
            ],
            'status' => 'menunggu_rw',
        ]);
        $rwUser = User::create([
            'nik' => '3217010101010097',
            'username' => 'Admin RW NIK',
            'role' => 'Admin RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        $this->actingAs($rwUser)
            ->post(route('rw.mutasi.approve', $mutation->id))
            ->assertRedirect()
            ->assertSessionHasErrors('nik');

        $this->assertDatabaseHas('mutasi_warga', [
            'id' => $mutation->id,
            'status' => 'menunggu_rw',
        ]);
    }

    public function test_editing_identity_requires_a_kk_document(): void
    {
        $rt = MasterRt::create(['kode_rt' => '005', 'nama_rt' => 'RT 005']);
        $family = $this->createFamily($rt, '3217010101010005', 'Nama Lama');
        $warga = User::create([
            'nik' => '3217010101010005',
            'username' => 'Warga Dokumen',
            'role' => 'Warga',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        $this->actingAs($warga)
            ->withHeader('Accept', 'application/json')
            ->put(route('warga.keluarga.anggota.update', $family->anggota->first()->id), [
                'pendidikan_terakhir' => 'SMA/Sederajat',
                'pekerjaan' => 'Wiraswasta',
                'status_perkawinan' => 'Belum Kawin',
                'nama_lengkap' => 'Nama Baru',
                'nik' => '3217010101010005',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file_kk');

        $this->assertDatabaseCount('mutasi_warga', 0);
    }

    private function createFamily(MasterRt $rt, string $nik, string $name): Keluarga
    {
        $family = Keluarga::create([
            'no_kk' => $nik,
            'nik_kepala_keluarga' => $nik,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '081234567890',
        ]);
        Penduduk::create([
            'keluarga_id' => $family->id,
            'nik' => $nik,
            'nama_lengkap' => $name,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Belum Kawin',
        ]);

        return $family;
    }
}
