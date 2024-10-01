<?php
include 'config.php';
include 'templates/header.php';

// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain = $_POST['domain'];
    $recordType = $_POST['record_type'];
    $recordName = $_POST['record_name'];
    $recordContent = $_POST['record_content'];
    $ttl = $_POST['ttl'];

    $data = [
        'rrsets' => [
            [
                'name' => $recordName,
                'type' => strtoupper($recordType),
                'ttl' => (int)$ttl,
                'changetype' => 'REPLACE',
                'records' => [
                    [
                        'content' => $recordContent,
                        'disabled' => false
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init("$apiUrl/zones/$domain");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-API-Key: $apiKey",
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка CURL: ' . curl_error($ch);
    } else {
        if (strpos($response, 'error') !== false) {
            echo '<p>Ошибка при добавлении записи: ' . htmlspecialchars($response) . '</p>';
        } else {
            echo '<p>Запись добавлена успешно!</p>';
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
