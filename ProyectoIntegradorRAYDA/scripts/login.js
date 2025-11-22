document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btn-login');
  const btnDemo = document.getElementById('btn-demo');
  const msg = document.getElementById('login-msg');

  btn.addEventListener('click', async () => {
    msg.style.display = 'none';
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    if (!username || !password) {
      msg.textContent = 'Completa usuario y contraseña'; msg.style.display = 'block'; return;
    }

    try {
      const res = await fetch('../../backend/login.php', {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ username, password })
      });
      const data = await res.json();
      if (data.status === 'ok') {
        // redirigir al catálogo
        window.location.href = 'index.html';
      } else {
        msg.textContent = data.message || 'Error en login'; msg.style.display = 'block';
      }
    } catch (err) {
      msg.textContent = 'Error en la conexión'; msg.style.display = 'block';
    }
  });

  btnDemo.addEventListener('click', async () => {
    // Login rápido con credenciales sembradas
    document.getElementById('username').value = 'admin';
    document.getElementById('password').value = 'admin123';
    btn.click();
  });
});
