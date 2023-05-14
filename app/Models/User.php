<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nombre', 'telefono', 'email', 'password', 'fecha_nacimiento', 'biografia', 'foto'
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

    public function follows() {
        return $this->belongsToMany(User::class);
    }

    public function followers() {
        return $this->belongsToMany(User::class);
    }

    public function eventosCreados()
    {
        return $this->hasMany(Evento::class, 'user_id');
    }

    public function eventosAsistidos()
    {
        return $this->belongsToMany(Evento::class, 'evento_users', 'user_id', 'evento_id');
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

    //la codificacion de la contraseña aunque venga por JSON, lo hace automaticamente una vez detecta password
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}