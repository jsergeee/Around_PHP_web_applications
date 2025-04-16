<?php

namespace App\Database;

use App\Application;
use PDO;

class SQLite extends Db
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;

        // Инициализация соединения с базой данных SQLite
        parent::__construct(
            'sqlite:' . $this->app->env('SQLITE_DATABASE'),
            null,
            null,
            array(PDO::ATTR_PERSISTENT => true)
        );
        
        // Устанавливаем режим обработки ошибок
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Метод для выполнения SQL-запросов
    public function execute(string $sql, array $parameters = []): bool
    {
        $stmt = $this->prepare($sql); // Подготовка запроса
        return $stmt->execute($parameters); // Выполнение запроса с параметрами
    }
}
