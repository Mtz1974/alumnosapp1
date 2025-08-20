<?php

use App\Http\Controllers\AlumnoController;
//use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Ruta para exportar PDF de alumnos
Route::get('/alumnos/pdf', [AlumnoController::class, 'exportarPDF'])->name('alumnos.pdf');

Route::view('/', 'welcome');

// routes/web.php
Route::get('dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil (opcional, si usás Breeze Jetstream)
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Auth routes (login, register, etc.)
require __DIR__ . '/auth.php';
