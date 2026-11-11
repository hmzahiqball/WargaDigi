{{-- Perbandingan data lama vs baru untuk pengajuan mutasi. Membutuhkan: $mutasi. --}}
@php
    $dataPengajuan = $mutasi->data_pengajuan ?? [];
    $dataLama = $mutasi->data_lama ?? [];

    // Key teknis yang tidak perlu ditampilkan.
    $hiddenKeys = ['_token', '_method', 'file_kk', 'file_ktp_path', 'nik_lama', 'nik_tujuan'];

    // Label ramah untuk key yang umum.
    $labelField = function ($key) {
        $map = [
            'nama_lengkap'             => 'Nama Lengkap',
            'nik'                      => 'NIK',
            'no_wa'                    => 'Nomor WhatsApp',
            'jenis_kelamin'            => 'Jenis Kelamin',
            'tempat_lahir'             => 'Tempat Lahir',
            'tanggal_lahir'            => 'Tanggal Lahir',
            'agama'                    => 'Agama',
            'pekerjaan'                => 'Pekerjaan',
            'pendidikan_terakhir'      => 'Pendidikan Terakhir',
            'status_hubungan_keluarga' => 'Hubungan Keluarga',
            'status_perkawinan'        => 'Status Perkawinan',
            'kewarganegaraan'          => 'Kewarganegaraan',
            'alamat'                   => 'Alamat',
            'alamat_domisili'          => 'Alamat Domisili',
            'status_warga'             => 'Status Warga',
            'pemilik_rumah'            => 'Pemilik Rumah',
            'rw'                       => 'RW',
            'kelurahan'                => 'Kelurahan',
            'kecamatan'                => 'Kecamatan',
            'kabupaten'                => 'Kabupaten',
            'provinsi'                 => 'Provinsi',
            'kode_pos'                 => 'Kode Pos',
            'nama_ayah'                => 'Nama Ayah',
            'nama_ibu'                 => 'Nama Ibu',
        ];
        return $map[$key] ?? ucwords(str_replace('_', ' ', $key));
    };

    // Format nilai agar ramah dibaca (boolean, kosong).
    $tampilNilai = function ($value) {
        if (is_bool($value)) {
            return $value ? 'Ya' : 'Bukan';
        }
        if ($value === null || $value === '') {
            return '-';
        }
        return (string) $value;
    };
@endphp

@if($mutasi->jenis_mutasi === 'pindah_akun')
    @php
        $namaLama = $dataLama['nama_pemegang_lama'] ?? ($dataPengajuan['nik_lama'] ?? '-');
        $namaTujuan = $dataPengajuan['nama_tujuan'] ?? '-';
    @endphp
    <div class="p-3 bg-light rounded-3">
        <div class="text-muted mb-1" style="font-size:0.8rem;">Pemindahan Pemegang Akun</div>
        <div class="fw-medium text-dark">
            Dari: {{ $namaLama ?: '-' }}
            <i class="bi bi-arrow-right mx-2 text-success"></i>
            Ke: {{ $namaTujuan ?: '-' }}
        </div>
    </div>
@elseif($mutasi->jenis_mutasi === 'tambah')
    <div class="text-muted mb-2" style="font-size:0.85rem;">Data anggota baru</div>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="small fw-semibold text-muted">Field</th>
                    <th class="small fw-semibold text-muted">Nilai Baru</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataPengajuan as $key => $value)
                    @continue(in_array($key, $hiddenKeys, true))
                    <tr>
                        <td class="small text-muted">{{ $labelField($key) }}</td>
                        <td class="small fw-medium text-dark">{{ $tampilNilai($value) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="small fw-semibold text-muted">Field</th>
                    <th class="small fw-semibold text-muted">Nilai Lama</th>
                    <th class="small fw-semibold text-muted">Nilai Baru</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataPengajuan as $key => $value)
                    @continue(in_array($key, $hiddenKeys, true))
                    @php
                        $punyaLama = array_key_exists($key, $dataLama);
                        $nilaiLama = $punyaLama ? $dataLama[$key] : null;
                        $berubah = $punyaLama && ((string) $nilaiLama !== (string) $value);
                    @endphp
                    <tr @class(['table-warning' => $berubah])>
                        <td class="small text-muted">{{ $labelField($key) }}</td>
                        <td class="small text-dark">{{ $punyaLama ? $tampilNilai($nilaiLama) : '-' }}</td>
                        <td class="small fw-medium text-dark">{{ $tampilNilai($value) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
