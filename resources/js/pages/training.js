const CSRF = () => document.querySelector('meta[name="csrf-token"]').content;
const STORAGE_KEY = 'myfitness_training';

let sessionId = null;
let sessionStart = null;
let exercisesDB = [];
let exerciseBlocks = [];

function saveState() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify({
        sessionId,
        sessionStart,
        exerciseBlocks
    }));
}

function loadState() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return;
    const state = JSON.parse(raw);
    sessionId = state.sessionId;
    sessionStart = state.sessionStart;
    exerciseBlocks = state.exerciseBlocks;
}

function clearState() {
    localStorage.removeItem(STORAGE_KEY);
}

window.addEventListener("DOMContentLoaded", async () => {

    const el = document.getElementById("training-app");
    if (!el) return;

    if (window.PRELOADED_SESSION_ID && window.PRELOADED_ROUTINE) {
        clearState();
        sessionId = window.PRELOADED_SESSION_ID;
        sessionStart = Date.now();
        exerciseBlocks = window.PRELOADED_ROUTINE;
        saveState();
    } else {
        loadState();
    }

    try {
        const res = await fetch(`${window.APP_URL}/api/exercises`);
        exercisesDB = await res.json();
    } catch (e) {
        exercisesDB = [];
    }

    render();

    function render() {
        el.innerHTML = `
            <div class="space-y-4 pb-24">
                ${!sessionId ? renderStartBanner() : ''}
                ${sessionId ? renderExerciseBlocks() : ''}
                ${sessionId ? renderAddExercise() : ''}
                ${sessionId ? renderFinishButton() : ''}
            </div>
        `;
        bindEvents();
    }

    function renderStartBanner() {
        return `
            <div class="bg-[#003942] text-white p-6 rounded-2xl shadow-lg text-center">
                <span class="material-symbols-outlined text-4xl mb-2 block opacity-80">fitness_center</span>
                <h2 class="text-xl font-bold mb-1">¿Listo para entrenar?</h2>
                <p class="text-white/60 text-sm mb-6">Pulsa el botón para comenzar a registrar tu entrenamiento</p>
                <button id="startSession"
                    class="bg-white text-[#003942] px-8 py-4 rounded-xl font-bold text-lg w-full active:scale-95 transition">
                    Comenzar entrenamiento
                </button>
            </div>
        `;
    }

    function renderExerciseBlocks() {
        if (exerciseBlocks.length === 0) return `
            <div class="text-center text-gray-400 py-10">
                <span class="material-symbols-outlined text-5xl mb-3 block">add_circle</span>
                <p class="font-medium">Añade tu primer ejercicio</p>
            </div>
        `;

        return exerciseBlocks.map((block, bi) => `
            <div class="bg-white rounded-2xl shadow overflow-hidden">

                <div class="bg-[#003942] px-4 py-3 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        ${block.image
                            ? `<img src="${block.image}" alt="${block.name}" class="h-10 w-10 rounded-lg object-cover bg-white/10">`
                            : `<span class="material-symbols-outlined text-white/60">fitness_center</span>`
                        }
                        <div>
                            <h3 class="font-bold text-white text-base">${block.name}</h3>
                            <p class="text-white/50 text-xs">${block.muscle_group}</p>
                        </div>
                    </div>
                    <button data-remove-block="${bi}"
                        class="text-white/50 hover:text-white text-sm transition">
                        Eliminar
                    </button>
                </div>

                <div class="p-4 space-y-3">

                    <div class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-semibold uppercase px-1 mb-1">
                        <span class="col-span-2 text-center">Serie</span>
                        <span class="col-span-4 text-center">Reps</span>
                        <span class="col-span-4 text-center">Kg</span>
                        <span class="col-span-2"></span>
                    </div>

                    ${block.sets.map((set, si) => `
                        <div class="grid grid-cols-12 gap-2 items-center">

                            <span class="col-span-2 text-center font-bold text-[#003942] text-lg">
                                ${si + 1}
                            </span>

                            <input type="number"
                                inputmode="numeric"
                                placeholder="—"
                                value="${set.reps}"
                                min="1"
                                class="col-span-4 border-2 ${set.saved ? 'border-green-300 bg-green-50 text-green-700' : 'border-gray-200 focus:border-[#003942]'} rounded-xl p-3 text-center text-lg font-bold focus:outline-none transition"
                                data-bi="${bi}" data-si="${si}" data-f="reps"
                                ${set.saved ? 'readonly' : ''}>

                            <input type="number"
                                inputmode="decimal"
                                placeholder="—"
                                value="${set.weight}"
                                min="0" step="0.5"
                                class="col-span-4 border-2 ${set.saved ? 'border-green-300 bg-green-50 text-green-700' : 'border-gray-200 focus:border-[#003942]'} rounded-xl p-3 text-center text-lg font-bold focus:outline-none transition"
                                data-bi="${bi}" data-si="${si}" data-f="weight"
                                ${set.saved ? 'readonly' : ''}>

                            <div class="col-span-2 flex flex-col gap-1">
                                <button data-save-bi="${bi}" data-save-si="${si}"
                                    title="${set.saved ? 'Desmarcar serie' : 'Marcar como completada'}"
                                    class="flex items-center justify-center h-9 rounded-xl font-bold transition active:scale-95
                                    ${set.saved ? 'bg-green-100 text-green-600' : 'bg-[#003942] text-white'}">
                                    <span class="material-symbols-outlined text-xl">
                                        ${set.saved ? 'check' : 'arrow_forward'}
                                    </span>
                                </button>
                                <button data-remove-set-bi="${bi}" data-remove-set-si="${si}"
                                    title="Eliminar serie"
                                    class="flex items-center justify-center h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-400 hover:text-gray-600 transition active:scale-95">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>

                        </div>
                    `).join('')}

                    <button data-add-set="${bi}"
                        class="w-full border-2 border-dashed border-[#003942]/30 hover:border-[#003942] text-[#003942] py-3 rounded-xl font-semibold text-sm transition mt-1">
                        + Añadir serie
                    </button>

                </div>
            </div>
        `).join('');
    }

    function renderAddExercise() {
        return `
            <div class="bg-white rounded-2xl shadow p-4 space-y-3">
                <select id="exerciseSelect"
                    class="w-full border-2 border-gray-200 rounded-xl p-4 text-[#003942] font-semibold focus:border-[#003942] focus:outline-none transition">
                    <option value="">Selecciona un ejercicio...</option>
                    ${exercisesDB.map(e => `
                        <option value="${e.id}"
                            data-name="${e.name}"
                            data-image="${e.image ?? ''}"
                            data-muscle-group="${e.muscle_group}">
                            ${e.name}
                        </option>
                    `).join('')}
                </select>
                <button id="addExercise"
                    class="w-full bg-[#003942] text-white py-4 rounded-xl font-bold text-lg active:scale-95 transition">
                    + Añadir ejercicio
                </button>
            </div>
        `;
    }

    function renderFinishButton() {
        return `
            <button id="finishSession"
                class="w-full bg-white border-2 border-[#003942] text-[#003942] hover:bg-[#003942] hover:text-white py-4 rounded-xl font-bold text-lg active:scale-95 transition shadow">
                Finalizar entrenamiento
            </button>
        `;
    }

    function renderModal() {
        const modal = document.createElement('div');
        modal.id = 'finish-modal';
        modal.className = 'fixed inset-0 bg-black/50 z-50 flex items-end md:items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
                <h2 class="text-xl font-bold text-[#003942]">Finalizar entrenamiento</h2>
                <p class="text-gray-500 text-sm">Añade una nota o foto opcional antes de guardar.</p>
                <textarea id="session-notes"
                    placeholder="Ej: Me sentí con mucha energía, aumenté el peso en press banca..."
                    class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 text-sm focus:outline-none transition resize-none h-28"></textarea>
                <div>
                    <label class="block text-sm font-medium text-[#003942] mb-1">Foto del entrenamiento (opcional)</label>
                    <input type="file" id="session-photo" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-[#003942] file:text-white hover:file:bg-[#002a31]">
                </div>
                <div class="flex gap-3">
                    <button id="modal-cancel"
                        class="flex-1 border-2 border-gray-200 text-gray-500 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button id="modal-confirm"
                        class="flex-1 bg-[#003942] text-white py-3 rounded-xl font-semibold hover:bg-[#002a31] transition">
                        Guardar y salir
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        document.getElementById('modal-cancel').onclick = () => modal.remove();

        document.getElementById('modal-confirm').onclick = async () => {
            const notes = document.getElementById('session-notes').value;
            const photo = document.getElementById('session-photo').files[0];
            const duration = sessionStart ? Math.round((Date.now() - Number(sessionStart)) / 60000) : null;

            const formData = new FormData();
            formData.append('notes', notes);
            formData.append('duration', duration !== null ? duration : '');
            if (photo) formData.append('photo', photo);

            try {
                const res = await fetch(`${window.APP_URL}/training/session/${sessionId}/finish`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF() },
                    body: formData
                });
                if (!res.ok) throw new Error();
                clearState();
                window.location.href = `${window.APP_URL}/dashboard`;
            } catch (e) {
                alert('Error al finalizar el entrenamiento. Inténtalo de nuevo.');
            }
        };
    }

    function bindEvents() {

        const startBtn = document.getElementById('startSession');
        if (startBtn) {
            startBtn.onclick = async () => {
                startBtn.disabled = true;
                startBtn.textContent = 'Iniciando...';
                try {
                    const res = await fetch(`${window.APP_URL}/training/session`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() }
                    });
                    if (!res.ok) throw new Error();
                    const data = await res.json();
                    sessionId = data.session_id;
                    sessionStart = Date.now();
                    saveState();
                    render();
                } catch (e) {
                    alert('No se pudo iniciar el entrenamiento. Inténtalo de nuevo.');
                    startBtn.disabled = false;
                    startBtn.textContent = 'Comenzar entrenamiento';
                }
            };
        }

        const addExBtn = document.getElementById('addExercise');
        if (addExBtn) {
            addExBtn.onclick = () => {
                const select = document.getElementById('exerciseSelect');
                const option = select.options[select.selectedIndex];
                const id = select.value;
                if (!id) { alert('Selecciona un ejercicio primero'); return; }
                exerciseBlocks.push({
                    exercise_id:  id,
                    name:         option.dataset.name,
                    image:        option.dataset.image,
                    muscle_group: option.dataset.muscleGroup,
                    sets:         [{ reps: '', weight: '', saved: false }]
                });
                select.value = '';
                saveState();
                render();
            };
        }

        document.querySelectorAll('[data-add-set]').forEach(btn => {
            btn.onclick = (e) => {
                const bi = e.currentTarget.dataset.addSet;
                exerciseBlocks[bi].sets.push({ reps: '', weight: '', saved: false });
                saveState();
                render();
            };
        });

        document.querySelectorAll('[data-remove-block]').forEach(btn => {
            btn.onclick = (e) => {
                const bi = e.currentTarget.dataset.removeBlock;
                if (confirm('¿Eliminar este ejercicio y todas sus series?')) {
                    exerciseBlocks.splice(bi, 1);
                    saveState();
                    render();
                }
            };
        });

        document.querySelectorAll('[data-remove-set-bi]').forEach(btn => {
            btn.onclick = (e) => {
                const bi = e.currentTarget.dataset.removeSetBi;
                const si = e.currentTarget.dataset.removeSetSi;
                exerciseBlocks[bi].sets.splice(si, 1);
                saveState();
                render();
            };
        });

        document.querySelectorAll('input[data-bi]').forEach(input => {
            input.oninput = (e) => {
                const { bi, si, f } = e.target.dataset;
                exerciseBlocks[bi].sets[si][f] = e.target.value;
                saveState();
            };
        });

        document.querySelectorAll('[data-save-bi]').forEach(btn => {
            btn.onclick = async (e) => {
                const bi = e.currentTarget.dataset.saveBi;
                const si = e.currentTarget.dataset.saveSi;
                const set = exerciseBlocks[bi].sets[si];

                if (set.saved) {
                    exerciseBlocks[bi].sets[si].saved = false;
                    saveState();
                    render();
                    return;
                }

                if (!set.reps || !set.weight) {
                    alert('Rellena repeticiones y peso antes de guardar');
                    return;
                }

                try {
                    const res = await fetch(`${window.APP_URL}/training/set`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
                        body: JSON.stringify({
                            session_id:  sessionId,
                            exercise_id: exerciseBlocks[bi].exercise_id,
                            repetitions: set.reps,
                            weight:      set.weight
                        })
                    });
                    if (!res.ok) throw new Error();
                    exerciseBlocks[bi].sets[si].saved = true;
                    saveState();
                    render();
                } catch (e) {
                    alert('Error al guardar la serie. Inténtalo de nuevo.');
                }
            };
        });

        const finishBtn = document.getElementById('finishSession');
        if (finishBtn) {
            finishBtn.onclick = () => {
                const unsaved = exerciseBlocks.some(block =>
                    block.sets.some(set => !set.saved)
                );
                if (unsaved) {
                    alert('Tienes series sin guardar. Márcalas todas antes de finalizar.');
                    return;
                }
                if (exerciseBlocks.length === 0) {
                    alert('No has añadido ningún ejercicio.');
                    return;
                }
                renderModal();
            };
        }
    }
});