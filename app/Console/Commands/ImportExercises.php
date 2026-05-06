<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exercise;
use Illuminate\Support\Facades\Http;

class ImportExercises extends Command
{
    protected $signature = 'exercises:import';
    protected $description = 'Importa ejercicios desde la API de wger';

    private array $muscleMap = [
        1  => 'Bíceps',
        2  => 'Deltoides anterior',
        3  => 'Pectoral',
        4  => 'Tríceps',
        5  => 'Bíceps femoral',
        6  => 'Glúteos',
        7  => 'Cuádriceps',
        8  => 'Abdominales',
        9  => 'Trapecio',
        10 => 'Espalda baja',
        11 => 'Dorsal',
        12 => 'Gastrocnemio',
        13 => 'Isquiotibiales',
        14 => 'Deltoides lateral',
        15 => 'Deltoides posterior',
    ];

    public function handle()
    {
        $this->info('Importando ejercicios...');

        $page = 1;
        $imported = 0;
        $skipped = 0;

        do {
            $response = Http::get('https://wger.de/api/v2/exerciseinfo/', [
                'format' => 'json',
                'limit'  => 100,
                'offset' => ($page - 1) * 100,
            ]);

            if (!$response->ok()) {
                $this->error('Error al conectar con la API');
                return;
            }

            $data = $response->json();

            foreach ($data['results'] as $ex) {

                $translation = collect($ex['translations'])->firstWhere('language', 6)
                            ?? collect($ex['translations'])->firstWhere('language', 2);

                if (!$translation || empty($translation['name'])) {
                    $skipped++;
                    continue;
                }

                $muscleGroup = 'General';
                if (!empty($ex['muscles'])) {
                    $muscleId = $ex['muscles'][0]['id'];
                    $muscleGroup = $this->muscleMap[$muscleId] ?? 'General';
                }

                $image = $ex['images'][0]['image'] ?? null;

                Exercise::firstOrCreate(
                    ['name' => $translation['name']],
                    [
                        'muscle_group' => $muscleGroup,
                        'description'  => strip_tags($translation['description'] ?? ''),
                        'image'        => $image,
                    ]
                );

                $imported++;
            }

            $this->info("Página {$page} procesada — {$imported} ejercicios importados");
            $page++;

        } while (!empty($data['next']));

        $this->info("Importación completa: {$imported} ejercicios importados, {$skipped} omitidos.");
    }
}