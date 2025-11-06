<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'user/*', 'user/profile-information', 'user/password'], // Adicione rotas Fortify se necessário, embora 'paths' => ['*'] geralmente cubra tudo.

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://betabit-agenda-frontend.vercel.app', 
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,


    'supports_credentials' => true,

];