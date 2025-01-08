<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', [TaskController::class, 'index']);

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::put('/tasks/{task}', [TaskController::class, 'update']);
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleComplete']);
Route::patch('/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
Route::post('/tasks/complete', action: [TaskController::class, 'completeSelected']);
Route::post('/tasks/delete', [TaskController::class, 'deleteSelected']);