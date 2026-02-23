<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Clase Database
 * Implementa el Patrón Singleton para gestionar una única conexión PDO.
 */
class Database {
    // Contenedor de la instancia única
    private static $instance = null;
    // Objeto de conexión PDO
    private $connection;

    /**
     * Constructor privado para evitar instanciación externa.
     */
    private function __construct() {
        // Configuración de acceso (Pendiente mover a un archivo .env para mayor seguridad)
        $host    = 'localhost';
        $db      = 'asrs_db';
        $user    = 'root';
        $pass    = '';
        $charset = 'utf8mb4';

        // Definición del Data Source Name (DSN)
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        
        // Opciones de configuración de PDO
        $options = [
            // Lanza excepciones en caso de error (fundamental para la seguridad)
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Devuelve los datos como arreglos asociativos por defecto
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Desactiva la emulación de consultas preparadas para mayor protección contra SQLi
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // En desarrollo mostramos el error; en producción se debe registrar en un log
            die("Error crítico de conexión: " . $e->getMessage());
        }
    }

    /**
     * Método estático para obtener la instancia única de la conexión.
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna el objeto de conexión PDO listo para ejecutar consultas.
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Evita que la instancia sea clonada.
     */
    private function __clone() { }

    /**
     * Evita que la instancia sea serializada.
     */
    public function __wakeup() {
        throw new \Exception("No se puede deserializar una instancia de Database.");
    }
}
