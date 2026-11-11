<?php

namespace App\Services\Ocr;

use App\Services\Ocr\Contracts\OcrProvider;

/**
 * Provider OCR non-aktif: selalu mengembalikan null (tidak terbaca), sehingga
 * alur jatuh ke input manual. Dipakai bila OCR dimatikan.
 */
class NullOcrProvider implements OcrProvider
{
    public function readText(string $absolutePath, string $mime): ?string
    {
        return null;
    }
}
