<?php

namespace Database\Seeders;

use App\Models\Bloqueo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BloqueoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/bloquear.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new Bloqueo();
            $modelo->id_usuario_bloqueador = $item['id_usuario_bloqueador'];
            $modelo->id_usuario_bloqueado = $item['id_usuario_bloqueado'];
            $modelo->save();
        }
    }
}