<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria'
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'categoria_users');
    }

    public function eventos() {
        return $this->hasMany(Evento::class);
    }
}