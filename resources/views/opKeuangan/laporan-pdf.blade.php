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
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .container {
            padding: 40px 50px;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header .subtitle {
            font-size: 11px;
            color: #555;
        }

        /* Report Title */
        .report-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .report-title h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .report-title .periode {
            font-size: 11px;
            color: #666;
            font-style: italic;
        }

        /* Info Text */
        .info-text {
            font-size: 11px;
            margin-bottom: 15px;
            color: #444;
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .data-table thead th {
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            padding: 10px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }

        .data-table tbody td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            font-size: 11px;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        /* Saldo Akhir Row */
        .saldo-row td {
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #ccc;
            padding: 10px 12px;
        }

        /* Footer Note */
        .footer-note {
            margin-top: 20px;
            font-size: 10px;
            color: #888;
            font-style: italic;
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
            <div class="subtitle">Desa Tanimulya, Kec. Ngamprah, Kab. Bandung Barat</div>
        </div>

        {{-- Report Title --}}
        <div class="report-title">
            <h2>{{ $laporan->judul }}</h2>
            <div class="periode">Periode: {{ $namaBulan }} {{ $laporan->periode_tahun }}</div>
        </div>

        {{-- Info Text --}}
        <div class="info-text">
            Berikut adalah laporan transparansi keuangan kas {{ $laporan->unit }} untuk periode yang bersangkutan:
        </div>

        {{-- Data Table --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 35%;">Keterangan</th>
                    <th style="width: 20%;">Jenis</th>
                    <th style="width: 30%;" class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @if($laporan->saldo_awal > 0)
                <tr>
                    <td>01 {{ substr($namaBulan, 0, 3) }}</td>
                    <td>Saldo Awal</td>
                    <td>Pemasukan</td>
                    <td class="text-right">Rp {{ number_format($laporan->saldo_awal, 0, ',', '.') }}</td>
                </tr>
                @endif

                @foreach($transaksiList as $tx)
                <tr>
                    <td>{{ $tx->tanggal->format('d M') }}</td>
                    <td>{{ $tx->judul }}</td>
                    <td>{{ $tx->tipe === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                    <td class="text-right">Rp {{ number_format($tx->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach

                {{-- Saldo Akhir --}}
                <tr class="saldo-row">
                    <td colspan="3" class="text-right">Saldo Akhir</td>
                    <td class="text-right">Rp {{ number_format($laporan->saldo_akhir, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Footer Note --}}
        <div class="footer-note">
            *Catatan: Ini adalah dokumen yang digenerate secara otomatis dari sistem WargaDigi.
        </div>
    </div>
</body>
</html>
