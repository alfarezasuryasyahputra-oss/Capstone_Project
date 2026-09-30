<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan & Rekapitulasi</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body>
    <div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area"><?php
                                                                                                $year = (int)($_GET['year'] ?? date('Y'));
                                                                                                $month = (int)($_GET['month'] ?? date('n'));
                                                                                                $start = sprintf('%04d-%02d-01', $year, $month);
                                                                                                $end = date('Y-m-t', strtotime($start));
                                                                                                function cnt($pdo, $sql, $a)
                                                                                                {
                                                                                                    $s = $pdo->prepare($sql);
                                                                                                    $s->execute($a);
                                                                                                    return (int)$s->fetchColumn();
                                                                                                }
                                                                                                $stats = [
                                                                                                    'Ibu Hamil' => cnt($pdo, 'SELECT COUNT(*) FROM ibu_hamil WHERE DATE(created_at) BETWEEN ? AND ?', [$start, $end]),
                                                                                                    'Balita' => cnt($pdo, 'SELECT COUNT(*) FROM bayi_balita WHERE DATE(created_at) BETWEEN ? AND ?', [$start, $end]),
                                                                                                    'Pelayanan Balita' => cnt($pdo, 'SELECT COUNT(*) FROM pelayanan_balita WHERE service_date BETWEEN ? AND ?', [$start, $end]),
                                                                                                    'Imunisasi' => cnt($pdo, 'SELECT COUNT(*) FROM imunisasi WHERE immunization_date BETWEEN ? AND ?', [$start, $end]),
                                                                                                    'Vitamin Balita' => cnt($pdo, 'SELECT COUNT(*) FROM vitamin WHERE vitamin_date BETWEEN ? AND ?', [$start, $end]),
                                                                                                    'Pemeriksaan ANC' => cnt($pdo, 'SELECT COUNT(*) FROM pemeriksaan_ibu_hamil WHERE examination_date BETWEEN ? AND ?', [$start, $end]),
                                                                                                    'Vitamin Ibu Hamil' => cnt($pdo, 'SELECT COUNT(*) FROM vitamin_ibu_hamil WHERE vitamin_date BETWEEN ? AND ?', [$start, $end]),
                                                                                                    'Janji Temu' => cnt($pdo, 'SELECT COUNT(*) FROM appointments WHERE DATE(created_at) BETWEEN ? AND ?', [$start, $end])
                                                                                                ];
                                                                                                ?>
            <div class="topbar">
                <div>
                    <h1>Laporan & Rekapitulasi</h1>
                    <p>Ringkasan pelayanan untuk periode yang dipilih.</p>
                </div>
                <div class="action-row"><button class="small-btn" onclick="window.print()">🖨 Cetak / PDF</button></div>
            </div>
            <div class="panel-box">
                <form method="get" class="action-row"><label>Bulan<select name="month"><?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>" <?= $month === $i ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option><?php endfor; ?></select></label><label>Tahun<input type="number" name="year" value="<?= $year ?>" min="2020" max="2100"></label><button class="small-btn">Tampilkan</button></form>
            </div>
            <div class="stats"><?php foreach ($stats as $label => $value): ?><div class="stat"><small><?= htmlspecialchars($label) ?></small><strong><?= $value ?></strong></div><?php endforeach; ?></div>
            <div class="panel-box">
                <h2>Periode <?= htmlspecialchars($start) ?> s/d <?= htmlspecialchars($end) ?></h2>
                <p>Gunakan tombol Cetak / PDF untuk mencetak laporan atau memilih opsi <strong>Save as PDF</strong> pada dialog cetak browser.</p>
            </div>
        </main>
    </div>
</body>

</html>