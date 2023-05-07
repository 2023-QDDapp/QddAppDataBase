<?php

namespace Database\Seeders;

use App\Models\Comentario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ComentarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/comentarios.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new Comentario();
            $modelo->user_id = $item['user_id'];
            $modelo->evento_id = $item['evento_id'];
            $modelo->comentario = $item['comentario'];
            $modelo->save();
        }
    }
}