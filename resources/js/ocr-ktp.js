// OCR di browser memakai tesseract.js (client-side, gratis, privat).
// Menyediakan OCR untuk KTP (full image) dan KK (crop header + enhance).

let workerPromise = null;

async function getWorker(onProgress) {
    if (workerPromise) return workerPromise;

    workerPromise = (async () => {
        const { createWorker } = await import('tesseract.js');
        const worker = await createWorker('ind+eng', 1, {
            logger: (m) => {
                if (onProgress && m.status === 'recognizing text') {
                    onProgress(Math.round((m.progress || 0) * 100));
                }
            },
        });
        // Parameter yang cocok untuk dokumen KTP:
        // - PSM 6: anggap satu blok teks seragam (layout KTP cukup rapi per baris).
        // - preserve spasi antar kata agar label & nilai tidak menyatu.
        try {
            await worker.setParameters({
                tessedit_pageseg_mode: '6',
                preserve_interword_spaces: '1',
            });
        } catch (e) { /* versi worker tertentu abaikan */ }
        return worker;
    })();

    return workerPromise;
}

/** Muat file gambar menjadi HTMLImageElement. */
function loadImage(file) {
    return new Promise((resolve, reject) => {
        const url = URL.createObjectURL(file);
        const img = new Image();
        img.onload = () => { resolve(img); URL.revokeObjectURL(url); };
        img.onerror = reject;
        img.src = url;
    });
}

/**
 * Pra-proses gambar ke canvas: opsional crop (rasio), upscale, grayscale + kontras.
 * @returns {HTMLCanvasElement}
 */
function preprocess(img, opts = {}) {
    const cropTop = opts.cropTopRatio || 0;      // buang bagian atas (0..1)
    const cropBottom = opts.cropBottomRatio || 0; // simpan sampai rasio ini dari atas (0..1), 0 = full
    const scale = opts.scale || 1;

    const sy = Math.round(img.naturalHeight * cropTop);
    const sh = Math.round(img.naturalHeight * ((cropBottom || 1) - cropTop));
    const sw = img.naturalWidth;

    const canvas = document.createElement('canvas');
    canvas.width = Math.round(sw * scale);
    canvas.height = Math.round(sh * scale);
    const ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, sy, sw, sh, 0, 0, canvas.width, canvas.height);

    if (opts.enhance || opts.binarize) {
        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const d = imgData.data;

        // 1) Grayscale + sedikit kontras, sekaligus kumpulkan histogram untuk Otsu.
        const contrast = opts.contrast || 1.3;
        const intercept = 128 * (1 - contrast);
        const hist = new Array(256).fill(0);
        const gray = new Uint8ClampedArray(d.length / 4);
        for (let i = 0, j = 0; i < d.length; i += 4, j++) {
            let v = 0.299 * d[i] + 0.587 * d[i + 1] + 0.114 * d[i + 2];
            v = contrast * v + intercept;
            v = v < 0 ? 0 : (v > 255 ? 255 : v);
            const g = v | 0;
            gray[j] = g;
            hist[g]++;
        }

        if (opts.binarize) {
            // 2) Threshold global Otsu: pisahkan teks (gelap) dari latar (terang).
            const total = gray.length;
            let sum = 0;
            for (let t = 0; t < 256; t++) sum += t * hist[t];
            let sumB = 0, wB = 0, maxVar = -1, threshold = 128;
            for (let t = 0; t < 256; t++) {
                wB += hist[t];
                if (wB === 0) continue;
                const wF = total - wB;
                if (wF === 0) break;
                sumB += t * hist[t];
                const mB = sumB / wB;
                const mF = (sum - sumB) / wF;
                const between = wB * wF * (mB - mF) * (mB - mF);
                if (between > maxVar) { maxVar = between; threshold = t; }
            }
            // Sedikit bias agar stroke huruf tidak terlalu tipis.
            threshold = Math.min(255, threshold + 8);
            for (let i = 0, j = 0; i < d.length; i += 4, j++) {
                const bw = gray[j] > threshold ? 255 : 0;
                d[i] = d[i + 1] = d[i + 2] = bw;
            }
        } else {
            for (let i = 0, j = 0; i < d.length; i += 4, j++) {
                d[i] = d[i + 1] = d[i + 2] = gray[j];
            }
        }

        ctx.putImageData(imgData, 0, 0);
    }

    return canvas;
}

async function recognize(source, onProgress) {
    const worker = await getWorker(onProgress);
    const { data } = await worker.recognize(source);
    return (data && data.text) ? data.text : '';
}

/** OCR KTP: gambar penuh, upscale agar tinggi huruf memadai + binarisasi Otsu. */
export async function ocrKtpBrowser(file, onProgress) {
    let source = file;
    try {
        const img = await loadImage(file);
        // Target lebar efektif ~1600px: tesseract andal bila tinggi huruf ~20-30px.
        const w = img.naturalWidth || 1000;
        let scale = 1600 / w;
        if (scale < 1) scale = 1;       // jangan perkecil
        if (scale > 3) scale = 3;       // batasi beban memori
        source = preprocess(img, { scale: scale, enhance: true, binarize: true, contrast: 1.4 });
    } catch (e) { /* fallback ke file asli */ }
    return recognize(source, onProgress);
}

/** OCR KK: crop HEADER (bagian atas) + upscale kuat + enhance agar teks kecil terbaca. */
export async function ocrKkBrowser(file, onProgress) {
    let source = file;
    try {
        const img = await loadImage(file);
        // Header KK ada di ~40% bagian atas. Upscale 2.2x + enhance.
        source = preprocess(img, { cropTopRatio: 0, cropBottomRatio: 0.42, scale: 2.2, enhance: true });
    } catch (e) { /* fallback ke file asli */ }
    return recognize(source, onProgress);
}

// Ekspos ke global untuk skrip Blade inline.
window.ocrKtpBrowser = ocrKtpBrowser;
window.ocrKkBrowser = ocrKkBrowser;
