<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
session_start();
require_once "ConferenceRequest.php";

// Обработка удаления заявок
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["delete_ids"])) {
    ConferenseRequest::softDelete($_POST["delete_ids"]);
    header("Location: admin.php");
    exit();
}

// Получаем данные через класс
$requestsData = ConferenseRequest::readAll();
$headers = $requestsData["headers"] ?? [];
$data = $requestsData["data"] ?? [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Панель администратора</title>
    <meta charset='utf-8'>
    <meta name='viewport' content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7f6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 95%;
            max-width: 1200px;
            overflow-x: auto;
        }
        h2 { color: #333; margin-bottom: 20px; text-align: center; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th { background-color: #f8f9fa; color: #555; }
        tr:hover { background-color: #f1f1f1; }

        button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover { background-color: #c0392b; }

        .empty-msg { text-align: center; color: #999; padding: 20px; font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Список заявок на конференцию</h2>

        <?php if (empty($data)): ?>
            <p class="empty-msg">Активных заявок пока нет.</p>
        <?php
            // Выводим все заголовки, кроме последнего (статуса)
            // Перебираем массив заявок (ключ - это номер строки из файла)
            // Выводим данные ячеек, кроме последней (статуса)
            // Выводим все заголовки, кроме последнего (статуса)
            // Перебираем массив заявок (ключ - это номер строки из файла)
            // Выводим данные ячеек, кроме последней (статуса)
            // Выводим все заголовки, кроме последнего (статуса)
            // Перебираем массив заявок (ключ - это номер строки из файла)
            // Выводим данные ячеек, кроме последней (статуса)
            // Выводим все заголовки, кроме последнего (статуса)
            // Перебираем массив заявок (ключ - это номер строки из файла)
            // Выводим данные ячеек, кроме последней (статуса)
            else: ?>
            <form action="admin.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>✔</th>
                            <?php for (
                                $i = 0;
                                $i < count($headers) - 1;
                                $i++
                            ): ?>
                                <th><?= htmlspecialchars($headers[$i]) ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data as $row_num => $row): ?>
                            <tr>
                                <td><input type='checkbox' name='delete_ids[]' value='<?= $row_num ?>'></td>
                                <?php for (
                                    $i = 0;
                                    $i < count($row) - 1;
                                    $i++
                                ): ?>
                                    <td><?= htmlspecialchars($row[$i]) ?></td>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Удалить выбранные</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
