<?php
require_once '../config.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $image = 'placeholder.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
    }

    if (empty($name) || $price <= 0) {
        $error = 'Заполните все поля корректно';
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $image, $category_id]);
        $success = 'Товар добавлен';
    }
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

<h2>Добавить товар</h2>
<?php if ($error): ?><div class="alert"><?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div>
        <label>Название:</label>
        <input type="text" name="name" required>
    </div>
    <div>
        <label>Категория:</label>
        <select name="category_id">
            <option value="">— Без категории —</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Описание:</label>
        <textarea name="description" rows="4"></textarea>
    </div>
    <div>
        <label>Цена:</label>
        <input type="number" step="0.01" name="price" required>
    </div>
    <div>
        <label>Изображение:</label>
        <input type="file" name="image" accept="image/*">
    </div>
    <button type="submit" class="btn">Добавить</button>
    <a href="index.php" class="btn">Назад</a>
</form>

<?php include '../footer.php'; ?>