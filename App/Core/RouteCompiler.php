<?php
namespace App\Core;

use App\Core\Database;
use Exception;

/**
 * Clase RouteCompiler
 * * Se encarga de transformar las rutas almacenadas en la base de datos
 * en un archivo de caché PHP para optimizar el rendimiento del Router.
 */
class RouteCompiler {
    
    private $cachePath;

    public function __construct() {
        // Definimos la ruta de salida siguiendo el estándar de carpetas con Mayúsculas
        $this->cachePath = __DIR__ . '/../Storage/Cache/routes_cache.php';
    }

    /**
     * Compila las rutas y genera el archivo físico.
     * @return bool Retorna true si tuvo éxito, false en caso contrario.
     */
    public function compile(): bool {
        try {
            // Obtenemos la instancia de la conexión
            $db = Database::getInstance()->getConnection();
            
            // 1. Extraer los datos de la tabla 'routes'
            $query = "SELECT * FROM routes";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $routes = $stmt->fetchAll();

            // 2. Preparar el contenido del archivo de caché
            // Usamos var_export para que el array sea código ejecutable directamente
            $content = "<?php\n\n";
            $content .= "/**\n";
            $content .= " * ARCHIVO DE CACHÉ DE RUTAS - GENERADO AUTOMÁTICAMENTE\n";
            $content .= " * Este archivo es parte del sistema ASRS. No editar manualmente.\n";
            $content .= " * Fecha de generación: " . date('Y-m-d H:i:s') . "\n";
            $content .= " */\n\n";
            $content .= "return " . var_export($routes, true) . ";\n";

            // 3. Verificar si el directorio existe, si no, intentar crearlo
            $directory = dirname($this->cachePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // 4. Escribir el archivo en App/Storage/Cache/
            if (file_put_contents($this->cachePath, $content) === false) {
                throw new Exception("Error crítico: No se pudo escribir en " . $this->cachePath);
            }

            return true;

        } catch (Exception $e) {
            // En desarrollo podrías imprimir el error, en producción se debería loguear
            error_log("RouteCompiler Error: " . $e->getMessage());
            return false;
        }
    }
}