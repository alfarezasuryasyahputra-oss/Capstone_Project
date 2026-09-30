<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Vitamin Balita</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body>
    <div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area"><?php
                                                                                                $children = $pdo->query('SELECT id,name FROM bayi_balita ORDER BY name')->fetchAll();
                                                                                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                                                                    $s = $pdo->prepare('INSERT INTO vitamin(child_id,vitamin_date,vitamin_name,dose,notes) VALUES(?,?,?,?,?)');
                                                                                                    $s->execute([(int)$_POST['child_id'], $_POST['vitamin_date'], trim($_POST['vitamin_name'] ?? ''), trim($_POST['dose'] ?? ''), trim($_POST['notes'] ?? '')]);
                                                                                                    header('Location: vitamin.php');
                                                                                                    exit;
                                                                                                }
                                                                                                $rows = $pdo->query('SELECT v.*,b.name child_name FROM vitamin v JOIN bayi_balita b ON b.id=v.child_id ORDER BY v.vitamin_date DESC,v.id DESC LIMIT 100')->fetchAll();
                                                                                                ?>
            <div class="topbar">
                <div>
                    <h1>Vitamin Balita</h1>
                    <p>Catat pemberian vitamin balita.</p>
                </div>
            </div>
            <div class="panel-box">
                <h2>Tambah Data Vitamin</h2>
                <form method="post" class="data-form">
                    <label>Balita<select name="child_id" required>
                            <option value="">Pilih anak</option><?php foreach ($children as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
                        </select></label>
                    <label>Tanggal<input type="date" name="vitamin_date" required value="<?= date('Y-m-d') ?>"></label>
                    <label>Vitamin<input name="vitamin_name" required></label><label>Dosis<input name="dose"></label>
                    <label class="full">Catatan<textarea name="notes"></textarea></label>
                    <div class="full"><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
            <div class="panel-box">
                <h2>Riwayat Vitamin</h2>
                <div class="table-wrap">
                    <table class="data-table">
                        <tr>
                            <th>Tanggal</th>
                            <th>Balita</th>
                            <th>Vitamin</th>
                            <th>Dosis</th>
                            <th>Catatan</th>
                        </tr><?php foreach ($rows as $r): ?><tr>
                                <td><?= htmlspecialchars($r['vitamin_date']) ?></td>
                                <td><?= htmlspecialchars($r['child_name']) ?></td>
                                <td><?= htmlspecialchars($r['vitamin_name']) ?></td>
                                <td><?= htmlspecialchars($r['dose'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($r['notes'] ?: '-') ?></td>
                            </tr><?php endforeach; ?>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>