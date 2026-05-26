<?php

class CalendarRequest
{
    // Данные
    private $id;
    private $topic;
    private $type;
    private $place;
    private $date_time;
    private $duration;
    private $comment;
    private $status;

    public function __construct($postData = [])
    {
        $this->id        = $postData["id"] ?? null;
        $this->topic     = $postData["topic"] ?? null;
        $this->type      = $postData["type"] ?? null;
        $this->place     = $postData["place"] ?? null;
        $this->date_time = $postData["date_time"] ?? null;
        $this->duration  = $postData["duration"] ?? null;
        $this->comment   = $postData["comment"] ?? null;
        $this->status    = $postData["status"] ?? 'new';
    }


    public function getId()
    {
        return $this->id;
    }
    public function getTopic()
    {
        return $this->topic;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getPlace()
    {
        return $this->place;
    }
    public function getDateTime()
    {
        return $this->date_time;
    }
    public function getDuration()
    {
        return $this->duration;
    }
    public function getComment()
    {
        return $this->comment;
    }
    public function getStatus()
    {
        return $this->status;
    }

    public function validate()
    {
        $errors = [];
        //тема
        if (empty($this->topic)) {
            $errors[] = "Поле с темой обязательно к заполнению";
        }
        //тип
        if (empty($this->type)) {
            $errors[] = "Поле с типом обязательно для выбора";
        }
        // Место, дата-время и продолжительность, комментарии оставляем без обязательного заполнения

        return $errors;
    }

    public function save()
    {
        $db = DB::getConnection();

        if ($this->id) {
            // Если ID есть, значит задача уже существует — обновляем её
            $sql = "UPDATE tasks 
                    SET topic = ?, type = ?, place = ?, date_time = ?, duration = ?, comment = ?, status = ? 
                    WHERE id = ?";
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                $this->topic, $this->type, $this->place,
                $this->date_time, $this->duration, $this->comment,
                $this->status, $this->id
            ]);
        } else {
            // ID нет — это создание новой задачи
            $sql = "INSERT INTO tasks (topic, type, place, date_time, duration, comment, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                $this->topic, $this->type, $this->place,
                $this->date_time, $this->duration, $this->comment,
                $this->status
            ]);

            if ($result) {
                $this->id = $db->lastInsertId(); // Запоминаем присвоенный ID
            }
            return $result;
        }
    }

    // Получить вообще все задачи для таблицы снизу
    public static function readAll()
    {
        $db = Db::getConnection();
        $stmt = $db->query("SELECT * FROM tasks ORDER BY date_time ASC");
        return $stmt->fetchAll();
    }

    // получить одну конкретную задачу по ID для карточки редактирования
    public static function find($id)
    {
        $db = Db::getConnection();
        $stmt = $db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // фильтрация задач (текущие, выполненные, просроченные, на дату)
    public static function getByFilter($type, $date = null)
    {
        $db = Db::getConnection();
        $now = date('Y-m-d H:i:s');

        switch ($type) {
            case 'completed':
                // Выполненные
                $stmt = $db->prepare("SELECT * FROM tasks WHERE status = 'completed'");
                $stmt->execute();
                break;
            case 'overdue':
                // Просроченные
                $stmt = $db->prepare("SELECT * FROM tasks WHERE status != 'completed' AND date_time < ?");
                $stmt->execute([$now]);
                break;
            case 'date':
                // Фильтр по конкретному дню
                $stmt = $db->prepare("SELECT * FROM tasks WHERE DATE(date_time) = ?");
                $stmt->execute([$date]);
                break;
            case 'current':
            default:
                // Текущие
                $stmt = $db->prepare("SELECT * FROM tasks WHERE status = 'new' AND (date_time >= ? OR date_time IS NULL)");
                $stmt->execute([$now]);
                break;
        }

        return $stmt->fetchAll();
    }


}