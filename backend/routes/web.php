<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Default welcome page
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
require __DIR__.'/auth.php';

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard routes (both /dashboard and /admin/dashboard point to same)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin routes with prefix
    Route::prefix('admin')->name('admin.')->group(function () {
        // Super Admin only routes
        Route::middleware(['super_admin'])->group(function () {
            Route::resource('users', UserController::class);
        });

        // All authenticated users can access
        Route::resource('projects', ProjectController::class);
        Route::resource('tasks', TaskController::class);
    });
});