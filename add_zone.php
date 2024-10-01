<?php
include 'config.php';
include 'templates/header.php';

// Проверяем, была ли отправлена форма
if (isset($_POST['new_zone'])) {
    $newZone = trim($_POST['new_zone']); // Удаляем пробелы по краям
    $zoneType = $_POST['zone_type'];
    $zoneMaster = isset($_POST['zone_master']) ? trim($_POST['zone_master']) : '';

    // Подготовка данных зоны
    $zoneData = array(
        "name" => $newZone,
        "kind" => $zoneType,
        "masters" => $zoneType === "slave" ? array($zoneMaster) : array(),
        "nameservers" => array() // Добавьте необходимые nameservers, если нужно
    );

    // Инициализация cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-API-Key: ' . $apiKey,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($zoneData));

    // Выполнение запроса и обработка ответа
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode === 201) {
            echo "<p>Зона создана: " . htmlspecialchars($newZone) . "</p>";
        } else {
            echo "<p>Ошибка при создании зоны: " . htmlspecialchars($response) . "</p>";
        }
    }
    curl_close($ch);
}
?>

<h1>Создание новой зоны</h1>
<form action="add_zone.php" method="post">
    <label for="new_zone">Домен (зона):</label>
    <input type="text" id="new_zone" name="new_zone" required><br>
    
    <label for="zone_type">Тип зоны:</label>
    <select id="zone_type" name="zone_type" required>
        <option value="master">Master</option>
        <option value="slave">Slave</option>
    </select><br>

    <label for="zone_master">Мастер сервер (для slave):</label>
    <input type="text" id="zone_master" name="zone_master" placeholder="Введите IP или FQDN"><br>

    <input type="submit" value="Создать зону">
</form>

<?php
include 'templates/footer.php';
?>
