<?php
require_once __DIR__ . '/config/database.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name && $username && strlen($password) >= 8) {
        try {
            $s = $pdo->prepare('INSERT INTO staff(username,password_hash,name) VALUES(?,?,?)');
            $s->execute([$username, password_hash($password, PASSWORD_DEFAULT), $name]);
            $message = 'Akun berhasil dibuat. Hapus setup_admin.php setelah selesai.';
        } catch (PDOException $e) {
            $message = 'Username sudah digunakan atau terjadi kesalahan.';
        }
    } else {
        $message = 'Nama dan username wajib diisi. Password minimal 8 karakter.';
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Buat Staff</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/staff.css">
</head>

<body class="staff-body">
    <div class="login-card">
        <h1>Buat Akun Staff</h1>
        <p>Gunakan sekali setelah database diimport.</p><?php if ($message): ?><div class="error-box"><?= htmlspecialchars($message) ?></div><?php endif; ?><form method="post" class="staff-form"><label>Nama<input name="name" required></label><label>Username<input name="username" required></label><label>Password<input type="password" name="password" minlength="8" required></label><button class="btn btn-primary">Buat Akun</button></form>
    </div>
</body>

</html>