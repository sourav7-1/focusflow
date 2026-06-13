<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudySessionController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {

    Route::post('/study/start', [StudySessionController::class, 'start'])->name('study.start');

    Route::post('/study/stop/{id}', [StudySessionController::class, 'stop'])->name('study.stop');

});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
