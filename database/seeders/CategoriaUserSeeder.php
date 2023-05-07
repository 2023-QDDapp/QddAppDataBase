<?php

namespace Database\Seeders;

use App\Models\CategoriaUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategoriaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/elegir.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new CategoriaUser();
            $modelo->user_id = $item['user_id'];
            $modelo->categoria_id = $item['categoria_id'];
            $modelo->save();
        }
    }
}