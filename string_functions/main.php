<?php
$errors = [];
$success_message = "";
$user_ip = $_SERVER["REMOTE_ADDR"] ?: "unknown";

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
            $_POST["first_name"],
            $_POST["last_name"],
            $_POST["email"],
            $_POST["phone_number"],
            $_POST["topic"],
            $_POST["payment"],
            isset($_POST["subscribe"]) ? "Да" : "Нет",
            $user_ip,
            "1",
        ];
        $file_name = "request.txt";
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
                "IP",
                "active",
            ];
            fputcsv($file, $headers, "|");
        }

        fputcsv($file, $data, "|");

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
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f4f7f6;
                color: #333;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 40px 20px;
            }

            form {
                background: #fff;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                max-width: 450px;
                width: 100%;
            }

            .form_fields {
                width: 100%;
                padding: 10px;
                margin: 8px 0 20px 0;
                border: 1px solid #ddd;
                border-radius: 6px;
                box-sizing: border-box;
                font-size: 16px;
            }

            .form_fields:focus {
                border-color: #4A90E2;
                outline: none;
                box-shadow: 0 0 5px rgba(74,144,226,0.3);
            }

            label {
                font-weight: 600;
                font-size: 14px;
                display: block;
                margin-bottom: 5px;
            }

            /* Стилизация радио-кнопок и чекбоксов */
            .conference_topics, .payment_method {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .conference_topics div, .payment_method div {
                margin-bottom: 8px;
            }

            input[type="radio"], input[type="checkbox"] {
                margin-right: 10px;
                cursor: pointer;
            }

            /* Кнопка */
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

            button:hover {
                background-color: #357ABD;
            }

            /* Уведомления */
            .error {
                background: #ffebee;
                color: #c62828;
                padding: 15px;
                border-radius: 8px;
                list-style: none;
                max-width: 450px;
                width: 100%;
                margin-bottom: 20px;
                border: 1px solid #ffcdd2;
            }

            .success {
                background: #e8f5e9;
                color: #2e7d32;
                padding: 15px;
                border-radius: 8px;
                text-align: center;
                max-width: 450px;
                width: 100%;
                margin-bottom: 20px;
                border: 1px solid #c8e6c9;
            }
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
                <label for="topic_ad_and_marketing">Реклама и маркетинг</label>
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
