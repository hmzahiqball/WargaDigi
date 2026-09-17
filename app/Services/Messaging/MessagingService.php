<?php

namespace App\Services\Messaging;

use App\Services\Messaging\Contracts\MessagingDriverInterface;
use App\Services\Messaging\Drivers\TelegramDriver;
use App\Services\Messaging\Drivers\WhatsAppDriver;
use InvalidArgumentException;

class MessagingService
{
    /** @var array<string, MessagingDriverInterface> */
    protected static array $drivers = [];

    public static function driver(?string $name = null): MessagingDriverInterface
    {
        $name = $name ?: config('messaging.default', 'telegram');

        if (!isset(static::$drivers[$name])) {
            static::$drivers[$name] = match (strtolower($name)) {
                'telegram' => new TelegramDriver(),
                'whatsapp' => new WhatsAppDriver(),
                default => throw new InvalidArgumentException("Messaging driver [{$name}] tidak didukung."),
            };
        }

        return static::$drivers[$name];
    }

    public static function getName(): string
    {
        return static::driver()->getName();
    }

    public static function getLabel(): string
    {
        return static::driver()->getLabel();
    }

    public static function getIcon(): string
    {
        return static::driver()->getIcon();
    }

    public static function getOutlineButtonClass(): string
    {
        return static::driver()->getOutlineButtonClass();
    }

    public static function getSolidButtonClass(): string
    {
        return static::driver()->getSolidButtonClass();
    }

    public static function getShareUrl(string $text, ?string $url = null): string
    {
        return static::driver()->getShareUrl($text, $url);
    }

    public static function getDirectChatUrl(string $recipient, string $message): string
    {
        return static::driver()->getDirectChatUrl($recipient, $message);
    }
}
