<?php

use Illuminate\Support\Facades\Route;
use Shahadh\TemporaryLinks\Http\Controllers\TemporaryLinkController;

Route::group([
    'prefix' => config('temporary-links.routes.prefix'),
    'middleware' => config('temporary-links.routes.middleware')
], function () {
    Route::get('/{token}', [TemporaryLinkController::class, 'access'])
        ->name('temporary-links.access');
});