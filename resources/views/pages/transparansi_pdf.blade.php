<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #2E7D32; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; font-weight: bold; font-size: 18px; margin-bottom: 5px; }
        .subtitle { text-align: center; margin-bottom: 30px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h2>RUKUN TETANGGA (RT) 01 RUKUN WARGA (RW) 21</h2>
        <p>Desa Tanimulya, Kec. Ngamprah, Kab. Bandung Barat</p>
    </div>

    <div class="title">{{ $report['title'] }}</div>
    <div class="subtitle">Periode: {{ $report['period'] }}</div>

    <p>Berikut adalah laporan transparansi keuangan kas RT/RW untuk periode yang bersangkutan:</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jenis</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>01 {{ substr($report['period'], 0, 3) }}</td>
                <td>Saldo Awal</td>
                <td>Pemasukan</td>
                <td>Rp 10.000.000</td>
            </tr>
            <tr>
                <td>15 {{ substr($report['period'], 0, 3) }}</td>
                <td>Iuran Warga</td>
                <td>Pemasukan</td>
                <td>Rp 3.500.000</td>
            </tr>
            <tr>
                <td>20 {{ substr($report['period'], 0, 3) }}</td>
                <td>Biaya Kebersihan</td>
                <td>Pengeluaran</td>
                <td>Rp 1.500.000</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Saldo Akhir</td>
                <td style="font-weight: bold;">Rp 12.000.000</td>
            </tr>
        </tbody>
    </table>

    <p><em>*Catatan: Ini adalah dokumen yang digenerate secara otomatis dari sistem WargaDigi.</em></p>
</body>
</html>
