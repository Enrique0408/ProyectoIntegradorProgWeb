document.addEventListener('DOMContentLoaded', async () => {
  // Asegurar que sea admin
  const s = await ensureAuth(['admin']);
  if (!s) return; // ensureAuth ya redirige si no

  const btn = document.getElementById('btn-agregar');
  const msg = document.getElementById('msg');

  btn.addEventListener('click', async () => {
    msg.textContent = '';
    const nombre = document.getElementById('nombre').value.trim();
    const cantidad = Number(document.getElementById('cantidad').value) || 0;
    const lugar = document.getElementById('lugar').value.trim();
    const imagen = document.getElementById('imagen').value.trim();

    if (!nombre) { msg.textContent = 'Nombre requerido'; return; }

    try {
      const res = await fetch('../../backend/add_producto.php', {
        method: 'POST', headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ nombre, cantidad, lugar, imagen })
      });
      const data = await res.json();
      if (data.status === 'ok') {
        msg.style.color = 'green'; msg.textContent = 'Producto agregado ✅';
        setTimeout(()=> window.location.href = 'index.html', 900);
      } else {
        msg.style.color = '#c0392b'; msg.textContent = data.message || 'Error';
      }
    } catch (err) {
      msg.style.color = '#c0392b'; msg.textContent = 'Error de conexión';
    }
  });
});