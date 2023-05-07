<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario_emisor', 'id_usuario_receptor', 'mensaje'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}