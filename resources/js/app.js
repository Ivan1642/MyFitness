import './pages/session-banner.js';

document.addEventListener('DOMContentLoaded', async () => {
    const badge = document.getElementById('notif-count');
    if (!badge) return;

    try {
        const res = await fetch(window.APP_URL + '/notifications/unread');
        const data = await res.json();
        if (data.count > 0) {
            badge.textContent = data.count > 9 ? '9+' : data.count;
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        }
    } catch (e) {}
});