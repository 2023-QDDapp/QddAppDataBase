<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'is_super_admin' => false,
        ];
    }

    //con esta funcion se le puede asignar super administrador a un usuario
    public function superAdmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_super_admin' => true,
            ];
        });
    }
}
