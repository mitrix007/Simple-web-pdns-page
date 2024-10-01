<?php
include 'config.php';
include 'templates/header.php';

if (isset($_POST['test_zone'])) {
    $testZone = $_POST['test_zone'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$apiUrl/" . urlencode($testZone));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-API-Key: ' . $apiKey
    ));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
    } else {
        $zoneInfo = json_decode($response, true);

        if ($zoneInfo) {
            echo "<h2>Результаты тестирования зоны: " . htmlspecialchars($testZone) . "</h2>";
            echo "<p>Зона найдена: " . htmlspecialchars($zoneInfo['name']) . "</p>";

            // Проверяем ключевые записи
            $hasARecord = false;
            $hasNSRecord = false;
            $hasSOARecord = false;

            foreach ($zoneInfo['rrsets'] as $record) {
                if ($record['type'] === 'A') {
                    $hasARecord = true;
                }
                if ($record['type'] === 'NS') {
                    $hasNSRecord = true;
                }
                if ($record['type'] === 'SOA') {
                    $hasSOARecord = true;
                }
            }

            echo "<p>Запись A: " . ($hasARecord ? "найдена" : "не найдена") . "</p>";
            echo "<p>Запись NS: " . ($hasNSRecord ? "найдена" : "не найдена") . "</p>";
            echo "<p>Запись SOA: " . ($hasSOARecord ? "найдена" : "не найдена") . "</p>";
        } else {
            echo "<p>Зона не найдена.</p>";
        }
    }
    curl_close($ch);
}

?>

<h1>Тестирование зоны</h1>
<form action="test_zone.php" method="post">
    <label for="test_zone">Введите зону для тестирования:</label>
    <input type="text" id="test_zone" name="test_zone" required><br>
    <input type="submit" value="Тестировать зону">
</form>

<?php
include 'templates/footer.php';
?>