<?php
session_start(); require_once __DIR__.'/../config/database.php';
if(isset($_SESSION['parent_id'])){header('Location: dashboard.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $u=trim($_POST['username']??'');$p=$_POST['password']??'';
 $s=$pdo->prepare('SELECT * FROM parent_users WHERE username=? LIMIT 1');$s->execute([$u]);$row=$s->fetch();
 if($row && password_verify($p,$row['password_hash'])){session_regenerate_id(true);$_SESSION['parent_id']=$row['id'];$_SESSION['parent_name']=$row['name'];$_SESSION['parent_child_id']=$row['child_id'];$_SESSION['parent_mother_id']=$row['mother_id'];header('Location: dashboard.php');exit;}
 $error='Username atau password salah.';
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login Orang Tua</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/staff.css"></head><body class="staff-body"><div class="login-card"><h1>Dashboard Orang Tua</h1><p>Lihat perkembangan kesehatan anak dan informasi Posyandu.</p><?php if($error):?><div class="error-box"><?=htmlspecialchars($error)?></div><?php endif;?><form method="post" class="staff-form"><label>Username<input name="username" required></label><label>Password<input type="password" name="password" required></label><button class="btn btn-primary">Masuk</button></form><a class="back-link" href="../index.php">← Website Posyandu</a></div></body></html>