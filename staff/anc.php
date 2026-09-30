<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pemeriksaan ANC</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head>
<body><div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area"><?php
$mothers=$pdo->query('SELECT id,name,nik FROM ibu_hamil ORDER BY name')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $s=$pdo->prepare('INSERT INTO pemeriksaan_ibu_hamil(mother_id,examination_date,gestational_age_weeks,lila,weight,blood_pressure,complaints,notes) VALUES(?,?,?,?,?,?,?,?)');
 $s->execute([(int)$_POST['mother_id'],$_POST['examination_date'],$_POST['gestational_age_weeks']!==''?$_POST['gestational_age_weeks']:null,$_POST['lila']!==''?$_POST['lila']:null,$_POST['weight']!==''?$_POST['weight']:null,trim($_POST['blood_pressure']??''),trim($_POST['complaints']??''),trim($_POST['notes']??'')]);
 header('Location: anc.php'); exit;
}
$rows=$pdo->query('SELECT a.*,m.name mother_name,m.nik FROM pemeriksaan_ibu_hamil a JOIN ibu_hamil m ON m.id=a.mother_id ORDER BY a.examination_date DESC,a.id DESC LIMIT 100')->fetchAll();
?>
<div class="topbar"><div><h1>Pemeriksaan ANC</h1><p>Pencatatan pemeriksaan kehamilan, LILA, dan usia kehamilan.</p></div></div>
<div class="panel-box"><h2>Tambah Pemeriksaan</h2><form method="post" class="data-form">
<label>Ibu Hamil<select name="mother_id" required><option value="">Pilih ibu</option><?php foreach($mothers as $m):?><option value="<?=$m['id']?>"><?=htmlspecialchars($m['name'])?><?= $m['nik']?' — '.htmlspecialchars($m['nik']):'' ?></option><?php endforeach;?></select></label>
<label>Tanggal Pemeriksaan<input type="date" name="examination_date" required value="<?=date('Y-m-d')?>"></label>
<label>Usia Kehamilan (minggu)<input type="number" step="0.1" name="gestational_age_weeks"></label>
<label>LILA (cm)<input type="number" step="0.1" name="lila"></label>
<label>Berat Badan (kg)<input type="number" step="0.1" name="weight"></label>
<label>Tekanan Darah<input name="blood_pressure" placeholder="Contoh: 110/70"></label>
<label class="full">Keluhan<textarea name="complaints"></textarea></label>
<label class="full">Catatan Petugas<textarea name="notes"></textarea></label>
<div class="full"><button class="btn btn-primary">Simpan Pemeriksaan</button></div></form></div>
<div class="panel-box"><h2>Riwayat ANC</h2><div class="table-wrap"><table class="data-table"><tr><th>Tanggal</th><th>Ibu</th><th>Usia Kehamilan</th><th>LILA</th><th>BB</th><th>Tekanan Darah</th><th>Keluhan</th></tr><?php foreach($rows as $r):?><tr><td><?=htmlspecialchars($r['examination_date'])?></td><td><?=htmlspecialchars($r['mother_name'])?></td><td><?=htmlspecialchars($r['gestational_age_weeks']??'-')?></td><td><?=htmlspecialchars($r['lila']??'-')?></td><td><?=htmlspecialchars($r['weight']??'-')?></td><td><?=htmlspecialchars($r['blood_pressure']?:'-')?></td><td><?=htmlspecialchars($r['complaints']?:'-')?></td></tr><?php endforeach;?></table></div></div></main></div></body></html>