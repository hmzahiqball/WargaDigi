<?php

namespace Tests\Feature;

use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Regression test IR51: pengiriman pengingat WhatsApp fail-safe.
 */
class NotificationWhatsAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalisasi_nomor_ke_format_internasional(): void
    {
        $this->assertSame('6281234567890', NotificationService::normalizeGatewayNumber('081234567890'));
        $this->assertSame('6281234567890', NotificationService::normalizeGatewayNumber('+62 812-3456-7890'));
        $this->assertSame('6281234567890', NotificationService::normalizeGatewayNumber('81234567890'));
        $this->assertSame('', NotificationService::normalizeGatewayNumber('-'));
    }

    public function test_tanpa_gateway_dikonfigurasi_tidak_memanggil_http_dan_tetap_sukses_mode_log(): void
    {
        config(['services.whatsapp.url' => null, 'services.whatsapp.token' => null]);
        Http::fake();

        $result = NotificationService::sendWhatsApp('081234567890', 'Pesan uji');

        $this->assertTrue($result);
        Http::assertNothingSent();
    }

    public function test_dengan_gateway_dikonfigurasi_mengirim_http_ke_endpoint(): void
    {
        config([
            'services.whatsapp.url' => 'https://gateway.test/send',
            'services.whatsapp.token' => 'secret-token',
            'services.whatsapp.sender' => '62800000000',
            'services.whatsapp.timeout' => 10,
        ]);

        Http::fake([
            'gateway.test/*' => Http::response(['status' => true], 200),
        ]);

        $result = NotificationService::sendWhatsApp('081234567890', 'Pesan uji');

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'gateway.test/send')
                && $request['target'] === '6281234567890';
        });
    }
}
