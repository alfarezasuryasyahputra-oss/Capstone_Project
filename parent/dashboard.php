<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

$parentId = (int)$_SESSION['parent_id'];

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function indoDate($date)
{
    if (!$date) return '-';
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $ts = strtotime($date);
    if (!$ts) return e($date);
    return date('j', $ts) . ' ' . $months[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}
function ageText($birthDate)
{
    if (!$birthDate) return '-';
    try {
        $birth = new DateTime($birthDate);
        $now = new DateTime();
        $diff = $birth->diff($now);
    } catch (Exception $e) {
        return '-';
    }
    if ($diff->y > 0) return $diff->y . ' tahun' . ($diff->m > 0 ? ' ' . $diff->m . ' bulan' : '');
    if ($diff->m > 0) return $diff->m . ' bulan' . ($diff->d > 0 ? ' ' . $diff->d . ' hari' : '');
    return $diff->d . ' hari';
}
function latestByDate($rows, $dateKey)
{
    return $rows[0] ?? null;
}

$s = $pdo->prepare('SELECT id,name,phone FROM parent_users WHERE id=? LIMIT 1');
$s->execute([$parentId]);
$parent = $s->fetch();
if (!$parent) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$s = $pdo->prepare('SELECT DISTINCT b.*
    FROM bayi_balita b
    LEFT JOIN parent_children pc ON pc.child_id=b.id AND pc.parent_id=?
    LEFT JOIN parent_mothers pm ON pm.mother_id=b.mother_id AND pm.parent_id=?
    WHERE pc.parent_id IS NOT NULL OR pm.parent_id IS NOT NULL
    ORDER BY b.name');
$s->execute([$parentId, $parentId]);
$children = $s->fetchAll();

$s = $pdo->prepare('SELECT m.* FROM ibu_hamil m INNER JOIN parent_mothers pm ON pm.mother_id=m.id WHERE pm.parent_id=? ORDER BY m.name');
$s->execute([$parentId]);
$mothers = $s->fetchAll();

$selectedId = (int)($_GET['child_id'] ?? 0);
$child = null;
foreach ($children as $c) {
    if ((int)$c['id'] === $selectedId) {
        $child = $c;
        break;
    }
}
if (!$child && $children) $child = $children[0];

$points = [];
$immunizations = [];
$vitamins = [];
$services = [];
if ($child) {
    $s = $pdo->prepare('SELECT service_date,weight,height,head_circumference,nutrition_status,notes FROM pelayanan_balita WHERE child_id=? ORDER BY service_date ASC,id ASC');
    $s->execute([$child['id']]);
    $points = $s->fetchAll();

    $s = $pdo->prepare('SELECT immunization_date,vaccine_name,dose,notes FROM imunisasi WHERE child_id=? ORDER BY immunization_date DESC,id DESC');
    $s->execute([$child['id']]);
    $immunizations = $s->fetchAll();

    $s = $pdo->prepare('SELECT vitamin_date,vitamin_name,dose,notes FROM vitamin WHERE child_id=? ORDER BY vitamin_date DESC,id DESC');
    $s->execute([$child['id']]);
    $vitamins = $s->fetchAll();

    $s = $pdo->prepare('SELECT service_date,weight,height,head_circumference,nutrition_status,notes FROM pelayanan_balita WHERE child_id=? ORDER BY service_date DESC,id DESC LIMIT 5');
    $s->execute([$child['id']]);
    $services = $s->fetchAll();
}

$anc = [];
$latestAnc = [];
foreach ($mothers as $mother) {
    $s = $pdo->prepare('SELECT * FROM pemeriksaan_ibu_hamil WHERE mother_id=? ORDER BY examination_date DESC,id DESC LIMIT 10');
    $s->execute([$mother['id']]);
    $anc[$mother['id']] = $s->fetchAll();
    $latestAnc[$mother['id']] = $anc[$mother['id']][0] ?? null;
}

$jadwal = $pdo->query("SELECT * FROM jadwal_posyandu WHERE event_date >= CURDATE() ORDER BY event_date,event_time,id LIMIT 5")->fetchAll();
$latestPoint = $points ? $points[count($points) - 1] : null;
$latestImmunization = $immunizations[0] ?? null;
$latestVitamin = $vitamins[0] ?? null;
$childCount = count($children);
$motherCount = count($mothers);
$parentInitial = mb_strtoupper(mb_substr(trim($parent['name']), 0, 1));
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Orang Tua | Posyandu Bina Warga</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/staff.css">
    <link rel="stylesheet" href="../css/parent.css">
</head>

<body class="staff-body parent-body parent-dashboard">
    <div class="dashboard">
        <aside class="sidebar">
            <div class="brand">♡ Posyandu Bina Warga</div>
            <nav>
                <a class="active" href="dashboard.php">🏠 Dashboard</a>
                <a href="dashboard.php#anak">👶 Anak Saya</a>
                <a href="dashboard.php#ibu">🤰 Data Ibu</a>
                <a href="dashboard.php#jadwal">📅 Jadwal Posyandu</a>
                <a href="dashboard.php#edukasi">📚 Edukasi</a>
                <a href="logout.php">🚪 Keluar</a>
            </nav>
        </aside>

        <main class="main-area">
            <div class="parent-topbar">
                <div class="parent-greeting">
                    <div class="eyebrow">Dashboard kesehatan keluarga</div>
                    <h1>Halo, <?= e($parent['name']) ?> 👋</h1>
                    <p>Pantau informasi ibu dan anak yang terhubung dengan akun Anda.</p>
                </div>
                <div class="parent-user-chip">
                    <div class="parent-user-avatar"><?= e($parentInitial) ?></div><span><?= e($parent['name']) ?></span>
                </div>
            </div>

            <section class="parent-stats" aria-label="Ringkasan akun">
                <div class="parent-stat">
                    <div class="parent-stat-icon">👶</div>
                    <div><small>Anak terhubung</small><strong><?= $childCount ?> anak</strong></div>
                </div>
                <div class="parent-stat">
                    <div class="parent-stat-icon">🤰</div>
                    <div><small>Data ibu</small><strong><?= $motherCount ?> data</strong></div>
                </div>
                <div class="parent-stat">
                    <div class="parent-stat-icon">📅</div>
                    <div><small>Jadwal tersedia</small><strong><?= count($jadwal) ?> jadwal</strong></div>
                </div>
                <div class="parent-stat">
                    <div class="parent-stat-icon">💉</div>
                    <div><small>Imunisasi tercatat</small><strong><?= count($immunizations) ?> data</strong></div>
                </div>
            </section>

            <div class="parent-grid">
                <div class="parent-main">
                    <section class="parent-panel" id="anak">
                        <div class="parent-panel-header">
                            <div>
                                <h2>Anak Saya</h2>
                                <p><?= $childCount ? 'Pilih anak untuk melihat data kesehatannya.' : 'Belum ada anak yang terhubung ke akun ini.' ?></p>
                            </div>
                        </div>
                        <?php if ($children): ?>
                            <div class="child-switcher">
                                <?php foreach ($children as $c): ?>
                                    <a class="child-card <?= ($child && (int)$child['id'] === (int)$c['id']) ? 'active' : '' ?>" href="dashboard.php?child_id=<?= (int)$c['id'] ?>#anak">
                                        <div class="child-card-top">
                                            <div class="child-avatar">👶</div>
                                            <div><strong><?= e($c['name']) ?></strong><small><?= e(ageText($c['birth_date'])) ?></small></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?><div class="empty-state">
                                <div class="empty-icon">👶</div>Belum ada data anak yang terhubung. Silakan hubungi kader Posyandu.
                            </div><?php endif; ?>
                    </section>

                    <?php if ($child): ?>
                        <section class="parent-panel">
                            <div class="child-profile">
                                <div class="child-profile-avatar">👶</div>
                                <div>
                                    <h2><?= e($child['name']) ?></h2>
                                    <p><?= e(ageText($child['birth_date'])) ?> · <?= e($child['gender'] ?: 'Jenis kelamin belum diisi') ?> · Lahir <?= e(indoDate($child['birth_date'])) ?></p>
                                </div>
                                <a class="action-btn primary" href="../staff/print-kms.php?child_id=<?= (int)$child['id'] ?>" target="_blank">🖨 Cetak KMS</a>
                            </div>

                            <div class="latest-grid">
                                <div class="latest-card"><small>Berat terakhir</small>
                                    <div class="latest-value"><?= $latestPoint && $latestPoint['weight'] !== null ? e(number_format((float)$latestPoint['weight'], 1, ',', '.')) . ' kg' : '-' ?></div>
                                    <div class="latest-date"><?= $latestPoint ? e(indoDate($latestPoint['service_date'])) : 'Belum ada data' ?></div>
                                </div>
                                <div class="latest-card"><small>Tinggi terakhir</small>
                                    <div class="latest-value"><?= $latestPoint && $latestPoint['height'] !== null ? e(number_format((float)$latestPoint['height'], 1, ',', '.')) . ' cm' : '-' ?></div>
                                    <div class="latest-date"><?= $latestPoint ? e(indoDate($latestPoint['service_date'])) : 'Belum ada data' ?></div>
                                </div>
                                <div class="latest-card"><small>Lingkar kepala</small>
                                    <div class="latest-value"><?= $latestPoint && $latestPoint['head_circumference'] !== null ? e(number_format((float)$latestPoint['head_circumference'], 1, ',', '.')) . ' cm' : '-' ?></div>
                                    <div class="latest-date"><?= $latestPoint ? e(indoDate($latestPoint['service_date'])) : 'Belum ada data' ?></div>
                                </div>
                            </div>

                            <div class="parent-panel-header">
                                <div>
                                    <h2>Grafik Pertumbuhan</h2>
                                    <p>Riwayat berat badan berdasarkan data pemeriksaan Posyandu.</p>
                                </div>
                            </div>
                            <?php if ($points):
                                $validWeights = array_values(array_filter(array_map(fn($x) => $x['weight'] !== null ? (float)$x['weight'] : null, $points), fn($v) => $v !== null));
                                if ($validWeights) {
                                    $minW = max(0, min($validWeights) - 2);
                                    $maxW = max(15, max($validWeights) + 2);
                                    $range = max(1, $maxW - $minW);
                                    $w = 760;
                                    $h = 300;
                                    $pl = 58;
                                    $pr = 22;
                                    $pt = 24;
                                    $pb = 54;
                                    $iw = $w - $pl - $pr;
                                    $ih = $h - $pt - $pb;
                                    $coords = [];
                                    $weightPoints = [];
                                    foreach ($points as $p) {
                                        if ($p['weight'] !== null) $weightPoints[] = $p;
                                    }
                                    foreach ($weightPoints as $i => $p) {
                                        $x = $pl + ($i / max(1, count($weightPoints) - 1)) * $iw;
                                        $y = $pt + (1 - (((float)$p['weight'] - $minW) / $range)) * $ih;
                                        $coords[] = [$x, $y];
                                    }
                                }
                            endif; ?>
                            <?php if (!empty($weightPoints)): ?>
                                <div class="chart-wrap">
                                    <svg viewBox="0 0 <?= $w ?> <?= $h ?>" class="parent-growth-chart" role="img" aria-label="Grafik berat badan <?= e($child['name']) ?>">
                                        <line x1="<?= $pl ?>" y1="<?= $pt ?>" x2="<?= $pl ?>" y2="<?= $h - $pb ?>" stroke="currentColor" opacity=".22" />
                                        <line x1="<?= $pl ?>" y1="<?= $h - $pb ?>" x2="<?= $w - $pr ?>" y2="<?= $h - $pb ?>" stroke="currentColor" opacity=".22" />
                                        <?php for ($i = 0; $i <= 4; $i++): $v = $minW + $range * $i / 4;
                                            $y = $pt + (1 - $i / 4) * $ih; ?>
                                            <line x1="<?= $pl ?>" y1="<?= $y ?>" x2="<?= $w - $pr ?>" y2="<?= $y ?>" stroke="currentColor" opacity=".07" />
                                            <text x="7" y="<?= $y + 5 ?>" font-size="12"><?= e(number_format($v, 1, ',', '.')) ?> kg</text>
                                        <?php endfor; ?>
                                        <?php if (count($coords) > 1): ?>
                                            <polyline points="<?= e(implode(' ', array_map(fn($c) => $c[0] . ',' . $c[1], $coords))) ?>" fill="none" stroke="currentColor" stroke-width="3" /><?php endif; ?>
                                        <?php foreach ($coords as $i => $c): ?>
                                            <circle cx="<?= $c[0] ?>" cy="<?= $c[1] ?>" r="5" fill="currentColor" /><text x="<?= $c[0] ?>" y="<?= $h - 17 ?>" text-anchor="middle" font-size="10"><?= e(date('d/m', strtotime($weightPoints[$i]['service_date']))) ?></text><?php endforeach; ?>
                                    </svg>
                                </div>
                                <p class="chart-caption">Grafik ini menampilkan data berat yang tercatat di Posyandu dan bukan pengganti penilaian klinis.</p>
                            <?php else: ?><div class="empty-state">
                                    <div class="empty-icon">📈</div>Belum ada data berat badan untuk dibuat grafik.
                                </div><?php endif; ?>
                        </section>

                        <section class="parent-panel">
                            <div class="parent-panel-header">
                                <div>
                                    <h2>Riwayat Pelayanan Anak</h2>
                                    <p>Lima pemeriksaan terbaru untuk <?= e($child['name']) ?>.</p>
                                </div>
                            </div>
                            <?php if ($services): ?>
                                <div class="parent-table-wrap">
                                    <table class="parent-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Berat</th>
                                                <th>Tinggi</th>
                                                <th>LK</th>
                                                <th>Status Gizi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($services as $r): ?><tr>
                                                    <td><?= e(indoDate($r['service_date'])) ?></td>
                                                    <td><?= $r['weight'] !== null ? e(number_format((float)$r['weight'], 1, ',', '.')) . ' kg' : '-' ?></td>
                                                    <td><?= $r['height'] !== null ? e(number_format((float)$r['height'], 1, ',', '.')) . ' cm' : '-' ?></td>
                                                    <td><?= $r['head_circumference'] !== null ? e(number_format((float)$r['head_circumference'], 1, ',', '.')) . ' cm' : '-' ?></td>
                                                    <td><?= e($r['nutrition_status'] ?: '-') ?></td>
                                                </tr><?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?><div class="empty-state">
                                    <div class="empty-icon">🩺</div>Belum ada riwayat pelayanan untuk anak ini.
                                </div><?php endif; ?>
                        </section>

                        <section class="parent-panel">
                            <div class="parent-panel-header">
                                <div>
                                    <h2>Imunisasi & Vitamin</h2>
                                    <p>Riwayat yang tercatat untuk <?= e($child['name']) ?>.</p>
                                </div>
                            </div>
                            <div class="service-list">
                                <div class="service-item">
                                    <div class="service-icon">💉</div>
                                    <div><strong><?= $latestImmunization ? e($latestImmunization['vaccine_name']) : 'Belum ada imunisasi' ?></strong><span><?= $latestImmunization ? e(indoDate($latestImmunization['immunization_date'])) . ' · Dosis ' . e($latestImmunization['dose'] ?: '-') : 'Data imunisasi belum tersedia.' ?></span></div>
                                    <div class="service-value"><?= count($immunizations) ?> data</div>
                                </div>
                                <div class="service-item">
                                    <div class="service-icon">💊</div>
                                    <div><strong><?= $latestVitamin ? e($latestVitamin['vitamin_name']) : 'Belum ada vitamin' ?></strong><span><?= $latestVitamin ? e(indoDate($latestVitamin['vitamin_date'])) . ' · Dosis ' . e($latestVitamin['dose'] ?: '-') : 'Data vitamin belum tersedia.' ?></span></div>
                                    <div class="service-value"><?= count($vitamins) ?> data</div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>

                <aside class="parent-side">
                    <section class="parent-panel" id="ibu">
                        <div class="parent-panel-header">
                            <div>
                                <h2>Data Ibu</h2>
                                <p>Data ibu yang terhubung dengan akun.</p>
                            </div>
                        </div>
                        <?php if ($mothers): foreach ($mothers as $mother): $la = $latestAnc[$mother['id']] ?? null; ?>
                                <div class="schedule-card" style="background:linear-gradient(145deg,#fff7f8,#fff)">
                                    <div class="schedule-date">🤰 IBU HAMIL</div>
                                    <h3><?= e($mother['name']) ?></h3>
                                    <div class="schedule-meta">
                                        <span>NIK: <?= e($mother['nik'] ?: '-') ?></span>
                                        <span>HPHT: <?= e(indoDate($mother['hpht'])) ?></span>
                                        <span>HPL: <?= e(indoDate($mother['edd'])) ?></span>
                                        <?php if ($la): ?><span>Pemeriksaan: <?= e(indoDate($la['examination_date'])) ?></span><span>Usia kehamilan: <?= e($la['gestational_age_weeks'] ?? '-') ?> minggu</span><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach;
                        else: ?><div class="empty-state">
                                <div class="empty-icon">🤰</div>Belum ada data ibu yang terhubung.
                            </div><?php endif; ?>
                    </section>

                    <section class="parent-panel" id="jadwal">
                        <div class="parent-panel-header">
                            <div>
                                <h2>Jadwal Posyandu</h2>
                                <p>Kegiatan yang akan datang.</p>
                            </div>
                        </div>
                        <?php if ($jadwal): foreach ($jadwal as $j): ?>
                                <div class="schedule-card">
                                    <div class="schedule-date">📅 <?= e(indoDate($j['event_date'])) ?></div>
                                    <h3><?= e($j['title']) ?></h3>
                                    <div class="schedule-meta"><span>⏰ <?= e($j['event_time'] ? substr($j['event_time'], 0, 5) : 'Jam belum ditentukan') ?></span><span>📍 <?= e($j['location'] ?: 'Lokasi belum ditentukan') ?></span></div>
                                </div>
                            <?php endforeach;
                        else: ?><div class="empty-state">
                                <div class="empty-icon">📅</div>Belum ada jadwal Posyandu berikutnya.
                            </div><?php endif; ?>
                    </section>

                    <section class="parent-panel" id="edukasi">
                        <div class="parent-panel-header">
                            <div>
                                <h2>Edukasi Kesehatan</h2>
                                <p>Pengingat umum untuk keluarga.</p>
                            </div>
                        </div>
                        <ul class="education-list">
                            <li>Ikuti jadwal Posyandu dan pemeriksaan sesuai anjuran petugas.</li>
                            <li>Simpan catatan kesehatan dan hasil pemeriksaan untuk dibawa saat kunjungan.</li>
                            <li>Perhatikan perubahan pertumbuhan dan sampaikan pertanyaan kepada petugas.</li>
                            <li>Untuk keluhan atau kondisi yang mengkhawatirkan, hubungi tenaga kesehatan.</li>
                        </ul>
                        <p class="parent-footer-note">Informasi edukasi bersifat umum dan tidak menggantikan pemeriksaan tenaga kesehatan.</p>
                    </section>
                </aside>
            </div>
        </main>
    </div>
</body>

</html>