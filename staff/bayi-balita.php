<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = $_POST['action'] ?? '';
    if ($a === 'delete') {
        $s = $pdo->prepare('DELETE FROM bayi_balita WHERE id=?');
        $s->execute([(int)$_POST['id']]);
        header('Location: bayi-balita.php');
        exit;
    }
    if ($a === 'save') {
        $d = [trim($_POST['name'] ?? ''), trim($_POST['mother_name'] ?? ''), !empty($_POST['mother_id']) ? (int)$_POST['mother_id'] : null, $_POST['birth_date'] ?: null, $_POST['gender'] ?? '', $_POST['weight'] !== '' ? $_POST['weight'] : null, $_POST['height'] !== '' ? $_POST['height'] : null, $_POST['head_circumference'] !== '' ? $_POST['head_circumference'] : null, trim($_POST['notes'] ?? '')];
        if (!empty($_POST['id'])) {
            $s = $pdo->prepare('UPDATE bayi_balita SET name=?,mother_name=?,mother_id=?,birth_date=?,gender=?,weight=?,height=?,head_circumference=?,notes=? WHERE id=?');
            $s->execute([...$d, (int)$_POST['id']]);
            $childId=(int)$_POST['id'];
        } else {
            $s = $pdo->prepare('INSERT INTO bayi_balita(name,mother_name,mother_id,birth_date,gender,weight,height,head_circumference,notes) VALUES(?,?,?,?,?,?,?,?,?)');
            $s->execute($d);
            $childId=(int)$pdo->lastInsertId();
        }

        // Jika anak memiliki Ibu yang sudah terhubung ke akun Orang Tua,
        // otomatis tambahkan anak ini ke akun tersebut. Tidak perlu membuat
        // akun baru atau memilih anak dengan Ctrl + Click.
        if (!empty($d[2]) && $childId) {
            $sync = $pdo->prepare('INSERT IGNORE INTO parent_children(parent_id,child_id) SELECT pm.parent_id, ? FROM parent_mothers pm WHERE pm.mother_id=?');
            $sync->execute([$childId,(int)$d[2]]);
        }
        header('Location: bayi-balita.php');
        exit;
    }
}
$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM bayi_balita WHERE id=?');
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$q = trim($_GET['q'] ?? '');
$s = $q !== '' ? $pdo->prepare('SELECT * FROM bayi_balita WHERE name LIKE ? OR mother_name LIKE ? ORDER BY id DESC') : $pdo->query('SELECT * FROM bayi_balita ORDER BY id DESC');
if ($q !== '') {
    $like = "%$q%";
    $s->execute([$like, $like]);
}
$rows = $s->fetchAll();
$mothers = $pdo->query('SELECT id,name FROM ibu_hamil ORDER BY name')->fetchAll();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Data Bayi/Anak</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body>
    <div class="dashboard">
        <?php require __DIR__ . '/sidebar.php'; ?>
        <main class="main-area">
            <div class="topbar">
                <div>
                    <h1>Data Bayi/Anak</h1>
                    <p>Catat data dasar dan hasil pengukuran.</p>
                </div>
            </div>
            <div class="panel-box">
                <h2><?= $edit ? 'Edit Data' : 'Tambah Data' ?></h2>
                <form method="post" class="data-form"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= htmlspecialchars($edit['id'] ?? '') ?>">
                    <label>Nama Anak<input name="name" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>"></label><label>Nama Ibu<input name="mother_name" value="<?= htmlspecialchars($edit['mother_name'] ?? '') ?>"></label><label>Hubungkan ke Data Ibu<select name="mother_id"><option value="">Tidak dipilih</option><?php foreach($mothers as $m): ?><option value="<?= $m['id'] ?>" <?= ((int)($edit['mother_id'] ?? 0)===(int)$m['id'])?'selected':'' ?>><?= htmlspecialchars($m['name']) ?></option><?php endforeach; ?></select></label>
                    <label>Tanggal Lahir<input type="date" name="birth_date" value="<?= htmlspecialchars($edit['birth_date'] ?? '') ?>"></label><label>Jenis Kelamin<select name="gender">
                            <option value="">Pilih</option>
                            <option <?= ($edit['gender'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option <?= ($edit['gender'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select></label>
                    <label>Berat Badan (kg)<input type="number" step="0.01" name="weight" value="<?= htmlspecialchars($edit['weight'] ?? '') ?>"></label><label>Tinggi/Panjang (cm)<input type="number" step="0.1" name="height" value="<?= htmlspecialchars($edit['height'] ?? '') ?>"></label>
                    <label>Lingkar Kepala (cm)<input type="number" step="0.1" name="head_circumference" value="<?= htmlspecialchars($edit['head_circumference'] ?? '') ?>"></label><label class="full">Catatan<textarea name="notes"><?= htmlspecialchars($edit['notes'] ?? '') ?></textarea></label>
                    <div class="full"><button class="btn btn-primary">Simpan</button> <?php if ($edit): ?><a class="btn btn-light" href="bayi-balita.php">Batal</a><?php endif; ?></div>
                </form>
            </div>
            <div class="panel-box">
                <div class="panel-title-row"><h2>Daftar Bayi/Anak</h2><a class="small-btn" href="print-bayi-balita.php" target="_blank">🖨 Cetak / PDF</a></div>
                <form method="get" class="action-row"><input name="q" placeholder="Cari nama anak / ibu" value="<?= htmlspecialchars($q) ?>"><button class="small-btn">Cari</button></form>
                <div class="table-wrap">
                    <table class="data-table">
                        <tr>
                            <th>Anak</th>
                            <th>Ibu</th>
                            <th>Tgl Lahir</th>
                            <th>JK</th>
                            <th>BB/TB</th>
                            <th>Aksi</th>
                        </tr>
                        <?php foreach ($rows as $r): ?><tr>
                                <td><?= htmlspecialchars($r['name']) ?></td>
                                <td><?= htmlspecialchars($r['mother_name']) ?></td>
                                <td><?= htmlspecialchars($r['birth_date'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($r['gender'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($r['weight'] ?: '-') ?> / <?= htmlspecialchars($r['height'] ?: '-') ?></td>
                                <td><a class="small-btn" href="?edit=<?= $r['id'] ?>">Edit</a>
                                    <form style="display:inline" method="post" onsubmit="return confirm('Hapus data ini?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="small-btn danger">Hapus</button></form>
                                </td>
                            </tr><?php endforeach; ?>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>