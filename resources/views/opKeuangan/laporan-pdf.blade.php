<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $laporan->judul }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.4;
            background: #fff;
        }

        .container {
            padding: 35px 45px;
        }

        /* Header Formatted */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header .subtitle {
            font-size: 11px;
            color: #444;
            margin-bottom: 2px;
        }

        /* Report Title */
        .report-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .report-title h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .report-title .periode {
            font-size: 11px;
            color: #555;
            font-weight: bold;
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .data-table thead th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            vertical-align: middle;
        }

        .data-table tbody td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10px;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #fdfdfd;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .col-pemasukan { color: #0f5132; }
        .col-pengeluaran { color: #842029; }

        /* Summary Box */
        .summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .summary-box td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 11px;
            font-weight: bold;
        }

        .summary-label {
            background-color: #f0f0f0;
            text-align: left;
            width: 25%;
        }

        .summary-value {
            text-align: right;
            width: 25%;
        }

        .saldo-akhir-row td {
            background-color: #e8f5e9;
            font-size: 12px;
        }

        /* Signature Area */
        .signature-area {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border: none;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            border: none;
            padding: 5px;
        }
        .signature-name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* Footer */
        .footer-note {
            margin-top: 30px;
            font-size: 9px;
            color: #666;
            font-style: italic;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            @if($laporan->unit === 'RT' && $rtLabel)
                <h1>RUKUN TETANGGA (RT) {{ $rtLabel }} RUKUN WARGA (RW) 21</h1>
            @elseif($laporan->unit === 'DKM')
                <h1>DEWAN KELUARGA MASJID (DKM) RUKUN WARGA (RW) 21</h1>
            @else
                <h1>RUKUN WARGA (RW) 21</h1>
            @endif
            <div class="subtitle">Perumahan Tanimulya Indah</div>
            <div class="subtitle">Desa Tanimulya, Kec. Ngamprah, Kab. Bandung Barat</div>
        </div>

        {{-- Report Title --}}
        <div class="report-title">
            <h2>{{ $laporan->judul }}</h2>
            <div class="periode">Periode Bulan: {{ $namaBulan }} {{ $laporan->periode_tahun }}</div>
        </div>

        {{-- Data Table --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 36%;">Keterangan</th>
                    <th style="width: 22%;">Pemasukan (Rp)</th>
                    <th style="width: 22%;">Pengeluaran (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">01 {{ substr($namaBulan, 0, 3) }}</td>
                    <td><strong>Saldo Bulan Sebelumnya</strong></td>
                    <td class="text-right"><strong>{{ number_format($laporan->saldo_awal, 0, ',', '.') }}</strong></td>
                    <td class="text-right">-</td>
                </tr>

                @foreach($transaksiList as $tx)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $tx->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $tx->judul }}</td>
                    <td class="text-right col-pemasukan">
                        {{ $tx->tipe === 'pemasukan' ? number_format($tx->jumlah, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right col-pengeluaran">
                        {{ $tx->tipe === 'pengeluaran' ? number_format($tx->jumlah, 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Summary Box --}}
        <table class="summary-box">
            <tr>
                <td class="summary-label">Total Pemasukan Bulan Ini</td>
                <td class="summary-value col-pemasukan">{{ number_format($laporan->total_pemasukan, 0, ',', '.') }}</td>
                <td class="summary-label">Total Pengeluaran Bulan Ini</td>
                <td class="summary-value col-pengeluaran">{{ number_format($laporan->total_pengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr class="saldo-akhir-row">
                <td colspan="3" style="text-align: right; background-color: #e8f5e9;"><strong>SALDO AKHIR KAS</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($laporan->saldo_akhir, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        {{-- Signatures --}}
        <div class="signature-area">
            <table class="signature-table">
                <tr>
                    <td>
                        Disetujui Oleh,<br>
                        <strong>Ketua {{ $laporan->unit === 'RT' ? 'RT '.$rtLabel : ($laporan->unit === 'DKM' ? 'RW 21' : $laporan->unit) }}</strong>
                        <div class="signature-name">
                            {{ $laporan->penyetuju->name ?? '.........................................' }}
                        </div>
                    </td>
                    <td>
                        Dibuat Oleh,<br>
                        <strong>Bendahara {{ $laporan->unit === 'RT' ? 'RT '.$rtLabel : $laporan->unit }}</strong>
                        <div class="signature-name">
                            {{ $laporan->pembuat->name ?? '.........................................' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Footer Note --}}
        <div class="footer-note">
            Dicetak otomatis dari WargaDigi pada {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
