<?php

declare(strict_types=1);

namespace Asrs\Core;

use RuntimeException;
use Throwable;

/**
 * Encargada exclusivamente de resolver las rutas físicas de las vistas
 * y renderizar el contenido dentro del layout correspondiente.
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
     * Resuelve los archivos de contenido y layout de la ruta, captura el contenido
     * con output buffering y renderiza el layout final.
     *
     * @param array<string, mixed> $route Registro de ruta proveniente de la caché.
     *
     * @throws RuntimeException Si el nombre de archivo es inválido o no existen los archivos de vista.
     * @throws Throwable        Si ocurre un error durante la ejecución de la plantilla.
     */
    public function render(array $route): void
    {
        $isChild = (int)($route['is_child'] ?? 0) === 1;
        $isPublic = (int)($route['is_public'] ?? 0) === 1;

        $appDir = $this->resolveAppDir($isChild);
        $scope = $isPublic ? 'public' : 'private';

        $layoutName = $this->sanitizeFileName((string)($route['layout'] ?? 'default'));
        $rawContent = (string)($route['content_name'] ?? $route['content'] ?? '');
        $contentName = $this->sanitizeFileName($rawContent);

        $viewsBase = $this->projectRoot
            . DIRECTORY_SEPARATOR . 'apps'
            . DIRECTORY_SEPARATOR . $appDir
            . DIRECTORY_SEPARATOR . 'Views'
            . DIRECTORY_SEPARATOR . $scope;

        $contentFile = $viewsBase . DIRECTORY_SEPARATOR . 'contents' . DIRECTORY_SEPARATOR . $contentName . '.php';
        $layoutFile = $viewsBase . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layoutName . '.php';

        if (!is_file($contentFile)) {
            throw new RuntimeException('El archivo de contenido de la vista no existe.');
        }

        if (!is_file($layoutFile)) {
            throw new RuntimeException('El archivo de layout de la vista no existe.');
        }

        $contentCss = $this->resolveContentAsset($appDir, 'css', $contentName);
        $contentJs = $this->resolveContentAsset($appDir, 'js', $contentName);

        ob_start();
        try {
            require $contentFile;
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
     * Resuelve el directorio de la aplicación según el flag `is_child`.
     */
    private function resolveAppDir(bool $isChild): string
    {
        if ($isChild) {
            return 'appChild';
        }

        $asrsViews = $this->projectRoot
            . DIRECTORY_SEPARATOR . 'apps'
            . DIRECTORY_SEPARATOR . 'asrs'
            . DIRECTORY_SEPARATOR . 'Views';

        if (is_dir($asrsViews)) {
            return 'asrs';
        }

        $asrsWebViews = $this->projectRoot
            . DIRECTORY_SEPARATOR . 'apps'
            . DIRECTORY_SEPARATOR . 'asrsWeb'
            . DIRECTORY_SEPARATOR . 'Views';

        return is_dir($asrsWebViews) ? 'asrsWeb' : 'asrs';
    }

    /**
     * Sanitiza un nombre de archivo de vista usando basename() para evitar path traversal.
     *
     * @throws RuntimeException Si el nombre resultante está vacío o es inválido.
     */
    private function sanitizeFileName(string $name): string
    {
        $clean = basename(trim($name));
        if (str_ends_with(strtolower($clean), '.php')) {
            $clean = substr($clean, 0, -4);
        }

        if ($clean === '' || $clean === '.' || $clean === '..') {
            throw new RuntimeException('Nombre de archivo de vista inválido.');
        }

        return $clean;
    }

    /**
     * Infiere la ruta web del asset (css o js) a partir de `content_name`
     * aplicando basename() y verificando su existencia física en public/assets/.
     */
    private function resolveContentAsset(string $appDir, string $type, string $contentName): ?string
    {
        $safeName = basename($contentName);
        if ($safeName === '' || $safeName === '.' || $safeName === '..') {
            return null;
        }

        $relativeWebPath = '/assets/' . $appDir . '/' . $type . '/contents/' . $safeName . '.' . $type;
        $diskPath = $this->projectRoot
            . DIRECTORY_SEPARATOR . 'public'
            . DIRECTORY_SEPARATOR . 'assets'
            . DIRECTORY_SEPARATOR . $appDir
            . DIRECTORY_SEPARATOR . $type
            . DIRECTORY_SEPARATOR . 'contents'
            . DIRECTORY_SEPARATOR . $safeName . '.' . $type;

        return is_file($diskPath) ? $relativeWebPath : null;
    }
}
