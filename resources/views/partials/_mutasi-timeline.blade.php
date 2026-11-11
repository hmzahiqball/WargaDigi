{{-- Timeline tahap pengajuan mutasi warga. Membutuhkan: $mutasi (App\Models\MutasiWarga). --}}
@php
    $tahapan = $mutasi->timeline();
@endphp
<div class="mutasi-timeline">
    @foreach($tahapan as $tahap)
        @php
            $state = $tahap['state'] ?? 'pending';
            [$ikon, $warna] = match ($state) {
                'done'     => ['bi-check-circle-fill', 'text-success'],
                'rejected' => ['bi-x-circle-fill', 'text-danger'],
                default    => ['bi-clock-fill', 'text-secondary'],
            };
            $waktu = $tahap['at'] ?? null;
            if ($waktu) {
                try {
                    $waktuLabel = $waktu->locale('id')->translatedFormat('d M Y H:i') . ' WIB';
                } catch (\Throwable $e) {
                    $waktuLabel = $waktu->format('d M Y H:i') . ' WIB';
                }
            } else {
                $waktuLabel = '-';
            }
        @endphp
        <div class="d-flex gap-3 pb-3 position-relative">
            <div class="d-flex flex-column align-items-center">
                <i class="bi {{ $ikon }} {{ $warna }} fs-5"></i>
                @unless($loop->last)
                    <span class="flex-grow-1" style="width:2px;background:#e2e8f0;margin-top:2px;min-height:18px;"></span>
                @endunless
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $tahap['label'] ?? '-' }}</div>
                <div class="text-muted" style="font-size:0.8rem;">{{ $waktuLabel }}</div>
                @if(! empty($tahap['oleh']))
                    <div class="text-muted" style="font-size:0.8rem;">Oleh: {{ $tahap['oleh'] }}</div>
                @endif
                @if(! empty($tahap['catatan']))
                    <div class="mt-1 p-2 bg-danger bg-opacity-10 text-danger rounded-2" style="font-size:0.8rem;">
                        <i class="bi bi-chat-left-text me-1"></i>{{ $tahap['catatan'] }}
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
