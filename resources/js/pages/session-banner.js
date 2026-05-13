const STORAGE_KEY = 'myfitness_training';
const APP_URL = window.APP_URL ?? '';
const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.content;

function renderBanner(sessionId) {
    const existing = document.getElementById('session-banner');
    if (existing) return;

    const banner = document.createElement('div');
    banner.id = 'session-banner';
    banner.className = 'fixed bottom-0 left-0 right-0 z-50 p-4 flex justify-center';
    banner.innerHTML = `
        <div class="bg-[#003942] text-white rounded-2xl shadow-xl px-6 py-4 flex items-center gap-4 w-full max-w-lg">
            <span class="material-symbols-outlined text-2xl opacity-80">fitness_center</span>
            <div class="flex-1">
                <p class="font-bold text-sm">Entrenamiento en curso</p>
                <p class="text-white/60 text-xs">Tienes una sesión activa</p>
            </div>
            <a href="${APP_URL}/training/start"
                class="bg-white text-[#003942] px-4 py-2 rounded-xl font-bold text-sm active:scale-95 transition">
                Continuar
            </a>
            <button id="banner-delete"
                class="text-white/50 hover:text-white transition">
                <span class="material-symbols-outlined text-xl">delete</span>
            </button>
        </div>
    `;

    document.body.appendChild(banner);

    document.getElementById('banner-delete').onclick = async () => {
        if (!confirm('¿Cancelar el entrenamiento en curso? Se perderán los datos no guardados.')) return;

        try {
            await fetch(`${APP_URL}/training/session/${sessionId}/cancel`, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF()
                }
            });
        } catch (e) {}

        localStorage.removeItem(STORAGE_KEY);
        banner.remove();
    };
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('/training/start')) return;

    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return;

    try {
        const state = JSON.parse(raw);
        if (state.sessionId) {
            renderBanner(state.sessionId);
        }
    } catch (e) {
        localStorage.removeItem(STORAGE_KEY);
    }
});