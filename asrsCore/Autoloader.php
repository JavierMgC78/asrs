<?php

namespace Asrs\Core;

class Autoloader {
    
    /**
     * Registra la función de carga automática en la pila de SPL.
     */
    public static function register(): void {
        spl_autoload_register(function ($class) {
            // Definir el namespace raíz del núcleo y el prefijo base del proyecto
            // Ejemplo de namespace: Asrs\Core\Database -> asrsCore/Database.php
            // O mapear namespaces de aplicaciones: Apps\CeaWeb\Controllers\HomeController
            
            $prefix = 'Asrs\\';
            $base_dir = __DIR__ . '/'; // Apunta a asrsCore/

            // Verificar si la clase usa el prefijo del core
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // Si no pertenece a Asrs\, se podría evaluar si pertenece a una app hija o retornar
                return;
            }

            // Obtener el nombre relativo de la clase eliminando el prefijo
            $relative_class = substr($class, $len);

            // Reemplazar losseparadores de espacio de nombres por barras diagonales de directorio,
            // agregar la extensión .php
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            // Si el archivo existe, cargarlo
            if (file_exists($file)) {
                require $file;
            }
        });
    }
}