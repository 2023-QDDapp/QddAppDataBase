<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre', 'telefono', 'email', 'contrasena', 'fecha_nacimiento', 'biografia', 'foto'
    ];

    public function categorias() {
        return $this->belongsToMany(Categoria::class, 'categoria_users');
    }

    public function eventos() {
        return $this->belongsToMany(Evento::class, 'evento_users');
    }

    public function comentarios() {
        return $this->hasMany(Comentario::class);
    }

    public function resenas() {
        return $this->hasMany(Resena::class);
    }

    public function notificaciones() {
        return $this->hasMany(Notificacion::class);
    }

    public function bloqueos() {
        return $this->hasMany(Bloqueo::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }


    // API
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    //función para calcular la edad por fecha introducida
    public function getAgeFromDate()
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }
}