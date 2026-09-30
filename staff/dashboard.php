<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
$ih = (int)$pdo->query('SELECT COUNT(*) FROM ibu_hamil')->fetchColumn();
$bb = (int)$pdo->query('SELECT COUNT(*) FROM bayi_balita')->fetchColumn();
$ap = (int)$pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
$pending = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Menunggu'")->fetchColumn();
$recent = $pdo->query('SELECT name,service,requested_date,status FROM appointments ORDER BY id DESC LIMIT 8')->fetchAll();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Staff</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="brand">♡ Posyandu Staff</div>
            <nav><a class="active" href="dashboard.php">Dashboard</a><a href="ibu-hamil.php">Ibu Hamil</a><a href="bayi-balita.php">Bayi/Balita</a><a href="appointment.php">Appointment</a><a href="../index.php">Website Publik</a><a href="logout.php">Logout</a></nav>
        </aside>
        <main class="main-area">
            <div class="topbar">
                <div>
                    <h1>Dashboard</h1>
                    <p>Selamat datang, <?= htmlspecialchars($_SESSION['staff_name']) ?>.</p>
                </div>
            </div>
            <div class="stats">
                <div class="stat"><small>Ibu Hamil</small><strong><?= $ih ?></strong></div>
                <div class="stat"><small>Bayi/Anak</small><strong><?= $bb ?></strong></div>
                <div class="stat"><small>Appointment</small><strong><?= $ap ?></strong></div>
                <div class="stat"><small>Menunggu</small><strong><?= $pending ?></strong></div>
            </div>
            <div class="panel-box">
                <h2>Appointment Terbaru</h2>
                <div class="table-wrap">
                    <table class="data-table">
                        <tr>
                            <th>Nama</th>
                            <th>Keperluan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr><?php foreach ($recent as $r): ?><tr>
                                <td><?= htmlspecialchars($r['name']) ?></td>
                                <td><?= htmlspecialchars($r['service']) ?></td>
                                <td><?= htmlspecialchars($r['requested_date'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($r['status']) ?></td>
                            </tr><?php endforeach; ?><?php if (!$recent): ?><tr>
                                <td colspan="4">Belum ada appointment.</td>
                            </tr><?php endif; ?>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>