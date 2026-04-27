<?php

use Illuminate\Support\Facades\Route;

//landing
Route::get('/', function () {
    return view('home');
});
//dashboard (solo logueado)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');
//registro
Route::get('/register', function () {
    return view('auth.register');
});

Route::post('/register', function () {
    // aquí irá la lógica de crear usuario
});
//inicio de sesión
Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', function () {
    // aquí irá la lógica de login
});
//cerrar sesión
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
});