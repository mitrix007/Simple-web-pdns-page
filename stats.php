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
 echo '<table>';
    echo '<tr><th>Параметр</th><th>Значение</th><th>Комментарий</th></tr>';
    foreach ($stats as $stat) {
        $comment = ''; // По умолчанию комментарий пуст
        switch ($stat['name']) {
            case 'zones':
                $comment = 'Количество зон, управляемых сервером.';
                break;
            case 'records':
                $comment = 'Общее количество DNS-записей.';
                break;
            case 'queries':
                $comment = 'Количество запросов, обработанных сервером.';
                break;
            // Добавьте другие параметры и комментарии при необходимости
        }
        echo "<tr><td>{$stat['name']}</td><td>{$stat['value']}</td><td>$comment</td></tr>";
    }
    echo '</table>';
}
curl_close($ch);

include 'templates/footer.php';
?>