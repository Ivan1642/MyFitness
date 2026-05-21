const APP_URL = window.APP_URL;
const CSRF = window.CSRF;
const AUTH_USER_ID = window.AUTH_USER_ID;

let currentType = 'para_ti';
let currentPage = 1;
let loading = false;
let hasMore = true;

const container = document.getElementById('feed-container');
const loadingEl = document.getElementById('feed-loading');
const endEl = document.getElementById('feed-end');
const btnParaTi = document.getElementById('btn-para-ti');
const btnSeguidos = document.getElementById('btn-seguidos');
const searchInput = document.getElementById('search-input');
const searchResults = document.getElementById('search-results');

function setActiveTab(type) {
    currentType = type;
    currentPage = 1;
    hasMore = true;
    container.innerHTML = '';
    endEl.classList.add('hidden');

    if (type === 'para_ti') {
        btnParaTi.classList.add('active-tab');
        btnSeguidos.classList.remove('active-tab');
    } else {
        btnSeguidos.classList.add('active-tab');
        btnParaTi.classList.remove('active-tab');
    }

    loadItems();
}

async function loadItems() {
    if (loading || !hasMore) return;
    loading = true;
    loadingEl.classList.remove('hidden');

    try {
        const res = await fetch(`${APP_URL}/feed/load?type=${currentType}&page=${currentPage}`);
        const data = await res.json();

        data.items.forEach(item => container.appendChild(renderItem(item)));

        hasMore = data.hasMore;
        currentPage++;

        if (!hasMore) endEl.classList.remove('hidden');
    } catch (e) {
        console.error(e);
    }

    loadingEl.classList.add('hidden');
    loading = false;
}

function renderItem(item) {
    const div = document.createElement('div');
    div.className = 'bg-white rounded-2xl shadow overflow-hidden';

    const typeLabel = {
        session:     { icon: 'fitness_center', label: 'Entrenamiento' },
        post:        { icon: 'photo_camera',   label: 'Publicación' },
        achievement: { icon: 'emoji_events',   label: 'Logro' },
        record:      { icon: 'monitoring',     label: 'Récord personal' },
    }[item.type];

    const date = new Date(item.created_at).toLocaleDateString('es-ES', {
        day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });

    div.innerHTML = `
        <div class="p-4">
            <div class="flex items-center gap-3 mb-3">
                <a href="${APP_URL}/profile/${item.user_id}">
                    <img src="${item.user_avatar}" class="h-10 w-10 rounded-full object-cover">
                </a>
                <div class="flex-1">
                    <a href="${APP_URL}/profile/${item.user_id}" class="font-semibold text-[#003942] hover:underline">${item.user_name}</a>
                    <p class="text-xs text-gray-400">${item.user_username ? '@' + item.user_username : ''} · ${date}</p>
                </div>
                <div class="flex items-center gap-1 text-xs text-gray-400">
                    <span class="material-symbols-outlined text-sm">${typeLabel.icon}</span>
                    <span>${typeLabel.label}</span>
                </div>
            </div>

            ${item.content ? `<p class="text-gray-700 text-sm mb-3">${item.content}</p>` : ''}
            ${item.duration ? `<p class="text-xs text-gray-400 mb-3"><span class="material-symbols-outlined text-sm align-middle">timer</span> ${item.duration} min</p>` : ''}
            ${item.image ? `<img src="${item.image}" class="w-full rounded-xl object-cover max-h-80 mb-3">` : ''}

            ${item.type === 'session' && item.exercises && item.exercises.length > 0 ? `
                <div class="space-y-1 mb-3">
                    ${item.exercises.map(ex => `
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-[#003942]">${ex.name}</span>
                            <span class="text-gray-400">${ex.sets_count} series</span>
                        </div>
                    `).join('')}
                </div>
                <a href="${APP_URL}/training/session/${item.id}"
                    class="inline-block text-xs text-[#003942] hover:underline font-semibold mb-3">
                    Ver entrenamiento completo →
                </a>
            ` : ''}

            <div class="flex items-center gap-2 pt-2 border-t">
                <button class="like-btn flex items-center gap-1 text-sm font-semibold transition"
                    data-id="${item.id}"
                    data-type="${item.likeable_type}"
                    data-liked="${item.liked}">
                    <span class="material-symbols-outlined text-xl ${item.liked ? 'text-red-500' : 'text-gray-400'}">
                        ${item.liked ? 'favorite' : 'favorite_border'}
                    </span>
                    <span class="like-count ${item.liked ? 'text-red-500' : 'text-gray-400'}">${item.likes_count}</span>
                </button>

                ${(item.type === 'post' || item.type === 'session') && item.user_id === AUTH_USER_ID ? `
                    <button class="delete-btn flex items-center gap-1 text-sm text-gray-400 hover:text-red-500 transition ml-auto"
                        data-id="${item.id}"
                        data-type="${item.type}">
                        <span class="material-symbols-outlined text-xl">delete</span>
                    </button>
                ` : ''}
            </div>
        </div>
    `;

    div.querySelector('.like-btn').onclick = async (e) => {
        const btn = e.currentTarget;
        const res = await fetch(`${APP_URL}/feed/like`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ likeable_id: btn.dataset.id, likeable_type: btn.dataset.type })
        });
        const data = await res.json();
        const icon = btn.querySelector('.material-symbols-outlined');
        const count = btn.querySelector('.like-count');
        btn.dataset.liked = data.liked;
        icon.textContent = data.liked ? 'favorite' : 'favorite_border';
        icon.className = `material-symbols-outlined text-xl ${data.liked ? 'text-red-500' : 'text-gray-400'}`;
        count.textContent = data.count;
        count.className = `like-count ${data.liked ? 'text-red-500' : 'text-gray-400'}`;
    };

    const deleteBtn = div.querySelector('.delete-btn');
    if (deleteBtn) {
        deleteBtn.onclick = async () => {
            if (!confirm('¿Eliminar esta publicación?')) return;
            const type = deleteBtn.dataset.type;
            const url = type === 'post'
                ? `${APP_URL}/posts/${deleteBtn.dataset.id}`
                : `${APP_URL}/training/session/${deleteBtn.dataset.id}`;

            const res = await fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF }
            });
            if (res.ok) div.remove();
        };
    }

    return div;
}

let searchTimeout = null;
searchInput.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    const q = searchInput.value.trim();

    if (q.length < 2) {
        searchResults.classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(async () => {
        const res = await fetch(`${APP_URL}/feed/search?q=${encodeURIComponent(q)}`);
        const users = await res.json();

        if (users.length === 0) {
            searchResults.classList.add('hidden');
            return;
        }

        searchResults.innerHTML = users.map(u => `
            <a href="${APP_URL}/profile/${u.id}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer border-b last:border-0">
                <img src="${u.avatar}" class="h-8 w-8 rounded-full object-cover">
                <div>
                    <p class="font-semibold text-sm text-[#003942]">${u.name}</p>
                    <p class="text-xs text-gray-400">${u.username ? '@' + u.username : ''}</p>
                </div>
            </a>
        `).join('');

        searchResults.classList.remove('hidden');
    }, 300);
});

document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.classList.add('hidden');
    }
});

btnParaTi.addEventListener('click', () => setActiveTab('para_ti'));
btnSeguidos.addEventListener('click', () => setActiveTab('seguidos'));

window.addEventListener('scroll', () => {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
        loadItems();
    }
});

setActiveTab('para_ti');