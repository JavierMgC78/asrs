<?php

declare(strict_types=1);

namespace Asrs\Core;

/**
 * Cargador automático PSR-4 nativo para el núcleo (Asrs\Core) y las aplicaciones (Apps).
 */
class Autoloader
{
    /**
     * Registra la función de carga automática en la pila de SPL.
     */
    public static function register(): void
    {
        $projectRoot = dirname(__DIR__);

        $prefixes = [
            'Asrs\\Core\\' => __DIR__ . DIRECTORY_SEPARATOR,
            'Apps\\'       => $projectRoot . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR,
        ];

        spl_autoload_register(static function (string $class) use ($prefixes): void {
            foreach ($prefixes as $prefix => $baseDir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    continue;
                }

                $relativeClass = substr($class, $len);
                $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

                if (is_file($file)) {
                    require $file;
                }

                return;
            }
        });
    }
}
