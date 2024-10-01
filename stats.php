<?php
include 'config.php';
include 'templates/header.php';

// Запрос статистики сервера
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8081/api/v1/servers/localhost/statistics");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-API-Key: ' . $apiKey
));

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Ошибка cURL: ' . curl_error($ch);
} else {
    $stats = json_decode($response, true);
    echo "<h2>Статистика сервера</h2>";

    foreach ($stats as $stat) {
        echo "<p>" . htmlspecialchars($stat['name']) . ": " . htmlspecialchars($stat['value']) . "</p>";
    }
}
curl_close($ch);

include 'templates/footer.php';
?>