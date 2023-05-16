<?php

namespace App\Http\Controllers\V1;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EventoControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datos = Evento::select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.categoria')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->get();

        return response()->json(
            $datos
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $evento = new Evento;
        $evento->user_id = $request->user_id;
        $evento->categoria_id = $request->categoria_id;
        $evento->titulo = $request->titulo;
        $evento->fecha_hora_inicio = $request->fecha_hora_inicio;
        $evento->fecha_hora_fin = $request->fecha_hora_fin;
        $evento->descripcion = $request->descripcion;
        $evento->imagen = $request->imagen;
        $evento->tipo = $request->tipo;
        $evento->location = $request->location;
        $evento->latitud = $request->latitud;
        $evento->longitud = $request->longitud;
        $evento->save();

        $user = User::find($request->user_id);
        $user->evento_users()->attach($request->evento_id);
        $user->update(['estado' => 1]);

        return response()->json([
            'mensaje' => 'El evento ha sido creado correctamente',
            'evento' => $evento
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDetailEvent($id)
    {
        $event = Evento::select('eventos.id AS id_evento', 'eventos.*', 'users.id AS id_organizador', 'users.nombre', 'users.foto', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'categorias.*')
            ->join('users', 'users.id', '=', 'eventos.user_id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->where('eventos.id', $id)
            ->get();

        $evento = $event->first();

        $datosAsistentes = User::join('evento_users', 'users.id', '=', 'evento_users.user_id')
            ->where('evento_users.evento_id', $id)
            ->where('evento_users.estado', '=', 1)
            ->get(['users.id', 'users.nombre', 'users.foto']);

        if ($evento->tipo == 'público') {
            $datosEvento = [
                'id_evento' => $evento->id_evento,
                'titulo' => $evento->titulo,
                'organizador' => $evento->nombre,
                'id_organizador' => $evento->id_organizador,
                'foto_organizador' => $evento->foto,
                'edad' => $evento->edad,
                'descripcion' => $evento->descripcion,
                'imagen_evento' => $evento->imagen,
                'fecha_hora_inicio' => $evento->fecha_hora_inicio,
                'fecha_hora_fin' => $evento->fecha_hora_fin,
                'location' => $evento->location,
                'latitud' => $evento->latitud,
                'longitud' => $evento->longitud,
                'id_categoria' => $evento->categoria_id,
                'categoria' => $evento->categoria,
                'num_participantes' => $evento->n_participantes,
                'asistentes' => $datosAsistentes
            ];
        } else {
            $datosEvento = [
                'id_evento' => $evento->id_evento,
                'titulo' => $evento->titulo,
                'organizador' => $evento->nombre,
                'id_organizador' => $evento->id_organizador,
                'foto_organizador' => $evento->foto,
                'edad' => $evento->edad,
                'descripcion' => $evento->descripcion,
                'imagen_evento' => $evento->imagen,
                'fecha_hora_inicio' => $evento->fecha_hora_inicio,
                'fecha_hora_fin' => $evento->fecha_hora_fin,
                'location' => $evento->location,
                'id_categoria' => $evento->categoria_id,
                'categoria' => $evento->categoria,
                'num_participantes' => $evento->n_participantes,
                'asistentes' => $datosAsistentes
            ];
        }

        return response()->json(
            $datosEvento
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Evento::destroy($id);

        return response()->json([
            'mensaje' => 'Se ha eliminado el evento #' . $id
        ]);
    }
}
