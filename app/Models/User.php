<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nombre', 'telefono', 'email', 'password', 'fecha_nacimiento', 'biografia', 'foto', 'verification_token'
    ];

    public function categorias() {
        return $this->belongsToMany(Categoria::class, 'categoria_users', 'user_id', 'categoria_id');
    }

    public function eventoCreado() {
        return $this->hasMany(Evento::class);
    }

    public function eventos() {
        return $this->belongsToMany(Evento::class, 'evento_users');
    }

    public function comentarios() {
        return $this->hasMany(Comentario::class);
    }

    public function resenas() {
        return $this->belongsToMany(User::class, 'resenas', 'id_usuario_emisor', 'id_usuario_receptor',);
    }

    public function mensajesRecibidos() {
        return $this->hasMany(Resena::class, 'id_usuario_receptor');
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

    public function eventosCreados() {
        return $this->hasMany(Evento::class, 'user_id');
    }

    public function eventosAsistidos() {
        return $this->belongsToMany(Evento::class, 'evento_users', 'user_id', 'evento_id');
    }

    public function following() {
        return $this->belongsToMany(User::class, 'followers', 'id_usuario_seguidor', 'id_usuario_seguido')->withPivot('id');
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    //función para calcular la edad por fecha introducida
    public function getAgeFromDate()
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    public function setPasswordAttribute($value)
    {
        if (strpos($value, '$2y$') === 0) {
            // La contraseña ya está codificada, no es necesario volver a codificarla
            $this->attributes['password'] = $value;
        } else {
            // La contraseña no está codificada, se debe aplicar bcrypt()
            $this->attributes['password'] = bcrypt($value);
        }
    }

    // API
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }
}
