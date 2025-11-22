document.addEventListener('DOMContentLoaded', async () => {
  // Asegurar que sea admin
  const s = await ensureAuth(['admin']);
  if (!s) return; // ensureAuth ya redirige si no

  const btn = document.getElementById('btn-agregar');
  const msg = document.getElementById('msg');

  btn.addEventListener('click', async () => {
    msg.textContent = '';
    msg.style.color = '#c0392b'; // Establecer color de error por defecto

    // 1. Capturar todos los campos, incluyendo la imagen
    const nombre = document.getElementById('nombre').value.trim();
    const cantidad = Number(document.getElementById('cantidad').value) || 0;
    const lugar = document.getElementById('lugar').value.trim();
    const imagenFile = document.getElementById('imagen').files[0]; // Capturar el archivo

    // 2. Validaciones actualizadas
    if (!nombre || !lugar) {
      msg.textContent = 'Nombre y lugar son requeridos.';
      return;
    }
    if (!imagenFile) {
      msg.textContent = 'Debe seleccionar una imagen.';
      return;
    }
    // Validar que cantidad no sea negativa
    if (cantidad < 0) {
      msg.textContent = 'La cantidad no puede ser negativa.';
      return;
    }

    // 3. Crear objeto FormData para enviar datos y archivo
    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('cantidad', cantidad);
    formData.append('lugar', lugar);
    formData.append('imagen', imagenFile); // Añadir el archivo

    // Deshabilitar botón para evitar envíos múltiples
    btn.disabled = true;
    msg.textContent = 'Enviando...';

    try {
      const res = await fetch('../../backend/add_producto.php', {
        method: 'POST',
        // Usamos FormData, no especificamos Content-Type
        body: formData
      });

      const data = await res.json();

      if (data.status === 'ok') {
        msg.style.color = 'green';
        msg.textContent = 'Producto agregado ✅';
        // Limpiar campos después del éxito
        document.getElementById('nombre').value = '';
        document.getElementById('cantidad').value = '0';
        document.getElementById('lugar').value = '';
        document.getElementById('imagen').value = ''; // Limpiar el input file

        setTimeout(() => window.location.href = 'index.html', 900);
      } else {
        msg.textContent = data.message || 'Error desconocido';
      }
    } catch (err) {
      console.error(err);
      msg.textContent = 'Error de conexión con el servidor.';
    } finally {
      btn.disabled = false; // Habilitar el botón nuevamente
    }
  });
});