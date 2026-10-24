<?php

namespace App\Http\Controllers\Concerns;

trait NormalizesMoney
{
    /**
     * Normalisasi nilai nominal uang dari number formatter sisi klien.
     *
     * Input bisa berupa string berpemisah ribuan (mis. "100.000" atau "1.250.000")
     * yang berasal dari formatter tampilan. Fungsi ini mengembalikan string angka
     * murni ("100000") agar lolos validasi numeric dan tersimpan sebagai integer.
     *
     * Nilai kosong/null dikembalikan apa adanya supaya rule required tetap berfungsi.
     */
    protected function normalizeMoney($value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // Ambil hanya digit (buang titik ribuan, spasi, "Rp", dsb).
        $digits = preg_replace('/\D/', '', (string) $value);

        return $digits === '' ? '' : $digits;
    }
}
