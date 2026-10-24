<?php
namespace asrsCore\Core;

use Throwable;

class CacheRolesBuilder {
    /** Ruta por defecto del caché: asrsCore/Cache/roles.php */
    public const DEFAULT_CACHE_FILE = __DIR__ . '/../Cache/roles.php';

    /**
     * Genera el archivo de caché de roles y permisos (roles.php) si no existe.
     *
     * Estructura del arreglo (búsqueda O(1)):
     *  - 'roles' => [ nombre_rol => [id, name, display_name, description] ]  (roles activos)
     *  - 'views' => [ "app|uri"  => [nombre_rol, ...] ]                       (roles permitidos por vista)
     */
    public static function buildIfNotExists(string $cacheFilePath = self::DEFAULT_CACHE_FILE): bool {
        if (is_file($cacheFilePath)) {
            return true;
        }

        try {
            $pdo = Conection::getInstance();

            $roles = [];
            $stmt = $pdo->query(
                "SELECT id, name, display_name, description FROM roles WHERE status = 1"
            );
            foreach ($stmt->fetchAll() as $row) {
                $row['id'] = (int)$row['id'];
                $roles[$row['name']] = $row;
            }

            // Roles permitidos por vista (asrs_views.roles es un JSON con nombres de rol)
            $views = [];
            $stmt = $pdo->query("SELECT uri, app, roles FROM asrs_views WHERE status = 1");
            foreach ($stmt->fetchAll() as $row) {
                $allowed = $row['roles'] !== null ? (json_decode($row['roles'], true) ?? []) : [];
                $views[$row['app'] . '|' . $row['uri']] = array_values((array)$allowed);
            }

            $data = ['roles' => $roles, 'views' => $views];

            $dir = dirname($cacheFilePath);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                return false;
            }

            $content = "<?php\nreturn " . var_export($data, true) . ";\n";

            // Escritura atómica: archivo temporal + rename
            $tmp = $cacheFilePath . '.' . uniqid('', true) . '.tmp';
            if (file_put_contents($tmp, $content, LOCK_EX) === false) {
                return false;
            }
            if (!rename($tmp, $cacheFilePath)) {
                @unlink($tmp);
                return false;
            }
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}
