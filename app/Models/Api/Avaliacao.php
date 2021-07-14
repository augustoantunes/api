<?php

namespace App\Models\Api;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';

    public function artigos()
    {
        return $this->hasOne(Artigo::class,'id','artigos_id');
    }

        public function users(){
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
