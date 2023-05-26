<?php

namespace App\Http\Controllers\V1;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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
        // Obtenemos todos los eventos
        $eventos = Evento::select('eventos.id AS id_evento', 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'categorias.id AS id_categoria', 'categorias.categoria')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->get();

        // Por cada evento obtenido...
        foreach ($eventos as $evento) {
            // Cambiamos la ruta de la imagen para que devuelva la URL correctamente
            $imagenUrl = null;
            if ($evento->imagen_evento) {
                $imagenUrl = asset('storage/' . $evento->imagen_evento);
            }

            // Obtenemos los datos de los organizadores
            $user = User::select('users.id AS id_organizador','users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'))
                ->join('eventos', 'eventos.user_id', '=', 'users.id')
                ->where('eventos.id', $evento->id_evento)
                ->first();

            // Cambiamos la ruta de la imagen para que devuelva la URL correctamente
            $fotoUrl = null;
            if ($user->foto_organizador) {
                $fotoUrl = asset('storage/' . $user->foto_organizador);
            }

            // Guardamos cada evento con sus datos correspondientes
            $results = [
                'id_evento' => $evento->id_evento,
                'id_organizador' => $user->id_organizador,
                'organizador' => $user->organizador,
                'foto_organizador' => $fotoUrl,
                'edad' => $user->edad,
                'titulo' => $evento->titulo,
                'imagen_evento' => $imagenUrl,
                'inicio' => $evento->fecha_hora_inicio,
                'fin' => $evento->fecha_hora_fin,
                'categoria' => $evento->categoria
            ];

            // Y lo almacenamos en un array
            $datosEventos[] = $results;
        }

        // Devolvemos un único objeto con todos los eventos
        return response()->json($datosEventos);
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
        $evento->tipo = $request->tipo;
        $evento->location = $request->location;
        $evento->latitud = $request->latitud;
        $evento->longitud = $request->longitud;

        // Guardar la foto
        if ($request->has('imagen')) {
            $base64Image = $request->input('imagen');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = 'event_' . time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/event/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);
            $evento->imagen = 'img/event/' . $fileName;
        }

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
    // Muestra la pantalla detalle de un evento
    public function showDetailEvent($id)
    {
        // Obtenemos el evento por la ID
        $eventos = Evento::select('eventos.id AS id_evento', 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'eventos.location', 'eventos.latitud', 'eventos.longitud', 'eventos.tipo', 'categorias.id AS id_categoria', 'categorias.categoria')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->where('eventos.id', $id)
            ->get();

        // Por cada evento obtenido...
        foreach ($eventos as $evento) {
            // Cambiamos la ruta de la imagen para que devuelva la URL correctamente
            $imagenUrl = null;
            if ($evento->imagen_evento) {
                $imagenUrl = asset('storage/' . $evento->imagen_evento);
            }

            // Obtenemos los datos de los organizadores
            $user = User::select('users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'))
                ->join('eventos', 'eventos.user_id', '=', 'users.id')
                ->where('eventos.id', $evento->id_evento)
                ->first();

            // Cambiamos la ruta de la imagen para que devuelva la URL correctamente
            $fotoUrl = null;
            if ($user->foto_organizador) {
                $fotoUrl = asset('storage/' . $user->foto_organizador);
            }

            // Obtenemos los asistentes de cada evento
            $asistentes = User::join('evento_users', 'users.id', '=', 'evento_users.user_id')
                ->where('evento_users.evento_id', $evento->id_evento)
                ->where('evento_users.estado', 1)
                ->get(['users.id', 'users.nombre', 'users.foto']);

            // Por cada asistente...
            foreach ($asistentes as $asistente) {
                // Cambiamos la ruta de la foto
                $fotoAsistenteUrl = null;
                if ($asistente->foto) {
                    $fotoAsistenteUrl = asset('storage/' . $asistente->foto);
                }

                // Y guardamos los datos en un array
                $asistenteData = [
                    'id' => $asistente->id,
                    'nombre' => $asistente->nombre,
                    'foto' => $fotoAsistenteUrl,
                ];

                // Para asignarlos a una variable
                $asistentesData[] = $asistenteData;
            }

            // Contamos los particpantes
            $num_participantes = count($asistentes);

            // Guardamos todos los datos definitivos
            $results = [
                'id_evento' => $evento->id_evento,
                'id_organizador' => $user->id_organizador,
                'organizador' => $user->organizador,
                'foto_organizador' => $fotoUrl,
                'edad' => $user->edad,
                'titulo' => $evento->titulo,
                'imagen_evento' => $imagenUrl,
                'inicio' => $evento->fecha_hora_inicio,
                'fin' => $evento->fecha_hora_fin,
                'location' => $evento->location,
                'latitud' => $evento->latitud,
                'longitud' => $evento->longitud,
                'tipo' => $evento->tipo,
                'num_participantes' => $num_participantes,
                'categoria' => $evento->categoria,
                'asistentes' => $asistentesData
            ];

            // Y los almacenamos en otra variable
            $datosEventos[] = $results;
        }

        // Devolvemos un único objeto con todos los eventos
        return response()->json($datosEventos);
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

        $eventos = $query->get();

        return response()->json($eventos);
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
