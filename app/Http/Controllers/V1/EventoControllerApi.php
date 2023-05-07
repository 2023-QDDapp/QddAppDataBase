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
        $datos = DB::table('eventos')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre', 'users.foto', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.categoria')
            ->get();

        return response()->json([
            'datos' => $datos
        ]);
    }

    public function eventosPorCategoria($id)
    {
        $datos = DB::table('eventos')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre', 'users.foto', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.categoria')
            ->where('eventos.categoria_id', $id)
            ->get();

        return response()->json([
            'datos' => $datos
        ]);
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
    public function show($id)
    {
        /* $datos = DB::table('eventos')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre', 'users.foto', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'eventos.location', 'eventos.latitud', 'eventos.longitud', 'categorias.categoria', DB::raw('COUNT(evento_users.user_id) as num_asistentes'))
            ->where('eventos.id', $id)
            ->with(['evento_users' => function ($query) {
                $query->select('evento_users.user_id');
            }])
            ->join('evento_users', 'eventos.id', '=', 'evento_users.evento_id')
            ->groupBy('eventos.id', 'users.id', 'users.nombre', 'users.foto', 'fecha_nacimiento', 'eventos.imagen', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'eventos.location', 'eventos.latitud', 'eventos.longitud', 'categorias.categoria')
            ->get();

        return response()->json([
            'datos' => $datos
        ]); */


        $datos = Evento::select('id', 'titulo', 'descripcion', 'imagen', 'fecha_hora_inicio', 'fecha_hora_fin')
            ->where('id', $id)
            ->with(['users' => function ($query) {
                $query->select('nombre', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'foto')
                ->join('users', 'users.id', '=', 'eventos.user_id')
                ->sum('users.foto');
            }])
            ->get();

        return response()->json([
            'eventos' => $datos
        ]);

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
