<?php
include 'header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = 'Пользователь с таким логином уже существует';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $success = 'Регистрация успешна. Теперь вы можете войти.';
        }
    }
}
?>

<h2>Регистрация</h2>
<?php if ($error): ?>
    <div class="alert"><?= $error ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success"><?= $success ?></div>
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
    <div>
        <label>Подтвердите пароль:</label>
        <input type="password" name="confirm" required>
    </div>
    <button type="submit" class="btn">Зарегистрироваться</button>
</form>
<p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>

<?php include 'footer.php'; ?>