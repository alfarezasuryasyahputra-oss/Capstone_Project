<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Akun Orang Tua</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head>
<body><div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area"><?php
$children=$pdo->query('SELECT id,name FROM bayi_balita ORDER BY name')->fetchAll();
$mothers=$pdo->query('SELECT id,name FROM ibu_hamil ORDER BY name')->fetchAll();
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $action=$_POST['action']??'';
 if($action==='delete'){ $s=$pdo->prepare('DELETE FROM parent_users WHERE id=?');$s->execute([(int)$_POST['id']]);header('Location: orang-tua.php');exit;}
 if($action==='save'){
   $name=trim($_POST['name']??'');$username=trim($_POST['username']??'');$password=$_POST['password']??'';
   if($name && $username && strlen($password)>=8){
    try{$s=$pdo->prepare('INSERT INTO parent_users(username,password_hash,name,phone,mother_id,child_id) VALUES(?,?,?,?,?,?)');$s->execute([$username,password_hash($password,PASSWORD_DEFAULT),$name,trim($_POST['phone']??''),!empty($_POST['mother_id'])?(int)$_POST['mother_id']:null,!empty($_POST['child_id'])?(int)$_POST['child_id']:null]);$message='Akun orang tua berhasil dibuat.';}catch(PDOException $e){$message='Username sudah digunakan atau data tidak dapat disimpan.';}
   }else{$message='Nama, username, dan password minimal 8 karakter wajib diisi.';}
 }
}
$rows=$pdo->query('SELECT p.*,b.name child_name,m.name mother_name FROM parent_users p LEFT JOIN bayi_balita b ON b.id=p.child_id LEFT JOIN ibu_hamil m ON m.id=p.mother_id ORDER BY p.id DESC')->fetchAll();
?>
<div class="topbar"><div><h1>Akun Orang Tua / Ibu</h1><p>Buat akses dashboard kesehatan untuk orang tua.</p></div></div>
<?php if($message):?><div class="success-box"><?=htmlspecialchars($message)?></div><?php endif;?>
<div class="panel-box"><h2>Buat Akun Orang Tua</h2><form method="post" class="data-form"><input type="hidden" name="action" value="save">
<label>Nama Orang Tua<input name="name" required></label><label>No. Telepon<input name="phone"></label>
<label>Username<input name="username" required></label><label>Password<input type="password" name="password" minlength="8" required></label>
<label>Hubungkan ke Ibu Hamil<select name="mother_id"><option value="">Tidak dipilih</option><?php foreach($mothers as $m):?><option value="<?=$m['id']?>"><?=htmlspecialchars($m['name'])?></option><?php endforeach;?></select></label>
<label>Hubungkan ke Anak<select name="child_id"><option value="">Tidak dipilih</option><?php foreach($children as $c):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option><?php endforeach;?></select></label>
<div class="full"><button class="btn btn-primary">Buat Akun</button></div></form></div>
<div class="panel-box"><h2>Daftar Akun Orang Tua</h2><div class="table-wrap"><table class="data-table"><tr><th>Nama</th><th>Username</th><th>Telepon</th><th>Anak</th><th>Ibu Hamil</th><th>Aksi</th></tr><?php foreach($rows as $r):?><tr><td><?=htmlspecialchars($r['name'])?></td><td><?=htmlspecialchars($r['username'])?></td><td><?=htmlspecialchars($r['phone']?:'-')?></td><td><?=htmlspecialchars($r['child_name']?:'-')?></td><td><?=htmlspecialchars($r['mother_name']?:'-')?></td><td><form method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="small-btn danger" onclick="return confirm('Hapus akun?')">Hapus</button></form></td></tr><?php endforeach;?></table></div></div></main></div></body></html>