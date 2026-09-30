<?php
$active = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
  <div class="brand">♡ Posyandu Bina Warga</div>
  <nav>
    <a class="<?= $active==='dashboard.php'?'active':'' ?>" href="dashboard.php">Dashboard</a>
    <details class="nav-group" <?= in_array($active,['ibu-hamil.php','bayi-balita.php','orang-tua.php','jadwal.php'])?'open':'' ?>>
      <summary>Data Master</summary>
      <a class="<?= $active==='ibu-hamil.php'?'active':'' ?>" href="ibu-hamil.php">Ibu Hamil</a>
      <a class="<?= $active==='bayi-balita.php'?'active':'' ?>" href="bayi-balita.php">Bayi/Balita</a>
      <a class="<?= $active==='orang-tua.php'?'active':'' ?>" href="orang-tua.php">Akun Orang Tua</a>
      <a class="<?= $active==='jadwal.php'?'active':'' ?>" href="jadwal.php">Jadwal Posyandu</a>
    </details>
    <details class="nav-group" <?= in_array($active,['pelayanan-balita.php','imunisasi.php','vitamin.php','kms.php'])?'open':'' ?>>
      <summary>Pelayanan Balita</summary>
      <a class="<?= $active==='pelayanan-balita.php'?'active':'' ?>" href="pelayanan-balita.php">Penimbangan & Pengukuran</a>
      <a class="<?= $active==='imunisasi.php'?'active':'' ?>" href="imunisasi.php">Imunisasi</a>
      <a class="<?= $active==='vitamin.php'?'active':'' ?>" href="vitamin.php">Vitamin</a>
      <a class="<?= $active==='kms.php'?'active':'' ?>" href="kms.php">KMS Digital</a>
    </details>
    <details class="nav-group" <?= in_array($active,['anc.php','vitamin-ibu.php'])?'open':'' ?>>
      <summary>Pelayanan Ibu Hamil</summary>
      <a class="<?= $active==='anc.php'?'active':'' ?>" href="anc.php">Pemeriksaan ANC</a>
      <a class="<?= $active==='vitamin-ibu.php'?'active':'' ?>" href="vitamin-ibu.php">Vitamin / Suplemen</a>
    </details>
    <a class="<?= $active==='appointment.php'?'active':'' ?>" href="appointment.php">Janji Temu</a>
    <a class="<?= $active==='laporan.php'?'active':'' ?>" href="laporan.php">Laporan & Rekapitulasi</a>
    <a href="../index.php">Website Publik</a>
    <a href="logout.php">Logout</a>
  </nav>
</aside>