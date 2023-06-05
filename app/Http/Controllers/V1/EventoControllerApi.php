<?php

namespace App\Http\Controllers\V1;

use App\Models\Evento;
use App\Models\User;
use App\Models\EventoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventoControllerApi extends Controller
{
    // Muestra todos los eventos
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
                'fecha_hora_inicio' => $evento->fecha_hora_inicio,
                'fecha_hora_fin' => $evento->fecha_hora_fin,
                'categoria' => $evento->categoria
            ];

            // Y lo almacenamos en un array
            $datosEventos[] = $results;
        }

        // Devolvemos todos los eventos
        return response()->json($datosEventos);
    }

    public function store(Request $request)
    {
        // Obtener el ID del usuario logeado
        $id_organizador = $request->user()->id;

        $campo = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'imagen' => 'required|string',
            'tipo' => 'required|string',
            'location' => 'required|string',
            'longitud' => 'required|string|between:-180,180',
            'latitud' => 'required|string|between:-90,90',
        ];

        $mensaje = [
            'required' => 'El campo :attribute es obligatorio',
            'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
            'between' => 'El campo :attribute debe estar entre :between'
        ];

        $evento = new Evento;
        $evento->user_id = $id_organizador;
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
            $fileName = time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/event/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);
            $evento->imagen = 'img/event/' . $fileName;
        }

        $validator = Validator::make($request->all(), $campo, $mensaje);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en los datos proporcionados',
                'errores' => $validator->errors()
            ], 400);
        }

        // Modificamos la imagen
		$imagenUrl = null;
		if ($evento->imagen) {
			$imagenUrl = asset('storage/' . $evento->imagen);
		}

        $user = User::find($id_organizador);
        $user->eventos()->attach($evento->id, ['estado' => 1]);

        return response()->json([
            'mensaje' => 'El evento ha sido creado correctamente',
            'id_evento' => $evento->id,
            'id_organizador' => $evento->user_id,
            'titulo' => $evento->titulo,
            'descripcion' => $evento->descripcion,
            'imagen_evento' => $imagenUrl,
            'fecha_hora_inicio' => $evento->fecha_hora_inicio,
            'fecha_hora_fin' => $evento->fecha_hora_fin,
            'location' => $evento->location,
            'latitud' => $evento->latitud,
            'longitud' => $evento->longitud,
            'tipo' => $evento->tipo,
            'id_categoria' => $evento->categoria_id
        ]);
    }

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
                $datosAsistente = [
                    'id' => $asistente->id,
                    'nombre' => $asistente->nombre,
                    'foto' => $fotoAsistenteUrl,
                ];

                // Para asignarlos a una variable
                $asistentesData[] = $datosAsistente;
            }

            // Contamos los particpantes
            $num_participantes = count($asistentes);

            // Guardamos todos los datos definitivos
            $datosEventos = [
                'id_evento' => $evento->id_evento,
                'id_organizador' => $user->id_organizador,
                'organizador' => $user->organizador,
                'foto_organizador' => $fotoUrl,
                'edad' => $user->edad,
                'titulo' => $evento->titulo,
                'descripcion' => $evento->descripcion,
                'imagen_evento' => $imagenUrl,
                'fecha_hora_inicio' => $evento->fecha_hora_inicio,
                'fecha_hora_fin' => $evento->fecha_hora_fin,
                'location' => $evento->location,
                'latitud' => $evento->latitud,
                'longitud' => $evento->longitud,
                'tipo' => $evento->tipo,
                'num_participantes' => $num_participantes,
                'categoria' => $evento->categoria,
                'asistentes' => $asistentesData ?? []
            ];
        }

        // Devolvemos el evento
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

        // Latitud
        if ($request->has('latitud')) {
            $query->where('latitud', $request->input('latitud'));
        }

        // Longitud
        if ($request->has('longitud')) {
            $query->where('longitud', $request->input('longitud'));
        }

        // Obtenemos los eventos filtrados
        $eventos = $query->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->select('eventos.id AS id_evento', 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'eventos.tipo', 'categorias.id AS id_categoria', 'categorias.categoria')
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
                'fecha_hora_inicio' => $evento->fecha_hora_inicio,
                'fecha_hora_fin' => $evento->fecha_hora_fin,
                //'tipo' => $evento->tipo,
                'categoria' => $evento->categoria
            ];

            // Y lo almacenamos en un array
            $datosEventos[] = $results;
        }

        if (!empty($datosEventos)) {
            return response()->json($datosEventos);
        } else {
            return [];
        }
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        // Verificar si el usuario autenticado es el organizador del evento
        if ($evento->user_id != auth()->user()->id) {
            return response()->json([
                'mensaje' => 'No puedes editar este evento porque no eres el organizador'
            ]);
        }

        $campo = [
            'titulo' => 'string|max:255',
            'fecha_hora_inicio' => 'date',
            'fecha_hora_fin' => 'date',
            'descripcion' => 'string|max:500',
            'imagen' => 'string',
            'tipo' => 'string',
            'location' => 'string',
            'longitud' => 'numeric|between:-180,180',
            'latitud' => 'string|between:-90,90',
        ];

        $mensaje = [
            'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
            'between' => 'El campo :attribute debe estar entre :between'
        ];

        $datosEvento = $request->only(array_keys($campo));

        // Validar los datos del evento
        $validator = Validator::make($datosEvento, $campo, $mensaje);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en los datos proporcionados',
                'errores' => $validator->errors()
            ], 400);
        }

        // Guardar la foto
        if ($request->has('foto')) {
            $base64Image = $request->input('foto');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = 'event_' . time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/event/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);

            // Eliminar la foto anterior si existe
            if ($evento->foto) {
                Storage::delete($evento->foto);
            }

            $evento->foto = 'img/event/' . $fileName;
        }

        // Actualizar los datos del evento con los parámetros proporcionados
        $evento->fill($datosEvento);
        $evento->save();

        return response()->json([
            'mensaje' => 'Se ha actualizado el evento #' . $id,
            'evento' => $evento
        ]);
    }

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

    public function userRelationEvent($eventoId)
    {
       $usuarioId = auth()->user()->id;

       // Verifica si el usuario es el organizador del evento
       $esOrganizador = Evento::where('id', $eventoId)
           ->where('user_id', $usuarioId)
           ->exists();

       if ($esOrganizador) {
           // El usuario es el organizador del evento
           $estado = 'organizador';
       } else {
           // Verifica si el usuario está relacionado con el evento como asistente
           $relacion = EventoUser::where('evento_id', $eventoId)
               ->where('user_id', $usuarioId)
               ->first();

           if ($relacion) {
               // El usuario está relacionado con el evento
               $estado = $relacion->estado;
           } else {
               // El usuario no está relacionado con el evento
               $estado = null;
           }
       }

       return response()->json([
           'relacion' => $estado
       ]);
    }
}
