<?php
require_once 'auth.php';require_once __DIR__.'/../config/database.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $a=$_POST['action']??'';
 if($a==='delete'){$s=$pdo->prepare('DELETE FROM ibu_hamil WHERE id=?');$s->execute([(int)$_POST['id']]);header('Location: ibu-hamil.php');exit;}
 if($a==='save'){
  $d=[trim($_POST['name']??''),trim($_POST['phone']??''),trim($_POST['address']??''),$_POST['birth_date']?:null,$_POST['hpht']?:null,$_POST['edd']?:null,(int)($_POST['pregnancy_number']??0),trim($_POST['notes']??'')];
  if(!empty($_POST['id'])){$s=$pdo->prepare('UPDATE ibu_hamil SET name=?,phone=?,address=?,birth_date=?,hpht=?,edd=?,pregnancy_number=?,notes=? WHERE id=?');$s->execute([...$d,(int)$_POST['id']]);}
  else{$s=$pdo->prepare('INSERT INTO ibu_hamil(name,phone,address,birth_date,hpht,edd,pregnancy_number,notes) VALUES(?,?,?,?,?,?,?,?)');$s->execute($d);}
  header('Location: ibu-hamil.php');exit;
 }}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM ibu_hamil WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$q=trim($_GET['q']??'');$s=$q!==''?$pdo->prepare('SELECT * FROM ibu_hamil WHERE name LIKE ? OR phone LIKE ? ORDER BY id DESC'):$pdo->query('SELECT * FROM ibu_hamil ORDER BY id DESC');
if($q!==''){$like="%$q%";$s->execute([$like,$like]);}$rows=$s->fetchAll();
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Data Ibu Hamil</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head>
<body><div class="dashboard"><aside class="sidebar"><div class="brand">♡ Posyandu Staff</div><nav><a href="dashboard.php">Dashboard</a><a class="active" href="ibu-hamil.php">Ibu Hamil</a><a href="bayi-balita.php">Bayi/Balita</a><a href="appointment.php">Appointment</a><a href="../index.php">Website Publik</a><a href="logout.php">Logout</a></nav></aside>
<main class="main-area"><div class="topbar"><div><h1>Data Ibu Hamil</h1><p>Tambah, cari, edit, dan hapus data.</p></div></div>
<div class="panel-box"><h2><?= $edit?'Edit Data':'Tambah Data'?></h2><form method="post" class="data-form"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=htmlspecialchars($edit['id']??'')?>">
<label>Nama Lengkap<input name="name" required value="<?=htmlspecialchars($edit['name']??'')?>"></label><label>Nomor Telepon<input name="phone" value="<?=htmlspecialchars($edit['phone']??'')?>"></label>
<label>Tanggal Lahir<input type="date" name="birth_date" value="<?=htmlspecialchars($edit['birth_date']??'')?>"></label><label>Kehamilan Ke-<input type="number" min="1" name="pregnancy_number" value="<?=htmlspecialchars($edit['pregnancy_number']??'')?>"></label>
<label>HPHT<input type="date" name="hpht" value="<?=htmlspecialchars($edit['hpht']??'')?>"></label><label>Perkiraan Persalinan<input type="date" name="edd" value="<?=htmlspecialchars($edit['edd']??'')?>"></label>
<label class="full">Alamat<textarea name="address"><?=htmlspecialchars($edit['address']??'')?></textarea></label><label class="full">Catatan<textarea name="notes"><?=htmlspecialchars($edit['notes']??'')?></textarea></label>
<div class="full"><button class="btn btn-primary">Simpan</button> <?php if($edit):?><a class="btn btn-light" href="ibu-hamil.php">Batal</a><?php endif;?></div></form></div>
<div class="panel-box"><h2>Daftar Ibu Hamil</h2><form method="get" class="action-row"><input name="q" placeholder="Cari nama / telepon" value="<?=htmlspecialchars($q)?>"><button class="small-btn">Cari</button></form><div class="table-wrap"><table class="data-table"><tr><th>Nama</th><th>Telepon</th><th>HPHT</th><th>HPL</th><th>Aksi</th></tr>
<?php foreach($rows as $r):?><tr><td><?=htmlspecialchars($r['name'])?></td><td><?=htmlspecialchars($r['phone'])?></td><td><?=htmlspecialchars($r['hpht']?:'-')?></td><td><?=htmlspecialchars($r['edd']?:'-')?></td><td><a class="small-btn" href="?edit=<?=$r['id']?>">Edit</a> <form style="display:inline" method="post" onsubmit="return confirm('Hapus data ini?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="small-btn danger">Hapus</button></form></td></tr><?php endforeach;?></table></div></div>
</main></div></body></html>
