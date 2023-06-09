<?php

namespace App\Http\Controllers\V1;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoriaControllerApi extends Controller
{
    public function index()
    {
        // Obtenemos todas la categorías
        $categoria = DB::table('categorias')
            ->select('id', 'categoria')
            ->get();

        return response()->json(
            $categoria
        );
    }
}
