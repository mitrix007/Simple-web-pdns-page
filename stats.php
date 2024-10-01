<?php
include 'config.php';
include 'templates/header.php';

// Получение статистики с сервера PowerDNS
$ch = curl_init("$apiUrl/statistics");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-API-Key: $apiKey"]);

$response = curl_exec($ch);
$stats = json_decode($response, true);

if (curl_errno($ch)) {
    echo 'Ошибка CURL: ' . curl_error($ch);
} else {
    echo '<h2>Статистика сервера</h2>';
    echo '<table>';
    echo '<tr><th>Параметр</th><th>Значение</th></tr>';
    foreach ($stats as $stat) {
        echo "<tr><td>{$stat['name']}</td><td>{$stat['value']}</td></tr>";
    }
    echo '</table>';
}

curl_close($ch);

include 'templates/footer.php';
?>
