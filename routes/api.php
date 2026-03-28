<?php

use App\Http\Controllers\Api\BusinessApiController;
use App\Http\Controllers\Api\DuplicateApiController;
use App\Http\Controllers\Api\MergeApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/businesses', [BusinessApiController::class, 'index']);
    Route::get('/duplicates', [DuplicateApiController::class, 'index']);
    Route::post('/merge', [MergeApiController::class, 'store']);
});
