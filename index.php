<?php
// Подключаем файл конфигурации
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление DNS</title>
</head>
<body>

<h1>Добавить DNS запись</h1>
<form action="index.php" method="post">
    <label for="zone">Зона:</label>
    <input type="text" id="zone" name="zone" required><br>
    
    <label for="name">Имя записи:</label>
    <input type="text" id="name" name="name" required><br>
    
    <label for="type">Тип записи:</label>
    <select id="type" name="type" required>
        <option value="A">A</option>
        <option value="CNAME">CNAME</option>
        <option value="MX">MX</option>
        <option value="TXT">TXT</option>
    </select><br>
    
    <label for="content">Содержимое:</label>
    <input type="text" id="content" name="content" required><br>
    
    <label for="ttl">TTL:</label>
    <input type="number" id="ttl" name="ttl" value="3600" required><br>
    
    <input type="submit" value="Добавить запись">
</form>

<?php
// cURL запрос для получения всех зон
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-API-Key: ' . $apiKey
));

// Получение ответа и обработка
$output = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Ошибка cURL: ' . curl_error($ch);
} else {
    $zones = json_decode($output, true);
    // Отобразим список зон
    echo "<h1>Список зон</h1>";
    echo "<ul>";
    foreach ($zones as $zone) {
        echo "<li>" . $zone['name'] . "</li>";
    }
    echo "</ul>";
}
curl_close($ch);

// Обработка запроса на добавление записи
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $zone = $_POST['zone'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $content = $_POST['content'];
    $ttl = $_POST['ttl'];

    // Создание данных для запроса
    $recordData = array(
        "rrset_name" => $name,
        "rrset_type" => $type,
        "rrset_ttl" => $ttl,
        "rrset_records" => array($content)
    );

    // Подготовка cURL для добавления записи
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8081/api/v1/servers/localhost/zones/" . urlencode($zone));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-API-Key: ' . $apiKey,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([$recordData]));

    // Выполнение запроса
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
    } else {
        echo "<p>Запись добавлена: " . htmlspecialchars($name) . " " . htmlspecialchars($type) . " " . htmlspecialchars($content) . "</p>";
    }
    curl_close($ch);
}
?>

</body>
</html>
