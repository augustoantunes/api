<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interveniente extends Model
{
    use HasFactory;


    protected $table = 'intervenientes';

    protected $hidden = [
        'updated_at', 'created_at', 'pivot'
    ];

    public function artigos(){
        return $this->belongsTo(Artigo::class, 'artigos_id', 'id');
    }

    public function users(){
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
