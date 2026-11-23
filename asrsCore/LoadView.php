<?php

declare(strict_types=1);

namespace Asrs\Core;

use Throwable;

/**
 * Encargada de resolver dinámicamente las rutas físicas de vistas y assets
 * según APP_CHILD_IS_DEFAULT y el alcance (public/private), sin fallback cruzado entre ramas.
 */
class LoadView
{
    private string $projectRoot;

    /**
     * @param string|null $projectRoot Ruta raíz del proyecto. Si es null, se resuelve con dirname(__DIR__).
     */
    public function __construct(?string $projectRoot = null)
    {
        $resolvedRoot = $projectRoot ?? dirname(__DIR__);
        $this->projectRoot = rtrim($resolvedRoot, '/\\');
    }

    /**
     * Resuelve y renderiza el contenido dentro del layout para la rama activa.
     * Si el layout o el contenido no existen en la rama activa, responde con 404 controlado.
     *
     * @param array<string, mixed> $route Registro de ruta resuelto por Router/Middleware.
     *
     * @throws Throwable Si ocurre un error durante la ejecución de la plantilla.
     */
    public function render(array $route): void
    {
        $appDir = $this->resolveAppDir();
        $scope = ((int)($route['is_public'] ?? 1) === 1) ? 'public' : 'private';

        $layoutName = $this->sanitizeName((string)($route['layout'] ?? 'default'));
        $contentName = $this->sanitizeName((string)($route['content_name'] ?? $route['content'] ?? ''));

        if ($layoutName === '' || $contentName === '') {
            $this->handleNotFound();
            return;
        }

        $viewsBase = $this->projectRoot
            . DIRECTORY_SEPARATOR . 'apps'
            . DIRECTORY_SEPARATOR . $appDir
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . $scope;

        $layoutFile = $viewsBase . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layoutName . '.php';
        $contentViewPath = $viewsBase . DIRECTORY_SEPARATOR . 'contents' . DIRECTORY_SEPARATOR . $contentName . '.php';

        if (!is_file($layoutFile) || !is_file($contentViewPath)) {
            $this->handleNotFound();
            return;
        }

        $contentCssUrl = $this->resolveAssetUrl($appDir, 'css', $scope, $contentName);
        $contentJsUrl = $this->resolveAssetUrl($appDir, 'js', $scope, $contentName);

        // Variables de compatibilidad para layouts existentes
        $contentFile = $contentViewPath;
        $contentCss = $contentCssUrl;
        $contentJs = $contentJsUrl;

        ob_start();
        try {
            require $contentViewPath;
            $content = (string)ob_get_clean();
        } catch (Throwable $e) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $e;
        }

        require $layoutFile;
    }

    /**
     * Evalúa dinámicamente en cada ejecución si la rama activa es `appChild` o `asrs`.
     * No almacena ni fija la rama en memoria persistente.
     */
    private function resolveAppDir(): string
    {
        if (defined('APP_CHILD_IS_DEFAULT')) {
            return ((int)constant('APP_CHILD_IS_DEFAULT') === 1) ? 'appChild' : 'asrs';
        }

        $configFile = $this->projectRoot . DIRECTORY_SEPARATOR . 'asrsCore' . DIRECTORY_SEPARATOR . 'config.php';
        if (is_file($configFile)) {
            $config = require $configFile;
            if (is_array($config) && isset($config['APP_CHILD_IS_DEFAULT'])) {
                return ((int)$config['APP_CHILD_IS_DEFAULT'] === 1) ? 'appChild' : 'asrs';
            }
        }

        return 'asrs';
    }

    /**
     * Sanea nombres de layout y contenido con basename() y caracteres permitidos [a-zA-Z0-9_-].
     */
    private function sanitizeName(string $name): string
    {
        $clean = basename(trim($name));
        if (str_ends_with(strtolower($clean), '.php')) {
            $clean = substr($clean, 0, -4);
        }

        $sanitized = (string)preg_replace('/[^a-zA-Z0-9_-]/', '', $clean);

        return $sanitized;
    }

    /**
     * Verifica en disco si existe el asset (css/js) para el contenido y alcance dados
     * en `public/assets/{appDir}/{type}/{scope}/contents/{content_name}.{type}`.
     */
    private function resolveAssetUrl(string $appDir, string $type, string $scope, string $contentName): ?string
    {
        $diskPath = $this->projectRoot
            . DIRECTORY_SEPARATOR . 'public'
            . DIRECTORY_SEPARATOR . 'assets'
            . DIRECTORY_SEPARATOR . $appDir
            . DIRECTORY_SEPARATOR . $type
            . DIRECTORY_SEPARATOR . $scope
            . DIRECTORY_SEPARATOR . 'contents'
            . DIRECTORY_SEPARATOR . $contentName . '.' . $type;

        if (!is_file($diskPath)) {
            return null;
        }

        return '/assets/' . $appDir . '/' . $type . '/' . $scope . '/contents/' . $contentName . '.' . $type;
    }

    /**
     * Dispara una respuesta HTTP 404 controlada cuando la vista o el layout no existen en la rama activa.
     */
    private function handleNotFound(): void
    {
        if (!headers_sent()) {
            http_response_code(404);
        }

        echo '404 - Vista no encontrada.';
    }
}
