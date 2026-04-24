<?php
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<p class="alert">Товар не найден</p>';
    include 'footer.php';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<p class="alert">Товар не найден</p>';
    include 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $quantity = (int)$_POST['quantity'];

    $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check->execute([$user_id, $id]);
    $cartItem = $check->fetch();

    if ($cartItem) {
        $newQty = $cartItem['quantity'] + $quantity;
        $update = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update->execute([$newQty, $cartItem['id']]);
    } else {
        $insert = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->execute([$user_id, $id, $quantity]);
    }
    echo '<p class="success">Товар добавлен в корзину</p>';
}

$catStmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
$catStmt->execute([$product['category_id']]);
$category = $catStmt->fetch();
?>

<h2><?= htmlspecialchars($product['name']) ?></h2>
<div style="display: flex; gap: 30px;">
    <div>
        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 300px;">
    </div>
    <div class="product-info">
        <p class="product-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <p><strong>Цена: <?= number_format($product['price'], 2) ?> руб.</strong></p>
        <?php if ($category): ?>
            <p>Категория: <?= htmlspecialchars($category['name']) ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="post">
                <label>Количество: <input type="number" name="quantity" value="1" min="1" style="width: 80px;"></label>
                <button type="submit" name="add_to_cart" class="btn">В корзину</button>
            </form>
        <?php else: ?>
            <p><a href="login.php" class="btn">Войдите, чтобы добавить в корзину</a></p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>