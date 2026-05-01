<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
$file_name = "request.csv";

// Обработка удаления
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_ids"])) {
    $ids_to_delete = $_POST["delete_ids"]; // Это массив, например: [0, 2]

    // Читаем все строки файла в массив
    $all_rows = file($file_name);

    if ($all_rows) {
        $file = fopen($file_name, "w"); // Открываем для перезаписи

        foreach ($all_rows as $current_index => $row_content) {
            // 0-я строка — это всегда заголовки, их не трогаем
            if ($current_index === 0) {
                fwrite($file, $row_content);
                continue;
            }

            // Индекс данных в твоей таблице начинается с 0 для первой анкеты.
            // Но в файле первая анкета — это строка №1 (т.к. строка №0 — заголовки).
            $data_index = $current_index - 1;

            // Если индекса нет в списке на удаление — записываем строку обратно
            if (!in_array($data_index, $ids_to_delete)) {
                fwrite($file, $row_content);
            }
        }
        fclose($file);
        header("Location: admin.php");
        exit();
    }

    // Перезагружаем страницу, чтобы форма обновилась и не было повторной отправки
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

    </style>
</head>
<body>
    <form action="admin.php" method="POST">
<?php
$file = fopen($file_name, "r");
$row_num = 0;
echo "<table>";
$headers = fgetcsv($file, 0, ";", '"', "");
echo "<tr>";
if ($headers) {
    echo "<th> </th>";
    foreach ($headers as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
}

while (($data = fgetcsv($file, 1000, ";", '"', "")) !== false) {
    echo "<tr>";
    // Чекбокс, где value — это индекс строки
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
