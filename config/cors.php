<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    */

    // 1. Indicar que el filtro CORS se aplique a todas las rutas que comiencen con /api
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // 2. Permitir todos los métodos HTTP que usará Angular (GET, POST, PUT, DELETE, etc.)
    'allowed_methods' => ['*'],

    // 3. Declarar explícitamente la URL local donde corre tu proyecto Angular
    'allowed_origins' => ['http://localhost:4200', 'http://127.0.0.1:4200'],

    'allowed_origins_patterns' => [],

    // 4. Permitir que viajen todas las cabeceras estándar (Content-Type, Authorization, X-Requested-With, etc.)
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    // 5. Tiempo máximo (en segundos) que el navegador guardará en caché la aprobación de CORS
    'max_age' => 0,

    // 6. Cambiar a true si en el futuro necesitas enviar cookies o sesiones seguras entre Angular y Laravel
    'supports_credentials' => true,

];
