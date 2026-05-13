let exerciseBlocks = [];

const container = document.getElementById('exercises-container');

document.getElementById('routine-form').onsubmit = (e) => {
    syncInputs();

    if (exerciseBlocks.length === 0) {
        e.preventDefault();
        alert('Añade al menos un ejercicio a la rutina.');
        return;
    }

    for (const block of exerciseBlocks) {
        for (const set of block.sets) {
            if (!set.reps || !set.weight) {
                e.preventDefault();
                alert('Rellena todas las repeticiones y pesos antes de guardar.');
                return;
            }
        }
    }
};

document.getElementById('add-exercise').onclick = () => {
    const select = document.getElementById('exercise-select');
    const id = select.value;
    const name = select.options[select.selectedIndex]?.dataset.name;
    const muscle = select.options[select.selectedIndex]?.dataset.muscle;
    if (!id) { alert('Selecciona un ejercicio primero'); return; }

    syncInputs();
    exerciseBlocks.push({ id, name, muscle, sets: [{ reps: '', weight: '' }] });
    select.value = '';
    renderBlocks();
};

function syncInputs() {
    document.querySelectorAll('[data-bi]').forEach(input => {
        const bi = input.dataset.bi;
        const si = input.dataset.si;
        const f = input.dataset.f;
        if (exerciseBlocks[bi] && exerciseBlocks[bi].sets[si]) {
            exerciseBlocks[bi].sets[si][f] = input.value;
        }
    });
}

function renderBlocks() {
    container.innerHTML = exerciseBlocks.map((block, bi) => `
        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <div class="bg-[#003942] px-4 py-3 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-white text-base">${block.name}</h3>
                    <p class="text-white/50 text-xs">${block.muscle}</p>
                </div>
                <button type="button" onclick="removeBlock(${bi})"
                    class="text-white/50 hover:text-white text-sm transition">
                    Eliminar
                </button>
            </div>

            <input type="hidden" name="exercises[${bi}][exercise_id]" value="${block.id}">

            <div class="p-4 space-y-3">
                <div class="grid grid-cols-12 gap-2 text-xs text-gray-400 font-semibold uppercase px-1">
                    <span class="col-span-2 text-center">Serie</span>
                    <span class="col-span-4 text-center">Reps</span>
                    <span class="col-span-4 text-center">Kg</span>
                    <span class="col-span-2"></span>
                </div>

                ${block.sets.map((set, si) => `
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <span class="col-span-2 text-center font-bold text-[#003942] text-lg">${si + 1}</span>
                        <input type="number" inputmode="numeric" placeholder="—" value="${set.reps}" min="1"
                            class="col-span-4 border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 text-center text-lg font-bold focus:outline-none transition"
                            name="exercises[${bi}][sets][${si}][repetitions]"
                            data-bi="${bi}" data-si="${si}" data-f="reps">
                        <input type="number" inputmode="decimal" placeholder="—" value="${set.weight}" min="0" step="0.5"
                            class="col-span-4 border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 text-center text-lg font-bold focus:outline-none transition"
                            name="exercises[${bi}][sets][${si}][weight]"
                            data-bi="${bi}" data-si="${si}" data-f="weight">
                        <button type="button" onclick="removeSet(${bi}, ${si})"
                            class="col-span-2 flex items-center justify-center h-12 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-400 transition">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                `).join('')}

                <button type="button" onclick="addSet(${bi})"
                    class="w-full border-2 border-dashed border-[#003942]/30 hover:border-[#003942] text-[#003942] py-3 rounded-xl font-semibold text-sm transition">
                    + Añadir serie
                </button>
            </div>
        </div>
    `).join('');
}

window.addSet = (bi) => {
    syncInputs();
    exerciseBlocks[bi].sets.push({ reps: '', weight: '' });
    renderBlocks();
};

window.removeSet = (bi, si) => {
    syncInputs();
    exerciseBlocks[bi].sets.splice(si, 1);
    renderBlocks();
};

window.removeBlock = (bi) => {
    if (confirm('¿Eliminar este ejercicio?')) {
        syncInputs();
        exerciseBlocks.splice(bi, 1);
        renderBlocks();
    }
};