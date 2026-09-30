<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>KMS Digital</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>

<body>
    <div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area"><?php
                                                                                                $children = $pdo->query('SELECT id,name,birth_date,gender FROM bayi_balita ORDER BY name')->fetchAll();
                                                                                                $childId = (int)($_GET['child_id'] ?? 0);
                                                                                                $child = null;
                                                                                                $points = [];
                                                                                                if ($childId) {
                                                                                                    $s = $pdo->prepare('SELECT * FROM bayi_balita WHERE id=?');
                                                                                                    $s->execute([$childId]);
                                                                                                    $child = $s->fetch();
                                                                                                    $s = $pdo->prepare('SELECT service_date,weight,height,head_circumference FROM pelayanan_balita WHERE child_id=? ORDER BY service_date ASC');
                                                                                                    $s->execute([$childId]);
                                                                                                    $points = $s->fetchAll();
                                                                                                }
                                                                                                ?>
            <div class="topbar">
                <div>
                    <h1>KMS Digital</h1>
                    <p>Riwayat pertumbuhan berdasarkan data pengukuran yang dicatat kader.</p>
                </div>
            </div>
            <div class="panel-box">
                <form method="get" class="action-row"><select name="child_id" required>
                        <option value="">Pilih balita</option><?php foreach ($children as $c): ?><option value="<?= $c['id'] ?>" <?= $childId === $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
                    </select><button class="small-btn">Tampilkan</button></form>
            </div>
            <?php if ($child): ?>
                <div class="panel-box">
                    <div class="panel-title-row">
                        <h2><?= htmlspecialchars($child['name']) ?></h2><a class="small-btn" href="print-kms.php?child_id=<?= $childId ?>" target="_blank">🖨 Cetak KMS</a>
                    </div>
                    <p class="muted">Tanggal lahir: <?= htmlspecialchars($child['birth_date'] ?: '-') ?> · Jenis kelamin: <?= htmlspecialchars($child['gender'] ?: '-') ?></p>
                    <?php if (count($points) > 0):
                        $maxW = max(15.0, max(array_map(fn($x) => (float)($x['weight'] ?? 0), $points)) + 2);
                        $minW = max(0.0, min(array_map(fn($x) => (float)($x['weight'] ?? 0), $points)) - 2);
                        $range = max(1, $maxW - $minW);
                        $w = 760;
                        $h = 330;
                        $padL = 55;
                        $padR = 25;
                        $padT = 25;
                        $padB = 55;
                        $innerW = $w - $padL - $padR;
                        $innerH = $h - $padT - $padB;
                        $coords = [];
                        foreach ($points as $i => $pt) {
                            $x = $padL + ($i / max(1, count($points) - 1)) * $innerW;
                            $y = $padT + (1 - (((float)$pt['weight'] - $minW) / $range)) * $innerH;
                            $coords[] = [$x, $y];
                        }
                    ?>
                        <div class="chart-wrap"><svg viewBox="0 0 <?= $w ?> <?= $h ?>" class="growth-chart" role="img" aria-label="Grafik berat badan">
                                <line x1="<?= $padL ?>" y1="<?= $padT ?>" x2="<?= $padL ?>" y2="<?= $h - $padB ?>" stroke="currentColor" opacity=".25" />
                                <line x1="<?= $padL ?>" y1="<?= $h - $padB ?>" x2="<?= $w - $padR ?>" y2="<?= $h - $padB ?>" stroke="currentColor" opacity=".25" />
                                <?php for ($i = 0; $i <= 4; $i++): $val = $minW + $range * $i / 4;
                                    $y = $padT + (1 - $i / 4) * $innerH; ?>
                                    <line x1="<?= $padL ?>" y1="<?= $y ?>" x2="<?= $w - $padR ?>" y2="<?= $y ?>" stroke="currentColor" opacity=".08" /><text x="8" y="<?= $y + 5 ?>" font-size="12"><?= number_format($val, 1) ?> kg</text><?php endfor; ?>
                                <?php if (count($coords) > 1): ?>
                                    <polyline points="<?= implode(' ', array_map(fn($c) => $c[0] . ',' . $c[1], $coords)) ?>" fill="none" stroke="currentColor" stroke-width="3" /><?php endif; ?>
                                <?php foreach ($coords as $i => $c): ?>
                                    <circle cx="<?= $c[0] ?>" cy="<?= $c[1] ?>" r="5" fill="currentColor" /><text x="<?= $c[0] ?>" y="<?= $h - 20 ?>" text-anchor="middle" font-size="10"><?= htmlspecialchars(date('d/m', strtotime($points[$i]['service_date']))) ?></text><?php endforeach; ?>
                            </svg>
                            <p class="muted">Grafik di atas menampilkan berat badan dari data pemeriksaan yang tercatat. Penilaian status gizi tetap mengikuti hasil pemeriksaan/pedoman yang digunakan Posyandu.</p>
                        </div>
                    <?php else: ?><p>Belum ada data pengukuran untuk anak ini.</p><?php endif; ?>
                </div>
                <div class="panel-box">
                    <h2>Riwayat Pertumbuhan</h2>
                    <div class="table-wrap">
                        <table class="data-table">
                            <tr>
                                <th>Tanggal</th>
                                <th>BB (kg)</th>
                                <th>TB/PB (cm)</th>
                                <th>LK (cm)</th>
                            </tr><?php foreach ($points as $r): ?><tr>
                                    <td><?= htmlspecialchars($r['service_date']) ?></td>
                                    <td><?= htmlspecialchars($r['weight'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($r['height'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($r['head_circumference'] ?? '-') ?></td>
                                </tr><?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>