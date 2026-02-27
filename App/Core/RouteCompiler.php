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

    /************************************************************************************
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
            $rawRoutes = $stmt->fetchAll();

            // --- AQUÍ VA EL FOREACH DE REESTRUCTURACIÓN ---
            $indexedRoutes = [];
            foreach ($rawRoutes as $route) {
                // Usamos el valor de 'slug' como KEY del array
                $indexedRoutes[$route['slug']] = [
                    'id'    => $route['id'],
                    'view_namespace'    => $route['view_namespace'],
                    'view_controller'   => $route['view_controller'],
                    'view_path'         => $route['view_path'],
                    'view'  => $route['view'],
                    'layout_path'         => $route['layout_path'],
                    'layout_namespace'    => $route['layout_namespace'],
                    'layout_controller' => $route['layout_controller'],
                    'layout'    => $route['layout'],
                    'is_public'     => $route['is_public'],
                    'access_level'  => $route['access_level'],
                    'specific_user' => $route['specific_user'],
                    'status'        => $route['status']
                ];
            }
            // ----------------------------------------------
            
            // 2. Preparar el contenido del archivo de caché
            $content = "<?php\n\n";
            $content .= "/**\n";
            $content .= " * ARCHIVO DE CACHÉ DE RUTAS - GENERADO AUTOMÁTICAMENTE\n";
            $content .= " * Este archivo es parte del sistema ASRS. No editar manualmente.\n";
            $content .= " * Fecha de generación: " . date('Y-m-d H:i:s') . "\n";
            $content .= " */\n\n";
            
            // Exportamos $indexedRoutes en lugar de $rawRoutes
            $content .= "return " . var_export($indexedRoutes, true) . ";\n";

            // 3. Verificar si el directorio existe
            $directory = dirname($this->cachePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // 4. Escribir el archivo
            if (file_put_contents($this->cachePath, $content) === false) {
                throw new \Exception("Error crítico: No se pudo escribir en " . $this->cachePath);
            }

            return true;

        } catch (\Exception $e) {
            error_log("RouteCompiler Error: " . $e->getMessage());
            return false;
        }
    }
}