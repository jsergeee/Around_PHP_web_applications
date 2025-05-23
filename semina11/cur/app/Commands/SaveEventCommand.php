<?php

namespace App\Commands;

use App\Application;
use App\Database\SQLite;
use App\Models\Event;

class SaveEventCommand extends Command
{
    protected Application $app;
public function __construct(Application $app)
{
    $this->app = $app;
}

public function run(array $options = []): void
{
    echo "Запуск команды save_event\n"; // Логируем начало выполнения команды
    $options = $this->getGetoptOptionValues(); // Получаем массив параметров

    if ($this->isNeedHelp($options)) {
        $this->showHelp();
        return;
    }

    $cronValues = $this->getCronValues($options['cron']); // Получаем массив Cron

    $params = [
        'name' => $options['name'],
        'text' => $options['text'],
        'receiver_id' => $options['receiver'],
        'cron' => $options['cron'],     // Добавляем сюда cron явно
        'minute' => $cronValues[0],
        'hour' => $cronValues[1],
        'day' => $cronValues[2],
        'month' => $cronValues[3],
        'day_of_week' => $cronValues[4]
    ];

    // Проверка существования события перед сохранением
    $databaseConnection = new SQLite($this->app);
    if ($databaseConnection->eventExists($params['name'], $params['receiver_id'], $params['text'], $params['cron'])) {
        echo "Событие уже существует. Пропускаем сохранение.\n";
        return; // Выход из метода, если событие уже существует
    }

    $this->saveEvent($params);
    echo "Параметры для сохранения: " . json_encode($params) . "\n"; // Логируем параметры
}

private function getGetoptOptionValues(): array
{
    $shortopts = 'c:h:';
    $longopts = [
        'command:',
        'name:',
        'text:',
        'receiver:',
        'cron:',
        'help:'
    ];

    return getopt($shortopts, $longopts);
}

private function isNeedHelp(array $options): bool
{
    return !isset($options['name']) ||
           !isset($options['text']) ||
           !isset($options['receiver']) ||
           !isset($options['cron']) ||
           isset($options['help']) ||
           isset($options['h']);
}

private function showHelp(): void
{
    echo "Это тестовый скрипт добавления правил\n\n"
        . "Чтобы добавить правило нужно перечислить следующие поля:\n"
        . "--name Имя события\n"
        . "--text Текст, который будет отправлен по событию\n"
        . "--cron Расписание отправки в формате cron\n"
        . "--receiver Идентификатор получателя сообщения\n\n"
        . "Для справки используйте флаги -h или --help\n";
}

private function getCronValues(string $cronString): array
{
    $cronValues = explode(" ", $cronString);
    return array_map(function ($item) {
        return $item === "*" ? null : $item;
    }, $cronValues);
}

private function saveEvent(array $params): void
{
    $databaseConnection = new SQLite($this->app);
    $event = new Event($databaseConnection);

    // Добавляем cron в массив параметров
    $eventData = [
        'name' => $params['name'],
        'receiver_id' => $params['receiver_id'],
        'text' => $params['text'],
            'cron' => $params['cron'],  // Добавляем сюда cron
            'minute' => $params['minute'],
            'hour' => $params['hour'],
            'day' => $params['day'],
            'month' => $params['month'],
            'day_of_week' => $params['day_of_week']
        ];

        if ($event->save($eventData)) {
            echo "Событие успешно сохранено.\n";
        } else {
            echo "Ошибка при сохранении события.\n";
        }
    }
}
