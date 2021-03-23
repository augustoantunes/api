<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artigo extends Model
{
    use HasFactory;

    protected $table = 'artigos';

    protected $hidden = [

    ];


    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'artigos_has_categorias', 'artigos_id', 'categorias_id');
    }

}
