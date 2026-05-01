<?php
$errors = [];
$success_message = "";

// Проверка на пустые строки на стороне сервера
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["first_name"])) {
        $errors[] = "Поле с именем обязательно к заполнению";
    }

    if (empty($_POST["last_name"])) {
        $errors[] = "Поле с фамилией обязательно к заполнению";
    }

    if (empty($_POST["email"])) {
        $errors[] = "Поле с электронной почтой обязательно к заполнению";
    }

    if (empty($_POST["phone_number"])) {
        $errors[] = "Поле с номером телефона обязательно к заполнению";
    }

    if (empty($_POST["topic"])) {
        $errors[] = "Поле с тематикой обязательно для выбора";
    }

    if (empty($_POST["payment"])) {
        $errors[] = "Поле с способом оплаты обязательно для выбора";
    }

    // Валидация Email на стороне сервера
    if (
        !empty($_POST["email"]) &&
        !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)
    ) {
        $errors[] = "Некорректный формат электронной почты";
    }

    // Валидация номера телефона на стороне сервера
    $phone = $_POST["phone_number"];
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

    if (empty($errors)) {
        $data = [
            date("Y-m-d H:i:s"),
            htmlspecialchars($_POST["first_name"]),
            htmlspecialchars($_POST["last_name"]),
            htmlspecialchars($_POST["email"]),
            htmlspecialchars($_POST["phone_number"]),
            htmlspecialchars($_POST["topic"]),
            htmlspecialchars($_POST["payment"]),
            isset($_POST["subscribe"]) ? "Да" : "Нет",
        ];
        $file_name = "request.csv";
        $file_exists = file_exists($file_name);

        $file = fopen($file_name, "a");

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
            ];
            fputcsv($file, $headers, ";");
        }

        fputcsv($file, $data, ";");

        fclose($file);

        $success_message = "Заявка успешно принята!";
        $_POST = [];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Server Lab</title>
    <meta charset='utf-8'>
    <meta name='viewport' content="width=device-width, initial-scale=1">
    <style>
        .error { color: red; }
        .success { color: green; font-weight: bold; }
        .form_fields { margin: 5px}
    </style>
</head>
<body>

    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <p class="success"><?= $success_message ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <!-- Имя --!>
        <div>
            <label for="first_name">Имя:</label>
            <input type="text" id="first_name" name="first_name" class="form_fields"
                   value="<?= isset($_POST["first_name"])
                       ? htmlspecialchars($_POST["first_name"])
                       : "" ?>">
        </div>

        <!-- Фамилия --!>
        <div>
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name" class="form_fields"
                   value="<?= isset($_POST["last_name"])
                       ? htmlspecialchars($_POST["last_name"])
                       : "" ?>">
        </div>

        <!-- Почта --!>
        <div>
            <label for="email">email:</label>
            <input type="email" id="email" name="email" class="form_fields" required
                   value="<?= isset($_POST["email"])
                       ? htmlspecialchars($_POST["email"])
                       : "" ?>">
        </div>

        <!-- Телефон --!>
        <div>
            <label for="phone_number">Номер телефона:</label>
            <input type="tel" id="phone_number" name="phone_number" class="form_fields" placeholder="+7 (999) 999-99-99"
                   pattern="^(\+7|8|7)?[\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$"
                   title="Введите номер в формате +7 (999) 999-99-99"
                   required
                   value="<?= isset($_POST["phone_number"])
                       ? htmlspecialchars($_POST["phone_number"])
                       : "" ?>">
        </div>

        <!-- Тематика --!>
        <div class="conference_topics">
            <div>
                <input type="radio" id="topic_business" name="topic" value="buisness"
                    <?= isset($_POST["topic"]) && $_POST["topic"] === "buisness"
                        ? "checked"
                        : "" ?>>
                <label for="topic_business">Бизнес</label>
            </div>
            <div>
                <input type="radio" id="topic_techonologies" name="topic" value="technologies"
                    <?= isset($_POST["topic"]) &&
                    $_POST["topic"] === "technologies"
                        ? "checked"
                        : "" ?>>
                <label for="topic_technologies">Технологии</label>
            </div>
            <div>
                <input type="radio" id="topic_ad_and_marketing" name="topic" value="ad_and_marketing"
                    <?= isset($_POST["topic"]) &&
                    $_POST["topic"] === "ad_and_marketing"
                        ? "checked"
                        : "" ?>>
                <label for="topic_ad_and_marketing">Бизнес</label>
            </div>
        </div>

        <!-- Способ оплаты --!>
        <div class="payment_method">
            <div>
                <input type="radio" id="WebMoney" name="payment" value="WebMoney"
                    <?= isset($_POST["payment"]) &&
                    $_POST["payment"] === "WebMoney"
                        ? "checked"
                        : "" ?>>
                <label for="WebMoney">WebMoney</label>
            </div>
            <div>
                <input type="radio" id="Yandex.Money" name="payment" value="Yandex.Money"
                    <?= isset($_POST["payment"]) &&
                    $_POST["payment"] === "Яндекс.Деньги"
                        ? "checked"
                        : "" ?>>
                <label for="Yandex.Money">Яндекс.Деньги</label>
            </div>
            <div>
                <input type="radio" id="PayPal" name="payment" value="PayPal"
                    <?= isset($_POST["payment"]) &&
                    $_POST["payment"] === "PayPal"
                        ? "checked"
                        : "" ?>>
                <label for="PayPal">PayPal</label>
            </div>
            <div>
                <input type="radio" id="credit_card" name="payment" value="credit_card"
                    <?= isset($_POST["payment"]) &&
                    $_POST["payment"] === "credit_card"
                        ? "checked"
                        : "" ?>>
                <label for="credit_card">Кредитная карта</label>
            </div>
        </div>
        <div>
            <input type="checkbox" id="subscribe" name="subscribe" value="yes"
                <?= isset($_POST["subscribe"]) ? "checked" : "" ?>>
            <label for="subscribe">Получать рассылку о конференции</label>
        </div>

        <br>
        <div>
            <button type="submit">Отправить</button>
        </div>
    </form>

</body>
</html>
