<?php

namespace App\Services\Ocr\Contracts;

/**
 * Kontrak penyedia OCR: mengekstraksi teks mentah dari sebuah file dokumen
 * (gambar/PDF). Implementasi WAJIB fail-safe: kembalikan null bila gagal,
 * jangan melempar exception ke pemanggil.
 */
interface OcrProvider
{
    /**
     * @param string $absolutePath path absolut file dokumen
     * @param string $mime         mime type file
     * @return string|null teks hasil OCR, atau null bila gagal/tidak terbaca
     */
    public function readText(string $absolutePath, string $mime): ?string;
}
