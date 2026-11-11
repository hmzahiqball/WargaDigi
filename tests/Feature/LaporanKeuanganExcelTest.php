<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exports\LaporanKeuanganExport;
use App\Models\LaporanKeuangan;
use App\Models\MasterRt;
use App\Models\TransaksiKeuangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Excel laporan keuangan: nominal terformat, saldo/total/rekap berupa rumus yang menghasilkan angka benar. */
class LaporanKeuanganExcelTest extends TestCase
{
    use RefreshDatabase;

    private function laporan(string $unit, ?string $rtId = null): LaporanKeuangan
    {
        static $n = 0;
        $n++;
        $tahun = (int) now()->format('Y');
        $bulan = (int) now()->format('n');
        $tgl = now()->startOfMonth()->toDateString();
        foreach ([['pemasukan', 500000, 'Infaq'], ['pemasukan', 250000, 'Infaq'], ['pemasukan', 100000, ''], ['pengeluaran', 150000, 'Operasional']] as $i => [$tipe, $jml, $kat]) {
            TransaksiKeuangan::create([
                'kode_transaksi' => "TRX-XL-{$n}-{$i}", 'tipe' => $tipe, 'kategori' => $kat, 'judul' => "Tx {$i}",
                'jumlah' => $jml, 'tanggal' => $tgl, 'status' => 'Verified', 'unit_sumber' => $unit, 'rt_id' => $rtId,
            ]);
        }
        // Transaksi unit lain dan Rejected tidak boleh ikut.
        TransaksiKeuangan::create([
            'kode_transaksi' => "TRX-XL-{$n}-x", 'tipe' => 'pemasukan', 'kategori' => 'Lain', 'judul' => 'Unit lain',
            'jumlah' => 9000000, 'tanggal' => $tgl, 'status' => 'Verified', 'unit_sumber' => $unit === 'RW' ? 'DKM' : 'RW', 'rt_id' => null,
        ]);

        return LaporanKeuangan::create([
            'unit' => $unit, 'rt_id' => $rtId, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun,
            'judul' => "Laporan {$unit}", 'saldo_awal' => 1000000, 'total_pemasukan' => 850000, 'total_pengeluaran' => 150000,
            'saldo_akhir' => 1700000, 'status' => 'Draft',
        ]);
    }

    private function sheet(LaporanKeuangan $laporan)
    {
        $path = tempnam(sys_get_temp_dir(), 'xl') . '.xlsx';
        file_put_contents($path, Excel::raw(new LaporanKeuanganExport($laporan), \Maatwebsite\Excel\Excel::XLSX));
        $book = IOFactory::load($path);
        @unlink($path);

        return $book->getActiveSheet();
    }

    #[DataProvider('unitProvider')]
    public function test_rumus_dan_format_untuk_setiap_unit(string $unit): void
    {
        $rtId = $unit === 'RT' ? MasterRt::create(['kode_rt' => '981', 'nama_rt' => 'RT 981'])->id : null;
        $sheet = $this->sheet($this->laporan($unit, $rtId));

        // Baris 6 saldo awal; 7-10 transaksi; 12 total; 13 saldo akhir.
        $this->assertSame(1000000, (int) $sheet->getCell('G6')->getCalculatedValue());
        $this->assertStringStartsWith('=G6', (string) $sheet->getCell('G7')->getValue());      // saldo berjalan = rumus
        $this->assertSame(1850000, (int) $sheet->getCell('G9')->getCalculatedValue());          // 1.000.000 + 850.000
        $this->assertSame(1700000, (int) $sheet->getCell('G10')->getCalculatedValue());         // dikurangi 150.000

        $this->assertSame('TOTAL', $sheet->getCell('C12')->getValue());
        $this->assertSame('=SUM(E6:E10)', $sheet->getCell('E12')->getValue());
        $this->assertSame(850000, (int) $sheet->getCell('E12')->getCalculatedValue());
        $this->assertSame(150000, (int) $sheet->getCell('F12')->getCalculatedValue());
        $this->assertSame(1700000, (int) $sheet->getCell('G13')->getCalculatedValue());

        // Format rupiah dan tanggal.
        $this->assertStringContainsString('#,##0', $sheet->getStyle('E7')->getNumberFormat()->getFormatCode());
        $this->assertStringContainsString('#,##0', $sheet->getStyle('G13')->getNumberFormat()->getFormatCode());
        $this->assertSame('dd/mm/yyyy', $sheet->getStyle('B7')->getNumberFormat()->getFormatCode());

        // Rekap per kategori via SUMIF; kategori kosong menjadi "Lainnya"; transaksi unit lain tidak ikut.
        $semua = collect($sheet->toArray(null, true, false, false))->flatten()->filter()->map(fn ($v) => (string) $v)->all();
        $this->assertContains('Infaq', $semua);
        $this->assertContains('Lainnya', $semua);
        $this->assertContains('750000', $semua);          // Infaq: 500.000 + 250.000
        $this->assertContains('Total Pemasukan', $semua);
        $this->assertNotContains('9000000', $semua);
    }

    public static function unitProvider(): array
    {
        return ['DKM' => ['DKM'], 'RT' => ['RT'], 'RW' => ['RW']];
    }

    public function test_keterangan_berawalan_sama_dengan_tidak_menjadi_rumus(): void
    {
        $laporan = $this->laporan('DKM');
        TransaksiKeuangan::where('unit_sumber', 'DKM')->first()->update(['judul' => '=1+1']);

        $sheet = $this->sheet($laporan);
        $this->assertNotSame(2, $sheet->getCell('C7')->getCalculatedValue());
    }
}
