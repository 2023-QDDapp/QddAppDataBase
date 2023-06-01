<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\UserControllerApi;
use App\Http\Controllers\V1\RegisterApiController;
use App\Http\Controllers\V1\EventoControllerApi;
use App\Http\Controllers\V1\CategoriaControllerApi;
use App\Http\Controllers\V1\AuthControllerApi;


Route::get('/users/{id}/following', [UserControllerApi::class, 'showFollowing']); //Muestra los seguidos de un usuario
Route::get('/events/filter', [EventoControllerApi::class, 'filtrar']); // Filtro de búsqueda
Route::get('/events/{id}', [EventoControllerApi::class, 'showDetailEvent']); // Muestra el detalle de un evento
Route::get('/users/{id}/pantallaseguidos', [UserControllerApi::class, 'pantallaSeguidos']); // Muestra los eventos de quien sigues

Route::get('/users/{id}', [UserControllerApi::class, 'show']); // Muestra los datos de un usuario

Route::get('/categorias', [CategoriaControllerApi::class, 'index']); // Muestra todas las categorias

Route::get('/users/{id}/events', [UserControllerApi::class, 'showEventosUser']); // Muestra eventos de un usuario
Route::get('/users/{id}/parati', [UserControllerApi::class, 'pantallaParaTi']); //Eventos para ti
//Route::get('/events', [EventoControllerApi::class, 'index']); // Muestra todos los eventos

//Route::post('/users/follow', [UserControllerApi::class, 'follow']); // Seguir a un usuario
//Route::post('/users/unfollow', [UserControllerApi::class, 'unfollow']); // Dejar de seguir a un usuario

Route::get('users/{id}/historial', [UserControllerApi::class, 'showHistorial']); // Historial de un usuario

//Notificaciones
Route::post('/users/events/join', [UserControllerApi::class, 'unirseEvento']); // Solicitar unirse a un evento
Route::post('/events/{eventoId}/aceptar/{userId}', [UserControllerApi::class, 'eventoAceptado']); // Aceptar usuario a un evento
Route::post('/events/{eventoId}/denegar/{userId}', [UserControllerApi::class, 'eventoCancelado']); // Denegar usuario en un evento

Route::post('/resenas', [ResenaControllerApi::class, 'store']); // Crear una reseña

//Ruta para el registro y verificación del email
Route::post('/register', [RegisterApiController::class, 'register']);
Route::get('/verify-email/{id}/{token}', [RegisterApiController::class, 'verifyEmail'])->name('api.verify.email');
Route::post('/continue/register/{id}', [RegisterApiController::class, 'continueRegister']); // Continúa el registro una vez se ha verificado el email

Route::post('/loginApi', [AuthControllerApi::class, 'login']);

Route::middleware('auth:api')->group(function () {

    
    // Usuarios
    Route::get('/categorias', [CategoriaControllerApi::class, 'index']); // Muestra todas las categorias
    //Route::get('/users/{id}', [UserControllerApi::class, 'show']); // Muestra los datos de un usuario
    Route::put('/users/{id}/edit', [UserControllerApi::class, 'update']); // Edita un usuario
    Route::delete('/users/{id}', [UserControllerApi::class, 'destroy']); // Elimina un usuario

    // Eventos
    Route::get('/events', [EventoControllerApi::class, 'index']); // Muestra todos los eventos
    //Route::get('/events/{id}', [EventoControllerApi::class, 'show']); // Muestra eventos con participantes*
    Route::post('/events', [EventoControllerApi::class, 'store']); // Crea un evento
    Route::put('/events/{id}/edit', [EventoControllerApi::class, 'update']); // Edita un evento
    Route::delete('/events/{id}', [EventoControllerApi::class, 'destroy']); // Elimina un evento

    //seguir usuario/dejar de seguir a usuario
    Route::post('/users/{userId}/follow', [UserControllerApi::class, 'followUser']);
    Route::post('/users/{userId}/unfollow', [UserControllerApi::class, 'unfollowUser']);

    Route::post('/logout', [AuthControllerApi::class, 'logout']); // Cerrar sesión
    
});

Route::middleware('jwt.refresh')->get('/token/refresh', [UserControllerApi::class, 'refresh']);
