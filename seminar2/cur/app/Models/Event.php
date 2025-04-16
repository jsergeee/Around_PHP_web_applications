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
        // Пример данных, которые могут быть получены из базы данных
        return [
            ['receiverId' => '1', 'text' => 'Hello, User 1!', 'minute' => date("i"), 'hour' => date("H"), 'day' => date("d"), 'month' => date("m"), 'weekDay' => date("w")],
            ['receiverId' => '2', 'text' => 'Hello, User 2!', 'minute' => date("i"), 'hour' => date("H"), 'day' => date("d"), 'month' => date("m"), 'weekDay' => date("w")],
            // другие события
        ];
    }

    public function save(array $data): bool
    {
        // SQL запрос для вставки данных
        $sql = "INSERT INTO event (receiver_id, text, minute, hour, day, month, day_of_week) VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Выполнение SQL-запроса с помощью вашего класса SQLite
        return $this->db->execute($sql, [
            $data['receiver_id'],
            $data['text'],
            $data['minute'],
            $data['hour'],
            $data['day'],
            $data['month'],
            $data['day_of_week']
        ]);
    }
}
