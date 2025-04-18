<?php

namespace App\Database;

use PDO;
use PDOException;
use App\Application;

class SQLite
{
    private PDO $pdo;
    private Application $app;public function __construct(Application $app)
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
        $this->pdo = new PDO('sqlite:' . $databasePath, null, null, [PDO::ATTR_PERSISTENT => true]);
        // Устанавливаем режим обработки ошибок
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Обработка ошибок подключения
        echo 'Ошибка подключения: ' . $e->getMessage();
        exit;
    }
}

/**
 * Выполняет SQL-запрос и возвращает результаты.
 *
 * @param string $sql SQL-запрос.
 * @param array $params Параметры для запроса.
 * @return array Результаты запроса в виде ассоциативного массива.
 */
public function query(string $sql, array $params = []): array
{
    try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Возвращаем массив или пустой массив
    } catch (PDOException $e) {
        echo "Ошибка выполнения запроса: " . $e->getMessage();
        return []; // Возвращаем пустой массив в случае ошибки
    }
}

/**
 * Выполняет SQL-запрос без возврата результатов (например, INSERT, UPDATE, DELETE).
 *
 * @param string $sql SQL-запрос.
 * @param array $params Параметры для запроса.
 * @return bool Возвращает true в случае успеха, иначе false.
 */
public function execute(string $sql, array $params = []): bool
{
    try {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params); // Возвращаем результат выполнения
    } catch (PDOException $e) {
        echo "Ошибка выполнения запроса: " . $e->getMessage();
        return false; // Возвращаем false в случае ошибки
    }
}

/**
 * Проверяет, существует ли событие в базе данных.
 *
 * @param string $name Название события.
 * @param string $receiverId Идентификатор получателя.
 * @param string $text Текст события.
 * @param string $cron Расписание события.
 * @return bool Возвращает true, если событие существует, иначе false.
 */
public function eventExists(string $name, string $receiverId, string $text, string $cron): bool
{
    $sql = "SELECT COUNT(*) FROM event WHERE name = ? AND receiver_id = ? AND text = ? AND cron = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $receiverId, $text, $cron]);
        return $stmt->fetchColumn() > 0; // Возвращает true, если событие существует
    }
}
