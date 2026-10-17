<?php

declare(strict_types=1);

namespace App\Shared\Presentation;

use Illuminate\Support\Facades\Route;

final class ApiRoutes
{
    public static function load(string $routesFile): void
    {
        Route::middleware('api')->prefix('api')->group($routesFile);
    }
}
