<?php

return [
    /*
     * បន្ថែម 'sanctum/csrf-cookie' និង 'login' ដើម្បីឱ្យ Frontend មានសិទ្ធិហៅមកបាន
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173', 'https://beautiful-reflection-production.up.railway.app'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];