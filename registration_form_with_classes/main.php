<?php
session_start();
require_once "ConferenceRequest.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request = new ConferenseRequest($_POST);
    $errors = $request->validate();
    if (empty($errors)) {
        $request->save();
        $_SESSION["success_message"] = "Заявка успешно принята!";
    } else {
        $_SESSION["errors"] = $errors;
        $_SESSION["old_data"] = $_POST;
    }
    header("Location: test.php");
    exit();
}
$errors = $_SESSION["errors"] ?? [];
$success_message = $_SESSION["success_message"] ?? "";
$old_data = $_SESSION["old_data"] ?? [];

unset($_SESSION["errors"], $_SESSION["success_message"], $_SESSION["old_data"]);
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
                   value="<?= isset($old_data["first_name"])
                       ? htmlspecialchars($old_data["first_name"])
                       : "" ?>">
        </div>

        <!-- Фамилия --!>
        <div>
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name" class="form_fields"
                   value="<?= isset($old_data["last_name"])
                       ? htmlspecialchars($old_data["last_name"])
                       : "" ?>">
        </div>

        <!-- Почта --!>
        <div>
            <label for="email">email:</label>
            <input type="email" id="email" name="email" class="form_fields" required
                   value="<?= isset($old_data["email"])
                       ? htmlspecialchars($old_data["email"])
                       : "" ?>">
        </div>

        <!-- Телефон --!>
        <div>
            <label for="phone_number">Номер телефона:</label>
            <input type="tel" id="phone_number" name="phone_number" class="form_fields" placeholder="+7 (999) 999-99-99"
                   pattern="^(\+7|8|7)?[\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$"
                   title="Введите номер в формате +7 (999) 999-99-99"
                   required
                   value="<?= isset($old_data["phone_number"])
                       ? htmlspecialchars($old_data["phone_number"])
                       : "" ?>">
        </div>

        <!-- Тематика --!>
        <div class="conference_topics">
            <div>
                <input type="radio" id="topic_business" name="topic" value="buisness"
                    <?= isset($old_data["topic"]) &&
                    $old_data["topic"] === "buisness"
                        ? "checked"
                        : "" ?>>
                <label for="topic_business">Бизнес</label>
            </div>
            <div>
                <input type="radio" id="topic_techonologies" name="topic" value="technologies"
                    <?= isset($old_data["topic"]) &&
                    $old_data["topic"] === "technologies"
                        ? "checked"
                        : "" ?>>
                <label for="topic_technologies">Технологии</label>
            </div>
            <div>
                <input type="radio" id="topic_ad_and_marketing" name="topic" value="ad_and_marketing"
                    <?= isset($old_data["topic"]) &&
                    $old_data["topic"] === "ad_and_marketing"
                        ? "checked"
                        : "" ?>>
                <label for="topic_ad_and_marketing">Реклама и маркетинг</label>
            </div>
        </div>

        <!-- Способ оплаты --!>
        <div class="payment_method">
            <div>
                <input type="radio" id="WebMoney" name="payment" value="WebMoney"
                    <?= isset($old_data["payment"]) &&
                    $old_data["payment"] === "WebMoney"
                        ? "checked"
                        : "" ?>>
                <label for="WebMoney">WebMoney</label>
            </div>
            <div>
                <input type="radio" id="Yandex.Money" name="payment" value="Yandex.Money"
                    <?= isset($old_data["payment"]) &&
                    $old_data["payment"] === "Яндекс.Деньги"
                        ? "checked"
                        : "" ?>>
                <label for="Yandex.Money">Яндекс.Деньги</label>
            </div>
            <div>
                <input type="radio" id="PayPal" name="payment" value="PayPal"
                    <?= isset($old_data["payment"]) &&
                    $old_data["payment"] === "PayPal"
                        ? "checked"
                        : "" ?>>
                <label for="PayPal">PayPal</label>
            </div>
            <div>
                <input type="radio" id="credit_card" name="payment" value="credit_card"
                    <?= isset($old_data["payment"]) &&
                    $old_data["payment"] === "credit_card"
                        ? "checked"
                        : "" ?>>
                <label for="credit_card">Кредитная карта</label>
            </div>
        </div>
        <div>
            <input type="checkbox" id="subscribe" name="subscribe" value="yes"
                <?= isset($old_data["subscribe"]) ? "checked" : "" ?>>
            <label for="subscribe">Получать рассылку о конференции</label>
        </div>

        <br>
        <div>
            <button type="submit">Отправить</button>
        </div>
    </form>

</body>
</html>
