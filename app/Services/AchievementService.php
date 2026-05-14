<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use Carbon\Carbon;

class AchievementService
{
    private array $definitions = [
        // Sesiones
        'sessions_1'   => '¡Empieza el camino!',
        'sessions_10'  => 'Cogiendo el ritmo',
        'sessions_25'  => 'Ya es un hábito',
        'sessions_50'  => 'Atleta en formación',
        'sessions_100' => 'Veterano de hierro',
        'sessions_250' => 'Leyenda del gimnasio',
        'sessions_500' => 'Indestructible',

        // PRs
        'prs_1'  => 'Primera marca',
        'prs_5'  => 'Rompe límites',
        'prs_10' => 'Máquina de records',
        'prs_25' => 'Imparable',
        'prs_50' => 'El más fuerte',

        // Volumen total
        'volume_1000'   => 'Primera tonelada',
        'volume_10000'  => 'Fuerza bruta',
        'volume_50000'  => 'Titán',
        'volume_100000' => 'Hércules',
        'volume_500000' => 'Más allá del límite',

        // Racha semanal
        'streak_2'  => 'Sin excusas',
        'streak_4'  => 'Modo bestia',
        'streak_8'  => 'Disciplina total',
        'streak_12' => 'Estilo de vida',
        'streak_52' => 'Un año sin parar',

        // Social
        'following_5'   => 'Construyendo comunidad',
        'following_20'  => 'Conector social',
        'followers_100' => 'Inspiración para otros',
        'followers_500' => 'Influencer del gimnasio',

        // Sesiones semanales
        'weekly_3' => 'Semana productiva',
        'weekly_5' => 'Semana perfecta',

        // Peso en una rep
        'rep_weight_100' => 'Club de los 100',
        'rep_weight_150' => 'Medio camión',
        'rep_weight_200' => 'Fuerza sobrehumana',

        // Volumen en una serie
        'set_volume_1000' => 'Serie brutal',
        'set_volume_2000' => 'Serie épica',

        // Diversidad muscular
        'muscle_groups_5' => 'Atleta completo',

        // Horario
        'early_bird' => 'El madrugador',
        'night_owl'  => 'El nocturno',

        // Dios del Gym
        'god_of_gym' => 'Dios del Gym',
    ];

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function check(User $user): void
    {
        $this->checkSessions($user);
        $this->checkPRs($user);
        $this->checkVolume($user);
        $this->checkStreak($user);
        $this->checkSocial($user);
        $this->checkWeeklySessions($user);
        $this->checkRepWeight($user);
        $this->checkSetVolume($user);
        $this->checkMuscleGroups($user);
        $this->checkSchedule($user);
        $this->checkGodOfGym($user);
    }

    private function award(User $user, string $slug): bool
    {
        $already = Achievement::where('user_id', $user->id)
            ->where('slug', $slug)
            ->exists();

        if ($already) return false;

        Achievement::create([
            'user_id' => $user->id,
            'slug'    => $slug,
            'name'    => $this->definitions[$slug],
        ]);

        return true;
    }

    private function checkSessions(User $user): void
    {
        $count = $user->trainingSessions()->where('is_finished', true)->count();
        foreach ([1, 10, 25, 50, 100, 250, 500] as $tier) {
            if ($count >= $tier) $this->award($user, "sessions_{$tier}");
        }
    }

    private function checkPRs(User $user): void
    {
        $count = $user->records()->count();
        foreach ([1, 5, 10, 25, 50] as $tier) {
            if ($count >= $tier) $this->award($user, "prs_{$tier}");
        }
    }

    private function checkVolume(User $user): void
    {
        $volume = 0;
        foreach ($user->trainingSessions()->where('is_finished', true)->with('sets')->get() as $session) {
            foreach ($session->sets as $set) {
                $volume += $set->weight * $set->repetitions;
            }
        }
        foreach ([1000, 10000, 50000, 100000, 500000] as $tier) {
            if ($volume >= $tier) $this->award($user, "volume_{$tier}");
        }
    }

    private function checkStreak(User $user): void
    {
        $sessions = $user->trainingSessions()
            ->where('is_finished', true)
            ->orderBy('date')
            ->get();

        $weeklyMap = [];
        foreach ($sessions as $session) {
            $week = Carbon::parse($session->date)->startOfWeek()->format('Y-W');
            $weeklyMap[$week] = true;
        }

        $streak = 0;
        $maxStreak = 0;
        $currentWeek = Carbon::now()->startOfWeek();

        for ($i = 0; $i < 60; $i++) {
            $week = $currentWeek->copy()->subWeeks($i)->format('Y-W');
            if (isset($weeklyMap[$week])) {
                $streak++;
                $maxStreak = max($maxStreak, $streak);
            } else {
                break;
            }
        }

        foreach ([2, 4, 8, 12, 52] as $tier) {
            if ($maxStreak >= $tier) $this->award($user, "streak_{$tier}");
        }
    }

    private function checkSocial(User $user): void
    {
        $following = $user->following()->count();
        foreach ([5, 20] as $tier) {
            if ($following >= $tier) $this->award($user, "following_{$tier}");
        }

        $followers = $user->followers()->count();
        foreach ([100, 500] as $tier) {
            if ($followers >= $tier) $this->award($user, "followers_{$tier}");
        }
    }

    private function checkWeeklySessions(User $user): void
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();
        $count = $user->trainingSessions()
            ->where('is_finished', true)
            ->whereBetween('date', [$start, $end])
            ->count();

        foreach ([3, 5] as $tier) {
            if ($count >= $tier) $this->award($user, "weekly_{$tier}");
        }
    }

    private function checkRepWeight(User $user): void
    {
        $maxWeight = \App\Models\Set::whereHas('trainingSession', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_finished', true);
        })->where('repetitions', 1)->max('weight');

        if (!$maxWeight) return;

        foreach ([100, 150, 200] as $tier) {
            if ($maxWeight >= $tier) $this->award($user, "rep_weight_{$tier}");
        }
    }

    private function checkSetVolume(User $user): void
    {
        $sets = \App\Models\Set::whereHas('trainingSession', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_finished', true);
        })->get();

        $maxSetVolume = 0;
        foreach ($sets as $set) {
            $maxSetVolume = max($maxSetVolume, $set->weight * $set->repetitions);
        }

        foreach ([1000, 2000] as $tier) {
            if ($maxSetVolume >= $tier) $this->award($user, "set_volume_{$tier}");
        }
    }

    private function checkMuscleGroups(User $user): void
    {
        $groups = \App\Models\Set::whereHas('trainingSession', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_finished', true);
        })->with('exercise')->get()->pluck('exercise.muscle_group')->unique()->count();

        if ($groups >= 5) $this->award($user, 'muscle_groups_5');
    }

    private function checkSchedule(User $user): void
    {
        $earlyBird = $user->trainingSessions()
            ->where('is_finished', true)
            ->whereRaw('HOUR(date) < 7')
            ->exists();

        if ($earlyBird) $this->award($user, 'early_bird');

        $nightOwl = $user->trainingSessions()
            ->where('is_finished', true)
            ->whereRaw('HOUR(date) >= 22')
            ->exists();

        if ($nightOwl) $this->award($user, 'night_owl');
    }

    private function checkGodOfGym(User $user): void
    {
        $allSlugs = array_keys($this->definitions);
        $allSlugs = array_filter($allSlugs, fn($s) => $s !== 'god_of_gym');

        $userSlugs = Achievement::where('user_id', $user->id)
            ->whereIn('slug', $allSlugs)
            ->pluck('slug')
            ->toArray();

        $missing = array_diff($allSlugs, $userSlugs);

        if (empty($missing)) {
            $this->award($user, 'god_of_gym');
        } else {
            Achievement::where('user_id', $user->id)
                ->where('slug', 'god_of_gym')
                ->delete();
        }
    }
}