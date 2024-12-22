<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ImportedDataController;
use App\Http\Controllers\ImportHistoryController;

Auth::routes();

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Home route
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // User Management
    Route::resource('users', UserController::class);

    // Permissions Management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    // Data Import
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');

    // Imported Data
    Route::get('/imported-data/{type}', [ImportedDataController::class, 'show'])->name('imported-data.show');
    Route::get('/imported-data/{type}/search', [ImportedDataController::class, 'search'])->name('imported-data.search');
    Route::get('/imported-data/{type}/export', [ImportedDataController::class, 'export'])->name('imported-data.export');

    // Import History
    Route::get('/imports-history', [ImportHistoryController::class, 'index'])->name('imports.history');
    Route::get('/imports-history/search', [ImportHistoryController::class, 'search'])->name('imports.history.search');
    Route::get('/imports-history/{import}/logs', [ImportHistoryController::class, 'logs'])->name('imports.history.logs');
});
