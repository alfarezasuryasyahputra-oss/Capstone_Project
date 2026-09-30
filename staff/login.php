<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (isset($_SESSION['staff_id'])) {
  header('Location: dashboard.php');
  exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';
  $s = $pdo->prepare('SELECT id,username,password_hash,name FROM staff WHERE username=? LIMIT 1');
  $s->execute([$u]);
  $staff = $s->fetch();
  if ($staff && password_verify($p, $staff['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['staff_id'] = $staff['id'];
    $_SESSION['staff_name'] = $staff['name'];
    header('Location: dashboard.php');
    exit;
  }
  $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login Staff</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/staff.css">
</head>

<body class="staff-body">
  <div class="login-card">
    <h1>Login Staff</h1>
    <p>Kelola data Posyandu.</p><?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" class="staff-form"><label>Username<input name="username" required></label><label>Password<input type="password" name="password" required></label><button class="btn btn-primary">Masuk</button></form><a class="back-link" href="../index.php">← Kembali</a>
  </div>
</body>

</html>