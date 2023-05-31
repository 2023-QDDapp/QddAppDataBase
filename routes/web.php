<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\EventoUserController;
use App\Http\Controllers\FollowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/verify-email/{id}/{token}', [RegisterApiController::class, 'verifyEmail'])->name('verification.verify');

Route::middleware(['auth'])->group(function () {

    Route::resource('admins', AdminController::class);
    Route::resource('users', UserController::class);
    Route::resource('events', EventoController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('resenas', ResenaController::class);
    Route::resource('eventousers', EventoUserController::class);
    Route::resource('follows', FollowController::class);

});

Auth::routes();
