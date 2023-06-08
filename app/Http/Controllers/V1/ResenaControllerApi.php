<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Resena;
use App\Models\EventoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResenaControllerApi extends Controller
{
    public function store(Request $request, $eventoId)
    {
        $user = $request->user();

        // Validar si el evento ha terminado y el usuario ha asistido
        $eventoUsuario = EventoUser::where('evento_users.user_id', $user->id)
            ->where('evento_users.evento_id', $eventoId)
            ->where('evento_users.estado', 1)
            ->join('eventos', 'eventos.id', '=', 'evento_users.evento_id')
            ->join('users', 'users.id', '=', 'eventos.user_id')
            ->first();

        if (!$eventoUsuario || $eventoUsuario->fecha_hora_fin > now()) {
            return response()->json([
                'mensaje' => 'No puedes dejar una reseña en este evento'
            ], 400);
        }

        // Verificar si el usuario ya ha dejado una reseña para el mismo receptor
        $existeResena = Resena::where('id_usuario_emisor', $user->id)
        ->where('id_usuario_receptor', $eventoUsuario->user_id)
        ->exists();

        if ($existeResena) {
            return response()->json([
                'mensaje' => 'Ya has dejado una reseña en este evento'
            ], 400);
        }

        $campo = [
            'valoracion' => 'required|numeric|between:0.5,5'
        ];

        $mensaje = [
            'between' => 'La valoración debe ser entre 0.5 y 5'
        ];

        $resena = new Resena;
        $resena->id_usuario_emisor = $user->id;
        $resena->id_usuario_receptor = $eventoUsuario->user_id;
        $resena->mensaje = $request->mensaje;
        $resena->valoracion = $request->valoracion;

        $validator = Validator::make($request->all(), $campo, $mensaje);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en los datos proporcionados',
                'errores' => $validator->errors()
            ], 400);
        }

        $resena->save();

        return response()->json([
            'mensaje' => 'Se ha publicado la reseña',
            'resena' => $resena
        ]);
    }
}
