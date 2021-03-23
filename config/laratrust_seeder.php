<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'editor_chefe' => [
            'artigo' => 'c,r,u,d',
            'users' => 'c,r,u,d',
            'posts' => 'c,r,u,d',
            'cartas' => 'c,r,u,d'
        ],
        'editor' => [
            'users' => 'c,r,u,d',
            'cartas' => 'c,r,u,d',
            'users' => 'c,r,u,d',
            'posts' => 'c,r,u,d',
        ],
        'autor' => [
            'artigo' => 'c,r,u,d',
            'cartas' => 'r,u',
            'posts' => 'c,r,u,d',
            'users' => 'r',
        ],
        'usuario' => [
            'artigo' => 'r',
            'cartas' => 'r,u',
            'users' => 'r',
            'posts' => 'r',
        ]
        // 'role_name' => [
        //     'module_1_name' => 'c,r,u,d',
        // ]
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
