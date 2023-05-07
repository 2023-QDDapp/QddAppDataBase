<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/categorias.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new Categoria();
            $modelo->categoria = $item['categoria'];
            $modelo->save();
        }
    }
}