<?php
session_start();

// Классы
require_once "Db.php";
require_once "CalendarRequest.php";

// Обработка отправки формы (Создание новой задачи)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task = new CalendarRequest($_POST);
    $errors = $task->validate();

    if (empty($errors)) {
        if ($task->save()) {
            $_SESSION["success_message"] = "Задача успешно добавлена!";
        } else {
            $_SESSION["errors"] = ["Произошла ошибка при сохранении в базу данных."];
            $_SESSION["old_data"] = $_POST;
        }
    } else {
        $_SESSION["errors"] = $errors;
        $_SESSION["old_data"] = $_POST;
    }

    // Редирект на себя же, чтобы сбросить POST-запрос (защита от двойной отправки при обновлении страницы F5)
    header("Location: index.php");
    exit();
}

// Получаем сообщения и старые данные из сессии
$errors = $_SESSION["errors"] ?? [];
$success_message = $_SESSION["success_message"] ?? "";
$old_data = $_SESSION["old_data"] ?? [];

// Очищаем сессию, чтобы сообщения не висели вечно
unset($_SESSION["errors"], $_SESSION["success_message"], $_SESSION["old_data"]);

// Получаем текущий фильтр из GET-параметра (по умолчанию - текущие)
$current_filter = $_GET['filter'] ?? 'current';

// Получаем список задач из БД
$tasks = CalendarRequest::getByFilter($current_filter);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content="width=device-width, initial-scale=1">
    <title>Мой календарь</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        form, .task-list-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 { margin-top: 0; color: #4A90E2; }

        .form-group { margin-bottom: 15px; }

        label {
            font-weight: 600;
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }

        .form_fields {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        textarea.form_fields { resize: vertical; min-height: 80px; }

        .form_fields:focus {
            border-color: #4A90E2;
            outline: none;
            box-shadow: 0 0 5px rgba(74,144,226,0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4A90E2;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover { background-color: #357ABD; }

        /* Уведомления */
        .error, .success {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; text-align: center; }

        /* Стили для списка задач */
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .filters a {
            text-decoration: none;
            color: #555;
            font-weight: 600;
        }
        .filters a.active { color: #4A90E2; border-bottom: 2px solid #4A90E2; padding-bottom: 10px; margin-bottom: -12px;}

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f5f9; }
        .empty-msg { text-align: center; color: #777; font-style: italic; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <h2>Новая задача</h2>

        <div class="form-group">
            <label for="topic">Тема:</label>
            <input type="text" id="topic" name="topic" class="form_fields"
                   value="<?= htmlspecialchars($old_data["topic"] ?? "") ?>">
        </div>

        <div class="form-group">
            <label for="type">Тип:</label>
            <select id="type" name="type" class="form_fields">
                <option value="">-- Выберите тип --</option>
                <option value="Встреча" <?= ($old_data["type"] ?? "") === "Встреча" ? "selected" : "" ?>>Встреча</option>
                <option value="Звонок" <?= ($old_data["type"] ?? "") === "Звонок" ? "selected" : "" ?>>Звонок</option>
                <option value="Совещание" <?= ($old_data["type"] ?? "") === "Совещание" ? "selected" : "" ?>>Совещание</option>
                <option value="Дело" <?= ($old_data["type"] ?? "") === "Дело" ? "selected" : "" ?>>Дело</option>
            </select>
        </div>

        <div class="form-group">
            <label for="place">Место:</label>
            <input type="text" id="place" name="place" class="form_fields"
                   value="<?= htmlspecialchars($old_data["place"] ?? "") ?>">
        </div>

        <div class="form-group">
            <label for="date_time">Дата и время:</label>
            <input type="datetime-local" id="date_time" name="date_time" class="form_fields"
                   value="<?= htmlspecialchars($old_data["date_time"] ?? "") ?>">
        </div>

        <div class="form-group">
            <label for="duration">Длительность:</label>
            <input type="text" id="duration" name="duration" class="form_fields" placeholder="Например: 1 час"
                   value="<?= htmlspecialchars($old_data["duration"] ?? "") ?>">
        </div>

        <div class="form-group">
            <label for="comment">Комментарий:</label>
            <textarea id="comment" name="comment" class="form_fields"><?= htmlspecialchars($old_data["comment"] ?? "") ?></textarea>
        </div>

        <button type="submit">Добавить задачу</button>
    </form>

    <div class="task-list-container">
        <h2>Список задач</h2>

        <div class="filters">
            <a href="index.php?filter=current" class="<?= $current_filter === 'current' ? 'active' : '' ?>">Текущие</a>
            <a href="index.php?filter=completed" class="<?= $current_filter === 'completed' ? 'active' : '' ?>">Выполненные</a>
            <a href="index.php?filter=overdue" class="<?= $current_filter === 'overdue' ? 'active' : '' ?>">Просроченные</a>
        </div>

        <?php if (empty($tasks)): ?>
            <p class="empty-msg">В этой категории пока нет задач.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>Тип</th>
                    <th>Задача (Тема)</th>
                    <th>Место</th>
                    <th>Дата и время</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tasks as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['type']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $t['id'] ?>" style="color: #4A90E2; text-decoration: none;">
                                <?= htmlspecialchars($t['topic']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($t['place'] ?: '-') ?></td>
                        <td><?= htmlspecialchars($t['date_time'] ? date('d.m.Y H:i', strtotime($t['date_time'])) : 'Без времени') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

</body>
</html>