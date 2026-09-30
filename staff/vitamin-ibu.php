<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Vitamin Ibu Hamil</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head>
<body><div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area"><?php
$mothers=$pdo->query('SELECT id,name FROM ibu_hamil ORDER BY name')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $s=$pdo->prepare('INSERT INTO vitamin_ibu_hamil(mother_id,vitamin_date,supplement_name,dose,notes) VALUES(?,?,?,?,?)');
 $s->execute([(int)$_POST['mother_id'],$_POST['vitamin_date'],trim($_POST['supplement_name']??''),trim($_POST['dose']??''),trim($_POST['notes']??'')]);
 header('Location: vitamin-ibu.php'); exit;
}
$rows=$pdo->query('SELECT v.*,m.name mother_name FROM vitamin_ibu_hamil v JOIN ibu_hamil m ON m.id=v.mother_id ORDER BY v.vitamin_date DESC,v.id DESC LIMIT 100')->fetchAll();
?>
<div class="topbar"><div><h1>Vitamin / Suplemen Ibu Hamil</h1><p>Catat pemberian vitamin atau suplemen selama kehamilan.</p></div></div>
<div class="panel-box"><h2>Tambah Data</h2><form method="post" class="data-form">
<label>Ibu Hamil<select name="mother_id" required><option value="">Pilih ibu</option><?php foreach($mothers as $m):?><option value="<?=$m['id']?>"><?=htmlspecialchars($m['name'])?></option><?php endforeach;?></select></label>
<label>Tanggal<input type="date" name="vitamin_date" required value="<?=date('Y-m-d')?>"></label>
<label>Vitamin/Suplemen<input name="supplement_name" required></label><label>Dosis<input name="dose"></label>
<label class="full">Catatan<textarea name="notes"></textarea></label><div class="full"><button class="btn btn-primary">Simpan</button></div></form></div>
<div class="panel-box"><h2>Riwayat Vitamin/Suplemen</h2><div class="table-wrap"><table class="data-table"><tr><th>Tanggal</th><th>Ibu</th><th>Suplemen</th><th>Dosis</th><th>Catatan</th></tr><?php foreach($rows as $r):?><tr><td><?=htmlspecialchars($r['vitamin_date'])?></td><td><?=htmlspecialchars($r['mother_name'])?></td><td><?=htmlspecialchars($r['supplement_name'])?></td><td><?=htmlspecialchars($r['dose']?:'-')?></td><td><?=htmlspecialchars($r['notes']?:'-')?></td></tr><?php endforeach;?></table></div></div></main></div></body></html>