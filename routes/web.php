<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TypeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('locations', LocationController::class);
    Route::get('/map', [LocationController::class, 'map'])->name('map');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/locations/partials/table', [LocationController::class, 'tablePartial'])->name('locations.partials.table');
    Route::resource('types', TypeController::class);
    Route::post('/locations/import', [LocationController::class, 'import'])->name('locations.import');
});

// Admin Dashboard
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UsersController::class);
    Route::resource('sites', SiteController::class);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Route::middleware('auth')->group(function () {});

require __DIR__ . '/auth.php';
