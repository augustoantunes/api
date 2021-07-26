<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class sendData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('ciencia')->insert([
            ['ramo_ciencia' => 'Ciências Sociais'],
            ['ramo_ciencia' => 'Ciências Exatas'],
            ['ramo_ciencia' => 'Ciências Informáticas'],
            ['ramo_ciencia' => 'Ciências Humanísticas']
        ]);

        DB::table('categorias')->insert([
            ['nome' => 'Matemática', 'slug' => 'matematica', 'ciencia_id' => 2],
            ['nome' => 'Direito', 'slug' => 'direito', 'ciencia_id' => 1],
            ['nome' => 'Psicologia Criminal', 'slug' => 'direito', 'ciencia_id' => 1],
            ['nome' => 'Computação', 'slug' => 'computacao', 'ciencia_id' => 3],
            ['nome' => 'Telecomunicações', 'slug' => 'computacao', 'ciencia_id' => 3],
        ]);

        DB::table('edicoes')->insert([
            ['numero' => '000001', 'status' => 0],
        ]);

        DB::table('roles')->insert([
            ['name' => 'editor_chefe', 'display_name' => 'Editor Chefe', 'description' => 'Edito chefe da revista'],
            ['name' => 'revisor', 'display_name' => 'Revisor', 'description' => 'Revisor da revista'],
            ['name' => 'usuario', 'display_name' => 'Usuário', 'description'=> 'Usuário da Revista'],
            ['name' => 'autor', 'display_name' => 'Autor', 'description' => 'Autor da revista'],
            ['name' => 'editor', 'display_name' => 'Editor', 'description' => 'Editor da  revista'],
        ]);

    }
}
