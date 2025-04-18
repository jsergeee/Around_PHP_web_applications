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
        // Проверяем, существует ли событие
        if ($this->db->eventExists($data['name'], $data['receiver_id'], $data['text'], $data['cron'])) {
            echo "Событие уже существует. Пропускаем сохранение.\n";
            return false; // Или выбросьте исключение, если нужно
        }

        echo "Вызов метода save в классе Event\n"; // Логируем вызов метода save
        $sql = "INSERT INTO event (name, receiver_id, text, cron, minute, hour, day, month, day_of_week) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Добавь доп. вывод массива data
        // var_dump($data);

        return $this->db->execute($sql, [
            $data['name'],
            $data['receiver_id'],
            $data['text'],
            $data['cron'],                  // Присваиваем cron
            $data['minute'],
            $data['hour'],
            $data['day'],
            $data['month'],
            $data['day_of_week']
        ]);
    }
}
