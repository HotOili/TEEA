<?php
include 'header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверное имя пользователя или пароль';
    }
}
?>

<h2>Вход</h2>
<?php if ($error): ?>
    <div class="alert"><?= $error ?></div>
<?php endif; ?>
<form method="post">
    <div>
        <label>Логин:</label>
        <input type="text" name="username" required>
    </div>
    <div>
        <label>Пароль:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn">Войти</button>
</form>
<p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>

<?php include 'footer.php'; ?>