<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resi Pembayaran - {{ $pembayaran->tagihan->judul }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #28a745; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; text-decoration: underline; }
        .table-info { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-info th { text-align: left; padding: 8px; width: 35%; color: #555; vertical-align: top; }
        .table-info td { padding: 8px; border-bottom: 1px dashed #ddd; vertical-align: top; }
        .badge { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .footer { margin-top: 50px; text-align: right; }
        .signature-area { display: inline-block; text-align: center; margin-top: 20px; }
        .stamp { color: #28a745; border: 2px solid #28a745; border-radius: 5px; padding: 10px; transform: rotate(-5deg); display: inline-block; font-weight: bold; font-size: 20px; margin-bottom: 10px; opacity: 0.7; }
    </style>
</head>
<body>
    <div class="header">
        <h2>WARGADIGI 21</h2>
        <p>Sistem Informasi dan Manajemen Keuangan Warga</p>
    </div>

    <div class="title">RESI BUKTI PEMBAYARAN</div>

    <table class="table-info">
        <tr>
            <th>Nomor Referensi</th>
            <td><strong>#WD-{{ strtoupper(substr($pembayaran->id, 0, 8)) }}</strong></td>
        </tr>
        <tr>
            <th>Tanggal Pembayaran</th>
            <td>{{ \Carbon\Carbon::parse($pembayaran->updated_at)->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span class="badge">LUNAS</span></td>
        </tr>
    </table>

    <h4 style="margin-bottom: 10px; color: #444; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Rincian Tagihan</h4>
    <table class="table-info">
        <tr>
            <th>Untuk Pembayaran</th>
            <td>{{ $pembayaran->tagihan->judul }}</td>
        </tr>
        <tr>
            <th>Periode</th>
            <td>{{ \Carbon\Carbon::create(null, $pembayaran->tagihan->periode_bulan)->translatedFormat('F') }} {{ $pembayaran->tagihan->periode_tahun }}</td>
        </tr>
        <tr>
            <th>Total Dibayar</th>
            <td><strong style="font-size: 16px;">{{ $pembayaran->tagihan->formatted_nominal }}</strong></td>
        </tr>
        <tr>
            <th>Metode Pembayaran</th>
            <td>{{ $pembayaran->metode ?? 'Cash' }}</td>
        </tr>
    </table>

    <h4 style="margin-bottom: 10px; color: #444; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Data Warga</h4>
    <table class="table-info">
        <tr>
            <th>Nama Penyetor</th>
            <td>{{ $pembayaran->warga->nama_lengkap ?? ($pembayaran->keluarga->kepalaKeluarga->nama_lengkap ?? '-') }}</td>
        </tr>
        <tr>
            <th>Atas Nama Keluarga</th>
            <td>Keluarga Bpk/Ibu {{ $pembayaran->keluarga->kepalaKeluarga->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <th>Alamat / Blok</th>
            <td>{{ $pembayaran->keluarga->alamat }}</td>
        </tr>
    </table>

    <div class="footer">
        <div class="signature-area">
            <div class="stamp">LUNAS</div>
            <p style="margin: 0;">Disahkan oleh,</p>
            <p style="margin: 5px 0 0; font-weight: bold;">Bendahara {{ $pembayaran->tagihan->unit }}</p>
            <p style="margin: 0; font-size: 12px; color: #777;">(Digital Signature via WargaDigi)</p>
        </div>
    </div>
</body>
</html>
