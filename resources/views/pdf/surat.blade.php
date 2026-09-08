<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar RT/RW 21</title>
    <style>
        @page { margin: 1cm 1.5cm 0.8cm 1.5cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 13px; line-height: 1.3; margin: 0; padding: 0; }
        .kop { width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .kop td { vertical-align: middle; }
        .kop-logo { width: 65px; }
        .kop-logo img { width: 60px; height: auto; }
        .kop-text { text-align: center; }
        .kop-text h2 { font-size: 16px; margin: 0; font-weight: bold; }
        .kop-text h3 { font-size: 14px; margin: 0; font-weight: bold; }
        .kop-text p { font-size: 12px; margin: 0; font-weight: bold; }
        .nomor { text-align: center; margin: 10px 0; font-weight: bold; font-size: 13px; }
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table td { padding: 1.5px 0; vertical-align: top; font-size: 13px; }
        .label { width: 170px; }
        .sep { width: 12px; }
        .pekerjaan-table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .pekerjaan-table td { padding: 1px 5px; font-size: 12px; vertical-align: top; }
        .surat-table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .surat-table td { padding: 2.5px 5px; font-size: 13px; vertical-align: top; }
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .ttd-table td.rw-col { width: 50%; text-align: left; vertical-align: top; padding-left: 15px; }
        .ttd-table td.rt-col { width: 50%; text-align: right; vertical-align: top; padding-right: 15px; }
        .ttd-box-container { display: inline-block; text-align: center; width: 180px; }
        .ttd-box { height: 75px; position: relative; margin-top: 15px; margin-bottom: 5px; }
        .ttd-img { max-height: 60px; max-width: 150px; position: relative; z-index: 1; margin-top: 5px; }
        .stempel-img { max-height: 65px; max-width: 80px; opacity: 0.85; position: absolute; z-index: 10; top: 5px; left: 50px; }
        .selected { font-weight: bold; text-decoration: underline; }
        .catatan { color: red; font-size: 12px; font-style: italic; margin-top: 10px; margin-left: 15px; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="kop">
        <tr>
            <td class="kop-logo">
                @if(file_exists(public_path('images/logo_rw21.png')))
                    <img src="{{ isset($data->is_preview) && $data->is_preview ? asset('images/logo_rw21.png') : public_path('images/logo_rw21.png') }}" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <h2>RUKUN WARGA 21</h2>
                <h3>KOMPLEK PURI CIPAGERAN INDAH II</h3>
                <p>DESA TANIMULYA, KECAMATAN NGAMPRAH, KABUPATEN BANDUNG BARAT</p>
            </td>
        </tr>
    </table>

    {{-- NOMOR SURAT --}}
    <div class="nomor">
        Nomor :............/ SK / RT{{ $data->kode_rt }}/ RW21 / {{ $data->bulan_romawi }}/ 20{{ $data->tahun }}
    </div>

    <p style="margin: 8px 0; margin-left: 15px;">Yang bertandatangan dibawah ini, Pengurus RT{{ $data->kode_rt }}/RW21, menerangkan bahwa :</p>

    {{-- DATA PEMOHON --}}
    <table class="content-table" style="margin-left: 30px;">
        <tr><td class="label">Nama Lengkap</td><td class="sep">:</td><td>{{ $data->nama_lengkap }}</td></tr>
        <tr><td class="label">Tempat & Tanggal lahir</td><td class="sep">:</td><td>{{ $data->tempat_tgl_lahir }}</td></tr>
        <tr><td class="label">Alamat</td><td class="sep">:</td><td>{{ $data->alamat }}</td></tr>
        <tr><td class="label">Nomor KK / NIK</td><td class="sep">:</td><td>{{ $data->no_kk }} / {{ $data->nik }}</td></tr>
        <tr><td class="label">Jenis Kelamin</td><td class="sep">:</td><td>{!! $data->jenis_kelamin == 'Laki-laki' ? '<span class="selected">Laki-laki</span> / Perempuan' : 'Laki-laki / <span class="selected">Perempuan</span>' !!}</td></tr>
        <tr><td class="label">Golongan Darah</td><td class="sep">:</td><td>A / B / AB / O / Tidak tahu</td></tr>
        @php
            $agamaList = ['Islam', 'Kristen', 'Katholik', 'Hindu', 'Budha', 'Lain-lain'];
            $agamaHtml = collect($agamaList)->map(fn($a) => strtolower($a) == strtolower($data->agama) ? '<span class="selected">'.$a.'</span>' : $a)->implode(' / ');
        @endphp
        <tr><td class="label">Agama</td><td class="sep">:</td><td>{!! $agamaHtml !!}</td></tr>
        @php
            $kawinList = ['Kawin', 'Belum Kawin', 'Duda', 'Janda'];
            $kawinHtml = collect($kawinList)->map(fn($s) => strtolower($s) == strtolower($data->status_perkawinan) ? '<span class="selected">'.$s.'</span>' : $s)->implode(' / ');
        @endphp
        <tr><td class="label">Status Perkawinan</td><td class="sep">:</td><td>{!! $kawinHtml !!}</td></tr>
        @php
            $statusList = ['Suami', 'Istri', 'Anak', 'Saudara', 'Lain-lain'];
            $hubungan = $data->status_hubungan_keluarga ?? '-';
            $statusHtml = collect($statusList)->map(fn($s) => strtolower($s) == strtolower($hubungan) || (strtolower($hubungan) == 'kepala keluarga' && $s == 'Suami') ? '<span class="selected">'.$s.'</span>' : $s)->implode(' / ');
        @endphp
        <tr><td class="label">Status di Keluarga</td><td class="sep">:</td><td>{!! $statusHtml !!}</td></tr>
        <tr><td class="label">Pendidikan</td><td class="sep">:</td><td>SD / SLTP / SLTA / D III / D III / D4 / S1 / S2 / S3 / Lain-lain</td></tr>
    </table>

    {{-- DAFTAR PEKERJAAN --}}
    <p style="margin: 8px 0 3px 30px; font-weight: bold; font-size: 13px;">Pekerjaan</p>
    @php
        $pekerjaanList = [
            'Belum / tidak bekerja', 'Buruh harian', 'Pedagang', 'TNI / Polri',
            'Mengurus rumah tangga', 'Wartawan', 'Arsitek', 'Petani / pekebun',
            'Pelajar / mahasiswa', 'Pengacara', 'PNS', 'Sopir',
            'Psikiater / psikolog', 'Notaris', 'Dokter', 'Bidan / Perawat',
            'BUMN / BUMD', 'Dosen / guru', 'Apoteker', 'Lain-lain',
            'Wiraswasta', '', '', '',
        ];
        $pekerjaanWarga = strtolower($data->pekerjaan ?? '');
    @endphp
    <table class="pekerjaan-table" style="margin-left: 30px;">
        @for($row = 0; $row < 6; $row++)
        <tr>
            @for($col = 0; $col < 4; $col++)
                @php $idx = $row + $col * 6; $p = $pekerjaanList[$idx] ?? ''; @endphp
                <td style="width:25%;">
                    @if($p)
                        {{ $row + $col * 6 + 1 }}.
                        @if(str_contains($pekerjaanWarga, strtolower($p)) || strtolower($p) == $pekerjaanWarga)
                            <span class="selected">{{ $p }}</span>
                        @else
                            {{ $p }}
                        @endif
                    @endif
                </td>
            @endfor
        </tr>
        @endfor
    </table>

    {{-- JENIS SURAT (CHECKLIST) --}}
    <p style="margin: 12px 0 5px 15px; font-size: 13px;">Surat Keterangan ini diperlukan untuk membuat / menerangkan bahwa yang bersangkutan :</p>
    @php
        $selected = $data->tipe_surat ?? '';
    @endphp
    <table class="surat-table" style="margin-left: 30px;">
        <tr>
            <td style="width:5%;">1.</td>
            <td style="width:40%;">{!! $selected == 'Kartu Keluarga' ? '<span class="selected">Kartu Keluarga</span>' : 'Kartu Keluarga' !!}</td>
            <td style="width:5%;">6.</td>
            <td style="width:50%;">{!! $selected == 'Surat Keterangan Domisili' ? '<span class="selected">Surat Keterangan Domisili</span>' : 'Surat Keterangan Domisili' !!}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>{!! $selected == 'Kartu Tanda Penduduk' ? '<span class="selected">Kartu Tanda Penduduk</span>' : 'Kartu Tanda Penduduk' !!}</td>
            <td>7.</td>
            <td>{!! $selected == 'Surat Keterangan Miskin / Tidak Mampu' ? '<span class="selected">Surat Keterangan Miskin / Tidak Mampu</span>' : 'Surat Keterangan Miskin / Tidak Mampu' !!}</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>{!! $selected == 'Surat Keterangan Ahli Waris' ? '<span class="selected">Surat Keterangan Ahli Waris</span>' : 'Surat Keterangan Ahli Waris' !!}</td>
            <td>8.</td>
            <td>{!! $selected == 'Surat Keterangan Serbaguna' ? '<span class="selected">Surat Keterangan Serbaguna</span>' : 'Surat Keterangan Serbaguna' !!} untuk&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>{!! $selected == 'Surat Kelahiran' ? '<span class="selected">Surat Kelahiran</span>' : 'Surat Kelahiran' !!}</td>
            <td>9.</td>
            <td>{!! $selected == 'Surat Keterangan Kelakuan Baik / Catatan Kepolisian' ? '<span class="selected">Surat Keterangan Kelakuan Baik / Catatan Kepolisian</span>' : 'Surat Keterangan Kelakuan Baik / Catatan Kepolisian' !!}&hellip;&hellip;</td>
        </tr>
        <tr>
            <td>5.</td>
            <td>{!! $selected == 'Surat Kematian' ? '<span class="selected">Surat Kematian</span>' : 'Surat Kematian' !!}</td>
            <td></td>
            <td>untuk&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3" style="padding-left: 15px;">Meninggal dunia Hari / Tanggal&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;pukul&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;WIB.</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3" style="padding-left: 15px;">Di&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;Dimakamkan di&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</td>
        </tr>
    </table>

    {{-- PENUTUP --}}
    <p style="margin: 15px 0 5px 15px; font-size: 13px;">Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

    <p style="margin: 10px 0 0 15px; font-size: 13px;">Tanimulya, {{ $data->tanggal_surat }}</p>

    {{-- TANDA TANGAN --}}
    <table class="ttd-table">
        <tr>
            <td class="rw-col">
                <div class="ttd-box-container">
                    Ketua RW 21
                    <div class="ttd-box">
                        @if(isset($data->stempel_rw) && $data->stempel_rw)
                            <img src="{{ isset($data->is_preview) && $data->is_preview ? asset('storage/' . $data->stempel_rw) : public_path('storage/' . $data->stempel_rw) }}" class="stempel-img" alt="Stempel RW">
                        @endif
                        @if(isset($data->ttd_rw) && $data->ttd_rw)
                            <img src="{{ isset($data->is_preview) && $data->is_preview ? asset('storage/' . $data->ttd_rw) : public_path('storage/' . $data->ttd_rw) }}" class="ttd-img" alt="TTD RW">
                        @endif
                    </div>
                    <div style="text-decoration: underline; font-weight: bold;">({{ $data->nama_ketua_rw }})</div>
                </div>
            </td>
            <td class="rt-col">
                <div class="ttd-box-container">
                    Ketua RT {{ $data->kode_rt }}
                    <div class="ttd-box">
                        @if(isset($data->stempel_rt) && $data->stempel_rt)
                            <img src="{{ isset($data->is_preview) && $data->is_preview ? asset('storage/' . $data->stempel_rt) : public_path('storage/' . $data->stempel_rt) }}" class="stempel-img" alt="Stempel RT">
                        @endif
                        @if(isset($data->ttd_rt) && $data->ttd_rt)
                            <img src="{{ isset($data->is_preview) && $data->is_preview ? asset('storage/' . $data->ttd_rt) : public_path('storage/' . $data->ttd_rt) }}" class="ttd-img" alt="TTD RT">
                        @endif
                    </div>
                    <div style="text-decoration: underline; font-weight: bold;">({{ $data->nama_ketua_rt }})</div>
                </div>
            </td>
        </tr>
    </table>

    <p class="catatan">Catatan : Lingkari pilihan yang diperlukan</p>

    <script>
    window.addEventListener('message', function(event) {
        var data = event.data;
        if (data && data.type === 'update-preview') {
            var box = document.querySelector(data.selector);
            if (box) {
                var img = box.querySelector('img.' + data.className);
                if (!img) {
                    img = document.createElement('img');
                    img.className = data.className;
                    if (data.className === 'ttd-img') {
                        img.style.cssText = 'max-height:60px;max-width:150px;position:relative;z-index:1;margin-top:5px;';
                    } else {
                        img.style.cssText = 'max-height:65px;max-width:80px;opacity:0.85;position:absolute;z-index:10;top:5px;left:50px;';
                    }
                    box.appendChild(img);
                }
                img.src = data.src;
            }
        } else if (data && data.type === 'clear-preview') {
            var box = document.querySelector(data.selector);
            if (box) {
                var img = box.querySelector('img.' + data.className);
                if (img) img.remove();
            }
        }
    });
    </script>
</body>
</html>

