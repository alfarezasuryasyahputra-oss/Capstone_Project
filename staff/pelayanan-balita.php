<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pelayanan Balita</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head>
<body><div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area"><?php
$children=$pdo->query('SELECT id,name FROM bayi_balita ORDER BY name')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $s=$pdo->prepare('INSERT INTO pelayanan_balita(child_id,service_date,weight,height,head_circumference,nutrition_status,notes) VALUES(?,?,?,?,?,?,?)');
 $s->execute([(int)$_POST['child_id'],$_POST['service_date'],$_POST['weight']!==''?$_POST['weight']:null,$_POST['height']!==''?$_POST['height']:null,$_POST['head_circumference']!==''?$_POST['head_circumference']:null,trim($_POST['nutrition_status']??''),trim($_POST['notes']??'')]);
 $u=$pdo->prepare('UPDATE bayi_balita SET weight=?,height=?,head_circumference=? WHERE id=?');
 $u->execute([$_POST['weight']!==''?$_POST['weight']:null,$_POST['height']!==''?$_POST['height']:null,$_POST['head_circumference']!==''?$_POST['head_circumference']:null,(int)$_POST['child_id']]);
 header('Location: pelayanan-balita.php'); exit;
}
$rows=$pdo->query('SELECT p.*,b.name child_name FROM pelayanan_balita p JOIN bayi_balita b ON b.id=p.child_id ORDER BY p.service_date DESC,p.id DESC LIMIT 100')->fetchAll();
?>
<div class="topbar"><div><h1>Pelayanan Balita</h1><p>Penimbangan, pengukuran, dan pencatatan status gizi untuk KMS digital.</p></div></div>
<div class="panel-box"><h2>Tambah Pemeriksaan</h2><form method="post" class="data-form">
<label>Balita<select name="child_id" required><option value="">Pilih anak</option><?php foreach($children as $c): ?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option><?php endforeach;?></select></label>
<label>Tanggal<input type="date" name="service_date" required value="<?=date('Y-m-d')?>"></label>
<label>Berat Badan (kg)<input type="number" step="0.01" name="weight"></label>
<label>Tinggi/Panjang (cm)<input type="number" step="0.1" name="height"></label>
<label>Lingkar Kepala (cm)<input type="number" step="0.1" name="head_circumference"></label>
<label>Status Gizi<select name="nutrition_status"><option value="">Belum dinilai</option><option>Baik</option><option>Perlu pemantauan</option><option>Kurang</option><option>Buruk</option></select></label>
<label class="full">Catatan<textarea name="notes"></textarea></label><div class="full"><button class="btn btn-primary">Simpan Pemeriksaan</button></div></form></div>
<div class="panel-box"><h2>Riwayat Pelayanan</h2><div class="table-wrap"><table class="data-table"><tr><th>Tanggal</th><th>Balita</th><th>BB</th><th>TB/PB</th><th>LK</th><th>Status Gizi</th><th>Catatan</th></tr><?php foreach($rows as $r):?><tr><td><?=htmlspecialchars($r['service_date'])?></td><td><?=htmlspecialchars($r['child_name'])?></td><td><?=htmlspecialchars($r['weight']??'-')?></td><td><?=htmlspecialchars($r['height']??'-')?></td><td><?=htmlspecialchars($r['head_circumference']??'-')?></td><td><?=htmlspecialchars($r['nutrition_status']?:'-')?></td><td><?=htmlspecialchars($r['notes']?:'-')?></td></tr><?php endforeach;?></table></div></div></main></div></body></html>