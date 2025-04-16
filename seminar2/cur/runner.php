#!/usr/bin/env php
<?php

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Путь к директории приложения
define('DIR', __DIR__);

// Загружаем приложение
$app = require DIR . '/app/bootstrap.php';

echo "Application loaded successfully.\n";

// Проверяем, переданы ли аргументы командной строки
if ($argc > 1) {
    $command = $argv[1];
    echo "Command: $command\n";
} else {
    echo "No command provided.\n";
}

// Запускаем приложение и выводим результат
$result = $app->run();
echo "App run result: " . var_export($result, true) . "\n";

exit($result);
