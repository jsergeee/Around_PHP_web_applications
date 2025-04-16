<?php

declare(ticks=1);

namespace App\Commands;

use App\Application;

class HandleEventsDaemonCommand extends Command
{
    protected Application $app;

    const CACHE_PATH = __DIR__ . '/../../cache.txt';

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
        $lastData = $this->getLastData();
        $handleEventsCommand = new HandleEventsCommand($this->app);

        while (true) {
            $currentTime = $this->getCurrentTime();
            echo "Текущее время: " . json_encode($currentTime) . "\n";
            echo "Последние данные: " . json_encode($lastData) . "\n";

            // Приводим оба массива к строковому типу
            if ($lastData === array_map('strval', $currentTime)) {
                sleep(10);
                continue;
            }

            echo "Время изменилось, выполняем HandleEventsCommand...\n";
            $handleEventsCommand->run($options);
            $lastData = $currentTime;

            sleep(10);
        }
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

    private function getLastData(): array
    {
        if (!file_exists(self::CACHE_PATH)) {
            return [];
        }

        $lastData = file_get_contents(self::CACHE_PATH);
        if ($lastData === false) {
            echo "Ошибка при чтении кэш-файла.\n";
            return [];
        }

        return json_decode($lastData, true) ?? [];
    }


}