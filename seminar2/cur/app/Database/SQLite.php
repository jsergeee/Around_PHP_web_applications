<?php

namespace App\Database;

use App\Application;
use PDO;
use PDOException;

class SQLite extends Db
{
    private $pdo;
    private Application $app;

    public function __construct(Application $app)
    {
        echo "Конструктор SQLite вызывается.\n"; // Отладочный вывод
        $this->app = $app;

        // Путь к базе данных
        $databasePath = '/home/sergey/myDocuments/Around_PHP_web_applications/seminar2/cur/database/new_database.sqlite';

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
            $this->pdo = new PDO('sqlite:' . $databasePath, null, null, array(PDO::ATTR_PERSISTENT => true));
            // Устанавливаем режим обработки ошибок
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($parameters);
        } catch (PDOException $e) {
            echo 'Ошибка выполнения запроса: ' . $e->getMessage();
            return false;
        }
    }

    // Метод для вставки события в таблицу event

    public function saveEvent(string $name, int $receiver, string $text, string $cron): bool
    {
        echo "Вызов saveEvent: name=$name, receiver=$receiver, text=$text, cron=$cron\n";

        // Экранирование потенциально опасных символов
        $name = trim(strip_tags($name));
        $text = trim(strip_tags($text));

        // Разбиваем cron на части
        $cronParts = explode(' ', $cron);
        $minute = $cronParts[0] ?? null;
        $hour = $cronParts[1] ?? null;
        $day = $cronParts[2] ?? null;
        $month = $cronParts[3] ?? null;
        $day_of_week = $cronParts[4] ?? null;

        try {
            $stmt = $this->pdo->prepare("INSERT INTO event (name, receiver_id, text, cron, minute, hour, day, month, day_of_week) VALUES (:name, :receiver_id, :text, :cron, :minute, :hour, :day, :month, :day_of_week)");

            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':receiver_id', $receiver, PDO::PARAM_INT);
            $stmt->bindValue(':text', $text, PDO::PARAM_STR);
            $stmt->bindValue(':cron', $cron, PDO::PARAM_STR); // Добавляем cron
            $stmt->bindValue(':minute', $minute, PDO::PARAM_STR);
            $stmt->bindValue(':hour', $hour, PDO::PARAM_STR);
            $stmt->bindValue(':day', $day, PDO::PARAM_STR);
            $stmt->bindValue(':month', $month, PDO::PARAM_STR);
            $stmt->bindValue(':day_of_week', $day_of_week, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Ошибка при сохранении события: ' . $e->getMessage());
        }

        return false;
    }

    // Новый метод для проверки существования события
    public function eventExists(string $name, int $receiver, string $text, string $cron): bool
    {
        $sql = "SELECT COUNT(*) FROM event WHERE name = ? AND receiver_id = ? AND text = ? AND cron = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $receiver, $text, $cron]);
        return $stmt->fetchColumn() > 0; // Возвращает true, если событие существует
    }
}
