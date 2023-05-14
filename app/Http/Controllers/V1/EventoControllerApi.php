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
        /*$datos = Evento::select('id', 'titulo', 'descripcion', 'imagen', 'fecha_hora_inicio', 'fecha_hora_fin')
            ->where('id', $id)
            ->with(['users' => function ($query) {
                $query->select('nombre', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'foto')
                ->join('users', 'users.id', '=', 'eventos.user_id')
                ->sum('users.foto');
            }])
            ->get();

        return response()->json(
            $datos
        );*/

        //$evento = Evento::with('creador', 'usuariosAsistentes')->find($id);

        //$objeto = json_decode($evento);

        /* foreach ($objeto as $objetos) {
            $id[] = $objetos->id;
            $user_id[] = $objetos->user_id;
            $categoria_id[] = $objetos->categoria_id;
        } */

        
        /* return response()->json(
            $objeto
        ); */


        // Paso 1: Realiza la primera consulta select
        $evento = DB::table('eventos')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre', 'users.foto', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.categoria')
            ->where('eventos.id', $id)
            ->get();

        // Paso 2: Crea un objeto o array asociativo para almacenar los datos que deseas incluir en el primer objeto JSON
        $datosObjeto1 = [];

        foreach ($evento as $objeto) {
            // Accede a la propiedad "id" en cada objeto individual
            $id = $objeto->id;
            $imagen = $objeto->imagen;
            $descripcion = $objeto->descripcion;
            $location = $objeto->location;
            $latitud = $objeto->latitud;
            $longitud = $objeto->longitud;
            $tipo = $objeto->tipo;
        
            // Agrega los datos del objeto individual al array
            $datosObjeto1[] = [
                'id' => $id,
                'imagen' => $imagen,
                'descripcion' => $descripcion,
                'location' => $location,
                'latitud' => $latitud,
                'longitud' => $longitud,
                'tipo' => $tipo,
            ];
        }

        // Select organizador
        $organizador = DB::table('users')
            ->join('eventos', 'eventos.user_id', '=', 'users.id')
            ->select('users.id', 'users.nombre', 'users.foto')
            ->get();

        $datosObjeto2 = [
            'id' => $organizador->id,
            'nombre' => $organizador->nombre,
            'foto' => $organizador->foto
        ];

        // Paso 3: Realiza la segunda consulta select
        $asistentes = DB::table('evento_users')
            ->join('users', 'users.id', '=', 'evento_users.user_id')
            ->join('eventos', 'eventos.id', '=', 'evento_users.evento_id')
            ->select('users.id', 'users.nombre', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'users.foto')
            ->where('eventos.id', $id)
            ->where('evento_users.estado', '=', 1)
            ->get();

        // Paso 4: Crea un objeto o array asociativo para almacenar los datos que deseas incluir en el segundo objeto JSON
        $datosObjeto3 = [
            'id' => $asistentes->id,
            'nombre' => $asistentes->nombre,
            'foto' => $asistentes->foto
        ];

        

        // Paso 6: Combina los objetos o arrays creados en los pasos anteriores para formar la estructura deseada
        $resultadoFinal = [
            'evento' => $datosObjeto1,
            'organizador' => $datosObjeto2,
            'asistentes' => $datosObjeto3,
        ];

        // Paso 7: Convierte el resultado final a formato JSON
        $resultadoJSON = json_encode($resultadoFinal);

        // Devuelve el resultado JSON
        return response()->json($resultadoJSON);

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
