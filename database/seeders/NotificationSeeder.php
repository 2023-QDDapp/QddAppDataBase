<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/notificaciones.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new Notification();
            $modelo->user_id = $item['user_id'];
            $modelo->evento_id = $item['evento_id'];
            $modelo->contenido = $item['contenido'];
            $modelo->fecha_hora_recibida = $item['fecha_hora_recibida'];
            $modelo->save();
        }
    }
}