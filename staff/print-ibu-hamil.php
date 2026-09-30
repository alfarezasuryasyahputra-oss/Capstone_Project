<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
$rows = $pdo->query('SELECT * FROM ibu_hamil ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Data Ibu Hamil</title>
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body class="print-page">
    <div class="print-head">
        <div>
            <h1>Posyandu Bina Warga</h1>
            <h2>Data Ibu Hamil</h2>
        </div><button onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    </div>
    <p>Dicetak: <?= date('d-m-Y H:i') ?></p>
    <table class="data-table">
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Telepon</th>
            <th>Tgl Lahir</th>
            <th>HPHT</th>
            <th>HPL</th>
            <th>Kehamilan Ke-</th>
            <th>Alamat</th>
        </tr><?php foreach ($rows as $i => $r): ?><tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($r['nik'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['phone'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['birth_date'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['hpht'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['edd'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['pregnancy_number'] ?: '-') ?></td>
                <td><?= htmlspecialchars($r['address'] ?: '-') ?></td>
            </tr><?php endforeach; ?>
    </table>
    <script>
        /* pilih Print -> Save as PDF */
    </script>
</body>

</html>