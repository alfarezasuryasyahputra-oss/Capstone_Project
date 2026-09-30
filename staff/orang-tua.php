<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$children=$pdo->query('SELECT id,name,birth_date,gender FROM bayi_balita ORDER BY name')->fetchAll();
$mothers=$pdo->query('SELECT id,name,nik FROM ibu_hamil ORDER BY name')->fetchAll();
$message=''; $error='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';
    if($action==='delete'){
        $id=(int)($_POST['id']??0);
        $s=$pdo->prepare('DELETE FROM parent_users WHERE id=?');
        $s->execute([$id]);
        header('Location: orang-tua.php?msg=deleted'); exit;
    }
    if($action==='save'){
        $name=trim($_POST['name']??'');
        $username=trim($_POST['username']??'');
        $password=$_POST['password']??'';
        $phone=trim($_POST['phone']??'');
        $childIds=array_values(array_unique(array_filter(array_map('intval',$_POST['child_ids']??[]))));
        $motherIds=array_values(array_unique(array_filter(array_map('intval',$_POST['mother_ids']??[]))));
        if(!$name || !$username || strlen($password)<8){
            $error='Nama, username, dan password minimal 8 karakter wajib diisi.';
        } else {
            try{
                $pdo->beginTransaction();
                $legacyChild=$childIds[0]??null;
                $legacyMother=$motherIds[0]??null;
                $s=$pdo->prepare('INSERT INTO parent_users(username,password_hash,name,phone,mother_id,child_id) VALUES(?,?,?,?,?,?)');
                $s->execute([$username,password_hash($password,PASSWORD_DEFAULT),$name,$phone,$legacyMother,$legacyChild]);
                $parentId=(int)$pdo->lastInsertId();
                if($childIds){
                    $s=$pdo->prepare('INSERT INTO parent_children(parent_id,child_id) VALUES(?,?)');
                    foreach($childIds as $childId) $s->execute([$parentId,$childId]);
                }
                if($motherIds){
                    $s=$pdo->prepare('INSERT INTO parent_mothers(parent_id,mother_id) VALUES(?,?)');
                    foreach($motherIds as $motherId) $s->execute([$parentId,$motherId]);
                }
                $pdo->commit();
                $message='Akun orang tua berhasil dibuat dengan '.count($childIds).' anak.';
            }catch(PDOException $e){
                if($pdo->inTransaction()) $pdo->rollBack();
                $error='Username sudah digunakan atau data tidak dapat disimpan.';
            }
        }
    }
}
if(isset($_GET['msg']) && $_GET['msg']==='deleted') $message='Akun orang tua berhasil dihapus.';

$rows=$pdo->query("SELECT p.*, (SELECT COUNT(*) FROM parent_children pc WHERE pc.parent_id=p.id) child_count, (SELECT COUNT(*) FROM parent_mothers pm WHERE pm.parent_id=p.id) mother_count FROM parent_users p ORDER BY p.id DESC")->fetchAll();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Akun Orang Tua</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head>
<body><div class="dashboard"><?php require __DIR__ . '/sidebar.php'; ?><main class="main-area">
<div class="topbar"><div><h1>Akun Orang Tua / Ibu</h1><p>Satu akun dapat terhubung dengan beberapa anak.</p></div></div>
<?php if($message):?><div class="success-box"><?=htmlspecialchars($message)?></div><?php endif;?>
<?php if($error):?><div class="error-box"><?=htmlspecialchars($error)?></div><?php endif;?>
<div class="panel-box"><h2>Buat Akun Orang Tua</h2><p class="muted">Gunakan Ctrl + klik untuk memilih beberapa anak. Data lama tetap didukung.</p>
<form method="post" class="data-form"><input type="hidden" name="action" value="save">
<label>Nama Orang Tua<input name="name" required></label><label>No. Telepon<input name="phone"></label>
<label>Username<input name="username" required autocomplete="username"></label><label>Password<input type="password" name="password" minlength="8" required autocomplete="new-password"></label>
<label class="full">Anak yang dapat dilihat
<select name="child_ids[]" multiple size="6">
<?php foreach($children as $c):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?><?= $c['birth_date']?' · '.htmlspecialchars($c['birth_date']):'' ?></option><?php endforeach; ?></select></label>
<label class="full">Data Ibu yang dapat dilihat
<select name="mother_ids[]" multiple size="4">
<?php foreach($mothers as $m):?><option value="<?=$m['id']?>"><?=htmlspecialchars($m['name'])?><?= $m['nik']?' · NIK '.htmlspecialchars($m['nik']):'' ?></option><?php endforeach; ?></select></label>
<div class="full"><button class="btn btn-primary">Buat Akun</button></div></form></div>
<div class="panel-box"><h2>Daftar Akun Orang Tua</h2><div class="table-wrap"><table class="data-table"><tr><th>Nama</th><th>Username</th><th>Telepon</th><th>Anak</th><th>Ibu</th><th>Aksi</th></tr><?php foreach($rows as $r):?><tr><td><?=htmlspecialchars($r['name'])?></td><td><?=htmlspecialchars($r['username'])?></td><td><?=htmlspecialchars($r['phone']?:'-')?></td><td><?=((int)$r['child_count'])?> anak</td><td><?=((int)$r['mother_count'])?> data</td><td><form method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="small-btn danger" onclick="return confirm('Hapus akun orang tua ini?')">Hapus</button></form></td></tr><?php endforeach;?></table></div></div>
</main></div></body></html>
