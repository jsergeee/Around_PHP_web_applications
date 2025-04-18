<?php

namespace App\Commands;

use App\Application;
use App\Database\SQLite;
use App\EventSender\EventSender;
use App\Models\Event;
use App\Telegram\TelegramApiImpl;

class HandleEventsCommand extends Command
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    // public function run(array $options = []): void
    // {
    //     echo "HandleEventsCommand выполняется...\n";
    //     echo "Запуск приложения...\n"; // Отладочное сообщение

    //     $eventModel = new Event(new SQLite($this->app));
    //     $events = $eventModel->select();
    //     echo "Получено событий: " . count($events) . "\n";

    //     // Получаем токен из конфигурации
    //     $token = $this->app->env('TELEGRAM_TOKEN'); // Убедитесь, что этот метод возвращает правильный токен
    //     $tgApi = new TelegramApiImpl($token); // Передаем токен в TelegramApiImpl
    //     $eventSender = new EventSender($tgApi); // Передаем tgApi в EventSender

    //     foreach ($events as $event) {
    //         if ($this->shouldEventBeRan($event)) {
    //             echo "Отправка сообщения на ID: " . $event['receiverId'] . "\n"; // Используем массив
    //             $eventSender->sendMessage($event['receiverId'], $event['text']); // Используем массив
    //         } else {
    //             echo "Событие не выполнено для ID: " . $event['receiverId'] . "\n"; // Используем массив
    //         }
    //     }
    // }

    public function run(array $options = []): void
    {
        echo "HandleEventsCommand выполняется...\n";
        echo "Запуск приложения...\n"; // Отладочное сообщение
    
        $eventModel = new Event(new SQLite($this->app));
        $events = $eventModel->select();
        echo "Получено событий: " . count($events) . "\n";
    
        // Получаем токен из конфигурации
        $token = $this->app->env('TELEGRAM_TOKEN'); // Убедитесь, что этот метод возвращает правильный токен
        $tgApi = new TelegramApiImpl($token); // Передаем токен в TelegramApiImpl
        $eventSender = new EventSender($tgApi); // Передаем tgApi в EventSender
    
        foreach ($events as $event) {
            // Отправляем сообщение немедленно, без проверки времени
            echo "Отправка сообщения на ID: " . $event['receiverId'] . "\n"; // Используем массив
            $eventSender->sendMessage($event['receiverId'], $event['text']); // Используем массив
        }
    }
    



    private function shouldEventBeRan(array $event): bool
    {
        $currentMinute = date("i");
        $currentHour = date("H");
        $currentDay = date("d");
        $currentMonth = date("m");
        $currentWeekday = date("w");

        return ($event['minute'] === $currentMinute &&
                $event['hour'] === $currentHour &&
                $event['day'] === $currentDay &&
                $event['month'] === $currentMonth &&
                $event['weekDay'] === $currentWeekday);
    }
}
