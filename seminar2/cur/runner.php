#!/usr/bin/env php
<?php

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Путь к директории приложения
define('DIR', __DIR__); // Используем текущую директорию

// Подключаем автозагрузчик Composer
require DIR . '/vendor/autoload.php';

// Загружаем переменные окружения из .env файла
$dotenv = Dotenv\Dotenv::createImmutable(DIR);
$dotenv->load();

// Загружаем приложение
$app = require DIR . '/app/bootstrap.php';

echo "Application loaded successfully.\n";

$result = $app->run();
echo "App run result: " . var_export($result, true) . "\n";

exit($result);
