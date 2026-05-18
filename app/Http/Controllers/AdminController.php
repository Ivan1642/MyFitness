<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TrainingSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', false)
            ->withCount(['trainingSessions', 'followers', 'following'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.index', compact('users'));
    }

    public function ban(Request $request, $id)
    {
        $user = User::where('id', $id)->where('is_admin', false)->firstOrFail();
        $user->update(['banned_at' => Carbon::now()]);
        return redirect()->route('admin.index')->with('success', "Usuario {$user->name} baneado por 2 días.");
    }

    public function unban($id)
    {
        $user = User::where('id', $id)->where('is_admin', false)->firstOrFail();
        $user->update(['banned_at' => null]);
        return redirect()->route('admin.index')->with('success', "Usuario {$user->name} desbaneado.");
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->where('is_admin', false)->firstOrFail();
        $user->delete();
        return redirect()->route('admin.index')->with('success', 'Usuario eliminado correctamente.');
    }
}