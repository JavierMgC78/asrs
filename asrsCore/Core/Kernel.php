<?php

namespace asrsCore\Core;

/**
 * Kernel ASRS: encapsula el ciclo de vida de la petición HTTP
 * (cachés, enrutamiento, 404, baseUrl y renderizado con Output Buffering).
 */
class Kernel
{
    private string $cacheDir;

    /** Roles y permisos en memoria (cache roles.php): ['roles' => [...], 'views' => [...]] */
    private array $roles = ['roles' => [], 'views' => []];

    private const AUTH_ENDPOINTS = ['auth/login' => 'login', 'auth/logout' => 'logout'];

    public function __construct(?string $cacheDir = null)
    {
        $this->cacheDir = rtrim($cacheDir ?? __DIR__ . '/../Cache', '/\\');
    }

    public function handleRequest(?string $uri = null): void
    {
        $uri ??= $_GET['uri'] ?? '';

        // Endpoints reservados del subsistema de autenticación
        $authMethod = self::AUTH_ENDPOINTS[trim($uri, '/')] ?? null;
        if ($authMethod !== null) {
            (new \asrsCore\Controllers\AuthController())->{$authMethod}();
            return;
        }

        // Endpoint administrativo de regeneración de caché
        if (trim($uri, '/') === 'admin/cache/regenerate') {
            (new \asrsCore\Controllers\AdminController())->regenerateCache();
            return;
        }

        [$routes, $apps] = $this->loadCaches();

        $route = RoutingEngine::resolve($uri, $routes, $apps);

        if ($route === null) {
            $this->notFound();
            return;
        }

        // Interceptor perimetral: las rutas privadas exigen Split Token válido
        if (($route['type'] ?? 'public') === 'private') {
            $currentUser = Security::guard($route);
        }

        $this->render($route, $uri, $currentUser ?? null);
    }

    /** Carga (y genera si faltan) las cachés de apps y rutas. */
    private function loadCaches(): array
    {
        $appsCacheFile = $this->cacheDir . '/apps.php';
        $apps = CacheAppsBuilder::buildIfNotExists($appsCacheFile)
            ? (require $appsCacheFile)
            : [];
        if (!is_array($apps)) {
            @unlink($appsCacheFile);
            CacheAppsBuilder::buildIfNotExists($appsCacheFile);
            $apps = is_file($appsCacheFile) ? (require $appsCacheFile) : [];
            $apps = is_array($apps) ? $apps : [];
        }

        $routesCacheFile = $this->cacheDir . '/routes.php';
        $routes = CacheRoutesBuilder::buildIfNotExists($routesCacheFile)
            ? (require $routesCacheFile)
            : [];
        if (!is_array($routes)) {
            @unlink($routesCacheFile);
            CacheRoutesBuilder::buildIfNotExists($routesCacheFile);
            $routes = is_file($routesCacheFile) ? (require $routesCacheFile) : [];
            $routes = is_array($routes) ? $routes : [];
        }

        $rolesCacheFile = $this->cacheDir . '/roles.php';
        if (CacheRolesBuilder::buildIfNotExists($rolesCacheFile)) {
            $loadedRoles = require $rolesCacheFile;
            if (!is_array($loadedRoles)) {
                @unlink($rolesCacheFile);
                CacheRolesBuilder::buildIfNotExists($rolesCacheFile);
                $loadedRoles = is_file($rolesCacheFile) ? (require $rolesCacheFile) : [];
            }
            $this->roles = is_array($loadedRoles) ? $loadedRoles : ['roles' => [], 'views' => []];
        }

        return [$routes, $apps];
    }

    /** Roles y permisos cargados en memoria, disponibles para el subsistema privado. */
    public function roles(): array
    {
        return $this->roles;
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>404</title></head>'
            . '<body style="font-family:sans-serif;text-align:center;padding:4rem">'
            . '<h1>404</h1><p>La página solicitada no existe.</p></body></html>';
    }

    private function computeBaseUrl(): string
    {
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $scriptDir . '/';
    }

    private function render(array $route, string $uri, ?array $currentUser = null): void
    {
        // Variables disponibles en el layout: $route, $baseUrl, $content, $cssFiles, $jsFiles, $currentUri
        $baseUrl     = $this->computeBaseUrl();
        $contentFile = $route['contentFile'];
        $cssFiles    = $route['cssFiles'];
        $jsFiles     = $route['jsFiles'];
        $currentUri  = trim($uri, '/');

        ob_start();
        require $contentFile;
        $content = ob_get_clean();

        require $route['layoutFile'];
    }
}
