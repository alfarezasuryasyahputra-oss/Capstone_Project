<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Jadwal Posyandu</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head>
<body><div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area"><div class="topbar"><div><h1>Jadwal Posyandu</h1><p>Kelola jadwal kegiatan Posyandu untuk kader dan orang tua.</p></div></div>
<?php
if($_SERVER['REQUEST_METHOD']==='POST'){
  $a=$_POST['action']??'';
  if($a==='delete'){ $s=$pdo->prepare('DELETE FROM jadwal_posyandu WHERE id=?'); $s->execute([(int)$_POST['id']]); header('Location: jadwal.php'); exit; }
  $s=$pdo->prepare('INSERT INTO jadwal_posyandu(title,event_date,event_time,location,description) VALUES(?,?,?,?,?)');
  $s->execute([trim($_POST['title']??''),$_POST['event_date']??null,$_POST['event_time']?:null,trim($_POST['location']??''),trim($_POST['description']??'')]);
  header('Location: jadwal.php'); exit;
}
$rows=$pdo->query('SELECT * FROM jadwal_posyandu ORDER BY event_date DESC,event_time DESC')->fetchAll();
?>
<div class="panel-box"><h2>Tambah Jadwal</h2><form method="post" class="data-form">
<label>Nama Kegiatan<input name="title" required placeholder="Contoh: Posyandu Balita"></label>
<label>Tanggal<input type="date" name="event_date" required></label>
<label>Jam<input type="time" name="event_time"></label>
<label>Lokasi<input name="location" placeholder="Balai banjar / Posyandu"></label>
<label class="full">Keterangan<textarea name="description"></textarea></label>
<div class="full"><button class="btn btn-primary">Simpan Jadwal</button></div></form></div>
<div class="panel-box"><h2>Daftar Jadwal</h2><div class="table-wrap"><table class="data-table"><tr><th>Kegiatan</th><th>Tanggal</th><th>Jam</th><th>Lokasi</th><th>Keterangan</th><th>Aksi</th></tr>
<?php foreach($rows as $r): ?><tr><td><?=htmlspecialchars($r['title'])?></td><td><?=htmlspecialchars($r['event_date'])?></td><td><?=htmlspecialchars($r['event_time']?substr($r['event_time'],0,5):'-')?></td><td><?=htmlspecialchars($r['location']?:'-')?></td><td><?=htmlspecialchars($r['description']?:'-')?></td><td><form method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="small-btn danger" onclick="return confirm('Hapus jadwal?')">Hapus</button></form></td></tr><?php endforeach; ?></table></div></div></main></div></body></html>