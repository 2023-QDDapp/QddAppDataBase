<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario_emisor', 'id_usuario_receptor', 'mensaje', 'valoracion'
    ];

    public function emisor()
    {
        return $this->belongsTo(User::class, 'id_usuario_emisor');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'id_usuario_receptor');
    }
}