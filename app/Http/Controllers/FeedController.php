<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;
use App\Models\TrainingSession;
use App\Models\Achievement;
use App\Models\Record;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isBanned()) {
            $bannedUntil = $user->banned_at->addDays(2)->format('d/m/Y H:i');
            return view('feed.banned', compact('bannedUntil'));
        }

        $type = $request->get('type', 'para_ti');
        return view('feed.index', compact('type'));
    }

    public function load(Request $request)
    {
        if (auth()->user()->isBanned()) {
            return response()->json(['items' => [], 'hasMore' => false]);
        }

        $type   = $request->get('type', 'para_ti');
        $page   = $request->get('page', 1);
        $perPage = 15;
        $userId = auth()->id();

        if ($type === 'para_ti') {
            $items = $this->getParaTi($userId);
        } else {
            $items = $this->getSeguidos($userId);
        }

        $total  = $items->count();
        $sliced = $items->forPage($page, $perPage)->values();

        return response()->json([
            'items'    => $sliced,
            'hasMore'  => ($page * $perPage) < $total,
        ]);
    }

    public function loadProfile(Request $request, $id)
    {
        $page    = $request->get('page', 1);
        $perPage = 15;
        $userId  = auth()->id();

        $sessions = TrainingSession::with('user')
            ->where('user_id', $id)
            ->where('is_finished', true)
            ->where('is_public', true)
            ->get()
            ->map(fn($s) => $this->formatSession($s, $userId));

        $posts = Post::with('user')
            ->where('user_id', $id)
            ->get()
            ->map(fn($p) => $this->formatPost($p, $userId));

        $achievements = Achievement::with('user')
            ->where('user_id', $id)
            ->get()
            ->map(fn($a) => $this->formatAchievement($a, $userId));

        $records = Record::with(['user', 'exercise'])
            ->where('user_id', $id)
            ->get()
            ->map(fn($r) => $this->formatRecord($r, $userId));

        $items = $sessions->concat($posts)->concat($achievements)->concat($records)
            ->sortByDesc('created_at')->values();

        $total  = $items->count();
        $sliced = $items->forPage($page, $perPage)->values();

        return response()->json([
            'items'   => $sliced,
            'hasMore' => ($page * $perPage) < $total,
        ]);
    }

    private function getParaTi($userId)
    {
        $sessions = TrainingSession::with(['user', 'sets.exercise'])
            ->where('is_finished', true)
            ->where('is_public', true)
            ->whereHas('user', fn($q) => $q->where(function($q) {
                $q->whereNull('banned_at')
                  ->orWhereRaw('banned_at < DATE_SUB(NOW(), INTERVAL 2 DAY)');
            }))
            ->get()
            ->map(fn($s) => $this->formatSession($s, $userId));

        $posts = Post::with('user')
            ->whereHas('user', fn($q) => $q->where(function($q) {
                $q->whereNull('banned_at')
                  ->orWhereRaw('banned_at < DATE_SUB(NOW(), INTERVAL 2 DAY)');
            }))
            ->get()
            ->map(fn($p) => $this->formatPost($p, $userId));

        return $sessions->concat($posts)->sortByDesc('created_at')->values();
    }

    private function getSeguidos($userId)
    {
        $followingIds = DB::table('followers')
            ->where('follower_id', $userId)
            ->pluck('following_id');

        if ($followingIds->isEmpty()) return collect();

        $sessions = TrainingSession::with(['user', 'sets.exercise'])
            ->where('is_finished', true)
            ->where('is_public', true)
            ->whereIn('user_id', $followingIds)
            ->get()
            ->map(fn($s) => $this->formatSession($s, $userId));

        $posts = Post::with('user')
            ->whereIn('user_id', $followingIds)
            ->get()
            ->map(fn($p) => $this->formatPost($p, $userId));

        $achievements = Achievement::with('user')
            ->whereIn('user_id', $followingIds)
            ->get()
            ->map(fn($a) => $this->formatAchievement($a, $userId));

        $records = Record::with(['user', 'exercise'])
            ->whereIn('user_id', $followingIds)
            ->get()
            ->map(fn($r) => $this->formatRecord($r, $userId));

        return $sessions->concat($posts)->concat($achievements)->concat($records)
            ->sortByDesc('created_at')->values();
    }

    private function getLikes($id, $type, $userId)
    {
        $count = DB::table('routine_likes')
            ->where('likeable_id', $id)
            ->where('likeable_type', $type)
            ->count();

        $liked = DB::table('routine_likes')
            ->where('likeable_id', $id)
            ->where('likeable_type', $type)
            ->where('id_usuario', $userId)
            ->exists();

        return [$count, $liked];
    }

    private function formatSession($s, $userId)
    {
        [$count, $liked] = $this->getLikes($s->id, 'App\\Models\\TrainingSession', $userId);

        $exercises = $s->sets->groupBy('exercise_id')->take(3)->map(function($sets) {
            return [
                'name'       => $sets->first()->exercise->name,
                'sets_count' => $sets->count(),
            ];
        })->values();

        return [
            'id'            => $s->id,
            'user_id'       => $s->user_id,
            'type'          => 'session',
            'content'       => $s->notes,
            'image'         => $s->photo ? asset('storage/' . $s->photo) : null,
            'duration'      => $s->duration,
            'created_at'    => $s->date,
            'exercises'     => $exercises,
            'user_name'     => $s->user->name,
            'user_username' => $s->user->username,
            'user_avatar'   => $s->user->avatar ? asset('storage/' . $s->user->avatar) : asset('img/predeterminada_perfil.png'),
            'likes_count'   => $count,
            'liked'         => $liked,
            'likeable_type' => 'App\\Models\\TrainingSession',
        ];
    }

    private function formatPost($p, $userId)
    {
        [$count, $liked] = $this->getLikes($p->id, 'App\\Models\\Post', $userId);
        return [
            'id'            => $p->id,
            'user_id'       => $p->user_id,
            'type'          => 'post',
            'content'       => $p->content,
            'image'         => $p->image ? asset('storage/' . $p->image) : null,
            'duration'      => null,
            'created_at'    => $p->created_at,
            'user_name'     => $p->user->name,
            'user_username' => $p->user->username,
            'user_avatar'   => $p->user->avatar ? asset('storage/' . $p->user->avatar) : asset('img/predeterminada_perfil.png'),
            'likes_count'   => $count,
            'liked'         => $liked,
            'likeable_type' => 'App\\Models\\Post',
        ];
    }

    private function formatAchievement($a, $userId)
    {
        [$count, $liked] = $this->getLikes($a->id, 'App\\Models\\Achievement', $userId);
        return [
            'id'            => $a->id,
            'user_id'       => $a->user_id,
            'type'          => 'achievement',
            'content'       => $a->name,
            'image'         => null,
            'duration'      => null,
            'created_at'    => $a->created_at,
            'user_name'     => $a->user->name,
            'user_username' => $a->user->username,
            'user_avatar'   => $a->user->avatar ? asset('storage/' . $a->user->avatar) : asset('img/predeterminada_perfil.png'),
            'likes_count'   => $count,
            'liked'         => $liked,
            'likeable_type' => 'App\\Models\\Achievement',
        ];
    }

    private function formatRecord($r, $userId)
    {
        [$count, $liked] = $this->getLikes($r->id, 'App\\Models\\Record', $userId);
        return [
            'id'            => $r->id,
            'user_id'       => $r->user_id,
            'type'          => 'record',
            'content'       => $r->exercise->name . ' — ' . $r->max_weight . ' kg',
            'image'         => null,
            'duration'      => null,
            'created_at'    => $r->updated_at,
            'user_name'     => $r->user->name,
            'user_username' => $r->user->username,
            'user_avatar'   => $r->user->avatar ? asset('storage/' . $r->user->avatar) : asset('img/predeterminada_perfil.png'),
            'likes_count'   => $count,
            'liked'         => $liked,
            'likeable_type' => 'App\\Models\\Record',
        ];
    }

    public function like(Request $request)
    {
        $request->validate([
            'likeable_id'   => 'required|integer',
            'likeable_type' => 'required|string',
        ]);

        $existing = DB::table('routine_likes')
            ->where('id_usuario', auth()->id())
            ->where('likeable_id', $request->likeable_id)
            ->where('likeable_type', $request->likeable_type)
            ->first();

        if ($existing) {
            DB::table('routine_likes')
                ->where('id_usuario', auth()->id())
                ->where('likeable_id', $request->likeable_id)
                ->where('likeable_type', $request->likeable_type)
                ->delete();
            $liked = false;
        } else {
            DB::table('routine_likes')->insert([
                'id_usuario'    => auth()->id(),
                'likeable_id'   => $request->likeable_id,
                'likeable_type' => $request->likeable_type,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $liked = true;
        }

        $count = DB::table('routine_likes')
            ->where('likeable_id', $request->likeable_id)
            ->where('likeable_type', $request->likeable_type)
            ->count();

        return response()->json(['liked' => $liked, 'count' => $count]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('is_admin', false)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('username', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'username', 'avatar')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'avatar'   => $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : asset('img/predeterminada_perfil.png'),
                ];
            });

        return response()->json($users);
    }
}