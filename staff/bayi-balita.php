<?php
require_once 'auth.php';require_once __DIR__.'/../config/database.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $a=$_POST['action']??'';
 if($a==='delete'){$s=$pdo->prepare('DELETE FROM bayi_balita WHERE id=?');$s->execute([(int)$_POST['id']]);header('Location: bayi-balita.php');exit;}
 if($a==='save'){
  $d=[trim($_POST['name']??''),trim($_POST['mother_name']??''),$_POST['birth_date']?:null,$_POST['gender']??'',$_POST['weight']!==''?$_POST['weight']:null,$_POST['height']!==''?$_POST['height']:null,$_POST['head_circumference']!==''?$_POST['head_circumference']:null,trim($_POST['notes']??'')];
  if(!empty($_POST['id'])){$s=$pdo->prepare('UPDATE bayi_balita SET name=?,mother_name=?,birth_date=?,gender=?,weight=?,height=?,head_circumference=?,notes=? WHERE id=?');$s->execute([...$d,(int)$_POST['id']]);}
  else{$s=$pdo->prepare('INSERT INTO bayi_balita(name,mother_name,birth_date,gender,weight,height,head_circumference,notes) VALUES(?,?,?,?,?,?,?,?)');$s->execute($d);}
  header('Location: bayi-balita.php');exit;
 }}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM bayi_balita WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$q=trim($_GET['q']??'');$s=$q!==''?$pdo->prepare('SELECT * FROM bayi_balita WHERE name LIKE ? OR mother_name LIKE ? ORDER BY id DESC'):$pdo->query('SELECT * FROM bayi_balita ORDER BY id DESC');
if($q!==''){$like="%$q%";$s->execute([$like,$like]);}$rows=$s->fetchAll();
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Data Bayi/Balita</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head>
<body><div class="dashboard"><aside class="sidebar"><div class="brand">♡ Posyandu Staff</div><nav><a href="dashboard.php">Dashboard</a><a href="ibu-hamil.php">Ibu Hamil</a><a class="active" href="bayi-balita.php">Bayi/Balita</a><a href="appointment.php">Appointment</a><a href="../index.php">Website Publik</a><a href="logout.php">Logout</a></nav></aside>
<main class="main-area"><div class="topbar"><div><h1>Data Bayi/Balita</h1><p>Catat data dasar dan hasil pengukuran.</p></div></div>
<div class="panel-box"><h2><?= $edit?'Edit Data':'Tambah Data'?></h2><form method="post" class="data-form"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=htmlspecialchars($edit['id']??'')?>">
<label>Nama Anak<input name="name" required value="<?=htmlspecialchars($edit['name']??'')?>"></label><label>Nama Ibu<input name="mother_name" value="<?=htmlspecialchars($edit['mother_name']??'')?>"></label>
<label>Tanggal Lahir<input type="date" name="birth_date" value="<?=htmlspecialchars($edit['birth_date']??'')?>"></label><label>Jenis Kelamin<select name="gender"><option value="">Pilih</option><option <?=($edit['gender']??'')==='Laki-laki'?'selected':''?>>Laki-laki</option><option <?=($edit['gender']??'')==='Perempuan'?'selected':''?>>Perempuan</option></select></label>
<label>Berat Badan (kg)<input type="number" step="0.01" name="weight" value="<?=htmlspecialchars($edit['weight']??'')?>"></label><label>Tinggi/Panjang (cm)<input type="number" step="0.1" name="height" value="<?=htmlspecialchars($edit['height']??'')?>"></label>
<label>Lingkar Kepala (cm)<input type="number" step="0.1" name="head_circumference" value="<?=htmlspecialchars($edit['head_circumference']??'')?>"></label><label class="full">Catatan<textarea name="notes"><?=htmlspecialchars($edit['notes']??'')?></textarea></label>
<div class="full"><button class="btn btn-primary">Simpan</button> <?php if($edit):?><a class="btn btn-light" href="bayi-balita.php">Batal</a><?php endif;?></div></form></div>
<div class="panel-box"><h2>Daftar Bayi/Balita</h2><form method="get" class="action-row"><input name="q" placeholder="Cari nama anak / ibu" value="<?=htmlspecialchars($q)?>"><button class="small-btn">Cari</button></form><div class="table-wrap"><table class="data-table"><tr><th>Anak</th><th>Ibu</th><th>Tgl Lahir</th><th>JK</th><th>BB/TB</th><th>Aksi</th></tr>
<?php foreach($rows as $r):?><tr><td><?=htmlspecialchars($r['name'])?></td><td><?=htmlspecialchars($r['mother_name'])?></td><td><?=htmlspecialchars($r['birth_date']?:'-')?></td><td><?=htmlspecialchars($r['gender']?:'-')?></td><td><?=htmlspecialchars($r['weight']?:'-')?> / <?=htmlspecialchars($r['height']?:'-')?></td><td><a class="small-btn" href="?edit=<?=$r['id']?>">Edit</a> <form style="display:inline" method="post" onsubmit="return confirm('Hapus data ini?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="small-btn danger">Hapus</button></form></td></tr><?php endforeach;?></table></div></div>
</main></div></body></html>
