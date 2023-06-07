<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'evento_id', 'estado'
    ];

    public function eventos() {
        return $this->belongsToMany(Evento::class, 'evento_users', 'user_id', 'evento_id');
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }
}