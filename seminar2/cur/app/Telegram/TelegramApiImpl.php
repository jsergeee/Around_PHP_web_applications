<?php
namespace App\Telegram;

interface TelegramApi {
    public function getMessage(int $offset): array; // Метод объявлен здесь
    public function sendMessage(string $chatId, string $text): void;
}

class TelegramApiImpl implements TelegramApi {
    const ENDPOINT = "https://api.telegram.org/bot";
    private int $offset;
    private string $token;

    public function __construct(string $token) 
    {
        $this->token = $token;
    }

    public function getMessage(int $offset): array
    {
        $url = self::ENDPOINT . $this->token . '/getUpdates?timeout+1';
        $result = [];
        $_result = []; // Инициализация массива для хранения результатов

        while (true) {
            $sh = curl_init("{$url}&offset={$offset}");

            curl_setopt($sh, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); // Исправлено
            curl_setopt($sh, CURLOPT_RETURNTRANSFER, true);

            $response = json_decode(curl_exec($sh), true); // Добавлен второй параметр true для ассоциативного массива

            if (!$response['ok'] || empty($response['result'])) break; // Исправлено условие

            foreach ($response['result'] as $data) {
                $_result[$data['message']['chat']['id']] = [$data['message']['text']]; // Исправлено
                $offset = $data['update_id'] + 1; // Исправлено имя поля и добавлено +1 для обновления offset
            }

            curl_close($sh); // Исправлено
            if (count($response['result']) < 100) break;
        }

        return [
            'offset' => $offset,
            'result' => $_result,
        ];
    }

    public function sendMessage(string $chatId, string $text): void
    {
        $url = self::ENDPOINT . $this->token . '/sendMessage';

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        $ch = curl_init($url);
        $jsonData = json_encode($data);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);
        curl_close($ch);
    }
}
