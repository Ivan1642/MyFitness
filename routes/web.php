<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrainingSessionController;

//landing sin loguear
Route::get('/', function () {
    return view('home');
})->name('home');

// Landing logueado
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

//Autenticaciones
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/training/start', [TrainingSessionController::class, 'start'])->name('training.start');
    Route::post('/training/session', [TrainingSessionController::class, 'store'])->name('training.session');
    Route::post('/training/set', [TrainingSessionController::class, 'storeSet'])->name('training.set');
});