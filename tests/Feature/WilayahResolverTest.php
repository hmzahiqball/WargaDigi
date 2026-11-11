<?php

namespace Tests\Feature;

use App\Models\Wilayah;
use App\Services\WilayahResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Derivasi wilayah dari NIK/No.KK + fuzzy match kelurahan.
 */
class WilayahResolverTest extends TestCase
{
    use RefreshDatabase;

    private function seedWilayah(): void
    {
        Wilayah::insert([
            ['kode' => '32', 'nama' => 'Jawa Barat'],
            ['kode' => '32.04', 'nama' => 'Kabupaten Bandung'],
            ['kode' => '32.04.13', 'nama' => 'Banjaran'],
            ['kode' => '32.04.13.2001', 'nama' => 'Tarajusari'],
            ['kode' => '32.04.13.2002', 'nama' => 'Banjaran'],
            ['kode' => '32.04.13.2003', 'nama' => 'Kamasan'],
        ]);
    }

    public function test_derive_wilayah_dari_nik(): void
    {
        $this->seedWilayah();
        $r = new WilayahResolver();
        $w = $r->dariKode('3204130601930011');

        $this->assertSame('Jawa Barat', $w['provinsi']);
        $this->assertSame('Kabupaten Bandung', $w['kabupaten']);
        $this->assertSame('Banjaran', $w['kecamatan']);
        $this->assertSame('32.04.13', $w['kode_kecamatan']);
    }

    public function test_kode_tidak_valid_mengembalikan_kosong(): void
    {
        $this->seedWilayah();
        $r = new WilayahResolver();
        $w = $r->dariKode('9999990000000000');

        $this->assertNull($w['kecamatan']);
    }

    public function test_daftar_kelurahan_per_kecamatan(): void
    {
        $this->seedWilayah();
        $r = new WilayahResolver();
        $list = $r->kelurahanDiKecamatan('32.04.13');

        $this->assertCount(3, $list);
        $this->assertSame('Tarajusari', $list[0]['nama']);
    }

    public function test_fuzzy_match_kelurahan(): void
    {
        $this->seedWilayah();
        $r = new WilayahResolver();

        $this->assertSame('Tarajusari', $r->cocokkanKelurahan('TARAJUSARI', '32.04.13'));
        $this->assertSame('Tarajusari', $r->cocokkanKelurahan('TARAJUSAR', '32.04.13')); // typo
        $this->assertNull($r->cocokkanKelurahan('XYZ TIDAK ADA', '32.04.13'));
    }

    public function test_prediksi_kelurahan_dari_teks_mentah(): void
    {
        $this->seedWilayah();
        $r = new WilayahResolver();

        // Nama kelurahan muncul utuh di teks OCR mentah → terdeteksi walau field kelurahan kosong.
        $mentah = "KARTU KELUARGA\nKP. PANGKALAN TARAJUSARI BANJARAN\nJAWA BARAT";
        $this->assertSame('Tarajusari', $r->cocokkanKelurahan(null, '32.04.13', $mentah));

        // Tidak ada yang mirip & tidak muncul di teks → null (dikosongkan, pilih manual).
        $this->assertNull($r->cocokkanKelurahan(null, '32.04.13', 'teks acak tanpa nama kelurahan'));
    }
}
