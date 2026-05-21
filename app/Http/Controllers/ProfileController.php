<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $followers = $user->followers()->with('follower')->get();
        $following = $user->following()->with('following')->get();
        $achievements = $user->achievements()->orderByDesc('created_at')->get();
        $totalLikes = $this->getTotalLikes($user);

        return view('profile.index', compact('user', 'followers', 'following', 'achievements', 'totalLikes'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:30|alpha_dash|unique:users,username,' . $user->id,
            'bio'      => 'nullable|string|max:500',
            'weight'   => 'nullable|numeric|min:0|max:300',
            'height'   => 'nullable|integer|min:0|max:300',
            'avatar' => 'nullable|image|max:5120',
        ]);

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'bio'      => $request->bio,
            'weight'   => $request->weight,
            'height'   => $request->height,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente.');
    }

    public function show($id)
    {
        if (auth()->id() == $id) {
            return redirect()->route('profile');
        }

        $user = \App\Models\User::findOrFail($id);
        $followers = $user->followers()->with('follower')->get();
        $following = $user->following()->with('following')->get();
        $achievements = $user->achievements()->orderByDesc('created_at')->get();
        $totalLikes = $this->getTotalLikes($user);

        $isFollowing = auth()->user()->following()
            ->where('following_id', $id)
            ->exists();

        return view('profile.show', compact('user', 'followers', 'following', 'achievements', 'isFollowing', 'totalLikes'));
    }

    public function follow($id)
    {
        $user = auth()->user();

        $existing = $user->following()->where('following_id', $id)->first();

        if ($existing) {
            \App\Models\Follower::where('follower_id', $user->id)
                ->where('following_id', $id)
                ->delete();
        } else {
            \App\Models\Follower::create([
                'follower_id'  => $user->id,
                'following_id' => $id,
            ]);

            (new \App\Services\AchievementService())->check($user);
        }

        return redirect()->route('profile.show', $id);
    }

    public function feed($id)
    {
        $user = User::findOrFail($id);
        return view('profile.feed', compact('user'));
    }

    private function getTotalLikes($user)
    {
        $postIds = $user->posts()->pluck('id');
        $sessionIds = $user->trainingSessions()->pluck('id');
        $achievementIds = $user->achievements()->pluck('id');
        $recordIds = $user->records()->pluck('id');

        return \DB::table('routine_likes')
            ->where(function($q) use ($postIds) {
                $q->where('likeable_type', 'App\\Models\\Post')
                ->whereIn('likeable_id', $postIds);
            })
            ->orWhere(function($q) use ($sessionIds) {
                $q->where('likeable_type', 'App\\Models\\TrainingSession')
                ->whereIn('likeable_id', $sessionIds);
            })
            ->orWhere(function($q) use ($achievementIds) {
                $q->where('likeable_type', 'App\\Models\\Achievement')
                ->whereIn('likeable_id', $achievementIds);
            })
            ->orWhere(function($q) use ($recordIds) {
                $q->where('likeable_type', 'App\\Models\\Record')
                ->whereIn('likeable_id', $recordIds);
            })
            ->count();
    }
}