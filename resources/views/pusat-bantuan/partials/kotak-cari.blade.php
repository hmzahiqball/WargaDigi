{{-- Kotak pencarian Pusat Bantuan dengan saran saat mengetik. $kecil = versi ringkas (halaman topik). --}}
<form action="{{ route('bantuan.index') }}" method="GET" role="search" class="pb-cari-wrap {{ ($kecil ?? false) ? 'pb-cari-kecil' : '' }}" data-testid="form-cari-bantuan" autocomplete="off">
    <label for="pbCari" class="visually-hidden">Cari panduan</label>
    <div class="pb-cari">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" id="pbCari" name="q" value="{{ $q ?? '' }}" maxlength="100" data-testid="input-cari-bantuan"
               placeholder="Contoh: cara mengajukan surat"
               aria-autocomplete="list" aria-controls="pbSaran" aria-expanded="false">
        <button type="submit">Cari</button>
    </div>
    <div id="pbSaran" class="pb-saran d-none" role="listbox" data-testid="saran-bantuan"></div>
</form>

@push('scripts')
<script>
(function () {
    const input = document.getElementById('pbCari');
    const kotak = document.getElementById('pbSaran');
    if (!input || !kotak) return;
    const urlCari = @json(route('bantuan.cari'));
    let timer = null, pilih = -1, nomorPermintaan = 0;

    const tutup = () => { kotak.classList.add('d-none'); kotak.innerHTML = ''; input.setAttribute('aria-expanded', 'false'); pilih = -1; };
    const tandai = (i) => {
        const item = kotak.querySelectorAll('a');
        item.forEach((a, k) => a.classList.toggle('aktif', k === i));
        if (item[i]) item[i].scrollIntoView({ block: 'nearest' });
    };
    const tampil = (data) => {
        kotak.innerHTML = '';
        if (!data.length) { tutup(); return; }
        data.forEach((d) => {
            const a = document.createElement('a');
            a.href = d.url;
            a.setAttribute('role', 'option');
            const bab = document.createElement('div'); bab.className = 'pb-saran-bab'; bab.textContent = d.bab;
            const judul = document.createElement('div'); judul.className = 'fw-semibold'; judul.textContent = d.judul;
            const ringkas = document.createElement('div'); ringkas.className = 'pb-saran-ringkas'; ringkas.textContent = d.ringkasan || '';
            a.append(bab, judul, ringkas);
            kotak.appendChild(a);
        });
        kotak.classList.remove('d-none');
        input.setAttribute('aria-expanded', 'true');
        pilih = -1;
    };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { tutup(); return; }
        timer = setTimeout(async () => {
            const nomor = ++nomorPermintaan;
            try {
                const res = await fetch(urlCari + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const json = await res.json();
                if (nomor === nomorPermintaan) tampil(json.data || []);
            } catch (e) { /* jaringan putus: pengguna tetap bisa menekan Cari */ }
        }, 250);
    });

    input.addEventListener('keydown', (e) => {
        const item = kotak.querySelectorAll('a');
        if (!item.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); pilih = Math.min(pilih + 1, item.length - 1); tandai(pilih); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); pilih = Math.max(pilih - 1, 0); tandai(pilih); }
        else if (e.key === 'Enter' && pilih >= 0) { e.preventDefault(); window.location = item[pilih].href; }
        else if (e.key === 'Escape') { tutup(); }
    });
    document.addEventListener('click', (e) => { if (!kotak.contains(e.target) && e.target !== input) tutup(); });
})();
</script>
@endpush
