<?php include 'header.php'; ?>

<div style="display: flex; gap: 20px;">
    <aside style="width: 200px;">
        <h3>Категории</h3>
        <ul style="list-style: none; padding: 0;">
            <li><a href="index.php">Все товары</a></li>
            <?php
            $catStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
            while ($cat = $catStmt->fetch()):
            ?>
                <li><a href="index.php?category=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
            <?php endwhile; ?>
        </ul>
    </aside>

    <div style="flex: 1;">
        <h2>
            <?php
            $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
            $categoryName = 'Все товары';
            if ($categoryId) {
                $catInfo = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                $catInfo->execute([$categoryId]);
                $cat = $catInfo->fetch();
                if ($cat) {
                    $categoryName = 'Категория: ' . htmlspecialchars($cat['name']);
                }
            }
            echo $categoryName;
            ?>
        </h2>

        <div class="product-list">
            <?php
            $sql = "SELECT * FROM products";
            $params = [];
            if ($categoryId) {
                $sql .= " WHERE category_id = ?";
                $params[] = $categoryId;
            }
            $sql .= " ORDER BY id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            while ($product = $stmt->fetch()):
            ?>
                <div class="product-card">
                    <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <strong><?= number_format($product['price'], 2) ?> руб.</strong>
                    <br><br>
                    <a href="product.php?id=<?= $product['id'] ?>" class="btn">Подробнее</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>