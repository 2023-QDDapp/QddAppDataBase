<?php

namespace Database\Seeders;

use App\Models\EventoUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class EventoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/asistir.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new EventoUser();
            $modelo->user_id = $item['user_id'];
            $modelo->evento_id = $item['evento_id'];
            $modelo->estado = $item['estado'];
            $modelo->save();
        }
    }
}