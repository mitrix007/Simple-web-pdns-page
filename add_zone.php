<?php
// Подключаем конфигурацию и заголовок
include 'config.php';
include 'templates/header.php';

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
        'nameservers' => ['ns1.example.com', 'ns2.example.com'], // Пример nameservers
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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать зону</title>
</head>
<body>
    <h2>Создать новую зону</h2>
    <!-- Форма для создания новой зоны DNS -->
    <form method="post" action="add_zone.php">
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
</body>
</html>

<?php include 'templates/footer.php'; ?>
