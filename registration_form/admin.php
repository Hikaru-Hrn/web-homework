<?php
// Для дебага
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
$file_name = "request.csv";

// Обработка удаления
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_ids"])) {
    $ids_to_delete = $_POST["delete_ids"];

    $all_rows = file($file_name);

    if ($all_rows) {
        $file = fopen($file_name, "w");

        foreach ($all_rows as $current_index => $row_content) {
            // 0-я строка — заголовки
            if ($current_index === 0) {
                fwrite($file, $row_content);
                continue;
            }

            $data_index = $current_index - 1;

            if (!in_array($data_index, $ids_to_delete)) {
                fwrite($file, $row_content);
            }
        }
        fclose($file);
        header("Location: admin.php");
        exit();
    }

    header("Location: admin.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Server Lab</title>
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
            h2 { color: #333; margin-bottom: 20px; }
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

            .empty-msg { text-align: center; color: #999; padding: 20px; }
    </style>
</head>
<body>
    <form action="admin.php" method="POST">
<?php
$file = fopen($file_name, "r");
$row_num = 0;
echo "<table>";
$headers = fgetcsv($file, 0, ";", '"', "");

//Заголовки
echo "<tr>";
if ($headers) {
    echo "<th> </th>";
    foreach ($headers as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
}

// Содержание таблицы
while (($data = fgetcsv($file, 1000, ";", '"', "")) !== false) {
    echo "<tr>";
    echo "<td><input type='checkbox' name='delete_ids[]' value='$row_num'></td>";

    foreach ($data as $cell) {
        echo "<td>" . htmlspecialchars($cell) . "</td>";
    }
    echo "</tr>";
    $row_num++;
}
echo "</table>";
fclose($file);
?>

<button type="submit">Удалить выбранные</button>
</form>
</body>
