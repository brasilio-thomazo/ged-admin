<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::apiResource('user', UserController::class);
Route::apiResource('group', GroupController::class);
Route::apiResource('client', ClientController::class);
Route::apiResource('app', AppController::class);


// Route::post('app/{app}/test', [AppController::class, 'test'])->name('app.test');
// Route::post('app/{app}/install', [AppController::class, 'install'])->name('app.test');
// Route::post('app/{app}/start', [AppController::class, 'start'])->name('app.test');
