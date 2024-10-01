<?php
include 'config.php';
include 'templates/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zoneName = $_POST['zone_name'];
    $zoneType = $_POST['zone_type'];

    $data = [
        'name' => $zoneName,
        'kind' => strtoupper($zoneType),
        'masters' => [],
        'nameservers' => []
    ];

    $ch = curl_init("$apiUrl/zones");
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
        echo '<p>Зона добавлена успешно!</p>';
    }

    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить зону</title>
</head>
<body>
    <h2>Добавить новую зону</h2>
    <form method="post" action="add_zone.php">
        <label for="zone_name">Имя зоны:</label>
        <input type="text" id="zone_name" name="zone_name" required><br>
        
        <label for="zone_type">Тип зоны:</label>
        <select id="zone_type" name="zone_type">
            <option value="master">Мастер</option>
            <option value="slave">Слейв</option>
        </select><br>

        <input type="submit" value="Добавить зону">
    </form>
</body>
</html>

<?php include 'templates/footer.php'; ?>
