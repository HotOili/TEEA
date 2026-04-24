<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = (int)$qty;
        if ($qty > 0) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$qty, $cart_id, $user_id]);
        }
    }
    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$remove_id, $user_id]);
    header('Location: cart.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT cart.id AS cart_id, products.*, cart.quantity 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

$total = 0;
?>

<h2>Корзина</h2>

<?php if (empty($items)): ?>
    <p>Ваша корзина пуста.</p>
<?php else: ?>
    <form method="post">
        <table>
            <tr>
                <th>Товар</th>
                <th>Цена</th>
                <th>Кол-во</th>
                <th>Сумма</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($items as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?> руб.</td>
                    <td>
                        <input type="number" name="quantity[<?= $item['cart_id'] ?>]" value="<?= $item['quantity'] ?>" min="1" style="width: 70px;">
                    </td>
                    <td><?= number_format($subtotal, 2) ?> руб.</td>
                    <td>
                        <a href="cart.php?remove=<?= $item['cart_id'] ?>" class="btn" onclick="return confirm('Удалить товар?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" align="right"><strong>Итого:</strong></td>
                <td><strong><?= number_format($total, 2) ?> руб.</strong></td>
                <td></td>
            </tr>
        </table>
        <button type="submit" name="update" class="btn">Обновить корзину</button>
        <a href="#" class="btn">Оформить заказ (заглушка)</a>
    </form>
<?php endif; ?>

<?php include 'footer.php'; ?>