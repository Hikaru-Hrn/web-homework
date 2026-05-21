<?php

class ConferenseRequest
{
    // Данные
    private $first_name;
    private $last_name;
    private $email;
    private $phone_number;
    private $topic;
    private $payment;
    private $subscribe;
    private $date;
    private $userIP;
    private $active;

    // Конструктор
    public function __construct($postData = [])
    {
        $this->first_name = $postData["first_name"] ?? "";
        $this->last_name = $postData["last_name"] ?? "";
        $this->email = $postData["email"] ?? "";
        $this->phone_number = $postData["phone_number"] ?? "";
        $this->topic = $postData["topic"] ?? "";
        $this->payment = $postData["payment"] ?? "";
        $this->subscribe = isset($postData["subscribe"]) ? "Да" : "Нет";
        $this->date = date("Y-m-d H:i:s");
        $this->userIP = $_SERVER["REMOTE_ADDR"] ?: "unknown";
        $this->active = "1";
    }

    // Валидация на стороне сервера
    public function validate()
    {
        $errors = [];
        if (empty($this->first_name)) {
            $errors[] = "Поле с именем обязательно к заполнению";
        }

        if (empty($this->last_name)) {
            $errors[] = "Поле с фамилией обязательно к заполнению";
        }

        if (empty($this->email)) {
            $errors[] = "Поле с электронной почтой обязательно к заполнению";
        }

        if (empty($this->phone_number)) {
            $errors[] = "Поле с номером телефона обязательно к заполнению";
        }

        if (empty($this->topic)) {
            $errors[] = "Поле с тематикой обязательно для выбора";
        }

        if (empty($this->payment)) {
            $errors[] = "Поле с способом оплаты обязательно для выбора";
        }

        // Валидация Email
        if (
            !empty($this->email) &&
            !filter_var($this->email, FILTER_VALIDATE_EMAIL)
        ) {
            $errors[] = "Некорректный формат электронной почты";
        }

        // Валидация номера телефона
        $phone = $this->phone_number;
        if (
            !empty($phone) &&
            !preg_match(
                "/^(\+7|8|7)?\s?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{2}[\s.-]?\d{2}$/",
                $phone,
            )
        ) {
            $errors[] =
                "Неверный формат номера телефона. Используйте формат +7 (XXX) XXX-XX-XX";
        }

        return $errors;
    }

    // Сохранение файла
    public function save()
    {
        $data = [
            $this->date,
            $this->first_name,
            $this->last_name,
            $this->email,
            $this->phone_number,
            $this->topic,
            $this->payment,
            $this->subscribe,
            $this->userIP,
            $this->active,
        ];
        $file_name = "request.txt";
        $file_exists = file_exists($file_name);

        $file = fopen($file_name, "a");

        // Создаем заголовки если файл не существует
        if (!$file_exists) {
            $headers = [
                "Дата и время",
                "Имя",
                "Фамилия",
                "Email",
                "Телефон",
                "Тематика",
                "Оплата",
                "Рассылка",
                "IP",
                "active",
            ];
            fputcsv($file, $headers, "|");
        }
        fputcsv($file, $data, "|");

        fclose($file);
    }

    // Чтение файла для
    public static function readAll()
    {
        $file_name = "request.txt";
        if (!file_exists($file_name)) {
            return ["headers" => [], "data" => []];
        }

        $file = fopen($file_name, "r");
        $row_num = 0;
        $data = [];

        $headers = fgetcsv($file, 0, "|", '"', "");
        $row_num++;

        while (($row = fgetcsv($file, 1000, "|", '"', "")) !== false) {
            if (isset($row[9]) && trim($row[9]) === "0") {
                $row_num++;
                continue;
            }
            $data[$row_num] = $row;
            $row_num++;
        }

        fclose($file);
        return ["headers" => $headers, "data" => $data];
    }

    public static function softDelete($idsToDelete)
    {
        $file_name = "request.txt";
        if (!file_exists($file_name)) {
            return;
        }

        $all_rows = file($file_name);
        $file = fopen($file_name, "w");

        foreach ($all_rows as $current_index => $row_content) {
            if ($current_index === 0) {
                fwrite($file, $row_content);
                continue;
            }

            $data_index = $current_index;

            if (in_array($data_index, $idsToDelete)) {
                $row_data = str_getcsv($row_content, "|");
                $row_data[9] = "0";
                fputcsv($file, $row_data, "|");
            } else {
                fwrite($file, $row_content);
            }
        }
        fclose($file);
    }
}
