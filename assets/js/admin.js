// assets/js/admin.js
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('admin-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                const res = await fetch('../../backend/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                const data = await res.json();
                if (data.success) {
                    window.location.href = '../../frontend/login.html';
                }
            } catch (err) {
                console.error(err);
            }
        });
    }
});
