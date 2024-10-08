<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;


Route::get('/', [MainController::class, 'ShowIndex'])->name('ShowIndex');
//Route::post('/', [MainController::class, 'AddProduct'])->name('AddProduct');
Route::post('/update/{id}', [MainController::class, 'CheckProduct'])->name('CheckProduct');

Route::post('/', [MainController::class, 'AddWord'])->name('AddWord');
Route::delete('/', [MainController::class, 'DeleteWord'])->name('DeleteWord');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
