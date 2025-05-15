<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;

// City API routes - public, no middleware
Route::get('/cities/search', [CityController::class, 'search']);
Route::post('/cities', [CityController::class, 'store']);
