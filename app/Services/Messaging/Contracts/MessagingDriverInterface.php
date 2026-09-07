<?php

namespace App\Services\Messaging\Contracts;

interface MessagingDriverInterface
{
 
    public function getName(): string;

    public function getLabel(): string;

    public function getIcon(): string;

    public function getOutlineButtonClass(): string;

    public function getSolidButtonClass(): string;

    public function getShareUrl(string $text, ?string $url = null): string;

    public function getDirectChatUrl(string $recipient, string $message): string;
}
