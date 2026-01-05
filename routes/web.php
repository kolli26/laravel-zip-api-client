<?php

use App\Http\Controllers\CountyController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// County routes
Route::get('/counties', [CountyController::class, 'index'])->name('counties.index');
Route::get('/counties/{id}', [CountyController::class, 'show'])->name('counties.show');
Route::get('/counties/{id}/edit', [CountyController::class, 'edit'])->name('counties.edit');
Route::get('/counties/create', [CountyController::class, 'create'])->name('counties.create');
Route::post('/counties', [CountyController::class, 'store'])->name('counties.store');
Route::put('/counties/{id}', [CountyController::class, 'update'])->name('counties.update');
Route::delete('/counties/{id}', [CountyController::class, 'destroy'])->name('counties.destroy');
Route::get('/counties/export/csv', [CountyController::class, 'exportCsv'])->name('counties.export.csv');
Route::get('/counties/export/pdf', [CountyController::class, 'exportPdf'])->name('counties.export.pdf');

// City routes
Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
Route::get('/cities/{id}', [CityController::class, 'show'])->name('cities.show');
Route::get('/cities/{id}/edit', [CityController::class, 'edit'])->name('cities.edit');
Route::get('/cities/create', [CityController::class, 'create'])->name('cities.create');
Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
Route::put('/cities/{id}', [CityController::class, 'update'])->name('cities.update');
Route::delete('/cities/{id}', [CityController::class, 'destroy'])->name('cities.destroy');
Route::get('/cities/export/csv', [CityController::class, 'exportCsv'])->name('cities.export.csv');
Route::get('/cities/export/pdf', [CityController::class, 'exportPdf'])->name('cities.export.pdf');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
