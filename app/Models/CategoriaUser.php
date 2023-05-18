<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaUser extends Model
{
    //use HasFactory;
    protected $table = 'user_categoria';

    protected $fillable = [
        'user_id', 'categoria_id'
    ];

    public function users() {
        return $this->belongsTo(User::class, 'categoria_users');
    }

    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }
}