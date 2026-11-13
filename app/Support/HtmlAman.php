<?php

namespace App\Support;

/**
 * Pembersih HTML sederhana untuk konten editor (berita/agenda) yang tampil di halaman publik.
 * Hanya tag format teks yang diizinkan; atribut event (on*), style, dan URL berbahaya
 * (javascript:, data:, vbscript:) dibuang. Bukan pengganti validasi saat menyimpan.
 */
class HtmlAman
{
    private const TAG = '<p><br><b><strong><i><em><u><s><strike><ul><ol><li><a><h2><h3><h4><h5><h6><blockquote><span><div><img><table><thead><tbody><tr><th><td><hr><sub><sup>';

    public static function bersihkan(?string $html): string
    {
        $html = (string) $html;
        if ($html === '') {
            return '';
        }

        // Buang blok berbahaya beserta isinya sebelum strip_tags.
        $html = preg_replace('#<(script|style|iframe|object|embed|form|svg|math)\b[^>]*>.*?</\1>#is', '', $html);
        $html = strip_tags($html, self::TAG);

        // Atribut event (onclick=...), style, srcdoc, dan formaction.
        $html = preg_replace('/\s+(on\w+|style|srcdoc|formaction)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

        // href/src hanya boleh http(s), mailto, tel, path relatif, atau anchor.
        $html = preg_replace_callback('/\s(href|src)\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', function ($m) {
            $nilai = html_entity_decode($m[3] !== '' ? $m[3] : ($m[4] ?? '') . ($m[5] ?? ''), ENT_QUOTES | ENT_HTML5);
            $bersih = preg_replace('/[\x00-\x20]+/', '', $nilai);
            if (preg_match('#^(https?:|mailto:|tel:|/|\#|[a-z0-9_.\-]+(/|$))#i', $bersih) && ! preg_match('#^(javascript|data|vbscript):#i', $bersih)) {
                return ' ' . strtolower($m[1]) . '="' . htmlspecialchars($nilai, ENT_QUOTES) . '"';
            }

            return '';
        }, $html);

        // Tautan keluar dibuka aman.
        return preg_replace('/<a\s(?![^>]*\brel=)/i', '<a rel="noopener nofollow" ', $html);
    }
}
