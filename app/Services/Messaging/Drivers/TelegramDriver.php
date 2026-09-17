<?php

namespace App\Services\Messaging\Drivers;

use App\Services\Messaging\Contracts\MessagingDriverInterface;

class TelegramDriver implements MessagingDriverInterface
{
    public function getName(): string
    {
        return 'telegram';
    }

    public function getLabel(): string
    {
        return 'Telegram';
    }

    public function getIcon(): string
    {
        return 'bi bi-telegram';
    }

    public function getOutlineButtonClass(): string
    {
        return 'btn-outline-telegram';
    }

    public function getSolidButtonClass(): string
    {
        return 'btn-telegram';
    }

    public function getShareUrl(string $text, ?string $url = null): string
    {
        $params = [];
        if ($url) {
            $params['url'] = $url;
        }
        $params['text'] = $text;

        return 'https://t.me/share/url?' . http_build_query($params);
    }


    public function getDirectChatUrl(string $recipient, string $message): string
    {
        $clean = trim($recipient);

        // Jika nomor telepon
        $digitsOnly = preg_replace('/[^0-9]/', '', $clean);
        if (!empty($digitsOnly)) {
            if (str_starts_with($digitsOnly, '0')) {
                $digitsOnly = '62' . substr($digitsOnly, 1);
            } elseif (!str_starts_with($digitsOnly, '62')) {
                $digitsOnly = '62' . $digitsOnly;
            }
            $target = '+' . $digitsOnly;
        } else {
            // Jika username Telegram
            $target = ltrim($clean, '@');
        }

        return 'https://t.me/' . $target . '?' . http_build_query(['text' => $message]);
    }
}
