<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$children=$pdo->query('SELECT id,name,birth_date,gender,mother_id FROM bayi_balita ORDER BY name')->fetchAll();
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
        $motherId=(int)($_POST['mother_id']??0);

        if(!$name || !$username || strlen($password)<8){
            $error='Nama, username, dan password minimal 8 karakter wajib diisi.';
        } elseif(!$motherId){
            $error='Pilih data Ibu terlebih dahulu. Semua anak yang terhubung dengan Ibu tersebut akan otomatis masuk ke akun ini.';
        } else {
            try{
                $pdo->beginTransaction();

                $s=$pdo->prepare('INSERT INTO parent_users(username,password_hash,name,phone,mother_id,child_id) VALUES(?,?,?,?,?,?)');
                // child_id lama dipertahankan untuk kompatibilitas; anak-anak aktif diambil melalui parent_mothers.
                $s->execute([$username,password_hash($password,PASSWORD_DEFAULT),$name,$phone,$motherId,null]);
                $parentId=(int)$pdo->lastInsertId();

                $s=$pdo->prepare('INSERT INTO parent_mothers(parent_id,mother_id) VALUES(?,?)');
                $s->execute([$parentId,$motherId]);

                // Sinkronisasi awal: seluruh anak milik ibu langsung masuk ke akun.
                $s=$pdo->prepare('INSERT IGNORE INTO parent_children(parent_id,child_id) SELECT ?, id FROM bayi_balita WHERE mother_id=?');
                $s->execute([$parentId,$motherId]);

                $pdo->commit();
                $message='Akun berhasil dibuat. Semua anak dari Ibu tersebut otomatis terhubung, termasuk anak yang ditambahkan nanti.';
            }catch(PDOException $e){
                if($pdo->inTransaction()) $pdo->rollBack();
                $error='Username sudah digunakan atau data tidak dapat disimpan.';
            }
        }
    }
}
if(isset($_GET['msg']) && $_GET['msg']==='deleted') $message='Akun orang tua berhasil dihapus.';

$rows=$pdo->query("SELECT p.*, 
    (SELECT COUNT(DISTINCT b.id) FROM bayi_balita b WHERE b.mother_id IN (SELECT pm.mother_id FROM parent_mothers pm WHERE pm.parent_id=p.id)) +
    (SELECT COUNT(DISTINCT pc.child_id) FROM parent_children pc WHERE pc.parent_id=p.id AND pc.child_id NOT IN (SELECT b2.id FROM bayi_balita b2 WHERE b2.mother_id IN (SELECT pm2.mother_id FROM parent_mothers pm2 WHERE pm2.parent_id=p.id))) AS child_count,
    (SELECT COUNT(*) FROM parent_mothers pm WHERE pm.parent_id=p.id) AS mother_count
    FROM parent_users p ORDER BY p.id DESC")->fetchAll();

// Daftar anak per akun untuk ditampilkan sebagai ringkasan.
foreach($rows as &$row){
    $s=$pdo->prepare("SELECT DISTINCT b.name FROM bayi_balita b WHERE b.mother_id IN (SELECT pm.mother_id FROM parent_mothers pm WHERE pm.parent_id=?) ORDER BY b.name");
    $s->execute([(int)$row['id']]);
    $autoChildren=$s->fetchAll(PDO::FETCH_COLUMN);
    $s=$pdo->prepare("SELECT DISTINCT b.name FROM bayi_balita b INNER JOIN parent_children pc ON pc.child_id=b.id WHERE pc.parent_id=? ORDER BY b.name");
    $s->execute([(int)$row['id']]);
    $manualChildren=$s->fetchAll(PDO::FETCH_COLUMN);
    $row['children_names']=array_values(array_unique(array_merge($autoChildren,$manualChildren)));
}
unset($row);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Akun Orang Tua</title>
<link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css">
<style>
.parent-account-help{margin:-6px 0 18px;color:#6b6170;line-height:1.6}
.account-link-preview{margin-top:4px;padding:14px 16px;border:1px solid #eadde2;border-radius:12px;background:#fff8fa}
.account-link-preview strong{display:block;margin-bottom:5px}.account-link-preview span{display:inline-block;margin:3px 6px 3px 0;padding:5px 9px;border-radius:999px;background:#f1e7eb;font-size:13px}
</style>
</head>
<body>
<div class="dashboard">
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="main-area">
<div class="topbar"><div><h1>Akun Orang Tua / Ibu</h1><p>Hubungkan akun ke data Ibu. Anak akan mengikuti hubungan Ibu secara otomatis.</p></div></div>
<?php if($message):?><div class="success-box"><?=htmlspecialchars($message)?></div><?php endif;?>
<?php if($error):?><div class="error-box"><?=htmlspecialchars($error)?></div><?php endif;?>

<div class="panel-box">
<h2>Buat Akun Orang Tua</h2>
<p class="parent-account-help"><strong>Lebih mudah:</strong> cukup pilih satu data Ibu. Semua anak yang sudah terhubung dengan Ibu tersebut otomatis dapat dilihat oleh akun ini. Jika nanti ada anak baru dengan Ibu yang sama, anak tersebut juga akan otomatis ikut.</p>
<form method="post" class="data-form" id="parentAccountForm">
<input type="hidden" name="action" value="save">
<label>Nama Orang Tua<input name="name" required placeholder="Contoh: Ria"></label>
<label>No. Telepon<input name="phone" placeholder="08xxxxxxxxxx"></label>
<label>Username<input name="username" required autocomplete="username" placeholder="ria"></label>
<label>Password<input type="password" name="password" minlength="8" required autocomplete="new-password" placeholder="Minimal 8 karakter"></label>
<label class="full">Hubungkan dengan Data Ibu
<select name="mother_id" id="motherSelect" required>
<option value="">-- Pilih Ibu --</option>
<?php foreach($mothers as $m):?><option value="<?=$m['id']?>" data-name="<?=htmlspecialchars($m['name'],ENT_QUOTES)?>"><?=htmlspecialchars($m['name'])?><?= $m['nik']?' · NIK '.htmlspecialchars($m['nik']):'' ?></option><?php endforeach; ?>
</select>
</label>
<div class="full account-link-preview" id="childPreview"><strong>👶 Anak yang akan otomatis terhubung</strong><span>Pilih data Ibu terlebih dahulu.</span></div>
<div class="full"><button class="btn btn-primary">Buat Akun</button></div>
</form>
</div>

<div class="panel-box">
<h2>Daftar Akun Orang Tua</h2>
<p class="muted">Tidak perlu memilih anak satu per satu. Hubungan anak mengikuti data Ibu yang terhubung.</p>
<div class="table-wrap"><table class="data-table"><tr><th>Nama</th><th>Username</th><th>Telepon</th><th>Ibu</th><th>Anak</th><th>Aksi</th></tr>
<?php foreach($rows as $r):?>
<tr>
<td><?=htmlspecialchars($r['name'])?></td><td><?=htmlspecialchars($r['username'])?></td><td><?=htmlspecialchars($r['phone']?:'-')?></td>
<td><?=((int)$r['mother_count'])?> data</td>
<td><?=count($r['children_names'])?> anak<?php if($r['children_names']): ?><br><small><?=htmlspecialchars(implode(', ',$r['children_names']))?></small><?php endif; ?></td>
<td><form method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="small-btn danger" onclick="return confirm('Hapus akun orang tua ini?')">Hapus</button></form></td>
</tr>
<?php endforeach;?></table></div>
</div>
</main></div>
<script>
const motherData = <?=json_encode(array_map(function($m) use ($children){
    $ids=[];
    foreach($children as $c){ if((int)$c['mother_id']===(int)$m['id']) $ids[]=$c['name']; }
    return ['id'=>(int)$m['id'],'children'=>$ids];
},$mothers),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const motherSelect=document.getElementById('motherSelect');
const childPreview=document.getElementById('childPreview');
function updateChildPreview(){
    const id=Number(motherSelect.value);
    const item=motherData.find(x=>x.id===id);
    if(!item){childPreview.innerHTML='<strong>👶 Anak yang akan otomatis terhubung</strong><span>Pilih data Ibu terlebih dahulu.</span>';return;}
    if(!item.children.length){childPreview.innerHTML='<strong>👶 Anak yang akan otomatis terhubung</strong><span>Belum ada anak. Saat anak baru dibuat dengan Ibu ini, anak akan otomatis masuk ke akun.</span>';return;}
    childPreview.innerHTML='<strong>👶 Anak yang akan otomatis terhubung</strong>'+item.children.map(n=>'<span>✓ '+escapeHtml(n)+'</span>').join('');
}
function escapeHtml(value){return String(value).replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));}
motherSelect.addEventListener('change',updateChildPreview);
</script>
</body></html>
