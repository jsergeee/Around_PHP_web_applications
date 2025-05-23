<?php

namespace App\Models;

use App\Database\SQLite;

class Event
{
    private SQLite $db;

    public function __construct(SQLite $db)
    {
        $this->db = $db;
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
}
