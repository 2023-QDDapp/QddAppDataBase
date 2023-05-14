<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run()
    {
        $json = File::get('database/data/administradores.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $modelo = new Admin();
            $modelo->name = $item['name'];
            $modelo->email = $item['email'];
            $modelo->password = $item['password'];
            $modelo->is_super_admin = $item['is_super_admin'];
            $modelo->save();
        }

        //Admin::factory()->count(4)->create();
        //Admin::factory()->superAdmin()->create();
    }
}
