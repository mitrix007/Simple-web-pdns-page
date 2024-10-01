<?php
include 'config.php';
include 'templates/header.php';

// Запрос статуса сервера
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8081/api/v1/servers/localhost");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-API-Key: ' . $apiKey
));

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Ошибка cURL: ' . curl_error($ch);
} else {
    $serverInfo = json_decode($response, true);
    echo "<h2>Статус сервера</h2>";
    echo "<p>Имя сервера: " . htmlspecialchars($serverInfo['id']) . "</p>";
    echo "<p>Версия: " . htmlspecialchars($serverInfo['version']) . "</p>";

    // Проверка на репликацию (master/slave)
    if (isset($serverInfo['replication-status'])) {
        echo "<h3>Статус репликации</h3>";
        echo "<p>Репликация активна: " . htmlspecialchars($serverInfo['replication-status']) . "</p>";
    } else {
        echo "<p>Репликация не активна.</p>";
    }
}
curl_close($ch);

include 'templates/footer.php';
?>
