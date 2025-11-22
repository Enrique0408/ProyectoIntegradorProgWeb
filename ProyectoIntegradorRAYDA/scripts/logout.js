document.addEventListener('DOMContentLoaded', () => {
  async function doLogout() {
    try {
      await fetch('../../backend/logout.php');
    } catch(e){}
    window.location.href = 'login.html';
  }

  // attach to any logout links
  document.querySelectorAll('#link-logout, #link-logout-2, #logout-link').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault(); doLogout();
    });
  });
});