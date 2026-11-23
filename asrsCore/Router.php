<?php

declare(strict_types=1);

namespace Asrs\Core;

/**
 * Enrutador principal de ASRS.
 * Normaliza la URI entrante, localiza la ruta activa en la caché
 * y delega la validación de acceso al Middleware.
 */
class Router
{
    /** @var array<string, array<string, mixed>> */
    private array $viewsMap;

    /** @var array<string, mixed> */
    private array $config;

    private Middleware $middleware;

    /**
     * @param array<string, array<string, mixed>> $viewsMap   Arreglo de vistas indexado por `uri`.
     * @param array<string, mixed>                $config     Configuración cargada desde `asrsCore/config.php`.
     * @param Middleware|null                     $middleware Middleware encargado de validar acceso y cargar la vista.
     */
    public function __construct(
        array $viewsMap,
        array $config = [],
        ?Middleware $middleware = null
    ) {
        $this->viewsMap = $viewsMap;
        $this->config = $config;
        $this->middleware = $middleware ?? new Middleware();
    }

    /**
     * Intercepta y normaliza la URI entrante.
     *
     * @param string|null $rawUri URI explícita; si es null, toma `$_GET['uri']` o `$_SERVER['REQUEST_URI']`.
     */
    public function normalizeUri(?string $rawUri = null): string
    {
        $uri = $rawUri ?? (string)($_GET['uri'] ?? $_SERVER['REQUEST_URI'] ?? '/');

        $path = explode('?', $uri, 2)[0];
        $path = str_replace('\\', '/', $path);
        $path = (string)preg_replace('#/+#', '/', $path);
        $path = trim($path, '/');

        return $path === '' ? '/' : '/' . $path;
    }

    /**
     * Busca un registro activo (`status = 1`) en el arreglo de caché para la URI dada.
     *
     * @return array<string, mixed>|null
     */
    public function resolve(?string $rawUri = null): ?array
    {
        $uri = $this->normalizeUri($rawUri);

        if (!isset($this->viewsMap[$uri]) || !is_array($this->viewsMap[$uri])) {
            return null;
        }

        $route = $this->viewsMap[$uri];

        if ((int)($route['status'] ?? 0) !== 1) {
            return null;
        }

        // En la raíz '/', si la caché contuviera múltiples variantes o se requiriera
        // validar el flag APP_CHILD_IS_DEFAULT, se respeta la configuración activa.
        if ($uri === '/' && isset($this->config['APP_CHILD_IS_DEFAULT'], $route['is_child'])) {
            $expectedChild = (int)$this->config['APP_CHILD_IS_DEFAULT'];
            if ((int)$route['is_child'] !== $expectedChild) {
                return $route;
            }
        }

        return $route;
    }

    /**
     * Despacha la petición entrante hacia el Middleware o responde 404 si no existe.
     *
     * @param string|null $rawUri URI opcional para pruebas o invocación manual.
     *
     * @return bool True si la vista fue despachada correctamente; false en 404 o 403.
     */
    public function dispatch(?string $rawUri = null): bool
    {
        $route = $this->resolve($rawUri);

        if ($route === null) {
            if (!headers_sent()) {
                http_response_code(404);
            }
            echo '404 - Ruta no encontrada.';
            return false;
        }

        return $this->middleware->handle($route);
    }
}
