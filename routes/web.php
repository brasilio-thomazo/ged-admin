<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::post('/login', [ProfileController::class, 'login'])->name('auth.login');
Route::get('me', [ProfileController::class, 'me'])->middleware(['auth:web'])->name('profile');
Route::post('logout', [ProfileController::class, 'logout'])->middleware(['auth:web'])->name('logout');

Route::get('/', fn () => view('app'))->name("home");
Route::get('/php-info', fn () => phpinfo())->name('php-info');

Route::controller(GroupController::class)->group(function () {
    Route::get("/group", 'index')->name('group.web.index');
    Route::post("/group", 'store')->name('group.web.store');
    Route::delete("/group", 'destroy')->name('group.web.destroy');
    Route::get("/group/{group}", 'show')->name('group.web.show');
    Route::put("/group/{group}", 'update')->name('group.web.update');
});

Route::controller(UserController::class)->group(function () {
    Route::get("/user", 'index')->name('user.web.index');
    Route::post("/user", 'store')->name('user.web.store');
    Route::delete("/user", 'destroy')->name('user.web.destroy');
    Route::get("/user/{user}", 'show')->name('user.web.show');
    Route::put("/user/{user}", 'update')->name('user.web.update');
});

Route::controller(ClientController::class)->group(function () {
    Route::get("/client", 'index')->name('client.web.index');
    Route::post("/client", 'store')->name('client.web.store');
    Route::delete("/client", 'destroy')->name('client.web.destroy');
    Route::get("/client/{client}", 'show')->name('client.web.show');
    Route::put("/client/{client}", 'update')->name('client.web.update');
});

Route::controller(AppController::class)->group(function () {
    Route::get("/app", 'index')->name('app.web.index');
    Route::post("/app", 'store')->name('app.web.store');
    Route::delete("/app", 'destroy')->name('app.web.destroy');
    Route::get("/app/{client}", 'show')->name('app.web.show');
    Route::put("/app/{client}", 'update')->name('app.web.update');
});


// Route::post('app/{app}/test', [AppController::class, 'test'])->name('app.test');
// Route::post('app/{app}/install', [AppController::class, 'install'])->name('app.test');
// Route::post('app/{app}/start', [AppController::class, 'start'])->name('app.start');
// Route::put('app/{app}/start', [AppController::class, 'started'])->name('app.started');
// Route::put('app/{app}/install', [AppController::class, 'installed'])->name('app.installed');
