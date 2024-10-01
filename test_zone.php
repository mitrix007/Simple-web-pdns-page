<?php
include 'config.php';
include 'templates/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zoneName = $_POST['zone_name'];

    // Вызов команды pdnsutil для проверки зоны
    $output = shell_exec("pdnsutil check-zone $zoneName 2>&1");

    echo "<pre>$output</pre>";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тестирование зоны</title>
</head>
<body>
    <h2>Тестировать зону</h2>
    <form method="post" action="test_zone.php">
        <label for="zone_name">Имя зоны:</label>
        <input type="text" id="zone_name" name="zone_name" required><br>
        <input type="submit" value="Тестировать">
    </form>
</body>
</html>

<?php include 'templates/footer.php'; ?>
