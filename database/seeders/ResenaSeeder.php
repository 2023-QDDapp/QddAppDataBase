<?php

namespace Database\Seeders;

use App\Models\Resena;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ResenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/resenas.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new Resena();
            $modelo->id_usuario_emisor = $item['id_usuario_emisor'];
            $modelo->id_usuario_receptor = $item['id_usuario_receptor'];
            $modelo->mensaje = $item['mensaje'];
            $modelo->save();
        }
    }
}