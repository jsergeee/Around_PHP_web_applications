<?php

declare(ticks=1);

namespace App\Commands;

use App\Application;
use App\Database\SQLite;

class HandleEventsDaemonCommand extends Command
{
    protected Application $app;

    const CACHE_PATH = DIR . '/../../cache.txt';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    private function initCacheFile(): void
    {
        if (!file_exists(self::CACHE_PATH)) {
            $initialData = $this->getCurrentTime();
            file_put_contents(self::CACHE_PATH, json_encode($initialData));
        }
    }

    public function run(array $options = []): void
    {
        echo "HandleEventsCommand выполняется...\n";

        $this->initPcntl();

        $this->daemonRun($options);

        echo "Script finished\n";
    }

    private function initPcntl(): void
    {
        $callback = function ($signal) {
            switch ($signal) {
                case SIGTERM:
                case SIGINT:
                case SIGHUP:
                    $lastData = $this->getCurrentTime();
                    $lastData[0] = $lastData[0] - 1;

                    file_put_contents(self::CACHE_PATH, json_encode($lastData));
                    exit;
            }
        };

        pcntl_signal(SIGTERM, $callback);
        pcntl_signal(SIGHUP, $callback);
        pcntl_signal(SIGINT, $callback);
    }

    private function daemonRun(array $options)
    {
        $handleEventsCommand = new HandleEventsCommand($this->app);
        $events = $this->getScheduledEvents(); // Получаем все запланированные события

        while (true) {
            $currentTime = $this->getCurrentTime();
            echo "Текущее время: " . json_encode($currentTime) . "\n";

            foreach ($events as $event) {
                if ($this->shouldRunEvent($event['cron'], $currentTime)) {
                    echo "Время для события '{$event['name']}', выполняем HandleEventsCommand...\n";
                    $handleEventsCommand->run($options);
                }
            }

            sleep(60); // Проверяем каждую минуту
        }
    }

    private function shouldRunEvent(string $cron, array $currentTime): bool
    {
        $cronValues = explode(" ", $cron);
        return (
            ($cronValues[0] === '*' || (int)$cronValues[0] === (int)$currentTime[0]) &&
            ($cronValues[1] === '*' || (int)$cronValues[1] === (int)$currentTime[1]) &&
            ($cronValues[2] === '*' || (int)$cronValues[2] === (int)$currentTime[2]) &&
            ($cronValues[3] === '*' || (int)$cronValues[3] === (int)$currentTime[3]) &&
            ($cronValues[4] === '*' || (int)$cronValues[4] === (int)$currentTime[4])
        );
    }

    private function getScheduledEvents(): array
    {
        // Здесь нужно реализовать логику получения запланированных событий из базы данных
        $databaseConnection = new SQLite($this->app);
        return $databaseConnection->getAllEvents(); // Предполагается, что есть метод для получения всех событий
    }

    private function getCurrentTime(): array
    {
        return [
            date("i"),
            date("H"),
            date("d"),
            date("m"),
            date("w")
        ];
    }
}
