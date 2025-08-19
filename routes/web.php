<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::view('/', 'welcome');

// Dashboard: decide vista según rol
use app\Livewire\AlumnosIndex;

// routes/web.php
Route::get('dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Perfil (opcional, si usás Breeze Jetstream)
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Auth routes (login, register, etc.)
require __DIR__.'/auth.php';
