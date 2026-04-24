<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1><a href="../index.php">TEA-TIME</a></h1>
        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="menu-btn">☰</label>
        <nav id="main-nav">
            <a href="../glav.php">Главная</a>
            <a href="../index.php">Каталог</a>
            <a href="../onas.php">О нас</a>
            <a href="../cart.php">Корзина</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Привет, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="../admin/index.php">Админ-панель</a>
                <?php endif; ?>
                <a href="../logout.php">Выйти</a>
            <?php else: ?>
                <a href="../login.php">Войти</a>
                <a href="../register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<h2>Управление товарами</h2>
<p><a href="add.php" class="btn">Добавить товар</a></p>

<table>
    <tr>
        <th>ID</th>
        <th>Изображение</th>
        <th>Название</th>
        <th>Категория</th>
        <th>Цена</th>
        <th>Действия</th>
    </tr>
    <?php
    $stmt = $pdo->query("
        SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC
    ");
    while ($product = $stmt->fetch()):
    ?>
    <tr>
        <td><?= $product['id'] ?></td>
        <td><img src="../uploads/<?= htmlspecialchars($product['image']) ?>" width="50"></td>
        <td><?= htmlspecialchars($product['name']) ?></td>
        <td><?= htmlspecialchars($product['category_name'] ?? '—') ?></td>
        <td><?= number_format($product['price'], 2) ?> руб.</td>
        <td>
            <a href="edit.php?id=<?= $product['id'] ?>" class="btn">Изменить</a>
            <a href="delete.php?id=<?= $product['id'] ?>" class="btn" onclick="return confirm('Удалить товар?')">Удалить</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<p>
    <a href="add.php" class="btn">Добавить товар</a>
    <a href="categories.php" class="btn">Управление категориями</a>
</p>

<?php include '../footer.php'; ?>