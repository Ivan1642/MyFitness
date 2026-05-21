<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrainingSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\NotificationController;

//landing sin loguear
Route::get('/', function () {
    return view('home');
})->name('home');

//Landing logueado
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

//Autenticaciones
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Una vez logueado
Route::middleware('auth')->group(function () {
    Route::get('/training/start', [TrainingSessionController::class, 'start'])->name('training.start');
    Route::post('/training/session', [TrainingSessionController::class, 'store'])->name('training.session');
    Route::post('/training/set', [TrainingSessionController::class, 'storeSet'])->name('training.set');
    Route::post('/training/session/{id}/finish', [TrainingSessionController::class, 'finish'])->name('training.finish');
    Route::delete('/training/session/{id}', [TrainingSessionController::class, 'destroy'])->name('training.destroy');
    Route::delete('/training/session/{id}/cancel', [TrainingSessionController::class, 'cancel'])->name('training.cancel');
    Route::patch('/training/session/{id}/visibility', [TrainingSessionController::class, 'toggleVisibility'])->name('training.visibility');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/training/session/{id}', [TrainingSessionController::class, 'show'])->name('training.show');
    Route::get('/rutinas', [RoutineController::class, 'index'])->name('routines.index');
    Route::get('/rutinas/crear', [RoutineController::class, 'create'])->name('routines.create');
    Route::post('/rutinas', [RoutineController::class, 'store'])->name('routines.store');
    Route::get('/rutinas/{id}/editar', [RoutineController::class, 'edit'])->name('routines.edit');
    Route::put('/rutinas/{id}', [RoutineController::class, 'update'])->name('routines.update');
    Route::delete('/rutinas/{id}', [RoutineController::class, 'destroy'])->name('routines.destroy');
    Route::post('/rutinas/{id}/start', [RoutineController::class, 'start'])->name('routines.start');
    Route::get('/progreso', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');
    Route::get('/feed/load', [FeedController::class, 'load'])->name('feed.load');
    Route::post('/feed/like', [FeedController::class, 'like'])->name('feed.like');
    Route::get('/feed/search', [FeedController::class, 'search'])->name('feed.search');
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/{id}/follow', [ProfileController::class, 'follow'])->name('profile.follow');
    Route::get('/profile/{id}/feed', [ProfileController::class, 'feed'])->name('profile.feed');
    Route::get('/profile/{id}/feed/load', [FeedController::class, 'loadProfile'])->name('profile.feed.load');
    Route::get('/posts/crear', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
});

//API ejercicios
Route::get('/api/exercises', function () {
    return response()->json(
        \App\Models\Exercise::select('id', 'name', 'muscle_group', 'image')->orderBy('name')->get()
    );
})->middleware('auth');

//Admin
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/users/{id}/ban', [AdminController::class, 'ban'])->name('admin.ban');
    Route::post('/users/{id}/unban', [AdminController::class, 'unban'])->name('admin.unban');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
});