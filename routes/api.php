<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/users/{id}', 'App\Http\Controllers\UserController@show');
Route::get('/users/details/{id}', 'App\Http\Controllers\UserController@showDetails');

Route::get('/eventos/{id}', 'App\Http\Controllers\EventoController@show');

Route::prefix('v1')->group(function(){
    //Todo lo que haya en este grupo se accederá escribiendo ~/api/v1/*
    Route::post('login', [AuthController::class, 'authenticate']);
   
    Route::group(['middleware' => ['jwt.verify']], function(){
        //Todo lo que haya en este grupo requiere autenticación de usuario
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
