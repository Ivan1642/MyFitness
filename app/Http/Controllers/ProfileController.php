<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $followers = $user->followers()->with('follower')->get();
        $following = $user->following()->with('following')->get();

        return view('profile.index', compact('user', 'followers', 'following'));
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
            'avatar'   => 'nullable|image|max:2048',
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
}