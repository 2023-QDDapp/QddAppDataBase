<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Resena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResenaControllerApi extends Controller
{
    public function store(Request $request, $id)
    {
        $user = $request->user();

        $campo = [
            'valoracion' => 'required|numeric|between:0.5,5'
        ];

        $mensaje = [
            'between' => 'La valoración debe ser entre 0.5 y 5'
        ];

        $resena = new Resena;
        $resena->id_usuario_emisor = $user->id;
        $resena->id_usuario_receptor = $id;
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
