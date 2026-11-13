@once
@push('scripts')
<script>
// Ceklis "Gunakan ... di profil" (nomor WA, alamat): isi nilai dari profil dan kunci input;
// lepas ceklis untuk kembali mengetik manual (nilai ketikan sebelumnya dipulihkan).
document.addEventListener('change', function (e) {
    const ceklis = e.target.closest ? e.target.closest('.ceklis-dari-profil') : null;
    if (!ceklis) return;
    const input = document.querySelector(ceklis.dataset.target);
    if (!input) return;
    if (ceklis.checked) {
        input.dataset.nilaiManual = input.value;
        input.value = ceklis.dataset.nilai || '';
        input.readOnly = true;
    } else {
        input.readOnly = false;
        input.value = input.dataset.nilaiManual || '';
        input.focus();
    }
});
</script>
@endpush
@endonce
