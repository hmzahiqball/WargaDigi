<?php

namespace App\Services\Ocr;

use App\Services\Ocr\Contracts\OcrProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Provider OCR via OCR.space (HTTP API).
 *
 * Mengirim file sebagai multipart ke endpoint parse/image. Fail-safe:
 * mengembalikan null bila API key kosong, request gagal, atau respons error.
 */
class OcrSpaceProvider implements OcrProvider
{
    public function readText(string $absolutePath, string $mime): ?string
    {
        $structured = $this->readStructured($absolutePath, $mime);

        return $structured['text'] ?? null;
    }

    /**
     * OCR dengan overlay (koordinat kata). Mengembalikan:
     *  [
     *    'text'  => string gabungan,
     *    'lines' => [ ['text'=>..., 'words'=>[ ['text','left','top','width','height'], ... ]], ... ],
     *    'image' => ['width'=>int|null, 'height'=>int|null]  // dimensi gambar yang DIKIRIM
     *  ]
     * atau null bila gagal.
     *
     * @return array{text:string,lines:array,image:array}|null
     */
    public function readStructured(string $absolutePath, string $mime): ?array
    {
        $config = config('ocr.ocrspace');
        $apiKey = $config['api_key'] ?? null;

        if (empty($apiKey) || ! is_file($absolutePath)) {
            return null;
        }

        // OCR.space free plan membatasi ukuran file. Kecilkan gambar besar.
        [$konten, $namaFile, $dim] = $this->siapkanKonten($absolutePath, $mime);

        try {
            $response = Http::timeout($config['timeout'] ?? 30)
                ->asMultipart()
                ->attach('file', $konten, $namaFile)
                ->post($config['url'], [
                    'apikey'            => $apiKey,
                    'language'          => $config['language'] ?? 'eng',
                    'OCREngine'         => (string) ($config['engine'] ?? 2),
                    'scale'             => 'true',
                    'isTable'           => 'false',
                    'detectOrientation' => 'true',
                    'isOverlayRequired' => 'false',
                ]);

            if (! $response->successful()) {
                Log::warning('OCR.space HTTP ' . $response->status() . ': ' . $response->body());
                return null;
            }

            $data = $response->json();

            if (! empty($data['IsErroredOnProcessing'])) {
                Log::warning('OCR.space error: ' . json_encode($data['ErrorMessage'] ?? 'unknown'));
                return null;
            }

            $teks = '';
            $lines = [];
            foreach ($data['ParsedResults'] ?? [] as $result) {
                $teks .= ($result['ParsedText'] ?? '') . "\n";

                foreach ($result['TextOverlay']['Lines'] ?? [] as $line) {
                    $words = [];
                    foreach ($line['Words'] ?? [] as $w) {
                        $words[] = [
                            'text'   => $w['WordText'] ?? '',
                            'left'   => (float) ($w['Left'] ?? 0),
                            'top'    => (float) ($w['Top'] ?? 0),
                            'width'  => (float) ($w['Width'] ?? 0),
                            'height' => (float) ($w['Height'] ?? 0),
                        ];
                    }
                    $lines[] = [
                        'text'  => $line['LineText'] ?? trim(implode(' ', array_column($words, 'text'))),
                        'words' => $words,
                    ];
                }
            }

            $teks = trim($teks);
            if ($teks === '') {
                return null;
            }

            return [
                'text'  => $teks,
                'lines' => $lines,
                'image' => ['width' => $dim[0] ?? null, 'height' => $dim[1] ?? null],
            ];
        } catch (\Throwable $e) {
            Log::error('OCR.space exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Siapkan konten untuk dikirim. Gambar yang besar dikecilkan (resize +
     * kompres JPEG) agar lolos batas ukuran OCR.space free plan (~1 MB).
     * PDF dikirim apa adanya.
     *
     * @return array{0:string,1:string,2:array{0:?int,1:?int}} [konten, nama file, [w,h] gambar dikirim]
     */
    private function siapkanKonten(string $path, string $mime): array
    {
        $isImage = str_contains($mime, 'image') || preg_match('/\.(jpe?g|png)$/i', $path);
        $maxBytes = 1024 * 1024; // target < 1 MB

        // Dimensi asli (untuk gambar yang tidak di-resize).
        $dim = [null, null];
        if ($isImage) {
            $size = @getimagesize($path);
            if ($size) { $dim = [$size[0], $size[1]]; }
        }

        if (! $isImage || ! function_exists('imagecreatefromstring') || filesize($path) <= $maxBytes) {
            return [file_get_contents($path), basename($path), $dim];
        }

        try {
            $img = @imagecreatefromstring(file_get_contents($path));
            if ($img === false) {
                return [file_get_contents($path), basename($path), $dim];
            }

            $w = imagesx($img);
            $h = imagesy($img);
            $maxDim = 1600;
            $scale = min(1, $maxDim / max($w, $h));
            $nw = (int) ($w * $scale);
            $nh = (int) ($h * $scale);

            $canvas = imagecreatetruecolor($nw, $nh);
            imagecopyresampled($canvas, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);

            // Turunkan kualitas bertahap hingga < target ukuran.
            $quality = 85;
            $data = null;
            do {
                ob_start();
                imagejpeg($canvas, null, $quality);
                $data = ob_get_clean();
                $quality -= 10;
            } while (strlen($data) > $maxBytes && $quality >= 35);

            imagedestroy($img);
            imagedestroy($canvas);

            return [$data, pathinfo($path, PATHINFO_FILENAME) . '.jpg', [$nw, $nh]];
        } catch (\Throwable $e) {
            Log::warning('OCR resize gagal, kirim asli: ' . $e->getMessage());
            return [file_get_contents($path), basename($path), $dim];
        }
    }
}
