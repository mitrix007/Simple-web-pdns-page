<?php
// Подключаем конфигурационный файл и заголовок шаблона
include 'config.php';
include 'templates/header.php';

// Получение списка зон через API PowerDNS
$ch = curl_init("$apiUrl/zones");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-API-Key: $apiKey"]);

$response = curl_exec($ch);
$zones = json_decode($response, true);
curl_close($ch);

// Обработка формы создания новой зоны
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_zone'])) {
    // Получаем данные из формы
    $domain = $_POST['domain'];
    $email = $_POST['email'];
    $default_ttl = $_POST['default_ttl'];
    $soa_ttl = $_POST['soa_ttl'];

    // Данные для создания зоны
    $zoneData = [
        'name' => $domain,
        'kind' => 'native',    // Тип зоны (например, 'native')
        'masters' => [],
        'nameservers' => ['ns1.example.com', 'ns2.example.com'],
        'soa_edit_api' => 'INCEPTION-INCREMENT', // Правила SOA
        'soa_ttl' => (int)$soa_ttl,
        'default_ttl' => (int)$default_ttl,
        'admin_email' => $email
    ];

    // Отправляем запрос на создание новой зоны
    $ch = curl_init("$apiUrl/zones");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($zoneData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-API-Key: $apiKey",
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка CURL: ' . curl_error($ch);
    } else {
        echo '<p>Зона добавлена успешно!</p>';
    }

    curl_close($ch);
}

// Обработка формы для добавления записи в зону
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record'])) {
    // Получаем данные из формы
    $domain = $_POST['domain'];
    $recordType = $_POST['record_type'];
    $recordName = $_POST['record_name'];
    $recordContent = $_POST['record_content'];
    $ttl = $_POST['ttl'];

    // Формируем массив данных для отправки
    $data = [
        'name' => $recordName,                // Имя записи
        'type' => strtoupper($recordType),    // Тип записи (A, CNAME и т.д.)
        'content' => $recordContent,          // Содержимое записи (IP, домен и т.д.)
        'ttl' => (int)$ttl                    // Время жизни записи
    ];

    // Инициализация cURL для отправки данных в PowerDNS API
    $ch = curl_init("$apiUrl/zones/$domain/records");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-API-Key: $apiKey",                // Используем API-ключ для авторизации
        'Content-Type: application/json'     // Указываем, что данные отправляются в формате JSON
    ]);

    // Выполнение запроса и обработка возможных ошибок
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка CURL: ' . curl_error($ch);
    } else {
        echo '<p>Запись добавлена успешно!</p>';  // Успешное добавление записи
    }

    curl_close($ch);  // Закрываем cURL-соединение
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление DNS зонами</title>
</head>
<body>

    <h2>Создать новую зону</h2>
    <!-- Форма для создания новой зоны DNS -->
    <form method="post" action="">
        <label for="domain">Домен:</label>
        <input type="text" id="domain" name="domain" required><br>

        <label for="email">Email администратора:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="default_ttl">Default TTL:</label>
        <input type="number" id="default_ttl" name="default_ttl" value="3600" required><br>

        <label for="soa_ttl">SOA TTL:</label>
        <input type="number" id="soa_ttl" name="soa_ttl" value="3600" required><br>

        <input type="submit" name="create_zone" value="Создать зону">
    </form>

    <h2>Добавить новую запись DNS</h2>
    <!-- Форма для добавления новой записи в DNS -->
    <form method="post" action="">
        <label for="domain">Домен:</label>
        <select id="domain" name="domain" required>
            <?php foreach ($zones as $zone): ?>
                <option value="<?= htmlspecialchars($zone['name']) ?>"><?= htmlspecialchars($zone['name']) ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="record_type">Тип записи:</label>
        <select id="record_type" name="record_type">
            <option value="A">A</option>
            <option value="CNAME">CNAME</option>
            <option value="MX">MX</option>
            <option value="TXT">TXT</option>
            <!-- Добавьте другие типы записей при необходимости -->
        </select><br>

        <label for="record_name">Имя записи:</label>
        <input type="text" id="record_name" name="record_name" required><br>

        <label for="record_content">Содержимое записи:</label>
        <input type="text" id="record_content" name="record_content" required><br>

        <label for="ttl">TTL:</label>
        <input type="number" id="ttl" name="ttl" value="3600" required><br>

        <input type="submit" name="add_record" value="Добавить запись">
    </form>

</body>
</html>

<?php include 'templates/footer.php'; ?>
