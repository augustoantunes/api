<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Artigo extends Model
{
    use HasFactory;

    protected $table = 'artigos';

    protected $hidden = [];


    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'artigos_has_categorias', 'artigos_id', 'categorias_id');
    }

    public function autores()
    {
        return $this->belongsToMany(User::class, 'intervenientes', 'artigos_id', 'users_id')->wherePivot('role','=', 'AUTOR');
    }
    public function revisores()
    {
        return $this->belongsToMany(User::class, 'intervenientes', 'artigos_id', 'users_id')->wherePivot('role','=', 'REVISOR');
    }
    public function intervenientes()
    {
        return $this->belongsTo(Interveniente::class, 'id', 'artigos_id');
    }
    public function edicoes()
    {
        return $this->belongsTo(Edicao::class, 'edicoes_id', 'id');
    }
}
