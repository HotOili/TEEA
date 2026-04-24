<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1><a href="index.php">TEA-TIME</a></h1>
        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="menu-btn">☰</label>
        <nav id="main-nav">
            <a href="glav.php">Главная</a>
            <a href="index.php">Каталог</a>
            <a href="onas.php">О нас</a>
            <a href="cart.php">Корзина</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Привет, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin/index.php">Админ-панель</a>
                <?php endif; ?>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Войти</a>
                <a href="register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>