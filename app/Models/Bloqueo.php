<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloqueo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario_bloqueador', 'id_usuario_bloqueado'
    ];

    public function user() {
        return $this->belongsToMany(User::class);
    }
}