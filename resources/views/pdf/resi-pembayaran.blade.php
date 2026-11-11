<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resi Pembayaran - {{ $pembayaran->tagihan->judul }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; line-height: 1.5; }
        @page { margin: 1cm 1.5cm 1cm 1.5cm; }
        .kop { width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 18px; }
        .kop td { vertical-align: middle; }
        .kop-logo { width: 70px; }
        .kop-logo img { width: 62px; height: auto; }
        .kop-text { text-align: center; color: #000; }
        .kop-text h2 { font-size: 16px; margin: 0; font-weight: bold; text-transform: uppercase; }
        .kop-text h3 { font-size: 13px; margin: 2px 0 0; font-weight: bold; text-transform: uppercase; }
        .kop-text p { font-size: 11px; margin: 2px 0 0; font-weight: bold; }
        .ttd { width: 100%; margin-top: 30px; }
        .ttd td { vertical-align: top; }
        .ttd-kotak { text-align: center; width: 230px; }
        .ttd-area { position: relative; height: 90px; }
        .ttd-area img.ttd-img { position: absolute; left: 50%; top: 4px; margin-left: -55px; width: 110px; height: auto; max-height: 80px; }
        .ttd-area img.stempel-img { position: absolute; left: 18px; top: 0; width: 85px; height: auto; opacity: 0.9; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin: 0; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; text-decoration: underline; }
        .table-info { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-info th { text-align: left; padding: 8px; width: 35%; color: #555; vertical-align: top; }
        .table-info td { padding: 8px; border-bottom: 1px dashed #ddd; vertical-align: top; }
        .badge { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .stamp { color: #28a745; border: 2px solid #28a745; border-radius: 5px; padding: 8px 12px; display: inline-block; font-weight: bold; font-size: 18px; opacity: 0.8; }
    </style>
</head>
<body>
    @php $kop = $resi['kop']; $logo = public_path($kop['logo'] ?? 'images/logo_rw21.png'); @endphp
    <table class="kop">
        <tr>
            <td class="kop-logo">
                @if(file_exists($logo))<img src="{{ $logo }}" alt="Logo">@endif
            </td>
            <td class="kop-text">
                <h2>{{ $kop['baris_1'] ?? '' }}</h2>
                <h3>{{ $kop['baris_2'] ?? '' }}</h3>
                <p>{{ $kop['baris_3'] ?? '' }}</p>
            </td>
            <td class="kop-logo"></td>
        </tr>
    </table>

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
            <td><strong style="font-size: 16px;">Rp {{ number_format((int) ($pembayaran->nominal ?: $pembayaran->tagihan->nominal), 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <th>Metode Pembayaran</th>
            <td>{{ $pembayaran->metode ?? 'Cash' }}</td>
        </tr>
        <tr>
            <th>Diterima oleh</th>
            <td>{{ $resi['penerima'] }}@if($resi['via_rt']) <span style="color:#777;font-size:12px;">(iuran dari RW, dikumpulkan lewat RT)</span>@endif</td>
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

    <table class="ttd">
        <tr>
            <td style="width:45%; vertical-align:bottom;">
                <div class="stamp">LUNAS</div>
            </td>
            <td></td>
            <td class="ttd-kotak">
                <p style="margin:0;">{{ $resi['tempat'] }}, {{ $resi['tanggal'] }}</p>
                <p style="margin:2px 0 0;">{{ $resi['jabatan'] }},</p>
                <div class="ttd-area">
                    @if($resi['stempel'])<img class="stempel-img" src="{{ $resi['stempel'] }}" alt="Stempel">@endif
                    @if($resi['ttd'])<img class="ttd-img" src="{{ $resi['ttd'] }}" alt="Tanda tangan">@endif
                </div>
                <p class="ttd-nama">{{ $resi['penandatangan'] ?? '........................' }}</p>
                @unless($resi['ttd'])
                    <p style="margin:2px 0 0; font-size:11px; color:#777;">(Disahkan secara digital via WargaDigi)</p>
                @endunless
            </td>
        </tr>
    </table></body>
</html>
