<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\UserControllerApi;
use App\Http\Controllers\V1\RegisterApiController;
use App\Http\Controllers\V1\EventoControllerApi;
use App\Http\Controllers\V1\CategoriaControllerApi;
use App\Http\Controllers\V1\AuthControllerApi;
use App\Http\Controllers\V1\ResenaControllerApi;

// Ruta para el registro y verificación del email
Route::post('/register', [RegisterApiController::class, 'register']);
Route::get('/verify-email/{id}/{token}', [RegisterApiController::class, 'verifyEmail'])->name('api.verify.email');
Route::post('/continue/register/{id}', [RegisterApiController::class, 'continueRegister']); // Continúa el registro una vez se ha verificado el email
Route::post('/validate/phone', [RegisterApiController::class, 'verifyPhoneNumber']); // Comprobar si existe el número de teléfono
Route::post('/validate/email', [RegisterApiController::class, 'isEmailVerified']); // Comprobar si el email está verificado

Route::post('/loginApi', [AuthControllerApi::class, 'login']); // Iniciar sesión

Route::middleware('auth:api')->group(function () {
    // Usuarios
    Route::get('/users/{id}', [UserControllerApi::class, 'show']); // Muestra los datos de un usuario
    Route::put('/users/{id}/edit', [UserControllerApi::class, 'update']); // Edita un usuario
    Route::delete('/users/{id}', [UserControllerApi::class, 'destroy']); // Elimina un usuario
    Route::get('/users/{id}/historial', [UserControllerApi::class, 'showHistorial']); // Historial de un usuario
    Route::get('/users/{id}/events', [UserControllerApi::class, 'showEventosUser']); // Muestra eventos de un usuario
    Route::get('/users/{id}/parati', [UserControllerApi::class, 'pantallaParaTi']); //Eventos para ti
    Route::get('/users/{id}/pantallaseguidos', [UserControllerApi::class, 'pantallaSeguidos']); // Muestra los eventos de quien sigues
    Route::get('/users/{id}/following', [UserControllerApi::class, 'showFollowing']); // Muestra los seguidos de un usuario
    Route::post('/users/{id}/follow', [UserControllerApi::class, 'followUser']); // Seguir usuario
    Route::post('/users/{id}/unfollow', [UserControllerApi::class, 'unfollowUser']); // Dejar de seguir usuario
    Route::post('/users/{id}/verifyFollowing', [UserControllerApi::class, 'verifyFollowing']); // Comprueba si sigues a un usuario

    Route::post('/events/{eventoId}/join', [UserControllerApi::class, 'unirseEvento']); // Solicitar unirse a un evento
    
    // Eventos
    Route::get('/events', [EventoControllerApi::class, 'index']); // Muestra todos los eventos
    Route::get('/events/filter', [EventoControllerApi::class, 'filtrar']); // Filtro de búsqueda
    Route::get('/events/{id}', [EventoControllerApi::class, 'showDetailEvent']); // Muestra el detalle de un evento
    Route::post('/events', [EventoControllerApi::class, 'store']); // Crea un evento
    Route::put('/events/{id}/edit', [EventoControllerApi::class, 'update']); // Edita un evento
    Route::delete('/events/{id}', [EventoControllerApi::class, 'destroy']); // Elimina un evento
    Route::get('/events/filter', [EventoControllerApi::class, 'filtrar']); // Filtro de búsqueda
    Route::get('events/{eventoId}/relationUser', [EventoControllerApi::class, 'userRelationEvent']); // Relación del usuario con el evento
    Route::post('/events/{eventoId}/abandonar', [UserControllerApi::class, 'abandonarEvento']); // Salir de un evento

    Route::post('/events/{eventoId}/aceptar/{userId}', [UserControllerApi::class, 'eventoAceptado']); // Aceptar usuario a un evento
    Route::post('/events/{eventoId}/denegar/{userId}', [UserControllerApi::class, 'eventoCancelado']); // Denegar usuario en un evento

    // Categorías
    Route::get('/categorias', [CategoriaControllerApi::class, 'index']); // Muestra todas las categorias

    // Reseñas
    Route::post('/resenas/{eventId}', [ResenaControllerApi::class, 'store']); // Crear una reseña
    
    // Cerrar sesión
    Route::post('/logout', [AuthControllerApi::class, 'logout']); // Cerrar sesión
});

Route::middleware('jwt.refresh')->get('/token/refresh', [UserControllerApi::class, 'refresh']);
