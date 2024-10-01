<?php
include 'config.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $adminUser && $password === $adminPassword) {
        // Успешная авторизация
        $_SESSION['loggedin'] = true;
        header("Location: index.php");
        exit();
    } else {
        $error = "Неверный логин или пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Вход в систему</h1>
    <form method="post" action="login.php">
        <label for="username">Логин:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Войти">
    </form>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
</body>
</html>
