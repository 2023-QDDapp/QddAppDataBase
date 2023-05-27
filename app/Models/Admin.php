<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class Admin extends Model implements Authenticatable
{
    use HasFactory, AuthenticableTrait;

    protected $table = 'admins'; 

    protected $fillable = [
        'name', 'email', 'password', 'is_super_admin'
    ]; 
    protected $hidden = [
        'password', 'remember_token', 'is_super_admin'
    ]; 

    public function isSuperAdmin()
    {
        return $this->is_super_admin;
    }

    //la codificacion de la contraseña aunque venga por JSON, lo hace automaticamente una vez detecta password
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

}