<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Domisili</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            line-height: 1.5;
            margin: 1cm 1.5cm 1cm 1.5cm;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .mb-0 {
            margin-bottom: 0;
        }
        .mb-1 {
            margin-bottom: 5px;
        }
        .mb-2 {
            margin-bottom: 10px;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        .mb-4 {
            margin-bottom: 20px;
        }
        .mt-4 {
            margin-top: 20px;
        }
        .mt-5 {
            margin-top: 40px;
        }
        h6 {
            font-size: 16px;
            margin: 0;
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .judul-surat {
            text-decoration: underline;
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: top;
            padding: 2px 0;
        }
        .label-col {
            width: 180px;
        }
        .separator-col {
            width: 15px;
        }
        .ttd-container {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 300px;
            float: right;
            text-align: center;
            position: relative;
        }
        .stempel-img {
            position: absolute;
            left: -40px;
            top: 20px;
            width: 100px;
            height: auto;
            opacity: 0.8;
            z-index: 1;
        }
        .ttd-img {
            width: 150px;
            height: auto;
            margin: 10px 0;
            position: relative;
            z-index: 2;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <div class="kop-surat text-center">
        <h6 class="fw-bold">PEMERINTAH KABUPATEN BANDUNG BARAT</h6>
        <h6 class="fw-bold">KECAMATAN NGAMPRAH</h6>
        <h6 class="fw-bold">DESA TANIMULYA</h6>
        <p class="mb-0">Jl. Pasirhalang Raya No. 1, Tanimulya, Kec. Ngamprah, Kab. Bandung Barat,</p>
        <p class="mb-0">Kode Pos 40552, Telp. (022) 1234567</p>
    </div>

    <div class="text-center mb-4">
        <div class="judul-surat">{{ $data->tipe_surat ?? 'SURAT KETERANGAN' }}</div>
        <div>Nomor: {{ $data->nomor_surat ?? '---/SKD/VIII/' . date('Y') }}</div>
    </div>

    <p>Yang bertanda tangan di bawah ini:</p>

    <div style="margin-left: 30px;" class="mb-3">
        <table>
            <tr>
                <td class="label-col">Nama</td>
                <td class="separator-col">:</td>
                <td>{{ $data->nama_ketua_rw ?? 'Ahmad Heryawan' }}</td>
            </tr>
            <tr>
                <td class="label-col">Jabatan</td>
                <td class="separator-col">:</td>
                <td>Ketua RW 10</td>
            </tr>
            <tr>
                <td class="label-col">Alamat</td>
                <td class="separator-col">:</td>
                <td>RT 03 RW 10, Kp. Pasirhalang, Desa Tanimulya, Ngamprah.</td>
            </tr>
        </table>
    </div>

    <p>Menerangkan dengan sesungguhnya bahwa:</p>

    <div style="margin-left: 30px;" class="mb-3">
        <table>
            <tr>
                <td class="label-col">Nama</td>
                <td class="separator-col">:</td>
                <td class="fw-bold">{{ $data->nama_pemohon_surat ?? $data->nama_pemohon }}</td>
            </tr>
            <tr>
                <td class="label-col">Tempat, Tanggal Lahir</td>
                <td class="separator-col">:</td>
                <td>{{ $data->tempat_tgl_lahir_surat ?? $data->tempat_tgl_lahir }}</td>
            </tr>
            <tr>
                <td class="label-col">Jenis Kelamin</td>
                <td class="separator-col">:</td>
                <td>{{ $data->jenis_kelamin_surat ?? $data->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td class="label-col">Pekerjaan</td>
                <td class="separator-col">:</td>
                <td>{{ $data->pekerjaan_surat ?? $data->pekerjaan }}</td>
            </tr>
            <tr>
                <td class="label-col">Agama</td>
                <td class="separator-col">:</td>
                <td>{{ $data->agama_surat ?? $data->agama }}</td>
            </tr>
            <tr>
                <td class="label-col">Status Perkawinan</td>
                <td class="separator-col">:</td>
                <td>{{ $data->status_perkawinan_surat ?? $data->status_perkawinan }}</td>
            </tr>
            <tr>
                <td class="label-col">Kewarganegaraan</td>
                <td class="separator-col">:</td>
                <td>{{ $data->kewarganegaraan_surat ?? 'Indonesia' }}</td>
            </tr>
            <tr>
                <td class="label-col">Alamat</td>
                <td class="separator-col">:</td>
                <td>{{ $data->alamat_surat ?? $data->alamat }}</td>
            </tr>
        </table>
    </div>

    <p style="text-indent: 30px; text-align: justify;" class="mb-4">
        Orang tersebut di atas adalah benar-benar warga yang berdomisili dan bertempat tinggal di alamat tersebut di Desa Tanimulya, Kecamatan Ngamprah, Kabupaten Bandung Barat.
    </p>

    <p style="text-indent: 30px; text-align: justify;">
        Demikian {{ $data->tipe_surat ?? 'Surat Keterangan' }} ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <div class="ttd-container clearfix mt-4">
        <div class="ttd-box">
            @if(isset($data->stempel_rw) && $data->stempel_rw)
                <img src="{{ public_path('storage/' . $data->stempel_rw) }}" class="stempel-img" alt="Stempel">
            @endif

            <p class="mb-0">Tanimulya, {{ \Carbon\Carbon::parse($data->tanggal_selesai ?? now())->translatedFormat('d F Y') }}</p>
            <p class="mb-0">Ketua RW,</p>
            
            @if(isset($data->ttd_rw) && $data->ttd_rw)
                <img src="{{ public_path('storage/' . $data->ttd_rw) }}" class="ttd-img" alt="Tanda Tangan">
            @else
                <br><br><br><br>
            @endif
            
            <p class="fw-bold mb-0 text-decoration-underline" style="text-decoration: underline;">{{ $data->nama_ketua_rw ?? 'Ahmad Heryawan' }}</p>
        </div>
    </div>

</body>
</html>
