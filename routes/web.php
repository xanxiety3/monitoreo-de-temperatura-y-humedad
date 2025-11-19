<?php

use App\Http\Controllers\ParametrizacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroTemperaturaController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [RegistroTemperaturaController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('usuarios', UserController::class);
    Route::resource('parametros', ParametrizacionController::class)->except(['show']);


    Route::post('/registro', [RegistroTemperaturaController::class, 'storeAmbos'])->name('registros.storeAmbos');
});

require __DIR__ . '/auth.php';
