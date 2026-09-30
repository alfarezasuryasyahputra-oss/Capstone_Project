<?php
require_once __DIR__ . '/config/database.php';
session_start();
$message = '';
$error = '';
$loggedIn = isset($_SESSION['staff_id']);
$count = (int)$pdo->query('SELECT COUNT(*) FROM staff')->fetchColumn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($count > 0 && !$loggedIn) {
        $error = 'Silakan login sebagai staff terlebih dahulu.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($name && $username && strlen($password) >= 8) {
            try {
                $st = $pdo->prepare('INSERT INTO staff(username,password_hash,name) VALUES(?,?,?)');
                $st->execute([$username, password_hash($password, PASSWORD_DEFAULT), $name]);
                $message = 'Akun staff berhasil dibuat.';
                $count++;
            } catch (PDOException $e) {
                $error = 'Username sudah digunakan atau terjadi kesalahan.';
            }
        } else {
            $error = 'Nama dan username wajib diisi. Password minimal 8 karakter.';
        }
    }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Buat Staff</title><link rel="stylesheet" href="css/style.css"><link rel="stylesheet" href="css/staff.css"></head>
<body class="staff-body"><div class="login-card"><h1>Buat Akun Staff</h1>
<p><?= $count === 0 ? 'Buat akun staff pertama.' : 'Halaman ini hanya dapat digunakan oleh staff yang sudah login.' ?></p>
<?php if($message): ?><div class="success-box"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if($count === 0 || $loggedIn): ?>
<form method="post" class="staff-form"><label>Nama<input name="name" required></label><label>Username<input name="username" required></label><label>Password<input type="password" name="password" minlength="8" required></label><button class="btn btn-primary">Buat Akun</button></form>
<?php else: ?><a class="btn btn-primary" href="staff/login.php">Login Staff</a><?php endif; ?>
</div></body></html>