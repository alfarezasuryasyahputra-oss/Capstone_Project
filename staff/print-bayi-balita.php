<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
$rows = $pdo->query('SELECT b.*,COALESCE(m.name,b.mother_name) mother_name FROM bayi_balita b LEFT JOIN ibu_hamil m ON m.id=b.mother_id ORDER BY b.id DESC')->fetchAll();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Data Bayi Balita</title>
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body class="print-page">
    <div class="print-head">
        <div>
            <h1>Posyandu Bina Warga</h1>
            <h2>Data Bayi/Balita</h2>
        </div><button onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    </div>
    <p>Dicetak: <?= date('d-m-Y H:i') ?></p>
    <table class="data-table">
        <tr>
            <th>No</th>
            <th>Nama Anak</th>
            <th>Ibu</th>
            <th>Tgl Lahir</th>
            <th>JK</th>
            <th>BB</th>
            <th>TB/PB</th>
            <th>LK</th>
        </tr><?php foreach ($rows as $i => $r): ?><tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['mother_name'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['birth_date'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['gender'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['weight'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['height'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['head_circumference'] ?? '-') ?></td>
            </tr><?php endforeach; ?>
    </table>
</body>

</html>