<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImageController;

Route::middleware('apikey')->group(function () {
    Route::get('/images', [ImageController::class, 'index']);
    Route::post('/images', [ImageController::class, 'store']);
    Route::delete('/images/{image}', [ImageController::class, 'destroy']);
    Route::get('/images/{image}', [ImageController::class, 'show']);
});