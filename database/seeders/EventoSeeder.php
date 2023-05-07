<?php

namespace Database\Seeders;

use App\Models\Evento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class EventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/eventos.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new Evento();
            $modelo->user_id = $item['user_id'];
            $modelo->categoria_id = $item['categoria_id'];
            $modelo->titulo = $item['titulo'];
            $modelo->fecha_hora_inicio = $item['fecha_hora_inicio'];
            $modelo->fecha_hora_fin = $item['fecha_hora_fin'];
            $modelo->descripcion = $item['descripcion'];
            $modelo->imagen = $item['imagen'];
            $modelo->location = $item['location'];
            $modelo->latitud = $item['latitud'];
            $modelo->longitud = $item['longitud'];
            $modelo->n_participantes = $item['n_participantes'];
            $modelo->save();
        }
    }
}