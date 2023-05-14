<?php

namespace Database\Seeders;

use App\Models\Follower;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/seguidores.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new Follower();
            $modelo->id_usuario_seguido = $item['id_usuario_seguido'];
            $modelo->id_usuario_seguidor = $item['id_usuario_seguidor'];
            $modelo->save();
        }
    }
}
