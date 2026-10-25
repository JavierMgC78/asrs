<?php
namespace asrsCore\Controllers;

use asrsCore\Core\CacheAppsBuilder;
use asrsCore\Core\CacheRolesBuilder;
use asrsCore\Core\CacheRoutesBuilder;
use asrsCore\Core\Security;

class AdminController
{
    /** POST admin/cache/regenerate: recompila Cache/routes.php, Cache/roles.php y Cache/apps.php (solo superadmin). */
    public function regenerateCache(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['ok' => false, 'error' => 'Método no permitido'], 405);
        }

        $user = Security::authenticate();
        if ($user === null) {
            $this->json(['ok' => false, 'error' => 'No autenticado'], 401);
        }
        if (!in_array(Security::ADMIN_ROLE, $user['roles'], true)) {
            $this->json(['ok' => false, 'error' => 'Acceso denegado'], 403);
        }

        $cacheDir   = dirname(CacheRoutesBuilder::DEFAULT_CACHE_FILE);
        $routesFile = CacheRoutesBuilder::DEFAULT_CACHE_FILE;
        $rolesFile  = CacheRolesBuilder::DEFAULT_CACHE_FILE;
        $appsFile   = $cacheDir . '/apps.php';

        @unlink($routesFile);
        @unlink($rolesFile);
        @unlink($appsFile);

        $routesOk = CacheRoutesBuilder::buildIfNotExists($routesFile);
        $rolesOk  = CacheRolesBuilder::buildIfNotExists($rolesFile);
        $appsOk   = CacheAppsBuilder::buildIfNotExists($appsFile);

        $routes = $routesOk ? (require $routesFile) : [];
        $roles  = $rolesOk ? (require $rolesFile) : ['roles' => []];
        $apps   = $appsOk ? (require $appsFile) : [];

        // Las claves "@..." son metadatos, no rutas
        $routeCount = count(array_filter(array_keys($routes), fn($k) => $k[0] !== '@'));

        $allOk = $routesOk && $rolesOk && $appsOk;

        $this->json([
            'ok'     => $allOk,
            'routes' => ['ok' => $routesOk, 'count' => $routeCount],
            'roles'  => ['ok' => $rolesOk, 'count' => count($roles['roles'] ?? [])],
            'apps'   => ['ok' => $appsOk, 'count' => count($apps)],
        ], $allOk ? 200 : 500);
    }

    private function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');
        echo json_encode($data);
        exit;
    }
}
