<?php

namespace App\Services\Messaging\Drivers;

use App\Services\Messaging\Contracts\MessagingDriverInterface;

class WhatsAppDriver implements MessagingDriverInterface
{
    public function getName(): string
    {
        return 'whatsapp';
    }

    public function getLabel(): string
    {
        return 'WhatsApp';
    }

    public function getIcon(): string
    {
        return 'bi bi-whatsapp';
    }

    public function getOutlineButtonClass(): string
    {
        return 'btn-outline-whatsapp';
    }

    public function getSolidButtonClass(): string
    {
        return 'btn-whatsapp';
    }

    public function getShareUrl(string $text, ?string $url = null): string
    {
        $fullText = $text;
        if ($url && !str_contains($text, $url)) {
            $fullText = $text . "\n" . $url;
        }

        return 'https://api.whatsapp.com/send?' . http_build_query(['text' => $fullText]);
    }

    public function getDirectChatUrl(string $recipient, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $recipient);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '62') && !empty($cleanPhone)) {
            $cleanPhone = '62' . $cleanPhone;
        }

        return 'https://wa.me/' . $cleanPhone . '?' . http_build_query(['text' => $message]);
    }
}
