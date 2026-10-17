<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Fallback also sees unknown /api/* URLs; those must stay a JSON 404, not the SPA shell.
Route::fallback(function (Request $request) {
    abort_if($request->is('api/*'), 404);

    return view('app');
});

// Route::fallback() only answers GET and HEAD, so other methods on an unknown /api/* URL would be a 405.
Route::match(['POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], '{any}', fn () => abort(404))
    ->where('any', 'api/.*')
    ->fallback();
