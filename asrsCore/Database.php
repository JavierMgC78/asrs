<?php

namespace Asrs\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    /**
     * Obtiene una instancia única de conexión PDO (Patrón Singleton).
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $db   = 'asrs_db'; // Reemplazar con el nombre de la BD
            $user = 'root';    // Usuario de la base de datos
            $pass = '';        // Contraseña del servidor
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Máxima seguridad con tipos nativos
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // En entorno de producción, registrar el error en logs internos sin exponer credenciales
                throw new PDOException("Error de conexión a la base de datos: " . $e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }
}