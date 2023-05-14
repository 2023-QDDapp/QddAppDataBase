<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario_seguido', 'id_usuario_seguidor'
    ];

    public function following() {
        return $this->belongsTo(User::class, 'id_usuario_seguido');
    }

    public function follower() {
        return $this->belongsTo(User::class, 'id_usuario_seguidor');
    }
}
