<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MasterRt;
use App\Models\Keluarga;
use App\Models\Tagihan;
use App\Models\PembayaranTagihan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class GenerateTagihanRutin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:generate-rutin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan rutin bulanan (Iuran RT dan Dana Kematian DKM) untuk seluruh warga';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;
        $namaBulan = Carbon::now()->translatedFormat('F');

        $this->info("Memulai generate tagihan rutin untuk $namaBulan $tahun...");

        DB::beginTransaction();
        try {
            // 1. Generate Iuran RT (Rp 50.000)
            $rts = MasterRt::all();
            foreach ($rts as $rt) {
                // Cari Bendahara RT atau pembuat
                $pembuatRt = User::where('role', 'Op Keuangan RT')->where('rt_id', $rt->id)->first();
                $pembuatId = $pembuatRt ? $pembuatRt->id : null;

                // Cek apakah tagihan rutin RT sudah ada bulan ini
                $existingTagihanRt = Tagihan::where('unit', 'RT')
                    ->where('rt_id', $rt->id)
                    ->where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->where('jenis', 'rutin')
                    ->first();

                if (!$existingTagihanRt) {
                    $tagihanRt = Tagihan::create([
                        'judul' => "Iuran Bulanan - $namaBulan $tahun",
                        'deskripsi' => 'Iuran wajib bulanan RT ' . $rt->nama_rt,
                        'jenis' => 'rutin',
                        'nominal' => 50000,
                        'periode_bulan' => $bulan,
                        'periode_tahun' => $tahun,
                        'unit' => 'RT',
                        'rt_id' => $rt->id,
                        'pembuat_id' => $pembuatId,
                        'status' => 'Active',
                        'tenggat' => Carbon::now()->endOfMonth(),
                    ]);

                    // Assign ke keluarga di RT ini
                    $keluargas = Keluarga::where('rt_id', $rt->id)->get();
                    $pembayaranData = [];
                    foreach ($keluargas as $kk) {
                        $pembayaranData[] = [
                            'id' => \Illuminate\Support\Str::uuid(),
                            'tagihan_id' => $tagihanRt->id,
                            'keluarga_id' => $kk->id,
                            'status' => 'Unpaid',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (count($pembayaranData) > 0) {
                        PembayaranTagihan::insert($pembayaranData);
                    }
                    $this->info("✅ Generated tagihan Iuran RT untuk " . $rt->nama_rt);
                } else {
                    $this->warn("⚠️ Tagihan Iuran RT untuk " . $rt->nama_rt . " bulan ini sudah ada. Melewati...");
                }
            }

            // 2. Generate Dana Kematian DKM (Rp 2.000)
            $pembuatDkm = User::where('role', 'DKM')->first();
            $pembuatDkmId = $pembuatDkm ? $pembuatDkm->id : null;

            $existingTagihanDkm = Tagihan::where('unit', 'DKM')
                ->where('periode_bulan', $bulan)
                ->where('periode_tahun', $tahun)
                ->where('jenis', 'rutin')
                ->first();

            if (!$existingTagihanDkm) {
                $tagihanDkm = Tagihan::create([
                    'judul' => "Iuran Dana Kematian - $namaBulan $tahun",
                    'deskripsi' => 'Iuran dana kematian bulanan seluruh warga',
                    'jenis' => 'rutin',
                    'nominal' => 2000,
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                    'unit' => 'DKM',
                    'rt_id' => null,
                    'pembuat_id' => $pembuatDkmId,
                    'status' => 'Active',
                    'tenggat' => Carbon::now()->endOfMonth(),
                ]);

                // Assign ke semua keluarga
                $allKeluarga = Keluarga::all();
                $pembayaranDataDkm = [];
                foreach ($allKeluarga as $kk) {
                    $pembayaranDataDkm[] = [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'tagihan_id' => $tagihanDkm->id,
                        'keluarga_id' => $kk->id,
                        'status' => 'Unpaid',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (count($pembayaranDataDkm) > 0) {
                    PembayaranTagihan::insert($pembayaranDataDkm);
                }
                $this->info("✅ Generated tagihan Dana Kematian DKM untuk semua warga");
            } else {
                $this->warn("⚠️ Tagihan Dana Kematian DKM bulan ini sudah ada. Melewati...");
            }

            DB::commit();
            $this->info("Proses generate tagihan selesai dengan sukses!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Gagal men-generate tagihan: " . $e->getMessage());
        }
    }
}
