<?php

namespace App\Exports;

use App\Models\LaporanKeuangan;
use App\Models\TransaksiKeuangan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Ekspor laporan keuangan dalam format buku kas (Excel), dipakai oleh unit DKM, RT, dan RW:
 * header info, saldo awal, transaksi dengan saldo berjalan (rumus), total & saldo akhir (rumus),
 * lalu rekap per kategori (SUMIF). Nominal diformat rupiah dan tanggal berupa tanggal Excel asli.
 */
class LaporanKeuanganExport implements FromArray, WithTitle, WithEvents
{
    private const FORMAT_RUPIAH = '"Rp" #,##0;[Red]-"Rp" #,##0;"Rp" 0';
    private const FORMAT_TANGGAL = 'dd/mm/yyyy';
    private const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /** Baris kosong yang tetap menempati satu baris (array kosong dibuang library). */
    private const KOSONG = [' ', ' ', ' ', ' ', ' ', ' ', ' '];

    protected LaporanKeuangan $laporan;

    /** Posisi baris yang dibutuhkan untuk styling (diisi saat array() dibangun). */
    private array $pos = [];

    public function __construct(LaporanKeuangan $laporan)
    {
        $this->laporan = $laporan;
    }

    public function title(): string
    {
        return 'Buku Kas';
    }

    /** Teks bebas dari user tidak boleh terbaca sebagai rumus (formula injection). */
    private static function teks(?string $nilai): string
    {
        $nilai = (string) $nilai;

        return $nilai !== '' && $nilai[0] === '=' ? "'" . $nilai : $nilai;
    }

    public function array(): array
    {
        $laporan = $this->laporan;
        $namaBulan = self::BULAN[(int) $laporan->periode_bulan] ?? (string) $laporan->periode_bulan;

        $query = TransaksiKeuangan::whereMonth('tanggal', $laporan->periode_bulan)
            ->whereYear('tanggal', $laporan->periode_tahun)
            ->where('unit_sumber', $laporan->unit)
            ->where('status', '!=', 'Rejected')
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');
        if ($laporan->unit === 'RT') {
            $query->where('rt_id', $laporan->rt_id);
        }
        $transaksi = $query->get();

        $rows = [];
        $baris = function () use (&$rows) { return count($rows) + 1; }; // nomor baris Excel untuk baris yang akan ditambahkan

        $rows[] = [self::teks($laporan->judul)];
        $rows[] = ['Periode', $namaBulan . ' ' . $laporan->periode_tahun];
        $rows[] = ['Unit', $laporan->unit . ($laporan->unit === 'RT' && $laporan->rt ? ' ' . $laporan->rt->kode_rt : '')];
        $rows[] = self::KOSONG;
        $this->pos['header'] = $baris();
        $rows[] = ['No', 'Tanggal', 'Keterangan', 'Kategori', 'Debet (Pemasukan)', 'Kredit (Pengeluaran)', 'Saldo'];

        // Saldo awal.
        $awal = $baris();
        $this->pos['awal'] = $awal;
        $tanggalAwal = \Carbon\Carbon::create((int) $laporan->periode_tahun, (int) $laporan->periode_bulan, 1);
        $rows[] = [1, ExcelDate::dateTimeToExcel($tanggalAwal), 'Saldo Bulan Sebelumnya', '-', null, null, (int) $laporan->saldo_awal];

        // Transaksi: saldo = saldo baris sebelumnya + debet - kredit.
        $no = 2;
        foreach ($transaksi as $tx) {
            $r = $baris();
            $rows[] = [
                $no++,
                ExcelDate::dateTimeToExcel($tx->tanggal),
                self::teks($tx->judul),
                self::teks($tx->kategori ?: 'Lainnya'),
                $tx->tipe === 'pemasukan' ? (int) $tx->jumlah : null,
                $tx->tipe === 'pengeluaran' ? (int) $tx->jumlah : null,
                '=G' . ($r - 1) . '+N(E' . $r . ')-N(F' . $r . ')',
            ];
        }
        $akhirData = $baris() - 1;
        $this->pos['akhirData'] = $akhirData;

        // Total dan saldo akhir (rumus).
        $rows[] = self::KOSONG;
        $totalRow = $baris();
        $this->pos['total'] = $totalRow;
        $rows[] = ['', '', 'TOTAL', '', "=SUM(E{$awal}:E{$akhirData})", "=SUM(F{$awal}:F{$akhirData})", ''];
        $saldoRow = $baris();
        $this->pos['saldoAkhir'] = $saldoRow;
        $rows[] = ['', '', 'SALDO AKHIR KAS', '', '', '', "=G{$awal}+E{$totalRow}-F{$totalRow}"];

        // Rekap per kategori (SUMIF ke tabel transaksi).
        $rows[] = self::KOSONG;
        $this->pos['judulRekapMasuk'] = $baris();
        $rows[] = ['REKAP PEMASUKAN PER KATEGORI'];
        $mulaiMasuk = $baris();
        $dataMasuk = $transaksi->where('tipe', 'pemasukan')
            ->groupBy(fn ($t) => $t->kategori ?: 'Lainnya')->map->sum('jumlah')->sortDesc();
        foreach ($dataMasuk as $kat => $jml) {
            $r = $baris();
            $rows[] = ['', '', self::teks($kat), '', "=SUMIF(\$D\${$awal}:\$D\${$akhirData},C{$r},\$E\${$awal}:\$E\${$akhirData})", '', ''];
        }
        $akhirMasuk = $baris() - 1;
        $this->pos['totalRekapMasuk'] = $baris();
        $rows[] = ['', '', 'Total Pemasukan', '', $dataMasuk->isEmpty() ? 0 : "=SUM(E{$mulaiMasuk}:E{$akhirMasuk})", '', ''];

        $rows[] = self::KOSONG;
        $this->pos['judulRekapKeluar'] = $baris();
        $rows[] = ['REKAP PENGELUARAN PER KATEGORI'];
        $mulaiKeluar = $baris();
        $dataKeluar = $transaksi->where('tipe', 'pengeluaran')
            ->groupBy(fn ($t) => $t->kategori ?: 'Lainnya')->map->sum('jumlah')->sortDesc();
        foreach ($dataKeluar as $kat => $jml) {
            $r = $baris();
            $rows[] = ['', '', self::teks($kat), '', '', "=SUMIF(\$D\${$awal}:\$D\${$akhirData},C{$r},\$F\${$awal}:\$F\${$akhirData})", ''];
        }
        $akhirKeluar = $baris() - 1;
        $this->pos['totalRekapKeluar'] = $baris();
        $rows[] = ['', '', 'Total Pengeluaran', '', '', $dataKeluar->isEmpty() ? 0 : "=SUM(F{$mulaiKeluar}:F{$akhirKeluar})", ''];

        $this->pos['mulaiMasuk'] = $mulaiMasuk;
        $this->pos['akhirMasuk'] = $akhirMasuk;
        $this->pos['mulaiKeluar'] = $mulaiKeluar;
        $this->pos['akhirKeluar'] = $akhirKeluar;
        $this->pos['terakhir'] = count($rows);

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $p = $this->pos;
                $sheet = $event->sheet->getDelegate();
                $hijau = '198754';

                // Lebar kolom tetap agar rapi.
                foreach (['A' => 6, 'B' => 13, 'C' => 42, 'D' => 24, 'E' => 20, 'F' => 22, 'G' => 20] as $kol => $lebar) {
                    $sheet->getColumnDimension($kol)->setWidth($lebar);
                }

                // Judul dan info.
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2:A3')->getFont()->setBold(true);

                // Header tabel.
                $h = $p['header'];
                $sheet->getStyle("A{$h}:G{$h}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $hijau]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                $sheet->getRowDimension($h)->setRowHeight(32);

                // Isi tabel: border, format, perataan.
                $awal = $p['awal'];
                $akhir = $p['akhirData'];
                $sheet->getStyle("A{$h}:G{$akhir}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A{$awal}:A{$akhir}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$awal}:B{$akhir}")->getNumberFormat()->setFormatCode(self::FORMAT_TANGGAL);
                $sheet->getStyle("B{$awal}:B{$akhir}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$awal}:D{$akhir}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle("E{$awal}:G{$akhir}")->getNumberFormat()->setFormatCode(self::FORMAT_RUPIAH);
                $sheet->getStyle("A{$awal}:G{$awal}")->getFont()->setItalic(true);
                $sheet->getStyle("A{$awal}:G{$awal}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');

                // Total dan saldo akhir.
                $t = $p['total'];
                $s = $p['saldoAkhir'];
                $sheet->getStyle("C{$t}:G{$s}")->getFont()->setBold(true);
                $sheet->getStyle("C{$t}:G{$s}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("C{$t}:G{$s}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E9');
                $sheet->getStyle("E{$t}:G{$s}")->getNumberFormat()->setFormatCode(self::FORMAT_RUPIAH);

                // Rekap per kategori.
                foreach ([
                    ['judul' => $p['judulRekapMasuk'], 'mulai' => $p['mulaiMasuk'], 'akhir' => $p['akhirMasuk'], 'total' => $p['totalRekapMasuk'], 'kol' => 'E'],
                    ['judul' => $p['judulRekapKeluar'], 'mulai' => $p['mulaiKeluar'], 'akhir' => $p['akhirKeluar'], 'total' => $p['totalRekapKeluar'], 'kol' => 'F'],
                ] as $g) {
                    $sheet->getStyle("A{$g['judul']}:G{$g['judul']}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $hijau]],
                    ]);
                    $sheet->getStyle("C{$g['mulai']}:G{$g['total']}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("{$g['kol']}{$g['mulai']}:{$g['kol']}{$g['total']}")->getNumberFormat()->setFormatCode(self::FORMAT_RUPIAH);
                    $sheet->getStyle("C{$g['total']}:G{$g['total']}")->getFont()->setBold(true);
                    $sheet->getStyle("C{$g['total']}:G{$g['total']}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E9');
                }

                // Tampilan dan cetak.
                $sheet->freezePane('A' . ($h + 1));
                $sheet->setShowGridlines(false);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getSheetView()->setZoomScale(100);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($h, $h);
            },
        ];
    }
}
