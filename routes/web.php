<?php

use App\Http\Controllers\ChirpController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//send anything from /chirps to chirpController clas
Route::resource('chirps', ChirpController::class) //assumes CRUD (generates 7 routes)
    ->only(['index', 'store', 'edit', 'update', 'destroy', 'show'])
    ->middleware(['auth', 'verified']);

//routes for likes (not resource route cause not CRUD)
Route::post('/chirps/{chirp}/like', [LikeController::class, 'store'])->middleware(['auth', 'verified']);
Route::delete('/chirps/{chirp}/like', [LikeController::class, 'destroy'])->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
