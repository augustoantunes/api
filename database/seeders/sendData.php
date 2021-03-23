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
        // DB::table('roles')->insert([
        //     ['name' => 'autor', 'display_name' => 'Autor', 'description'=> 'Autor da revista'],
        //     ['name' => 'revisor', 'display_name' => 'Revisor', 'description' => 'Revisor da revista'],
        //     ['name' => 'editor', 'display_name' => 'Editor', 'description' => 'Editor da  revista'],
        //     ['name' => 'editor_chefe', 'display_name' => 'Editor Chefe', 'description' => 'Edito chefe da revista'],
        //     ['name' => 'usuario', 'display_name' => 'Usuário', 'description'=> 'Usuário']

        // ]);

        // DB::table('permissions')->insert([

        //     // Articles 1 -4
        //     ['name' => 'create_article'],
        //     ['name' => 'edit_article'],
        //     ['name' => 'view_article'],
        //     ['name' => 'delete_article'],
        //     // commentários 5- 8
        //     ['name' => 'create_comments'],
        //     ['name' => 'edit_comments'],
        //     ['name' => 'view_comments'],
        //     ['name' => 'delete_comments'],

        //     // Categorias 9 - 12
        //     ['name' => 'create_categories'],
        //     ['name' => 'edit_categories'],
        //     ['name' => 'view_categories'],
        //     ['name' => 'delete_categories'],

        //     // Destaques 13 - 16
        //     ['name' => 'create_destaques'],
        //     ['name' => 'edit_destaques'],
        //     ['name' => 'view_destaques'],
        //     ['name' => 'delete_destaques'],

        //     // Cartas 17 - 20
        //     ['name' => 'create_cartas'],
        //     ['name' => 'edit_cartas'],
        //     ['name' => 'view_cartas'],
        //     ['name' => 'delete_cartas'],

        //     // Roles 21 -24
        //     ['name' => 'create_role'],
        //     ['name' => 'edit_role'],
        //     ['name' => 'view_role'],
        //     ['name' => 'delete_role'],

        //     // Publicar 25
        //     ['name' => 'public_article'],



        // ]);

        // DB::table('permission_role')->insert([


        //     /* ---------------- Autores -------------------*/

        //     // configure Permissões do autores - Artigos
        //     ['role_id' => 5, 'permission_id' => 3],

        //     // configure Permissões do autores - Artigos
        //     ['role_id' => 1, 'permission_id' => 1],
        //     ['role_id' => 1, 'permission_id' => 2],
        //     ['role_id' => 1, 'permission_id' => 3],
        //     ['role_id' => 1, 'permission_id' => 4],

        //     ['role_id' => 1, 'permission_id' => 17],
        //     ['role_id' => 1, 'permission_id' => 18],
        //     ['role_id' => 1, 'permission_id' => 19],
        //     ['role_id' => 1, 'permission_id' => 20],

        //     /* ---------------- Revisor -------------------*/

        //     // configure Permissões do Pares - Artigos
        //     ['role_id' => 2, 'permission_id' => 1],
        //     ['role_id' => 2, 'permission_id' => 2],
        //     ['role_id' => 2, 'permission_id' => 3],
        //     ['role_id' => 2, 'permission_id' => 4],

        //     // configure Permissões do Revisor - Commensta
        //     ['role_id' => 2, 'permission_id' => 5],
        //     ['role_id' => 2, 'permission_id' => 6],
        //     ['role_id' => 2, 'permission_id' => 7],
        //     ['role_id' => 2, 'permission_id' => 8],

        //     /* ---------------- Editor -------------------*/

        //     // configure Permissões do Editor - Artigos
        //     ['role_id' => 3, 'permission_id' => 1],
        //     ['role_id' => 3, 'permission_id' => 2],
        //     ['role_id' => 3, 'permission_id' => 3],
        //     ['role_id' => 3, 'permission_id' => 4],

        //     // configure Permissões do Editor - Commensta
        //     ['role_id' => 3, 'permission_id' => 5],
        //     ['role_id' => 3, 'permission_id' => 6],
        //     ['role_id' => 3, 'permission_id' => 7],
        //     ['role_id' => 3, 'permission_id' => 8],

        //     ['role_id' => 3, 'permission_id' => 25],


        //     /* ---------------- Editor Chefe -------------------*/

        //     // configure Permissões do Editor Chefe - Artigos
        //     ['role_id' => 4, 'permission_id' => 1],
        //     ['role_id' => 4, 'permission_id' => 2],
        //     ['role_id' => 4, 'permission_id' => 3],
        //     ['role_id' => 4, 'permission_id' => 4],

        //     // configure Permissões do Editor Chefe - Destaque
        //     ['role_id' => 4, 'permission_id' => 13],
        //     ['role_id' => 4, 'permission_id' => 14],
        //     ['role_id' => 4, 'permission_id' => 15],
        //     ['role_id' => 4, 'permission_id' => 16],

        //     // configure Permissões do Editor Chefe - Categorias
        //     ['role_id' => 4, 'permission_id' => 5],
        //     ['role_id' => 4, 'permission_id' => 6],
        //     ['role_id' => 4, 'permission_id' => 7],
        //     ['role_id' => 4, 'permission_id' => 8],

        //     // configure Permissões do Editor Chefe - Roles
        //     ['role_id' => 4, 'permission_id' => 21],
        //     ['role_id' => 4, 'permission_id' => 22],
        //     ['role_id' => 4, 'permission_id' => 23],
        //     ['role_id' => 4, 'permission_id' => 24],

        //     // Tornar um artigo cientifico publico
        //     ['role_id' => 4, 'permission_id' => 25],


        // ]);


        DB::table('ciencia')->insert([
            ['ramo_ciencia' => 'Ciências Exatas'],
            ['ramo_ciencia' => 'Ciências Socias']
        ]);

        DB::table('categorias')->insert([
            ['nome' => 'Matemática', 'slug' => 'matematica', 'ciencia_id' => 1],
            ['nome' => 'Direito', 'slug' => 'direito', 'ciencia_id' => 2],
            ['nome' => 'Computação', 'slug' => 'computacao', 'ciencia_id' => 2]
        ]);
    }
}
