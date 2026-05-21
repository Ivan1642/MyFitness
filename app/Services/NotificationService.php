<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function newPR(User $user, $exercise)
    {
        Notification::create([
            'user_id' => $user->id,
            'type'    => 'pr',
            'title'   => '¡Nuevo récord personal!',
            'body'    => 'Has superado tu marca en ' . $exercise->name,
            'read'    => false,
        ]);
    }

    public function newAchievement(User $user, string $name, string $slug)
    {
        $conditions = $this->getCondition($slug);

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'achievement',
            'title'   => '¡Nuevo logro desbloqueado!',
            'body'    => $name . ($conditions ? ' — ' . $conditions : ''),
            'read'    => false,
        ]);
    }

    public function newLike(User $owner, User $liker, $likeableId, $likeableType)
    {
        $existing = Notification::where('user_id', $owner->id)
            ->where('type', 'like')
            ->where('notifiable_id', $likeableId)
            ->where('notifiable_type', $likeableType)
            ->where('read', false)
            ->first();

        if ($existing) {
            $count = $this->extractLikeCount($existing->body) + 1;
            $existing->update([
                'body' => $count . ' personas han dado like a tu publicación',
            ]);
        } else {
            Notification::create([
                'user_id'         => $owner->id,
                'type'            => 'like',
                'title'           => '¡Nuevo like!',
                'body'            => $liker->name . ' ha dado like a tu publicación',
                'read'            => false,
                'notifiable_id'   => $likeableId,
                'notifiable_type' => $likeableType,
            ]);
        }
    }

    public function newFollower(User $owner, User $follower)
    {
        Notification::create([
            'user_id' => $owner->id,
            'type'    => 'follower',
            'title'   => '¡Nuevo seguidor!',
            'body'    => $follower->name . ' ha empezado a seguirte',
            'read'    => false,
        ]);
    }

    private function extractLikeCount(string $body): int
    {
        preg_match('/^(\d+)/', $body, $matches);
        return isset($matches[1]) ? (int) $matches[1] : 1;
    }

    private function getCondition(string $slug): string
    {
        $conditions = [
            'sessions_1'      => 'Completa tu primera sesión',
            'sessions_10'     => 'Completa 10 sesiones',
            'sessions_25'     => 'Completa 25 sesiones',
            'sessions_50'     => 'Completa 50 sesiones',
            'sessions_100'    => 'Completa 100 sesiones',
            'sessions_250'    => 'Completa 250 sesiones',
            'sessions_500'    => 'Completa 500 sesiones',
            'prs_1'           => 'Consigue tu primer récord personal',
            'prs_5'           => 'Consigue 5 récords personales',
            'prs_10'          => 'Consigue 10 récords personales',
            'prs_25'          => 'Consigue 25 récords personales',
            'prs_50'          => 'Consigue 50 récords personales',
            'volume_1000'     => 'Acumula 1.000 kg de volumen total',
            'volume_10000'    => 'Acumula 10.000 kg de volumen total',
            'volume_50000'    => 'Acumula 50.000 kg de volumen total',
            'volume_100000'   => 'Acumula 100.000 kg de volumen total',
            'volume_500000'   => 'Acumula 500.000 kg de volumen total',
            'streak_2'        => 'Entrena 2 semanas seguidas',
            'streak_4'        => 'Entrena 4 semanas seguidas',
            'streak_8'        => 'Entrena 8 semanas seguidas',
            'streak_12'       => 'Entrena 12 semanas seguidas',
            'streak_52'       => 'Entrena 52 semanas seguidas',
            'following_5'     => 'Sigue a 5 personas',
            'following_20'    => 'Sigue a 20 personas',
            'followers_100'   => 'Consigue 100 seguidores',
            'followers_500'   => 'Consigue 500 seguidores',
            'weekly_3'        => 'Entrena 3 días en una semana',
            'weekly_5'        => 'Entrena 5 días en una semana',
            'rep_weight_100'  => 'Levanta 100 kg en una repetición',
            'rep_weight_150'  => 'Levanta 150 kg en una repetición',
            'rep_weight_200'  => 'Levanta 200 kg en una repetición',
            'set_volume_1000' => 'Consigue 1.000 kg de volumen en una serie',
            'set_volume_2000' => 'Consigue 2.000 kg de volumen en una serie',
            'muscle_groups_5' => 'Entrena 5 grupos musculares distintos',
            'early_bird'      => 'Entrena antes de las 7:00 AM',
            'night_owl'       => 'Entrena después de las 22:00 PM',
            'god_of_gym'      => 'Consigue todos los demás logros',
        ];

        return $conditions[$slug] ?? '';
    }
}