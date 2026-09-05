<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; font-size: 14px; margin: 0 40px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        .content { text-align: justify; }
        .ttd { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h2 style="margin:0;">PENGURUS RUKUN TETANGGA (RT) 01 RUKUN WARGA (RW) 21</h2>
        <h3 style="margin:0;">DESA TANIMULYA, KEC. NGAMPRAH, KAB. BANDUNG BARAT</h3>
    </div>

    <div class="title">
        SURAT KETERANGAN<br>
        Nomor: {{ $surat->nomor_surat ?? '---/RT.01/RW.21/2026' }}
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini Ketua RT 01 / RW 21 Desa Tanimulya, dengan ini menerangkan bahwa:</p>
        
        <table style="margin-left: 20px; margin-bottom: 20px;">
            <tr><td width="150">Nama Lengkap</td><td>: {{ $surat->penduduk->nama_lengkap ?? auth()->user()->name }}</td></tr>
            <tr><td>NIK</td><td>: {{ $surat->nik }}</td></tr>
            <tr><td>Tempat/Tgl Lahir</td><td>: {{ $surat->penduduk->tempat_lahir ?? '-' }}, {{ $surat->penduduk->tanggal_lahir ? $surat->penduduk->tanggal_lahir->format('d-m-Y') : '-' }}</td></tr>
            <tr><td>Jenis Kelamin</td><td>: {{ $surat->penduduk->jenis_kelamin ?? '-' }}</td></tr>
            <tr><td>Pekerjaan</td><td>: {{ $surat->penduduk->pekerjaan ?? '-' }}</td></tr>
            <tr><td>Keperluan</td><td>: {{ $surat->keperluan }}</td></tr>
        </table>

        <p>Bahwa yang bersangkutan benar-benar warga yang bertempat tinggal di lingkungan RT 01 / RW 21 Desa Tanimulya, dan surat keterangan ini dibuat untuk keperluan pengurusan <strong>{{ $surat->jenis_surat }}</strong>.</p>
        <p>Demikian surat keterangan ini kami buat agar dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="ttd">
        Tanimulya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
        Ketua RT 01<br><br><br><br>
        ( ___________________ )
    </div>

</body>
</html>
