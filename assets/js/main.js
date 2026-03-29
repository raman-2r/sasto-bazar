// assets/js/main.js
const API_BASE = '../backend/';

// Utility: Show Flash Messages
function showFlash(message, type = 'success') {
    let msgDiv = document.getElementById('flash-msg');
    if (!msgDiv) {
        msgDiv = document.createElement('div');
        msgDiv.id = 'flash-msg';
        document.body.appendChild(msgDiv);
    }
    msgDiv.className = `show ${type}`;
    msgDiv.textContent = message;
    
    setTimeout(() => {
        msgDiv.className = '';
    }, 3000);
}

// Utility: General Fetch wrapper
async function apiCall(endpoint, options = {}) {
    options.credentials = 'include'; // Important for PHP Sessions
    try {
        const res = await fetch(`${API_BASE}${endpoint}`, options);
        if(!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return await res.json();
    } catch (e) {
        console.error("API Call Failed:", e);
        return { success: false, message: 'Network error or bad JSON.' };
    }
}

// Check session across all pages to update navbar
async function checkSession() {
    const res = await apiCall('auth.php?action=check');
    const authLinksDiv = document.getElementById('auth-links');
    
    if (authLinksDiv) {
        if (res && res.loggedIn) {
            let links = `
                <a href="sell.html" class="btn btn-outline" style="padding: 5px 15px; margin-right: 15px;">List Item</a>
                <a href="profile.html">My Profile</a>
            `;
            if (res.user.role === 'admin') {
                links += `<a href="../backend/admin/dashboard.php" style="margin-left:15px; color: var(--primary);">Admin</a>`;
            }
            links += `<a href="#" id="logout-btn" style="margin-left: 15px; color: red;">Logout</a>`;
            authLinksDiv.innerHTML = links;
            
            document.getElementById('logout-btn').addEventListener('click', async (e) => {
                e.preventDefault();
                await apiCall('auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                window.location.href = 'index.html';
            });
        } else {
            authLinksDiv.innerHTML = `
                <a href="login.html">Login</a>
                <a href="register.html" class="btn btn-primary" style="margin-left: 15px;">Register</a>
            `;
        }
    }
    return res;
}

// Initialize global things
document.addEventListener('DOMContentLoaded', () => {
    checkSession();
});
