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
    $options = getopt('c:', ['name:', 'receiver:', 'text:', 'cron:', 'help']); // Получаем массив параметров
    echo "Command: " . ($options['c'] ?? 'none') . "\n"; // Вывод команды для отладки

    // Обработка команды save_event
    if (
        isset($options['c']) && $options['c'] === 'save_event' &&
        isset($options['name']) && isset($options['receiver']) &&
        isset($options['text']) && isset($options['cron'])
    ) {
        echo "Обработка команды save_event\n"; // Логируем начало обработки команды
        $name = $options['name']; // Получаем имя
        $receiver = (int)$options['receiver']; // Получаем получателя и преобразуем в целое число
        $text = $options['text']; // Получаем текст
        $cron = $options['cron']; // Получаем cron

        echo "Переданные параметры: name=$name, receiver=$receiver, text=$text, cron=$cron\n";

        // Создаем экземпляр базы данных
        $db = new \App\Database\SQLite($app); // Обратите внимание на пространство имен

        // Проверка существования события
        if ($db->eventExists($name, $receiver, $text, $cron)) {
            echo "Событие уже существует. Пропускаем сохранение.\n";
        } else {
            $result = $db->saveEvent($name, $receiver, $text, $cron); // Вызываем метод saveEvent

            if ($result) {
                echo "Событие успешно сохранено.\n";
            } else {
                echo "Ошибка при сохранении события.\n";
            }
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
