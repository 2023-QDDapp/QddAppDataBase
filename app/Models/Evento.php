<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'categoria_id', 'titulo', 'fecha_hora_inicio', 'fecha_hora_fin', 
        'descripcion', 'imagen', 'tipo', 'location', 'latitud', 'longitud', 'n_participantes'
    ];

    public function user() {
        return $this->belongsToMany(User::class, 'evento_users', 'evento_id', 'user_id');
    }

    public function categoria() {
        return $this->hasOne(Categoria::class);
    }

    public function comentarios() {
        return $this->hasMany(Comentario::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }
}