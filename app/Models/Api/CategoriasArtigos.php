<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriasArtigos extends Model
{
    use HasFactory;

    protected $table = 'artigos_has_categorias';

    protected $hidden = [
        'updated_at', 'created_at'
    ];
}
