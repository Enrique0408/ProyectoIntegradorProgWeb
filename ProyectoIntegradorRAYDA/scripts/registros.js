document.addEventListener('DOMContentLoaded', async () => {
  // Solo admin
  const s = await ensureAuth(['admin']);
  if (!s) return;

  const tabla = document.getElementById('tabla-registros');
  try {
    const res = await fetch('../../backend/obtener_registros.php');
    if (res.status === 403) { alert('Acceso denegado'); window.location.href = 'index.html'; return; }
    const data = await res.json();
    if (!Array.isArray(data)) { tabla.innerHTML = '<tr><td colspan="4">No hay registros</td></tr>'; return; }
    tabla.innerHTML = '';
    data.forEach(r => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${r.created_at}</td><td>${r.producto_nombre ?? r.id_producto}</td><td>${r.tipo_movimiento}</td><td>${r.cantidad}</td>`;
      tabla.appendChild(tr);
    });
  } catch (err) {
    console.error(err);
    tabla.innerHTML = '<tr><td colspan="4">Error al cargar registros</td></tr>';
  }
});