<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = 'Категория добавлена';
        } catch (PDOException $e) {
            $error = 'Такая категория уже существует';
        }
    } else {
        $error = 'Введите название';
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->execute([$id]);
    $count = $check->fetchColumn();
    if ($count > 0) {
        $error = 'Нельзя удалить категорию, к которой привязаны товары';
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Категория удалена';
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

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

<h2>Управление категориями</h2>

<?php if ($error): ?><div class="alert"><?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>

<form method="post" style="margin-bottom: 20px;">
    <div style="display: flex; gap: 10px; align-items: flex-end;">
        <div style="flex: 1;">
            <label>Название новой категории:</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <button type="submit" name="add" class="btn">Добавить</button>
        </div>
    </div>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($categories as $cat): ?>
    <tr>
        <td><?= $cat['id'] ?></td>
        <td><?= htmlspecialchars($cat['name']) ?></td>
        <td>
            <a href="categories.php?delete=<?= $cat['id'] ?>" class="btn" onclick="return confirm('Удалить категорию? Это возможно только если нет товаров.')">Удалить</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<p><a href="index.php" class="btn">Назад к товарам</a></p>

<?php include '../footer.php'; ?>