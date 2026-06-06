<?php

return [
    /*
     * បន្ថែម 'sanctum/csrf-cookie' និង 'login' ដើម្បីឱ្យ Frontend មានសិទ្ធិហៅមកបាន
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    /*
     * ភ្ជាប់ទៅកាន់ Link Frontend របស់អ្នក
     */
    'allowed_origins' => ['https://beautiful-reflection-production.up.railway.app'],
    'allowed_origins_patterns' => [],
    'supports_credentials' => true,

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
     * ដាច់ខាតត្រូវតែ true ទើបអាច Login បាន
     */
    'supports_credentials' => true,
];