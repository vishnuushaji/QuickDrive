<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\DashboardController;

// CORS headers for all API routes (simplified for Bearer token auth)
Route::middleware(function ($request, $next) {
    $response = $next($request);
    $response->headers->set('Access-Control-Allow-Origin', 'https://quickdrive-1.onrender.com');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    return $response;
});

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    
    // Users (Super Admin only)
    Route::middleware('role:super_admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
    
    // Projects
    Route::apiResource('projects', ProjectController::class);
    
    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::patch('/tasks/{task}/approve', [TaskController::class, 'approve']);
    Route::patch('/tasks/{task}/reject', [TaskController::class, 'reject']);
});