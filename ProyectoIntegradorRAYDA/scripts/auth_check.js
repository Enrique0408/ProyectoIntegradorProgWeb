// Small helper used by pages to ensure the user is logged in. If not, redirect to login.
async function ensureAuth(allowRoles = null) {
  try {
    const res = await fetch('../../backend/session.php');
    const data = await res.json();
    if (data.status !== 'ok') {
      window.location.href = 'login.html';
      return null;
    }
    // if allowRoles provided, check
    if (allowRoles && !allowRoles.includes(data.role)) {
      // not authorized for this page
      alert('No tienes permiso para acceder a esta página');
      window.location.href = 'index.html';
      return null;
    }
    return data; // {username, role}
  } catch (err) {
    console.error('Error checking session', err);
    window.location.href = 'login.html';
    return null;
  }
}

// Helper to show admin links in headers: pass the id of the container element
async function showAdminLinks(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  try {
    const res = await fetch('../../backend/session.php');
    const data = await res.json();
    if (data.status === 'ok' && data.role === 'admin') {
      container.innerHTML = `<a href="agregar_producto.html">➕ Agregar producto</a> <a href="registros.html">📜 Registros</a> <a id="logout-link" href="#">🚪 Salir</a>`;
      document.getElementById('logout-link').addEventListener('click', async (e) => {
        e.preventDefault();
        await fetch('../../backend/logout.php');
        window.location.href = 'login.html';
      });
    } else if (data.status === 'ok') {
      container.innerHTML = `<a id="logout-link" href="#">🚪 Salir</a>`;
      document.getElementById('logout-link').addEventListener('click', async (e) => {
        e.preventDefault();
        await fetch('../../backend/logout.php');
        window.location.href = 'login.html';
      });
    } else {
      container.innerHTML = `<a href="login.html">🔑 Iniciar sesión</a>`;
    }
  } catch (err) {
    console.error(err);
  }
}
