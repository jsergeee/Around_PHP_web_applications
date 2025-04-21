<?php
namespace App\Commands;

use App\Application;
use App\Telegram\TelegramApiImpl;

class TgMessagesCommand extends Command
{
    public function __construct(public Application $app)
    {
    }

    function run(array $options = []): void
    {
        $tgApi = new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN'));
        $messages = $tgApi->getMessage(0); // Получаем сообщения

        if (empty($messages['result'])) {
            echo "Нет новых сообщений.\n"; // Если нет сообщений
        } else {
            foreach ($messages['result'] as $chatId => $texts) {
                foreach ($texts as $text) {
                    echo "Получено сообщение от ID: $chatId с текстом: $text\n"; // Выводим сообщение
                    if (trim($text) === '/start') {
                        $tgApi->sendMessage($chatId, "Добро пожаловать! Как я могу помочь?");
                    }
                    
                }
            }
        }
    }
}