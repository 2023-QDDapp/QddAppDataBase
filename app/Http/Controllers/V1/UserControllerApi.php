<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\CategoriaUser;
use App\Models\Evento;
use App\Models\Follower;
use App\Models\Resena;
use App\Notifications\AcceptedEventNotification;
use App\Notifications\JoinEventNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    // Crear un usuario
    public function store(Request $request)
    {
        // Escribimos los campos que se van a validar
        $campo = [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:9|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'nullable|string|min:6',
            'fecha_nacimiento' => 'required|date',
            'biografia' => 'required|string|max:500',
            'foto' => 'required|string',
            'categorias' => 'required|array|size:3'
        ];

        // Con el mensaje de error correspondiente
        $mensaje = [
            'required' => 'El campo :attribute es obligatorio',
            'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
            'min' => 'La contraseña no puede ser menor de :min caracteres',
            'size' => 'No puedes elegir más de tres categorías'
        ];

        // Almacenamos los datos
        $user = new User;
        $user->nombre = $request->nombre;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        $user->biografia = $request->biografia;
        $categoriasSeleccionadas = $request->input('categorias');
        
        // Guardamos la foto
        if ($request->has('foto')) {
            $base64Image = $request->input('foto');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/user/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);
            $user->foto = 'img/user/' . $fileName;
        }

        // Validamos y guardamos
        $this->validate($request, $campo, $mensaje);
        $user->save();

        // Modificamos la foto
		$fotoUrl = null;
		if ($user->foto) {
			$fotoUrl = asset('storage/' . $user->foto);
		}

        // Añadimos las categorías elegidas al usuario
        $user->categorias()->attach($categoriasSeleccionadas);

        $data = [
            'mensaje' => 'El usuario ha sido registrado correctamente',
            'id' => $user->id,
			'nombre' => $user->nombre,
			'foto' => $fotoUrl,
            'email' => $user->email,
            'password' => $user->password,
			'fecha_nacimiento' => $user->fecha_nacimiento,
			'biografia' => $user->biografia,
			'intereses' => $categoriasSeleccionadas,
        ];

        // Devolvemos el usuario creado
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Mostrar un usuario
    public function show($id)
	{
        // Obtenemos un usuario
		$user = User::select('users.id', 'users.nombre', 'users.foto', 'users.biografia', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'))
			->where('users.id', $id)
			->first();

        // Comprobamos que existe
		if (!$user) {
			return response()->json([
				'mensaje' => 'El usuario no existe'
			], 404);
		}

        // Modificamos la foto
		$fotoUrl = null;
		if ($user->foto) {
			$fotoUrl = asset('storage/' . $user->foto);
		}

        // Obtenemos sus categorías elegidas
		$categorias = Categoria::select('categorias.id', 'categorias.categoria')
			->join('categoria_users', 'categorias.id', '=', 'categoria_users.categoria_id')
			->where('categoria_users.user_id', $id)
			->get();

        // Obtenemos las reseñas que otros usuarios le han dejado
		$resenas = Resena::select('users.id AS id_usuario', 'users.nombre AS nombre_usuario', 'users.foto', 'resenas.mensaje')
			->join('users', 'users.id', '=', 'resenas.id_usuario_emisor')
			->where('resenas.id_usuario_receptor', $id)
			->get();

        //Transformamos las imágenes de los usuarios que mandan reseñas
        $resenas->transform(function ($resena) {
            $resena->foto = url('storage/' . $resena->foto);
            return $resena;
        });

        // Guardamos los datos
		$datosUsuario = [
			'id' => $user->id,
			'nombre' => $user->nombre,
			'foto' => $fotoUrl,
			'edad' => $user->edad,
			'biografia' => $user->biografia,
			'intereses' => $categorias,
			'valoraciones' => $resenas
		];

        // Devolvemos el objeto
		return response()->json($datosUsuario);
	}

    // Listado de eventos creados por un usuario
    public function showEventosUser($id)
    {
        // Obtenemos los datos de un usuario por la ID
        $user = User::select('users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'))
            ->where('users.id', $id)
            ->first();

        // Comprobamos que el usuario existe
        if (!$user) {
            return response()->json([
                'mensaje' => 'El usuario no existe'
            ], 404);
        }

        // Cambiamos la URL de la imagen
        $fotoUrl = null;
        if ($user->foto_organizador) {
            $fotoUrl = asset('storage/' . $user->foto_organizador);
        }

        // Obtenemos los eventos que ha creado el usuario
        $eventos = Evento::select('eventos.id AS id_evento', 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.categoria')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->where('eventos.user_id', $id)
            ->get();

        // Cambiamos de nuevo la ruta de la imagen
        $eventos->transform(function ($evento) {
            $evento->imagen_evento = url('storage/' . $evento->imagen_evento);
            return $evento;
        });

        // Guardamos los datos correspondientes
        $datosUsuario = [
			'id' => $user->id_organizador,
			'organizador' => $user->organizador,
			'foto' => $fotoUrl,
			'edad' => $user->edad,
            'eventos' => $eventos
		];

        // Devolvemos el objeto
		return response()->json($datosUsuario);
    }

    // Eventos de la pantalla 'Para ti'
    public function pantallaParaTi($id)
    {
        // Obtenemos las categorías elegidas por los usuarios
        $categoria = CategoriaUser::select('categorias.id AS id_categoria', 'categorias.categoria')
            ->join('categorias', 'categorias.id', '=', 'categoria_users.categoria_id')
            ->where('categoria_users.user_id', $id)
            ->get();
        
        // Decodificamos el resultado para obtener un objeto
        $objeto = json_decode($categoria);

        // Lo recorremos y almacenamos las IDs de las categorías en un array
        foreach ($objeto as $objetos) {
            $idCategoria[] = $objetos->id_categoria;
        }

        // Obtenemos los eventos de cada categoría
        $eventos = Evento::select('eventos.id AS id_evento', 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'categorias.id AS id_categoria', 'categorias.categoria')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->orderBy('eventos.fecha_hora_inicio')
            ->whereIn('eventos.categoria_id', $idCategoria)
            ->get();

        // Por cada evento obtenido...
        foreach ($eventos as $evento) {
            // Cambiamos la ruta de la imagen para que devuelva la URL correctamente
            $imagenUrl = null;
            if ($evento->imagen_evento) {
                $imagenUrl = asset('storage/' . $evento->imagen_evento);
            }

            // Obtenemos los datos de los usuarios organizadores
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
                'descripcion' => $evento->descripcion,
                'imagen_evento' => $imagenUrl,
                'fecha_hora_inicio' => $evento->fecha_hora_inicio,
                'fecha_hora_fin' => $evento->fecha_hora_fin,
                'categoria' => $evento->categoria
            ];

            // Y lo almacenamos en un array
            $datosEventos[] = $results;
        }

        // Devolvemos un único objeto con todos los eventos
        return response()->json($datosEventos);
    }

    // Listado de usuarios seguidos
    public function showFollowing($id)
    {
        // Obtenemos el usuario por la ID
        $user = User::select('users.id', 'users.nombre', 'users.foto')
            ->where('users.id', $id)
            ->first();

        // Cambiamos la ruta de la imagen
        $fotoUrl = null;
        if ($user->foto) {
            $fotoUrl = asset('storage/' . $user->foto);
        }

        // Obtenemos los usuarios que sigue
        $seguidos = Follower::select('users.id', 'users.nombre', 'users.foto')
            ->join('users', 'users.id', '=', 'followers.id_usuario_seguido')
            ->where('followers.id_usuario_seguidor', $id)
            ->get();

        // Y los almacenamos en un array vacío provisional
        $datosUsuario = [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'foto' => $fotoUrl,
            'siguiendo' => []
        ];

        // Por cada usuario seguido obtenido...
        foreach ($seguidos as $seguido) {
            // Cambiamos la URL de la imagen
            $fotoSeguidoUrl = null;
            if ($seguido->foto) {
                $fotoSeguidoUrl = asset('storage/' . $seguido->foto);
            }

            // Y añadimos todos los datos modificados al array vacío anterior
            $datosUsuario['siguiendo'][] = [
                'id' => $seguido->id,
                'nombre' => $seguido->nombre,
                'foto' => $fotoSeguidoUrl
            ];
        }

        return response()->json($datosUsuario);
    }

    // Eventos de la pantalla 'Seguidos'
    public function pantallaSeguidos($id)
    {
        // Obtenemos los seguidos de un usuario por la ID
        $seguidos = Follower::select('id_usuario_seguido')
            ->where('id_usuario_seguidor', $id)
            ->get();

        // Decodificamos el resultado para obtener un objeto
        $objeto = json_decode($seguidos);

        // Lo recorremos y almacenamos las IDs de los seguidos en un array
        foreach ($objeto as $objetos) {
            $idSeguido[] = $objetos->id_usuario_seguido;
        }

        // Obtenemos los eventos creados por los usuarios seguidos
        $eventos = Evento::select('eventos.id AS id_evento', 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'categorias.id AS id_categoria', 'categorias.categoria')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->orderBy('eventos.fecha_hora_inicio')
            ->whereIn('eventos.user_id', $idSeguido)
            ->get();

        // Por cada evento obtenido...
        foreach ($eventos as $evento) {
            // Cambiamos la ruta de la imagen
            $imagenUrl = null;
            if ($evento->imagen_evento) {
                $imagenUrl = asset('storage/' . $evento->imagen_evento);
            }

            // Obtenemos los datos del usuario organizador
            $user = User::select('users.id AS id_organizador','users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'))
                ->join('eventos', 'eventos.user_id', '=', 'users.id')
                ->where('eventos.id', $evento->id_evento)
                ->first();

            // Cambiamos también la ruta de sus fotos
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
                'descripcion' => $evento->descripcion,
                'imagen_evento' => $imagenUrl,
                'fecha_hora_inicio' => $evento->fecha_hora_inicio,
                'fecha_hora_fin' => $evento->fecha_hora_fin,
                'categoria' => $evento->categoria
            ];

            // Y los alamcenamos en un array
            $datosEventos[] = $results;
        }

        // Devolvemos un solo objeto con todos los eventos
        return response()->json($datosEventos);
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
        $user = Auth::user();
        
        $campo = [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:9|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'nullable|string|min:6',
            'biografia' => 'required|string|max:500',
            'foto' => 'required|string'
        ];

        $mensaje = [
            'required' => 'El campo :attribute es obligatorio',
            'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
            'min' => 'La contraseña no puede ser menor de :min caracteres'
        ];
        
        $user->nombre = $request->nombre;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->biografia = $request->biografia;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Guardar la foto
        if ($request->has('foto')) {
            $base64Image = $request->input('foto');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/user/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);

            // Eliminar la foto anterior si existe
            if ($user->foto) {
                Storage::delete($user->foto);
            }

            $user->foto = 'img/user/' . $fileName;
        }

        $this->validate($request, $campo, $mensaje);

        $datosUser = request();

        $user = User::where('id', '=', $id)->update($datosUser);

        return response()->json([
            'mensaje' => 'Se ha actualizado el usuario #' . $id,
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user == auth()->user()) {
            User::destroy($id);

            return response()->json([
                'mensaje' => 'Se ha eliminado el usuario #' . $id
            ]);
        }
    }

    public function unirseEvento(Request $request)
    {
        $user = User::find($request->user_id);

        $eventosUsuario = $user->eventos->pluck('id')->toArray();

        if (!in_array($request->evento_id, $eventosUsuario)) {
            //$evento = Evento::find($request->evento_id);
            $evento = Evento::where('id', $request->evento_id)->first();

            if ($evento->tipo == 'público') {
                $user->eventos()->attach($request->evento_id, ['estado' => 1]);

                return response()->json([
                    'mensaje' => 'Te has unido a este evento'
                ]);

            } else {
                $user->eventos()->attach($request->evento_id, ['estado' => 0]);

                // Notificación para el organizador del evento
                $evento->user->notify(new JoinEventNotification($user, $evento));

                return response()->json([
                    'mensaje' => 'Pendiente de que te acepten en este evento'
                ]);
            }

        } else {
            //if ($user->eventosAsistidos()->estado == 1) {
                return response()->json([
                    'mensaje' => 'Ya te has unido a este evento'
                ]);
            /*} else {
                return response()->json([
                    'mensaje' => 'Pendiente de que te acepten en este evento'
                ]);
            }*/
        }
    }

    public function eventoAceptado($eventoId, $userId)
    {
        $evento = Evento::find($eventoId);
        $user = User::find($userId);

        $user->eventos()->updateExistingPivot($eventoId, ['estado' => 1]);

        // Notificar al usuario que su solicitud ha sido aceptada
        $user->notify(new AcceptedEventNotification($evento));

        return response()->json([
            'mensaje' => 'El usuario ha sido aceptado en el evento'
        ]);
    }

    public function eventoCancelado($eventoId, $userId)
    {
        $evento = Evento::find($eventoId);
        $user = User::find($userId);

        $evento->usuarios()->detach($user->id);

        return response()->json([
            'mensaje' => 'El usuario no ha sido aceptado en el evento'
        ]);
    }

    public function follow(Request $request)
    {
        /*$usuario = auth()->user();
        $idSeguido = $request->input('id_usuario_seguido');

        $usuarioSeguido = User::find($idSeguido);

        // Verificar si el usuario ya está siendo seguido
        $isFollowing = Follower::where('id_usuario_seguidor', $usuario->id)
            ->where('id_usuario_seguido', $usuarioSeguido->id)
            ->exists();

        if ($isFollowing) {
            return response()->json([
                'mensaje' => 'Ya sigues a este usuario'
            ], 400);
        }

        $usuario->following()->attach($idSeguido);
        return response()->json([
            'mensaje' => 'Has comenzado a seguir al usuario ' . $usuarioSeguido->id
        ]);*/

        $usuario = auth()->user();
        $idSeguido = $request->input('id_usuario_seguido');

        $usuarioSeguido = User::find($idSeguido);

        // Verificar si se encontró el usuario a seguir
        if ($usuarioSeguido) {
            // Verificar si el usuario ya está siendo seguido
            $isFollowing = Follower::where('id_usuario_seguidor', $usuario->id)
                ->where('id_usuario_seguido', $usuarioSeguido->id)
                ->exists();

            if ($isFollowing) {
                return response()->json([
                    'mensaje' => 'Ya sigues a este usuario'
                ], 400);
            }

            $follower = new Follower();
            $follower->id_usuario_seguidor = $usuario->id;
            $follower->id_usuario_seguido = $usuarioSeguido->id;
            $follower->save();

            return response()->json([
                'mensaje' => 'Has comenzado a seguir al usuario ' . $usuarioSeguido->id
            ]);
        } else {
            return response()->json([
                'mensaje' => 'Usuario a seguir no encontrado'
            ], 404);
}


    }

    public function unfollow(Request $request)
    {
        $usuario = auth()->user();
        $idSeguido = $request->input('id_usuario_seguido');

        $usuarioSeguido = User::find($idSeguido);

        // Verificar si el usuario ya está siendo seguido
        if ($this->isFollowing($usuario)) {
            return false; // El usuario ya está siendo seguido
        }

        $this->following()->detach($usuario->id);
        return response()->json([
            'mensaje' => 'Has dejado de seguir al usuario ' . $usuarioSeguido->id
        ]);
    }

}
