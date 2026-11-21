<?php

declare(strict_types=1);

namespace Asrs\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Administra la generación, almacenamiento y carga de la caché de vistas/rutas
 * desde la tabla `asrs_views` hacia el archivo `caches/asrs_views.php`.
 */
class ViewsCacheManager
{
    private string $projectRoot;
    private string $cacheDir;
    private string $cacheFile;
    private ?PDO $pdo;
    private int $activeStatus;

    /**
     * @param string|null $projectRoot Ruta raíz del proyecto. Si es null, se resuelve con dirname(__DIR__).
     * @param PDO|null    $pdo         Conexión PDO opcional (útil para inyección o pruebas). Si es null, usa Database::getConnection().
     * @param int         $activeStatus Valor de la columna `status` considerado activo (por defecto 1).
     */
    public function __construct(
        ?string $projectRoot = null,
        ?PDO $pdo = null,
        int $activeStatus = 1
    ) {
        $resolvedRoot = $projectRoot ?? dirname(__DIR__);
        $this->projectRoot = rtrim($resolvedRoot, '/\\');
        $this->cacheDir = $this->projectRoot . DIRECTORY_SEPARATOR . 'caches';
        $this->cacheFile = $this->cacheDir . DIRECTORY_SEPARATOR . 'asrs_views.php';
        $this->pdo = $pdo;
        $this->activeStatus = $activeStatus;
    }

    /**
     * Verifica si el archivo de caché `caches/asrs_views.php` existe.
     */
    public function exists(): bool
    {
        return is_file($this->cacheFile);
    }

    /**
     * Devuelve la ruta absoluta del archivo de caché de vistas.
     */
    public function getCacheFilePath(): string
    {
        return $this->cacheFile;
    }

    /**
     * Carga y devuelve el arreglo asociativo de vistas indexado por `uri`.
     * Si el archivo de caché no existe (o se solicita regeneración), lo genera bajo demanda.
     *
     * @param bool $forceRefresh Si es true, regenera la caché desde la base de datos aunque el archivo exista.
     *
     * @return array<string, array<string, mixed>>
     *
     * @throws RuntimeException Si ocurre un error de base de datos o de sistema de archivos.
     */
    public function getViews(bool $forceRefresh = false): array
    {
        if ($forceRefresh || !$this->exists()) {
            return $this->generate();
        }

        $views = require $this->cacheFile;

        if (!is_array($views)) {
            return $this->generate();
        }

        return $views;
    }

    /**
     * Consulta la tabla `asrs_views`, construye el arreglo indexado por `uri`
     * y lo escribe en `caches/asrs_views.php`.
     *
     * @return array<string, array<string, mixed>>
     *
     * @throws RuntimeException Si falla la consulta PDO o la escritura en disco.
     */
    public function generate(): array
    {
        $rows = $this->fetchActiveViews();
        $views = [];

        foreach ($rows as $row) {
            if (!isset($row['uri']) || !is_string($row['uri']) || $row['uri'] === '') {
                continue;
            }

            $views[$row['uri']] = $row;
        }

        $this->writeCache($views);

        return $views;
    }

    /**
     * Elimina el archivo de caché si existe.
     *
     * @return bool True si se eliminó o no existía; false si falló la eliminación.
     */
    public function clear(): bool
    {
        if (!$this->exists()) {
            return true;
        }

        return @unlink($this->cacheFile);
    }

    /**
     * Obtiene la instancia PDO configurada o la conexión singleton del núcleo.
     */
    private function getPdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        try {
            return Database::getConnection();
        } catch (PDOException $e) {
            throw new RuntimeException(
                'No fue posible establecer conexión con la base de datos para generar la caché de vistas.',
                0,
                $e
            );
        }
    }

    /**
     * Consulta los registros activos de `asrs_views` mediante sentencia preparada.
     *
     * @return list<array<string, mixed>>
     *
     * @throws RuntimeException Si falla la consulta SQL.
     */
    private function fetchActiveViews(): array
    {
        $sql = 'SELECT id, uri, app, type, controller, method, layout, content, roles, css_path_view, js_path_view, status, created_at
                FROM asrs_views
                WHERE status = :status
                ORDER BY id ASC';

        try {
            $pdo = $this->getPdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':status', $this->activeStatus, PDO::PARAM_INT);
            $stmt->execute();

            /** @var list<array<string, mixed>> $rows */
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $rows;
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Error al consultar las vistas activas en la base de datos.',
                0,
                $e
            );
        }
    }

    /**
     * Garantiza que el directorio `caches` exista.
     *
     * @throws RuntimeException Si no se puede crear el directorio.
     */
    private function ensureCacheDirectory(): void
    {
        if (!is_dir($this->cacheDir)) {
            if (!@mkdir($this->cacheDir, 0755, true) && !is_dir($this->cacheDir)) {
                throw new RuntimeException(
                    sprintf('No se pudo crear el directorio de caché: %s', $this->cacheDir)
                );
            }
        }
    }

    /**
     * Exporta de forma segura el arreglo de vistas a `caches/asrs_views.php` mediante escritura atómica.
     *
     * @param array<string, array<string, mixed>> $views
     *
     * @throws RuntimeException Si falla la escritura del archivo.
     */
    private function writeCache(array $views): void
    {
        $this->ensureCacheDirectory();

        $exported = var_export($views, true);
        $content = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . $exported . ";\n";

        $tmpFile = $this->cacheDir . DIRECTORY_SEPARATOR . 'asrs_views_' . uniqid('', true) . '.tmp';

        if (@file_put_contents($tmpFile, $content, LOCK_EX) === false) {
            throw new RuntimeException(
                sprintf('No se pudo escribir el archivo temporal de caché en: %s', $this->cacheDir)
            );
        }

        if (!@rename($tmpFile, $this->cacheFile)) {
            @unlink($tmpFile);
            throw new RuntimeException(
                sprintf('No se pudo mover el archivo de caché a su destino final: %s', $this->cacheFile)
            );
        }
    }
}
