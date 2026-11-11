<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\User;
use App\Services\Ocr\KkParser;
use App\Services\Ocr\KtpParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Manajemen Penduduk RT (scope per RT) + Tambah Keluarga (multi-penduduk,
 * wilayah, pemegang akun, status warga) + parser OCR KK/KTP.
 */
class RtKeluargaOcrTest extends TestCase
{
    use RefreshDatabase;

    private function ketuaRt(MasterRt $rt): User
    {
        Role::findOrCreate('Ketua RT', 'web');
        $u = User::create([
            'nik' => null, 'username' => 'ketuart_test', 'role' => 'Ketua RT',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $u->syncRoles(['Ketua RT']);

        return $u;
    }

    private function pendudukDiRt(MasterRt $rt, string $nik, string $nama): void
    {
        $k = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat',
            'rt_id' => $rt->id, 'status_aktivasi' => 'Active',
        ]);
        Penduduk::create([
            'keluarga_id' => $k->id, 'nik' => $nik, 'nama_lengkap' => $nama,
            'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
        ]);
    }

    public function test_manajemen_penduduk_rt_hanya_menampilkan_rt_sendiri(): void
    {
        $rtA = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $rtB = MasterRt::create(['kode_rt' => '02', 'nama_rt' => 'RT 02']);
        $this->pendudukDiRt($rtA, '3217010100000401', 'Warga RT Satu');
        $this->pendudukDiRt($rtB, '3217010100000402', 'Warga RT Dua');

        $this->actingAs($this->ketuaRt($rtA))
            ->get(route('rt.penduduk.index'))
            ->assertOk()
            ->assertSee('Warga RT Satu')
            ->assertDontSee('Warga RT Dua');
    }

    public function test_tambah_keluarga_menyimpan_multi_penduduk_dan_akun(): void
    {
        $rt = MasterRt::create(['kode_rt' => '21', 'nama_rt' => 'RT 21']);
        $ketua = $this->ketuaRt($rt);

        $payload = [
            'no_kk' => '3217010100009001',
            'alamat' => 'Jl. Melati No. 1',
            'rw' => '21',
            'kelurahan' => 'Tanimulya',
            'pemilik_rumah' => '1',
            'penduduk' => [
                [
                    'nik' => '3217010100009101', 'nama_lengkap' => 'Bapak Kepala', 'no_wa' => '081200009101',
                    'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1980-05-05',
                    'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
                    'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
                    'kewarganegaraan' => 'WNI',
                ],
                [
                    'nik' => '3217010100009102', 'nama_lengkap' => 'Ibu Istri',
                    'jenis_kelamin' => 'P', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1985-06-06',
                    'agama' => 'Islam', 'pekerjaan' => 'IRT',
                    'status_hubungan_keluarga' => 'Istri', 'status_perkawinan' => 'Kawin',
                    'kewarganegaraan' => 'WNI',
                ],
            ],
            'pemegang_akun' => 0,
        ];

        $this->actingAs($ketua)
            ->post(route('rt.keluarga.store'), $payload)
            ->assertRedirect(route('rt.penduduk.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('keluarga', [
            'no_kk' => '3217010100009001',
            'rt_id' => $rt->id,
            'status_warga' => 'tetap', // RW 21 + Tanimulya
            'pemilik_rumah' => 1,
        ]);
        $this->assertDatabaseHas('penduduk', ['nik' => '3217010100009101', 'nama_lengkap' => 'Bapak Kepala']);
        $this->assertDatabaseHas('penduduk', ['nik' => '3217010100009102', 'nama_lengkap' => 'Ibu Istri']);
        $this->assertDatabaseHas('users', ['nik' => '3217010100009101', 'role' => 'Warga']);
    }

    public function test_tambah_keluarga_menyimpan_alamat_dan_status_warga_per_penduduk(): void
    {
        $rt = MasterRt::create(['kode_rt' => '21', 'nama_rt' => 'RT 21']);
        $ketua = $this->ketuaRt($rt);

        $this->actingAs($ketua)->post(route('rt.keluarga.store'), [
            'no_kk' => '3217010100009501', 'alamat' => 'Jl. KK No. 1',
            'rw' => '21', 'kelurahan' => 'Tanimulya',
            'penduduk' => [
                [
                    'nik' => '3217010100009601', 'nama_lengkap' => 'Kepala', 'no_wa' => '081200009601',
                    'alamat' => 'KP TANIMULYA RT 001 RW 021',
                    'alamat_domisili' => 'KP TANIMULYA RT 001 RW 021', 'status_warga' => 'tetap',
                    'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1980-01-01',
                    'agama' => 'Islam', 'pekerjaan' => 'Dosen',
                    'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
                    'kewarganegaraan' => 'WNI',
                ],
                [
                    'nik' => '3217010100009602', 'nama_lengkap' => 'Anak Kos',
                    'alamat' => 'KP SUKAMAJU RT 002 RW 005', 'status_warga' => 'tidak_tetap',
                    'jenis_kelamin' => 'L', 'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '2005-02-02',
                    'agama' => 'Islam', 'pekerjaan' => 'Pelajar',
                    'status_hubungan_keluarga' => 'Anak', 'status_perkawinan' => 'Belum Kawin',
                    'kewarganegaraan' => 'WNI',
                ],
            ],
            'pemegang_akun' => 0,
            'username' => 'kepala21', 'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('penduduk', [
            'nik' => '3217010100009601', 'alamat' => 'KP TANIMULYA RT 001 RW 021',
            'alamat_domisili' => 'KP TANIMULYA RT 001 RW 021', 'status_warga' => 'tetap',
        ]);
        $this->assertDatabaseHas('penduduk', [
            'nik' => '3217010100009602', 'alamat' => 'KP SUKAMAJU RT 002 RW 005', 'status_warga' => 'tidak_tetap',
        ]);
    }

    public function test_status_kk_dropdown_dihormati_bila_dikirim(): void
    {
        $rt = MasterRt::create(['kode_rt' => '05', 'nama_rt' => 'RT 05']);
        $ketua = $this->ketuaRt($rt);

        // RW 05 + Sukamaju → otomatis 'tidak_tetap', tapi admin override 'tetap'.
        $this->actingAs($ketua)->post(route('rt.keluarga.store'), [
            'no_kk' => '3217010100009901', 'alamat' => 'Jl. Override',
            'rw' => '05', 'kelurahan' => 'Sukamaju', 'status_warga' => 'tetap',
            'penduduk' => [[
                'nik' => '3217010100009911', 'nama_lengkap' => 'Kepala', 'no_wa' => '081200009911',
                'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1980-01-01',
                'agama' => 'Islam', 'pekerjaan' => 'Dosen',
                'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
                'kewarganegaraan' => 'WNI',
            ]],
            'pemegang_akun' => 0,
            'username' => 'override05', 'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('keluarga', ['no_kk' => '3217010100009901', 'status_warga' => 'tetap']);
    }

    public function test_status_warga_penduduk_default_dari_keluarga_bila_kosong(): void
    {
        $rt = MasterRt::create(['kode_rt' => '21', 'nama_rt' => 'RT 21']);
        $ketua = $this->ketuaRt($rt);

        // status_warga penduduk tidak dikirim → fallback ke status keluarga (tetap: RW21+Tanimulya).
        $this->actingAs($ketua)->post(route('rt.keluarga.store'), [
            'no_kk' => '3217010100009701', 'alamat' => 'Jl. KK',
            'rw' => '21', 'kelurahan' => 'Tanimulya',
            'penduduk' => [[
                'nik' => '3217010100009801', 'nama_lengkap' => 'Kepala', 'no_wa' => '081200009801',
                'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1980-01-01',
                'agama' => 'Islam', 'pekerjaan' => 'Dosen',
                'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
                'kewarganegaraan' => 'WNI',
            ]],
            'pemegang_akun' => 0,
            'username' => 'kepala21b', 'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('penduduk', ['nik' => '3217010100009801', 'status_warga' => 'tetap']);
    }

    public function test_status_warga_tidak_tetap_bila_di_luar_rw21_tanimulya(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        $this->actingAs($ketua)->post(route('rt.keluarga.store'), [
            'no_kk' => '3217010100009002', 'alamat' => 'Jl. Luar',
            'rw' => '05', 'kelurahan' => 'Sukamaju',
            'penduduk' => [[
                'nik' => '3217010100009201', 'nama_lengkap' => 'Pendatang', 'no_wa' => '081200009201',
                'jenis_kelamin' => 'L', 'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '1990-01-01',
                'agama' => 'Islam', 'pekerjaan' => 'Karyawan',
                'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Belum Kawin',
            ]],
            'pemegang_akun' => 0,
            'username' => 'pendatang01', 'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('keluarga', ['no_kk' => '3217010100009002', 'status_warga' => 'tidak_tetap']);
    }

    public function test_tambah_keluarga_wajib_satu_kepala_keluarga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        // Dua-duanya non kepala keluarga → ditolak.
        $this->actingAs($ketua)->post(route('rt.keluarga.store'), [
            'no_kk' => '3217010100009003', 'alamat' => 'Jl. X',
            'penduduk' => [[
                'nik' => '3217010100009301', 'nama_lengkap' => 'A', 'no_wa' => '081200009301', 'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam',
                'pekerjaan' => 'Karyawan', 'status_hubungan_keluarga' => 'Anak', 'status_perkawinan' => 'Belum Kawin',
            ]],
            'pemegang_akun' => 0,
            'username' => 'anak01', 'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('penduduk');

        $this->assertDatabaseMissing('keluarga', ['no_kk' => '3217010100009003']);
    }

    public function test_ocr_fail_safe_null_provider(): void
    {
        config(['ocr.provider' => 'null']);
        $svc = new \App\Services\Ocr\OcrService();
        // Path tidak valid → tetap aman, terbaca false.
        $hasil = $svc->bacaKk('/path/tidak/ada.jpg', 'image/jpeg');
        $this->assertFalse($hasil['terbaca']);
    }

    public function test_parser_kk_header_inline(): void
    {
        // Format "LABEL : NILAI" satu baris (umum dari OCR.space).
        $teks = "KARTU KELUARGA\nNO KK : 3217010100001234\nALAMAT : JL MELATI NO 5\nRT/RW : 001/021\nKELURAHAN : TANIMULYA\nKECAMATAN : NGAMPRAH\nKABUPATEN : BANDUNG BARAT\nPROVINSI : JAWA BARAT\nKODE POS : 40552\n";
        $h = KkParser::parse($teks);
        $this->assertSame('3217010100001234', $h['no_kk']);
        $this->assertSame('1', $h['rt']);
        $this->assertSame('21', $h['rw']);
        $this->assertSame('40552', $h['kode_pos']);
        $this->assertSame('Jawa Barat', $h['provinsi']);
        $this->assertSame('Tanimulya', $h['kelurahan']);
        $this->assertSame('Ngamprah', $h['kecamatan']);
        $this->assertEmpty($h['anggota']);
    }

    public function test_parser_kk_header_label_lalu_nilai(): void
    {
        // Format label di baris sendiri, nilai di baris berikutnya (e-KK OCR.space).
        $teks = implode("\n", [
            'KARTU KELUARGA', 'No. 3204130705180031',
            'RT/RW', '001/008',
            'Desa/Kelurahan', 'TARAJUSARI',
            'Kecamatan', 'BANJARAN',
            'Kabupaten/Kota', 'BANDUNG',
            'Provinsi', 'JAWA BARAT',
            'Kode Pos', '40377',
        ]);

        $h = KkParser::parse($teks);

        $this->assertSame('3204130705180031', $h['no_kk']);
        $this->assertSame('1', $h['rt']);
        $this->assertSame('8', $h['rw']);
        $this->assertSame('Jawa Barat', $h['provinsi']);
        $this->assertSame('40377', $h['kode_pos']);
        $this->assertSame('Tarajusari', $h['kelurahan']);
        $this->assertSame('Banjaran', $h['kecamatan']);
        $this->assertEmpty($h['anggota']);
    }

    public function test_endpoint_parse_ktp_dari_teks_browser(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        $teks = "PROVINSI JAWA BARAT\nNIK : 3217010100005678\nNama : BUDI SANTOSO\nJenis Kelamin : LAKI-LAKI\nAgama : ISLAM\n";

        $this->actingAs($ketua)
            ->postJson(route('ocr.parse-ktp'), ['text' => $teks])
            ->assertOk()
            ->assertJson([
                'terbaca' => true,
                'data' => ['nik' => '3217010100005678', 'jenis_kelamin' => 'L'],
            ]);
    }

    public function test_parser_ktp_mengekstrak_nik_dan_jk(): void
    {
        $teks = "PROVINSI JAWA BARAT\nNIK : 3217010100005678\nNama : BUDI SANTOSO\nJenis Kelamin : LAKI-LAKI\nAgama : ISLAM\nStatus Perkawinan : KAWIN\n";
        $hasil = KtpParser::parse($teks);
        $this->assertSame('3217010100005678', $hasil['nik']);
        $this->assertSame('L', $hasil['jenis_kelamin']);
        $this->assertSame('Islam', $hasil['agama']);
    }

    public function test_parser_ktp_nik_normalisasi_huruf_mirip_angka(): void
    {
        // OCR sering menukar angka dengan huruf mirip: O->0, I/L->1, B->8, S->5.
        // NIK asli: 3217010100005678
        $teks = "PROVINSI JAWA BARAT\nNIK : 32I70101000056z8\nNama : BUDI SANTOSO\n";
        $h = KtpParser::parse($teks);
        // 32I7 -> 3217 ; z/Z -> 2 pada digit terakhir.
        $this->assertSame('3217010100005628', $h['nik']);
    }

    public function test_parser_ktp_nik_dengan_O_sebagai_nol(): void
    {
        // NIK asli: 3204130601930011 dibaca dengan O menggantikan 0.
        $teks = "KABUPATEN BANDUNG\nNIK : 32O413O6O193OO11\n";
        $h = KtpParser::parse($teks);
        $this->assertSame('3204130601930011', $h['nik']);
    }

    public function test_parser_ktp_label_dan_nilai_baris_terpisah(): void
    {
        // Format OCR.space: label di satu baris, nilai di baris berikutnya
        // (terjadi bila titik dua tidak terbaca).
        $teks = implode("\n", [
            'PROVINSI JAWA BARAT',
            'KABUPATEN BANDUNG',
            'NIK',
            '3204130601930011',
            'Nama',
            'LUKMANNUL HAKIM FIRDAUS',
            'Tempat/Tgl Lahir',
            'BANDUNG, 06-01-1993',
            'Jenis Kelamin',
            'LAKI-LAKI',
            'Alamat',
            'KP PANGKALAN',
            'Agama',
            'ISLAM',
            'Status Perkawinan',
            'KAWIN',
            'Pekerjaan',
            'DOSEN',
            'Kewarganegaraan',
            'WNI',
        ]);

        $h = KtpParser::parse($teks);
        $this->assertSame('3204130601930011', $h['nik']);
        $this->assertSame('Lukmannul Hakim Firdaus', $h['nama_lengkap']);
        $this->assertSame('Bandung', $h['tempat_lahir']);
        $this->assertSame('1993-01-06', $h['tanggal_lahir']);
        $this->assertSame('L', $h['jenis_kelamin']);
        $this->assertSame('Kp Pangkalan', $h['alamat']);
        $this->assertSame('Islam', $h['agama']);
        $this->assertSame('Kawin', $h['status_perkawinan']);
        $this->assertSame('Dosen', $h['pekerjaan']);
        $this->assertSame('WNI', $h['kewarganegaraan']);
    }

    public function test_parser_ktp_kolom_label_dan_nilai_terpisah_ocrspace(): void
    {
        // Pola nyata OCR.space untuk KTP: semua LABEL dulu, lalu semua NILAI
        // berurutan (layout dua kolom KTP terbaca sebagai dua blok).
        $teks = implode("\n", [
            'NIK', 'Nama', 'Tempat/Tgl Lahir', 'Jenis kelamin', 'Alamat', 'RT/RW',
            'Kel/Desa', 'Kecamatan', 'Agama', 'Status Perkawinan', 'Pekerjaan',
            'Kewarganegaraan', 'Berlaku Hingga',
            'KABUPATEN BANDUNG', '3204134301950005', 'EVA NURROHMAH SYAMSUDIN',
            ': BANDUNG, 03-01-1995', ': PEREMPUAN', 'Gol. Darah', ': KP. PANGKALAN',
            ': 001/008', ': TARAJUSARI', ': BANJARAN', ': ISLAM', 'KAWIN', 'GURU E',
            'ENDO', 'WNI', 'SEUMUR HIDUP',
        ]);

        $h = KtpParser::parse($teks);
        $this->assertSame('3204134301950005', $h['nik']);
        $this->assertSame('Eva Nurrohmah Syamsudin', $h['nama_lengkap']);
        $this->assertSame('Bandung', $h['tempat_lahir']);
        $this->assertSame('1995-01-03', $h['tanggal_lahir']);
        $this->assertSame('P', $h['jenis_kelamin']);
        $this->assertStringContainsString('Kp. Pangkalan', $h['alamat']);
        $this->assertSame('Islam', $h['agama']);
        $this->assertSame('Kawin', $h['status_perkawinan']);
        $this->assertSame('Guru', $h['pekerjaan']);
        $this->assertSame('WNI', $h['kewarganegaraan']);
    }

    public function test_parser_ktp_toleran_teks_ocr_berantakan(): void
    {
        // Teks nyata hasil tesseract.js (separator & ejaan tidak rapi).
        $teks = implode("\n", [
            'KABUPATEN BANDUNG',
            'NIK : 3204130601930011',
            'Nama . "LUKMANNUL HAKIM FIRDAUS',
            'Tempat/Tgitahr - BANDUNG, 06-01-1993',
            'Jenis kelamin - LAKI-LAKI Gol Darah',
            'Alamat ~KP PANGKALAN',
            'Agama "ISLAM',
            'Status Perkawinan  " KAWIN',
            'Pekerjaan - DOSEN',
            'Kewarganegaraan - WNI',
        ]);

        $h = KtpParser::parse($teks);
        $this->assertSame('3204130601930011', $h['nik']);
        $this->assertSame('Lukmannul Hakim Firdaus', $h['nama_lengkap']);
        $this->assertSame('L', $h['jenis_kelamin']);
        $this->assertSame('Bandung', $h['tempat_lahir']);
        $this->assertSame('1993-01-06', $h['tanggal_lahir']);
        $this->assertSame('Islam', $h['agama']);
        $this->assertSame('Kawin', $h['status_perkawinan']);
        $this->assertSame('WNI', $h['kewarganegaraan']);
    }
}
