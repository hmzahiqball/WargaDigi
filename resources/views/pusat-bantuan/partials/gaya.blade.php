{{-- Gaya Pusat Bantuan: banner, kartu bab berwarna, akordeon FAQ, dan isi topik bergaya garis waktu. --}}
<style>
    /* Palet tema bab: --pb-c = warna aksen, --pb-bg = latar lembut. */
    .pb-tema-hijau      { --pb-c: #198754; --pb-bg: #E8F5EE; }
    .pb-tema-biru       { --pb-c: #0D6EFD; --pb-bg: #E7F1FF; }
    .pb-tema-kuning     { --pb-c: #B58105; --pb-bg: #FFF4DB; }
    .pb-tema-ungu       { --pb-c: #6F42C1; --pb-bg: #F1EAFE; }
    .pb-tema-tosca      { --pb-c: #0F8C7C; --pb-bg: #E3F7F4; }
    .pb-tema-merah-muda { --pb-c: #C2255C; --pb-bg: #FDEBF1; }
    .pb-tema-indigo     { --pb-c: #4C5BD4; --pb-bg: #ECEEFE; }
    .pb-tema-oranye     { --pb-c: #D9480F; --pb-bg: #FFF0E6; }

    /* Banner */
    .pb-banner { position: relative; overflow: hidden; border-radius: 20px; padding: 2rem 2rem 1.6rem;
        background: linear-gradient(135deg, #157347 0%, #1E9B63 60%, #2BB673 100%); color: #fff; margin-bottom: 1.75rem; }
    .pb-banner::before, .pb-banner::after { content: ""; position: absolute; border-radius: 50%; background: rgba(255,255,255,.08); }
    .pb-banner::before { width: 260px; height: 260px; right: -70px; top: -110px; }
    .pb-banner::after { width: 160px; height: 160px; right: 90px; bottom: -90px; }
    .pb-banner > * { position: relative; z-index: 1; }
    .pb-banner h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: .35rem; }
    .pb-banner .pb-banner-sub { color: #DDF5E8; margin-bottom: 1.1rem; }
    .pb-banner .pb-peran { display: inline-block; background: rgba(255,255,255,.18); border-radius: 999px; padding: .1rem .65rem; font-weight: 600; }
    .pb-chips { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; margin-top: 1rem; }
    .pb-chips span { color: #DDF5E8; font-size: .85rem; margin-right: .25rem; }
    .pb-chips a { background: rgba(255,255,255,.16); color: #fff; border: 1px solid rgba(255,255,255,.25); border-radius: 999px;
        padding: .3rem .85rem; font-size: .85rem; text-decoration: none; transition: background .15s; }
    .pb-chips a:hover { background: rgba(255,255,255,.3); }

    /* Kotak cari */
    .pb-cari-wrap { position: relative; max-width: 760px; }
    .pb-cari { display: flex; align-items: center; background: #fff; border-radius: 999px; padding: .35rem .35rem .35rem 1.1rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.12); border: 1px solid #E9ECEF; }
    .pb-cari i { color: #868E96; font-size: 1.1rem; }
    .pb-cari input { flex: 1; border: 0; outline: 0; padding: .65rem .75rem; font-size: 1.02rem; background: transparent; min-width: 0; color: #212529; }
    .pb-cari input::-webkit-search-cancel-button { cursor: pointer; }
    .pb-cari button { border: 0; border-radius: 999px; background: #157347; color: #fff; font-weight: 700; padding: .6rem 1.4rem; }
    .pb-cari button:hover { background: #0F5C38; }
    .pb-cari-wrap.pb-cari-kecil .pb-cari { box-shadow: 0 2px 8px rgba(0,0,0,.05); }
    .pb-saran { position: absolute; top: 100%; left: 0; right: 0; z-index: 40; margin-top: 6px; max-height: 420px; overflow-y: auto;
        background: #fff; border: 1px solid #E9ECEF; border-radius: 14px; box-shadow: 0 14px 30px rgba(0,0,0,.12); color: #212529; }
    .pb-saran a { display: block; padding: .7rem 1rem; color: inherit; text-decoration: none; border-bottom: 1px solid #F1F3F5; }
    .pb-saran a:last-child { border-bottom: 0; }
    .pb-saran a:hover, .pb-saran a.aktif { background: #E8F5EE; }
    .pb-saran .pb-saran-bab { font-size: .72rem; color: #198754; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
    .pb-saran .pb-saran-ringkas { font-size: .84rem; color: #6C757D; }

    /* Judul bagian halaman */
    .pb-judul-bagian { font-size: 1.1rem; font-weight: 800; color: #212529; margin: 0 0 .9rem; display: flex; align-items: center; gap: .5rem; }
    .pb-judul-bagian small { font-weight: 500; color: #868E96; font-size: .85rem; }

    /* Kartu bab */
    .pb-bab { background: #fff; border: 1px solid #E9ECEF; border-top: 4px solid var(--pb-c); border-radius: 16px; height: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,.03); transition: box-shadow .15s, transform .15s; display: flex; flex-direction: column; }
    .pb-bab:hover { box-shadow: 0 8px 20px rgba(0,0,0,.07); }
    .pb-bab-kepala { display: flex; align-items: center; gap: .8rem; padding: 1rem 1.1rem .6rem; }
    .pb-ikon { width: 44px; height: 44px; border-radius: 12px; background: var(--pb-bg); color: var(--pb-c);
        display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
    .pb-bab-judul { font-weight: 700; color: #212529; line-height: 1.25; }
    .pb-bab-jumlah { font-size: .78rem; color: #868E96; }
    .pb-item { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .62rem 1.1rem;
        color: #343A40; text-decoration: none; border-top: 1px solid #F1F3F5; background: #fff; transition: background .12s, color .12s; }
    .pb-item i { color: #CED4DA; transition: transform .12s, color .12s; }
    .pb-item:hover, .pb-item:focus { background: var(--pb-bg); color: var(--pb-c); }
    .pb-item:hover i, .pb-item:focus i { color: var(--pb-c); transform: translateX(2px); }
    .pb-lainnya { margin-top: auto; border-top: 1px solid #F1F3F5; padding: .55rem 1.1rem; font-size: .85rem; font-weight: 600;
        color: var(--pb-c); background: transparent; border-left: 0; border-right: 0; border-bottom: 0; text-align: left; width: 100%;
        border-radius: 0 0 16px 16px; }
    .pb-lainnya:hover { background: var(--pb-bg); }
    .pb-lainnya .bi { transition: transform .15s; }
    .pb-lainnya[aria-expanded="true"] .bi { transform: rotate(180deg); }

    /* FAQ akordeon */
    .pb-faq { background: #fff; border: 1px solid #E9ECEF; border-radius: 16px; overflow: hidden; }
    .pb-faq details { border-top: 1px solid #F1F3F5; }
    .pb-faq details:first-child { border-top: 0; }
    .pb-faq summary { list-style: none; cursor: pointer; padding: .95rem 1.2rem; font-weight: 600; color: #212529;
        display: flex; align-items: center; gap: .75rem; }
    .pb-faq summary::-webkit-details-marker { display: none; }
    .pb-faq summary::before { content: "\F4FE"; font-family: bootstrap-icons; color: #198754; font-size: 1.1rem; }
    .pb-faq summary::after { content: "\F282"; font-family: bootstrap-icons; margin-left: auto; color: #ADB5BD; transition: transform .15s; }
    .pb-faq details[open] summary { color: #157347; background: #F6FBF8; }
    .pb-faq details[open] summary::after { transform: rotate(180deg); color: #157347; }
    .pb-faq .pb-faq-jawab { padding: .2rem 1.2rem 1rem 3.1rem; color: #495057; }
    .pb-faq .pb-faq-jawab p:last-child { margin-bottom: .4rem; }
    .pb-label-umum { font-size: .7rem; font-weight: 700; color: #6C757D; background: #F1F3F5; border-radius: 999px; padding: .1rem .5rem; }

    /* Hasil pencarian */
    .pb-hasil { display: flex; gap: .9rem; padding: .95rem 1.1rem; text-decoration: none; color: inherit; border-top: 1px solid #F1F3F5; }
    .pb-hasil:first-child { border-top: 0; }
    .pb-hasil:hover { background: #F6FBF8; }
    .pb-hasil .pb-ikon { width: 38px; height: 38px; font-size: 1.05rem; border-radius: 10px; }
    .pb-hasil-bab { font-size: .74rem; font-weight: 700; color: var(--pb-c); text-transform: uppercase; letter-spacing: .03em; }
    .pb-hasil-judul { font-weight: 700; color: #212529; }
    .pb-hasil-ringkas { font-size: .88rem; color: #6C757D; }

    /* ---------------- Halaman topik ---------------- */
    .pb-topik-kepala { display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1.25rem; }
    .pb-topik-kepala .pb-ikon { width: 52px; height: 52px; font-size: 1.5rem; border-radius: 14px; }
    .pb-topik-bab { font-size: .78rem; font-weight: 700; color: var(--pb-c); text-transform: uppercase; letter-spacing: .04em; }
    .pb-topik-judul { font-size: 1.6rem; font-weight: 800; color: #212529; margin: .1rem 0 0; line-height: 1.25; }

    .pb-isi { font-size: 1rem; line-height: 1.75; color: #343A40; }
    .pb-isi > p:first-child { font-size: 1.05rem; color: #495057; }
    .pb-isi h3 { font-size: 1.1rem; font-weight: 800; margin: 1.8rem 0 .7rem; color: #212529; scroll-margin-top: 90px; }
    .pb-isi p { margin-bottom: .75rem; }
    .pb-isi strong { color: #212529; }

    .pb-bagian { border-radius: 14px; margin: 1.4rem 0; scroll-margin-top: 90px; }
    .pb-bagian h4 { font-size: 1rem; font-weight: 800; margin-bottom: .9rem; display: flex; align-items: center; gap: .5rem; }
    .pb-bagian-langkah h4 { color: #157347; }
    .pb-bagian-langkah h4::before { content: "\F47E"; font-family: bootstrap-icons; }
    .pb-bagian-hasil, .pb-bagian-catatan { padding: 1rem 1.25rem; border-left: 4px solid; }
    .pb-bagian-hasil { background: #F0FAF4; border-left-color: #198754; }
    .pb-bagian-hasil h4 { color: #0F5132; }
    .pb-bagian-hasil h4::before { content: "\F26B"; font-family: bootstrap-icons; color: #198754; }
    .pb-bagian-catatan { background: #FFF8E6; border-left-color: #E0A800; }
    .pb-bagian-catatan h4 { color: #664D03; }
    .pb-bagian-catatan h4::before { content: "\F33B"; font-family: bootstrap-icons; color: #E0A800; }
    .pb-bagian-hasil ul, .pb-bagian-catatan ul { padding-left: 1.1rem; margin-bottom: 0; }
    .pb-bagian-hasil li, .pb-bagian-catatan li { margin-bottom: .35rem; }

    /* Langkah sebagai garis waktu */
    .pb-langkah { list-style: none; counter-reset: langkah; padding-left: 0; margin: 0 0 .5rem; }
    .pb-langkah > li { counter-increment: langkah; position: relative; padding: 0 0 1.4rem 3.1rem; }
    .pb-langkah > li::before { content: counter(langkah); position: absolute; left: 0; top: 0; width: 2.1rem; height: 2.1rem; border-radius: 50%;
        background: #198754; color: #fff; font-weight: 800; font-size: .95rem; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 0 4px #E8F5EE; }
    .pb-langkah > li::after { content: ""; position: absolute; left: 1.03rem; top: 2.3rem; bottom: .2rem; width: 2px; background: #D3EEDD; }
    .pb-langkah > li:last-child::after { display: none; }
    .pb-langkah > li > strong:first-child { display: inline; font-size: 1.02rem; }
    .pb-langkah > li ul { margin: .45rem 0 0; padding-left: 1.1rem; }

    .pb-tips { background: #EEF4FF; border-left: 4px solid #0D6EFD; border-radius: 12px; padding: .8rem 1rem .8rem 2.6rem; margin: 1rem 0; position: relative; color: #1F3B70; }
    .pb-tips::before { content: "\F468"; font-family: bootstrap-icons; position: absolute; left: .95rem; top: .75rem; color: #0D6EFD; font-size: 1.05rem; }
    .pb-tips strong:first-child { color: #0A58CA; }

    .pb-gambar { margin: .85rem 0 .3rem; }
    .pb-gambar img { max-width: 100%; height: auto; border-radius: 12px; border: 1px solid #E9ECEF; box-shadow: 0 4px 14px rgba(0,0,0,.06); cursor: zoom-in; }
    .pb-gambar .pb-gambar-hp img { max-width: 290px; }
    .pb-gambar .pb-gambar-tautan + .pb-gambar-tautan:not(.d-none):not(.d-md-none) { margin-top: .6rem; }
    .pb-gambar figcaption { font-size: .82rem; color: #868E96; margin-top: .4rem; display: flex; align-items: center; gap: .35rem; }
    .pb-gambar figcaption::before { content: "\F42A"; font-family: bootstrap-icons; }
    .pb-tabel { font-size: .92rem; border-radius: 10px; overflow: hidden; }
    .pb-tabel th { background: #F1F8F4; color: #0F5132; }

    /* Panel samping topik */
    .pb-panel { background: #fff; border: 1px solid #E9ECEF; border-radius: 16px; overflow: hidden; }
    .pb-panel-judul { font-size: .72rem; font-weight: 800; color: #868E96; text-transform: uppercase; letter-spacing: .05em; padding: 1rem 1.1rem .4rem; }
    .pb-toc a { display: flex; align-items: center; gap: .55rem; padding: .4rem 1.1rem; color: #495057; text-decoration: none; font-size: .92rem; }
    .pb-toc a:hover { color: #157347; background: #F6FBF8; }
    .pb-toc .bi { font-size: .9rem; }
    .pb-toc .pb-toc-langkah .bi { color: #198754; }
    .pb-toc .pb-toc-hasil .bi { color: #198754; }
    .pb-toc .pb-toc-catatan .bi { color: #E0A800; }
    .pb-toc .pb-toc-subjudul .bi { color: #ADB5BD; }
    .pb-sebab a { display: block; padding: .5rem 1.1rem; color: #495057; text-decoration: none; font-size: .92rem; border-left: 3px solid transparent; }
    .pb-sebab a:hover { background: #F6FBF8; color: #157347; }
    .pb-sebab a.aktif { border-left-color: var(--pb-c); background: var(--pb-bg); color: var(--pb-c); font-weight: 700; }
    .pb-navigasi a { display: block; border: 1px solid #E9ECEF; border-radius: 12px; padding: .7rem 1rem; text-decoration: none; color: #212529; height: 100%; }
    .pb-navigasi a:hover { border-color: #198754; background: #F6FBF8; }
    .pb-navigasi small { display: block; color: #868E96; font-size: .75rem; }

    @media (max-width: 767.98px) {
        .pb-banner { padding: 1.4rem 1.1rem 1.2rem; border-radius: 16px; }
        .pb-banner h1 { font-size: 1.35rem; }
        .pb-cari button { padding: .55rem 1rem; }
        .pb-topik-judul { font-size: 1.3rem; }
        .pb-langkah > li { padding-left: 2.7rem; }
        .pb-isi { font-size: .97rem; }
    }
</style>
