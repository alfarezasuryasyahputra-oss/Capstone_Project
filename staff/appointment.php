<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = $pdo->prepare('UPDATE appointments SET status=? WHERE id=?');
    $s->execute([$_POST['status'] ?? 'Menunggu', (int)$_POST['id']]);
    header('Location: appointment.php');
    exit;
}
$rows = $pdo->query('SELECT * FROM appointments ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Appointment</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="brand">♡ Posyandu Staff</div>
            <nav><a href="dashboard.php">Dashboard</a><a href="ibu-hamil.php">Ibu Hamil</a><a href="bayi-balita.php">Bayi/Balita</a><a class="active" href="appointment.php">Appointment</a><a href="../index.php">Website Publik</a><a href="logout.php">Logout</a></nav>
        </aside>
        <main class="main-area">
            <div class="topbar">
                <div>
                    <h1>Appointment</h1>
                    <p>Kelola permintaan kunjungan.</p>
                </div>
            </div>
            <div class="panel-box">
                <div class="table-wrap">
                    <table class="data-table">
                        <tr>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Keperluan</th>
                            <th>Tanggal</th>
                            <th>Catatan</th>
                            <th>Status</th>
                        </tr>
                        <?php foreach ($rows as $r): ?><tr>
                                <td><?= htmlspecialchars($r['name']) ?></td>
                                <td><?= htmlspecialchars($r['phone']) ?></td>
                                <td><?= htmlspecialchars($r['service']) ?></td>
                                <td><?= htmlspecialchars($r['requested_date'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($r['message'] ?: '-') ?></td>
                                <td>
                                    <form method="post"><input type="hidden" name="id" value="<?= $r['id'] ?>"><select name="status" onchange="this.form.submit()"><?php foreach (['Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan'] as $st): ?><option <?= ($r['status'] === $st ? 'selected' : '') ?>><?= $st ?></option><?php endforeach; ?></select></form>
                                </td>
                            </tr><?php endforeach; ?>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>