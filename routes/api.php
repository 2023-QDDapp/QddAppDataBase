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


// Usuarios
Route::get('/users/{id}', 'App\Http\Controllers\V1\UserControllerApi@show'); // Muestra los datos de un usuario*
Route::post('/users', 'App\Http\Controllers\V1\UserControllerApi@store'); // Crea un nuevo usuario
Route::delete('/users/{id}', 'App\Http\Controllers\V1\UserControllerApi@destroy'); // Elimina un usuario
Route::post('/users/categoria', 'App\Http\Controllers\V1\UserControllerApi@categorias'); // Añade una categoría a un usuario

// Eventos
Route::get('/eventos', 'App\Http\Controllers\V1\EventoControllerApi@index'); // Muestra todos los eventos
Route::get('/eventos/categorias/{id}', 'App\Http\Controllers\V1\EventoControllerApi@eventosPorCategoria'); // Muestra los eventos de una categoría
Route::get('/eventos/{id}', 'App\Http\Controllers\V1\EventoControllerApi@show'); // Muestra eventos con participantes*
Route::post('/eventos', 'App\Http\Controllers\V1\EventoControllerApi@store'); // Crea un evento
Route::delete('/eventos/{id}', 'App\Http\Controllers\V1\EventoControllerApi@destroy'); // Elimina un evento

// Usuarios en eventos
Route::get('/eventos/asistentes/{id}', 'App\Http\Controllers\V1\EventoUserControllerApi@showAsistentes'); // Muestra los asistentes a un evento

// Comentarios
Route::get('/comentarios/{id}', 'App\Http\Controllers\V1\ComentarioControllerApi@show'); // Muestra los comentarios de un evento
Route::post('/comentarios', 'App\Http\Controllers\V1\ComentarioControllerApi@store'); // Crea un nuevo comentario


Route::prefix('v1')->group(function(){
    //Todo lo que haya en este grupo se accederá escribiendo ~/api/v1/*
    Route::post('login', [AuthController::class, 'authenticate']);

   
    Route::group(['middleware' => ['jwt.verify']], function(){
        //Todo lo que haya en este grupo requiere autenticación de usuario
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
