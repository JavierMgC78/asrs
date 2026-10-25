<?php
// Uso: php asrsCore/Database/seed.php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__ . '/../Helpers/global_functions.php';

\asrsCore\Database\Seeders\SuperAdminSeeder::run();
echo "[ASRS] Seeder ejecutado: " . \asrsCore\Database\Seeders\SuperAdminSeeder::EMAIL . PHP_EOL;
