<?php

namespace App\Models;

use App\Database\SQLite;

class Event
{
    private SQLite $db;

    public function __construct(SQLite $db)
    {
        $this->db = $db; // Устанавливаем соединение с базой данных
    }

    public function select(): array
    {
        // SQL-запрос для получения всех необходимых полей из таблицы event
        $query = "SELECT receiver_id AS receiverId, text, minute, hour, day, month, day_of_week AS weekDay FROM event";

        // Выполняем запрос и получаем результаты
        $results = $this->db->query($query);

        // Возвращаем результаты, предполагая, что $results - это массив ассоциативных массивов
        return $results;
    }

    public function save(array $data): bool
    {
        // Проверяем, существует ли событие
        if ($this->db->eventExists($data['name'], $data['receiver_id'], $data['text'], $data['cron'])) {
            echo "Событие уже существует. Пропускаем сохранение.\n";
            return false; // Или выбросьте исключение, если нужно
        }

        echo "Вызов метода save в классе Event\n"; // Логируем вызов метода save
        $sql = "INSERT INTO event (name, receiver_id, text, cron, minute, hour, day, month, day_of_week) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->db->execute($sql, [
            $data['name'],
            $data['receiver_id'],
            $data['text'],
            $data['cron'],                  
            $data['minute'],
            $data['hour'],
            $data['day'],
            $data['month'],
            $data['day_of_week']
        ]);
    }
}
