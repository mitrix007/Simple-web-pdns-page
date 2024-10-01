<?php
include 'config.php';
include 'templates/header.php';

// Получение списка доменов
$ch = curl_init("$apiUrl/zones");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-API-Key: $apiKey"]);

$response = curl_exec($ch);
$zones = json_decode($response, true);
curl_close($ch);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain = $_POST['domain'];
    $recordType = $_POST['record_type'];
    $recordName = $_POST['record_name'];
    $recordContent = $_POST['record_content'];
    $ttl = $_POST['ttl'];

    $data = [
        'name' => $recordName,
        'type' => strtoupper($recordType),
        'content' => $recordContent,
        'ttl' => (int)$ttl
    ];

    $ch = curl_init("$apiUrl/zones/$domain/records");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-API-Key: $apiKey",
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка CURL: ' . curl_error($ch);
    } else {
        echo '<p>Запись добавлена успешно!</p>';
    }

    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить запись</title>
</head>
<body>
    <h2>Добавить новую запись DNS</h2>
    <form method="post" action="add_record.php">
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

        <input type="submit" value="Добавить запись">
    </form>
</body>
</html>

<?php include 'templates/footer.php'; ?>
