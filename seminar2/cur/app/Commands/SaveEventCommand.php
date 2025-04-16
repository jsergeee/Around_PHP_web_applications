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
        $options = $this->getGetoptOptionValues();

        if ($this->isNeedHelp($options)) {
            $this->showHelp();
            return;
        }

        $cronValues = $this->getCronValues($options['cron']);

        if (count($cronValues) !== 5) {
            $this->showHelp();
            return;
        }

        $params = [
            'name' => $options['name'],
            'text' => $options['text'],
            'receiver_id' => $options['receiver'],
            'minute' => $cronValues[0],
            'hour' => $cronValues[1],
            'day' => $cronValues[2],
            'month' => $cronValues[3],
            'day_of_week' => $cronValues[4]
        ];

        $this->saveEvent($params);
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

    private function showHelp()
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
        $databaseConnection = new SQLite($this->app); // Создание соединения с базой данных
        $event = new Event($databaseConnection); // Передаем объект SQLite

        // Собираем данные для сохранения
        $eventData = [
            'receiver_id' => $params['receiver_id'],
            'text' => $params['text'],
            'minute' => $params['minute'],
            'hour' => $params['hour'],
            'day' => $params['day'],
            'month' => $params['month'],
            'day_of_week' => $params['day_of_week']
        ];

        // Сохраняем данные
        if ($event->save($eventData)) {
            echo "Событие успешно сохранено.\n";
        } else {
            echo "Ошибка при сохранении события.\n";
        }
    }
}
