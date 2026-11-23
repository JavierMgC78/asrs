<?php

return [
    // Identidad de la aplicación hija montada
    'APP_CHILD_NAME' => 'cea', // De aquí se deducen ceaWeb y ceaAdmin

    // Control operativo y de flujo
    'APP_CHILD_IS_DEFAULT' => 1, // 1 = Entrada raíz va a ceaWeb | 0 = Entrada raíz va a asrsWeb
    'MAINTENANCE_APP'      => 0, // 1 = Pausa la app hija y redirige a asrsWeb con aviso
    'MAINTENANCE_GLOBAL'   => 0, // 1 = Bloquea todo el ecosistema (prioridad máxima)

    // Persistencia (Base de datos)
    'DB_HOST'    => '127.0.0.1',
    'DB_PORT'    => '3306',
    'DB_NAME'    => 'asrs_db',
    'DB_USER'    => 'root',
    'DB_PASS'    => '',
    'DB_CHARSET' => 'utf8mb4',
];