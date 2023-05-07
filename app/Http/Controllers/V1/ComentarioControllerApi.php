<?php

namespace App\Http\Controllers\V1;

use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ComentarioControllerApi extends Controller
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
        $comentario = new Comentario;
        $comentario->user_id = $request->user_id;
        $comentario->evento_id = $request->evento_id;
        $comentario->comentario = $request->comentario;
        $comentario->save();

        return response()->json([
            'mensaje' => 'Comentario publicado',
            'comentario' => $comentario
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
        $datos = DB::table('comentarios')
            ->join('users', 'users.id', '=', 'comentarios.user_id')
            ->select('comentarios.user_id', 'users.nombre', 'users.foto', 'comentarios.comentario')
            ->where('comentarios.evento_id', $id)
            ->get();

        return response()->json([
            'datos' => $datos
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
        //
    }
}
