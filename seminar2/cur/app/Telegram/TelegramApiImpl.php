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
        $url = self::ENDPOINT . $this->token . '/getUpdates?timeout=1';
        $result = [];
        $_result = []; // Инициализация массива для хранения результатов

        while (true) {
            $sh = curl_init("{$url}&offset={$offset}");

            curl_setopt($sh, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); // Исправлено
            curl_setopt($sh, CURLOPT_RETURNTRANSFER, true);

            $response = json_decode(curl_exec($sh), true); // Добавлен второй параметр true для ассоциативного массива

            echo "Ответ от Telegram: " . json_encode($response) . "\n"; // Выводим ответ для отладки

            if (!$response['ok'] || empty($response['result'])) break; // Исправлено условие

            foreach ($response['result'] as $data) {
                $_result[$data['message']['chat']['id']] = [$data['message']['text']]; // Исправлено
                $offset = $data['update_id'] + 1; // Исправлено имя поля и добавлено +1 для обновления offset
            }

            curl_close($sh); // Исправлено
            if (count($response['result']) < 100) break;
        }

        return [
            'result' => $_result,
            'offset' => $offset,
            
        ];
    }

    public function sendMessage(string $chatId, string $text): void
    {
        echo "Попытка отправить сообщение в чат ID: $chatId с текстом: $text\n"; // Отладочное сообщение
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
    
        // Выполняем запрос
        $response = curl_exec($ch);
    
        // Проверка на ошибки cURL
        if (curl_errno($ch)) {
            echo 'Ошибка cURL: ' . curl_error($ch) . "\n";
        } else {
            // Декодируем ответ
            $responseData = json_decode($response, true);
    
            // Проверяем статус ответа от Telegram
            if (isset($responseData['ok']) && $responseData['ok']) {
                echo "Сообщение успешно отправлено в чат ID: $chatId\n";
            } else {
                // Выводим ошибку, если сообщение не было отправлено
                echo "Ошибка отправки сообщения: " . ($responseData['description'] ?? 'Неизвестная ошибка') . "\n";
            }
        }
    
        curl_close($ch);
    }
    
}
