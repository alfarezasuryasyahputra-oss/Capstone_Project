const menuBtn = document.getElementById('menuBtn');
const navLinks = document.getElementById('navLinks');

if (menuBtn && navLinks) {
  menuBtn.addEventListener('click', () => navLinks.classList.toggle('open'));
  navLinks.querySelectorAll('a').forEach(a =>
    a.addEventListener('click', () => navLinks.classList.remove('open'))
  );
}

const appointmentForm = document.getElementById('appointmentForm');

if (appointmentForm) {
  appointmentForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const button = appointmentForm.querySelector('button[type="submit"]');
    const oldText = button.textContent;
    button.disabled = true;
    button.textContent = 'Mengirim...';

    try {
      const response = await fetch(appointmentForm.action, {
        method: 'POST',
        body: new FormData(appointmentForm),
        headers: {'X-Requested-With': 'XMLHttpRequest'}
      });
      const data = await response.json();

      if (data.success) {
        alert('Permintaan janji temu sudah terkirim!');
        appointmentForm.reset();
      } else {
        alert(data.message || 'Permintaan belum dapat dikirim.');
      }
    } catch (error) {
      alert('Terjadi masalah koneksi. Pastikan Apache dan MySQL aktif.');
    } finally {
      button.disabled = false;
      button.textContent = oldText;
    }
  });
}
