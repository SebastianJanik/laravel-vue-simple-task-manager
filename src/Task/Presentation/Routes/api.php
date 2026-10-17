<?php

declare(strict_types=1);

use App\Task\Presentation\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::whereUuid(['project', 'task'])->group(function (): void {
    Route::get('projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('projects/{project}/tasks', [TaskController::class, 'store']);
    Route::put('projects/{project}/tasks/order', [TaskController::class, 'reorder']);

    Route::patch('tasks/{task}', [TaskController::class, 'update']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
});
