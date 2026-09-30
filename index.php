<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Website klinik modern dan hangat untuk perawatan ibu hamil dan bayi." />
  <title>Posyandu Bina Warga</title>
  
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <div class="container">
    <nav>
      <a class="logo" href="#home"><span class="logo-mark">♡</span> Posyandu Bina Warga</a>
      <div class="nav-links" id="navLinks">
        <a href="#services">Layanan</a>
        <a href="#care">Ibu Hamil & Balita</a>
        <a href="#tips">Tips</a>
        <a href="#contact">Contact</a>
        <a class="staff-link" href="staff/login.php">Admin/Kader</a><a class="staff-link" href="parent/login.php">Orang Tua</a>
      </div>
      <a class="btn btn-primary nav-cta" href="#appointment">Booking untuk Kunjungan</a>
      <button class="menu" id="menuBtn" aria-label="Open menu">☰</button>
    </nav>
  </div>
</header>

<main id="home">
  <section class="hero">
    <div class="container hero-grid">
      <div>
        <div class="eyebrow">Rawatlah setiap permulaan kecil</div>
        <h1>Perawatan dan Pengecekan untuk <span>Ibu Hamil & balita.</span></h1>
        <p>
          Klinik yang ramah untuk perawatan kehamilan, bayi baru lahir, dan keluarga. Dirancang agar
          setiap kunjungan terasa mudah, nyaman, dan didukung sepenuhnya..
        </p>
        <div class="actions">
          <a class="btn btn-primary" href="#appointment">Buat daftar kunjungan →</a>
          <a class="btn btn-light" href="#services">Layanan Kami</a>
        </div>
      </div>
    </div>
  </section>

  <section id="services">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Kami Peduli</div>
          <h2>Perawatan sederhana, semuanya di satu tempat.</h2>
        </div>
        <p>tim kami yang penuh perhatian siap mendukung keluarga Anda di setiap tahapan.</p>
      </div>
      <div class="cards">
        <article class="card">
          <div class="icon">🤰</div>
          <h3>Perawatan Kehamilan</h3>
          <p>Kunjungan pemeriksaan kehamilan rutin, panduan umum, pemantauan, dan dukungan sepanjang perjalanan kehamilan.</p>
        </article>
        <article class="card">
          <div class="icon">👶</div>
          <h3>Perawatan Bayi & Bayi Baru Lahir</h3>
          <p>Perawatan bayi dan bayi baru lahir, pemantauan pertumbuhan, dan dukungan praktis untuk orang tua baru.</p>
        </article>
        <article class="card">
          <div class="icon">🩺</div>
          <h3>Konsultasi Kesehatan</h3>
          <p>Tempat yang nyaman untuk membahas pertanyaan kesehatan dan memahami langkah-langkah berikutnya dengan seorang klinisi.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="split" id="care">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Mendukung perjalanan Anda</div>
        </div>
      </div>
      <div class="split-grid">
        <div class="panel mom">
          <div class="icon">🌷</div>
          <h3>untuk ibu hamil</h3>
          <ul>
            <li><span>✓</span> Dukungan pemeriksaan kehamilan</li>
            <li><span>✓</span> Konsultasi kesehatan umum</li>
            <li><span>✓</span> Edukasi & panduan kehamilan</li>
            <li><span>✓</span> Pemantauan pasca melahirkan</li>
          </ul>
        </div>
        <div class="panel baby">
          <div class="icon">🧸</div>
          <h3>untuk bayi & bayi baru lahir</h3>
          <ul>
            <li><span>✓</span> Panduan perawatan bayi baru lahir</li>
            <li><span>✓</span> Pemantauan pertumbuhan & perkembangan</li>
            <li><span>✓</span> Konsultasi kesehatan umum untuk bayi</li>
            <li><span>✓</span> Edukasi & dukungan bagi orang tua</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="appointment" id="appointment">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">daftar kunjungan</div>
          <h2>Ajukan janji temu.</h2>
        </div>
      </div>
      <div class="appointment-box">
        <form id="appointmentForm" action="actions/appointment.php" method="POST">
          <div class="field">
            <label for="name">Nama Lengkap</label>
            <input id="name" name="name" type="text" placeholder="Nama Anda" required>
          </div>
          <div class="field">
            <label for="phone">Nomor Telepon</label>
            <input id="phone" name="phone" type="tel" placeholder="+62..." required>
          </div>
          <div class="field">
            <label for="service">Keperluan</label>
            <select id="service" name="service">
              <option>Dukungan kehamilan</option>
              <option>Perawatan bayi / bayi baru lahir</option>
              <option>Konsultasi kesehatan umum</option>
              <option>Lainnya</option>
            </select>
          </div>
          <div class="field">
            <label for="date">Tanggal yang diinginkan</label>
            <input id="date" name="date" type="date">
          </div>
          <div class="field">
            <label for="time">Jam pertemuan</label>
            <input id="time" name="time" type="time">
          </div>
          <div class="field full">
            <label for="message">Catatan tambahan</label>
            <textarea id="message" name="message" placeholder="Beritahu kami hal-hal yang perlu diketahui..."></textarea>
          </div>
          <div class="field full">
            <button class="btn btn-primary" type="submit">Kirim Permintaan Janji Temu →</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <section class="tips" id="tips">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Sumber bermanfaat</div>
          <h2>Peringatan kecil untuk orang tua.</h2>
        </div>
      </div>
      <div class="tip-list">
        <article class="tip"><small>01 · Kehamilan</small><h3>Simpan janji temu Anda</h3><p>Periksa rutin membantu tim perawatan Anda memantau kehamilan dan menjawab pertanyaan seiring munculnya.</p></article>
        <article class="tip"><small>02 · Orang tua baru</small><h3>Tanyakan pertanyaan</h3><p>Tuliskan kekhawatiran sebelum berkunjung agar Anda tidak lupa.</p></article>
        <article class="tip"><small>03 · Bayi</small><h3>Ketahui kapan harus mencari bantuan</h3><p>Jika Anda khawatir akan gejala mendadak, hubungi penyedia layanan kesehatan Anda atau layanan darurat setempat secepatnya.</p></article>
      </div>
    </div>
  </section>

  <section id="contact">
    <div class="container contact-grid">
      <div>
        <div class="eyebrow">Kunjungi kami</div>
        <h2>Kami di sini untuk membantu Anda merasa didukung.</h2>
      </div>
      <div class="contact-info">
        <div class="contact-item"><strong>📍 Alamat</strong><span>Alamat klinik </span></div>
        <div class="contact-item"><strong>📞 No Telp</strong><span>+62 000 0000 0000</span></div>
        <div class="contact-item"><strong>💬 WhatsApp</strong><span>+62 000 0000 0000</span></div>
        <div class="contact-item"><strong>🕐 Jam Buka</strong><span>Senin-Sabtu · 08:00–17:00</span></div>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="container">
    <div class="footer-row">
      <strong>♡ Posyandu</strong>
    </div>
    <p class="notice">Kami siap membantu Anda dan keluarga Anda.</p>
  </div>
</footer>


  <script src="js/main.js"></script>
</body>
</html>
