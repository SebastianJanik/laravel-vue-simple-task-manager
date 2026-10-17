<?php

declare(strict_types=1);

use App\Project\Presentation\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('projects', [ProjectController::class, 'index']);
Route::post('projects', [ProjectController::class, 'store']);

Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->whereUuid('project');
