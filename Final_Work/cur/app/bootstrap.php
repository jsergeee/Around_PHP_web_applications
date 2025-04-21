<?php

use App\Application;

require __DIR__.'/../vendor/autoload.php';

return new Application(dirname(__DIR__));

// Загружаем переменные окружения из .env файла
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$dotenv->load();

// Отладочный вывод
echo "Переменные окружения загружены:\n";
print_r($_ENV); // Выводим все переменные окружения