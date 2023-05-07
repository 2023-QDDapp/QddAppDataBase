<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
        $user->contrasena = $request->contrasena;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        $user->biografia = $request->biografia;
        $user->foto = $request->foto;
        $user->tipo = $request->tipo;
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
        $user = User::select('id', 'nombre', DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, NOW()) AS edad'), 'foto', 'biografia')
            ->where('id', $id)
            ->with([
                'categorias' => function ($query) {
                    $query->join('categoria_users', 'categorias.id', '=', 'categoria_users.categoria_id')
                        ->select('categorias.id', 'categorias.categoria', 'categoria_users.user_id');
                },
                'resenas' => function ($query) {
                    $query->select('resenas.id', 'resenas.mensaje', 'resena_users.user_id')
                        ->join('resena_users', 'resenas.id', '=', 'resena_users.resena_id');
                },                
            ])
            ->get();

        return response()->json([
            'usuario' => $user
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
