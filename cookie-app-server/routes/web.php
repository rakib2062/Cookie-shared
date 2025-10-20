<?php

use App\Http\Controllers\CookieController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    // 'validate.tracker.origin', 'throttle:60,1'
    ])->group(function () {
    Route::get('/track', [CookieController::class, 'track']);
    Route::get('/redirect-sync', [CookieController::class, 'redirectSync']);
});
