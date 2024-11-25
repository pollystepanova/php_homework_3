<?php
$errors = [];
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $data['fio'] = $_POST['fio'] ?? '';
    $data['email'] = $_POST['email'] ?? '';
    $data['phone'] = $_POST['phone'] ?? '';
    $data['status'] = $_POST['status'] ?? '';
    $data['gender'] = $_POST['gender'] ?? '';
    $data['agree'] = isset($_POST['agree']);
    $data['password'] = $_POST['password'] ?? '';

    // Валидация данных
    if (empty($data['fio'])) {
        $errors[] = "Введите ФИО.";
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный email.";
    }
    if (empty($data['phone'])) {
        $errors[] = "Введите номер телефона.";
    }
    if (empty($data['status'])) {
        $errors[] = "Выберите статус.";
    }
    if (empty($data['gender'])) {
        $errors[] = "Выберите пол.";
    }
    if (!$data['agree']) {
        $errors[] = "Согласие на обработку данных обязательно.";
    }
    if (empty($data['password'])) {
        $errors[] = "Введите пароль.";
    }

    // Если нет ошибок, логируем данные в массив JSON
    if (empty($errors)) {
        $data['timestamp'] = date('Y-m-d H:i:s');

        // Читаем существующие данные из файла
        $filePath = 'form_log.json';
        $existingData = [];

        if (file_exists($filePath)) {
            $jsonContent = file_get_contents($filePath);
            $existingData = json_decode($jsonContent, true);
            if (!is_array($existingData)) {
                $existingData = []; // Инициализация как пустой массив, если данные некорректны
            }
        }

        // Добавляем новую запись
        $existingData[] = $data;

        // Записываем обновленный массив обратно в файл в формате JSON
        file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT));

        echo "Данные успешно отправлены!";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Форма регистрации</title>
</head>

<body>

    <h1>Регистрационная форма</h1>

    <!-- Вывод ошибок валидации -->
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Форма -->
    <form method="post" action="">
        <label for="fio">ФИО:</label>
        <input type="text" name="fio" id="fio" value="<?php echo htmlspecialchars($data['fio'] ?? ''); ?>"><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>"><br>

        <label for="phone">Телефон:</label>
        <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>"><br>

        <label for="status">Статус:</label>
        <select name="status" id="status">
            <option value="">Выберите статус</option>
            <option value="женат/замужем" <?php echo (isset($data['status']) && $data['status'] == 'женат/замужем') ? 'selected' : ''; ?>>Женат/Замужем</option>
            <option value="не женат/не замужем" <?php echo (isset($data['status']) && $data['status'] == 'не женат/не замужем') ? 'selected' : ''; ?>>Не женат/Не замужем</option>
            <option value="разведен/разведена" <?php echo (isset($data['status']) && $data['status'] == 'разведен/разведена') ? 'selected' : ''; ?>>Разведен/Разведена</option>
        </select><br>

        <label>Пол:</label>
        <input type="radio" name="gender" value="женский" <?php echo (isset($data['gender']) && $data['gender'] == 'женский') ? 'checked' : ''; ?>> Женский
        <input type="radio" name="gender" value="мужской" <?php echo (isset($data['gender']) && $data['gender'] == 'мужской') ? 'checked' : ''; ?>> Мужской<br>

        <label>
            <input type="checkbox" name="agree" <?php echo (isset($data['agree']) && $data['agree']) ? 'checked' : ''; ?>> Согласен на обработку персональных данных
        </label><br>

        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" value="<?php echo htmlspecialchars($data['password'] ?? ''); ?>"><br>

        <input type="submit" value="Отправить">
    </form>

</body>

</html>