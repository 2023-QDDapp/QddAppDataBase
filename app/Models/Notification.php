<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'evento_id', 'contenido', 'fecha_hora_recibida'
    ];

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function eventos() {
        return $this->belongsToMany(Evento::class);
    }
}