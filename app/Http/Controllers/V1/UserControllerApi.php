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
    public function store(Request $request)
    {
        $campo = [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:9|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'nullable|string|min:6',
            'fecha_nacimiento' => 'required|date',
            'biografia' => 'required|string|max:500',
            'foto' => 'required|string|mimes:png,jpg,jpeg'
        ];

        $mensaje = [
            'required' => 'El campo :attribute es obligatorio',
            'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
            'min' => 'La contraseña no puede ser menor de :min caracteres'
        ];

        $user = new User;
        $user->nombre = $request->nombre;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        $user->biografia = $request->biografia;
        
        // Guardar la foto
        if ($request->has('foto')) {
            $base64Image = $request->input('foto');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = 'user_' . time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/user/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);
            $user->foto = 'img/user/' . $fileName;
        }

        $user->save();

        return response()->json([
            'mensaje' => 'El usuario ha sido registrado correctamente',
            'user' => $user
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
        $user = User::select('users.id', 'users.nombre', 'users.foto', 'users.biografia', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'))
            ->where('users.id', $id)
            ->first();

        if (!$user) {
            return response()->json([
                'mensaje' => 'El usuario no existe'
            ], 404);
        }

        $foto = null;
        if ($user->foto) {
            $filePath = storage_path('app/public/' . $user->foto);
            $fotoData = file_get_contents($filePath);
            $foto = base64_encode($fotoData);
        }

        $categorias = Categoria::select('categorias.id', 'categorias.categoria')
            ->join('categoria_users', 'categorias.id', '=', 'categoria_users.categoria_id')
            ->where('categoria_users.user_id', $id)
            ->get();

        $resenas = Resena::select('users.id AS id_usuario', 'users.nombre AS nombre_usuario', 'users.foto', 'resenas.mensaje')
            ->join('users', 'users.id', '=', 'resenas.id_usuario_emisor')
            ->where('resenas.id_usuario_receptor', $id)
            ->get();

        $datosUsuario = [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'foto' => $foto, // Agregar la foto codificada en base64
            'edad' => $user->edad,
            'biografia' => $user->biografia,
            'intereses' => $categorias,
            'valoraciones' => $resenas
        ];

        return response()->json($datosUsuario);
    }

    public function showEventosUser($id)
    {
        $user = User::select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.categoria')
            ->join('eventos', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->where('eventos.user_id', $id)
            ->get();

        return response()->json(
            $user
        );
    }

    public function pantallaParaTi($id)
    {
        $categoria = CategoriaUser::select('categorias.id AS id_categoria', 'categorias.categoria')
            ->join('categorias', 'categorias.id', '=', 'categoria_users.categoria_id')
            ->where('categoria_users.user_id', $id)
            ->get();
        
        $objeto = json_decode($categoria);

        foreach ($objeto as $objetos) {
            $idCategoria[] = $objetos->id_categoria;
        }

        $eventos = Evento::select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'categorias.id AS id_categoria', 'categorias.categoria')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->orderBy('eventos.fecha_hora_inicio')
            ->whereIn('eventos.categoria_id', $idCategoria)
            ->get();

        return response()->json(
            $eventos
        );
    }

    public function showFollowing($id)
    {
        $user = User::select('users.*', 'followers.id_usuario_seguido')
            ->join('followers', 'users.id', '=', 'followers.id_usuario_seguidor')
            ->where('users.id', $id)
            ->get();

        $usuario = $user->first();
        $seguidos = $user->pluck('id_usuario_seguido')->toArray();
        $datosSeguidos = User::whereIn('id', $seguidos)->get(['id', 'nombre', 'foto']);

        $datosUsuario = [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'foto' => $usuario->foto,
            'siguiendo' => $datosSeguidos
        ];
         
        return response()->json(
            $datosUsuario
        );
    }

    public function pantallaSeguidos($id)
    {
        $seguidos = Follower::select('id_usuario_seguido')
            ->where('id_usuario_seguidor', $id)
            ->get();

        $objeto = json_decode($seguidos);

        foreach ($objeto as $objetos) {
            $idSeguido[] = $objetos->id_usuario_seguido;
        }

        $eventos = Evento::select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.id AS id_categoria', 'categorias.categoria')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->orderBy('eventos.fecha_hora_inicio')
            ->whereIn('eventos.user_id', $idSeguido)
            ->get();

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
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:9|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'nullable|string|min:6',
            'fecha_nacimiento' => 'required|date',
            'biografia' => 'required|string|max:500',
            'foto' => 'required|string|mimes:png,jpg,jpeg'
        ];
        
        $user = User::findOrFail($id);
        $user->nombre = $request->nombre;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        $user->biografia = $request->biografia;

        // Guardar la foto
        if ($request->has('foto')) {
            $base64Image = $request->input('foto');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = 'user_' . time() . '.jpg'; // Nombre del archivo
            $filePath = 'public/img/user/' . $fileName; // Ruta donde se guarda la foto
            Storage::put($filePath, $data);

            // Eliminar la foto anterior si existe
            if ($user->foto) {
                Storage::delete($user->foto);
            }

            $user->foto = 'img/user/' . $fileName;
        }

        $mensaje = [
            'required' => 'El campo :attribute es obligatorio',
            'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
            'min' => 'La contraseña no puede ser menor de :min caracteres'
        ];

        $this->validate($request, $campo, $mensaje);

        $datosUser = request()->only(['nombre', 'telefono', 'email', 'password', 'biografia', 'foto']);

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
        User::destroy($id);

        return response()->json([
            'mensaje' => 'Se ha eliminado el usuario #' . $id
        ]);
    }

    public function addCategorias(Request $request) 
    {
        $user = User::find($request->user_id);

        $categoriasUsuario = $user->categorias->pluck('id')->toArray();

        if (count($user->categorias) < 3) {
            if (!in_array($request->categoria_id, $categoriasUsuario)) {
                $user->categorias()->attach($request->categoria_id);
            
                return response()->json([
                    'mensaje' => 'La categoría se ha añadido a tus intereses'
                ]);

            } else {
                return response()->json([
                    'mensaje' => 'Esa categoría ya la has añadido'
                ]);
            }

        } else {
            return response()->json([
                'mensaje' => 'No puedes tener más de tres categorías'
            ]);
        }
    }

    public function deleteCategorias(Request $request)
    {
        $user = User::find($request->user_id);

        $user->categorias()->detach($request->categoria_id);

        return response()->json([
            'mensaje' => 'La categoría se ha eliminado de tus intereses'
        ]);
    }

    public function unirseEvento(Request $request)
    {
        $user = User::find($request->user_id);

        $eventosUsuario = $user->eventos->pluck('id')->toArray();

        if (!in_array($request->evento_id, $eventosUsuario)) {
            $evento = Evento::find($request->evento_id);

            if ($evento->tipo == 'público') {
                $user->eventos()->attach($request->evento_id);
                $evento->estado = 1;

                return response()->json([
                    'mensaje' => 'Te has unido a este evento'
                ]);

            } else {
                $user->eventos()->attach($request->evento_id);
                $evento->estado = 0;

                return response()->json([
                    'mensaje' => 'Pendiente de que te acepten en este evento'
                ]);
            }

        } else {
            return response()->json([
                'mensaje' => 'Ya te has unido a este evento'
            ]);
        }
    }
}
