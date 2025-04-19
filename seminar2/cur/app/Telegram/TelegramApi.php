<?php

namespace App\Telegram;

interface TelegramApi {
    public function __construct(string $token);


    public function getMessage(int $offset): array;

    public function sendMessage(string $chatid, string $text);
}

