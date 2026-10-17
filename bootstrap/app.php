<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// The two empty hooks are required: they register the default middleware groups and the exception handler.
// Module routes live in the module providers, exception rendering in SharedServiceProvider.
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->create();

$app->useAppPath($app->basePath('src'));

return $app;
