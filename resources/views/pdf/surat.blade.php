<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $data->judul }}</title>
    <style>
        @page { margin: 1cm 1.5cm 0.8cm 1.5cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 13px; line-height: 1.35; margin: 0; padding: 0; }
        .kop { width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .kop td { vertical-align: middle; }
        .kop-logo { width: 65px; }
        .kop-logo img { width: 60px; height: auto; }
        .kop-text { text-align: center; }
        .kop-text h2 { font-size: 16px; margin: 0; font-weight: bold; }
        .kop-text h3 { font-size: 14px; margin: 0; font-weight: bold; }
        .kop-text p { font-size: 12px; margin: 0; font-weight: bold; }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; font-size: 14px; margin: 14px 0 2px 0; }
        .nomor { text-align: center; margin: 0 0 12px 0; font-size: 13px; }
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table td { padding: 2px 0; vertical-align: top; font-size: 13px; }
        .label { width: 170px; }
        .sep { width: 12px; }
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .ttd-table td.rw-col { width: 50%; text-align: left; vertical-align: top; padding-left: 15px; }
        .ttd-table td.rt-col { width: 50%; text-align: right; vertical-align: top; padding-right: 15px; }
        .ttd-box-container { display: inline-block; text-align: center; width: 200px; }
        .ttd-box { height: 80px; position: relative; margin-top: 10px; margin-bottom: 0px; }
        .ttd-img { max-height: 60px; max-width: 150px; position: relative; z-index: 1; margin-top: 5px; }
        .stempel-img { max-height: 110px; max-width: 110px; opacity: 0.80; position: absolute; z-index: 10; top: -25px; left: -15px; }
        p.isi { margin: 8px 0 8px 15px; text-align: justify; }
    </style>
</head>
<body>
@php
    $preview = isset($data->is_preview) && $data->is_preview;
    $kop = $data->kop ?? [];
    $logoPath = $kop['logo'] ?? 'images/logo_rw21.png';
    $fileUrl = fn ($path) => $preview ? asset('storage/' . $path) : public_path('storage/' . $path);
@endphp

    {{-- KOP SURAT --}}
    <table class="kop">
        <tr>
            <td class="kop-logo">
                @if(file_exists(public_path($logoPath)))
                    <img src="{{ $preview ? asset($logoPath) : public_path($logoPath) }}" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <h2>{{ $kop['baris_1'] ?? '' }}</h2>
                <h3>{{ $kop['baris_2'] ?? '' }}</h3>
                <p>{{ $kop['baris_3'] ?? '' }}</p>
            </td>
        </tr>
    </table>

    {{-- JUDUL & NOMOR SURAT --}}
    <div class="judul">{{ $data->judul }}</div>
    <div class="nomor">
        Nomor : <span id="nomor-urut">{{ $data->nomor_urut ?: '............' }}</span>
        / SK / RT{{ $data->kode_rt }} / RW{{ $data->kode_rw }} / {{ $data->bulan_romawi }} / 20{{ $data->tahun }}
    </div>

    <p class="isi">
        Yang bertandatangan di bawah ini, Pengurus RT{{ $data->kode_rt }}/RW{{ $data->kode_rw }},
        menerangkan bahwa{{ $data->sebagai_pelapor ? ' pelapor' : '' }}:
    </p>

    {{-- BIODATA (langsung dari database) --}}
    <table class="content-table" style="margin-left: 30px;">
        <tr><td class="label">Nama Lengkap</td><td class="sep">:</td><td>{{ $data->nama_lengkap }}</td></tr>
        <tr><td class="label">Tempat &amp; Tanggal Lahir</td><td class="sep">:</td><td>{{ $data->tempat_tgl_lahir }}</td></tr>
        <tr><td class="label">Jenis Kelamin</td><td class="sep">:</td><td>{{ $data->jenis_kelamin }}</td></tr>
        <tr><td class="label">Agama</td><td class="sep">:</td><td>{{ $data->agama }}</td></tr>
        <tr><td class="label">Status Perkawinan</td><td class="sep">:</td><td>{{ $data->status_perkawinan }}</td></tr>
        <tr><td class="label">Pekerjaan</td><td class="sep">:</td><td>{{ $data->pekerjaan }}</td></tr>
        @if(!empty($data->pendidikan))
        <tr><td class="label">Pendidikan Terakhir</td><td class="sep">:</td><td>{{ $data->pendidikan }}</td></tr>
        @endif
        <tr><td class="label">Kewarganegaraan</td><td class="sep">:</td><td>{{ $data->kewarganegaraan }}</td></tr>
        <tr><td class="label">NIK</td><td class="sep">:</td><td>{{ $data->nik }}</td></tr>
        <tr><td class="label">Nomor KK</td><td class="sep">:</td><td>{{ $data->no_kk }}</td></tr>
        <tr><td class="label">Status dalam Keluarga</td><td class="sep">:</td><td>{{ $data->status_hubungan_keluarga }}</td></tr>
        <tr><td class="label">{{ $data->label_alamat }}</td><td class="sep">:</td><td>{{ $data->alamat }}</td></tr>
    </table>

    {{-- KETERANGAN TAMBAHAN KHUSUS JENIS SURAT --}}
    @if(!empty($data->tambahan_rows))
        <p class="isi" style="margin-top: 10px;">
            {{ $data->sebagai_pelapor ? 'Adapun keterangan yang dilaporkan adalah sebagai berikut:' : 'Keterangan tambahan:' }}
        </p>
        <table class="content-table" style="margin-left: 30px;">
            @foreach($data->tambahan_rows as $row)
                <tr>
                    <td class="label">{{ $row['label'] }}</td>
                    <td class="sep">:</td>
                    <td>
                        @if($row['nilai'] === null)
                            ..........................................
                        @elseif($row['multi'])
                            @foreach(preg_split('/\r\n|\r|\n/', $row['nilai']) as $i => $baris)
                                @if(trim($baris) !== '')
                                    {{ $i + 1 }}. {{ trim($baris) }}<br>
                                @endif
                            @endforeach
                        @else
                            {{ $row['nilai'] }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- TUJUAN & PENUTUP --}}
    <p class="isi" style="margin-top: 12px;">{{ $data->tujuan }}</p>
    <p class="isi">Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

    <p style="margin: 10px 0 0 15px; font-size: 13px;">{{ $kop['tempat_surat'] ?? '' }}, {{ $data->tanggal_surat }}</p>

    {{-- TANDA TANGAN --}}
    <table class="ttd-table">
        <tr>
            <td class="rw-col">
                <div class="ttd-box-container">
                    Ketua RW {{ $data->kode_rw }}
                    <div class="ttd-box">
                        @if(!empty($data->stempel_rw))
                            <img src="{{ $fileUrl($data->stempel_rw) }}" class="stempel-img" alt="Stempel RW">
                        @endif
                        @if(!empty($data->ttd_rw))
                            <img src="{{ $fileUrl($data->ttd_rw) }}" class="ttd-img" alt="TTD RW">
                        @endif
                    </div>
                    <div style="text-decoration: underline; font-weight: bold;">({{ $data->nama_ketua_rw }})</div>
                </div>
            </td>
            <td class="rt-col">
                <div class="ttd-box-container">
                    Ketua RT {{ $data->kode_rt }}
                    <div class="ttd-box">
                        @if(!empty($data->stempel_rt))
                            <img src="{{ $fileUrl($data->stempel_rt) }}" class="stempel-img" alt="Stempel RT">
                        @endif
                        @if(!empty($data->ttd_rt))
                            <img src="{{ $fileUrl($data->ttd_rt) }}" class="ttd-img" alt="TTD RT">
                        @endif
                    </div>
                    <div style="text-decoration: underline; font-weight: bold;">({{ $data->nama_ketua_rt }})</div>
                </div>
            </td>
        </tr>
    </table>

    <script>
    window.addEventListener('message', function(event) {
        var data = event.data;
        if (!data) return;

        if (data.type === 'update-preview') {
            var box = document.querySelector(data.selector);
            if (box) {
                var img = box.querySelector('img.' + data.className);
                if (!img) {
                    img = document.createElement('img');
                    img.className = data.className;
                    if (data.className === 'ttd-img') {
                        img.style.cssText = 'max-height:60px;max-width:150px;position:relative;z-index:1;margin-top:5px;';
                    } else {
                        img.style.cssText = 'max-height:110px;max-width:110px;opacity:0.80;position:absolute;z-index:10;top:-25px;left:-15px;';
                    }
                    box.appendChild(img);
                }
                img.src = data.src;
            }
        } else if (data.type === 'clear-preview') {
            var box2 = document.querySelector(data.selector);
            if (box2) {
                var img2 = box2.querySelector('img.' + data.className);
                if (img2) img2.remove();
            }
        } else if (data.type === 'update-nomor') {
            // Perbarui nomor urut surat secara langsung saat pengurus mengetik.
            var el = document.getElementById('nomor-urut');
            if (el) el.textContent = (data.value && String(data.value).trim() !== '') ? data.value : '............';
        }
    });
    </script>
</body>
</html>
