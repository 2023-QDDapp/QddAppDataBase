<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Resena;

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
        $user = new User;
        $user->nombre = $request->nombre;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        $user->biografia = $request->biografia;
        $user->foto = $request->foto;

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
        $user = User::select('users.id', 'users.nombre', 'users.foto', 'users.biografia', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'categorias.id AS categoria_id', 'categorias.categoria')
            ->join('categoria_users', 'users.id', '=', 'categoria_users.user_id')
            ->join('categorias', 'categoria_users.categoria_id', '=', 'categorias.id')
            ->where('users.id', $id)
            ->get();

        $usuario = $user->first();
        $categorias = $user->pluck('categoria_id')->toArray();
        $datosCategorias = Categoria::whereIn('id', $categorias)->get(['id', 'categoria']);

        $resenas = Resena::select('users.id AS id_usuario', 'users.nombre AS nombre_usuario', 'users.foto', 'resenas.mensaje')
            ->join('users', 'users.id', '=', 'resenas.id_usuario_emisor')
            ->where('resenas.id_usuario_receptor', $id)
            ->get();

        $datosUsuario = [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'foto' => $usuario->foto,
            'edad' => $usuario->edad,
            'biografia' => $usuario->biografia,
            'intereses' => $datosCategorias,
            'valoraciones' => $resenas
        ];

        return response()->json(
            $datosUsuario
        );
    }

    public function showEventosUser($id)
    {
        $user = DB::table('users')
            ->join('eventos', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.categoria')
            ->where('eventos.user_id', $id)
            ->get();

        return response()->json(
            $user
        );
    }

    public function pantallaParaTi($id)
    {
        $categoria = DB::table('categoria_users')
            ->join('categorias', 'categorias.id', '=', 'categoria_users.categoria_id')
            ->select('categorias.id AS id_categoria', 'categorias.categoria')
            ->where('categoria_users.user_id', $id)
            ->get();
        
        $objeto = json_decode($categoria);

        foreach ($objeto as $objetos) {
            $idCategoria[] = $objetos->id_categoria;
        }

        $eventos = DB::table('eventos')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'eventos.fecha_hora_fin', 'categorias.id AS id_categoria', 'categorias.categoria')
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
        $seguidos = DB::table('followers')
            ->select('id_usuario_seguido')
            ->where('id_usuario_seguidor', $id)
            ->get();

        $objeto = json_decode($seguidos);

        foreach ($objeto as $objetos) {
            $idSeguido[] = $objetos->id_usuario_seguido;
        }

        $eventos = DB::table('eventos')
            ->join('users', 'eventos.user_id', '=', 'users.id')
            ->join('categorias', 'eventos.categoria_id', '=', 'categorias.id')
            ->select('eventos.id AS id_evento', 'users.id AS id_organizador', 'users.nombre AS organizador', 'users.foto AS foto_organizador', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'eventos.imagen AS imagen_evento', 'eventos.titulo', 'eventos.descripcion', 'eventos.fecha_hora_inicio', 'categorias.id AS id_categoria', 'categorias.categoria')
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
        $user = new User;
        $user->nombre = $request->nombre;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->biografia = $request->biografia;
        $user->foto = $request->foto;

        $user->save();

        return response()->json([
            'mensaje' => 'El usuario ha sido registrado correctamente',
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

    public function categorias(Request $request) 
    {
        $user = User::find($request->user_id);

        if (count($user->categorias) < 3) {
            $user->categorias()->attach($request->categoria_id);
            
            return response()->json([
                'mensaje' => 'La categoría se ha añadido correctamente'
            ]);

        } else {
            return response()->json([
                'mensaje' => 'No puedes tener más de tres categorías'
            ]);
        }
    }
}
