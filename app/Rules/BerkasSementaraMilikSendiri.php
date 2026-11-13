<?php

namespace App\Rules;

use App\Services\DokumenSementara;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Path berkas KTP/KK dari formulir harus berkas sementara milik akun yang sedang login.
 * Mencegah path milik orang lain (atau path sembarang) ditautkan ke data penduduk.
 */
class BerkasSementaraMilikSendiri implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }
        if (! DokumenSementara::milik(is_string($value) ? $value : null, auth()->user())) {
            $fail('Berkas dokumen tidak valid atau sudah kedaluwarsa. Silakan unggah ulang.');
        }
    }
}
