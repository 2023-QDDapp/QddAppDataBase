<?php

namespace App\Http\Controllers\V1;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EventoControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $evento = Evento::select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.categoria')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->paginate(20);

        return response()->json(
            $evento
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
        $campo = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'imagen' => 'required|string|mimes:png,jpg,jpeg',
            'tipo' => 'required|string',
            'location' => 'required|string',
            'longitud' => 'required|numeric|between:-180,180',
            'latitud' => 'required|string|between:-90,90',
        ];

        $mensaje = [
            'required' => 'El campo :attribute es obligatorio',
            'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
            'between' => 'El campo :attribute debe estar entre :between'
        ];

        $evento = new Evento;
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

        // Guardar la foto
        /* if ($request->has('imagen')) {
            $base64Image = $request->input('imagen');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = 'event_' . time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/event/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);
            $evento->foto = 'img/event/' . $fileName;
        } */

        $this->validate($request, $campo, $mensaje);
        $evento->save();

        $user = User::find($request->user_id);
        $user->eventos()->attach($request->evento_id, ['estado' => 1]);

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

        return response()->json(
            $datosEvento
        );
    }

    public function filtrar(Request $request)
    {
        $query = Evento::query();

        // Título
        if ($request->has('titulo')) {
            $query->where('titulo', 'LIKE', '%' . $request->input('titulo') . '%');
        }

        // Categoría
        if ($request->has('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        // Fecha y hora inicio
        if ($request->has('fecha_hora_inicio')) {
            $query->where('fecha_hora_inicio', $request->input('fecha_hora_inicio'));
        }

        // Fecha y hora fin
        if ($request->has('fecha_hora_fin')) {
            $query->where('fecha_hora_fin', $request->input('fecha_hora_fin'));
        }

        // Tipo
        if ($request->has('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        // Location
        if ($request->has('location')) {
            $query->where('location', 'LIKE', '%' . $request->input('location') . '%');
        }

        //

        $eventos = $query->get();

        return response()->json(
            $eventos
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
        $campo = [
            'titulo' => 'required|string|max:255',
            'fecha_hora_inicio' => 'required|datetime',
            'fecha_hora_fin' => 'required|datetime',
            'descripcion' => 'required|string|max:500',
            'imagen' => 'required|string|mimes:png,jpg,jpeg',
            'tipo' => 'required|string',
            'location' => 'required|string',
            'longitud' => 'required|numeric|between:-180,180',
            'latitud' => 'required|string|between:-90,90',
        ];

        $mensaje = [
            'required' => 'El campo :attribute es obligatorio',
            'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
            'between' => 'El campo :attribute debe estar entre :between'
        ];

        $evento = Evento::findOrFail($id);
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

        // Guardar la foto
        if ($request->has('foto')) {
            $base64Image = $request->input('foto');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = 'event_' . time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/event/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);
            $evento->foto = 'img/event/' . $fileName;

            // Eliminar la foto anterior si existe
            if ($evento->foto) {
                Storage::delete($evento->foto);
            }

            $evento->foto = 'img/event/' . $fileName;
        }

        $this->validate($request, $campo, $mensaje);

        if ($evento->user_id == auth()->id()) {
            $evento = Evento::where('id', '=', $id)->update();

            return response()->json([
                'mensaje' => 'Se ha actualizado el evento #' . $id,
                'evento' => $evento
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);

        if ($evento->user_id == auth()->id()) {
            Evento::destroy($id);

            return response()->json([
                'mensaje' => 'Se ha eliminado el evento #' . $id
            ]);
        }
    }
}
