<?php

namespace asrsCore\Core;

/**
 * Kernel ASRS: encapsula el ciclo de vida de la petición HTTP
 * (cachés, enrutamiento, 404, baseUrl y renderizado con Output Buffering).
 */
class Kernel
{
    private string $cacheDir;

    public function __construct(?string $cacheDir = null)
    {
        $this->cacheDir = rtrim($cacheDir ?? __DIR__ . '/../Cache', '/\\');
    }

    public function handleRequest(?string $uri = null): void
    {
        $uri ??= $_GET['uri'] ?? '';

        [$routes, $apps] = $this->loadCaches();

        $route = RoutingEngine::resolve($uri, $routes, $apps);

        if ($route === null) {
            $this->notFound();
            return;
        }

        $this->render($route, $uri);
    }

    /** Carga (y genera si faltan) las cachés de apps y rutas. */
    private function loadCaches(): array
    {
        $appsCacheFile = $this->cacheDir . '/apps.php';
        $apps = CacheAppsBuilder::buildIfNotExists($appsCacheFile)
            ? (require $appsCacheFile)
            : [];

        $routesCacheFile = $this->cacheDir . '/routes.php';
        $routes = CacheRoutesBuilder::buildIfNotExists($routesCacheFile)
            ? (require $routesCacheFile)
            : [];

        return [$routes, $apps];
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

    private function render(array $route, string $uri): void
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
