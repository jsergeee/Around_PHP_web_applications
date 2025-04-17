<?php

namespace App\Database;

use App\Application;
use PDO;
use PDOException;

class SQLite extends Db
{
    private Application $app;

    public function __construct(Application $app)
    {
        echo "Конструктор SQLite вызывается.\n"; // Отладочный вывод
        $this->app = $app;

        // Путь к базе данных
        $databasePath = '/home/sergey/myDocuments/Around_PHP_web_applications/seminar2/cur/database/new_database.sqlite';
        echo "Путь к базе данных: $databasePath\n"; // Выводим путь для отладки

        // Проверяем, существует ли файл базы данных
        if (!file_exists($databasePath)) {
            echo "Файл базы данных не существует: $databasePath\n";
            exit;
        }

        // Проверяем права доступа
        if (!is_readable($databasePath) || !is_writable($databasePath)) {
            echo "Нет прав на чтение или запись файла базы данных: $databasePath\n";
            exit;
        }

        try {
            // Инициализация соединения с базой данных SQLite
            parent::__construct(
                'sqlite:' . $databasePath,
                null,
                null,
                array(PDO::ATTR_PERSISTENT => true)
            );

            // Устанавливаем режим обработки ошибок
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Обработка ошибок подключения
            echo 'Ошибка подключения: ' . $e->getMessage();
            exit;
        }
    }

    // Метод для выполнения SQL-запросов
public function execute(string $sql, array $parameters = []): bool
{
    try {
        $stmt = $this->prepare($sql);
        echo "Выполняется запрос: $sql\n"; // Выводим запрос
        print_r($parameters); // Выводим параметры
        return $stmt->execute($parameters);
    } catch (PDOException $e) {
        echo 'Ошибка выполнения запроса: ' . $e->getMessage();
        return false;
    }
}


    // Метод для вставки события в таблицу event
    public function saveEvent(string $name, int $receiver, string $text, string $cron): bool
    {
        
        $sql = "INSERT INTO event (name, receiver, text, cron) VALUES (:name, :receiver, :text, :cron)";
        echo "Выполняется запрос: $sql\n";
        return $this->execute($sql, [
            ':name' => $name,
            ':receiver' => $receiver,
            ':text' => $text,
            ':cron' => $cron
        ]);
    }
}
