console.log("training.js cargado");

let sessionId = null;
let exercisesDB = [];
let selectedExercise = null;
let sets = [];

window.addEventListener("DOMContentLoaded", async () => {

    const el = document.getElementById("training-app");

    if (!el) return;

    try {
        const res = await fetch("/api/exercises");
        exercisesDB = await res.json();
    } catch (e) {
        exercisesDB = [];
    }

    render();

    function render() {

        el.innerHTML = `
            <div class="max-w-3xl mx-auto p-4 space-y-4">

                <div class="bg-[#003942] text-white p-4 rounded-xl shadow flex justify-between items-center">

                    <div>
                        <h1 class="text-lg font-bold">Entrenamiento</h1>
                        <p class="text-sm opacity-80">
                            ${sessionId ? "Sesión activa" : "Sin iniciar"}
                        </p>
                    </div>

                    <button id="startSession"
                        class="bg-white text-[#003942] px-3 py-1 rounded-lg font-semibold">
                        ${sessionId ? "Iniciada" : "Iniciar"}
                    </button>

                </div>

                <div class="bg-white p-4 rounded-xl shadow space-y-3">

                    <select id="exerciseSelect"
                        class="w-full border rounded-lg p-2">

                        <option value="">Selecciona ejercicio</option>

                        ${exercisesDB.map(e => `
                            <option value="${e.id}">
                                ${e.name}
                            </option>
                        `).join("")}

                    </select>

                    <button id="addSet"
                        class="w-full bg-black text-white py-2 rounded-lg">
                        Añadir serie
                    </button>

                </div>

                <div class="space-y-3">

                    ${sets.map((set, i) => `
                        <div class="flex gap-2 items-center bg-white p-3 rounded-lg shadow">

                            <input type="number"
                                placeholder="Reps"
                                value="${set.reps}"
                                class="border p-2 w-20 rounded"
                                data-i="${i}"
                                data-f="reps">

                            <input type="number"
                                placeholder="Peso"
                                value="${set.weight}"
                                class="border p-2 w-20 rounded"
                                data-i="${i}"
                                data-f="weight">

                            <button data-save="${i}"
                                class="bg-[#003942] text-white px-3 py-2 rounded">
                                Guardar
                            </button>

                        </div>
                    `).join("")}

                </div>

            </div>
        `;

        bindEvents();
    }

    function bindEvents() {

        const startBtn = document.getElementById("startSession");

        startBtn.onclick = async () => {

            const res = await fetch("/training/session", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await res.json();
            sessionId = data.session_id;

            render();
        };

        const select = document.getElementById("exerciseSelect");

        select.onchange = (e) => {
            selectedExercise = e.target.value;
        };

        document.getElementById("addSet").onclick = () => {

            if (!selectedExercise) return;

            sets.push({
                exercise_id: selectedExercise,
                reps: "",
                weight: ""
            });

            render();
        };

        document.querySelectorAll("input[data-i]").forEach(input => {

            input.oninput = (e) => {

                const i = e.target.dataset.i;
                const f = e.target.dataset.f;

                sets[i][f] = e.target.value;
            };
        });

        document.querySelectorAll("button[data-save]").forEach(btn => {

            btn.onclick = async (e) => {

                const i = e.target.dataset.save;
                const set = sets[i];

                await fetch("/training/set", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        exercise_id: set.exercise_id,
                        repetitions: set.reps,
                        weight: set.weight
                    })
                });

                console.log("set guardado");
            };
        });
    }
});