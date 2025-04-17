#!/usr/bin/env php
<?php

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Путь к директории приложения
define('DIR', __DIR__); // Используем текущую директорию

// Загружаем приложение
$app = require DIR . '/app/bootstrap.php';

echo "Application loaded successfully.\n";

// Проверяем, переданы ли аргументы командной строки
if ($argc > 1) {
    $command = $argv[1];
    echo "Command: $command\n";

    // Обработка команды save_event
    if ($command === 'save_event' && $argc === 6) {
        $name = $argv[2]; // Получаем имя
        $receiver = (int)$argv[3]; // Получаем получателя и преобразуем в целое число
        $text = $argv[4]; // Получаем текст
        $cron = $argv[5]; // Получаем cron

        echo "Переданные параметры: name=$name, receiver=$receiver, text=$text, cron=$cron\n";

        // Создаем экземпляр базы данных
        $db = new \App\Database\SQLite($app); // Обратите внимание на пространство имен
        $result = $db->saveEvent($name, $receiver, $text, $cron); // Вызываем метод saveEvent

        if ($result) {
            echo "Событие успешно сохранено.\n";
        } else {
            echo "Ошибка при сохранении события.\n";
        }
    } else {
        echo "Неверное количество аргументов для команды save_event.\n";
    }
} else {
    echo "No command provided.\n";
}

// Запускаем приложение и выводим результат
$result = $app->run();
echo "App run result: " . var_export($result, true) . "\n";

exit($result);
